<?php

declare(strict_types=1);

namespace App\Services\Erp;

use App\Services\Erp\DTO\ErpOrderDTO;
use Illuminate\Database\Eloquent\Collection;

interface ErpServiceInterface
{
    public function placeNewOrder(ErpOrderDTO $orderDTO): string;
    
    public function sendDailySalesReport(Collection $orders): void;
}
