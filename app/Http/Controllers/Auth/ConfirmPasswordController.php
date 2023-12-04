<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\ConfirmsPasswords;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Support\Carbon;

class ConfirmPasswordController extends Controller
{
    use ConfirmsPasswords;

    /**
     * Show the confirmation form.
     *
     * @return \Illuminate\View\View
     */
    public function showConfirmForm(): View
    {
        return view('auth.passwords.reset');
    }

    /**
     * Confirm the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function confirm(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'password' => 'required|password',
        ]);

        // Your logic to confirm the password goes here

        return redirect()->intended(route('dashboard'));
    }
}
