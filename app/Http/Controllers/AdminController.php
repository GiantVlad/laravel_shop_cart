<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function __construct ()
    {
        $this->middleware('auth:admin');
    }
    
    /**
     * @return View
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard');
    }
}
