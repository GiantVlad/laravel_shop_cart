<?php

namespace App\Temporal;

use App\Services\Erp\DTO\ErpOrderDTO;
use App\Services\Erp\ErpServiceInterface;
use Temporal\Activity\ActivityMethod;

class ErpActivity implements ErpActivityInterface
{
    public function __construct(readonly private ErpServiceInterface $erpService)
    {
    }
    
    #[ActivityMethod(name: "sendOrder")]
    public function sendOrder(int $orderId): string
    {
        $orderDto = new ErpOrderDTO($orderId, json_encode(['items' => []]));
        
        return $this->erpService->placeNewOrder($orderDto);
    }
}
