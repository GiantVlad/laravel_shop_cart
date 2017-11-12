<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;

class SearchController extends Controller
{
    public function __construct ()
    {
        $this->middleware('auth');
    }
    public function search(Request $request)
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
