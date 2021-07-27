<?php

declare(strict_types=1);

namespace App\Services\Order;

class OrderStatuses
{
    public const PAYMENT_PENDING = 'pending payment';
    
    /**
     * @return string
     */
    public function getStatuses(): string
    {
        return self::PAYMENT_PENDING;
    }
}
