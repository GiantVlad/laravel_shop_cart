<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class TestController extends Controller
{
    public function test()
    {
        return Inertia::render('Home', [
            'user' => [
                'id' => 12,
                'name' => 'my name',
            ],
        ]);
    }
}
