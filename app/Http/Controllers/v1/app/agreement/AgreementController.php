<?php

namespace App\Http\Controllers\v1\app\agreement;

use App\Models\Agreement;
use App\Models\CheckList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Repositories\Organization\OrganizationRepositoryInterface;

class AgreementController extends Controller
{
    protected $organizationRepository;

    public function __construct(
        OrganizationRepositoryInterface $organizationRepository
    ) {
        $this->organizationRepository = $organizationRepository;
    }
    public function agreementDocuments(Request $request)
    {
        $organization_id = $request->input('organization_id');
        $agreement_id = $request->input('agreement_id');

        $locale = App::getLocale();
        $query = $this->organizationRepository->Organization($organization_id);
        $documents = $this->organizationRepository->agreementDocuments($query, $agreement_id, $locale);

        return response()->json([
            'agreement_documents' => $documents,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function index(Request $request, $id)
    {
        $data = Agreement::select('id', 'start_date', 'end_date')->where('organization_id', $id)->get();
        return response()->json([
            'message' => __('app_translation.success'),
            'agreement' => $data,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function statuses($id)
    {
        $locale = App::getLocale();
        $result = DB::table('organizations as n')
            ->where('n.id', '=', $id)
            ->join('agreements as a', function ($join) {
                $join->on('a.organization_id', '=', 'n.id')
                    ->whereRaw('a.id = (select max(ns2.id) from agreements as ns2 where ns2.organization_id = n.id)');
            })
            ->join('agreement_statuses as ags', function ($join) {
                $join->on('ags.agreement_id', '=', 'a.id');
            })
            ->join('predefined_comment_trans as pct', function ($join) use ($locale) {
                $join->on('pct.predefined_comment_id', '=', 'ags.predefined_comment_id')
                    ->where('pct.language_name', $locale);
            })
            ->join('status_trans as st', function ($join) use ($locale) {
                $join->on('st.status_id', '=', 'ags.status_id')
                    ->where('st.language_name', $locale);
            })->select(
                'n.id as organization_id',
                'ags.id',
                'pct.value as comment',
                'ags.status_id',
                'st.name',
                'ags.userable_type',
                'ags.is_active',
                'ags.created_at',
            )->orderByDesc('ags.id')
            ->get();

        return response()->json([
            'statuses' => $result,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
