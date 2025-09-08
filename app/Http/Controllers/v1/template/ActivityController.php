<?php

namespace App\Http\Controllers\v1\template;

use App\Traits\FilterTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class ActivityController extends Controller
{
    use FilterTrait;
    public function activities(Request $request)
    {
        $locale = App::getLocale();
        $tr = [];
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page

        $query = DB::table('user_login_logs as log')

            // Join with users table when userable_type = 'User'
            ->leftJoin(DB::raw("(
        SELECT id, username, profile, 'User' as user_type
        FROM users
    ) as usr"), function ($join) {
                $join->on('log.userable_id', '=', 'usr.id')
                    ->whereRaw("log.userable_type = usr.user_type");
            })

            // Join with organizations table when userable_type = 'Organization'
            ->leftJoin(DB::raw("(
        SELECT id, username, profile, 'Organization' as user_type
        FROM organizations
    ) as org"), function ($join) {
                $join->on('log.userable_id', '=', 'org.id')
                    ->whereRaw("log.userable_type = org.user_type");
            })

            // Select fields (merged username/profile using COALESCE)
            ->select(
                "log.id",
                DB::raw("COALESCE(usr.username, org.username) as username"),
                DB::raw("COALESCE(usr.profile, org.profile) as profile"),
                "log.userable_type",
                "log.action",
                "log.ip_address",
                "log.browser",
                "log.platform",
                "log.created_at as date"
            );


        // Apply pagination (ensure you're paginating after sorting and filtering)
        $tr = $query->paginate($perPage, ['*'], 'page', $page);
        return response()->json(
            [
                "logs" => $tr,
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
