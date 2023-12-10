<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationController extends Controller
{
    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Show the email verification notice.
     */
    public function show(): View
    {
        // Your logic to display the email verification notice goes here
        return view('auth.verify');
    }

    /**
     * Resend the email verification notification.
     */
    public function resend(Request $request)
    {
        // Your logic to resend the email verification notification goes here

        return $request->user()->sendEmailVerificationNotification()
            ?: back()->with('resent', true);
    }
}
