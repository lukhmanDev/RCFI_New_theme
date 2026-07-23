<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyAidFund extends Model
{
    protected $table = 'family_aid_funds';
    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(FamilyAidProject::class, 'family_aid_project_id');
    }
}
