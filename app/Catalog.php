<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Catalog extends Model
{
    protected $fillable = array('name', 'description', 'parent_id', 'image', 'priority');

    public function products()
    {
        return $this->hasMany('App\Product', 'catalog_id', 'id');
    }
}
