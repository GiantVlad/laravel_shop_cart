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
class Payment extends Model
{
    use HasFactory;
    
    const STATUS_INITIALIZED = 0;
    const STATUS_CREATED = 1;
    const STATUS_PAID = 5;
    
    protected $fillable = ['details', 'order_id', 'payment_method_id', 'external_id', 'status'];
    
    public function method(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id', 'id');
    }
}
