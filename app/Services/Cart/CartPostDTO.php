<?php

declare(strict_types=1);

namespace App\Services\Cart;

class CartPostDTO
{
    private int $countItems;
    private int $total;
    
    public function __construct(int $countItems, int $total)
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
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }
}
