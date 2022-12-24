<?php

declare(strict_types=1);

namespace App\Temporal\Cron;

use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;

#[WorkflowInterface]
interface SalesReportWorkflowInterface
{
    public const WORKFLOW_ID = 'Report.sales';
    
    /**
     * @return string
     */
    #[WorkflowMethod(name: "Report.sales.create")]
    public function create();
}
