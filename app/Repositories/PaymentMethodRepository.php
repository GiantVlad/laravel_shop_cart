<?php

namespace App\Repositories;

use App\PaymentMethod;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class PaymentMethodRepository
{
    public function __construct(
        private PaymentMethod $mPaymentMethod
    ) {}
    
    /**
     * @param bool $enabled
     * @return EloquentCollection
     */
    public function getList(bool $enabled = true): EloquentCollection
    {
        $query = $this->mPaymentMethod
            ->when($enabled, function ($query) {
                $query->where('enabled', true);
            });
        
        return $query->orderBy('priority')->get();
    }
}
