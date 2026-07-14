<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectExpense extends Model
{
    protected $fillable = [
        'project_id',
        'project_type',
        'material_index',
        'comm_index',
        'expense_name',
        'quantity',
        'amount',
        'type'
    ];

    public function project()
    {
        return $this->morphTo();
    }
}
