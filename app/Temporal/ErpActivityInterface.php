<?php

namespace App\Temporal;

use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;

#[ActivityInterface(prefix: "erp_activity.")]
interface ErpActivityInterface
{
    #[ActivityMethod(name: "sendOrder")]
    public function sendOrder(int $orderId): string;
}
