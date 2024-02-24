<?php

namespace App\Listeners;

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
