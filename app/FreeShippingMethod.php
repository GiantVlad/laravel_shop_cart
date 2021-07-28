<?php

namespace App;

class FreeShippingMethod extends ShippingMethod
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
