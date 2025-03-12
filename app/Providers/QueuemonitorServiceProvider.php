<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use romanzipp\QueueMonitor\Providers\QueueMonitorProvider;
use Illuminate\Support\Facades\View;

class QueueMonitorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(QueueMonitorProvider::class);

        $this->app->alias('QueueMonitor', \romanzipp\QueueMonitor\Providers\QueueMonitorProvider::class);
    }

    public function boot(): void
    {
        View::share('errors', session()->get('errors') ?: new \Illuminate\Support\ViewErrorBag);

        view()->addNamespace('queue-monitor', resource_path('views/vendor/queue-monitor'));
    }
}
