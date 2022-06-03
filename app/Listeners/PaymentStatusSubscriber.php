<?php

namespace App\Listeners;

use App\Events\PaymentCreated;
use App\Payment;
use App\Repositories\PaymentRepository;
use App\Services\WorkflowService;

class PaymentStatusSubscriber
{
    public function __construct(
        private PaymentRepository $paymentRepository,
        private WorkflowService $workflowService,
    ) {
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
        $this->checkPayment($event->payment->getPaymentId());
    }
    
    private function checkPayment(int $getPaymentId): void
    {
        $this->workflowService->checkPayment($getPaymentId);
    }
}
