<?php

namespace App\Import;

use Illuminate\Support\ServiceProvider;

class FileImportServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('import', function ($app) {
            return new FileImportService();
        });
    }

    public function boot()
    {

    }
}
