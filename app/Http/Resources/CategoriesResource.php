<?php

namespace App\Http\Resources;

use App\DTO\CategoriesDTO;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoriesResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var CategoriesDTO $categoriesDto */
        $categoriesDto = $this->resource;
        return [
            'catalogs' => $categoriesDto->getCatalogs(),
            'parentCatalogs' => $categoriesDto->getParentCatalogs(),
        ];
    }
}
