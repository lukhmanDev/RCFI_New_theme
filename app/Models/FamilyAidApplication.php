<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCategoryMeta;

class FamilyAidApplication extends Model
{
    use HasCategoryMeta;

    protected $table = 'family_aid_applications';
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
        $projectExists = \App\Models\FamilyAidProject::where('application_id', $application->id)->exists();
        if (!$projectExists) {
            \App\Models\FamilyAidProject::create([
                'application_id' => $application->id,
                'project_name' => $application->applicant_name,
                'agency_project_no' => $application->agency_number,
                'type_of_project' => 'Family Aid',
                'sponsor' => 'Sponsored',
                'stage' => 1,
                'status' => 'Pending',
            ]);
        } else {
            $project = \App\Models\FamilyAidProject::where('application_id', $application->id)->first();
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
                $project = \App\Models\FamilyAidProject::where('application_id', $application->id)->first();
                if ($project) {
                    $project->delete();
                }
            }
        });

        static::deleted(function ($application) {
            $project = \App\Models\FamilyAidProject::where('application_id', $application->id)->first();
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
        'age',
        'children_female',
        'children_male',
        'children_total',
        'chronic_patients_description',
        'disability_status',
        'district',
        'dob',
        'father_name',
        'fathers_father',
        'health_status',
        'house_name',
        'location',
        'mobile_1',
        'mobile_2',
        'monthly_income',
        'mother_name',
        'nri_status',
        'occupation',
        'other_income_sources',
        'own_house_condition',
        'own_place_size',
        'own_place_status',
        'panchayat',
        'pin_code',
        'post_office',
        'residence_info',
        'routine_treatment_explanation',
        'sequel_status',
        'welfare_assistance_areas',
    ];
}
