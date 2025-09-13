<?php

namespace App\Http\Controllers\v1\app\projects;

use App\Models\Email;
use App\Models\Contact;
use App\Models\Manager;
use App\Models\Project;
use App\Models\Document;
use App\Models\ManagerTran;
use App\Models\ProjectTran;
use App\Models\Organization;
use App\Models\ProjectDetail;
use App\Models\ProjectStatus;
use App\Models\ProjectManager;
use App\Models\ProjectDocument;
use App\Traits\UtilHelperTrait;
use App\Enums\Types\CountryEnum;
use App\Enums\Types\NotifierEnum;
use App\Enums\Types\TaskTypeEnum;
use App\Models\ProjectDetailTran;
use App\Enums\Statuses\StatusEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Lang;
use App\Enums\Languages\LanguageEnum;
use App\Models\ProjectDistrictDetail;
use App\Enums\Checklist\ChecklistEnum;
use App\Enums\Types\CheckListTypeEnum;
use App\Enums\Permissions\PermissionEnum;
use App\Models\ProjectDistrictDetailTran;
use App\Http\Requests\v1\project\ProjectStoreRequest;
use App\Http\Requests\v1\project\StoreSignedMouRequest;
use App\Repositories\Storage\StorageRepositoryInterface;
use App\Repositories\Approval\ApprovalRepositoryInterface;
use App\Repositories\Director\DirectorRepositoryInterface;
use App\Repositories\PendingTask\PendingTaskRepositoryInterface;
use App\Repositories\Notification\NotificationRepositoryInterface;
use App\Repositories\Representative\RepresentativeRepositoryInterface;
use App\Traits\ChecklistHelperTrait;

class StoreProjectController extends Controller
{
    use ChecklistHelperTrait, UtilHelperTrait;
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

    public function store(ProjectStoreRequest $request)
    {
        $authUser = $request->user();
        $user_id = $authUser->id;

        // 4. Ensure task exists before proceeding
        $task = $this->pendingTaskRepository->pendingTaskExist(
            $request->user(),
            TaskTypeEnum::project_registeration->value,
            $user_id
        );
        $exclude = [
            ChecklistEnum::project_presentation->value,
            ChecklistEnum::mou_en->value,
            ChecklistEnum::mou_fa->value,
            ChecklistEnum::mou_ps->value,
        ];

        $checlklistValidat = null;
        if (!$task) {
            $documentCheckListIds = collect($request->checklistMap)
                ->map(fn($item) => (int) $item[1]['check_list_id'] ?? null)
                ->filter()
                ->values()
                ->toArray();
            $checlklistValidat = $this->checkListWithExlude($task, $exclude, CheckListTypeEnum::project_registeration, $documentCheckListIds);
        } else {
            $checlklistValidat = $this->checkListWithExlude($task, $exclude, CheckListTypeEnum::project_registeration);
        }
        if ($checlklistValidat) {
            return response()->json([
                'errors' => $checlklistValidat,
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        DB::beginTransaction();
        $project_manager = null;

        // If no project_manager_id is provided, create new
        if ($request->previous_manager != true) {
            // 1. Email Check
            $request->validate([
                'pro_manager_name_english' => 'required|string',
                'pro_manager_name_farsi'   => 'required|string',
                'pro_manager_name_pashto'  => 'required|string',
                'pro_manager_contact'      => 'required|string|max:20',
                'pro_manager_email'        => 'required|email',
            ]);
            $email = Email::where('value', $request->pro_manager_email)->first();
            if ($email) {
                return response()->json([
                    'message' => __('app_translation.email_exist'),
                ], 409, [], JSON_UNESCAPED_UNICODE);
            } else {
                $email = Email::create(['value' => $request->pro_manager_email]);
            }

            // 2. Contact Check
            $contact = Contact::where('value', $request->pro_manager_contact)->first();
            if ($contact) {
                return response()->json([
                    'message' => __('app_translation.contact_exist'),
                ], 409, [], JSON_UNESCAPED_UNICODE);
            } else {
                $contact = Contact::create(['value' => $request->pro_manager_contact]);
            }

            // 3. Create Project Manager
            $project_manager = Manager::create([
                'email_id' => $email->id,
                'contact_id' => $contact->id,
                'organization_id' => $user_id,
            ]);

            // 4. Add ProjectManager Translations
            foreach (LanguageEnum::LANGUAGES as $code => $lang) {
                $field = 'pro_manager_name_' . $lang;

                ManagerTran::create([
                    'manager_id' => $project_manager->id,
                    'full_name' => $request->get($field),
                    'language_name' => $code,
                ]);
            }
        } else {
            // Use existing
            $project_manager = Manager::findOrFail($request->manager['id']);
        }


        // Create the main Project
        $project = Project::create([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'approved_date' => '0000-00-00',
            'total_budget' => $request->budget,
            'donor_registration_no' => $request->donor_register_no,
            'currency_id' => $request->currency['id'],
            'donor_id' => $request->donor['id'],
            'country_id' => CountryEnum::afghanistan->value, // hardcoded — optional improvement: make dynamic
            'registration_no' => '',
            'organization_id' => $user_id,

        ]);
        $project->registration_no = 'IRD-P-' . $project->id;
        $project->save();

        ProjectManager::create([
            'manager_id' => $project_manager->id,
            'project_id' => $project->id,
            'is_active' => true,
        ]);
        // Store Project Translations
        $translationFields = [
            'preamble'           => 'preamble',
            'health_experience'  => 'exper_in_health',
            'goals'              => 'goals',
            'objectives'         => 'objective',
            'expected_outcome'   => 'expected_outcome',
            'expected_impact'    => 'expected_impact',
            'subject'   => 'subject', //
            'main_activities'    => 'main_activities',
            'introduction'       => 'project_intro',
            'operational_plan'   => 'action_plan',
            'mission'   => 'mission', //
            'vission'   => 'vission', //
            'terminologies' => 'abbreviat',
            'name' => 'project_name',
            'project_structure' => 'project_structure',
            'organization_senior_manangement' => 'organization_sen_man',
        ];

        foreach (LanguageEnum::LANGUAGES as $code => $lang) {
            $data = [
                'project_id'     => $project->id,
                'language_name'  => $code,
            ];

            foreach ($translationFields as $column => $inputPrefix) {
                $inputKey = "{$inputPrefix}_{$lang}";
                $data[$column] = $request->get($inputKey) ?? '';
            }

            ProjectTran::create($data);
        }


        foreach ($request->centers_list as $center) {
            $projectDetail = ProjectDetail::create([
                'project_id' => $project->id, // pass it externally
                'province_id' => $center['province']['id'],
                'budget' => $center['budget'],
                'direct_beneficiaries' => $center['direct_benefi'],
                'in_direct_beneficiaries' => $center['in_direct_benefi'],
            ]);

            foreach (LanguageEnum::LANGUAGES as $code => $lang) {
                ProjectDetailTran::create([
                    'project_detail_id' => $projectDetail->id,
                    'language_name' => $code,
                    'health_center' => json_encode($center["health_centers_$lang"]),
                    'address' => $center["address_$lang"],
                    'health_worker' => json_encode($center["health_worker_$lang"]),
                    'managment_worker' => json_encode($center["fin_admin_employees_$lang"]),
                ]);
            }

            foreach ($center['district'] as $district) {
                $districtDetail = ProjectDistrictDetail::create([
                    'project_detail_id' => $projectDetail->id,
                    'district_id' => $district['id'],
                ]);

                $villageData = $center['villages'][$district['id']] ?? [];

                foreach (LanguageEnum::LANGUAGES as $code => $lang) {


                    ProjectDistrictDetailTran::create([
                        'project_district_detail_id' => $districtDetail->id,
                        'language_name' => $code,
                        'villages' => json_encode(
                            $villageData["village_$lang" ?? '']
                        ),
                    ]);
                }
            }
        }


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
        $documentsId = [];
        $this->storageRepository->projectDocumentStore($project->id, $user_id, $documents, function ($documentData) use (&$documentsId) {
            $checklist_id = $documentData['check_list_id'];
            $document = Document::create([
                'actual_name' => $documentData['actual_name'],
                'size' => $documentData['size'],
                'path' => $documentData['path'],
                'type' => $documentData['type'],
                'check_list_id' => $checklist_id,
            ]);
            array_push($documentsId, $document->id);
            ProjectDocument::create([
                'document_id' => $document->id,
                'project_id' => $documentData['project_id'],
            ]);
        });

        ProjectStatus::create([
            'project_id' => $project->id,
            'comment' => '',
            'userable_id' => $authUser->id,
            'userable_type' => $this->getModelName(get_class($authUser)),
            "is_active" => true,
            'status_id' => StatusEnum::document_upload_required->value,
        ]);

        DB::commit();
        $this->pendingTaskRepository->destroyPendingTask(
            $request->user(),
            TaskTypeEnum::project_registeration,
            $user_id
        );

        return response()->json([
            'message' => 'Project created successfully.',
            'project_id' =>  $project->id,
        ], 200);
    }
    public function StoreSignedMou(StoreSignedMouRequest $request)
    {
        $request->validated();
        $project_id = $request->project_id;
        $authUser = $request->user();
        $organization_id = $authUser->id;

        $project = DB::table('projects as p')
            ->where('p.id',  $project_id)
            ->where('p.organization_id', $organization_id)
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
            return response()->json(
                [
                    'message' => __('app_translation.project_not_belongs_tu'),
                ],
                500,
                [],
                JSON_UNESCAPED_UNICODE
            );
        }

        // 3. Ensure task exists before proceeding
        $task = $this->pendingTaskRepository->pendingTaskExist(
            $authUser,
            TaskTypeEnum::project_registeration,
            $project_id
        );
        if (!$task) {
            return response()->json([
                'message' => __('app_translation.task_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $include = [
            CheckListEnum::mou_en->value,
            CheckListEnum::mou_fa->value,
            CheckListEnum::mou_ps->value,
        ];
        $checlklistValidat = $this->checkListWithInclude($task, $include, CheckListTypeEnum::project_registeration);

        if ($checlklistValidat) {
            return response()->json([
                'errors' => $checlklistValidat,
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        DB::beginTransaction();
        $approval = $this->approvalRepository->storeApproval(
            $project_id,
            Project::class,
            NotifierEnum::confirm_signed_project_form->value,
            $request->request_comment
        );


        $documents = $this->pendingTaskRepository->pendingTaskDocuments($task->id);
        $this->storageRepository->projectDocumentApprovalStore($project_id, $organization_id, $documents, function ($documentData) use (&$approval) {
            $this->approvalRepository->storeApprovalDocument(
                $approval->id,
                $documentData
            );
        });


        $this->pendingTaskRepository->destroyPendingTask(
            $authUser,
            TaskTypeEnum::project_registeration->value,
            $organization_id
        );

        ProjectStatus::where('project_id', $project_id)->update(['is_active' => false]);
        ProjectStatus::create([
            'project_id' => $project_id,
            'comment' => $request->request_comment,
            'userable_id' => $authUser->id,
            'userable_type' => $this->getModelName(get_class($authUser)),
            "is_active" => true,
            'status_id' => StatusEnum::pending->value,
        ]);
        DB::commit();

        // Notification
        $message = [
            'en' => Lang::get('app_translation.project_sent_for_approval', ['username' => $project->name_english ?? 'Unknown User'], 'en'),
            'fa' => Lang::get('app_translation.project_sent_for_approval', ['username' => $project->name_farsi ?? 'Unknown User'], 'fa'),
            'ps' => Lang::get('app_translation.project_sent_for_approval', ['username' => $project->name_pashto ?? 'Unknown User'], 'ps'),
        ];
        $this->notificationRepository->sendStoreNotification(
            NotifierEnum::confirm_signed_project_form->value,
            $message,
            "/dashboard/approval?order=desc&sch_col=requester_id&sch_val={$organization_id}&m_t=54&s_t=pending",
            null,
            PermissionEnum::projects->value,
            'projects'
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
