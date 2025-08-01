<?php

namespace AliRaghebi\LaravelPdf\Facades;

use AliRaghebi\LaravelPdf\LaravelPdfFactory;
use Illuminate\Support\Facades\Facade;

class LaravelPdf extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return LaravelPdfFactory::class;
    }
}
