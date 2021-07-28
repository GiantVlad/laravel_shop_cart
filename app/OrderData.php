<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderData extends Model
{
    use HasFactory;
    
    protected $fillable = array('order_id', 'product_id', 'is_related_product', 'price', 'qty');
    protected $table = 'order_data';
    
    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo('App\Product', 'product_id','id');
    }
}
