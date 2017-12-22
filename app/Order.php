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

    public function users ()
    {
        return $this->belongsTo('User', 'user_id', 'id');
    }

    public function getOrderByID(int $id)
    {
        return $this->find($id);
    }
}
