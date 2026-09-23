<?php

declare(strict_types=1);

namespace App\Services\Payment;

/**
 * Cash on pickup.
 *
 * There is nothing to charge online: the customer pays when the order is picked
 * up. No checkout URL is returned, so CheckoutController sends the buyer straight
 * to their orders and the payment stays in the "created" state.
 */
class CashPayment implements PaymentMethodInterface
{
    public function pay(array $paymentRequestData): PaymentResponse
    {
        return new PaymentResponse(
            [
                'method' => 'cash',
                'order_id' => $paymentRequestData['order_id'] ?? null,
                'status' => 'pending_on_pickup',
            ],
            (string)($paymentRequestData['order_id'] ?? ''),
        );
    }
}
