<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class PropertyValue
 * @package App
 * @property int $id
 * @property int $property_id
 * @property int $value
 * @property int $unit_id
 */
class PropertyValue extends Model
{
    use HasFactory;
    
    protected $fillable = array('property_id', 'value', 'unit_id');
    
    /**
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany('App\Product', 'product_property', 'property_value_id','product_id' )->withTimestamps();
    }
    
    /**
     * @return BelongsTo
     */
    public function properties(): BelongsTo
    {
        return $this->belongsTo('App\Property', 'property_id','id');
    }
}
