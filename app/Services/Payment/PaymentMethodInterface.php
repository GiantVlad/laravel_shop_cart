<?php

namespace App\Services\Payment;

interface PaymentMethodInterface
{
    public function pay(array $paymentRequestData): PaymentResponse;
}
