<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrphanCareApplication extends Model
{
    protected $table = 'orphan_care_applications';
    protected $guarded = [];

    protected $casts = [
        'meta' => 'array'
    ];
}
