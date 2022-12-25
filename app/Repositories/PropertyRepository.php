<?php

namespace App\Repositories;

use App\Property;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class PropertyRepository
{
    public function __construct(
        private readonly Property $mProperty
    ) {}
    
    public function getFilteredProducts(): EloquentCollection
    {
        return $this->mProperty->newQuery()
            ->with('propertyValues')
            ->orderBy('priority')
            ->get();
    }
}
