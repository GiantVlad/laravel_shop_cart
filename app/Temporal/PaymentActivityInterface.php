<?php

namespace App\Temporal;

use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;

#[ActivityInterface(prefix: "payment_activity.")]
interface PaymentActivityInterface
{
    #[ActivityMethod(name: "paymentStatus")]
    public function checkStatus(int $paymentId): bool;
}
