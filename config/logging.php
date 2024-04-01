<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Processor\PsrLogMessageProcessor;

return [

    'channels' => [
        'email' => [
            'driver' => 'single',
            'path' => storage_path('logs/email.log'),
            'level' => env('EMAIL_LOG_LEVEL', 'info'),
        ],
    ],

];
