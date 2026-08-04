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
        'amount_requested',
        'applicant_name',
        'gender',
        'father_name',
        'grandfather_name',
        'student_photo',
        'mother_name',
        'mothers_father_name',
        'dob',
        'age',
        'aadhar_number',
        'guardian_name',
        'guardian_relation',
        'father_death_date',
        'father_death_cause',
        'mother_alive_status',
        'mother_remarried_status',
        'mother_death_date',
        'mother_death_cause',
        'siblings_male',
        'siblings_female',
        'siblings_total',
        'current_beneficiaries',
        'monthly_income',
        'monthly_expense',
        'sponsorship_details',
        'house_type',
        'school_name',
        'school_class',
        'madrassa_name',
        'madrassa_class',
        'not_studying_reason',
        'health_status',
        'details',
        'recommender_name',
        'recommender_org',
        'recommender_phone',
        'recommender_position',
        'cluster_id',
        'agency_number',
        'agency_name',
        'application_date',
        'recommendation_name',
        'recommendation_organization',
        'recommendation_organization_other',
        'recommendation_phone',
        'recommendation_position'
    ];

    public static function ensureProjectExists($application)
    {
        $projectExists = \App\Models\OrphanCareProject::where('application_id', $application->id)->exists();
        if (!$projectExists) {
            $year = date('y');
            $idString = str_pad($application->id, 3, '0', STR_PAD_LEFT);
            $tempId = 'RCFI/' . $year . '-OC' . $idString;

            \App\Models\OrphanCareProject::create([
                'application_id' => $application->id,
                'project_name' => $application->applicant_name ?? 'Orphan Care Project',
                'agency_project_no' => $application->agency_number,
                'project_id' => $tempId,
                'type_of_project' => 'Orphan Care',
                'sponsor' => 'Sponsored',
                'stage' => 1,
                'status' => 'Active',
            ]);
        } else {
            $project = \App\Models\OrphanCareProject::where('application_id', $application->id)->first();
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
