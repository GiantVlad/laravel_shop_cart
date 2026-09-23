<?php

declare(strict_types=1);

namespace App\Services\Filter;

use App\DTO\FilterNumberDTO;
use App\DTO\FilterSelectorDTO;
use App\Property;
use Illuminate\Support\Collection;

/**
 * Turns raw query-string filters of the form
 *   values_<propertyId>=1,2        (selector -> property_value ids)
 *   values_<propertyId>=10,50      (number   -> min,max)
 * into the DTOs consumed by ProductRepository::getFilteredProducts().
 *
 * Any key that is not `values_*` (page, category_id, sort, ...) and any
 * unknown property id is ignored instead of raising a 404.
 */
class ProductFilterParser
{
    /**
     * @param array<string, mixed> $filters
     * @return Collection<int, FilterSelectorDTO|FilterNumberDTO>
     */
    public function parse(array $filters): Collection
    {
        $properties = new Collection();

        foreach ($filters as $key => $filterValues) {
            $key = (string) $key;

            if (!str_starts_with($key, 'values_')) {
                continue;
            }

            if ($filterValues === null || $filterValues === '') {
                continue;
            }

            $property = Property::find((int) str_replace('values_', '', $key));

            if (!$property instanceof Property) {
                continue;
            }

            $values = is_array($filterValues)
                ? $filterValues
                : explode(',', (string) $filterValues);

            if ($property->type === Property::TYPE_SELECTOR) {
                $values = array_map('intval', $values);
                $values = array_values(array_filter($values, static fn (int $id): bool => $id > 0));
                $properties->add(new FilterSelectorDTO($values, $property->id));
            } elseif ($property->type === Property::TYPE_NUMBER) {
                $properties->add(new FilterNumberDTO(
                    (float) ($values[0] ?? 0),
                    (float) ($values[1] ?? 0),
                    $property->id
                ));
            }
        }

        return $properties;
    }
}
