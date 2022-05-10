<?php

declare(strict_types=1);

namespace App\Services\Payment;

use PayPal\Core\PayPalHttpClient;
use PayPal\Core\SandboxEnvironment;
use PayPal\v1\Payments\PaymentCreateRequest;

class PaypalPayment implements PaymentMethodInterface
{
    public function pay(array $paymentRequestData): PaymentResponse
    {
        $cred = config('payments.methods.paypal');
        $environment = new SandboxEnvironment($cred['client_id'], $cred['secret']);
        $client = new PayPalHttpClient($environment);
    
        $body = [
            'intent' => 'sale',
            'transactions' => [
                [
                    'amount' => [
                        'total' => $paymentRequestData['amount'],
                        'currency' => $paymentRequestData['currency'],
                    ]
                ]
            ],
            'redirect_urls' => [
                'cancel_url' => 'https://localhost/orders',
                'return_url' =>  $paymentRequestData['response_url'],
            ],
            'payer' => [
                'payment_method' => 'paypal',
            ],
        ];
    
        $request = new PaymentCreateRequest();
        $request->body = $body;
    
        $response = $client->execute($request);
        // todo check status code $response->statusCode == 201
        /** @var array $links */
        $links = $response->result->links ?? []; /* @phpstan-ignore-line */
        $redirect = array_first($links, fn($item) => $item->rel === 'approval_url');
        
        return new PaymentResponse(
            $response->result,
            $response->result->id ?? '', /* @phpstan-ignore-line */
            $redirect->href
        );
    }
}
