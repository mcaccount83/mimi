<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Show the email verification notice.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        // Your logic to display the email verification notice goes here
        return view('auth.verify');
    }

    /**
     * Resend the email verification notification.
     *
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        // Your logic to resend the email verification notification goes here

        return $request->user()->sendEmailVerificationNotification()
            ?: back()->with('resent', true);
    }
}
