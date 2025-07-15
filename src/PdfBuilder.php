<?php

namespace ArsamMe\LaravelPdf;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Traits\Macroable;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class PdfBuilder implements Responsable
{
    use Macroable;

    protected Mpdf $mpdf;
    protected array $config = [];
    public string $downloadName = '';
    protected array $responseHeaders = [
        'Content-Type' => 'application/pdf',
    ];
    protected ?string $diskName = null;
    protected string $visibility = 'private';

    public function __construct(array $config = [])
    {
        $this->config = $config;

        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $tempDir = $defaultConfig['tempDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        $configGlobal = [
            'mode' => $this->getConfig('mode'),
            'format' => $this->getConfig('format'),
            'orientation' => $this->getConfig('orientation'),
            'default_font_size' => $this->getConfig('default_font_size'),
            'default_font' => $this->getConfig('default_font'),
            'margin_left' => $this->getConfig('margin_left'),
            'margin_right' => $this->getConfig('margin_right'),
            'margin_top' => $this->getConfig('margin_top'),
            'margin_bottom' => $this->getConfig('margin_bottom'),
            'margin_header' => $this->getConfig('margin_header'),
            'margin_footer' => $this->getConfig('margin_footer'),
            'fontDir' => array_merge($fontDirs, [
                $this->getConfig('custom_font_dir')
            ]),
            'fontdata' => array_merge($fontData, $this->getConfig('custom_font_data')),
            'autoScriptToLang' => $this->getConfig('auto_language_detection'),
            'autoLangToFont' => $this->getConfig('auto_language_detection'),
            'tempDir' => ($this->getConfig('temp_dir')) ?: $tempDir,
        ];
        $configMerge = array_merge($configGlobal, $this->config);

        $this->mpdf = new Mpdf(array_merge($defaultConfig, $configMerge));

        $this->mpdf->SetTitle($this->getConfig('title'));
        $this->mpdf->SetSubject($this->getConfig('subject'));
        $this->mpdf->SetKeywords($this->getConfig('keywords'));
        $this->mpdf->SetAuthor($this->getConfig('author'));
        $this->mpdf->SetCreator($this->getConfig('creator'));
        $this->mpdf->SetWatermarkText($this->getConfig('watermark'));
        $this->mpdf->SetWatermarkImage(
            $this->getConfig('watermark_image_path'),
            $this->getConfig('watermark_image_alpha'),
            $this->getConfig('watermark_image_size'),
            $this->getConfig('watermark_image_position')
        );
        $this->mpdf->SetDisplayMode($this->getConfig('display_mode'));

        $this->mpdf->PDFA = $this->getConfig('pdfa') ?: false;
        $this->mpdf->PDFAauto = $this->getConfig('pdfaauto') ?: false;
        $this->mpdf->showWatermarkText = $this->getConfig('show_watermark');
        $this->mpdf->showWatermarkImage = $this->getConfig('show_watermark_image');
        $this->mpdf->watermark_font = $this->getConfig('watermark_font');
        $this->mpdf->watermarkTextAlpha = $this->getConfig('watermark_text_alpha');
        // use active forms
        $this->mpdf->useActiveForms = $this->getConfig('use_active_forms');

        // use_dictionary_lbr
        $this->mpdf->useDictionaryLBR = $this->getConfig('use_dictionary_lbr');
    }

    protected function getConfig($key)
    {
        return $this->config[$key] ?? Config::get('pdf.' . $key);
    }

    /**
     * Get instance mpdf
     * @return Mpdf
     */
    public function getMpdf(): Mpdf
    {
        return $this->mpdf;
    }

    public function getPdf(): string
    {
        return $this->mpdf->Output('', Destination::STRING_RETURN);
    }

    public function name(string $downloadName): self
    {
        if (!str_ends_with(strtolower($downloadName), '.pdf')) {
            $downloadName .= '.pdf';
        }

        $this->downloadName = $downloadName;

        return $this;
    }

    protected function addHeaders(array $headers): self
    {
        $this->responseHeaders = array_merge($this->responseHeaders, $headers);

        return $this;
    }

    protected function hasHeader(string $headerName): bool
    {
        return array_key_exists($headerName, $this->responseHeaders);
    }

    public function isInline(): bool
    {
        if (!$this->hasHeader('Content-Disposition')) {
            return false;
        }

        return str_contains($this->responseHeaders['Content-Disposition'], 'inline');
    }

    public function isDownload(): bool
    {
        if (!$this->hasHeader('Content-Disposition')) {
            return false;
        }

        return str_contains($this->responseHeaders['Content-Disposition'], 'attachment');
    }

    public function download(?string $downloadName = null): self
    {
        $this->downloadName ?: $this->name($downloadName ?? 'download');

        $this->addHeaders([
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $this->downloadName . '"',
        ]);

        return $this;
    }

    public function inline(string $downloadName = ''): self
    {
        $this->name($downloadName);

        $this->addHeaders([
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $this->downloadName . '"',
        ]);

        return $this;
    }

    public function save(string $path): self
    {
        if ($this->diskName) {
            return $this->saveOnDisk($this->diskName, $path);
        }

        $this->mpdf->Output($path, Destination::FILE);

        return $this;
    }

    public function disk(string $diskName, string $visibility = 'private'): self
    {
        $this->diskName = $diskName;
        $this->visibility = $visibility;

        return $this;
    }

    protected function saveOnDisk(string $diskName, string $path): self
    {
        $visibility = $this->visibility;

        Storage::disk($diskName)->put($path, $this->getPdf(), $visibility);

        return $this;
    }


    public function toResponse($request): \Illuminate\Http\Response
    {
        if (!$this->hasHeader('Content-Disposition')) {
            $this->inline($this->downloadName);
        }

        $pdfContent = $this->getPdf();

        return response($pdfContent, 200, $this->responseHeaders);
    }
}