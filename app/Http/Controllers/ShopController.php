<?php

namespace App\Http\Controllers;

use App\Product;
use App\RelatedProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct ()
    {
        $this->middleware('auth');
    }

    public function list () {
        $products = Product::all();
        return view('shop', ['products' => $products]);
    }

    public function get_product ($id) {
        $product = Product::where('id', $id)->first();

        return view('shop.single', ['product' => $product]);
    }
}