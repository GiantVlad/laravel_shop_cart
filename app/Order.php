<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;


class Order extends Model
{
    protected $fillable = array('commentary', 'total', 'status');

    public function orderData ()
    {
        return $this->hasMany('App\OrderData', 'order_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function getOrderByLabel(string $label)
    {
        return $this->where('order_label', 'like' ,  '%'.$label.'%');
    }
}
