<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ShippingMethod
 * @package App
 *
 * @property bool|int $enable
 * @property bool $priority
 * @property string $label
 * @property int $rate
 * @property string $time
 * @property string $class_name
 *
 * @method ShippingMethod findOrFail(int $id)
 */
class ShippingMethod extends Model
{
    private const NAMESPACE = 'App\\Services\\Shipping\\';
    protected $fillable = array('class_name', 'enable', 'priority');
    
    /**
     * @return Collection
     */
    public function getAllEnabled(): Collection
    {
        $shippingMethods = $this->where('enable', 1)->orderBy('priority')->get();
        
        return $this->addImplementation($shippingMethods);
    }
    
    /**
     * @param int $id
     * @return bool
     */
    public function getStatusById(int $id): bool
    {
        return (bool)$this->findOrFail($id)->enable;
    }
    
    /**
     * @param int $id
     * @param bool $status
     */
    public function setStatusById(int $id, bool $status): void
    {
        $this->where('id', $id)->update(['enable' => $status]);
    }
    
    /**
     * @return Collection
     */
    public function list(): Collection
    {
        $shippingMethods = $this->orderBy('priority')->get();
        
        return $this->addImplementation($shippingMethods);
    }
    
    /**
     * @param Collection $shippingMethods
     * @return Collection
     */
    private function addImplementation(Collection $shippingMethods): Collection
    {
        if ($shippingMethods->isNotEmpty()) {
            $shippingMethods->filter(
                fn(ShippingMethod $shippingMethod) => class_exists(self::NAMESPACE . $shippingMethod->class_name)
            );
            
            $shippingMethods->map(static function(ShippingMethod $shippingMethod) {
                $shippingMethod->class_name = self::NAMESPACE . $shippingMethod->class_name;
                $shippingMethod->label = $shippingMethod->class_name::getLabel();
                $shippingMethod->rate = $shippingMethod->class_name::getRate();
                $shippingMethod->time = $shippingMethod->class_name::getDeliveryTime();
            });
        }
        
        return $shippingMethods;
    }
}
