<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveAccrualLog extends Model
{
    protected $table = 'leave_accrual_log';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'accrual_month',
        'accrual_year',
        'days_accrued',
        'accrued_on',
    ];

    protected $casts = [
        'accrued_on'   => 'datetime',
        'days_accrued' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }
}
