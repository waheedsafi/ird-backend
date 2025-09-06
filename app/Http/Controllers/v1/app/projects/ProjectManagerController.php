<?php

namespace App\Http\Controllers\v1\app\projects;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;

class ProjectManagerController extends Controller
{
    public function names($organizationId)
    {
        $locale = App::getLocale();
        $query =    DB::table('managers as pm')
            ->where('pm.organization_id', $organizationId)
            ->join('manager_trans as pmt', function ($join) use ($locale) {
                $join->on('pm.id', '=', 'pmt.manager_id')
                    ->where('language_name', $locale);
            })
            ->select(
                'pm.id',
                'pmt.full_name as name'
            )->get();

        return response()->json(
            $query,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function uniqueNames(Request $request)
    {
        $locale = App::getLocale();
        $organization_id = $request->route('organization_id');
        $project_id = $request->route('project_id');
        $query = DB::table('managers as pm')
            ->where('pm.organization_id', $organization_id)
            ->join('manager_trans as pmt', function ($join) use ($locale) {
                $join->on('pm.id', '=', 'pmt.manager_id')
                    ->where('language_name', $locale);
            })
            ->select(
                'pm.id',
                'pmt.full_name as name'
            )->get();

        return response()->json(
            $query,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
