<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class Handler extends ExceptionHandler
{
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            $context = [
                'url' => request()->fullUrl(),
                'method' => request()->method(),
                'user' => Auth::id() ?? 'guest',
                'input' => collect(request()->all())->except(['password'])->toArray(),
                'referrer' => request()->headers->get('referer'),
                'trace' => $e->getTraceAsString()
            ];

            Log::error($e->getMessage(), $context);
        });
    }
}
