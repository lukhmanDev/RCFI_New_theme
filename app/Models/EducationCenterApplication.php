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
        'amount_requested',
        'status',
        'contact_email',
        'additional_note',
        'house_name',
        'place',
        'post_office',
        'village',
        'panchayat',
        'district',
        'state',
        'pin_code',
        'location',
        'contact_number_1',
        'contact_number_2',
        'area',
        'building_area_sq',
        'committee_name',
        'distance_education_center',
        'education_center_nearby',
        'families_in_mahallu',
        'land_area_sq',
        'legal_approvals_status',
        'locality_district',
        'locality_location',
        'locality_state',
        'locality_village',
        'mahallu_name',
        'num_classrooms',
        'num_students',
        'project_type',
        'received_support_before',
        'reg_number',
        'requirement',
        'rooms',
        'site_has_building',
        'status_of_current_building',
        'students_boys',
        'students_girls',
        'submitted_before',
        'syllabus',
        'year',
    ];
}
