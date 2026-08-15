<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $table = 'leave_types';

    protected $fillable = [
        'leave_code',
        'leave_name',
        'description',
        'accrual_type',
        'max_days_per_year',
        'max_days_lifetime',
        'carry_forward',
        'applicable_gender',
        'requires_marital_status',
        'min_service_years',
        'is_lifetime_only',
        'is_active',
    ];

    protected $casts = [
        'max_days_per_year' => 'float',
        'max_days_lifetime' => 'float',
        'carry_forward'     => 'boolean',
        'is_lifetime_only'  => 'boolean',
        'is_active'         => 'boolean',
    ];

    public function getCodeAttribute()
    {
        return $this->leave_code;
    }

    public function getNameAttribute()
    {
        return $this->leave_name;
    }

    public function isUnlimited(): bool
    {
        return $this->leave_code === 'LWP' || $this->accrual_type === 'None';
    }

    public function balances()
    {
        return $this->hasMany(LeaveBalance::class, 'leave_type_id');
    }

    public function requests()
    {
        return $this->hasMany(LeaveRequest::class, 'leave_type_id');
    }

    public function getIneligibilityReason(User $user): string
    {
        if ($this->leave_code === 'OL' || $this->leave_code === 'OTHER') {
            return "Only HOD or Admin can add Other Leave";
        }

        if ($this->applicable_gender !== 'All' && strtolower($user->gender ?? '') !== strtolower($this->applicable_gender)) {
            return "Applies to {$this->applicable_gender} staff only";
        }

        if ($this->requires_marital_status !== 'Any' && strtolower($user->marital_status ?? '') !== strtolower($this->requires_marital_status)) {
            return "Requires {$this->requires_marital_status} status";
        }

        if ($this->min_service_years > 0) {
            $years = $user->date_of_joining ? \Carbon\Carbon::parse($user->date_of_joining)->diffInYears(now()) : 0;
            if ($years < $this->min_service_years) {
                return "Requires {$this->min_service_years}+ years of service";
            }
        }

        if ($this->is_lifetime_only) {
            $usedDays = LeaveBalance::where('user_id', $user->id)
                ->where('leave_type_id', $this->id)
                ->sum('used_days');
            if ($usedDays > 0) {
                return "Lifetime allowance already used";
            }
        }

        return "Ineligible";
    }

    public function getAccruedEntitlementToDate($year = null)
    {
        $allocated = $this->max_days_per_year ?? $this->max_days_lifetime ?? 0;

        if ($this->leave_code === 'SL') {
            $yearVal = $year ?? now()->year;
            if ($yearVal < now()->year) {
                $days = 365;
            } elseif ($yearVal > now()->year) {
                $days = 0;
            } else {
                $days = now()->dayOfYear;
            }
            $accruedSL = floor($days / 36);
            return (float)min($allocated > 0 ? $allocated : 10.0, $accruedSL);
        }

        if ($this->accrual_type === 'Monthly') {
            $yearVal = $year ?? now()->year;
            if ($yearVal < now()->year) {
                $months = 12;
            } elseif ($yearVal > now()->year) {
                $months = 0;
            } else {
                $months = now()->month;
            }
            $rate = $allocated > 0 ? ($allocated / 12) : 1;
            return round(min($allocated, $rate * $months), 1);
        }
        return $allocated;
    }
}
