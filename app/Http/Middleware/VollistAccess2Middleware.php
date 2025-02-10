<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use TeamTeaTime\Forum\Models\Thread;
use App\Policies\Forum\ThreadPolicy;

class VollistAccess2Middleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user(); // Same as in your other middleware

        // Ensure user is authenticated
        if (!$user) {
            Auth::logout();
            $request->session()->flush();
            return redirect()->to('/login')->with('error', 'You must be logged in to access this page.');
        }

        // Get the thread from the route
        $thread = Thread::find($request->route('thread_id')); // Changed from category_id

        // Ensure thread exists
        if (!$thread) {
            return abort(404, 'Thread not found');
        }

        // Log for debugging
        Log::info('Accessing thread', ['user_id' => $user->id, 'thread_id' => $thread->id]);

        // Check access using the ThreadPolicy
        if (!(new ThreadPolicy)->canAccessVollist($user, $thread)) {
            Auth::logout();
            $request->session()->flush();
            return redirect()->to('/login')->with('error', 'You do not have permission to access this thread.');
        }

        return $next($request);
    }
}


