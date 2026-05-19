<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use App\Enums\UserTypeEnum;
use Closure;
use Illuminate\Http\Request;

class SetViewAsSession
{
    public function handle(Request $request, Closure $next): Response
    {
        $userTypeId = session('userTypeId'); // or however you get it

        if ($userTypeId == UserTypeEnum::COORD) {
            session(['viewing_as' => 'president']);
        } else {
            $request->session()->forget('viewing_as');
        }

        return $next($request);
    }
}
