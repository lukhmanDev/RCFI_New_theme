<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCategoryMeta;

class GeneralApplication extends Model
{
    use HasCategoryMeta;

    protected $table = 'general_applications';
    protected $guarded = [];

    public $metaFields = ['accommodation_details', 'address', 'age', 'applying_for', 'average_monthly_income', 'block', 'contact_number_1', 'contact_number_2', 'district', 'education', 'expected_amount', 'father_name', 'female_family_members', 'general_app_status', 'health_status', 'house_name', 'location', 'male_family_members', 'married', 'mobile_1', 'mobile_2', 'monthly_income', 'monthly_income_detail', 'mother_name', 'num_earning_members', 'num_female_family', 'num_male_family', 'num_total_family', 'occupation', 'office_app_type', 'office_application_type', 'other_income', 'panchayat', 'panchayat_municipality_corporation', 'pin', 'pin_code', 'post', 'post_office', 'recommended_by', 'recommended_phone', 'sex', 'state', 'status_of_applicant', 'total_family_members', 'village', 'ward'];
}
