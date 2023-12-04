<?php

namespace App\Providers;

use Barryvdh\DomPDF\Facade\pdf as PDF;
use Illuminate\Support\ServiceProvider;

class DomPDFServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('dompdf', function ($app) {
            return PDF::loadView('your.view'); // You may customize this as needed
        });

        $this->app->alias('dompdf', \Barryvdh\DomPDF\Facade::class); // Correct import statement
    }
}
