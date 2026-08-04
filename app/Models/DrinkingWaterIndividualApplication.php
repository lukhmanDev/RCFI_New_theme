<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCategoryMeta;

class DrinkingWaterIndividualApplication extends Model
{
    use HasCategoryMeta;

    protected $table = 'drinking_water_individual_applications';
    protected $guarded = [];

    public $metaFields = [
        'applicant_name',
        'pin',
        'location',
        'address',
        'village',
        'post',
        'panchayath',
        'district',
        'state',
        'contact_number_1',
        'contact_number_2',
        'job',
        'monthly_income',
        'num_male_benefited',
        'num_female_benefited',
        'num_benefited_people',
        'land_owner_name',
        'land_owner_address',
        'land_owner_place',
        'land_owner_post',
        'land_owner_panchayath',
        'land_owner_district',
        'land_owner_mobile',
        'well_type',
        'well_depth',
        'well_diameter',
        'amount_requested',
        'land_nature',
        'current_water_source',
        'need_pump',
        'well_for_agriculture',
        'details',
        'recommendation_name',
        'recommendation_organization',
        'recommendation_organization_other',
        'recommendation_phone',
        'recommendation_position'
    ];

    protected $casts = [
        'beneficiaries' => 'array'
    ];
}
