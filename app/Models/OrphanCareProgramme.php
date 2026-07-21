<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrphanCareProgramme extends Model
{
    protected $table = 'orphan_care_programmes';
    protected $guarded = [];
    protected $casts = [
        'present_ticked' => 'boolean',
        'photo_ticked' => 'boolean',
        'marklist_ticked' => 'boolean',
        'thanks_letter_ticked' => 'boolean',
        'report_form_ticked' => 'boolean',
        'other_document_ticked' => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(OrphanCareProject::class, 'orphan_care_project_id');
    }
}
