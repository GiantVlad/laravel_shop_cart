<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Product extends Model
{
    protected $fillable = array('name', 'description', 'price');

    public function relatedProducts()
    {
        return $this->hasMany('App\RelatedProduct', 'related_product_id', 'id');
    }
    public function getProductPriceById (int $id) {

        return $this::find($id)->price;
    }
}
