<?php

return [

    'ping_threshold' => 10,

    // Override global "From" address
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS'),
        'name' => env('MAIL_FROM_NAME'),
    ],

    // Add global BCC
    'bcc' => [
        'address' => env('MAIL_BCC_ADDRESS'),
    ],

];
