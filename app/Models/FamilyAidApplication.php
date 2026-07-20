<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCategoryMeta;

class FamilyAidApplication extends Model
{
    use HasCategoryMeta;

    protected $table = 'family_aid_applications';
    protected $guarded = [];

    public $metaFields = [
        'applicant_name',
        'amount_requested',
        'status',
        'contact_email',
        'additional_note',
        'aadhar_number',
        'age',
        'children_female',
        'children_male',
        'children_total',
        'chronic_patients_description',
        'disability_status',
        'district',
        'dob',
        'father_name',
        'fathers_father',
        'health_status',
        'house_name',
        'location',
        'mobile_1',
        'mobile_2',
        'monthly_income',
        'mother_name',
        'nri_status',
        'occupation',
        'other_income_sources',
        'own_house_condition',
        'own_place_size',
        'own_place_status',
        'panchayat',
        'pin_code',
        'post_office',
        'residence_info',
        'routine_treatment_explanation',
        'sequel_status',
        'welfare_assistance_areas',
    ];
}
