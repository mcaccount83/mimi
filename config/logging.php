<?php

return [

    'channels' => [
        'email' => [
            'driver' => 'single',
            'path' => storage_path('logs/email.log'),
            'level' => env('EMAIL_LOG_LEVEL', 'info'),
        ],
    ],

];
