<?php

namespace App\Temporal;

use Temporal\Activity\ActivityOptions;
use Temporal\Workflow;

class PaymentWorkflow implements PaymentWorkflowInterface
{
    #[Workflow\WorkflowMethod(name: "payment_workflow")]
    public function start(int $id)
    {
        $activity = Workflow::newActivityStub(
            PaymentActivityInterface::class,
            ActivityOptions::new()->withStartToCloseTimeout(10)
        );
    
        return yield $activity->checkStatus($id);
    }
}
