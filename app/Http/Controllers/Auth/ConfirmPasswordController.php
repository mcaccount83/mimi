<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Auth\ConfirmsPasswords;
use Illuminate\Routing\Controllers\HasMiddleware;

class ConfirmPasswordController extends Controller implements HasMiddleware
{
    use ConfirmsPasswords;

    /**
     * Where to redirect users when the intended url fails.
     */
    protected $redirectTo = AppServiceProvider::HOME;

    public static function middleware(): array
    {
        return [
            'auth',
        ];
    }
}
