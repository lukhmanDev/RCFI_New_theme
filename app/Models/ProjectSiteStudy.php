<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectSiteStudy extends Model
{
    protected $table = 'project_site_studies';

    protected $fillable = [
        'project_id',
        'project_type',
        'report',
        'remarks',
        'file_path',
        'status',
        'ticked_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'ticked_at' => 'datetime',
    ];

    /**
     * Get the owning project model.
     */
    public function project()
    {
        return $this->morphTo();
    }
}
