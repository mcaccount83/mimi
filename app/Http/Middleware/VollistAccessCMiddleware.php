<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use TeamTeaTime\Forum\Models\Category;
use App\Policies\Forum\CategoryPolicy;

class VollistAccessCMiddleware
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

        // Get the category from the route
        $category = Category::find($request->route('category_id'));

        // Ensure category exists
        if (!$category) {
            return abort(404, 'Category not found');
        }

        // Check access using the CategoryPolicy
        if (!(new CategoryPolicy)->canAccessVollist($user, $category)) {
            Auth::logout();
            $request->session()->flush();
            return redirect()->to('/login')->with('error', 'You do not have permission to access this category.');
        }

        return $next($request);
    }
}

