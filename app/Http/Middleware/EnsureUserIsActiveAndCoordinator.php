<?php

namespace App\Http\Middleware;

use App\Enums\AdminStatusEnum;
use App\Enums\UserStatusEnum;
use App\Enums\UserTypeEnum;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActiveAndCoordinator
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Check if the user is active AND (is a coordinator OR is an admin)
        if (! $user ||
            $user->is_active !=  UserStatusEnum::ACTIVE ||
            ! ($user->type_id == UserTypeEnum::COORD || $user->is_admin == AdminStatusEnum::ADMIN)) {
            Auth::logout();
            $request->session()->flush();

            return redirect()->to('/login')->with('error', 'You are not permitted to view this page. Please log in with the proper credentials.');
        }

        return $next($request);
    }
}
