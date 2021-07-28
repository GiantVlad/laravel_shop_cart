<?php

declare(strict_types=1);

namespace App\Services\Order;

class OrderActions
{
    public const REPEAT = 'repeat';
    public const UNDO = 'undo';
    
    /**
     * @return array
     */
    public static function getActions(): array
    {
        return [
            self::REPEAT,
            self::UNDO,
        ];
    }
}
