<?php

return [

    'ping_threshold' => 10,

    // Override the default mailer if needed
    // 'default' => env('MAIL_MAILER', 'smtp'),

    // Override global "From" address
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS'),
        'name' => env('MAIL_FROM_NAME'),
    ],

    // Add global BCC
    'bcc' => [
        'address' => env('MAIL_BCC_ADDRESS'),
    ],

    // Only override SMTP settings if you need custom values
    // 'mailers' => [
    //     'smtp' => [
    //         'transport' => 'smtp',
    //         'host' => env('MAIL_HOST', '127.0.0.1'),
    //         'port' => env('MAIL_PORT', 2525),
    //         'encryption' => env('MAIL_ENCRYPTION', 'tls'),
    //         'username' => env('MAIL_USERNAME'),
    //         'password' => env('MAIL_PASSWORD'),
    //     ],
    // ],

];
