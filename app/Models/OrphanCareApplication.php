<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCategoryMeta;

class OrphanCareApplication extends Model
{
    use HasCategoryMeta;

    protected $table = 'orphan_care_applications';
    protected $guarded = [];

    public $metaFields = ['aadhar_number', 'age', 'district', 'dob', 'father_death_cause', 'father_death_date', 'father_name', 'gender', 'grandfather_name', 'guardian_name', 'guardian_relation', 'health_status', 'house_name', 'house_type', 'madrassa_class', 'madrassa_name', 'mobile_1', 'mobile_2', 'monthly_expense', 'monthly_income', 'mother_alive_status', 'mother_death_cause', 'mother_death_date', 'mother_name', 'mother_remarried_status', 'mothers_father_name', 'not_studying_reason', 'pin_code', 'place', 'post_office', 'school_class', 'school_name', 'siblings_female', 'siblings_male', 'siblings_total', 'sponsorship_details', 'state', 'town'];
}
