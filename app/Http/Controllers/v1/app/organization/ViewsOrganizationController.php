<?php

namespace App\Http\Controllers\v1\app\organization;

use Illuminate\Http\Request;
use App\Enums\Types\TaskTypeEnum;
use App\Enums\Statuses\StatusEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Enums\Languages\LanguageEnum;
use Illuminate\Support\Facades\Cache;
use App\Repositories\Organization\OrganizationRepositoryInterface;
use App\Repositories\PendingTask\PendingTaskRepositoryInterface;
use App\Traits\FilterTrait;

class ViewsOrganizationController extends Controller
{
    use FilterTrait;
    private  $cacheName = 'organization_statistics';
    protected $organizationRepository;
    protected $pendingTaskRepository;

    public function __construct(
        PendingTaskRepositoryInterface $pendingTaskRepository,
        OrganizationRepositoryInterface $organizationRepository
    ) {
        $this->organizationRepository = $organizationRepository;
        $this->pendingTaskRepository = $pendingTaskRepository;
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page
        $locale = App::getLocale();

        $query = DB::table('organizations as n')
            ->join('organization_trans as nt', function ($join) use ($locale) {
                $join->on('nt.organization_id', '=', 'n.id')
                    ->where('nt.language_name', $locale);
            })
            ->join('agreements as a', function ($join) {
                $join->on('a.organization_id', '=', 'n.id')
                    ->whereRaw('a.id = (select max(ns2.id) from agreements as ns2 where ns2.organization_id = n.id)');
            })
            ->join('agreement_statuses as ags', function ($join) {
                $join->on('ags.agreement_id', '=', 'a.id')
                    ->where('ags.is_active', true);
            })
            ->join('status_trans as st', function ($join) use ($locale) {
                $join->on('st.status_id', '=', 'ags.status_id')
                    ->where('st.language_name', $locale);
            })
            ->join('organization_type_trans as ntt', function ($join) use ($locale) {
                $join->on('ntt.organization_type_id', '=', 'n.organization_type_id')
                    ->where('ntt.language_name', $locale);
            })
            ->join('emails as e', 'e.id', '=', 'n.email_id')
            ->join('contacts as c', 'c.id', '=', 'n.contact_id')
            ->select(
                'n.id',
                'n.profile',
                'n.registration_no',
                'n.date_of_establishment as establishment_date',
                'st.status_id',
                'st.name as status',
                'nt.name',
                'ntt.organization_type_id as type_id',
                'ntt.value as type',
                'e.value as email',
                'c.value as contact',
                'n.created_at'
            );

        $this->applyDate($query, $request, 'n.created_at', 'n.created_at');
        $this->applyFilters($query, $request, [
            'name' => 'nt.name',
            'type' => 'ntt.value',
            'contact' => 'c.value',
            'status' => 'nstr.name'
        ]);
        $this->applySearch($query, $request, [
            'registration_no' => 'n.registration_no',
            'name' => 'nt.name',
            'type' => 'ntt.value',
            'contact' => 'c.value',
            'email' => 'e.value'
        ]);

        $result = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'organizations' => $result
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function publicOrganizations(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page
        $locale = App::getLocale();
        $includedIds  = [StatusEnum::registered->value];

        $query = $this->organizationRepository->organization();  // Start with the base query
        $this->organizationRepository->transJoin($query, $locale)
            ->statusJoin($query)
            ->statusTypeTransJoin($query, $locale)
            ->typeTransJoin($query, $locale)
            ->directorJoin($query)
            ->directorTransJoin($query, $locale)
            ->emailJoin($query)
            ->contactJoin($query);
        $query->whereIn('ns.status_id', $includedIds)
            ->select(
                'n.id',
                'n.abbr',
                'stt.name as status',
                'nt.name',
                'ntt.value as type',
                'dt.name as director',
            );

        $this->applyFilters($query, $request, [
            'name' => 'nt.name',
            'type' => 'ntt.value',
            'contact' => 'c.value',
            'status' => 'nstr.name'
        ]);
        $this->applySearch($query, $request, [
            'registration_no' => 'n.registration_no',
            'name' => 'nt.name',
            'type' => 'ntt.value',
            'contact' => 'c.value',
            'email' => 'e.value'
        ]);
        // Now paginate the result (after mapping provinces)
        $result = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'organizations' => $result
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }


    public function startRegisterForm(Request $request, $organization_id)
    {
        $locale = App::getLocale();

        $pendingTaskContent = $this->pendingTaskRepository->pendingTask($request, TaskTypeEnum::organization_registeration->value, $organization_id);
        if ($pendingTaskContent['content']) {
            return response()->json([
                'message' => __('app_translation.success'),
                'content' => $pendingTaskContent['content']
            ], 200);
        }

        $data = $this->organizationRepository->startRegisterFormInfo($organization_id, $locale);
        if (!$data) {
            return response()->json([
                'message' => __('app_translation.organization_not_found'),
            ], 404);
        }

        return response()->json([
            'organization' => $data,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function details($organization_id)
    {
        $locale = App::getLocale();
        $data = $this->organizationRepository->afterRegisterFormInfo($organization_id, $locale);
        if (!$data) {
            return response()->json([
                'message' => __('app_translation.organization_not_found'),
            ], 404);
        }

        return response()->json([
            'organization' => $data,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function startExtendForm(Request $request, $organization_id)
    {
        $locale = App::getLocale();
        $pendingTaskContent = $this->pendingTaskRepository->pendingTask($request, TaskTypeEnum::organization_agreement_extend->value, $organization_id);
        if ($pendingTaskContent['content']) {
            return response()->json([
                'message' => __('app_translation.success'),
                'content' => $pendingTaskContent['content']
            ], 200);
        }

        $data = $this->organizationRepository->afterRegisterFormInfo($organization_id, $locale);
        if (!$data) {
            return response()->json([
                'message' => __('app_translation.organization_not_found'),
            ], 404);
        }

        return response()->json([
            'organization' => $data,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function status($organization_id)
    {
        $result = DB::table('organizations as n')
            ->where('n.id', '=', $organization_id)
            ->join('agreements as a', function ($join) {
                $join->on('a.organization_id', '=', 'n.id')
                    ->whereRaw('a.id = (select max(ns2.id) from agreements as ns2 where ns2.organization_id = n.id)');
            })
            ->join('agreement_statuses as ags', function ($join) {
                $join->on('ags.agreement_id', '=', 'a.id')
                    ->where('ags.is_active', true);
            })->select(
                'ags.status_id',
            )->first();
        if (!$result) {
            return response()->json([
                'message' => __('app_translation.organization_status_not_found'),
            ], 404);
        }

        return response()->json($result, 200, [], JSON_UNESCAPED_UNICODE);
    }


    public function moreInformation($id)
    {
        $query = $this->organizationRepository->organization($id);  // Start with the base query
        $this->organizationRepository->transJoinLocales($query);
        $organizations = $query->select(
            'nt.vision',
            'nt.mission',
            'nt.general_objective',
            'nt.objective',
            'nt.language_name'
        )->get();

        $result = [];
        foreach ($organizations as $item) {
            $language = $item->language_name;

            if ($language === LanguageEnum::default->value) {
                $result['vision_english'] = $item->vision;
                $result['mission_english'] = $item->mission;
                $result['general_objes_english'] = $item->general_objective;
                $result['objes_in_afg_english'] = $item->objective;
            } elseif ($language === LanguageEnum::farsi->value) {
                $result['vision_farsi'] = $item->vision;
                $result['mission_farsi'] = $item->mission;
                $result['general_objes_farsi'] = $item->general_objective;
                $result['objes_in_afg_farsi'] = $item->objective;
            } else {
                $result['vision_pashto'] = $item->vision;
                $result['mission_pashto'] = $item->mission;
                $result['general_objes_pashto'] = $item->general_objective;
                $result['objes_in_afg_pashto'] = $item->objective;
            }
        }

        return response()->json([
            'organization' => $result,

        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function agreementStatuses($id)
    {

        $locale = App::getLocale();
        $result = DB::table('organizations as n')
            ->where('n.id', '=', $id)
            ->join('agreements as a', function ($join) {
                $join->on('a.organization_id', '=', 'n.id');
            })
            ->join('agreement_statuses as ags', function ($join) {
                $join->on('ags.agreement_id', '=', 'a.id')
                    ->where('ags.is_active', true);
            })
            ->join('status_trans as st', function ($join) use ($locale) {
                $join->on('st.status_id', '=', 'ags.status_id')
                    ->where('st.language_name', $locale);
            })->select(
                'n.id as organization_id',
                'ns.id',
                'ns.comment',
                'st.status_id',
                'st.name',
                'ns.userable_type',
                'ns.is_active',
                'ns.created_at',
            )->get();

        return response()->json([
            'statuses' => $result,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function headerInfo($organization_id)
    {
        $locale = App::getLocale();
        // 1. Get organization information
        $organization = DB::table('organizations as n')->where('n.id', $organization_id)
            ->join('agreements as a', function ($join) {
                $join->on('a.organization_id', '=', 'n.id')
                    ->whereRaw('a.id = (select max(ns2.id) from agreements as ns2 where ns2.organization_id = n.id)');
            })
            ->join('agreement_statuses as ags', function ($join) {
                $join->on('ags.agreement_id', '=', 'a.id')
                    ->where('ags.is_active', true);
            })
            ->join('status_trans as st', function ($join) use ($locale) {
                $join->on('st.status_id', '=', 'ags.status_id')
                    ->where('st.language_name', $locale);
            })
            ->join('emails as e', 'e.id', '=', 'n.email_id')
            ->join('contacts as c', 'c.id', '=', 'n.contact_id')
            ->select(
                'n.profile',
                'n.username',
                'c.value as contact',
                'e.value as email',
                'st.status_id',
                'st.name as status',
                'st.status_id',
                'st.name as status',
            )->first();
        if (!$organization) {
            return response()->json([
                'message' => __('app_translation.organization_not_found'),
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }
        $result = [
            "profile" => $organization->profile,
            "status_id" => $organization->status_id,
            "username" => $organization->username,
            "contact" => $organization->contact,
            "email" => $organization->email,
            "status" => $organization->status,
        ];
        return response()->json([
            'organization' => $result,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function organizationStatistics()
    {
        $registered = StatusEnum::registered->value;
        $block = StatusEnum::block->value;
        $locale = App::getLocale();

        $statistics = Cache::remember($this->cacheName . "_{$locale}", 180, function () use ($registered, $block) {
            return DB::select("
            SELECT
                COUNT(n.id) AS \"organizationCount\",
                (SELECT COUNT(*) FROM organizations WHERE DATE(created_at) = CURRENT_DATE) AS \"todayCount\",
                (
                    SELECT COUNT(*)
                    FROM organizations n2
                    INNER JOIN organization_statuses ns ON ns.organization_id = n2.id
                    WHERE ns.status_id = ?
                ) AS \"activeCount\",
                (
                    SELECT COUNT(*)
                    FROM organizations n3
                    INNER JOIN organization_statuses ns ON ns.organization_id = n3.id
                    WHERE ns.status_id = ?
                ) AS \"inActiveCount\"
            FROM organizations n
        ", [$registered, $block]);
        });

        return response()->json([
            'counts' => [
                "count" => $statistics[0]->organizationCount,
                "todayCount" => $statistics[0]->todayCount,
                "activeCount" => $statistics[0]->activeCount,
                "unRegisteredCount" => $statistics[0]->inActiveCount,
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
