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
 *
 * @method ShippingMethod findOrFail(int $id)
 */
abstract class ShippingMethod extends Model
{
    protected $fillable = array('class_name', 'enable', 'priority');
    
    /**
     * @return Collection
     */
    public function getAllEnabled(): Collection
    {
        $shippingMethods = $this->where('enable', 1)->orderBy('priority')->get();
        return $this->getParams($shippingMethods);
    }
    
    /**
     * @return int
     */
    abstract public static function getRate(): int;
    
    /**
     * @return string
     */
    abstract public static function getLabel(): string;
    
    /**
     * @return string
     */
    abstract public static function getDeliveryTime(): string;
    
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
        
        return $this->getParams($shippingMethods);
    }
    
    /**
     * @param Collection $shippingMethods
     * @return Collection
     */
    private function getParams(Collection $shippingMethods): Collection
    {
        if ($shippingMethods->isNotEmpty()) {
            foreach ($shippingMethods as $shippingMethod) {
                $shippingMethod->class_name = 'App\\'.$shippingMethod->class_name;
                $shippingMethod->label = $shippingMethod->class_name::getLabel();
                $shippingMethod->rate = $shippingMethod->class_name::getRate();
                $shippingMethod->time = $shippingMethod->class_name::getDeliveryTime();
            }
        }
        
        return $shippingMethods;
    }
}
