<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCategoryMeta;

class DrinkingWaterGroupApplication extends Model
{
    use HasCategoryMeta;

    protected $table = 'drinking_water_group_applications';
    protected $guarded = [];

    public $metaFields = ['aadhar_number', 'address', 'age', 'beneficiaries', 'contact_number_1', 'contact_number_2', 'district', 'dob', 'father_name', 'fathers_father', 'female_adults', 'female_children', 'gender', 'land_owner_address', 'land_owner_district', 'land_owner_mobile', 'land_owner_name', 'land_owner_panchayath', 'land_owner_place', 'land_owner_post', 'legal_permissions', 'location', 'male_adults', 'male_children', 'mother_name', 'num_benefited_people', 'panchayath', 'pin', 'post', 'state', 'village', 'well_depth', 'well_type'];

    protected $casts = [
        'beneficiaries' => 'array'
    ];
}
