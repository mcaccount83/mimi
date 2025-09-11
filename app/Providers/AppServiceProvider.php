<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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

        // Register mail markdown views namespace
        $this->loadViewsFrom(resource_path('views/vendor/mail/html'), 'mail');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
