<?php

namespace App\Providers;

use App\Policies\Forum\ForumConditions;
use App\Services\PositionConditionsService;
use Illuminate\Support\ServiceProvider;

class ForumServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ForumConditions::class, function ($app) {
            // Resolve the PositionConditionsService from the container
            // and pass it to ForumConditions
            return new ForumConditions(
                $app->make(PositionConditionsService::class)
            );
        });
    }
}
