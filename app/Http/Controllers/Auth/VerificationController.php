<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Routing\Attributes\Controllers\Middleware;

#[Middleware('auth')]
#[Middleware('signed', only: ['verify'])]
#[Middleware('throttle:6,1', only: ['verify', 'resend'])]
class VerificationController extends Controller
{
    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     */
    protected $redirectTo = AppServiceProvider::HOME;

}
