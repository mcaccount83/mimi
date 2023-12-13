<?php

namespace App\Providers;

use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\ServiceProvider;

class DomPDFServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('dompdf', function ($app) {
            return PDF::loadView('your.view'); // You may customize this as needed
        });

        $this->app->alias('dompdf', PDF::class); // Correct import statement
    }
}
