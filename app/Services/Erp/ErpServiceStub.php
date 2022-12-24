<?php

declare(strict_types=1);

namespace App\Services\Erp;

use App\Services\Erp\DTO\ErpOrderDTO;
use Illuminate\Database\Eloquent\Collection;

class ErpServiceStub implements ErpServiceInterface
{
    public function placeNewOrder(ErpOrderDTO $orderDTO): string
    {
        sleep(1);
        
        return uuid_create();
    }
    
    public function sendDailySalesReport(Collection $orders): void
    {
        usleep(500);
    }
}
