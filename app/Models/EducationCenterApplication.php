<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationCenterApplication extends Model
{
    protected $table = 'education_center_applications';
    protected $guarded = [];

    protected $casts = [
        'meta' => 'array'
    ];
}
