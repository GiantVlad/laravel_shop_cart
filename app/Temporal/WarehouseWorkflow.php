<?php

namespace App\Temporal;

use Temporal\Activity\ActivityOptions;
use Temporal\Workflow;

class WarehouseWorkflow implements WarehouseWorkflowInterface
{
    #[Workflow\WorkflowMethod(name: "warehouse_workflow")]
    public function start(int $orderId)
    {
        $activity = Workflow::newActivityStub(
            WarehouseActivityInterface::class,
            ActivityOptions::new()->withStartToCloseTimeout(10)
        );
    
        return yield $activity->sendOrder($orderId);
    }
}
