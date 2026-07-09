<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectStatus extends Model
{
    protected $table = 'project_statuses';

    protected $fillable = [
        'project_id',
        'project_type',
        'status',
        'status_custom',
    ];

    public function project()
    {
        return $this->morphTo();
    }
}
