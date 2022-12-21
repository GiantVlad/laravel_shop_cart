<?php

namespace App\Services;

use App\Temporal\PaymentWorkflowInterface;
use App\Temporal\WarehouseWorkflowInterface;
use Temporal\Client\WorkflowClientInterface;
use Temporal\Client\WorkflowOptions;

class WorkflowService
{
    public function __construct(private WorkflowClientInterface $workflowClient)
    {
    }
    
    public function checkPayment(int $paymentId): void
    {
        $workflow = $this->workflowClient->newWorkflowStub(
            PaymentWorkflowInterface::class,
            WorkflowOptions::new()
                ->withWorkflowTaskTimeout(60)
        );
    
        $workflow->start($paymentId);
    }
    
    public function sendOrderToWarehouse(int $orderId): void
    {
        $workflow = $this->workflowClient->newWorkflowStub(
            WarehouseWorkflowInterface::class,
            WorkflowOptions::new()
                ->withWorkflowTaskTimeout(60)
        );
        
        $workflow->start($orderId);
    }
}
