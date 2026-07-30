<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrphanCareFund extends Model
{
    protected $table = 'orphan_care_funds';
    protected $guarded = [];
    protected $casts = [
        'date' => 'date',
        'amount' => 'float',
    ];

    public function project()
    {
        return $this->belongsTo(OrphanCareProject::class, 'agency_project_no', 'agency_project_no');
    }

    public function donorModel()
    {
        return $this->belongsTo(Donor::class, 'donor', 'name');
    }
}
