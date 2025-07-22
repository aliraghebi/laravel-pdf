<?php

namespace AliRaghebi\LaravelPdf;

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
        $this->publishes([__DIR__ . '/../config/pdf.php' => config_path("pdf.php")], "laravel-pdf-config");
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/pdf.php', 'pdf');
    }
}
