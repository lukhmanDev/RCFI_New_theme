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
        'coo_approved_at',
        'coo_approver_id',
        'coo_remarks',
    ];

    public function project()
    {
        return $this->morphTo();
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'coo_approver_id');
    }
}
