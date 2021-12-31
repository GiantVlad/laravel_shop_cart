<?php

namespace App\Repositories;

use App\DTO\FilterNumberDTO;
use App\DTO\FilterSelectorDTO;
use App\Product;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class ProductRepository
{
    public function __construct(
        private Product $mProduct
    ) {}
    
    /**
     * @param int[]|null $category_ids
     * @param Collection $filters
     * @return EloquentCollection
     */
    public function getFilteredProducts(Collection $filters, ?array $category_ids = []): EloquentCollection
    {
        $query = $this->mProduct->newQuery()
            ->when(!empty($category_ids), function ($query) use ($category_ids) {
                $query->whereIn('catalog_id', $category_ids);
            });
        foreach ($filters as $filterDto) {
            $query->whereHas('properties', function ($q) use ($filterDto) {
                $q->when($filterDto instanceof FilterNumberDTO, function ($nQuery) use ($filterDto) {
                    $nQuery->where('property_values.property_id', $filterDto->getId());
                    if ($filterDto->getMinValue()) {
                        $nQuery->where('property_values.value', '>=', $filterDto->getMinValue());
                    }
                    if ($filterDto->getMaxValue()) {
                        $nQuery->where('property_values.value', '<=', $filterDto->getMaxValue());
                    }
                })
                ->when($filterDto instanceof FilterSelectorDTO, function ($sQuery) use ($filterDto) {
                    if ($filterDto->getValues()) {
                        $sQuery->whereIn('property_value_id', $filterDto->getValues());
                    }
                });
            });
        }
        
        return $query->limit($this->mProduct::LIST_LIMIT)->orderBy('updated_at', 'desc')->get();
    }
}
