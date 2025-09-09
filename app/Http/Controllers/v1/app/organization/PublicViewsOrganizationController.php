<?php

namespace App\Http\Controllers\v1\app\organization;

use App\Enums\Statuses\StatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;

class PublicViewsOrganizationController extends Controller
{
    //

    public function latestOrganizations()
    {
        $locale = App::getLocale();

        $organizations = DB::table('organizations as org')
            ->join('organization_trans as orgt', function ($join) use ($locale) {
                $join->on('org.id', '=', 'orgt.organization_id')
                    ->where('orgt.locale', $locale);
            })
            ->join('organization_statuses as orgst', function ($join) {
                $join->on('org.id', '=', 'orgst.organization_id')
                    ->where('orgst.is_active', true)
                    ->where('orgst.status_id', StatusEnum::registered->value);
            })
            ->select(
                'org.id',
                'org.profile as logo',
                'orgt.name'
            )
            ->orderBy('org.created_at', 'desc')
            ->limit(6)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'organizations' => $organizations,
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function topOrganizationsByProjects()
    {
        $locale = App::getLocale();

        $organizations = DB::table('organizations as org')
            ->join('organization_trans as orgt', function ($join) use ($locale) {
                $join->on('org.id', '=', 'orgt.organization_id')
                    ->where('orgt.locale', $locale);
            })
            ->join('organization_statuses as orgst', function ($join) {
                $join->on('org.id', '=', 'orgst.organization_id')
                    ->where('orgst.is_active', true)
                    ->where('orgst.status_id', StatusEnum::registered->value);
            })
            ->leftJoin('projects as p', 'p.organization_id', '=', 'org.id')
            ->select(
                'org.id',
                'org.profile as logo',
                'orgt.name',
                DB::raw('COUNT(p.id) as projects_count')
            )
            ->groupBy('org.id', 'org.profile', 'orgt.name')
            ->orderByDesc('projects_count')
            ->limit(6)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'organizations' => $organizations,
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
