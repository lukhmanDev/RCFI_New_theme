<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BroadcastsChanges;
use App\Traits\BroadcastsTableChanges;

class Project extends Model
{
    use BroadcastsChanges, BroadcastsTableChanges;

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
            $unitPrefix = (strtoupper($project->unit) === 'MARKAZ') ? 'MRKZ/' : 'RCFI/';
            $project->project_id = $unitPrefix . $year . '-' . $prefix . $idString;
            $project->saveQuietly();
        });
    }

    public function toBroadcastArray(): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id ?? 'N/A',
            'title' => $this->name_of_project ?? $this->title ?? 'Project #' . $this->id,
            'status' => $this->status ?? 'Running',
            'stage' => $this->current_stage ?? 1,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : now()->toIso8601String(),
        ];
    }

    public function broadcastChannels(): array
    {
        return [
            'role.super_admin',
            'role.coo',
            'role.project_manager',
            'role.hod',
            'role.engineer',
        ];
    }

    public function toTableRowArray(): array
    {
        return $this->toBroadcastArray();
    }

    public function tableChannels(): array
    {
        return $this->broadcastChannels();
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

    public function engineer()
    {
        return $this->belongsTo(User::class, 'engineer_id');
    }
}
