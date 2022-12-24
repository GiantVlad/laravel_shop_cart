<?php

declare(strict_types=1);

namespace App\Temporal\Cron;

use App\Domain\Reports\DailySalesReport;

class SalesReportActivity implements SalesReportActivityInterface
{
    public function __construct(private readonly DailySalesReport $report)
    {
    }
    
    public function composeCreating(): void
    {
        $this->report->createReport();
    }
}
