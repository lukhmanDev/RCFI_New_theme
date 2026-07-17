<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectInspection extends Model
{
    protected $fillable = [
        'project_id',
        'project_type',
        'name',
        'designation',
        'date',
        'remarks',
    ];

    public function project()
    {
        return $this->morphTo();
    }
}
