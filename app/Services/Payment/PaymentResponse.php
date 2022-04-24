<?php

namespace App\Services\Payment;

class PaymentResponse
{
    public function __construct(
        private $data,
        private string $externalId,
        private ?string $checkoutUrl = null,
        private ?int $paymentId = null,
    )
    {}
    
    public function getData()
    {
        return $this->data;
    }
    
    public function getCheckoutUrl(): ?string
    {
        return $this->checkoutUrl;
    }
    
    public function getExternalId(): ?string
    {
        return $this->externalId;
    }
    
    public function getPaymentId(): ?int
    {
        return $this->paymentId;
    }
    
    public function setPaymentId(int $paymentId): self
    {
        $this->paymentId = $paymentId;
        
        return $this;
    }
}
