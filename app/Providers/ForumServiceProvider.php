<?php

namespace App\Providers;

use App\Policies\Forum\ForumConditions;
use Illuminate\Support\ServiceProvider;

class ForumServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ForumConditions::class, function ($app) {
            return new ForumConditions;
        });
    }
}
