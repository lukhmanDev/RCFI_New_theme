<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCategoryMeta;

class OrphanCareApplication extends Model
{
    use HasCategoryMeta;

    protected $table = 'orphan_care_applications';
    protected $guarded = [];
    protected $attributes = [
        'sponsor_status' => 'Not Sponsored',
    ];
    public $metaFields = [
        'applicant_name',
        'amount_requested',
        'status',
        'contact_email',
        'additional_note',
        'aadhar_number',
        'age',
        'district',
        'dob',
        'father_death_cause',
        'father_death_date',
        'father_name',
        'gender',
        'grandfather_name',
        'guardian_name',
        'guardian_relation',
        'health_status',
        'house_name',
        'house_type',
        'madrassa_class',
        'madrassa_name',
        'mobile_1',
        'mobile_2',
        'monthly_expense',
        'monthly_income',
        'mother_alive_status',
        'mother_death_cause',
        'mother_death_date',
        'mother_name',
        'mother_remarried_status',
        'mothers_father_name',
        'not_studying_reason',
        'pin_code',
        'place',
        'post_office',
        'school_class',
        'school_name',
        'siblings_female',
        'siblings_male',
        'siblings_total',
        'sponsorship_details',
        'state',
        'town',
        'cluster_id',
        'agency_number',
        'sponsor_status',
    ];

    public static function ensureProjectExists($application)
    {
        $projectExists = \App\Models\OrphanCareProject::where('application_id', $application->id)->exists();
        if (!$projectExists) {
            \App\Models\OrphanCareProject::create([
                'application_id' => $application->id,
                'project_name' => $application->applicant_name,
                'agency_project_no' => $application->agency_number,
                'type_of_project' => 'Orphan Care',
                'sponsor' => 'Sponsored',
                'stage' => 1,
                'status' => 'Pending',
            ]);
        } else {
            $project = \App\Models\OrphanCareProject::where('application_id', $application->id)->first();
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
                $project = \App\Models\OrphanCareProject::where('application_id', $application->id)->first();
                if ($project) {
                    $project->delete();
                }
            }
        });

        static::deleted(function ($application) {
            $project = \App\Models\OrphanCareProject::where('application_id', $application->id)->first();
            if ($project) {
                $project->delete();
            }
        });
    }

    public function cluster()
    {
        return $this->belongsTo(Cluster::class, 'cluster_id');
    }
}
