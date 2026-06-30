<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyAidApplication extends Model
{
    protected $table = 'family_aid_applications';
    protected $guarded = [];

    protected $casts = [
        'meta' => 'array'
    ];
}
