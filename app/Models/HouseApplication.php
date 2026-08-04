<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCategoryMeta;

class HouseApplication extends Model
{
    use HasCategoryMeta;

    protected $table = 'house_applications';
    protected $guarded = [];

    public $metaFields = [
        'applicant_name',
        'age',
        'father_name',
        'mother_name',
        'house_name',
        'pin_code',
        'place',
        'village',
        'post',
        'panchayath',
        'district',
        'state',
        'gender',
        'contact_number_1',
        'contact_number_2',
        'education',
        'married',
        'num_children',
        'num_male_children',
        'num_female_children',
        'has_occupation',
        'monthly_income',
        'other_income',
        'health_status',
        'daily_treatment_explanation',
        'accommodation_details',
        'own_place',
        'own_place_details',
        'land_type',
        'desired_model',
        'building_area_sq',
        'amount_requested',
        'legal_approvals_status',
        'intended_house_form',
        'office_build_house',
        'details',
        'recommendation_name',
        'recommendation_organization',
        'recommendation_organization_other',
        'recommendation_phone',
        'recommendation_position'
    ];
}
