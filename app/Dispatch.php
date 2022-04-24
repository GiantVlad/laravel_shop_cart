<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Dispatch
 * @package App
 *
 * @property int $id
 * @property string $details
 */
class Dispatch extends Model
{
    use HasFactory;
    
    protected $fillable = ['details', 'order_id', 'shipping_method_id', 'code'];
    
    public function method(): BelongsTo
    {
        return $this->belongsTo(ShippingMethod::class, 'shipping_method_id', 'id');
    }
}
