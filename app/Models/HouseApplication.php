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
        'accommodation_details',
        'age',
        'building_area_sq',
        'children_female',
        'children_male',
        'children_total',
        'daily_treatment_explanation',
        'desired_model',
        'education',
        'expected_amount',
        'father_name',
        'gender',
        'has_occupation',
        'health_status',
        'house_type',
        'intended_house_form',
        'land_type',
        'legal_approvals_status',
        'married',
        'monthly_income',
        'mother_name',
        'num_children',
        'num_female_children',
        'num_male_children',
        'occupation',
        'office_build_house',
        'other_income',
        'own_place',
        'own_place_details',
        'permission',
    ];
}
