<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Services\Cart\CartPostDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var CartPostDTO $cardPostDto */
        $cardPostDto = $this->resource;
        return [
            'items' => $cardPostDto->getCountItems(),
            'total' => $cardPostDto->getTotal(),
        ];
    }
}
