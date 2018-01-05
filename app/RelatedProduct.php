<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class RelatedProduct extends Model
{
    protected $fillable = array('points');

    public function products()
    {
        return $this->belongsToMany('App\Product', 'products_related_products', 'product_id', 'related_product_id')->withTimestamps();
    }

    public function getRelatedProduct(array $productsInCart)
    {
        return Product::whereHas('relatedProducts', function ($query) use ($productsInCart) {
            $query->whereIn('product_id', $productsInCart);
        })
        ->whereNotIn('products.id', $productsInCart)
        ->join('related_products', 'products.id', '=', 'related_products.id')
        ->orderBy('points', 'desc')
        ->first();
    }
}
