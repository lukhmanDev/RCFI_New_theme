<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    protected $table = 'leave_balances';

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'year',
        'allocated_days',
        'used_days',
        'carried_forward_days',
    ];

    protected $casts = [
        'allocated_days'       => 'float',
        'used_days'            => 'float',
        'carried_forward_days' => 'float',
    ];

    protected $appends = ['accrued_days', 'balance_days'];

    public function getAccruedDaysAttribute()
    {
        $allocated = $this->allocated_days ?? 0;
        $type = $this->leaveType;

        if ($type) {
            // Sick Leave accrues 1 day for every 36 days in the year
            if ($type->leave_code === 'SL') {
                $year = $this->year ?? now()->year;
                if ($year < now()->year) {
                    $days = 365;
                } elseif ($year > now()->year) {
                    $days = 0;
                } else {
                    $days = now()->dayOfYear;
                }
                $accruedSL = floor($days / 36);
                return (float)min($allocated > 0 ? $allocated : 10.0, $accruedSL);
            }

            if ($type->accrual_type === 'Monthly') {
                $year = $this->year ?? now()->year;
                if ($year < now()->year) {
                    $months = 12;
                } elseif ($year > now()->year) {
                    $months = 0;
                } else {
                    $months = now()->month;
                }
                $monthlyRate = $allocated > 0 ? ($allocated / 12) : 1;
                return round(min($allocated, $monthlyRate * $months), 1);
            }
        }

        return $allocated;
    }

    public function getBalanceDaysAttribute()
    {
        $accrued = $this->accrued_days;
        return max(0, round(($accrued) + ($this->carried_forward_days ?? 0) - ($this->used_days ?? 0), 1));
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }
}
