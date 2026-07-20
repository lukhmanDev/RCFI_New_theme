<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicantAddress extends Model
{
    protected $table = 'applicant_addresses';
    protected $guarded = [];

    public function addressable()
    {
        return $this->morphTo();
    }
}
