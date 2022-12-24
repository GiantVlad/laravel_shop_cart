<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Temporal\Cron\SalesReportWorkflowInterface;
use Carbon\CarbonInterval;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Temporal\Client\WorkflowClientInterface;
use Temporal\Client\WorkflowOptions;
use Temporal\Exception\Client\WorkflowExecutionAlreadyStartedException;

class SalesReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:sales {days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily sales report for ERP system. The days argument is a period during which it works.';

    public function __construct(private readonly WorkflowClientInterface $workflowClient)
    {
        parent::__construct();
    }
    
    public function handle(): int
    {
        $workflow = $this->workflowClient->newWorkflowStub(
            SalesReportWorkflowInterface::class,
            WorkflowOptions::new()
                ->withWorkflowId(SalesReportWorkflowInterface::WORKFLOW_ID)
                // every day at 1:30
                ->withCronSchedule('30 1 * * *')
                // Execution timeout limits total time. Cron will stop executing after this timeout.
                ->withWorkflowExecutionTimeout(CarbonInterval::days($this->argument('days')))
                // Run timeout limits duration of a single workflow invocation.
                ->withWorkflowRunTimeout(CarbonInterval::minutes(15))
        );
    
        $this->info("Starting <comment>Periodical sales report</comment>... ");
    
        try {
            $run = $this->workflowClient->start($workflow);
    
            $this->info(
                sprintf(
                    'Started: WorkflowID=<fg=magenta>%s</fg=magenta>, RunID=<fg=magenta>%s</fg=magenta>',
                    $run->getExecution()->getID(),
                    $run->getExecution()->getRunID(),
                )
            );
        } catch (WorkflowExecutionAlreadyStartedException $e) {
            $this->error('<fg=red>Workflow execution already started</fg=red>');
            
            return SymfonyCommand::FAILURE;
        }
    
        return SymfonyCommand::SUCCESS;
    }
}
