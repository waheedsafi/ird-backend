<?php

namespace App\Http\Controllers\v1\template;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Donor;
use App\Models\Project;
use App\Models\Approval;
use App\Models\Document;
use App\Models\Agreement;
use App\Models\UserStatus;
use App\Models\Application;
use App\Traits\FilterTrait;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Models\ProjectStatus;
use App\Models\AgreementStatus;
use App\Traits\UtilHelperTrait;
use App\Models\ApprovalDocument;
use App\Enums\Types\NotifierEnum;
use App\Models\AgreementDocument;
use App\Enums\Statuses\StatusEnum;
use Illuminate\Support\Facades\DB;
use App\Enums\Types\ApplicationEnum;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;
use App\Enums\Types\ApprovalTypeEnum;
use App\Enums\Permissions\PermissionEnum;
use App\Enums\Types\PredefinedCommentEnum;
use App\Repositories\Approval\ApprovalRepositoryInterface;
use App\Repositories\Notification\NotificationRepositoryInterface;

class ApprovalController extends Controller
{
    // use HelperTrait;
    use FilterTrait, UtilHelperTrait;
    protected $approvalRepository;
    protected $notificationRepository;

    public function __construct(
        ApprovalRepositoryInterface $approvalRepository,
        NotificationRepositoryInterface $notificationRepository,
    ) {
        $this->approvalRepository = $approvalRepository;
        $this->notificationRepository = $notificationRepository;
    }
    public function approval($approval_id)
    {
        $approval =  DB::table('approvals as a')
            ->where('a.id', $approval_id)
            ->select('a.id', 'a.requester_type')
            ->first();
        if (!$approval) {
            return response()->json([
                'message' => __('app_translation.approval_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        $tr = [];
        if ($approval->requester_type == User::class) {
            $tr = $this->approvalRepository->userApproval($approval_id);
        } else if ($approval->requester_type == Organization::class) {
            $tr = $this->approvalRepository->organizationApproval($approval_id);
        } else if ($approval->requester_type == Project::class) {
            $tr = $this->approvalRepository->projectApproval($approval_id);
        }
        return response()->json($tr, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function store(Request $request)
    {
        $request->validate([
            "approved" => "required",
            "approval_id" => "required",
        ]);
        $approval_id = $request->approval_id;
        $approval =  Approval::find($approval_id);
        if (!$approval) {
            return response()->json([
                'message' => __('app_translation.approval_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        $message = [];
        DB::beginTransaction();
        $authUser = $request->user();
        if ($approval->requester_type === User::class) {
            if ($approval->notifier_type_id == NotifierEnum::confirm_adding_user->value) {
                DB::table('user_statuses')
                    ->where('user_id', $approval->requester_id)
                    ->where('is_active', true)
                    ->limit(1)
                    ->update(['is_active' => false]);
                $user = DB::table('users as u')
                    ->where('u.id', $approval->requester_id)
                    ->first();
                if ($request->approved) {
                    UserStatus::create([
                        "user_id" => $approval->requester_id,
                        "saved_by" => $request->user()->id,
                        "is_active" => true,
                        "status_id" => StatusEnum::active->value,
                    ]);
                    $approval->approval_type_id = ApprovalTypeEnum::approved->value;
                    $message = [
                        'en' => Lang::get('app_translation.user_approved', ['username' => $user->username ?? 'Unknown User'], 'en'),
                        'fa' => Lang::get('app_translation.user_approved', ['username' => $user->username ?? 'Unknown User'], 'fa'),
                        'ps' => Lang::get('app_translation.user_approved', ['username' => $user->username ?? 'Unknown User'], 'ps'),
                    ];
                } else {
                    UserStatus::create([
                        "user_id" => $approval->requester_id,
                        "saved_by" => $request->user()->id,
                        "is_active" => true,
                        "status_id" => StatusEnum::rejected->value,
                    ]);
                    $approval->approval_type_id = ApprovalTypeEnum::rejected->value;
                    $message = [
                        'en' => Lang::get('app_translation.user_rejected', ['username' => $user->username ?? 'Unknown User'], 'en'),
                        'fa' => Lang::get('app_translation.user_rejected', ['username' => $user->username ?? 'Unknown User'], 'fa'),
                        'ps' => Lang::get('app_translation.user_rejected', ['username' => $user->username ?? 'Unknown User'], 'ps'),
                    ];
                }
            }

            $this->notificationRepository->sendStoreNotification(
                NotifierEnum::confirm_adding_user->value,
                $message,
                null,
                null,
                PermissionEnum::users->value,
                'users'
            );
        } else if ($approval->requester_type === Organization::class) {
            if ($approval->notifier_type_id == NotifierEnum::confirm_signed_registration_form->value) {
                $agreement = Agreement::where('organization_id', $approval->requester_id)
                    ->latest("end_date")
                    ->first();
                if (!$agreement) {
                    return response()->json(
                        [
                            'message' => __('app_translation.agreement_not_exists')
                        ],
                        500,
                        [],
                        JSON_UNESCAPED_UNICODE
                    );
                }
                // 1. Find organization
                $organization = Organization::where('id', $approval->requester_id)
                    ->select('id', 'approved')
                    ->first();
                if (!$organization) {
                    return response()->json([
                        'message' => __('app_translation.organization_not_found'),
                    ], 404, [], JSON_UNESCAPED_UNICODE);
                }
                if ($request->approved == true) {
                    $approval->notifier_type_id = NotifierEnum::signed_register_form_accepted->value;
                    $approval->approval_type_id = ApprovalTypeEnum::approved->value;
                    $approval->respond_date = Carbon::now();
                    $approval->responder_id = $authUser->id;
                    $approval->respond_comment = $request->respond_comment;
                    $approval->completed = true;

                    AgreementStatus::where('agreement_id', $agreement->id)->update(['is_active' => false]);
                    AgreementStatus::create([
                        'agreement_id' => $agreement->id,
                        'userable_id' => $authUser->id,
                        'userable_type' => $this->getModelName(get_class($authUser)),
                        "is_active" => true,
                        'status_id' => StatusEnum::registered->value,
                        'predefined_comment_id' => PredefinedCommentEnum::signed_documents_are_approved,
                    ]);

                    $message = [
                        'en' => Lang::get('app_translation.org_doc_approved', [], 'en'),
                        'fa' => Lang::get('app_translation.org_doc_approved', [], 'fa'),
                        'ps' => Lang::get('app_translation.org_doc_approved', [], 'ps'),
                    ];

                    // 1. Assign Approval Document value to agreement and document
                    $approvalDocuments = ApprovalDocument::where('approval_id', $approval_id)
                        ->get();
                    foreach ($approvalDocuments as $document) {
                        $document = Document::create([
                            'actual_name' => $document->actual_name,
                            'size' => $document->size,
                            'path' => $document->path,
                            'type' => $document->type,
                            'check_list_id' => $document->check_list_id,
                        ]);

                        AgreementDocument::create([
                            'document_id' => $document->id,
                            'agreement_id' => $agreement->id,
                        ]);
                    }

                    // 1. Validate date
                    $expirationDate = Application::where('id', ApplicationEnum::organization_registeration_valid_time->value)
                        ->select('id', 'value as days')
                        ->first();
                    if (!$expirationDate) {
                        return response()->json(
                            [
                                'message' => __('app_translation.setting_record_not_found'),
                            ],
                            404,
                            [],
                            JSON_UNESCAPED_UNICODE
                        );
                    }
                    $agreement = Agreement::where('organization_id', $organization->id)
                        ->where('end_date', null) // Order by end_date descending
                        ->first();           // Get the first record (most recent)
                    if (!$agreement) {
                        return response()->json(
                            [
                                'message' => __('app_translation.doc_already_submitted'),
                            ],
                            500,
                            [],
                            JSON_UNESCAPED_UNICODE
                        );
                    }
                    $end_date = Carbon::parse($agreement->start_date)->addDays((int)$expirationDate->days);
                    $agreement->end_date = $end_date;
                    $agreement->save();
                    // Allow project permission
                    $organization->approved = true;
                    $organization->save();
                } else {
                    $approval->approval_type_id = ApprovalTypeEnum::rejected->value;
                    // 1. set current agreement start_date and end_date to null
                    $agreement->end_date = null;
                    $agreement->start_date = null;
                    // 2. Update Approval
                    $approval->approval_type_id = ApprovalTypeEnum::rejected->value;
                    $approval->respond_date = Carbon::now();
                    $approval->responder_id = $authUser->id;
                    $approval->respond_comment = $request->respond_comment;
                    $approval->completed = true;

                    AgreementStatus::where('agreement_id', $agreement->id)->update(['is_active' => false]);
                    AgreementStatus::create([
                        'agreement_id' => $agreement->id,
                        'userable_id' => $authUser->id,
                        'userable_type' => $this->getModelName(get_class($authUser)),
                        "is_active" => true,
                        'status_id' => StatusEnum::document_upload_required->value,
                        'predefined_comment_id' => PredefinedCommentEnum::signed_documents_are_rejected,

                    ]);

                    $message = [
                        'en' => Lang::get('app_translation.org_doc_rejected', [], 'en'),
                        'fa' => Lang::get('app_translation.org_doc_rejected', [], 'fa'),
                        'ps' => Lang::get('app_translation.org_doc_rejected', [], 'ps'),
                    ];
                }
            }
            $this->notificationRepository->sendStoreUniqueNotification(
                NotifierEnum::confirm_signed_registration_form->value,
                $message,
                null,
                null,
                'organizations',
                $approval->requester_id
            );
        } else if ($approval->requester_type === Project::class) {
            if ($approval->notifier_type_id == NotifierEnum::confirm_signed_project_form->value) {
                // 1. Find organization
                $project =
                    DB::table('projects as p')
                    ->where('p.id', $approval->requester_id)
                    ->join('project_trans as pt', 'pt.project_id', '=', 'p.id')
                    ->select(
                        'p.id',
                        DB::raw("MAX(CASE WHEN pt.language_name = 'fa' THEN pt.name END) as name_farsi"),
                        DB::raw("MAX(CASE WHEN pt.language_name = 'ps' THEN pt.name END) as name_pashto"),
                        DB::raw("MAX(CASE WHEN pt.language_name = 'en' THEN pt.name END) as name_english")
                    )
                    ->groupBy('p.id')
                    ->first();
                if (!$project) {
                    return response()->json([
                        'message' => __('app_translation.project_not_fou'),
                    ], 404, [], JSON_UNESCAPED_UNICODE);
                }
                ProjectStatus::where('project_id', $approval->requester_id)->update(['is_active' => false]);

                if ($request->approved) {
                    $approval->approval_type_id = ApprovalTypeEnum::approved->value;

                    ProjectStatus::create([
                        'project_id' => $approval->requester_id,
                        'comment' => $request->request_comment ?? ' ',
                        'userable_id' => $authUser->id,
                        'userable_type' => $this->getModelName(get_class($authUser)),
                        "is_active" => true,
                        'status_id' => StatusEnum::pending_for_schedule->value,
                    ]);
                    $message = [
                        'en' => Lang::get('app_translation.project_doc_approved', ['username' => $project->name_english ?? 'Unknown User'], 'en'),
                        'fa' => Lang::get('app_translation.project_doc_approved', ['username' => $project->name_farsi ?? 'Unknown User'], 'fa'),
                        'ps' => Lang::get('app_translation.project_doc_approved', ['username' => $project->name_pashto ?? 'Unknown User'], 'ps'),
                    ];
                } else {
                    $approval->approval_type_id = ApprovalTypeEnum::rejected->value;
                    ProjectStatus::create([
                        'project_id' => $approval->requester_id,
                        'comment' => $request->request_comment ?? ' ',
                        'userable_id' => $authUser->id,
                        'userable_type' => $this->getModelName(get_class($authUser)),
                        "is_active" => true,
                        'status_id' => StatusEnum::document_upload_required->value,
                    ]);
                    $message = [
                        'en' => Lang::get('app_translation.project_doc_rejected', ['username' => $project->name_english ?? 'Unknown User'], 'en'),
                        'fa' => Lang::get('app_translation.project_doc_rejected', ['username' => $project->name_farsi ?? 'Unknown User'], 'fa'),
                        'ps' => Lang::get('app_translation.project_doc_rejected', ['username' => $project->name_pashto ?? 'Unknown User'], 'ps'),
                    ];
                }

                $this->notificationRepository->sendStoreUniqueNotification(
                    NotifierEnum::confirm_signed_project_form->value,
                    $message,
                    null,
                    null,
                    'projects',
                    $approval->requester_id
                );
            }
        }
        $approval->respond_comment = $request->respond_comment;
        $approval->respond_date = Carbon::now();
        $approval->responder_id = $authUser->id;
        $approval->completed = true;
        $approval->save();

        DB::commit();
        return response()->json([
            'message' => __('app_translation.success'),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    // User
    public function pendingUserApproval(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page

        $query = $this->approvalRepository->getByNotifierTypeAndRequesterType(
            ApprovalTypeEnum::pending->value,
            User::class
        );
        $this->applySearch($query, $request, [
            'id' => 'a.id',
            'requester' => 'usr.username',
            'requester_id' => 'a.requester_id',
        ]);
        $approvals = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($approvals, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function approvedUserApproval(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page

        $query = $this->approvalRepository->getByNotifierTypeAndRequesterType(
            ApprovalTypeEnum::approved->value,
            User::class
        );
        $this->applySearch($query, $request, [
            'id' => 'a.id',
            'requester' => 'usr.username',
            'requester_id' => 'a.requester_id',

        ]);
        $approvals = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($approvals, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function rejectedUserApproval(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page

        $query = $this->approvalRepository->getByNotifierTypeAndRequesterType(
            ApprovalTypeEnum::rejected->value,
            User::class
        );
        $this->applySearch($query, $request, [
            'id' => 'a.id',
            'requester' => 'usr.username',
            'requester_id' => 'a.requester_id',

        ]);
        $approvals = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($approvals, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function requestForUser(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'message' => __('app_translation.user_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        $userStatus = DB::table('user_statuses as us')
            ->where("us.user_id", $user->id)
            ->where('is_active', true)
            ->select('us.status_id')
            ->first();
        DB::beginTransaction();

        if (
            $userStatus->status_id != StatusEnum::rejected->value
        ) {
            return response()->json([
                'message' => __('app_translation.your_account_un_app'),
            ], 403, [], JSON_UNESCAPED_UNICODE);
        }
        DB::table('user_statuses')
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->limit(1)
            ->update(['is_active' => false]);

        UserStatus::create([
            "user_id" => $user->id,
            "saved_by" => $request->user()->id,
            "is_active" => true,
            "status_id" => StatusEnum::pending->value,
        ]);
        $this->approvalRepository->storeApproval(
            $id,
            User::class,
            NotifierEnum::confirm_adding_user->value,
            ''
        );
        DB::commit();
        return response()->json([
            'message' => __('app_translation.success'),
            'status_id' => StatusEnum::pending->value,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    // Donor
    public function pendingDonorApproval(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page

        $query = $this->approvalRepository->getByNotifierTypeAndRequesterType(
            ApprovalTypeEnum::pending->value,
            Donor::class
        );
        $this->applySearch($query, $request, [
            'id' => 'a.id',
            'requester' => 'dnr.name',
            'requester_id' => 'a.requester_id',
        ]);
        $approvals = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($approvals, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function approvedDonorApproval(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page

        $query = $this->approvalRepository->getByNotifierTypeAndRequesterType(
            ApprovalTypeEnum::approved->value,
            Donor::class
        );
        $this->applySearch($query, $request, [
            'id' => 'a.id',
            'requester' => 'dnr.name',
            'requester_id' => 'a.requester_id',
        ]);
        $approvals = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($approvals, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function rejectedDonorApproval(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page

        $query = $this->approvalRepository->getByNotifierTypeAndRequesterType(
            ApprovalTypeEnum::rejected->value,
            Donor::class
        );
        $this->applySearch($query, $request, [
            'id' => 'a.id',
            'requester' => 'dnr.name',
            'requester_id' => 'a.requester_id',
        ]);
        $approvals = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($approvals, 200, [], JSON_UNESCAPED_UNICODE);
    }
    // Organization
    public function pendingOrganizationApproval(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page
        $query = $this->approvalRepository->getByNotifierTypeAndRequesterType(
            ApprovalTypeEnum::pending->value,
            Organization::class
        );
        $this->applySearch($query, $request, [
            'id' => 'a.id',
            'requester' => 'org.name',
            'requester_id' => 'a.requester_id',
        ]);
        $approvals = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($approvals, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function approvedOrganizationApproval(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page
        $query = $this->approvalRepository->getByNotifierTypeAndRequesterType(
            ApprovalTypeEnum::approved->value,
            Organization::class
        );
        $this->applySearch($query, $request, [
            'id' => 'a.id',
            'requester' => 'org.name',
            'requester_id' => 'a.requester_id',

        ]);
        $approvals = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($approvals, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function rejectedOrganizationApproval(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page

        $query = $this->approvalRepository->getByNotifierTypeAndRequesterType(
            ApprovalTypeEnum::rejected->value,
            Organization::class
        );
        $this->applySearch($query, $request, [
            'id' => 'a.id',
            'requester' => 'org.name',
            'requester_id' => 'a.requester_id',
        ]);
        $approvals = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($approvals, 200, [], JSON_UNESCAPED_UNICODE);
    }
    // Project
    public function pendingProjectsApproval(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page
        $query = $this->approvalRepository->getByNotifierTypeAndRequesterType(
            ApprovalTypeEnum::pending->value,
            Project::class
        );
        $this->applySearch($query, $request, [
            'id' => 'a.id',
            'requester' => 'pt.name',
            'requester_id' => 'a.requester_id',
        ]);
        $approvals = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($approvals, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function approvedProjectsApproval(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page
        $query = $this->approvalRepository->getByNotifierTypeAndRequesterType(
            ApprovalTypeEnum::approved->value,
            Project::class
        );
        $this->applySearch($query, $request, [
            'id' => 'a.id',
            'requester' => 'pt.name',
            'requester_id' => 'a.requester_id',

        ]);
        $approvals = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($approvals, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function rejectedProjectsApproval(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page

        $query = $this->approvalRepository->getByNotifierTypeAndRequesterType(
            ApprovalTypeEnum::rejected->value,
            Project::class
        );
        $this->applySearch($query, $request, [
            'id' => 'a.id',
            'requester' => 'pt.name',
            'requester_id' => 'a.requester_id',
        ]);
        $approvals = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($approvals, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
