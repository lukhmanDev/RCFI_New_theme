<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subtheme extends Model
{
    protected $table = 'subthemes';
    protected $guarded = [];

    public function theme()
    {
        return $this->belongsTo(Theme::class, 'theme_id');
    }
}
