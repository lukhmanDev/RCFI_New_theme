<?php

namespace App\Models;

use App\Traits\HasProjectColumns;

use Illuminate\Database\Eloquent\Model;

class DifferentlyAbledProject extends Model
{
    use HasProjectColumns;
    protected $table = 'differently_abled_projects';
    protected $guarded = [];
    protected $casts = ['community_contributions' => 'array', 'completion_details' => 'array'];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($project) {
            $year = date('y');
            $idString = str_pad($project->id, 3, '0', STR_PAD_LEFT);
            $unitPrefix = (strtoupper($project->unit) === 'MARKAZ') ? 'MRKZ/' : 'RCFI/';
            $project->project_id = $unitPrefix . $year . '-DA' . $idString;
            $project->saveQuietly();
        });
    }

    public function donor()
    {
        return $this->belongsTo(Donor::class, 'donor_id');
    }

    public function projectManager()
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    public function engineer()
    {
        return $this->belongsTo(User::class, 'engineer_id');
    }
}
