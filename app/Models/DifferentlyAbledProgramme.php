<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DifferentlyAbledProgramme extends Model
{
    protected $table = 'differently_abled_programmes';
    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(DifferentlyAbledProject::class, 'differently_abled_project_id');
    }
}
