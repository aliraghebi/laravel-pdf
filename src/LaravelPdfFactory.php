<?php

namespace ArsamMe\LaravelPdf;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class LaravelPdfFactory
{
    /**
     * @param array $config optional, default []
     * @return PdfBuilder
     */
    public function getPdf(array $config = []): PdfBuilder
    {
        return new PdfBuilder($config);
    }

    /**
     * Load a HTML string
     *
     * @param string $html
     * @param array $config optional, default []
     * @return PdfBuilder
     */
    public function html(string $html, array $config = []): PdfBuilder
    {
        $pdf = $this->getPdf($config);
        $pdf->getMpdf()->WriteHTML($html);

        return $pdf;
    }

    /**
     * Load a HTML file
     *
     * @param string $file
     * @param array $config optional, default []
     * @return PdfBuilder
     */
    public function file(string $file, array $config = []): PdfBuilder
    {
        return $this->html(File::get($file), $config);
    }

    /**
     * Load a View and convert to HTML
     *
     * @param string $view
     * @param array $data
     * @param array $mergeData
     * @param array $config optional, default []
     * @return PdfBuilder
     */
    public function view(string $view, array $data = [], array $mergeData = [], array $config = []): PdfBuilder
    {
        return $this->html(View::make($view, $data, $mergeData)->render(), $config);
    }
}
