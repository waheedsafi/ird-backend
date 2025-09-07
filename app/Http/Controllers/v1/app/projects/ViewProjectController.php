<?php

namespace App\Http\Controllers\v1\app\projects;

use App\Traits\FilterTrait;
use Illuminate\Http\Request;
use App\Enums\Types\TaskTypeEnum;
use App\Enums\Statuses\StatusEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Enums\Languages\LanguageEnum;
use App\Repositories\PendingTask\PendingTaskRepositoryInterface;

class ViewProjectController extends Controller
{
    use FilterTrait;
    protected $pendingTaskRepository;

    public function __construct(
        PendingTaskRepositoryInterface $pendingTaskRepository,
    ) {
        $this->pendingTaskRepository = $pendingTaskRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page
        $locale = App::getLocale();

        $authUser = $request->user();
        $user_id = $authUser->id;

        $query = DB::table('projects as pro')
            ->where('pro.organization_id', $user_id)
            ->join('project_trans as prot', function ($join) use ($locale) {
                $join->on('pro.id', '=', 'prot.project_id')
                    ->where('prot.language_name', $locale);
            })
            ->join('project_statuses as ps', function ($join) {
                $join->on('ps.project_id', '=', 'pro.id')
                    ->where('ps.is_active', true);
            })
            ->join('status_trans as st', function ($join)  use ($locale) {
                $join->on('st.status_id', '=', 'ps.status_id')
                    ->where('st.language_name', $locale);
            })
            ->join('donor_trans as dont', function ($join) use ($locale) {
                $join->on('dont.donor_id', 'pro.donor_id')
                    ->where('dont.language_name', $locale);
            })
            ->join('currency_trans as curt', function ($join) use ($locale) {
                $join->on('pro.currency_id', 'curt.currency_id')
                    ->where('curt.language_name', $locale);
            })
            ->select(
                'pro.id',
                'pro.total_budget as budget',
                'pro.start_date',
                'curt.name as currency',
                'pro.end_date',
                'pro.donor_registration_no',
                'prot.name as project_name',
                'dont.name as donor',
                'st.name as status',
                'ps.status_id',
                'pro.registration_no',
                'pro.created_at'
            );
        $this->applyDate($query, $request, 'pro.created_at', 'pro.created_at');
        $allowColumn = [
            'title' => 'prot.title',
            'donor' => 'dont.donor'
        ];


        $this->applyDate($query, $request, 'pro.created_at', 'pro.created_at');
        $this->applyFilters($query, $request, [
            'registration_no' => 'pro.registration_no',
            'project_name' => 'prot.name',
            'donor' => 'dont.name',
            'status' => 'st.name',
            'currency' => 'curt.name'
        ]);
        $this->applySearch($query, $request, [
            'registration_no' => 'pro.registration_no',
            'project_name' => 'prot.name',
            'donor' => 'dont.name',
            'budget' => 'pro.total_budget',
        ]);

        $result = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'projects' => $result
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function startRegisterForm(Request $request, $organization_id)
    {
        $pendingTaskContent = $this->pendingTaskRepository->pendingTask($request, TaskTypeEnum::project_registeration->value, $organization_id);
        if ($pendingTaskContent['content']) {
            return response()->json([
                'message' => __('app_translation.success'),
                'content' => $pendingTaskContent['content']
            ], 200);
        }

        return response()->json([
            [],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function projectsWithName()
    {
        $locale = App::getLocale();
        $result = DB::table('projects as p')
            ->join(
                'project_trans as pt',
                function ($join) use ($locale) {
                    $join->on('p.id', '=', 'pt.project_id')
                        ->where('pt.language_name', $locale);
                }
            )
            ->join('project_statuses as ps', function ($join) use ($locale) {
                $join->on('p.id', '=', 'ps.project_id')
                    ->where('ps.is_active', true);
            })
            ->whereIn('ps.status_id', [StatusEnum::has_comment->value, StatusEnum::pending_for_schedule->value])
            ->select(
                'p.id',
                'pt.name',
            )
            ->get();

        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function headerInfo($id)
    {
        $locale = App::getLocale();
        // 1. Get organization information
        $project = DB::table('projects as p')
            ->where('p.id', $id)
            ->join('project_trans as pt', function ($join) use (&$locale) {
                $join->on('pt.project_id', '=', 'p.id')
                    ->where('pt.language_name', $locale);
            })
            ->join('project_statuses as ps', function ($join) {
                $join->on('ps.project_id', '=', 'p.id')
                    ->where('ps.is_active', true);
            })
            ->join('status_trans as st', function ($join) use ($locale) {
                $join->on('st.status_id', '=', 'ps.status_id')
                    ->where('st.language_name', $locale);
            })
            ->select(
                'pt.name',
                'p.registration_no',
                'st.status_id',
                'st.name as status',
            )->first();
        if (!$project) {
            return response()->json([
                'message' => __('app_translation.organization_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        $result = [
            "name" => $project->name,
            "registration_no" => $project->registration_no,
            "status_id" => $project->status_id,
            "status" => $project->status,
        ];
        return response()->json(
            $result,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function budget($id)
    {
        $languages = LanguageEnum::LANGUAGES;
        $locale = App::getLocale();

        $project = DB::table('projects as pro')->where('pro.id', $id)
            ->join('donor_trans as don', function ($join) use ($locale) {
                $join->on('pro.donor_id', 'don.donor_id')
                    ->where('don.language_name', $locale);
            })
            ->join('currencies as curren', function ($join) use ($locale) {
                $join->on('curren.id', 'pro.currency_id');
            })
            ->join('currency_trans as cur', function ($join) use ($locale) {
                $join->on('curren.id', 'cur.currency_id')
                    ->where('cur.language_name', $locale);
            })
            ->select(
                'pro.start_date',
                'pro.end_date',
                'pro.total_budget as budget',
                'pro.donor_registration_no',
                'don.donor_id',
                'don.name',
                'pro.currency_id',
                'cur.name as currency_name',
                'curren.symbol',
                'pro.approved_date'

            )
            ->first();
        // Fetch centers with province in current locale
        $centers = DB::table('project_details as pd')
            ->where('pd.project_id', $id)
            ->leftJoin('province_trans as p', function ($join) use ($locale) {
                $join->on('pd.province_id', '=', 'p.province_id')
                    ->where('p.language_name', $locale);
            })
            ->select(
                'pd.id',
                'pd.budget',
                'pd.direct_beneficiaries',
                'pd.in_direct_beneficiaries',
                'p.province_id as province_id',
                'p.value as province_name'
            )
            ->get()
            ->keyBy('id');

        // Center translations
        $centerTrans = DB::table('project_detail_trans')
            ->whereIn('project_detail_id', $centers->keys())
            ->get()
            ->groupBy('project_detail_id');

        // Districts only in app language
        $districts = DB::table('project_district_details as pdd')
            ->join('district_trans as dt', function ($join) use ($locale) {
                $join->on('pdd.district_id', '=', 'dt.district_id')
                    ->where('dt.language_name', $locale);
            })
            ->whereIn('pdd.project_detail_id', $centers->keys())
            ->select('pdd.id', 'pdd.project_detail_id', 'pdd.district_id', 'dt.value as district_name')
            ->get()
            ->groupBy('project_detail_id');

        // Villages in all languages
        $districtTrans = DB::table('project_district_detail_trans')
            ->whereIn(
                'project_district_detail_id',
                $districts->flatten()->pluck('id')
            )
            ->get()
            ->groupBy('project_district_detail_id');

        $result = [];

        foreach ($centers as $centerId => $center) {
            $item = [
                'id' => rand(1, 9999999999),
                'province' => [
                    'id'   => $center->province_id,
                    'name' => $center->province_name,
                ],
                'budget' => $center->budget,
                'direct_benefi' => $center->direct_beneficiaries,
                'in_direct_benefi' => $center->in_direct_beneficiaries,
            ];

            // Add center translations
            foreach ($languages as $code => $lang) {
                $tran = $centerTrans[$centerId]->firstWhere('language_name', $code);
                $item["health_centers_$lang"] = json_decode($tran->health_center ?? '[]', true);
                $item["address_$lang"] = $tran->address ?? null;
                $item["health_worker_$lang"] = json_decode($tran->health_worker ?? '[]', true);
                $item["fin_admin_employees_$lang"] = json_decode($tran->managment_worker ?? '[]', true);
            }

            // Add unique districts
            $item['district'] = [];
            $item['villages'] = [];

            foreach ($districts[$centerId] ?? [] as $district) {
                // Add district (only once)
                if (!collect($item['district'])->contains('id', $district->district_id)) {
                    $item['district'][] = [
                        'id' => $district->district_id,
                        'name' => $district->district_name, // only app language
                    ];
                }

                // Add villages in all languages for this district
                $villageData = ['district_id' => $district->district_id];
                foreach ($languages as $code => $lang) {
                    $tran = $districtTrans[$district->id]->firstWhere('language_name', $code);
                    $villageData["village_$lang"] = json_decode($tran->villages ?? '[]', true);
                }
                $item['villages'][] = $villageData;
            }

            $result[] = $item;
        }

        return $result = [
            'start_date' => $project->start_date,
            'end_date' => $project->end_date,
            'donor' => ['id' => $project->donor_id, 'name' => $project->name, 'created_at' => ''],
            'donor_register_no' => $project->donor_registration_no,
            'currency' => ['id' => $project->currency_id, 'name' => $project->currency_name, 'symbol' => $project->symbol],
            'budget' => $project->budget,
            'centers_list' => $result,
            'optional_lang' => $locale

        ];
        return response()->json(
            $result,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function details($id)
    {
        $map = [
            'preamble'           => 'preamble',
            'health_experience'  => 'exper_in_health',
            'goals'              => 'goals',
            'objectives'         => 'objective',
            'expected_outcome'   => 'expected_outcome',
            'expected_impact'    => 'expected_impact',
            'subject'            => 'subject',
            'main_activities'    => 'main_activities',
            'introduction'       => 'project_intro',
            'operational_plan'   => 'action_plan',
            'mission'            => 'mission',
            'vission'            => 'vission',
            'terminologies'      => 'abbreviat',
            'name'               => 'project_name',
            'organization_senior_manangement'   => 'organization_sen_man',
            'project_structure'  => 'project_structure',
        ];

        // Fetch project translations in ONE query
        $translations = DB::table('project_trans')
            ->where('project_id', $id)
            ->whereIn('language_name', [
                LanguageEnum::default->value,
                LanguageEnum::farsi->value,
                LanguageEnum::pashto->value,
            ])
            ->get()
            ->keyBy('language_name');

        // Build the result dynamically
        $result = [];
        foreach ($map as $dbColumn => $aliasBase) {
            $result["{$aliasBase}_english"] = $translations[LanguageEnum::default->value]->{$dbColumn} ?? null;
            $result["{$aliasBase}_farsi"]   = $translations[LanguageEnum::farsi->value]->{$dbColumn} ?? null;
            $result["{$aliasBase}_pashto"]  = $translations[LanguageEnum::pashto->value]->{$dbColumn} ?? null;
        }

        return response()->json(
            $result,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function structure($id)
    {
        $locale = App::getLocale();
        // 1. Get project and project_manager_id in one query
        $project = DB::table('projects as pro')->where('pro.id', $id)
            ->join('project_managers as pm', 'pro.id', 'pm.project_id')
            ->join('managers as m', 'm.id', 'pm.manager_id')
            ->join('emails as em', 'em.id', 'm.email_id')
            ->join('contacts as cnt', 'cnt.id', 'm.contact_id')
            ->join('manager_trans as mt', function ($join) use ($locale) {
                $join->on('mt.manager_id', 'm.id')
                    ->where('language_name', $locale);
            })
            ->select(
                'm.id',
                'em.value as email',
                'cnt.value as contact',
                'mt.full_name',
                'pm.is_active',
                'pm.created_at',
            )
            ->get();
        return response()->json(
            $project,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function checklists(Request $request, $id)
    {
        $locale = App::getLocale();
        $documents = DB::table('documents as doc')
            ->join('check_lists as chk', 'chk.id', 'doc.check_list_id')
            ->join('check_list_trans as chkt', function ($join) use ($locale) {
                $join->on('chkt.check_list_id', 'chk.id')
                    ->where('language_name', $locale);
            })
            ->join('project_documents as pdoc', 'doc.id', 'pdoc.document_id')
            ->where('pdoc.project_id', $id)
            ->select(
                'doc.path',
                'doc.id as document_id',
                'doc.size',
                'chk.id as checklist_id',
                'doc.type',
                'doc.actual_name as name',
                'chkt.value as checklist_name',
                'chk.acceptable_extensions',
                'chk.acceptable_mimes'
            )->get();

        return response()->json(
            $documents,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function projectStatistics(Request $request)
    {
        $authOrganization = $request->user();

        $result = DB::table('projects')
            ->leftJoin('project_details', 'projects.id', '=', 'project_details.project_id')
            ->where('projects.organization_id', $authOrganization->id)
            ->select(
                DB::raw('COUNT(DISTINCT projects.id) as total_projects'),
                DB::raw('COALESCE(SUM(project_details.budget), 0) as total_budget'),
                DB::raw('COALESCE(SUM(project_details.direct_beneficiaries), 0) as total_direct_beneficiaries'),
                DB::raw('COALESCE(SUM(project_details.in_direct_beneficiaries), 0) as total_in_direct_beneficiaries')
            )
            ->first();

        return response()->json([
            'counts' => [
                'total_projects' => $result->total_projects,
                'total_budget' => $result->total_budget,
                'total_direct_beneficiaries' => $result->total_direct_beneficiaries,
                'total_in_direct_beneficiaries' => $result->total_in_direct_beneficiaries,
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
