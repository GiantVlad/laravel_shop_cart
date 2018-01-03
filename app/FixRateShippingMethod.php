<?php

namespace App;


class FixRateShippingMethod extends ShippingMethod
{


    static function getRate()
    {
        return 15;
    }

    static function getLabel()
    {
        return 'Fix Rate Shipping';
    }

    static function getDeliveryTime ()
    {
        return '5-7 days';
    }
}
