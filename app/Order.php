<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Order extends Model
{
    use HasFactory;
    
    /**
     * @var string[]
     */
    protected $fillable = array('commentary', 'total', 'status', 'order_label');

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

    public function getOrdersByUserId(int $id)
    {
        return $this->where('user_id', $id);
    }

    public function getOrderById(int $id)
    {
        return $this->find($id);
    }
}
