<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrinkingWaterGroupApplication extends Model
{
    protected $table = 'drinking_water_group_applications';
    protected $guarded = [];

    protected $casts = [
        'meta' => 'array'
    ];
}
