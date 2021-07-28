<?php

declare(strict_types=1);

namespace App\Services\Order;

class OrderActions
{
    public const REPEAT = 'repeat';
    public const UNDO = 'undo';
    public const RE_PAYMENT = 're_payment';
    
    /**
     * @return array
     */
    public static function getActions(): array
    {
        return [
            self::REPEAT,
            self::UNDO,
            self::RE_PAYMENT,
        ];
    }
}
