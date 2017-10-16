<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Order extends Model
{
    protected $fillable = array('commentary', 'total', 'status');

    public function orderData()
    {
        return $this->hasMany('App\OrderData', 'order_id', 'id');
    }
}
