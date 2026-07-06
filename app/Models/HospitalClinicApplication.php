<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCategoryMeta;

class HospitalClinicApplication extends Model
{
    use HasCategoryMeta;

    protected $table = 'hospital_clinic_applications';
    protected $guarded = [];

    public $metaFields = ['area', 'area_sqft', 'building_area_sq', 'committee_name', 'contact_number_1', 'contact_number_2', 'district', 'estimated_amount', 'families_in_mahallu', 'is_pharmacy', 'legal_approvals_status', 'locality_location', 'locality_state', 'locality_village', 'mahallu_name', 'mobile', 'num_beds', 'panchayat', 'panchayath', 'permitted_type', 'place', 'post', 'project_area', 'reg_number', 'requirement', 'rooms', 'site_has_building', 'state', 'village', 'year'];
}
