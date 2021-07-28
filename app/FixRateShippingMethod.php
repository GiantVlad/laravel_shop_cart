<?php

namespace App;

class FixRateShippingMethod extends ShippingMethod
{
    
    /**
     * @return int
     */
    static function getRate(): int
    {
        return 15;
    }
    
    /**
     * @return string
     */
    static function getLabel(): string
    {
        return 'Fix Rate Shipping';
    }
    
    /**
     * @return string
     */
    static function getDeliveryTime(): string
    {
        return '5-7 days';
    }
}
