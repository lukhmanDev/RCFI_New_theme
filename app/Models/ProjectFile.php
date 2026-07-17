<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectFile extends Model
{
    protected $fillable = [
        'project_id',
        'project_type',
        'photos',
        'photos_before',
        'photos_starting',
        'photos_inbetween',
        'photos_after',
        'photos_banner',
        'photos_stone',
        'photos_inauguration',
    ];

    public function project()
    {
        return $this->morphTo();
    }
}
