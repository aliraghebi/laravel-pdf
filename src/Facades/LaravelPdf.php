<?php

namespace ArsamMe\LaravelPdf\Facades;

use ArsamMe\LaravelPdf\PdfFile as Pdf;
use ArsamMe\LaravelPdf\LaravelPdfWrapper;
use Illuminate\Support\Facades\Facade as BaseFacade;

class LaravelPdf extends BaseFacade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return LaravelPdfWrapper::class;
    }
}
