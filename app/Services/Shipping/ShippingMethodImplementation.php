<?php

namespace App\Services\Shipping;

interface ShippingMethodImplementation
{
    /**
     * @return int
     */
    public static function getRate(): int;

    /**
     * @return string
     */
    public static function getLabel(): string;

    /**
     * @return string
     */
    public static function getDeliveryTime(): string;
}
