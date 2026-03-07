<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }
    
    public function index()
    {
        return Inertia::render('Home', [
            'user' => [
                'id' => 12,
                'name' => 'my name',
            ],
        ]);
    }
}
