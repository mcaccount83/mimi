<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsActiveAndCoordinator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Check if the user is active and is of type 'board'
        if (!$user || $user->is_active != 1 || $user->user_type != 'coordinator') {
            Auth::logout();
            $request->session()->flush();

            return redirect()->to('/login')->with('error', 'You are not permitted to view this page. Please log in with the proper credentials.');
        }

        return $next($request);
    }
}

