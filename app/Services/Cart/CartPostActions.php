<?php

declare(strict_types=1);

namespace App\Services\Cart;

class CartPostActions
{
    public const REMOVE_ROW = 'removeRow';
    public const CHANGE_SHIPPING = 'changeShipping';
    public const ADD_RELATED = 'addRelated';
    
    /**
     * @return array
     */
    public static function getActions(): array
    {
        return [
            self::REMOVE_ROW,
            self::CHANGE_SHIPPING,
        ];
    }
}
