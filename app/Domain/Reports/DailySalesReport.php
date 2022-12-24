<?php

declare(strict_types=1);

namespace App\Domain\Reports;

use App\Repositories\OrderRepository;
use App\Services\Erp\ErpServiceInterface;
use Carbon\Carbon;

class DailySalesReport
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly ErpServiceInterface $erpService,
    ) {
    }
    
    public function createReport(Carbon $day = null): void
    {
        if ($day === null) {
            $day = (new Carbon())->subDay();
        }
        
        $orders = $this->orderRepository->getDailyOrders($day);
    
        $this->erpService->sendDailySalesReport($orders);
    }
}
