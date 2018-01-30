<?php

namespace App\Library\Services\Ipsp\Resource;

use App\Library\Services\Ipsp\Resource;
/**
 * Class PaymentResult
 */
class Capture extends Resource{
    protected $path   = '/capture/order_id';
    protected $fields = array(
        'merchant_id'=>array(
            'type'    => 'string',
            'required'=>TRUE
        ),
        'order_id'=>array(
            'type'    => 'string',
            'required'=>TRUE
        ),
        'currency' => array(
            'type' => 'string',
            'required'=>TRUE
        ),
        'amount' => array(
            'type' => 'integer',
            'required'=>TRUE
        ),
        'signature' => array(
            'type' => 'string',
            'required'=>TRUE
        )
    );
}