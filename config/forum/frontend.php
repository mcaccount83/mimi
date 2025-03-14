<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable/disable feature
    |--------------------------------------------------------------------------
    |
    | Whether or not to enable the frontend feature.
    |
    */

    'enable' => true,

    /*
    |--------------------------------------------------------------------------
    | Preset
    |--------------------------------------------------------------------------
    |
    | The frontend preset to use. Must be installed with the
    | forum:preset-install command.
    |
    */

    'preset' => 'blade-bootstrap',
    // 'preset' => 'livewire-tailwind',

    /*
    |--------------------------------------------------------------------------
    | Router
    |--------------------------------------------------------------------------
    |
    | Router config for the frontend routes.
    |
    */

    'router' => [
        'prefix' => '/forum',
        'as' => 'forum.',
        'middleware' => ['web'],
        'auth_middleware' => ['auth'],
        'publicannouncementslink' => '/c/1-public-announcements',
        'coordinatorlistLink' => '/c/2-coordinatorlist',
        'boardlistLink' => '/c/3-boardlist',
        'boardlistYear' => '2024-25',
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Prefixes
    |--------------------------------------------------------------------------
    |
    | Prefixes to use for each model in frontend routes.
    |
    */

    'route_prefixes' => [
        'category' => 'c',
        'thread' => 't',
        'post' => 'p',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Category Color
    |--------------------------------------------------------------------------
    |
    | The default color to use when creating new categories.
    |
    */

    'default_category_color' => '#007bff',

    /*
    |--------------------------------------------------------------------------
    | Utility Class
    |--------------------------------------------------------------------------
    |
    | Here we specify the class to use for various frontend utility methods.
    | This is automatically aliased to 'Forum' for ease of use in views.
    |
    */

    'utility_class' => TeamTeaTime\Forum\Support\Frontend\Forum::class,

];
