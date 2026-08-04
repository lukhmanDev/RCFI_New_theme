<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCategoryMeta;

class EducationCenterApplication extends Model
{
    use HasCategoryMeta;

    protected $table = 'education_center_applications';
    protected $guarded = [];

    public $metaFields = [
        'applicant_name',
        'committee_name',
        'reg_number',
        'year',
        'pin_code',
        'location',
        'village',
        'post',
        'panchayath',
        'district',
        'state',
        'contact_number_1',
        'contact_number_2',
        'submitted_before',
        'received_support_before',
        'financial_support_purpose',
        'mahallu_name',
        'locality_pin_code',
        'locality_place',
        'locality_village',
        'locality_post',
        'locality_panchayath',
        'locality_district',
        'locality_state',
        'families_in_mahallu',
        'site_has_building',
        'status_of_current_building',
        'status_of_current_building_other',
        'students_boys',
        'students_girls',
        'total_students',
        'education_center_nearby',
        'syllabus',
        'distance_education_center',
        'project_type',
        'requirement',
        'building_area_sq',
        'land_area_sq',
        'num_classrooms',
        'num_students',
        'amount_requested',
        'legal_approvals_status',
        'permitted_type',
        'area',
        'details',
        'recommendation_name',
        'recommendation_organization',
        'recommendation_organization_other',
        'recommendation_phone',
        'recommendation_position'
    ];
}
