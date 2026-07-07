<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($project) {
            $prefixes = [
                'Education Center' => 'EC',
                'Cultural Center' => 'CC',
                'Hospital or Clinics' => 'HC',
                'Shops and Others' => 'SO',
                'House' => 'HS',
                'Drinking Water - Group Level' => 'DWG',
                'Drinking Water - Individual Level' => 'DWI',
                'Orphan Care' => 'OC',
                'Differently Abled' => 'DA',
                'Family Aid' => 'FA',
                'General' => 'GN'
            ];
            $prefix = $prefixes[$project->type_of_project] ?? 'APP';
            $year = date('y');
            $idString = str_pad($project->id, 3, '0', STR_PAD_LEFT);
            $project->project_id = 'RCFI' . $year . $prefix . $idString;
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

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }
}
