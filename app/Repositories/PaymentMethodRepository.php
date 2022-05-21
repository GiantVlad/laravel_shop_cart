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
    
    public function changeStatus(int $id, bool $status): void
    {
        /** @var PaymentMethod $method */
        $method = $this->mPaymentMethod->findOrFail($id);
        $method->enabled = $status;
        $method->save();
    }
    
    public function delete(int $id): void
    {
        /** @var PaymentMethod $method */
        $method = $this->mPaymentMethod->findOrFail($id);
        $method->delete();
    }
    
    public function addFromConfig(array $methodConfig): void
    {
        $this->mPaymentMethod->config_key = $methodConfig['config_key'];
        $this->mPaymentMethod->label = $methodConfig['label'];
        $this->mPaymentMethod->priority = 0;
        $this->mPaymentMethod->class_name = $methodConfig['class_name'];
        $this->mPaymentMethod->enabled = false;
        $this->mPaymentMethod->save();
    }
    
    public function updateById(int $id, array $array): void
    {
        /** @var PaymentMethod $method */
        $method = $this->mPaymentMethod->findOrFail($id);
        $method->update($array);
    }
}
