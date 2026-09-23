<?php

namespace App\Repositories;

use App\DTO\FilterNumberDTO;
use App\DTO\FilterSelectorDTO;
use App\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ProductRepository
{
    public function __construct(
        private Product $mProduct
    ) {}

    /**
     * Shared filter query builder: category subtree + property filters.
     *
     * @param Collection $filters
     * @param int[]|null $category_ids
     * @return Builder
     */
    public function buildFilteredQuery(Collection $filters, ?array $category_ids = []): Builder
    {
        $query = $this->mProduct->newQuery()
            ->when(!empty($category_ids), function ($query) use ($category_ids) {
                $query->whereIn('catalog_id', $category_ids);
            });
        foreach ($filters as $filterDto) {
            $query->whereHas('properties', function ($q) use ($filterDto) {
                $q->when($filterDto instanceof FilterNumberDTO, function (Builder $nQuery) use ($filterDto) {
                    $nQuery->where('property_values.property_id', $filterDto->getId());
                    if ($filterDto->getMinValue()) {
                        $nQuery->whereRaw('CAST(property_values.value as DECIMAL) >= ?')
                            ->addBinding($filterDto->getMinValue());
                    }
                    if ($filterDto->getMaxValue()) {
                        $nQuery->whereRaw('CAST(property_values.value as DECIMAL) <= ?')
                            ->addBinding($filterDto->getMaxValue());
                    }
                })
                ->when($filterDto instanceof FilterSelectorDTO, function ($sQuery) use ($filterDto) {
                    if ($filterDto->getValues()) {
                        $sQuery->whereIn('property_value_id', $filterDto->getValues());
                    }
                });
            });
        }

        return $query;
    }

    /**
     * @param int[]|null $category_ids
     * @param Collection $filters
     * @return EloquentCollection
     */
    public function getFilteredProducts(Collection $filters, ?array $category_ids = []): EloquentCollection
    {
        return $this->buildFilteredQuery($filters, $category_ids)
            ->limit($this->mProduct::LIST_LIMIT)
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Same filters, but paginated so the shop page keeps its paginator.
     *
     * @param int[]|null $category_ids
     * @param Collection $filters
     * @return LengthAwarePaginator
     */
    public function paginateFilteredProducts(
        Collection $filters,
        ?array $category_ids = [],
        int $perPage = Product::LIST_LIMIT
    ): LengthAwarePaginator {
        return $this->buildFilteredQuery($filters, $category_ids)
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage);
    }
}
