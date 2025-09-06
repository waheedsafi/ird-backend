<?php

namespace App\Repositories\Organization;

use App\Models\Document;
use Illuminate\Support\Facades\DB;

class OrganizationRepository implements OrganizationRepositoryInterface
{
    public function organizationProfileInfo($organization_id, $locale)
    {
        $organization = $this->generalQuery($organization_id, $locale)
            ->join('agreements as ag', function ($join) {
                $join->on('ag.organization_id', '=', 'n.id')
                    ->whereRaw('ag.id = (select max(ns2.id) from agreements as ns2 where ns2.organization_id = n.id)');
            })
            ->join('agreement_statuses as ags', function ($join) {
                $join->on('ags.agreement_id', '=', 'ag.id')
                    ->where('ags.is_active', true);
            })
            ->join('status_trans as st', function ($join) use ($locale) {
                $join->on('st.status_id', '=', 'ags.status_id')
                    ->where('st.language_name', $locale);
            })
            ->select(
                'n.id',
                'n.registration_no',
                'n.abbr',
                'n.organization_type_id',
                'ntt.value as organization_type',
                'c.value as contact',
                'e.value as email',
                'dt.value as district',
                'dt.district_id',
                'pt.value as province',
                'pt.province_id',
                'st.name as agreement_status',
                'st.status_id as agreement_status_id',
                // Aggregating the name by conditional filtering for each language
                DB::raw("MAX(CASE WHEN nt.language_name = 'ps' THEN nt.name END) as name_pashto"),
                DB::raw("MAX(CASE WHEN nt.language_name = 'fa' THEN nt.name END) as name_farsi"),
                DB::raw("MAX(CASE WHEN nt.language_name = 'en' THEN nt.name END) as name_english"),
                DB::raw("MAX(CASE WHEN at.language_name = 'ps' THEN at.area END) as area_pashto"),
                DB::raw("MAX(CASE WHEN at.language_name = 'fa' THEN at.area END) as area_farsi"),
                DB::raw("MAX(CASE WHEN at.language_name = 'en' THEN at.area END) as area_english")
            )
            ->groupBy(
                'n.id',
                'n.registration_no',
                'n.abbr',
                'n.organization_type_id',
                'ntt.value',
                'c.value',
                'e.value',
                'dt.value',
                'pt.value',
                'dt.district_id',
                'pt.province_id',
                'st.status_id',
                "st.name"
            )
            ->first();

        return [
            "id" => $organization->id,
            "abbr" => $organization->abbr,
            "name_english" => $organization->name_english,
            "name_farsi" => $organization->name_farsi,
            "name_pashto" => $organization->name_pashto,
            "type" => ['id' => $organization->organization_type_id, 'name' => $organization->organization_type],
            "contact" => $organization->contact,
            "email" => $organization->email,
            "registration_no" => $organization->registration_no,
            "province" => ["id" => $organization->province_id, "name" => $organization->province],
            "district" => ["id" => $organization->district_id, "name" => $organization->district],
            "area_english" => $organization->area_english,
            "area_pashto" => $organization->area_pashto,
            "area_farsi" => $organization->area_farsi,
            "agreement_status_id" => $organization->agreement_status_id,
            "agreement_status" => $organization->agreement_status,
        ];
    }
    public function startRegisterFormInfo($organization_id, $locale)
    {
        $organization = $this->generalQuery($organization_id, $locale)
            ->select(
                'n.id',
                'n.registration_no',
                'n.abbr',
                'n.organization_type_id',
                'ntt.value as organization_type',
                'c.value as contact',
                'e.value as email',
                'dt.value as district',
                'dt.district_id',
                'pt.value as province',
                'pt.province_id',
                // Aggregating the name by conditional filtering for each language
                DB::raw("MAX(CASE WHEN nt.language_name = 'ps' THEN nt.name END) as name_pashto"),
                DB::raw("MAX(CASE WHEN nt.language_name = 'fa' THEN nt.name END) as name_farsi"),
                DB::raw("MAX(CASE WHEN nt.language_name = 'en' THEN nt.name END) as name_english"),
                DB::raw("MAX(CASE WHEN at.language_name = 'ps' THEN at.area END) as area_pashto"),
                DB::raw("MAX(CASE WHEN at.language_name = 'fa' THEN at.area END) as area_farsi"),
                DB::raw("MAX(CASE WHEN at.language_name = 'en' THEN at.area END) as area_english")
            )
            ->groupBy(
                'n.id',
                'n.registration_no',
                'n.abbr',
                'n.organization_type_id',
                'ntt.value',
                'c.value',
                'e.value',
                'dt.value',
                'pt.value',
                'dt.district_id',
                'pt.province_id',
            )
            ->first();

        return [
            "id" => $organization->id,
            "abbr" => $organization->abbr,
            "name_english" => $organization->name_english,
            "name_farsi" => $organization->name_farsi,
            "name_pashto" => $organization->name_pashto,
            "type" => ['id' => $organization->organization_type_id, 'name' => $organization->organization_type],
            "contact" => $organization->contact,
            "email" => $organization->email,
            "registration_no" => $organization->registration_no,
            "province" => ["id" => $organization->province_id, "name" => $organization->province],
            "district" => ["id" => $organization->district_id, "name" => $organization->district],
            "area_english" => $organization->area_english,
            "area_pashto" => $organization->area_pashto,
            "area_farsi" => $organization->area_farsi,
        ];
    }
    public function afterRegisterFormInfo($organization_id, $locale)
    {
        $organization = $this->generalQuery($organization_id, $locale)
            ->join('country_trans as ct', function ($join) use ($locale) {
                $join->on('ct.country_id', '=', 'n.place_of_establishment')
                    ->where('ct.language_name', $locale);
            })
            ->select(
                'n.id',
                'n.registration_no',
                'n.place_of_establishment as country_id',
                'ct.value as country',
                'n.date_of_establishment as establishment_date',
                'n.moe_registration_no',
                'n.abbr',
                'n.organization_type_id',
                'ntt.value as organization_type',
                'c.value as contact',
                'e.value as email',
                'dt.value as district',
                'dt.district_id',
                'pt.value as province',
                'pt.province_id',
                // Aggregating the name by conditional filtering for each language
                DB::raw("MAX(CASE WHEN nt.language_name = 'ps' THEN nt.name END) as name_pashto"),
                DB::raw("MAX(CASE WHEN nt.language_name = 'fa' THEN nt.name END) as name_farsi"),
                DB::raw("MAX(CASE WHEN nt.language_name = 'en' THEN nt.name END) as name_english"),
                DB::raw("MAX(CASE WHEN at.language_name = 'ps' THEN at.area END) as area_pashto"),
                DB::raw("MAX(CASE WHEN at.language_name = 'fa' THEN at.area END) as area_farsi"),
                DB::raw("MAX(CASE WHEN at.language_name = 'en' THEN at.area END) as area_english")
            )
            ->groupBy(
                'n.id',
                'n.registration_no',
                'ct.value',
                'n.place_of_establishment',
                'ct.value',
                'n.date_of_establishment',
                'n.moe_registration_no',
                'n.abbr',
                'n.organization_type_id',
                'ntt.value',
                'c.value',
                'e.value',
                'dt.value',
                'pt.value',
                'dt.district_id',
                'pt.province_id',
            )
            ->first();

        return [
            "id" => $organization->id,
            "abbr" => $organization->abbr,
            "name_english" => $organization->name_english,
            "name_farsi" => $organization->name_farsi,
            "name_pashto" => $organization->name_pashto,
            "type" => ['id' => $organization->organization_type_id, 'name' => $organization->organization_type],
            "contact" => $organization->contact,
            "email" => $organization->email,
            "registration_no" => $organization->registration_no,
            "province" => ["id" => $organization->province_id, "name" => $organization->province],
            "district" => ["id" => $organization->district_id, "name" => $organization->district],
            "area_english" => $organization->area_english,
            "area_pashto" => $organization->area_pashto,
            "area_farsi" => $organization->area_farsi,
            "moe_registration_no" => $organization->moe_registration_no,
            'establishment_date' => $organization->establishment_date,
            'country' => ['id' => $organization->country_id, 'name' => $organization->country],
        ];
    }

    public function agreementDocuments($query, $agreement_id, $locale)
    {
        $document =  Document::join('agreement_documents as agd', 'agd.document_id', 'documents.id')
            ->where('agd.agreement_id',  $agreement_id)
            ->join('check_lists as cl', function ($join) {
                $join->on('documents.check_list_id', '=', 'cl.id');
            })
            ->join('check_list_trans as clt', function ($join) use ($locale) {
                $join->on('clt.check_list_id', '=', 'cl.id')
                    ->where('language_name', $locale);
            })
            ->select(
                'documents.path',
                'documents.id as document_id',
                'documents.size',
                'cl.file_size',
                'documents.check_list_id as checklist_id',
                'documents.type',
                'documents.actual_name as name',
                'clt.value as checklist_name',
                'cl.acceptable_extensions',
                'cl.acceptable_mimes'
            )
            ->get();

        return $document;
    }
    public function statuses($organization_id, $locale)
    {
        $query = $this->organization($organization_id);
        $this->statusJoinAll($query)
            ->statusTypeTransJoin($query, $locale);
        return $query
            ->select(
                'n.id as organization_id',
                'ns.id',
                'ns.comment',
                'ns.status_type_id',
                'stt.name',
                'ns.userable_type',
                'ns.is_active',
                'ns.created_at',
            )->get();
    }
    // Joins
    public function organization($id = null)
    {
        if ($id) {
            return DB::table('organizations as n')->where('n.id', $id);
        } else {
            return DB::table('organizations as n');
        }
    }
    public function transJoin($query, $locale)
    {
        $query->join('organization_trans as nt', function ($join) use ($locale) {
            $join->on('nt.organization_id', '=', 'n.id')
                ->where('nt.language_name', $locale);
        });
        return $this;
    }
    public function transJoinLocales($query)
    {
        $query->join('organization_trans as nt', function ($join) {
            $join->on('nt.organization_id', '=', 'n.id');
        });
        return $this;
    }
    public function statusJoin($query)
    {
        $query->join('organization_statuses as ns', function ($join) {
            $join->on('ns.organization_id', '=', 'n.id')
                ->where('ns.is_active', true);
            // ->whereRaw('ns.created_at = (select max(ns2.created_at) from organization_statuses as ns2 where ns2.organization_id = n.id)');
        });
        return $this;
    }
    public function statusJoinAll($query)
    {
        $query->join('organization_statuses as ns', function ($join) {
            $join->on('ns.organization_id', '=', 'n.id');
        });
        return $this;
    }
    public function statusTransJoin($query, $locale)
    {
        $query->join('status_trans as stt', function ($join) use ($locale) {
            $join->on('stt.status_id', '=', 'ns.status_id')
                ->where('stt.language_name', $locale);
        });
        return $this;
    }
    public function typeTransJoin($query, $locale)
    {
        $query->join('organization_type_trans as ntt', function ($join) use ($locale) {
            $join->on('ntt.organization_type_id', '=', 'n.organization_type_id')
                ->where('ntt.language_name', $locale);
        });
        return $this;
    }
    public function directorJoin($query)
    {
        $query->leftJoin('directors as d', function ($join) {
            $join->on('d.organization_id', '=', 'n.id')
                ->where('d.is_active', true);
        });
        return $this;
    }
    public function directorTransJoin($query, $locale)
    {
        $query->leftJoin('director_trans as dt', function ($join) use ($locale) {
            $join->on('d.id', '=', 'dt.director_id')
                ->where('dt.language_name', $locale);
        });
        return $this;
    }
    public function emailJoin($query)
    {
        $query->join('emails as e', 'e.id', '=', 'n.email_id');
        return $this;
    }
    public function contactJoin($query)
    {
        $query->join('contacts as c', 'c.id', '=', 'n.contact_id');
        return $this;
    }
    public function addressJoin($query)
    {
        $query->join('addresses as a', 'a.id', '=', 'n.address_id');
        return $this;
    }
    public function agreementJoin($query)
    {
        $query->join('agreements as ag', function ($join) {
            $join->on('n.id', '=', 'ag.organization_id')
                ->whereRaw('ag.end_date = (select max(ns2.end_date) from agreements as ns2 where ns2.organization_id = n.id)');
        });
        return $this;
    }
    public function generalQuery($organization_id, $locale)
    {
        return DB::table('organizations as n')
            ->where('n.id', $organization_id)
            ->join('organization_trans as nt', 'nt.organization_id', '=', 'n.id')
            ->join('organization_type_trans as ntt', function ($join) use ($locale) {
                $join->on('ntt.organization_type_id', '=', 'n.organization_type_id')
                    ->where('ntt.language_name', $locale);
            })
            ->join('contacts as c', 'c.id', '=', 'n.contact_id')
            ->join('emails as e', 'e.id', '=', 'n.email_id')
            ->join('addresses as a', 'a.id', '=', 'n.address_id')
            ->join('address_trans as at', 'at.address_id', '=', 'a.id')
            ->join('district_trans as dt', function ($join) use ($locale) {
                $join->on('dt.district_id', '=', 'a.district_id')
                    ->where('dt.language_name', $locale);
            })
            ->join('province_trans as pt', function ($join) use ($locale) {
                $join->on('pt.province_id', '=', 'a.province_id')
                    ->where('pt.language_name', $locale);
            });
    }
}
