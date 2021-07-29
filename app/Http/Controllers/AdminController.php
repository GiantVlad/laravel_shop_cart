<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AdminController extends Controller
{
    public function __construct ()
    {
        $this->middleware('auth:admin');
    }
    
    /**
     * @return View
     */
    public function index(): View
    {
        return view('admin.dashboard');
    }
}
