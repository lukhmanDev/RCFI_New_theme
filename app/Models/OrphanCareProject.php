<?php

namespace App\Models;

use App\Traits\HasProjectColumns;

use Illuminate\Database\Eloquent\Model;

class OrphanCareProject extends Model
{
    use HasProjectColumns;
    protected $table = 'orphan_care_projects';
    protected $guarded = [];
    protected $casts = [
        'stage' => 'integer',
        'community_contributions' => 'array',
        'completion_details' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($project) {
            $year = date('y');
            $idString = str_pad($project->id, 3, '0', STR_PAD_LEFT);
            $unitPrefix = (strtoupper($project->unit) === 'MARKAZ') ? 'MRKZ/' : 'RCFI/';
            $project->project_id = $unitPrefix . $year . '-OC' . $idString;
            $project->saveQuietly();
        });
    }

    public function programmes()
    {
        return $this->hasMany(OrphanCareProgramme::class, 'orphan_care_project_id');
    }

    public function funds()
    {
        return $this->hasMany(OrphanCareFund::class, 'orphan_care_project_id');
    }
}
