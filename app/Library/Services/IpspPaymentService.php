<?php
/**
 * Created by PhpStorm.
 * User: ale
 * Date: 23.01.2018
 * Time: 17:52
 */

namespace App\Library\Services;

use App\Library\Services\Ipsp\Api;



class IpspPaymentService implements PaymentServiceInterface
{
    private $api;
    public function __construct (Api $api)
    {
        $this->api = $api;
    }

    public function pay (array $requestData)
    {
        $requestData['currency'] = constant(get_class ($this->api).'::'.$requestData['currency']);
        return $this->api->call('checkout', $requestData)->getResponse();
    }
}