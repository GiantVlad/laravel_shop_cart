<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Events\PaymentCreated;
use App\Payment;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentRepository;
use App\Services\WorkflowService;

class OrderSubscriber
{
    public function __construct(
        private WorkflowService $workflowService,
    ) {
    }
    
    public function subscribe(): array
    {
        return [
            OrderCreated::class => 'handleOrderCreated',
        ];
    }
    
    public function handleOrderCreated(OrderCreated $event): void
    {
        $this->workflowService->sendOrderToWarehouse($event->order->id);
    }
}
