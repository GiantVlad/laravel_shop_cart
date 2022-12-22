<?php

namespace App\Services\Erp;

use App\Services\Erp\DTO\ErpOrderDTO;

interface ErpServiceInterface
{
    public function placeNewOrder(ErpOrderDTO $orderDTO): string;
}
