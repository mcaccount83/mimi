<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SubmitForgetPasswordFormForgotPasswordRequest;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * Display the forgot password view.
     */
    public function submitForgetPasswordForm(SubmitForgetPasswordFormForgotPasswordRequest $request): RedirectResponse
    {

        $token = Str::random(64);

        // Insert both email and token into the password_reset_tokens table
        DB::table('password_reset_tokens')->insert([
            'email' => $request->input('email'),
            'token' => $token,
            'created_at' => now(),
        ]);

        // Pass both email and token to the email view
        Mail::send('email.forgetPassword', ['token' => $token, 'email' => $request->input('email')], function ($message) use ($request) {
            $message->to($request->input('email'));
            $message->subject('Reset Password');
        });

        return redirect()->back()->with('message', 'We have e-mailed your password reset link!');
    }
}
