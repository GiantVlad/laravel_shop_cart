<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = array('name', 'prop_group_id', 'priority', 'type');

    public function propertyValues()
    {
        return $this->hasMany('App\PropertyValue', 'property_id', 'id');
    }
}
