<?php

declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PaymentMethod
 * @package App
 *
 * @property int $id
 * @property string $class_name
 * @property int $priority
 * @property boolean $enabled
 * @property boolean $selected
 * @property string $label
 *
 * @method PaymentMethod findOrFail(int $id)
 */
class PaymentMethod extends Model
{
    use HasFactory;
    
    /**
     * @var string[]
     */
    protected $fillable = ['class_name', 'priority', 'enabled', 'selected', 'label'];
}
