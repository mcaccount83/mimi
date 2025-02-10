<?php

use App\Http\Middleware\HandlePageExpired;
use App\Http\Middleware\VollistAccessMiddleware;
use App\Http\Middleware\VollistAccess2Middleware;
use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        \Barryvdh\DomPDF\ServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        // api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        // channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(AppServiceProvider::HOME);
        $middleware->append(HandlePageExpired::class);
        $middleware->throttleApi();
        $middleware->alias([
            'vollist.access' => VollistAccessMiddleware::class,
            'vollist2.access' => VollistAccessMiddleware::class,

        ]);


    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
