<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ConfirmsPasswords;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConfirmPasswordController extends Controller
{
    use ConfirmsPasswords;

    /**
     * Show the confirmation form.
     */
    public function showConfirmForm(): View
    {
        return view('auth.passwords.reset');
    }

    /**
     * Confirm the user's password.
     */
    protected function confirm(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => 'required|password',
        ]);

        return redirect()->intended(route('dashboard'));
    }
}
