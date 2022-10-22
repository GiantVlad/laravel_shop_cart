<?php

namespace App\Services;

use App\Temporal\PaymentWorkflowInterface;
use Temporal\Client\WorkflowClientInterface;
use Temporal\Client\WorkflowOptions;

class WorkflowService
{
    public function __construct(private WorkflowClientInterface $workflowClient)
    {
    }
    
    public function checkPayment(int $paymentId): void
    {
        $demo = $this->workflowClient->newWorkflowStub(
            PaymentWorkflowInterface::class,
            WorkflowOptions::new()
                ->withWorkflowTaskTimeout(60)
        );
    
        $demo->start($paymentId);
    }
}
