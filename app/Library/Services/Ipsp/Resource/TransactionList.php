<?php

namespace App\Library\Services\Ipsp\Resource;

use App\Library\Services\Ipsp\Resource;
/**
 * Class Transaction_List
 */
class TransactionList extends Resource
{

    protected $path = '/transaction_list';
    protected $fields = array(
        'order_id' => array(
            'type' => 'string',
            'required' => TRUE
        ),
        'merchant_id' => array(
            'type' => 'int',
            'required' => TRUE
        ),
        'signature' => array(
            'type' => 'string',
            'required' => TRUE
        )
    );
}