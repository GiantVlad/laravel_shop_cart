<?php

namespace App\Services\Payment;

use App\Exceptions\FondyPaymentException;
use App\Library\Services\IpspPaymentService;

class FondyPayment implements PaymentMethodInterface
{
    public const STATUS_FAILURE = 'failure';
    public const STATUS_SUCCESS = 'success';
    
    public function __construct(private IpspPaymentService $ipspPaymentService)
    {
    }
    
    /**
     * @throws FondyPaymentException
     */
    public function pay(array $paymentRequestData): PaymentResponse
    {
        $paymentRequestData['amount'] = (int) ($paymentRequestData['amount'] * 100);
        unset($paymentRequestData['paymentId']);
        $response = $this->ipspPaymentService->pay($paymentRequestData);
        
        $data = $response->getData();
        
        if (($data['response_status'] ?? '') === self::STATUS_SUCCESS) {
            return new PaymentResponse($data, $data['payment_id'], $data['checkout_url']);
        }
        
        throw new FondyPaymentException($data['error_message'] ?? "Sorry, Fondy doesn't work.");
    }
}
