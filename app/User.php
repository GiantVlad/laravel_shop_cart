<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 * @package App
 */
class User extends Authenticatable
{
    use Notifiable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'cart',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    /**
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany('Order', 'user_id', 'id');
    }
    
    /**
     * @param int $userId
     * @return int
     */
    public function markForLogoutById(int $userId): int
    {
        return $this->where('id', $userId)
            ->update(['cart' => '', 'force_logout' => true]);
    }
}
