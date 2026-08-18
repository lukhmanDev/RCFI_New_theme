<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCategoryMeta;

class DrinkingWaterGroupApplication extends Model
{
    use HasCategoryMeta;

    protected $table = 'drinking_water_group_applications';
    protected $guarded = [];

    public $metaFields = [
        'applicant_name',
        'father_name',
        'mother_name',
        'fathers_father',
        'gender',
        'dob',
        'age',
        'aadhar_number',
        'pin',
        'location',
        'village',
        'post',
        'panchayath',
        'district',
        'state',
        'contact_number_1',
        'contact_number_2',
        'male_adults',
        'male_children',
        'female_adults',
        'female_children',
        'beneficiaries',
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
        'legal_permissions',
        'amount_requested',
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
