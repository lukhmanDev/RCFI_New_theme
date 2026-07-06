<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrinkingWaterIndividualProject extends Model
{
    protected $table = 'drinking_water_individual_projects';
    protected $guarded = [];
    protected $casts = ['files' => 'array', 'materials' => 'array'];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($project) {
            $year = date('y');
            $idString = str_pad($project->id, 3, '0', STR_PAD_LEFT);
            $project->project_id = 'RCFI' . $year . 'DWI' . $idString;
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
}
