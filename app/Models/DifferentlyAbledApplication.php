<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCategoryMeta;

class DifferentlyAbledApplication extends Model
{
    use HasCategoryMeta;

    protected $table = 'differently_abled_applications';
    protected $guarded = [];
    protected $attributes = [
        'sponsor_status' => 'Not Sponsored',
    ];

    public function cluster()
    {
        return $this->belongsTo(Cluster::class, 'cluster_id');
    }

    public static function ensureProjectExists($application)
    {
        $projectExists = \App\Models\DifferentlyAbledProject::where('application_id', $application->id)->exists();
        if (!$projectExists) {
            $year = date('y');
            $idString = str_pad($application->id, 3, '0', STR_PAD_LEFT);
            $tempId = 'RCFI/' . $year . '-DA' . $idString;

            \App\Models\DifferentlyAbledProject::create([
                'application_id' => $application->id,
                'project_name' => $application->applicant_name ?? 'Differently Abled Project',
                'agency_project_no' => $application->agency_number,
                'project_id' => $tempId,
                'type_of_project' => 'Differently Abled',
                'sponsor' => 'Sponsored',
                'stage' => 1,
                'status' => 'Active',
            ]);
        } else {
            $project = \App\Models\DifferentlyAbledProject::where('application_id', $application->id)->first();
            if ($project) {
                if (!empty($application->applicant_name)) $project->project_name = $application->applicant_name;
                if (!empty($application->agency_number)) $project->agency_project_no = $application->agency_number;
                $project->save();
            }
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($application) {
            if ($application->sponsor_status === 'Sponsored') {
                self::ensureProjectExists($application);
            }
        });

        static::updated(function ($application) {
            if ($application->sponsor_status === 'Sponsored') {
                self::ensureProjectExists($application);
            } else {
                $project = \App\Models\DifferentlyAbledProject::where('application_id', $application->id)->first();
                if ($project) {
                    $project->delete();
                }
            }
        });

        static::deleted(function ($application) {
            $project = \App\Models\DifferentlyAbledProject::where('application_id', $application->id)->first();
            if ($project) {
                $project->delete();
            }
        });
    }



    public $metaFields = [
        'amount_requested',
        'applicant_name',
        'father_name',
        'fathers_father',
        'mother_name',
        'gender',
        'aadhar_number',
        'dob',
        'age',
        'marital_status',
        'guardian_name',
        'guardian_relation',
        'male_members',
        'female_members',
        'total_members',
        'people_with_disabilities',
        'monthly_income',
        'monthly_cost',
        'income_source',
        'studying_institution',
        'not_studying_reason',
        'health_status',
        'disability_type',
        'disability_percentage',
        'disability_date',
        'disability_level',
        'other_help',
        'description',
        'accommodation',
        'details',
        'cluster_id',
        'agency_number',
        'recommendation_name',
        'recommendation_organization',
        'recommendation_organization_other',
        'recommendation_phone',
        'recommendation_position'
    ];
}
