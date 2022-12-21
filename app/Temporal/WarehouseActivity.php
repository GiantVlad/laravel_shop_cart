<?php

namespace App\Temporal;

use App\Services\Warehouse\DTO\WarehouseOrderDTO;
use App\Services\Warehouse\WarehouseServiceInterface;
use Temporal\Activity\ActivityMethod;

class WarehouseActivity implements WarehouseActivityInterface
{
    public function __construct(readonly private WarehouseServiceInterface $warehouseService)
    {
    }
    
    #[ActivityMethod(name: "sendOrder")]
    public function sendOrder(int $orderId): bool
    {
        $orderDto = new WarehouseOrderDTO($orderId, json_encode(['items' => []]));
        $this->warehouseService->createNewOrder($orderDto);
            
        return true;
    }
}
