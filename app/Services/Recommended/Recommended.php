<?php

declare(strict_types=1);

namespace App\Services\Recommended;

use App\RelatedProduct;

class Recommended
{
    public const RATE_IMPACT_AFTER_REMOVAL_FROM_CART = -3;
    
    public function __construct(
        private RelatedProduct $relatedProduct
    ) {}
    
    /**
     * @param int $relatedProductId
     * @param int $value
     */
    public function incrementRate(int $relatedProductId, int $value): void
    {
        $this->relatedProduct->where('id', $relatedProductId)->increment('points', $value);
    }
}
