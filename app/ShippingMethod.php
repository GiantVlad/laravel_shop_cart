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
    protected $fillable = array('label', 'status');

    public function getAllEnabled() {
        return $this->where('enable', 1)->get();
    }

    public function getRate() {

    }

    static function getLabel() {

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
        if (!empty($shippingMethods)) {
            foreach ($shippingMethods as $shippingMethod) {
                $shippingMethod->class_name = 'App\\'.$shippingMethod->class_name;
                $shippingMethod->label = $shippingMethod->class_name::getLabel();
            }
        }
        return $shippingMethods;
    }

}