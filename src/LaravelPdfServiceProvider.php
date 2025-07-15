<?php

namespace ArsamMe\LaravelPdf;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class LaravelPdfServiceProvider extends ServiceProvider
{

    /**
     * Boot the service provider
     * 
     * @return void
     */

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/pdf.php' => config_path("pdf.php"),
        ], "mpdf-config");
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {    
        $this->mergeConfigFrom(
            __DIR__ . '/../config/pdf.php', 'pdf'
        );
    }

}
