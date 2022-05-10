<?php

namespace App\Services\Payment;

use App\Library\Services\IpspPaymentService;

class FondyPayment implements PaymentMethodInterface
{
    public function __construct(private IpspPaymentService $ipspPaymentService)
    {
    }
    
    public function pay(array $paymentRequestData): PaymentResponse
    {
        $paymentRequestData['amount'] = (int) ($paymentRequestData['amount'] * 100);
        $response = $this->ipspPaymentService->pay($paymentRequestData);
        
        // todo check status
        $data = $response->getData();
        return new PaymentResponse($data, $data['payment_id'], $data['checkout_url']);
    }
}
