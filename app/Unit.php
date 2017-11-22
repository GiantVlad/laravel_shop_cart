<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = array('name', 'code', 'full_name', 'decimal_place');
}
