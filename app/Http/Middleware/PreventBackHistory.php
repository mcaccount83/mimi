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

        // Apply headers only if not the specific route
        if (! $request->is('your-specific-route-here')) {
            $headers = [
                'Cache-Control' => 'nocache, no-store, max-age=0, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => 'Fri, 01 Jan 1990 00:00:00 GMT',
            ];

            foreach ($headers as $key => $value) {
                $response->headers->set($key, $value);
            }
        }

        return $response;
    }

    // public function handle(Request $request, Closure $next): Response
    // {
    //     $response = $next($request);
    //     /*return $response->header('Cache-Control','no-cache, no-store, max-age=0, must-revalidate')
    //         ->header('Pragma','no-cache')
    //         ->header('Expires','Sun, 02 Jan 1990 00:00:00 GMT');*/

    //     $headers = [
    //         'Cache-Control' => 'nocache, no-store, max-age=0, must-revalidate',
    //         'Pragma', 'no-cache',
    //         'Expires', 'Fri, 01 Jan 1990 00:00:00 GMT',
    //     ];

    //     foreach ($headers as $key => $value) {
    //         $response->headers->set($key, $value);
    //     }

    //     return $response;
    // }
}
