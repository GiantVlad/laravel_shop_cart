<?php

namespace App;


class FixRateShippingMethod extends ShippingMethod
{


    public function getRate()
    {
        return 15;
    }

    static function getLabel()
    {
        return 'Fix Rate Shipping';
    }
}
