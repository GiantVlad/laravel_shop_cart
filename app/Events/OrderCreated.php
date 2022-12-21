<?php

namespace App\Events;

use App\Order;

class OrderCreated
{
    public Order $order;
    
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
