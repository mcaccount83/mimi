<?php

return [
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'strict' => false,
            'modes' => [
                'ONLY_FULL_GROUP_BY',
                'STRICT_TRANS_TABLES',
                'ERROR_FOR_DIVISION_BY_ZERO',
                'NO_ENGINE_SUBSTITUTION',
            ],
        ],
    ],

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => false, // disable to preserve original behavior for existing applications
    ],

];
