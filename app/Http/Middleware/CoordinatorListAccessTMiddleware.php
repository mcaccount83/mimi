<?php

namespace App\Http\Middleware;

use App\Policies\Forum\ForumConditions;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use TeamTeaTime\Forum\Models\Thread;

class CoordinatorListAccessTMiddleware
{
    protected $forumConditions;

    public function __construct(ForumConditions $forumConditions)
    {
        $this->forumConditions = $forumConditions;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user(); // Same as in your other middleware

        // Ensure user is authenticated
        if (! $user) {
            Auth::logout();
            $request->session()->flush();

            return redirect()->to('/login')->with('error', 'You must be logged in to access this page.');
        }

        // Get the category from the thead route
        $thread = Thread::find($request->route('thread_id'));
        $category = $thread->category ?? abort(404, 'Category not found');

        // Ensure thread exists
        if (! $thread) {
            return abort(404, 'Thread not found');
        }

        // Check access using the ThreadPolicy
        if (! $this->forumConditions->canAccessCoordinatorList($user, $category)) {
            Auth::logout();
            $request->session()->flush();

            return redirect()->to('/login')->with('error', 'You do not have permission to access this category.');
        }

        return $next($request);
    }
}
