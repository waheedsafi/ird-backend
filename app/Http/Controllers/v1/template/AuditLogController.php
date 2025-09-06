<?php

namespace App\Http\Controllers\v1\template;

use Mpdf\Mpdf;
use App\Models\User;
use App\Models\Audit;
use App\Traits\FilterTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Schema;

class AuditLogController extends Controller
{
    use FilterTrait;

    public function edit($id)
    {
        $userType = DB::table('audits')->where('id', $id)->value('user_type');
        $audit = DB::table('audits as a')
            ->where('a.id', $id);
        if ($userType === "User") {
            $audit = $audit->leftJoin('users as u', 'a.user_id', '=', 'u.id')
                ->select(
                    'a.id',
                    'a.user_type',
                    'a.user_id',
                    'u.username',
                    'a.event',
                    'a.auditable_type as table',
                    'a.auditable_id',
                    'a.old_values',
                    'a.new_values',
                    'a.url',
                    'a.ip_address',
                    'a.user_agent',
                    'a.created_at',
                )
                ->first();
        }

        return response()->json(
            $audit,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function index(Request $request)
    {
        $tr = [];
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);

        $filters = $request->input('filters');

        $userType = $filters['filterBy']['userType']['name'] ?? null;
        $userId   = $filters['filterBy']['user']['id'] ?? null;
        $event    = $filters['filterBy']['event'] ?? null;
        $table    = $filters['filterBy']['table']['name'] ?? null;

        $query = DB::table('audits as a');
        if ($userType === 'Users')
            $query->where('a.user_type', 'User');
        if ($userId !== '0')
            $query->where('a.user_id', $userId);
        $query->where('a.auditable_type', $table);
        if ($event !== 'All')
            $query->where('a.event', $event);
        $query->where('a.auditable_type', $table)
            ->select(
                "a.id",
                "a.user_type",
                "a.user_id",
                "a.event",
                "a.auditable_type as table",
                "a.auditable_id",
                "a.url",
                "a.ip_address",
                "a.created_at",
            );
        $this->applyDate($query, $request, 'a.created_at', 'a.created_at');
        $this->applyFilters($query, $request, []);
        $this->applySearch($query, $request, [
            'user' => 'a.user_type',
            'event' => 'a.event',
        ]);

        $tr = $query->paginate($perPage, ['*'], 'page', $page);
        return response()->json(
            [
                "audits" => $tr,
            ],
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function userTypes()
    {
        $arr = [
            ["name" => __('front_end.users')],
        ];
        return response()->json(
            $arr,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function events()
    {
        $arr = [
            [
                "id" => -1,
                "name" => __('app_translation.all')
            ],
            [
                "id" => -2,
                "name" => __('app_translation.deleted')
            ],
            [
                "id" => -3,
                "name" => __('app_translation.created')
            ],
            [
                "id" => -4,
                "name" => __('app_translation.updated')
            ],
        ];
        return response()->json(
            $arr,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function users(Request $request)
    {
        $request->validate([
            'user_type' => 'required'
        ]);
        $tr = [];
        if ($request->user_type === 'Users') {
            $tr = DB::table('users as u')->select('u.id', 'u.username as name')->get();
        }
        $tr->prepend([
            'id' => 0,
            'name' => __('app_translation.all'),
        ]);
        return response()->json(
            $tr,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
    public function tables(Request $request)
    {
        $request->validate([
            'user_type' => 'required',
            'id' => 'required|integer'
        ]);
        $tr = [];
        // Get distinct auditable_type values
        if ($request->user_type == 'Users') {
            $query = Audit::select('auditable_type')
                ->where('user_type', 'User');
            if ($request->id !== '0') {
                $query->where('user_id', $request->id);
            }
            $tr =  $query->distinct()
                ->pluck('auditable_type')
                ->map(fn($item) => ['name' => $item]);
        }

        return response()->json(
            $tr,
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function generatePdfReport(Request $request)
    {
        $filters = $request->input('filters');

        $userType = $filters['filterBy']['userType']['name'] ?? null;
        $userId   = $filters['filterBy']['user']['id'] ?? null;
        $event    = $filters['filterBy']['event'] ?? null;
        $table    = $filters['filterBy']['table']['name'] ?? null;

        $query = DB::table('audits as a');
        if ($userType === 'Users')
            $query->where('a.user_type', 'User');
        if ($userId !== '0')
            $query->where('a.user_id', $userId);
        $query->where('a.auditable_type', $table);
        if ($event !== 'All')
            $query->where('a.event', $event);
        $query->where('a.auditable_type', $table)
            ->select(
                "a.id",
                "a.user_type",
                "a.user_id",
                "a.event",
                "a.auditable_type as table",
                "a.auditable_id",
                "a.url",
                "a.ip_address",
                "a.created_at",
            );
        $this->applyDate($query, $request, 'a.created_at', 'a.created_at');
        $this->applyFilters($query, $request, []);
        $this->applySearch($query, $request, [
            'user' => 'a.user_type',
            'event' => 'a.event',
        ]);

        $mpdf = new Mpdf();

        // Write some HTML content
        $html = '
            <h1>Hello from mPDF!</h1>
            <p>This is a sample PDF generated by mPDF.</p>
            <p>Current date/time:</p>
        ';

        $mpdf->WriteHTML($html);

        // Output PDF inline to browser
        return $mpdf->Output('sample.pdf', 'S');
    }
    public function generateExcelReport(Request $request)
    {
        $filters = $request->input('filters');

        $userType = $filters['filterBy']['userType']['name'] ?? null;
        $userId   = $filters['filterBy']['user']['id'] ?? null;
        $event    = $filters['filterBy']['event'] ?? null;
        $table    = $filters['filterBy']['table']['name'] ?? null;

        $query = DB::table('audits as a');
        if ($userType === 'Users')
            $query->where('a.user_type', 'User');
        if ($userId !== '0')
            $query->where('a.user_id', $userId);
        $query->where('a.auditable_type', $table);
        if ($event !== 'All')
            $query->where('a.event', $event);
        $query->where('a.auditable_type', $table)
            ->select(
                "a.id",
                "a.user_type",
                "a.user_id",
                "a.event",
                "a.auditable_type as table",
                "a.auditable_id",
                "a.url",
                "a.ip_address",
                "a.created_at",
            );
        $this->applyDate($query, $request, 'a.created_at', 'a.created_at');
        $this->applyFilters($query, $request, []);
        $this->applySearch($query, $request, [
            'user' => 'a.user_type',
            'event' => 'a.event',
        ]);

        $collection = $query->get();

        // Return the Excel download
        return (new FastExcel($collection))->download('filtered-audits.xlsx');
    }

    public function generatePdfReportById($id) {}
    public function generateExcelReportById($id)
    {
        $userType = DB::table('audits')->where('id', $id)->value('user_type');
        $audit = DB::table('audits as a')
            ->where('a.id', $id);
        if ($userType === "User") {
            $audit = $audit->leftJoin('users as u', 'a.user_id', '=', 'u.id')
                ->select(
                    'a.id',
                    'a.user_type',
                    'a.user_id',
                    'u.username',
                    'a.event',
                    'a.auditable_type as table',
                    'a.auditable_id',
                    'a.old_values',
                    'a.new_values',
                    'a.url',
                    'a.ip_address',
                    'a.user_agent',
                    'a.created_at',
                )
                ->get();
        }

        // Return the Excel download
        return (new FastExcel($audit))->download("{$id}_audits.xlsx");
    }
}
