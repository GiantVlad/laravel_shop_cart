<?php

namespace Ipsp\Resource;

use Ipsp\Resource;
/**
 * Class PaymentStatus
 */
class Status extends Resource{
    protected $path   = '/status/order_id';
    protected $fields = array(
        'order_id'=>array(
            'type'    => 'string',
            'required'=>TRUE
        ),
        'merchant_id'=>array(
            'type' => 'string',
            'required'=>TRUE
        ),
        'signature' => array(
            'type' => 'string',
            'required'=>TRUE
        ),
        'version' => array(
            'type' => 'string',
            'required'=>FALSE
        )
    );
}