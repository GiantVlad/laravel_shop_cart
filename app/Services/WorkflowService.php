<?php

namespace App\Services;

use App\Jobs\CheckPaymentJob;
use App\Jobs\SendOrderToWarehouseJob;

class WorkflowService
{
    public function checkPayment(int $paymentId): void
    {
        CheckPaymentJob::dispatch($paymentId);
    }

    public function sendOrderToWarehouse(int $orderId): void
    {
        SendOrderToWarehouseJob::dispatch($orderId);
    }
}
