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
        return $this->belongsTo(OrphanCareProject::class, 'orphan_care_project_id');
    }
}
