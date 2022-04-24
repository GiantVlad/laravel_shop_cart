<?php

namespace App\Events;

use App\Services\Payment\PaymentResponse;

class PaymentCreated
{
    public PaymentResponse $payment;
    
    public function __construct(PaymentResponse $payment)
    {
        $this->payment = $payment;
    }
}
