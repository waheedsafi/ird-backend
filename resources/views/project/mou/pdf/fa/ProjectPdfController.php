<?php

namespace App\Http\Controllers\v1\app\projects;

use App\Enums\Statuses\StatusEnum;
use App\Traits\AddressTrait;
use Illuminate\Http\Request;
use App\Traits\PdfGeneratorTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectStatus;
use ZipArchive;

class ProjectPdfController extends Controller
{
    //
    use PdfGeneratorTrait, AddressTrait;


    public function generateMou(Request $request, $id)
    {
        $languages = ['en'];
        $pdfFiles = [];

        $organizationId = $request->user()->id;

        // Validate project ownership
        $projectExists = Project::where('id', $id)
            ->where('organization_id', $organizationId)
            ->exists();

        if (! $projectExists) {
            return response()->json([
                'error' => __('app_translation.unauthorized'),

            ], 404);
        }

        // Check if MOU already generated
        $mouExists = ProjectStatus::where('project_id', $id)
            ->where([
                ['status_id', '=', StatusEnum::approved->value],
                ['is_active', '=', true],
            ])->exists();

        if ($mouExists) {
            return response()->json([
                'error' => __('app_translation.unauthorized'),

            ], 400);
        }




        foreach ($languages as $lang) {
            $mpdf = $this->generatePdf();
            $this->setWatermark($mpdf);

            $data = $this->loadProjectData($lang, $id);

            // STEP 1: Configure TOC
            $mpdf->TOC([
                'toc-preHTML' => '<h1 style="text-align:center;">Table of Contents</h1><div class="toc">',
                'toc-postHTML' => '</div>',
                'toc-bookmarkText' => 'Table of Contents',
                'paging' => true,
                'links' => true,
            ]);

            // STEP 2: First part
            $this->pdfFilePart($mpdf, "project.mou.pdf.{$lang}.mouFirstPart", $data);

            // Insert TOC page
            $mpdf->WriteHTML('<tocpagebreak />');
            $mpdf->WriteHTML('
            <style>
                .toc a {
                    cursor: pointer;
                    color: blue;
                    text-decoration: underline;
                }
            </style>
        ');

            // STEP 3: Content before table
            $this->pdfFilePart($mpdf, "project.mou.pdf.{$lang}.mouSecondPart_PortraitBeforeTable", $data);

            // STEP 4: Landscape section
            $mpdf->AddPage('L');
            $this->pdfFilePart($mpdf, "project.mou.pdf.{$lang}.mouSecondPart_LandscapeTableOnly", $data);
            $mpdf->AddPage('P');

            // STEP 5: Content after table
            $this->pdfFilePart($mpdf, "project.mou.pdf.{$lang}.mouSecondPart_PortraitAfterTable", $data);

            // Set protection
            $mpdf->SetProtection(['print']);

            // Store the PDF temporarily
            $fileName = "{$data['ngo_name']}_mou_{$lang}.pdf";
            $outputPath = storage_path("app/private/temp/");
            if (!is_dir($outputPath)) {
                mkdir($outputPath, 0755, true);
            }
            $filePath = $outputPath . $fileName;

            $mpdf->Output($filePath, 'F');
            $pdfFiles[] = $filePath;
        }

        // ✅ Create ZIP file
        $zipFile = storage_path('app/private/mou_documents.zip');
        $zip = new ZipArchive();

        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($pdfFiles as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
        }

        // Cleanup temporary PDFs
        foreach ($pdfFiles as $file) {
            unlink($file);
        }

        // Download ZIP and delete after sending
        return response()->file($zipFile);
    }


    protected function loadProjectData($lang, $id)
    {
        $locale = App::getLocale(); // e.g., 'en', 'fa', 'ps'

        // 1. Main project info with joined donor/currency
        $project = DB::table('projects as p')
            ->join('project_managers as prm', function ($join) {
                $join->on('prm.project_id', 'p.id')
                    ->where('prm.is_active', true);
            })
            ->leftJoin('donor_trans as d', 'd.donor_id', '=', 'p.donor_id')
            ->leftJoin('currency_trans as c', 'c.id', '=', 'p.currency_id')
            ->leftJoin('organization_trans as nt', function ($join) use ($lang) {
                $join->on('p.organization_id', 'nt.organization_id')
                    ->where('nt.language_name', $lang);
            })
            ->leftJoin('project_trans as prot', function ($join) use ($lang) {
                $join->on('p.organization_id', 'nt.organization_id')
                    ->where('nt.language_name', $lang);
            })
            ->where('p.id', $id)
            ->select(

                'p.*',
                'prot.*',
                'prm.manager_id',
                'nt.name as organization_name',
                'nt.vision as organization_vision',
                'nt.mission as organization_mission',
                'nt.introduction as organization_introduction',
                'd.name as donor_name',
                'c.name as currency_name',

            )
            ->first();

        // return $project;

        $focal_point = DB::table('managers as man')
            ->where('man.id', $project->manager_id)
            ->join('emails as e', 'e.id', '=', 'man.email_id')
            ->join('contacts as c', 'c.id', '=', 'man.contact_id')
            ->select(
                'c.value as contact',
                'e.value as email'
            )->first();
        $focal_point = 'Number:' . $focal_point->contact . ',' . 'Email:' . $focal_point->email;


        $organization_director = DB::table('directors as dir')
            ->where('organization_id', $project->organization_id)
            ->where('is_active', true)
            ->join('emails as e', 'e.id', '=', 'dir.email_id')
            ->join('contacts as c', 'c.id', '=', 'dir.contact_id')
            ->select(
                'c.value as contact',
                'e.value as email'
            )->first();
        $organization_director = 'Number:' . $organization_director->contact . ',' . 'Email:' . $organization_director->email;

        // 2. Project translation in selected locale
        $tran = DB::table('project_trans')
            ->where('project_id', $id)
            ->where('language_name', $locale)
            ->first();

        // 3. Project details + translations
        $detailTrans = DB::table('project_details as pd')
            ->leftJoin('project_detail_trans as pdt', 'pdt.project_detail_id', '=', 'pd.id')
            ->leftJoin('province_trans as prot', function ($join) use ($lang) {
                $join->on('prot.province_id', '=', 'pd.province_id')
                    ->where('prot.language_name', $lang);
            })
            ->where('pd.project_id', $id)
            ->where('pdt.language_name', $locale)
            ->select(
                'pd.province_id',
                'prot.value as province_name',
                'pd.budget',
                'pd.direct_beneficiaries',
                'pd.in_direct_beneficiaries',
                'pdt.health_center',
                'pdt.address',
                'pdt.health_worker',
                'pdt.managment_worker'
            )
            ->get();

        $direct_beneficiaries = $detailTrans->sum('direct_beneficiaries');
        $in_direct_beneficiaries = $detailTrans->sum('in_direct_beneficiaries');

        $provinceList = $detailTrans
            ->pluck('province_name')
            ->filter()         // remove null values
            ->unique()         // remove duplicates
            ->implode(',');    // join into comma-separated string



        $data['project_provinces'] = $provinceList;

        $health_worker = $detailTrans
            ->pluck('health_worker')
            ->filter()         // remove null values
            ->unique()         // remove duplicates
            ->implode(',');    // join into comma-separated string



        $data['health_worker'] = $health_worker;

        $managment_worker = $detailTrans
            ->pluck('managment_worker')
            ->filter()         // remove null values
            ->unique()         // remove duplicates
            ->implode(',');    // join into comma-separated string



        $data['managment_worker'] = $managment_worker;







        // 4. Convert health centers (json) to readable list
        $healthFacilities = $detailTrans->map(function ($row) {
            $facilities = json_decode($row->health_center ?? '[]', true);

            // Ensure it's an array before implode
            if (!is_array($facilities)) {
                $facilities = [$facilities]; // or: $facilities = explode(',', $facilities);
            }

            return [
                'province_id' => $row->province_id,
                'facilities' => implode(', ', $facilities),
            ];
        });




        $data = [
            'preamble' => $project->preamble,
            'ngo_name' => $project->name,
            'introduction_ngo' => $project->organization_introduction,
            'abbr' => $project->terminologies,
            'org_vision' => $project->organization_vision,
            'org_mission' =>  $project->organization_mission,
            'org_management_working_area' => $project->organization_senior_manangement,
            'project_structure' => $project->project_structure,
            'backgroud_experince' => $project->health_experience,
            'introduction_current_project' => $project->introduction,
            'health_facilities' => [
                ['province' => 'kabul', 'facilities' => 'arzan_qimat'],
                ['province' => 'logor', 'facilities' => 'pole alam']
            ],
            'goals' => $project->goals,
            'objectives' => $project->objectives,
            'expected_outcomes' => $project->expected_outcome,
            'expected_impact' => $project->expected_impact,
            'subject' => $project->subject,
            'activities' => $project->main_activities,
            'implementing_org' => $project->organization_name,
            'funder' => $project->donor_name,
            'budget' => $project->total_budget,
            'start_date' => $project->start_date,
            'end_date' => $project->end_date,
            'mou_date' => $project->created_at,
            'location' => '',
            'provinces' => $provinceList,
            'areas' => 'areas',
            'direct_beneficiaries' => $direct_beneficiaries,
            'indirect_beneficiaries' => $in_direct_beneficiaries,
            'org_structure' => $project->project_structure,
            'health_staff' => $health_worker,
            'admin_staff' => $managment_worker,
            'action_plan' => $project->operational_plan,
            'ngo_director_contact' => $organization_director,
            'project_focal_point_contact' => $focal_point,
            'project_provinces' => $provinceList,



            'director' => '................',


            'ird_director' => '................',



        ];
        return $data;
    }
}
