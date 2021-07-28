<?php

namespace App\Library\Services;

use App\Library\Services\Ipsp\Api;

class IpspPaymentService implements PaymentServiceInterface
{
    private Api $api;
    
    public function __construct(Api $api)
    {
        $this->api = $api;
    }
    
    public function checkStatus(array $requestData)
    {
        $requestData['currency'] = constant(get_class($this->api) . '::' . $requestData['currency']);
        return $this->api->call('status', $requestData)->getResponse();
    }

    public function pay(array $requestData)
    {
        $requestData['currency'] = constant(get_class($this->api) . '::' . $requestData['currency']);
        return $this->api->call('checkout', $requestData)->getResponse();
    }
}
