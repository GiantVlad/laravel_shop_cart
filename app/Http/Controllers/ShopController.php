<?php

namespace App\Http\Controllers;

use App\Product;
use App\RelatedProduct;
use App\Catalog;
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

    public function list ()
    {
        $products = Product::all();
        $catalogs = Catalog::where('parent_id', NULL)->get();
        return view('shop', ['products' => $products, 'catalogs' => $catalogs]);
    }

    public function get_product ($id)
    {
        $product = Product::where('id', $id)->first();

        return view('shop.single', ['product' => $product]);
    }

    public function get_child_catalogs ($id)
    {
        $child_catalogs = Catalog::where('parent_id', $id)->get();

        $child_ids = [ (int)$id ];
        $catalog_ids = [];
        do {
            $catalog_ids = array_merge($catalog_ids, $child_ids);
            $child_ids = Catalog::whereIn('parent_id', $child_ids)->pluck('id')->toArray();
        }
        while ($child_ids);

        $products = Product::whereIn('catalog_id', $catalog_ids)->get();

        $parent_id = $id;
        $parent_catalogs_array = [];
        while ($parent_id !== NULL) {
            $parent_catalog = Catalog::find($parent_id);
            $parent_id = $parent_catalog->parent_id;
            $parent_catalogs_array[] = ['id' => $parent_catalog->id, 'name' => $parent_catalog->name];
        }
        $parent_catalogs_array = array_reverse($parent_catalogs_array);

        return view('shop', ['products' => $products, 'catalogs' => $child_catalogs, 'parent_catalogs' => $parent_catalogs_array]);
    }
}