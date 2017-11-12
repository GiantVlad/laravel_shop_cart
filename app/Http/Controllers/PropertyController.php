<?php

namespace App\Http\Controllers;

use App\Catalog;
use Illuminate\Http\Request;
use App\Property;
use App\Product;

class PropertyController extends Controller
{
    private $catalog;

    public function __construct (Catalog $catalog)
    {
        $this->catalog = $catalog;
        $this->middleware('auth');
    }

    public function filter (Request $request)
    {
        //ToDO check is array with int type members
        $property_ids = $request->properties;
        $category_id = $request->category;
        $category_ids = $this->catalog->get_catalog_ids_tree($category_id);

        if (count($property_ids) < 1) {
            $products = Product::whereIn('catalog_id', $category_ids)->get();
        } else {
            $products = Product::whereIn('catalog_id', $category_ids)->whereHas('properties', function ($query) use ($property_ids) {
                return $query->whereIn('properties.id', $property_ids);
            })->get();
        }
        $html = view('shop', compact('products'))->render();

        return response()->json(compact('html'));
    }
}
