<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Support\Facades\View;
use App\Catalog;
use App\Property;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $catalog;
    public function __construct (Catalog $catalog)
    {
        $this->catalog = $catalog;
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

        $catalog_ids = $this->catalog->get_catalog_ids_tree((int) $id);

        $products = Product::whereIn('catalog_id', $catalog_ids)->get();

        $parent_id = $id;
        $parent_catalogs_array = [];
        while ($parent_id !== NULL) {
            $parent_catalog = $this->catalog::find($parent_id);
            $parent_id = $parent_catalog->parent_id;
            $parent_catalogs_array[] = ['id' => $parent_catalog->id, 'name' => $parent_catalog->name];
        }
        $parent_catalogs_array = array_reverse($parent_catalogs_array);
        $properties = Property::orderBy('priority')->get();
        $view = View::make('shop', ['products' => $products, 'catalogs' => $child_catalogs, 'parent_catalogs' => $parent_catalogs_array]);
        $view->nest('filter', 'layouts.filter', ['properties' => $properties]);

        return $view;
    }
}