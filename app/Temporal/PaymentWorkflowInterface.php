<?php

namespace App\Temporal;

use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
interface PaymentWorkflowInterface
{
    #[WorkflowMethod(name: "payment_workflow")]
    public function start(int $id);
}
