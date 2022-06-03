<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class StubController extends Controller
{
    public function payment(): JsonResponse
    {
        return response()->json(['status' => 'confirmed']);
    }
}
