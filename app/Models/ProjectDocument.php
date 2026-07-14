<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectDocument extends Model
{
    protected $fillable = [
        'project_id',
        'project_type',
        
        'land_document',
        'land_document_ticked_at',
        'possession_certificate',
        'possession_certificate_ticked_at',
        'recommendation_letter',
        'recommendation_letter_ticked_at',
        'committee_minutes',
        'committee_minutes_ticked_at',
        'permit_copy',
        'permit_copy_ticked_at',
        'plan',
        'plan_ticked_at',
        'tender_schedule_sheet',
        'tender_schedule_sheet_ticked_at',
        'site_study',
        'site_study_ticked_at',
        'quotations',
        'quotations_ticked_at',
        'quotations_approval_form',
        'quotations_approval_form_ticked_at',
        'work_order_letter',
        'work_order_letter_ticked_at',
        'meeting_minutes_copy',
        'meeting_minutes_copy_ticked_at',
        'agreement_with_contractor',
        'agreement_with_contractor_ticked_at',
        'agreement_with_committee',
        'agreement_with_committee_ticked_at',
        'project_summary_form',
        'project_summary_form_ticked_at',
        
        'completion_certificate',
        'completion_certificate_ticked_at',
        'measurement_book',
        'measurement_book_ticked_at',
        'location_map_link',
    ];

    protected $casts = [
        'land_document_ticked_at' => 'datetime',
        'possession_certificate_ticked_at' => 'datetime',
        'recommendation_letter_ticked_at' => 'datetime',
        'committee_minutes_ticked_at' => 'datetime',
        'permit_copy_ticked_at' => 'datetime',
        'plan_ticked_at' => 'datetime',
        'tender_schedule_sheet_ticked_at' => 'datetime',
        'site_study_ticked_at' => 'datetime',
        'quotations_ticked_at' => 'datetime',
        'quotations_approval_form_ticked_at' => 'datetime',
        'work_order_letter_ticked_at' => 'datetime',
        'meeting_minutes_copy_ticked_at' => 'datetime',
        'agreement_with_contractor_ticked_at' => 'datetime',
        'agreement_with_committee_ticked_at' => 'datetime',
        'project_summary_form_ticked_at' => 'datetime',
        
        'completion_certificate_ticked_at' => 'datetime',
        'measurement_book_ticked_at' => 'datetime',
    ];

    public static $docColumnMap = [
        'Land document' => 'land_document',
        'Copy of the land document ( well site)' => 'land_document',
        'Possession certificate' => 'possession_certificate',
        'Agreement with beneficiary' => 'possession_certificate',
        'Copy of the land document ( water tank site)' => 'possession_certificate',
        'Recommendation letter' => 'recommendation_letter',
        'Committee minutes' => 'committee_minutes',
        'Permit copy' => 'permit_copy',
        'No objection certificate of both land owners for implementing community drinking water project in their land' => 'permit_copy',
        'Plan' => 'plan',
        'Tender schedule sheet' => 'tender_schedule_sheet',
        'Site study' => 'site_study',
        'Site study report' => 'site_study',
        'Quotations' => 'quotations',
        '3 Quotations of the well, tank, pump house and house connection works' => 'quotations',
        'Quotations approval form' => 'quotations_approval_form',
        'Work order letter' => 'work_order_letter',
        'Meeting minutes copy' => 'meeting_minutes_copy',
        'Agreement with contractor' => 'agreement_with_contractor',
        'Agreement with committee' => 'agreement_with_committee',
        'Project summary form' => 'project_summary_form',
        'Completion Certificate' => 'completion_certificate',
        'Measurement Book' => 'measurement_book',
        'Consumption sheet for payment' => 'measurement_book',
    ];

    public function project()
    {
        return $this->morphTo();
    }
}
