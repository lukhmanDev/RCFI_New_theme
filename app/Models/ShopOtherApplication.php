<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCategoryMeta;

class ShopOtherApplication extends Model
{
    use HasCategoryMeta;

    protected $table = 'shop_other_applications';
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
        'site_has_building',
        'status_of_current_building',
        'status_of_current_building_other',
        'building_area_sq',
        'area',
        'amount_requested',
        'families_in_mahallu',
        'legal_approvals_status',
        'permitted_type',
        'project_area',
        'num_rooms',
        'office_shop',
        'details',
        'recommendation_name',
        'recommendation_organization',
        'recommendation_organization_other',
        'recommendation_phone',
        'recommendation_position'
    ];
}
