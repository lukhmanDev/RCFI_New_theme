<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectCompletionDetail extends Model
{
    protected $table = 'project_completion_details';

    protected $fillable = [
        'project_id',
        'project_type',
        'total_project_cost',
        'total_amount',
        'amount_paid_by_donor',
        'community_contribution',
        'any_other',
        'deductions',
        'handover_date',
        'handover_remarks',
    ];

    /**
     * Get the owning project model.
     */
    public function project()
    {
        return $this->morphTo();
    }
}
