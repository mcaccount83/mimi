<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Policies\Forum\ForumConditions;

class ForumServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ForumConditions::class, function ($app) {
            return new ForumConditions();
        });
    }
}
