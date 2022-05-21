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
 * @property string $config_key
 *
 * @method PaymentMethod findOrFail(int $id)
 * @method PaymentMethod update(array $values)
 * @method PaymentMethod delete()
 * @method PaymentMethod save()
 */
class PaymentMethod extends Model
{
    use HasFactory;
    
    /**
     * @var string[]
     */
    protected $fillable = ['class_name', 'priority', 'config_key', 'enabled', 'label'];
}
