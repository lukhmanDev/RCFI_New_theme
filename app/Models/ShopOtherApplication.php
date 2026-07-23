<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCategoryMeta;

class ShopOtherApplication extends Model
{
    use HasCategoryMeta;

    protected $table = 'shop_other_applications';
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
        'area',
        'building_area_sq',
        'committee_name',
        'families_in_mahallu',
        'legal_approvals_status',
        'locality_district',
        'locality_location',
        'locality_state',
        'locality_village',
        'mahallu_name',
        'num_rooms',
        'office_shop',
        'permitted_type',
        'project_area',
        'reg_number',
        'rooms',
        'site_has_building',
        'status_of_current_building',
        'year',
    ];
}
