<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCategoryMeta;

class ShopOtherApplication extends Model
{
    use HasCategoryMeta;

    protected $table = 'shop_other_applications';
    protected $guarded = [];

    public $metaFields = ['area', 'area_sqft', 'building_area_sq', 'committee_name', 'contact_number_1', 'contact_number_2', 'district', 'estimated_amount', 'families_in_mahallu', 'legal_approvals_status', 'locality_district', 'locality_location', 'locality_state', 'locality_village', 'mahallu_name', 'mobile', 'num_rooms', 'office_shop', 'panchayat', 'panchayath', 'permitted_type', 'place', 'post', 'project_area', 'reg_number', 'rooms', 'site_has_building', 'state', 'status_of_current_building', 'village', 'year'];
}
