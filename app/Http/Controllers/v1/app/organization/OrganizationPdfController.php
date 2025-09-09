<?php

namespace App\Http\Controllers\v1\app\organization;

use ZipArchive;
use Illuminate\Http\Request;
use App\Traits\PdfGeneratorTrait;
use Illuminate\Support\Facades\DB;
use App\Enums\Types\AboutStaffEnum;
use App\Http\Controllers\Controller;

class OrganizationPdfController extends Controller
{
    use PdfGeneratorTrait;
    // public function generateForm(Request $request, $id)
    // {
    //     // return $request;
    //     $mpdf =  $this->generatePdf();
    //     $this->setWatermark($mpdf);
    //     $lang = $request->input('language_name');
    //     $lang = ['en','ps','fa'];

    //     // $this->setFooter($mpdf, PdfFooterEnum::REGISTER_FOOTER->value);
    //     // $this->setFooter($mpdf, PdfFooterEnum::MOU_FOOTER_en->value);

    //     foreach($lang as $key){
    //         $data = $this->loadOrganizationData($key, $id);

    //     }



    //     // return view('project.mou.pdf.');
    //     // $this->pdfFilePart($mpdf, "project.mou.pdf.{$lang}.mou", $data);
    //     $this->pdfFilePart($mpdf, "organization.registeration.{$lang}.registeration", $data);
    //     // Write additional HTML content

    //     // $mpdf->AddPage();


    //     $mpdf->SetProtection(
    //         ['print'],  // Permissions (Disallow Copy & Print)

    //     );

    //     // Output the generated PDF to the browser
    //     return $mpdf->Output('document.pdf', 'D'); // Stream PDF to browser

    // }



    public function generateForm(Request $request, $id)
    {
        $languages = ['en', 'ps', 'fa'];
        $pdfFiles = [];



        foreach ($languages as $lang) {
            $mpdf = $this->generatePdf();

            $this->setWatermark($mpdf);
            $data = $this->loadOrganizationData($lang, $id);
            // return "organization.registeration.{$lang}.registeration";
            // Generate PDF content
            $this->pdfFilePart($mpdf, "organization.registeration.{$lang}.registeration", $data);
            // $this->pdfFilePart($mpdf, "organization.registeration.{$lang}.registeration", $data);
            $mpdf->SetProtection(['print']);

            // Store the PDF temporarily

            $fileName = "{$data['ngo_name']}_registration_{$lang}.pdf";
            $outputPath = storage_path("app/private/temp/");
            if (!is_dir($outputPath)) {
                mkdir($outputPath, 0755, true);
            }
            $filePath = $outputPath . $fileName;

            // return $filePath;
            $mpdf->Output($filePath, 'F'); // Save to file

            $pdfFiles[] = $filePath;
        }

        // Create ZIP file
        $zipFile = storage_path('app/private/documents.zip');
        $zip = new ZipArchive();

        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($pdfFiles as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
        }

        // Delete individual PDFs after zipping
        foreach ($pdfFiles as $file) {
            unlink($file);
        }


        return response()->download($zipFile)->deleteFileAfterSend(true);
    }
    protected function loadOrganizationData($locale = 'en', $id)
    {

        $organization = DB::table('organizations as n')
            ->where('n.id', $id)
            ->join('organization_trans as nt', function ($join) use ($locale) {
                $join->on('nt.organization_id', '=', 'n.id')
                    ->where('nt.language_name', $locale);
            })
            ->join('contacts as c', 'c.id', '=', 'n.contact_id')
            ->join('emails as e', 'e.id', '=', 'n.email_id')
            ->join('addresses as a', 'a.id', '=', 'n.address_id')
            ->join('address_trans as at', function ($join) use ($locale) {
                $join->on('at.address_id', '=', 'a.id')
                    ->where('at.language_name', $locale);
            })
            ->join('district_trans as dt', function ($join) use ($locale) {
                $join->on('dt.district_id', '=', 'a.district_id')
                    ->where('dt.language_name', $locale);
            })
            ->join('province_trans as pt', function ($join) use ($locale) {
                $join->on('pt.province_id', '=', 'a.province_id')
                    ->where('pt.language_name', $locale);
            })
            ->join('country_trans as ct', function ($join) use ($locale) {
                $join->on('ct.country_id', '=', 'n.place_of_establishment')
                    ->where('ct.language_name', $locale);
            })
            ->select(
                'n.id',
                'n.registration_no',
                'n.moe_registration_no',
                'n.abbr',
                'n.date_of_establishment',
                'nt.name',
                'nt.vision',
                'nt.mission',
                'nt.general_objective',
                'nt.objective',
                'c.value as contact',
                'e.value as email',
                'dt.value as district',
                'dt.district_id',
                'at.area',
                'pt.value as province',
                'pt.province_id',
                'ct.value as country',
            )
            ->first();

        $director =  DB::table('directors as d')
            ->where('d.organization_id', $id)
            ->where('d.is_active', true)
            ->join('director_trans as dirt', function ($join) use ($locale) {
                $join->on('dirt.director_id', '=', 'd.id')
                    ->where("dirt.language_name", $locale);
            })
            ->join('addresses as a', 'a.id', '=', 'd.address_id')
            ->join('address_trans as at', function ($join) use ($locale) {
                $join->on('at.address_id', '=', 'a.id')
                    ->where('at.language_name', $locale);
            })
            ->join('district_trans as dt', function ($join) use ($locale) {
                $join->on('dt.district_id', '=', 'a.district_id')
                    ->where('dt.language_name', $locale);
            })
            ->join('province_trans as pt', function ($join) use ($locale) {
                $join->on('pt.province_id', '=', 'a.province_id')
                    ->where('pt.language_name', $locale);
            })
            ->join('nationality_trans as nt', function ($join) use ($locale) {
                $join->on('nt.nationality_id', '=', 'd.nationality_id')
                    ->where('nt.language_name', $locale);
            })
            ->select(
                'dirt.name',
                'dirt.last_name',
                'dt.value as district',
                'dt.district_id',
                'pt.value as province',
                'pt.province_id',
                'nt.value as country',
                'at.area',
            )
            ->first();
        if (!$director) {
            return "Director not found";
        }
        $irdDirector = DB::table('about_staff as s')
            ->where('s.about_staff_type_id', AboutStaffEnum::director->value)
            ->join('about_staff_trans as st', function ($join) use ($locale) {
                $join->on('st.about_staff_id', '=', 's.id')
                    ->where("st.language_name", $locale);
            })
            ->select(
                'st.name',
            )
            ->first();
        if (!$irdDirector) {
            return "IRD Director not found";
        }
        $data = [
            'register_number' => $organization->registration_no,
            'date_of_sign' => '................',
            'ngo_name' =>  $organization->name,
            'abbr' => $organization->abbr,
            'contact' => $organization->contact,
            'address' => $organization->area . ',' . $organization->district . ',' . $organization->province . ',' . $organization->country,
            'director' => $director->name . " " . $director->last_name,
            'director_address' =>  $director->area . ',' . $director->district . ',' . $director->province . ',' . $director->country,
            'email' => $organization->email,
            'establishment_date' => $organization->date_of_establishment,
            'place_of_establishment' => $organization->country,
            'ministry_economy_no' => $organization->moe_registration_no,
            'general_objective' => $organization->general_objective,
            'afganistan_objective' => $organization->objective,
            'mission' => $organization->mission,
            'vission' => $organization->vision,
            'ird_director' => $irdDirector->name,
        ];


        return $data;
    }
}
