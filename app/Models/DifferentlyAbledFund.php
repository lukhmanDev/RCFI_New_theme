<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DifferentlyAbledFund extends Model
{
    protected $table = 'differently_abled_funds';
    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(DifferentlyAbledProject::class, 'agency_project_no', 'agency_project_no');
    }

    public function donorModel()
    {
        return $this->belongsTo(Donor::class, 'donor', 'name');
    }
}
