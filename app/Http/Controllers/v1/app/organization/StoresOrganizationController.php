<?php

namespace App\Http\Controllers\v1\app\organization;

use Carbon\Carbon;
use App\Models\Email;
use App\Models\Address;
use App\Models\Contact;
use App\Models\Document;
use App\Models\Agreement;
use App\Models\AddressTran;
use App\Models\Application;
use App\Models\StatusTrans;
use App\Models\Organization;
use App\Models\AgreementStatus;
use App\Traits\UtilHelperTrait;
use App\Enums\Types\CountryEnum;
use App\Models\OrganizationTran;
use App\Enums\Types\NotifierEnum;
use App\Enums\Types\TaskTypeEnum;
use App\Models\AgreementDirector;
use App\Models\AgreementDocument;
use App\Enums\Statuses\StatusEnum;
use App\Models\OrganizationStatus;
use Illuminate\Support\Facades\DB;
use App\Enums\Permissions\RoleEnum;
use App\Models\PendingTaskDocument;
use Illuminate\Support\Facades\App;
use App\Enums\Types\ApplicationEnum;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use App\Enums\Languages\LanguageEnum;
use App\Enums\Checklist\ChecklistEnum;
use App\Enums\Types\CheckListTypeEnum;
use App\Enums\Permissions\PermissionEnum;
use App\Enums\Types\PredefinedCommentEnum;
use App\Repositories\Storage\StorageRepositoryInterface;
use App\Repositories\Approval\ApprovalRepositoryInterface;
use App\Repositories\Director\DirectorRepositoryInterface;
use App\Repositories\PendingTask\PendingTaskRepositoryInterface;
use App\Http\Requests\v1\organization\OrganizationRegisterRequest;
use App\Repositories\Notification\NotificationRepositoryInterface;
use App\Http\Requests\v1\organization\OrganizationInitStoreRequest;
use App\Http\Requests\v1\organization\StoreSignedRegisterFormRequest;
use App\Repositories\Representative\RepresentativeRepositoryInterface;

class StoresOrganizationController extends Controller
{
    use UtilHelperTrait, UtilHelperTrait;
    protected $pendingTaskRepository;
    protected $notificationRepository;
    protected $approvalRepository;
    protected $directorRepository;
    protected $representativeRepository;
    protected $storageRepository;

    public function __construct(
        PendingTaskRepositoryInterface $pendingTaskRepository,
        NotificationRepositoryInterface $notificationRepository,
        ApprovalRepositoryInterface $approvalRepository,
        DirectorRepositoryInterface $directorRepository,
        RepresentativeRepositoryInterface $representativeRepository,
        StorageRepositoryInterface $storageRepository
    ) {
        $this->pendingTaskRepository = $pendingTaskRepository;
        $this->notificationRepository = $notificationRepository;
        $this->approvalRepository = $approvalRepository;
        $this->directorRepository = $directorRepository;
        $this->representativeRepository = $representativeRepository;
        $this->storageRepository = $storageRepository;
    }
    public function store(OrganizationRegisterRequest $request)
    {
        $validatedData = $request->validated();
        $authUser = $request->user();
        $locale = App::getLocale();
        // Create email
        $email = Email::where('value', '=', $validatedData['email'])->first();
        if ($email) {
            return response()->json([
                'message' => __('app_translation.email_exist'),
            ], 400, [], JSON_UNESCAPED_UNICODE);
        }
        $contact = Contact::where('value', '=', $validatedData['contact'])->first();
        if ($contact) {
            return response()->json([
                'message' => __('app_translation.contact_exist'),
            ], 400, [], JSON_UNESCAPED_UNICODE);
        }
        // Begin transaction
        DB::beginTransaction();
        $email = Email::create(['value' => $validatedData['email']]);
        $contact = Contact::create(['value' => $validatedData['contact']]);
        // Create address
        $address = Address::create([
            'district_id' => $validatedData['district_id'],
            'province_id' => $validatedData['province_id'],
        ]);

        // * Translations
        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            AddressTran::create([
                'address_id' => $address->id,
                'area' => $validatedData["area_{$name}"],
                'language_name' =>  $code,
            ]);
        }
        // Create Organization
        $newOrganization = Organization::create([
            "user_id" => $authUser->id,
            'abbr' => $validatedData['abbr'],
            'registration_no' => "",
            'role_id' => RoleEnum::organization->value,
            'organization_type_id' => $validatedData['organization_type_id'],
            'address_id' => $address->id,
            'email_id' => $email->id,
            'username' => $request->username,
            'contact_id' => $contact->id,
            "password" => Hash::make($validatedData['password']),
        ]);

        // Crea a registration_no
        $newOrganization->registration_no = "IRD" . '-' . Carbon::now()->year . '-' . $newOrganization->id;
        $newOrganization->save();
        // Set Organization status
        OrganizationStatus::create([
            "organization_id" => $newOrganization->id,
            'userable_id' => $authUser->id,
            'userable_type' => $this->getModelName(get_class($authUser)),
            "is_active" => true,
            "status_id" => StatusEnum::active->value,
            "comment" => "Intial"
        ]);

        // **Fix agreement creation**
        $agreement = Agreement::create([
            'organization_id' => $newOrganization->id,
            "agreement_no" => ""
        ]);
        $agreement->agreement_no = "AG" . '-' . Carbon::now()->year . '-' . $agreement->id;
        $agreement->save();

        $task = $this->pendingTaskRepository->pendingTaskExist(
            $request->user(),
            TaskTypeEnum::organization_registeration->value,
            null
        );
        if (!$task) {
            return response()->json([
                'message' => __('app_translation.task_not_found')
            ], 404);
        }
        $documentsId = [];
        $documents = $this->pendingTaskRepository->pendingTaskDocuments($task->id);

        $this->storageRepository->documentStore($agreement->id, $newOrganization->id, $documents, function ($documentData) use (&$documentsId) {
            $checklist_id = $documentData['check_list_id'];
            $document = Document::create([
                'actual_name' => $documentData['actual_name'],
                'size' => $documentData['size'],
                'path' => $documentData['path'],
                'type' => $documentData['type'],
                'check_list_id' => $checklist_id,
            ]);
            array_push($documentsId, $document->id);
            AgreementDocument::create([
                'document_id' => $document->id,
                'agreement_id' => $documentData['agreement_id'],
            ]);
        });

        // Representative with agreement
        $this->representativeRepository->storeRepresentative(
            $request,
            $newOrganization->id,
            $agreement->id,
            $documentsId,
            true,
            $authUser->id,
            $this->getModelName(get_class($authUser))
        );

        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            OrganizationTran::create([
                'organization_id' => $newOrganization->id,
                'language_name' => $code,
                'name' => $validatedData["name_{$name}"],
            ]);
        }

        $name =  $validatedData['name_english'];
        if ($locale == LanguageEnum::farsi->value) {
            $name = $validatedData['name_farsi'];
        } else if ($locale == LanguageEnum::pashto->value) {
            $name = $validatedData['name_pashto'];
        }

        $this->pendingTaskRepository->destroyPendingTask(
            $authUser,
            TaskTypeEnum::organization_registeration->value,
            null
        );
        // If everything goes well, commit the transaction
        AgreementStatus::create([
            'agreement_id' => $agreement->id,
            'userable_id' => $authUser->id,
            'userable_type' => $this->getModelName(get_class($authUser)),
            "is_active" => true,
            'status_id' => StatusEnum::registration_incomplete->value,
            'predefined_comment_id' => PredefinedCommentEnum::organization_user_created,
        ]);
        DB::commit();

        $status = StatusTrans::where('status_id', StatusEnum::registration_incomplete->value)
            ->where('language_name', $locale)
            ->select('name')->first();
        return response()->json(
            [
                'message' => __('app_translation.success'),
                "organization" => [
                    "id" => $newOrganization->id,
                    "profile" => $newOrganization->profile,
                    "abbr" => $newOrganization->abbr,
                    "registration_no" => $newOrganization->registration_no,
                    "status_id" => StatusEnum::registration_incomplete->value,
                    "status" => $status->name,
                    "type_id" => $validatedData['organization_type_id'],
                    "establishment_date" => null,
                    "name" => $name,
                    "contact" => $validatedData['contact'],
                    "email" => $validatedData['email'],
                    "created_at" => $newOrganization->created_at,
                ]
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function registerFormCompleted(OrganizationInitStoreRequest $request)
    {
        // return $request;
        $id = $request->organization_id;
        $validatedData = $request->validated();
        $authUser = $request->user();

        $agreement = Agreement::where('organization_id', $id)
            ->where('end_date', null) // Order by end_date descending
            ->first();           // Get the first record (most recent)

        // 1. If agreement does not exists no further process.
        if (!$agreement) {
            return response()->json([
                'message' => __('app_translation.agreement_not_exists')
            ], 409);
        }

        $agreementStatus = AgreementStatus::where('agreement_id', $agreement->id)
            ->where('is_active', true)
            ->first();
        // 2. Allow If agreement is in 
        if ($agreementStatus && $agreementStatus->status_id != StatusEnum::registration_incomplete->value) {
            return response()->json([
                'message' => __('app_translation.register_form_alre_submi'),
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
        // 3. CheckListEnum:: organization exist
        $organization = Organization::find($id);
        if (!$organization) {
            return response()->json([
                'message' => __('app_translation.organization_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        // 4. Ensure task exists before proceeding
        $task = $this->pendingTaskRepository->pendingTaskExist(
            $authUser,
            TaskTypeEnum::organization_registeration->value,
            $id
        );
        $exclude = [
            CheckListEnum::organization_representor_letter->value,
            CheckListEnum::organization_register_form_en->value,
            CheckListEnum::organization_register_form_fa->value,
            CheckListEnum::organization_register_form_ps->value,
        ];
        // If Directory Nationality is abroad ask for Work Permit
        if ($validatedData["nationality"]["id"] == CountryEnum::afghanistan->value) {
            array_push($exclude, ChecklistEnum::director_work_permit->value);
        }

        $checlklistValidat = null;
        if (!$task) {
            $documentCheckListIds = collect($request->checklistMap)
                ->map(fn($item) => (int) $item[1]['check_list_id'] ?? null)
                ->filter()
                ->values()
                ->toArray();
            $checlklistValidat = $this->validateCheckList($task, $exclude, CheckListTypeEnum::organization_registeration, $documentCheckListIds);
        } else {
            $checlklistValidat = $this->validateCheckList($task, $exclude, CheckListTypeEnum::organization_registeration);
        }
        if ($checlklistValidat) {
            return response()->json([
                'errors' => $checlklistValidat,
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        DB::beginTransaction();
        $email = Email::where('value', $validatedData['email'])
            ->select('id')->first();
        // Email Is taken by someone
        if ($email) {
            if ($email->id == $organization->email_id) {
                $email->value = $validatedData['email'];
                $email->save();
            } else {
                return response()->json([
                    'message' => __('app_translation.email_exist'),
                ], 409, [], JSON_UNESCAPED_UNICODE);
            }
        } else {
            $email = Email::where('id', $organization->email_id)->first();
            $email->value = $validatedData['email'];
            $email->save();
        }
        $contact = Contact::where('value', $validatedData['contact'])
            ->select('id')->first();
        if ($contact) {
            if ($contact->id == $organization->contact_id) {
                $contact->value = $validatedData['contact'];
                $contact->save();
            } else {
                return response()->json([
                    'message' => __('app_translation.contact_exist'),
                ], 409, [], JSON_UNESCAPED_UNICODE);
            }
        } else {
            $contact = Contact::where('id', $organization->contact_id)->first();
            $contact->value = $validatedData['contact'];
            $contact->save();
        }

        // store organization transalation
        $organizationTrans = OrganizationTran::where('organization_id', $id)->get();
        if (!$organizationTrans) {
            return response()->json([
                'message' => __('app_translation.organization_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            $tran =  $organizationTrans->where('language_name', $code)->first();
            $tran->name = $validatedData["name_{$name}"];
            $tran->vision = $validatedData["vision_{$name}"];
            $tran->mission = $validatedData["mission_{$name}"];
            $tran->general_objective = $validatedData["general_objes_{$name}"];
            $tran->objective = $validatedData["objes_in_afg_{$name}"];
            $tran->save();
        }

        // store organization Address
        $organization_addres = Address::find($organization->address_id);

        $organization_addres->province_id  = $validatedData["province"]["id"];
        $organization_addres->district_id  = $validatedData["district"]["id"];
        $organization_addres_trans = AddressTran::where('address_id', $organization->address_id)->get();

        foreach (LanguageEnum::LANGUAGES as $code => $name) {
            $tran =  $organization_addres_trans->where('language_name', $code)->first();
            $tran->area = $validatedData["area_{$name}"];
            $tran->save();
        }

        $organization->abbr =  $validatedData["abbr"];
        $organization->organization_type_id  = $validatedData['type']['id'];
        // $organization->organization_type_id  = $validatedData["type.id"];
        $organization->moe_registration_no  = $request->moe_registration_no;
        $organization->place_of_establishment   = $validatedData["country"]["id"];
        $organization->date_of_establishment  = $validatedData["establishment_date"];
        $organization_addres->save();
        $organization->save();

        // Make prevous state to false
        AgreementStatus::where('agreement_id', $agreement->id,)->update(['is_active' => false]);
        $agreementStatus = AgreementStatus::create([
            'agreement_id' => $agreement->id,
            'userable_id' => $authUser->id,
            'userable_type' => $this->getModelName(get_class($authUser)),
            "is_active" => true,
            'status_id' => StatusEnum::document_upload_required->value,
            'predefined_comment_id' => PredefinedCommentEnum::waiting_for_document_upload,
        ]);

        $directorDocumentsId = [];
        $documents = [];
        if ($task) {
            $documents = $this->pendingTaskRepository->pendingTaskDocuments($task->id);
        } else {
            $documents = collect($request->checklistMap)
                ->map(fn($item) => $item[1] ?? null)  // get the second element of each item or null
                ->filter()                            // remove null values
                ->map(function ($item) {
                    // rename 'name' to 'actual_name'
                    $item['actual_name'] = $item['name'];
                    unset($item['name']);
                    return $item;
                })
                ->values()
                ->toArray();
        }
        $document =  $this->storageRepository->documentStore($agreement->id, $id, $documents, function ($documentData) use (&$directorDocumentsId) {
            $checklist_id = $documentData['check_list_id'];
            $document = Document::create([
                'actual_name' => $documentData['actual_name'],
                'size' => $documentData['size'],
                'path' => $documentData['path'],
                'type' => $documentData['type'],
                'check_list_id' => $checklist_id,
            ]);
            if (
                $checklist_id == CheckListEnum::director_work_permit->value
                || $checklist_id == CheckListEnum::director_nid->value
            ) {
                array_push($directorDocumentsId, $document->id);
            }

            AgreementDocument::create([
                'document_id' => $document->id,
                'agreement_id' => $documentData['agreement_id'],
            ]);
        });
        if ($document) {
            return $document;
        }
        $director = $this->directorRepository->storeOrganizationDirector(
            $validatedData,
            $id,
            $agreement->id,
            $directorDocumentsId,
            true,
            $authUser->id,
            $this->getModelName(get_class($authUser))
        );
        AgreementDirector::create([
            'agreement_id' => $agreement->id,
            'director_id' => $director->id
        ]);
        $this->pendingTaskRepository->destroyPendingTask(
            $request->user(),
            TaskTypeEnum::organization_registeration,
            $id
        );

        DB::commit();
        $locale = App::getLocale();
        $status = DB::table('status_trans as st')
            ->where('st.status_id', StatusEnum::document_upload_required->value)
            ->where('st.language_name', $locale)
            ->select('st.name')
            ->first();
        return response()->json(
            [
                'message' => __('app_translation.success'),
                'agreement_status_id' => StatusEnum::document_upload_required->value,
                'agreement_status' => $status->name,
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function StoreSignedRegisterForm(StoreSignedRegisterFormRequest $request)
    {
        $request->validated();
        $organization_id = $request->organization_id;
        $authUser = $request->user();

        // 1. Validate date
        $expirationDate = Application::where('id', ApplicationEnum::organization_registeration_valid_time->value)
            ->select('id', 'value')
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
        $start_date = Carbon::parse($request->start_date);

        $agreement = Agreement::where('organization_id', $organization_id)
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

        // 2. CheckListEnum:: organization exist
        $organization = Organization::find($organization_id);
        if (!$organization) {
            return response()->json([
                'message' => __('app_translation.organization_not_found'),
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }

        // 3. Ensure task exists before proceeding
        $task = $this->pendingTaskRepository->pendingTaskExist(
            $authUser,
            TaskTypeEnum::organization_registeration,
            $organization_id
        );
        if (!$task) {
            return response()->json([
                'message' => __('app_translation.task_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        DB::beginTransaction();
        $approval = $this->approvalRepository->storeApproval(
            $organization_id,
            Organization::class,
            NotifierEnum::confirm_signed_registration_form->value,
            $request->request_comment
        );
        $documents = $this->pendingTaskRepository->pendingTaskDocuments($task->id);

        $document = $this->storageRepository->documentStore($agreement->id, $organization_id, $documents, function ($documentData) use (&$approval) {
            $this->approvalRepository->storeApprovalDocument(
                $approval->id,
                $documentData
            );
        });
        if ($document) {
            return $document;
        }

        $this->pendingTaskRepository->destroyPendingTask(
            $authUser,
            TaskTypeEnum::organization_registeration->value,
            $organization_id
        );

        // 7. Create a notification

        $agreement->start_date = $start_date;
        $agreement->save();
        // Update organization status
        AgreementStatus::where('agreement_id', $agreement->id,)->update(['is_active' => false]);
        AgreementStatus::create([
            'agreement_id' => $agreement->id,
            'userable_id' => $authUser->id,
            'userable_type' => $this->getModelName(get_class($authUser)),
            "is_active" => true,
            'status_id' => StatusEnum::pending->value,
            'predefined_comment_id' => PredefinedCommentEnum::document_pending_for_approval,
        ]);
        DB::commit();

        // Notification
        $message = [
            'en' => Lang::get('app_translation.org_sent_for_approval', ['username' => $organization->username ?? 'Unknown User'], 'en'),
            'fa' => Lang::get('app_translation.org_sent_for_approval', ['username' => $organization->username ?? 'Unknown User'], 'fa'),
            'ps' => Lang::get('app_translation.org_sent_for_approval', ['username' => $organization->username ?? 'Unknown User'], 'ps'),
        ];
        $this->notificationRepository->sendStoreNotification(
            NotifierEnum::confirm_adding_user->value,
            $message,
            "/dashboard/approval?order=desc&sch_col=requester_id&sch_val={$organization_id}&m_t=52&s_t=pending",
            null,
            PermissionEnum::organizations->value,
            'organizations'
        );
        return response()->json(
            [
                'message' => __('app_translation.you_get_notify_after_appr'),
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
