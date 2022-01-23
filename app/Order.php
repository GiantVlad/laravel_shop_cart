<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Order
 * @package App
 *
 * @property int $id
 * @property float $total
 * @property string $commentary
 * @property string $status
 * @property string $order_label
 * @property int $user_id
 *
 * @method Order findOrFail(int $id)
 */
class Order extends Model
{
    use HasFactory;
    
    /**
     * @var string[]
     */
    protected $fillable = array('commentary', 'total', 'status', 'order_label');
    
    /**
     * @return HasMany
     */
    public function orderData(): HasMany
    {
        return $this->hasMany('App\OrderData', 'order_id', 'id');
    }
    
    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
    
    /**
     * @param string $label
     * @return Builder
     */
    public function getOrderByLabel(string $label): Builder
    {
        return $this->where('order_label', 'like' ,  '%'.$label.'%');
    }
    
    /**
     * @param int $id
     * @return Builder
     */
    public function getOrdersByUserId(int $id): Builder
    {
        return $this->where('user_id', $id);
    }
    
    /**
     * @param int $id
     * @return Order|Collection
     */
    public function getOrderById(int $id): self|Collection
    {
        return $this->findOrFail($id);
    }
}
