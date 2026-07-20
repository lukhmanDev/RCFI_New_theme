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
        'amount_requested',
        'status',
        'contact_email',
        'additional_note',
        'aadhar_number',
        'address',
        'age',
        'beneficiaries',
        'contact_number_1',
        'contact_number_2',
        'current_water_source',
        'district',
        'dob',
        'father_name',
        'fathers_father',
        'gender',
        'job',
        'land_nature',
        'land_owner_address',
        'land_owner_district',
        'land_owner_mobile',
        'land_owner_name',
        'land_owner_panchayath',
        'land_owner_place',
        'land_owner_post',
        'legal_permissions',
        'location',
        'monthly_income',
        'mother_name',
        'name',
        'need_pump',
        'num_benefited_people',
        'num_female_benefited',
        'num_male_benefited',
        'panchayath',
        'phone',
        'pin',
        'post',
        'state',
        'village',
        'well_depth',
        'well_diameter',
        'well_for_agriculture',
        'well_type',
    ];

    protected $casts = [
        'beneficiaries' => 'array'
    ];
}
