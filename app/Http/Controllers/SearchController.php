<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Product;

class SearchController extends Controller
{
    /**
     * @param Request $request
     * @return string|JsonResponse
     */
    public function search(Request $request): string|JsonResponse
    {
        $keyword = $request->keyword;

        if ($keyword!='') {
            $products = Product::where("name", "LIKE","%$keyword%")
                    ->orWhere("description", "LIKE", "%$keyword%")
                    ->get();
            $html = view('shop.search-product', compact('products'))->render();

            return response()->json(compact('html'));

        }

        return 'false';
    }
}
