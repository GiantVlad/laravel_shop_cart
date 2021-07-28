<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class OrderData
 * @package App
 * @property int $order_id
 * @property int $product_id
 * @property bool $is_related_product
 * @property float $price
 * @property int $qty
 *
 * @method Builder byOrderId(int $orderId)
 */
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
    
    /**
     * @param Builder $query
     * @param int $orderId
     * @return Builder
     */
    public function scopeByOrderId(Builder $query, int $orderId): Builder
    {
        return $query->where('order_id', $orderId);
    }
}
