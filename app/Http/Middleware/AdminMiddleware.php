<?php

namespace App\Http\Middleware;

use App\Enums\AdminStatusEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->is_admin != AdminStatusEnum::ADMIN) {
            return redirect()->to('/login')->with('error', 'You need admin privileges to access this page.');
        }

        return $next($request);
    }
}
