<?php

namespace App\Services\Warehouse\ValueObject;

enum WarehouseOrderStatus
{
    case received;
    case confirmed;
    case processing;
    case sent;
}
