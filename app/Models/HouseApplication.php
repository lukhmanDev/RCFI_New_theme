<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCategoryMeta;

class HouseApplication extends Model
{
    use HasCategoryMeta;

    protected $table = 'house_applications';
    protected $guarded = [];

    public $metaFields = ['accommodation_details', 'age', 'building_area_sq', 'children_female', 'children_male', 'children_total', 'contact_number_1', 'contact_number_2', 'daily_treatment_explanation', 'desired_model', 'district', 'education', 'expected_amount', 'father_name', 'gender', 'has_occupation', 'health_status', 'house_name', 'house_type', 'intended_house_form', 'land_type', 'legal_approvals_status', 'location', 'married', 'mobile_1', 'mobile_2', 'monthly_income', 'mother_name', 'num_children', 'num_female_children', 'num_male_children', 'occupation', 'office_build_house', 'other_income', 'own_place', 'own_place_details', 'panchayat', 'panchayath', 'permission', 'pin_code', 'place', 'post', 'post_office', 'state'];
}
