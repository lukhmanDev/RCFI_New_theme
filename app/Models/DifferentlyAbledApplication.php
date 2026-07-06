<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCategoryMeta;

class DifferentlyAbledApplication extends Model
{
    use HasCategoryMeta;

    protected $table = 'differently_abled_applications';
    protected $guarded = [];

    public $metaFields = ['aadhar_number', 'accommodation', 'age', 'description', 'disability_date', 'disability_level', 'disability_percentage', 'disability_type', 'district', 'dob', 'father_name', 'fathers_father', 'female_members', 'gender', 'guardian_name', 'guardian_relation', 'health_status', 'house_name', 'income_source', 'male_members', 'marital_status', 'mobile', 'monthly_cost', 'monthly_income', 'mother_name', 'not_studying_reason', 'other_help', 'panchayat', 'people_with_disabilities', 'pincode', 'place', 'studying_institution', 'total_members'];
}
