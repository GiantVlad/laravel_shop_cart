<?php

namespace App\DTO;

class FilterNumberDTO
{
    public function __construct(
        private float $minValue,
        private float $maxValue,
        private int $id,
    ) {}
    
    public function getMinValue(): float
    {
        return $this->minValue;
    }
    
    public function getMaxValue(): float
    {
        return $this->maxValue;
    }
    
    public function getId(): int
    {
        return $this->id;
    }
}
