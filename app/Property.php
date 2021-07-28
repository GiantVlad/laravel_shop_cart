<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Property
 * @package App
 */
class Property extends Model
{
    protected $fillable = array('name', 'prop_group_id', 'priority', 'type');
    
    /**
     * @return HasMany
     */
    public function propertyValues(): HasMany
    {
        return $this->hasMany('App\PropertyValue', 'property_id', 'id');
    }
}
