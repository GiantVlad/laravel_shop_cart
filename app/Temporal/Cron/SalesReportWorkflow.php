<?php

declare(strict_types=1);

namespace App\Temporal\Cron;

use Carbon\CarbonInterval;
use Temporal\Activity\ActivityOptions;
use Temporal\Workflow;

class SalesReportWorkflow implements SalesReportWorkflowInterface
{
    private $salesReportActivity;
    
    public function __construct()
    {
        /** @var SalesReportActivityInterface $salesReportActivity */
        $this->salesReportActivity = Workflow::newActivityStub(
            SalesReportActivityInterface::class,
            ActivityOptions::new()
                ->withScheduleToCloseTimeout(CarbonInterval::seconds(30))
        );
    }
    
    public function create(): \Generator
    {
        return yield $this->salesReportActivity->composeCreating();
    }
}
