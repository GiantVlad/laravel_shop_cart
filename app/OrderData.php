<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderData extends Model
{
    protected $fillable = array('order_id', 'product_id', 'is_related_product', 'price', 'qty');
    protected $table = 'order_data';
    public function products()
    {
        return $this->belongsTo('App\Order', 'id','order_id');
    }
}
