<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Models\LeaveAccrualLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class AllocateAnnualLeaveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $now = Carbon::now();
        $year = $now->year;

        $annualTypes = LeaveType::where('accrual_type', 'Annual')->where('is_active', true)->get();
        $users = User::where('is_suspended', false)->get();

        foreach ($annualTypes as $type) {
            $daysToAllocate = $type->max_days_per_year ?? 10.0;

            foreach ($users as $user) {
                if (!$user->isEligibleFor($type)) {
                    continue;
                }

                // Idempotency check via leave_accrual_log (month = null for annual allocations)
                $alreadyAccrued = LeaveAccrualLog::where('user_id', $user->id)
                    ->where('leave_type_id', $type->id)
                    ->where('accrual_year', $year)
                    ->whereNull('accrual_month')
                    ->exists();

                if ($alreadyAccrued) {
                    continue;
                }

                // Allocate Annual Days
                $balance = LeaveBalance::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'leave_type_id' => $type->id,
                        'year' => $year,
                    ],
                    [
                        'allocated_days' => 0,
                        'used_days' => 0,
                        'carried_forward_days' => 0,
                    ]
                );

                $balance->update(['allocated_days' => $daysToAllocate]);

                // Log annual allocation
                LeaveAccrualLog::create([
                    'user_id' => $user->id,
                    'leave_type_id' => $type->id,
                    'accrual_month' => null,
                    'accrual_year' => $year,
                    'days_accrued' => $daysToAllocate,
                    'accrued_on' => now(),
                ]);
            }
        }
    }
}
