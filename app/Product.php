<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Product
 * @package App
 *
 * @property int $id
 * @property float $price
 * @property string $name
 * @property string $description
 * @property int $catalog_id
 * @property string $image
 * @property int $qty (optional)
 * @property bool $is_related (optional)
 * @property Collection $properties
 *
 * @method Product findOrFail(int $id)
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = array('name', 'description', 'price', 'catalog_id', 'image');
    
    /**
     * @return BelongsToMany
     */
    public function relatedProducts(): BelongsToMany
    {
        return $this->belongsToMany(
            'App\Product',
            'products_related_products',
            'related_product_id',
            'product_id'
        );
    }
    
    /**
     * @return HasOne
     */
    public function relatedProductData(): HasOne
    {
        return $this->hasOne('App\RelatedProduct', 'id', 'id');
    }
    
    /**
     * @return BelongsToMany
     */
    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(
            'App\PropertyValue',
            'product_property',
            'product_id',
            'property_value_id'
        )->with('properties')->withTimestamps();
    }
    
    /**
     * @return BelongsTo
     */
    public function catalogs(): BelongsTo
    {
        return $this->belongsTo('App\Catalog', 'catalog_id','id');
    }
    
    /**
     * @param int $id
     * @return float
     */
    public function getProductPriceById(int $id): float
    {
        return $this->findOrFail($id)->price;
    }
}
