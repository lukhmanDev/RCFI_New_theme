<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCategoryMeta;

class GeneralApplication extends Model
{
    use HasCategoryMeta;

    protected $table = 'general_applications';
    protected $guarded = [];

    public $metaFields = [
        'application_type',
        'organization_name',
        'unit',
        'applicant_name',
        'age',
        'sex',
        'status_of_applicant',
        'education',
        'num_male_family',
        'num_female_family',
        'num_total_family',
        'num_earning_members',
        'average_monthly_income',
        'applying_for',
        'monthly_income_detail',
        'recommended_by',
        'recommended_phone',
        'office_application_type',
        'details',
        'amount_requested',
        'recommendation_name',
        'recommendation_organization',
        'recommendation_organization_other',
        'recommendation_phone',
        'recommendation_position'
    ];
}
