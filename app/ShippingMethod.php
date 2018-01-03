<?php
/**
 * Created by PhpStorm.
 * User: ale
 * Date: 22.12.2017
 * Time: 13:34
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    protected $fillable = array('class_name', 'enable', 'priority');

    public function getAllEnabled() {
        $shippingMethods = $this->where('enable', 1)->orderBy('priority')->get();
        return $this->getParams($shippingMethods);
    }

    static function getRate()
    {

    }

    static function getLabel()
    {

    }
    static function getDeliveryTime ()
    {

    }

    public function getStatusById(int $id)
    {
        return $this->find($id)->enable;
    }

    public function setStatusById(int $id, bool $status)
    {
        $this->where('id', $id)->update(['enable' => $status]);
    }

    public function list ()
    {
        $shippingMethods = $this->orderBy('priority')->get();
        return $this->getParams($shippingMethods);
    }

    private function getParams($shippingMethods)
    {
        if (!empty($shippingMethods)) {
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