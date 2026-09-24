<?php

namespace App\Http\Controllers;

use App\DTO\CategoriesDTO;
use App\Http\Resources\CategoriesResource;
use App\Http\Resources\ProductCollection;
use App\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\View\View as ViewInstance;
use \Illuminate\Contracts\View\Factory as ViewFactoryContract;
use App\Catalog;
use App\Repositories\ProductRepository;
use App\Repositories\PropertyRepository;
use App\Services\Filter\ProductFilterParser;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Inertia\Inertia;
use Inertia\Response;

class ShopController extends Controller
{
    private Catalog $catalog;
    private ViewFactoryContract $viewFactory;
    
    public function __construct (Catalog $catalog, ViewFactoryContract $viewFactory)
    {
        $this->catalog = $catalog;
        $this->viewFactory = $viewFactory;
    }
    
    public function list(
        Request $request,
        ProductRepository $productRepository,
        PropertyRepository $propertyRepository,
        ProductFilterParser $filterParser
    ): Response {
        $query = $request->all();

        $categoryId = isset($query['category_id']) && (int) $query['category_id'] > 0
            ? (int) $query['category_id']
            : null;

        $catalogIds = $categoryId !== null
            ? $this->catalog->getCatalogIdsTree($categoryId)
            : [];

        $products = $productRepository
            ->paginateFilteredProducts($filterParser->parse($query), $catalogIds, Product::LIST_LIMIT)
            ->withQueryString();

        return Inertia::render('ProductList', [
            'products' => $products,
            'categories' => $this->catalogTree(),
            'properties' => $propertyRepository->getFilteredProducts(),
        ]);
    }

    /**
     * Root catalogs with their direct children, ordered for the sidebar.
     *
     * @return EloquentCollection
     */
    private function catalogTree(): EloquentCollection
    {
        return $this->catalog->newQuery()
            ->whereNull('parent_id')
            ->with(['children' => static fn ($query) => $query->orderBy('priority')])
            ->orderBy('priority')
            ->get(['id', 'name', 'parent_id']);
    }
    
    public function getProduct(int $id): Response
    {
        $product = Product::with('properties')->findOrFail($id);
        
        return Inertia::render('Product', [
            'product' => $product,
        ]);
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
