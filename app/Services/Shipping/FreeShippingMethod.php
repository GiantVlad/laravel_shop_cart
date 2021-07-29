<?php

namespace App\Services\Shipping;

class FreeShippingMethod implements ShippingMethodImplementation
{
    /**
     * @return int
     */
    static function getRate(): int
    {
        return 0;
    }
    
    /**
     * @return string
     */
    static function getLabel(): string
    {
        return 'Free Shipping';
    }
    
    /**
     * @return string
     */
    static function getDeliveryTime(): string
    {
        return '1-2 weeks';
    }
}
