<?php

namespace App\Traits;

use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

trait PdfGeneratorTrait
{
    public function generatePdf()
    {
        $configVariables = new \Mpdf\Config\ConfigVariables();
        $fontDirs = $configVariables->getDefaults()['fontDir'];
        $fontVariables = new \Mpdf\Config\FontVariables();
        $fontData = $fontVariables->getDefaults()['fontdata'];

        $mpdf = new \Mpdf\Mpdf([
            'fontDir' => array_merge($fontDirs, [public_path('fonts/amiri'), storage_path('fonts')]),
            'fontdata' => $fontData + [
                'amiri' => [
                    'R'  => 'Amiri-Regular.ttf',
                    'B'  => 'Amiri-Bold.ttf',
                    'I'  => 'Amiri-Italic.ttf',
                    'BI' => 'Amiri-BoldItalic.ttf',
                ]
            ],
            'default_font' => 'amiri',
            'mode' => 'utf-8',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'margin_bottom' => 50,
            'tempDir' => storage_path('fonts'),  // <--- This ensures font cache is written here
        ]);

        return $mpdf;
    }

    public function mergeExistingPdf(Mpdf $mpdf, $pdfPath, $footerHtml)
    {
        $pageCount = $mpdf->setSourceFile($pdfPath);

        for ($i = 1; $i <= $pageCount; $i++) {
            $mpdf->AddPage();
            $templateId = $mpdf->ImportPage($i);
            $mpdf->UseTemplate($templateId);
            $mpdf->SetFooter($footerHtml); // Ensure the footer is set for each page
        }
    }

    public function setWatermark($mpdf, $path = 'app/public/images/moph.png')
    {
        $watermarkImagePath = storage_path($path);
        $mpdf->SetWatermarkImage($watermarkImagePath, 0.2); // Set watermark and opacity
        $mpdf->showWatermarkImage = true; // Enable watermark

    }
    public function setFooter($mpdf, $footerHtml)
    {
        $mpdf->SetHTMLFooter($footerHtml, 'E'); // Even pages
        $mpdf->SetHTMLFooter($footerHtml, 'O'); // Odd pages
    }
    public function pdfFilePart($mpdf, $view, $data = [])
    {
        // Directly pass the associative $data array to the view method
        $part = view($view, $data)->render();
        $mpdf->WriteHTML($part);
    }
}
