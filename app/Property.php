<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Property
 * @package App
 *
 * @property int $id
 */
class Property extends Model
{
    use HasFactory;
    
    public const TYPE_SELECTOR = 'selector';
    public const TYPE_NUMBER = 'number';
    
    protected $fillable = array('name', 'prop_group_id', 'priority', 'type');
    
    /**
     * @return string[]
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_NUMBER,
            self::TYPE_SELECTOR,
        ];
    }
    
    /**
     * @return HasMany
     */
    public function propertyValues(): HasMany
    {
        return $this->hasMany('App\PropertyValue', 'property_id', 'id');
    }
}
