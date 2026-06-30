<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DifferentlyAbledApplication extends Model
{
    protected $table = 'differently_abled_applications';
    protected $guarded = [];

    protected $casts = [
        'meta' => 'array'
    ];
}
