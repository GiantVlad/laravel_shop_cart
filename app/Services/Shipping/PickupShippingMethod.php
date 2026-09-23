<?php

declare(strict_types=1);

namespace App\Services\Shipping;

/**
 * Pickup from the store: the customer collects the order themselves, so it is
 * free and every payment method is accepted (including cash).
 */
class PickupShippingMethod implements ShippingMethodImplementation
{
    /**
     * @return int
     */
    public static function getRate(): int
    {
        return 0;
    }

    /**
     * @return string
     */
    public static function getLabel(): string
    {
        return 'Pickup from the store';
    }

    /**
     * @return string
     */
    public static function getDeliveryTime(): string
    {
        return 'Ready in 1 day';
    }
}
