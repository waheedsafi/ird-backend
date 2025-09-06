<?php

namespace App\Http\Controllers\v1\template;

use App\Traits\FilterTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class LogController extends Controller
{
    use FilterTrait;

    public function index(Request $request)
    {
        $tr = [];
        $perPage = $request->input('per_page', 10); // Number of records per page
        $page = $request->input('page', 1); // Current page

        // Start building the query
        $query = DB::table('error_logs as el')
            ->select(
                'el.id',
                "el.trace",
                "el.error_code",
                "el.exception_type",
                "el.ip_address",
                "el.method",
                "el.uri",
                'el.created_at'
            );

        $this->applyDate($query, $request, 'created_at', 'el.created_at');
        $this->applyFilters($query, $request, [
            'uri' => 'el.uri',
            'method' => 'el.method',
            'created_at' => 'el.created_at',
        ]);
        $this->applySearch($query, $request, [
            'uri' => 'el.uri',
            'error_code' => 'el.error_code',
            'ip_address' => 'el.ip_address',
            'exception_type' => 'el.exception_type'
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
    public function edit($id)
    {
        $logs = DB::table('error_logs as el')
            ->select(
                'el.id',
                "el.error_message",
                "el.trace",
                "el.error_code",
                "el.exception_type",
                "el.user_id",
                "el.username",
                "el.ip_address",
                "el.method",
                "el.uri",
                'el.created_at'
            )
            ->first();

        return response()->json(
            [
                "id" => $logs->id,
                "error_message" => $logs->error_message ?? null,
                "trace" => $logs->trace ?? null,
                "error_code" => $logs->error_code ?? null,
                "exception_type" => $logs->exception_type ?? null,
                "saved_by" => $logs->username ?? null,
                "ip_address" => $logs->ip_address ?? null,
                "method" => $logs->method ?? null,
                "uri" => $logs->uri ?? null,
                "created_at" => $logs->created_at ?? null,
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
