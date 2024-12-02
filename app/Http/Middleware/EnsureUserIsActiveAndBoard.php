<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsActiveAndBoard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        // Check if the user is active and of type 'board'
        if (!$user || $user->is_active != 1 || $user->user_type != 'board') {
            Auth::logout();
            $request->session()->flush();

            return redirect()->to('/login')->with('error', 'You are not permitted to view this page. Please log in with the proper credentials.');
        }

        return $next($request);
    }
}


