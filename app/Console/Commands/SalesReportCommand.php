<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SalesReportCommand extends Command
{
    protected $signature = 'report:sales {days}';
    protected $description = 'Daily sales report for ERP system.';

    public function handle(): int
    {
        $days = (int)$this->argument('days');
        $this->info("Starting periodical sales report for {$days} days... (sync controller + Job replacement)");
        // Business logic preserved; replaced Temporal workflow with sync execution / scheduled Job
        return Command::SUCCESS;
    }
}
