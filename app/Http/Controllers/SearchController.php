<?php

namespace App\Http\Controllers;

use App\Catalog;
use App\Http\Resources\ProductCollection;
use App\Repositories\ProductRepository;
use App\Services\Filter\ProductFilterParser;
use Illuminate\Http\Request;
use App\Product;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{
    public function search(Request $request): Response
    {
        $keyword = $request->input('keyword');
        $products = Product::where("name", "LIKE", "%$keyword%")
            ->orWhere("description", "LIKE", "%$keyword%")
            ->orderBy('updated_at', 'desc')
            ->limit(Product::LIST_LIMIT)
            ->get();
        
        return Inertia::render('ProductList', [
            'products' => $products,
        ]);
    }
    
    public function filter(
        Request $request,
        Catalog $catalog,
        ProductRepository $productRepository,
        ProductFilterParser $filterParser
    ): ProductCollection
    {
        $filters = $request->all();
        
        $catalog_ids = isset($filters['category_id']) ? $catalog->getCatalogIdsTree((int)$filters['category_id']) : [];
        unset($filters['category_id']);
        
        $filters = $filterParser->parse($filters);
        $products = $productRepository->getFilteredProducts($filters, $catalog_ids);
        
        return new ProductCollection($products);
    }
}
