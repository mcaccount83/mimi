<?php

namespace App\Providers;

use App\Events\UserUpdated;
use App\Listeners\CreateLearnDashUsers;
use App\Listeners\SyncUserTypeToLearnDash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            CreateLearnDashUsers::class,
        ],
        UserUpdated::class => [
            SyncUserTypeToLearnDash::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }
}
