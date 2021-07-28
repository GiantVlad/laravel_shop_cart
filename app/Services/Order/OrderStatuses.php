<?php

declare(strict_types=1);

namespace App\Services\Order;

class OrderStatuses
{
    public const PAYMENT_PENDING = 'pending payment';
    public const PROCESS = 'process';
    public const COMPLETED = 'completed';
    public const DELETED = 'deleted';
    
    /**
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            self::PAYMENT_PENDING,
            self::PROCESS,
            self::COMPLETED,
            self::DELETED,
        ];
    }
}
