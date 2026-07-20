<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCategoryMeta;

class CulturalCenterApplication extends Model
{
    use HasCategoryMeta;

    protected $table = 'cultural_center_applications';
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
        'area_sqft',
        'benefited_households',
        'building_area_sq',
        'committee_name',
        'cultural_center_nearby',
        'distance_cultural_centre',
        'families_in_mahallu',
        'land_area_sq',
        'legal_approvals_status',
        'locality_district',
        'locality_location',
        'locality_state',
        'locality_village',
        'mahallu_name',
        'num_beneficiaries',
        'project_type',
        'received_support_before',
        'reg_number',
        'requirement',
        'rooms',
        'site_has_building',
        'status_of_current_building',
        'submitted_before',
        'year',
    ];
}
