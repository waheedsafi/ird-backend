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

        // Start building the query
        // $query = DB::table('user_login_logs as log')
        //     ->leftJoin(DB::raw('(SELECT id, username, profile, "User" as user_type FROM users 
        //                      UNION ALL 
        //                      SELECT id, username, profile, "Ngo" as user_type FROM ngos) as usr'), function ($join) {
        //         $join->on('log.userable_id', '=', 'usr.id')
        //             ->whereRaw('log.userable_type = usr.user_type');
        //     })
        //     ->select(
        //         "log.id",
        //         "usr.username",
        //         "usr.profile",
        //         "log.userable_type",
        //         "log.action",
        //         "log.ip_address",
        //         "log.browser",
        //         "log.device",
        //         "log.created_at as date",
        //     );
        $query = DB::table('user_login_logs as log')
            ->leftJoin(DB::raw('(
        SELECT id, username, profile, \'User\' as user_type 
        FROM users
    ) as usr'), function ($join) {
                $join->on('log.userable_id', '=', 'usr.id')
                    ->whereRaw('log.userable_type = usr.user_type');
            })
            ->select(
                "log.id",
                "usr.username",
                "usr.profile",
                "log.userable_type",
                "log.action",
                "log.ip_address",
                "log.browser",
                "log.platform",
                "log.created_at as date",
            );
        $this->applyDate($query, $request, 'log.created_at', 'log.created_at');
        $this->applySearch($query, $request, [
            'username' => 'usr.username',
            'action' => 'log.action',
            'ip_address' => 'log.ip_address'
        ]);

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
