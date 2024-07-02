<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Closure;
use Illuminate\Http\Request;

class HandlePageExpired
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response->getStatusCode() == 419) {
            return redirect()->to('/login')->with('error', 'Your session has expired, Please log in again');
        }

        return $response;
    }
}
