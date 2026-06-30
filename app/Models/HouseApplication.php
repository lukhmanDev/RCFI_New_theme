<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HouseApplication extends Model
{
    protected $table = 'house_applications';
    protected $guarded = [];

    protected $casts = [
        'meta' => 'array'
    ];
}
