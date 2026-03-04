<?php

namespace App\Http\Middleware;

use App\Enums\ChapterStatusEnum;
use App\Enums\UserTypeEnum;
use Closure;
use Illuminate\Http\Request;

class SetViewAsSession
{
    public function handle(Request $request, Closure $next)
    {
        $userTypeId = session('userTypeId'); // or however you get it

        if ($userTypeId == UserTypeEnum::COORD) {
            session(['viewing_as' => 'president']);
        } else {
            session()->forget('viewing_as');
        }

        return $next($request);
    }
}
