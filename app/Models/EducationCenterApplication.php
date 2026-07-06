<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCategoryMeta;

class EducationCenterApplication extends Model
{
    use HasCategoryMeta;

    protected $table = 'education_center_applications';
    protected $guarded = [];

    public $metaFields = ['area', 'area_sqft', 'building_area_sq', 'committee_name', 'contact_number_1', 'contact_number_2', 'distance_cultural_centre', 'district', 'education_center_nearby', 'estimated_amount', 'families_in_mahallu', 'land_area_sq', 'legal_approvals_status', 'locality_district', 'locality_location', 'locality_state', 'locality_village', 'location', 'mahallu_name', 'mobile', 'num_classrooms', 'num_students', 'panchayat', 'panchayath', 'place', 'post', 'project_type', 'received_support_before', 'reg_number', 'requirement', 'rooms', 'site_has_building', 'state', 'status_of_current_building', 'students_boys', 'students_girls', 'submitted_before', 'syllabus', 'village', 'year'];
}
