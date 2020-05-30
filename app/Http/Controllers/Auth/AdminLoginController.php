<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function __construct ()
    {
        $this->middleware('guest:admin')->except('adminLogout');
    }

    public function showLoginForm ()
    {
        return view('auth.admin-login');
    }

    public function login (Request $request)
    {
        $this->validate($request, [
            'email' => 'required | email',
            'password' =>'min:6 | required'
        ]);

        $credentials = ['email' => $request->email, 'password' => $request->password];
        $remember = $request->remember;

        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            return redirect()->intended(route('admin.dashboard'));
        }
        return redirect()->back()->withInput($request->only('email, remember'));
    }

    public function adminLogout ()
    {
        Auth::guard('admin')->logout();
        return redirect(route('admin.login'));
    }
}
