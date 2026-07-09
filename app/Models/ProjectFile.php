<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectFile extends Model
{
    protected $fillable = [
        'project_id',
        'project_type',
        'name',
        'path'
    ];

    public function project()
    {
        return $this->morphTo();
    }
}
