<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectCommunityContribution extends Model
{
    protected $table = 'project_community_contributions';

    protected $fillable = [
        'project_id',
        'project_type',
        'item',
        'amount',
    ];

    /**
     * Get the owning project model.
     */
    public function project()
    {
        return $this->morphTo();
    }
}
