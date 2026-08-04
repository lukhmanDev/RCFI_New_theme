<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCategoryMeta;

class HospitalClinicApplication extends Model
{
    use HasCategoryMeta;

    protected $table = 'hospital_clinic_applications';
    protected $guarded = [];

    public $metaFields = [
        'applicant_name',
        'committee_name',
        'reg_number',
        'year',
        'pin_code',
        'place',
        'village',
        'post',
        'panchayath',
        'district',
        'state',
        'contact_number_1',
        'contact_number_2',
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
        'requirement',
        'building_area_sq',
        'area',
        'amount_requested',
        'is_pharmacy',
        'legal_approvals_status',
        'permitted_type',
        'project_area',
        'num_beds',
        'details',
        'recommendation_name',
        'recommendation_organization',
        'recommendation_organization_other',
        'recommendation_phone',
        'recommendation_position'
    ];
}
