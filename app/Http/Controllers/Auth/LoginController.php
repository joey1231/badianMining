<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    protected $maxAttempts = 3;
    protected $delayMinutes = 1;
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function attemptLogin(Request $request)
    {

        $identity = $request->get("username");
        $password = $request->get("password");
        $remember = $request->get('remember', false);
        $user = \App\Models\User::where('username', $identity)->first();

        if (!is_null($user) && $user->is_locked) {
            //dd($user);
            $request->session()->flash('status', 'failed');
            $request->session()->flash('message', 'Your account is locked');
            //return redirect()->back()->withErrors(['username' => 'Your account is locked']);
        }
        //
        $attempt = Auth::attempt(['username' => $identity, 'password' => $request->get('password'), 'revoke_access' => false, 'is_locked' => false], $remember);

        if ($attempt) {

            return redirect()->intended('/');
        }
    }
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return Response
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $identity = $request->get('email');

        $remember = $request->get('remember', false);
        // $crentials = [filter_var($identity, FILTER_VALIDATE_EMAIL) ? 'email' : 'username' => $identity, 'password'=> $request->get('password')]
        // if (Auth::attempt($credentials, $remember)) {
        //     // Authentication passed...
        //     return redirect()->intended('/');
        // }

        if (Auth::attempt([filter_var($identity, FILTER_VALIDATE_EMAIL) ? 'email' : 'username' => $identity, 'password' => $request->get('password')], $remember)) {
            return redirect()->intended('/');
        }
    }

    public function username()
    {
        return 'username';
    }
}
