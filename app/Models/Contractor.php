<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contractor extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'company_name',
        'address',
        'email'
    ];

    public function projectContractors()
    {
        return $this->hasMany(ProjectContractor::class, 'contractor_id');
    }
}
