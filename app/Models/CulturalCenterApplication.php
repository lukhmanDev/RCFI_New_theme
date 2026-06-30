<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CulturalCenterApplication extends Model
{
    protected $table = 'cultural_center_applications';
    protected $guarded = [];

    protected $casts = [
        'meta' => 'array'
    ];
}
