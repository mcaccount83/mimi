<?php

use Illuminate\Support\Facades\Facade;

return [

    'aliases' => Facade::defaultAliases()->merge([
        'PDF' => Barryvdh\DomPDF\Facade\Pdf::class,
    ])->toArray(),

];
