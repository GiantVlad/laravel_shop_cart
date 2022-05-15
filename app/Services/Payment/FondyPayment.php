<?php

namespace App\Services\Payment;

use App\Library\Services\IpspPaymentService;

class FondyPayment implements PaymentMethodInterface
{
    public const STATUS_FAILURE = 'failure';
    public const STATUS_SUCCESS = 'success';
    
    public function __construct(private IpspPaymentService $ipspPaymentService)
    {
    }
    
    public function pay(array $paymentRequestData): PaymentResponse
    {
        $paymentRequestData['amount'] = (int) ($paymentRequestData['amount'] * 100);
        $response = $this->ipspPaymentService->pay($paymentRequestData);
        
        // todo check status
        $data = $response->getData();
        $responseStatus = $data['response_status'] ?? null;
        
        if ($responseStatus && $responseStatus === self::STATUS_SUCCESS) {
            return new PaymentResponse($data, $data['payment_id'], $data['checkout_url']);
        }
        
        throw new \Exception($data['error_message'] ?? "Sorry, Fondy doesn't work");
    }
}
