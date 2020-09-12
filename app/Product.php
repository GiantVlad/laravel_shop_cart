<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Product extends Model
{
    use HasFactory;

    protected $fillable = array('name', 'description', 'price', 'catalog_id', 'image');

    public function relatedProducts()
    {
        return $this->belongsToMany('App\Product', 'products_related_products', 'related_product_id', 'product_id');
    }

    public function relatedProductData()
    {
        return $this->hasOne('App\RelatedProduct', 'id', 'id');
    }

    public function properties()
    {
        return $this->belongsToMany('App\PropertyValue', 'product_property', 'product_id', 'property_value_id')->with('properties')->withTimestamps();
    }

    public function catalogs()
    {
        return $this->belongsTo('App\Catalog', 'catalog_id','id');
    }

    public function getProductPriceById (int $id) {

        return $this::find($id)->price;
    }
}
