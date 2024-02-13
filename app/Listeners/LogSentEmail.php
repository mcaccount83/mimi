<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogSentEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    protected $listen = [
        'App\Events\EmailSent' => [
            'App\Listeners\LogSentEmail',
        ],
    ];


    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        //
    }
}
