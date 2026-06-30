<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopOtherApplication extends Model
{
    protected $table = 'shop_other_applications';
    protected $guarded = [];

    protected $casts = [
        'meta' => 'array'
    ];
}
