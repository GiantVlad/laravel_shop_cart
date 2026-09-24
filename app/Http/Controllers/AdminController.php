<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    /**
     * @return View
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard');
    }
}
