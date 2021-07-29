<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:admin')->except('adminLogout');
    }
    
    /**
     * @return View
     */
    public function showLoginForm(): View
    {
        return view('auth.admin-login');
    }
    
    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function login(Request $request): RedirectResponse
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
    
    /**
     * @return RedirectResponse|Redirector
     */
    public function adminLogout(): RedirectResponse|Redirector
    {
        Auth::guard('admin')->logout();
        
        return redirect(route('admin.login'));
    }
}
