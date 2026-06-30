<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalClinicApplication extends Model
{
    protected $table = 'hospital_clinic_applications';
    protected $guarded = [];

    protected $casts = [
        'meta' => 'array'
    ];
}
