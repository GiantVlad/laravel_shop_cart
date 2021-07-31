<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RelatedProduct extends Model
{
    use HasFactory;
    protected $fillable = array('points');
    
    /**
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            'App\Product',
            'products_related_products',
            'product_id',
            'related_product_id'
        )->withTimestamps();
    }
    
    /**
     * @param array $productsInCart
     * @return Product|null
     */
    public function getRelatedProduct(array $productsInCart): ?Product
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
