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
        $project = \App\Models\DifferentlyAbledProject::where('application_id', $application->id)->first();
        if (!$project) {
            $year = date('y');
            $maxId = \App\Models\DifferentlyAbledProject::max('id') ?? 0;
            $nextSeq = $maxId + 1;
            $idString = str_pad($nextSeq, 3, '0', STR_PAD_LEFT);
            $tempId = 'RCFI/' . $year . '-DA' . $idString;

            while (\App\Models\DifferentlyAbledProject::where('project_id', $tempId)->exists()) {
                $nextSeq++;
                $idString = str_pad($nextSeq, 3, '0', STR_PAD_LEFT);
                $tempId = 'RCFI/' . $year . '-DA' . $idString;
            }

            \App\Models\DifferentlyAbledProject::create([
                'application_id'    => $application->id,
                'agency_project_no' => $application->agency_number,
                'project_id'        => $tempId,
                'type_of_project'   => 'Differently Abled',
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
        'agency_name',
        'application_date',
        'recommendation_name',
        'recommendation_organization',
        'recommendation_organization_other',
        'recommendation_phone',
        'recommendation_position',
        'whatsapp_number'
    ];
}
