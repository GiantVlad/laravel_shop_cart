<?php

namespace App\Services\Warehouse;

use App\Services\Warehouse\DTO\WarehouseOrderDTO;
use App\Services\Warehouse\ValueObject\WarehouseOrderStatus;

interface WarehouseServiceInterface
{
    public function createNewOrder(WarehouseOrderDTO $orderDTO): bool;
    
    public function updateOrder(WarehouseOrderDTO $orderDTO): bool;
    
    public function cancelOrder(int $orderId): bool;
    
    public function getOrderStatus(int $orderId): WarehouseOrderStatus;
}
