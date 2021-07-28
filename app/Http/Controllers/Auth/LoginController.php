<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
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

    /**
     * Where to redirect users after login.
     */
    protected string $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    
    /**
     * @param Request $request
     * @return RedirectResponse|Redirector
     */
    public function logout(Request $request): RedirectResponse|Redirector
    {
        if (session()->has('cartProducts')) {
            /** @var User $user */
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
    
    /**
     * @param Request $request
     * @param User $user
     */
    protected function authenticated(Request $request, User $user): void
    {

    }
}
