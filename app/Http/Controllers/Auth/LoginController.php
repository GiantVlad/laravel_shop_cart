<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;

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

    public function logout (Request $request) {
        if (session()->has('cartProducts')) {
            $user = Auth::user();
            if (empty($user->force_logout)) {
                $cartProductsString = serialize(session()->get('cartProducts'));
                $user->cart = $cartProductsString;
                $user->save();
            }
        }

        Auth::guard('web')->logout();

        return redirect($this->redirectTo);
    }

    protected function authenticated(Request $request, $user)
    {

    }
}
