<?php

namespace App\Http\Controllers;

use App\Catalog;
use App\DTO\FilterNumberDTO;
use App\DTO\FilterSelectorDTO;
use App\Http\Resources\ProductCollection;
use App\Property;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use App\Product;
use Illuminate\Support\Collection;

class SearchController extends Controller
{
    /**
     * @param Request $request
     *
     * @return ProductCollection
     */
    public function search(Request $request): ProductCollection
    {
        $keyword = $request->input('keyword');
        $products = Product::where("name", "LIKE", "%$keyword%")
            ->orWhere("description", "LIKE", "%$keyword%")
            ->orderBy('updated_at', 'desc')
            ->limit(Product::LIST_LIMIT)
            ->get();
        
        return new ProductCollection($products);
    }
    
    /**
     * @param Request $request
     *
     * @return ProductCollection
     */
    public function filter(
        Request $request,
        Catalog $catalog,
        ProductRepository $productRepository
    ): ProductCollection
    {
        $filters = $request->all();
        
        $catalog_ids = isset($filters['category_id']) ? $catalog->getCatalogIdsTree((int)$filters['category_id']) : [];
        unset($filters['category_id']);
        
        $filters = $this->prepareFilters($filters);
        $products = $productRepository->getFilteredProducts($filters, $catalog_ids);
        
        return new ProductCollection($products);
    }
    
    /**
     * @param array $filters
     * @return Collection
     */
    private function prepareFilters(array $filters): Collection
    {
        $properties = new Collection();
        foreach ($filters as $key => $filterValues) {
            if ($filterValues === null) {
                continue;
            }
            $property_id = (int)str_replace('values_', '', $key);
            $property = Property::findOrFail($property_id);
            if ($property instanceof Property) {
                $values = explode(',', $filterValues);
                if (!empty($values)) {
                    if ($property->type === Property::TYPE_SELECTOR) {
                        $values = array_map('intval', $values);
                        $properties->add(new FilterSelectorDTO($values, $property_id));
                    } elseif ($property->type === Property::TYPE_NUMBER) {
                        $properties->add(new FilterNumberDTO((float)$values[0], (float)$values[1], $property_id));
                    }
                }
            }
        }
        
        return $properties;
    }
}
