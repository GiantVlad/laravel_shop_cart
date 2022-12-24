<?php

declare(strict_types=1);

namespace App\Temporal\Cron;

use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;

#[ActivityInterface(prefix: 'Cron.')]
interface SalesReportActivityInterface
{
    #[ActivityMethod(name: "compose-creating")]
    public function composeCreating(): void;
}
