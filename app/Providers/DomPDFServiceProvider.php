<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Barryvdh\DomPDF\Facade\pdf as PDF;

class DomPDFServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('dompdf', function ($app) {
            return PDF::loadView('your.view'); // You may customize this as needed
        });

        $this->app->alias('dompdf', \Barryvdh\DomPDF\Facade::class); // Correct import statement
    }
}


