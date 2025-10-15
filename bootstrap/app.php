<?php

use App\Http\Middleware\CoordinatorListAccessCMiddleware;
use App\Http\Middleware\CoordinatorListAccessPMiddleware;
use App\Http\Middleware\CoordinatorListAccessTMiddleware;
use App\Http\Middleware\HandlePageExpired;
use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders()
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
            'coordinatorlistC.access' => CoordinatorListAccessCMiddleware::class,
            'coordinatorlistT.access' => CoordinatorListAccessTMiddleware::class,
            'coordinatorlistP.access' => CoordinatorListAccessPMiddleware::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->reportable(function (Throwable $e) {
            $context = [
                'url' => request()->fullUrl(),
                'method' => request()->method(),
                'user' => Auth::id() ?? 'guest',
                'input' => collect(request()->all())->except(['password'])->toArray(),
                'referrer' => request()->headers->get('referer'),
                'trace' => $e->getTraceAsString(),
            ];

            Log::error($e->getMessage(), $context);
        });
    })->create();

// use App\Http\Middleware\CoordinatorListAccessCMiddleware;
// use App\Http\Middleware\CoordinatorListAccessPMiddleware;
// use App\Http\Middleware\CoordinatorListAccessTMiddleware;
// use App\Http\Middleware\HandlePageExpired;
// use App\Providers\AppServiceProvider;
// use Illuminate\Foundation\Application;
// use Illuminate\Foundation\Configuration\Exceptions;
// use Illuminate\Foundation\Configuration\Middleware;

// return Application::configure(basePath: dirname(__DIR__))
//     ->withProviders([
//         \Barryvdh\DomPDF\ServiceProvider::class,
//     ])
//     ->withRouting(
//         web: __DIR__.'/../routes/web.php',
//         commands: __DIR__.'/../routes/console.php',
//         health: '/up',
//     )
//     ->withMiddleware(function (Middleware $middleware) {
//         $middleware->redirectGuestsTo(fn () => route('login'));
//         $middleware->redirectUsersTo(AppServiceProvider::HOME);
//         $middleware->append(HandlePageExpired::class);
//         $middleware->throttleApi();
//         $middleware->alias([
//             'coordinatorlistC.access' => CoordinatorListAccessCMiddleware::class,
//             'coordinatorlistT.access' => CoordinatorListAccessTMiddleware::class,
//             'coordinatorlistP.access' => CoordinatorListAccessPMiddleware::class,
//             'admin' => \App\Http\Middleware\AdminMiddleware::class,

//         ]);

//     })
//     ->withExceptions(function (Exceptions $exceptions) {
//         //
//     })->create();
