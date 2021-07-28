<?php

namespace App\Library\Services;

interface PaymentServiceInterface
{
    public function pay(array $requestData);
}
