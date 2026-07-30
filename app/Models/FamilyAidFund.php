<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyAidFund extends Model
{
    protected $table = 'family_aid_funds';
    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(FamilyAidProject::class, 'agency_project_no', 'agency_project_no');
    }

    public function donorModel()
    {
        return $this->belongsTo(Donor::class, 'donor', 'name');
    }
}
