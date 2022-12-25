<?php

namespace App\Http\Controllers;

use App\DTO\CategoriesDTO;
use App\Http\Resources\CategoriesResource;
use App\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\View\View as ViewInstance;
use \Illuminate\Contracts\View\Factory as ViewFactoryContract;
use App\Catalog;

class ShopController extends Controller
{
    private Catalog $catalog;
    private ViewFactoryContract $viewFactory;
    
    public function __construct (Catalog $catalog, ViewFactoryContract $viewFactory)
    {
        $this->catalog = $catalog;
        $this->viewFactory = $viewFactory;
    }
    
    public function list(Request $request): View
    {
        $keyword = $request->get('keyword');

        /** @var ViewInstance $view */
        $view = $this->viewFactory->make('shop', ['keyword' => $keyword]);

        return $view;
    }
    
    public function getProduct(int $id): View
    {
        $product = Product::with('properties')->findOrFail($id);
        
        return view('shop.single', ['product' => $product]);
    }
    
    public function getChildCatalogs(int $id): JsonResource
    {
        $childCatalogs = $id === 0 ? $this->catalog->whereNull('parent_id')->get() : $this->catalog->where('parent_id', $id)->get();
        $parentId = $id;
        $parentCatalogsArray = [];
        while ($parentId !== null) {
            $parentCatalog = $this->catalog::find($parentId);
            $parentId = null;
            if ($parentCatalog) {
                $parentId = $parentCatalog->parent_id;
                $parentCatalogsArray[] = ['id' => $parentCatalog->id, 'name' => $parentCatalog->name];
            }
        }
        $parentCatalogsArray = array_reverse($parentCatalogsArray);

        return new CategoriesResource(new CategoriesDTO($childCatalogs, new Collection($parentCatalogsArray)));
    }
}
