<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralApplication extends Model
{
    protected $table = 'general_applications';
    protected $guarded = [];

    protected $casts = [
        'meta' => 'array'
    ];
}
