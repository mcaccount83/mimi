<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActiveAndBoard
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Check if the user is active and of type 'board'
        if (! $user || $user->is_active != 1 || $user->user_type != 'board') {
            Auth::logout();
            $request->session()->flush();

            return redirect()->to('/login')->with('error', 'You are not permitted to view this page. Please log in with the proper credentials.');
        }

        return $next($request);
    }
}
