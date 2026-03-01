<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventBackHistory
{
    /**
     * Handle an incoming request.
     */
public function handle(Request $request, Closure $next): Response
{
    $response = $next($request);

    $response->headers->set('Cache-Control', 'no-cache, private');

    return $response;
}
}
