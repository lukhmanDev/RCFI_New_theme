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
            \App\Models\DifferentlyAbledProject::create([
                'application_id' => $application->id,
                'project_name' => $application->applicant_name,
                'agency_project_no' => $application->agency_number,
                'type_of_project' => 'Differently Abled',
                'sponsor' => 'Sponsored',
                'stage' => 1,
                'status' => 'Pending',
            ]);
        } else {
            $project = \App\Models\DifferentlyAbledProject::where('application_id', $application->id)->first();
            if ($project) {
                $project->project_name = $application->applicant_name;
                $project->agency_project_no = $application->agency_number;
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
        'applicant_name',
        'amount_requested',
        'status',
        'contact_email',
        'additional_note',
        'aadhar_number',
        'accommodation',
        'age',
        'description',
        'disability_date',
        'disability_level',
        'disability_percentage',
        'disability_type',
        'district',
        'dob',
        'father_name',
        'fathers_father',
        'female_members',
        'gender',
        'guardian_name',
        'guardian_relation',
        'health_status',
        'house_name',
        'income_source',
        'male_members',
        'marital_status',
        'mobile',
        'monthly_cost',
        'monthly_income',
        'mother_name',
        'not_studying_reason',
        'other_help',
        'panchayat',
        'people_with_disabilities',
        'pincode',
        'place',
        'studying_institution',
        'total_members',
    ];
}
