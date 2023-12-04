<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Rap2hpoutre\LaravelLogViewer\LaravelLogViewerServiceProvider as LogViewer;

class LogViewerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(LogViewer::class);

        $this->app->alias('LogViewer', Rap2hpoutre\LaravelLogViewer\LaravelLogViewerServiceProvider::class);
    }
}
