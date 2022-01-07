<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\View\View as ViewInstance;
use \Illuminate\Contracts\View\Factory as ViewFactoryContract;
use App\Catalog;
use App\Property;

class ShopController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private Catalog $catalog;
    private ViewFactoryContract $view;
    
    public function __construct (Catalog $catalog, ViewFactoryContract $view)
    {
        $this->catalog = $catalog;
        $this->view = $view;
    }
    
    /**
     * @return View
     */
    public function list(Request $request): View
    {
        $keyword = $request->get('keyword');
    
        /** @var ViewInstance $view */
        $view = $this->view->make('shop', ['keyword' => $keyword]);
        $view->nest('links', 'layouts.links');
        
        return $view;
    }
    
    /**
     * @param int $id
     * @return View
     */
    public function getProduct(int $id): View
    {
        $product = Product::with('properties')->findOrFail($id);
        
        return view('shop.single', ['product' => $product]);
    }
    
    /**
     * @param int $id
     * @return View
     */
    public function getChildCatalogs(int $id): View
    {
        $child_catalogs = Catalog::where('parent_id', $id)->get();

        $catalog_ids = $this->catalog->getCatalogIdsTree($id);

        $products = Product::whereIn('catalog_id', $catalog_ids)->with('catalogs')->with('properties')->get();
        $parent_id = $id;
        $parent_catalogs_array = [];
        while ($parent_id !== null) {
            $parent_catalog = $this->catalog::find($parent_id);
            $parent_id = null;
            if ($parent_catalog) {
                $parent_id = $parent_catalog->parent_id;
                $parent_catalogs_array[] = ['id' => $parent_catalog->id, 'name' => $parent_catalog->name];
            }
        }
        $parent_catalogs_array = array_reverse($parent_catalogs_array);
        $properties = Property::with('propertyValues')->orderBy('priority')->get();

        /** @var ViewInstance $view */
        $view = $this->view->make(
            'shop',
            ['products' => [], 'catalogs' => $child_catalogs, 'parent_catalogs' => $parent_catalogs_array]
        );
        $view->nest('filter', 'layouts.filter', ['properties' => $properties]);

        return $view;
    }
}
