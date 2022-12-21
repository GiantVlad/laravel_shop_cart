<?php

namespace App\Temporal;

use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
interface WarehouseWorkflowInterface
{
    #[WorkflowMethod(name: "warehouse_workflow")]
    public function start(int $id);
}
