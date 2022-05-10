<?php

namespace App\Listeners;

use App\Events\PaymentCreated;
use App\Payment;
use App\Repositories\PaymentRepository;

class PaymentStatusSubscriber
{
    public function __construct(private PaymentRepository $paymentRepository)
    {
    }
    
    public function subscribe(): array
    {
        return [
            PaymentCreated::class => 'handlePaymentCreated',
        ];
    }
    
    public function handlePaymentCreated(PaymentCreated $event): void
    {
        $this->paymentRepository->updateById(
            $event->payment->getPaymentId(),
            [
                'external_id' => $event->payment->getExternalId(),
                'status' => Payment::STATUS_CREATED,
            ],
        );
    }
}
