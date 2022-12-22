<?php

namespace App\Temporal;

use Temporal\Activity\ActivityOptions;
use Temporal\Promise;
use Temporal\Workflow;

class OrderCompletedWorkflow implements OrderCompletedWorkflowInterface
{
    #[Workflow\WorkflowMethod(name: "warehouse_workflow")]
    public function start(int $orderId)
    {
        $warehouseActivity = Workflow::newActivityStub(
            WarehouseActivityInterface::class,
            ActivityOptions::new()->withStartToCloseTimeout(10)
        );
    
        $erpActivity = Workflow::newActivityStub(
            ErpActivityInterface::class,
            ActivityOptions::new()->withStartToCloseTimeout(10)
        );
    
        $promises = [];
        $promises[] = yield $warehouseActivity->sendOrder($orderId);
        $promises[] = yield $erpActivity->sendOrder($orderId);
        
        return Promise::all($promises);
    }
}
