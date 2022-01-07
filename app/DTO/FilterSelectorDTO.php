<?php

namespace App\DTO;

class FilterSelectorDTO
{
    public function __construct(
        private array $values,
        private int $id,
    ) {}
    
    public function getValues(): array
    {
        return $this->values;
    }
    
    public function getId(): int
    {
        return $this->id;
    }
}
