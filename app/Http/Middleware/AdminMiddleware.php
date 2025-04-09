<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->is_admin != 1) {
            return redirect('/login')->with('error', 'You need admin privileges to access this page.');
        }

        return $next($request);
    }
}
