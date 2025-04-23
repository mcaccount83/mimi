<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Services\PositionConditionsService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     */
    public const HOME = '/home';

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register the PositionConditionsService as a singleton
        $this->app->singleton(PositionConditionsService::class, function ($app) {
            return new PositionConditionsService();
        });
    }
}
