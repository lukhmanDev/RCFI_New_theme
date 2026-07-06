<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCategoryMeta;

class CulturalCenterApplication extends Model
{
    use HasCategoryMeta;

    protected $table = 'cultural_center_applications';
    protected $guarded = [];

    public $metaFields = ['area', 'area_sqft', 'benefited_households', 'building_area_sq', 'committee_name', 'contact_number_1', 'contact_number_2', 'cultural_center_nearby', 'distance_cultural_centre', 'district', 'estimated_amount', 'families_in_mahallu', 'land_area_sq', 'legal_approvals_status', 'locality_district', 'locality_location', 'locality_state', 'locality_village', 'location', 'mahallu_name', 'mobile', 'num_beneficiaries', 'panchayat', 'panchayath', 'place', 'post', 'project_type', 'received_support_before', 'reg_number', 'requirement', 'rooms', 'site_has_building', 'state', 'status_of_current_building', 'submitted_before', 'village', 'year'];
}
