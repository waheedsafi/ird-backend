<?php

namespace App\Http\Controllers\v1\app\projects;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\ProjectManager;
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
    public function uniqueNames(Request $request, $id)
    {
        $locale = App::getLocale();
        $organizationId = $request->user()->id;

        // Get active project manager_id directly
        $activeManagerId = ProjectManager::where('project_id', $id)
            ->value('manager_id');

        $query = DB::table('managers as m')
            ->join('project_managers as pm', 'm.id', '=', 'pm.manager_id')
            ->join('projects as p', function ($join) use ($organizationId) {
                $join->on('pm.project_id', '=', 'p.id')
                    ->where('p.organization_id', $organizationId);
            })
            ->join('manager_trans as pmt', function ($join) use ($locale) {
                $join->on('pm.id', '=', 'pmt.manager_id')
                    ->where('pmt.language_name', $locale);
            })
            ->when($activeManagerId, function ($q) use ($activeManagerId) {
                // Exclude only if an active manager exists
                $q->where('m.id', '!=', $activeManagerId);
            })
            ->select('pm.id', 'pmt.full_name as name')
            ->get();

        return response()->json($query, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
