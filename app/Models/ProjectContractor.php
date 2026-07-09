<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectContractor extends Model
{
    protected $fillable = [
        'project_id',
        'project_type',
        'contractor_id',
        'contractor_name',
        'phone',
        'company_name',
        'address',
        'type_of_contract',
        'purpose_of_contract'
    ];

    public function project()
    {
        return $this->morphTo();
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class, 'contractor_id');
    }
}
