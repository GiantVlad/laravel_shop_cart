<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = array('property_id', 'name', 'value', 'prop_group_id', 'unit_id');

    public function products()
    {
        return $this->belongsToMany('App\Product', 'product_property');
    }
}
