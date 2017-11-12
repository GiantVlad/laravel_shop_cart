<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Product extends Model
{
    protected $fillable = array('name', 'description', 'price', 'catalog_id', 'image');

    public function relatedProducts()
    {
        return $this->hasMany('App\RelatedProduct', 'related_product_id', 'id');
    }

    public function properties()
    {
        return $this->belongsToMany('App\Property', 'product_property');
    }

    public function catalogs()
    {
        return $this->belongsTo('App\Catalog', 'catalog_id','id');
    }

    public function getProductPriceById (int $id) {

        return $this::find($id)->price;
    }
}
