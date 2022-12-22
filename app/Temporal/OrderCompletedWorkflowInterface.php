<?php

namespace App\Temporal;

use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
interface OrderCompletedWorkflowInterface
{
    #[WorkflowMethod(name: "warehouse_workflow")]
    public function start(int $id);
}
