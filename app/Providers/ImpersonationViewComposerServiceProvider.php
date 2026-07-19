<?php

namespace App\Providers;

use App\Services\ImpersonationService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ImpersonationViewComposerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $view->with('isImpersonating', app(ImpersonationService::class)->isImpersonating());
        });
    }
}
