<?php

namespace App\Temporal;

use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;

#[ActivityInterface(prefix: "warehouse_activity.")]
interface WarehouseActivityInterface
{
    #[ActivityMethod(name: "sendOrder")]
    public function sendOrder(int $orderId): bool;
}
