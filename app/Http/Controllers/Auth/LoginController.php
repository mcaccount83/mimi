<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    protected function authenticated(Request $request, $user)
    {
        Session::regenerate();

        return redirect()->intended($this->redirectTo);
    }

    /**
     * Where to redirect users after login.
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
        $this->middleware('preventBackHistory');
        $this->middleware('guest')->except('logout');
    }

    /**
     * Default method overriding for check user is active ot not
     */
    protected function credentials(Request $request)
    {
        $credentials = $request->only($this->username(), 'password');
        $credentials = Arr::add($credentials, 'is_active', '1');

        return $credentials;
    }

    public function logout(Request $request)
    {
        Auth::logout(); // logout user
        Session::flush();

        return redirect('/login');
    }
}
