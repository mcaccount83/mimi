<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
//use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ResetPasswordController extends Controller
{

  //    use ResetsPasswords;

    /**
     * Display the password reset view.
     */
    public function showResetForm(string $token): View
    {
    $resetRecord = DB::table('password_reset_tokens')->where('token', $token)->first();

    // Pass both token and email to the view
    return view('auth.passwords.reset', [
        'token' => $token,
      //  'email' => $resetRecord->email,
    ]);
}

    /**
     * Reset the given user's password.
     */
    protected function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
    ? redirect()->route('login')->with('status', 'Your password was successfully reset!')
    : back()->withErrors(['email' => [__($status)]])->withInput($request->only('email'));

    }

}
