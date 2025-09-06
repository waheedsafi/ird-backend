<?php

namespace App\Http\Controllers\v1\template;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $locale = App::getLocale();
        $auth_user_id = $request->user()->id;

        // Read _limit and _page from query params, set defaults
        $limit = $request->query('_limit', 10); // e.g. ?_limit=10
        $page = $request->query('_page', 1);    // e.g. ?_page=2
        $offset = ($page - 1) * $limit;

        // Paginated query
        $notifications = DB::table('notifications as n')
            ->where('n.user_id', $auth_user_id)
            ->join('notification_trans as nt', function ($join) use (&$locale) {
                $join->on('nt.notification_id', '=', 'n.id')
                    ->where('nt.language_name', $locale);
            })
            ->select(
                'n.id',
                'nt.message',
                'n.is_read',
                'n.action_url',
                'n.context',
                'n.created_at'
            )
            ->orderByDesc('n.id')
            ->offset($offset)
            ->limit($limit)
            ->get();

        // Count unread notifications
        $unreadCount = DB::table('notifications')
            ->where('user_id', $auth_user_id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function update(Request $request)
    {
        $auth_user_id = $request->user()->id;

        // Update notifications only for this user
        DB::table('notifications')
            ->where('user_id', $auth_user_id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
