<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyAidProgramme extends Model
{
    protected $table = 'family_aid_programmes';
    protected $guarded = [];
    protected $casts = [
        'present_ticked' => 'boolean',
        'photo_ticked' => 'boolean',
        'marklist_ticked' => 'boolean',
        'thanks_letter_ticked' => 'boolean',
        'report_form_ticked' => 'boolean',
        'medical_certificate_ticked' => 'boolean',
        'other_document_ticked' => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(FamilyAidProject::class, 'family_aid_project_id');
    }
}
