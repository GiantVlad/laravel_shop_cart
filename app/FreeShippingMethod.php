<?php

namespace App;


class FreeShippingMethod extends ShippingMethod
{


    public function getRate()
    {
        return 0;
    }

    static function getLabel()
    {
        return 'Free Shipping';
    }
}
