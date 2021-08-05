<?php

declare(strict_types=1);

namespace App\Services\Cart;

class CartPostDTO
{
    private int $countItems;
    private float $total;
    
    public function __construct(int $countItems, float $total)
    {
        $this->countItems = $countItems;
        $this->total = $total;
    }
    
    /**
     * @return int
     */
    public function getCountItems(): int
    {
        return $this->countItems;
    }
    
    /**
     * @return float
     */
    public function getTotal(): float
    {
        return $this->total;
    }
}
