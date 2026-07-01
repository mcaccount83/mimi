<?php

use Illuminate\Support\Facades\Facade;

return [

    'timezone' => env('APP_TIMEZONE', 'America/New_York'),

    'aliases' => Facade::defaultAliases()->merge([
        'PDF' => Barryvdh\DomPDF\Facade\Pdf::class,
    ])->toArray(),

];
