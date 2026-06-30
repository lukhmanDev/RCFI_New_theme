<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrinkingWaterIndividualApplication extends Model
{
    protected $table = 'drinking_water_individual_applications';
    protected $guarded = [];

    protected $casts = [
        'meta' => 'array'
    ];
}
