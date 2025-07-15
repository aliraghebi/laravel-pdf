<?php

namespace ArsamMe\LaravelMpdf\Facades;

use ArsamMe\LaravelMpdf\LaravelMpdf as Pdf;
use ArsamMe\LaravelMpdf\LaravelMpdfWrapper;
use Illuminate\Support\Facades\Facade as BaseFacade;

/**
 * Class LaravelMpdf
 * @package ArsamMe\LaravelMpdf\Facades
 *
 * @method static Pdf loadHTML(string $html, ?array $config = [])
 * @method static Pdf loadFile(string $file, ?array $config = [])
 * @method static Pdf loadView(string $view, ?array $data = [], ?array $mergeData = [], ?array $config = [])
 * @method static Pdf chunkLoadHTML(string $separator, string $html, ?array $config = [])
 * @method static Pdf chunkLoadFile(string $separator, string $file, ?array $config = [])
 * @method static Pdf chunkLoadView(string $separator, string $view, ?array $data = [], ?array $mergeData = [], ?array $config = [])
 */
class LaravelMpdf extends BaseFacade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return LaravelMpdfWrapper::class;
    }
}
