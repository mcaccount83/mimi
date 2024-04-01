<?php

namespace App\Http\Controllers\Auth;

use App\Providers\AppServiceProvider;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     */
    protected $redirectTo = AppServiceProvider::HOME;
}
