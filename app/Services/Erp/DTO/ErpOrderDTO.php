<?php

namespace App\Services\Erp\DTO;

class ErpOrderDTO
{
    private int $orderId;
    
    private string $payload;
    
    public function __construct(int $orderId, string $payload)
    {
        $this->orderId = $orderId;
        $this->payload = $payload;
    }
    
    public function getOrderId(): int
    {
        return $this->orderId;
    }
    
    public function setOrderId(int $orderId): self
    {
        $this->orderId = $orderId;
        
        return $this;
    }
    
    public function getPayload(): string
    {
        return $this->payload;
    }
    
    public function setPayload(string $payload): self
    {
        $this->payload = $payload;
        
        return $this;
    }
}
