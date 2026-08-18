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
        $project = \App\Models\FamilyAidProject::where('application_id', $application->id)->first();
        if (!$project) {
            $year = date('y');
            $maxId = \App\Models\FamilyAidProject::max('id') ?? 0;
            $nextSeq = $maxId + 1;
            $idString = str_pad($nextSeq, 3, '0', STR_PAD_LEFT);
            $tempId = 'RCFI/' . $year . '-FA' . $idString;

            while (\App\Models\FamilyAidProject::where('project_id', $tempId)->exists()) {
                $nextSeq++;
                $idString = str_pad($nextSeq, 3, '0', STR_PAD_LEFT);
                $tempId = 'RCFI/' . $year . '-FA' . $idString;
            }

            \App\Models\FamilyAidProject::create([
                'application_id'    => $application->id,
                'agency_project_no' => $application->agency_number,
                'project_id'        => $tempId,
                'type_of_project'   => 'Family Aid',
                'stage'             => 1,
                'status'            => 'Active',
            ]);
        } else {
            if (!empty($application->agency_number)) {
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
        'amount_requested',
        'applicant_name',
        'father_name',
        'mother_name',
        'fathers_father',
        'dob',
        'age',
        'aadhar_number',
        'house_name',
        'pin_code',
        'place',
        'village',
        'post_office',
        'panchayat',
        'district',
        'state',
        'mobile_1',
        'mobile_2',
        'whatsapp_number',
        'children_total',
        'children_male',
        'children_female',
        'nri_status',
        'occupation',
        'monthly_income',
        'other_income_sources',
        'health_status',
        'disability_status',
        'routine_treatment_explanation',
        'chronic_patients_description',
        'residence_info',
        'own_house_condition',
        'own_place_status',
        'own_place_size',
        'sequel_status',
        'welfare_assistance_areas',
        'details',
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
}
