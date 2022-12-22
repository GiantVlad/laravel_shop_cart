<?php

namespace App\Services\Erp;

use App\Services\Erp\DTO\ErpOrderDTO;

class ErpServiceStub implements ErpServiceInterface
{
    public function placeNewOrder(ErpOrderDTO $orderDTO): string
    {
        sleep(1);
        
        return uuid_create();
    }
}
