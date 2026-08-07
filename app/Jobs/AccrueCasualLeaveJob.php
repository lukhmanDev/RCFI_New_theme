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

class AccrueCasualLeaveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $clType = LeaveType::where('leave_code', 'CL')->first();
        if (!$clType || !$clType->is_active) {
            return;
        }

        $now = Carbon::now();
        $year = $now->year;
        $month = $now->month;

        $users = User::where('is_suspended', false)->get();

        foreach ($users as $user) {
            if (!$user->isEligibleFor($clType)) {
                continue;
            }

            // Idempotency check via leave_accrual_log
            $alreadyAccrued = LeaveAccrualLog::where('user_id', $user->id)
                ->where('leave_type_id', $clType->id)
                ->where('accrual_year', $year)
                ->where('accrual_month', $month)
                ->exists();

            if ($alreadyAccrued) {
                continue;
            }

            // Accrue 1.0 day to LeaveBalance
            $balance = LeaveBalance::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'leave_type_id' => $clType->id,
                    'year' => $year,
                ],
                [
                    'allocated_days' => 0,
                    'used_days' => 0,
                    'carried_forward_days' => 0,
                ]
            );

            $balance->increment('allocated_days', 1.0);

            // Log accrual
            LeaveAccrualLog::create([
                'user_id' => $user->id,
                'leave_type_id' => $clType->id,
                'accrual_month' => $month,
                'accrual_year' => $year,
                'days_accrued' => 1.0,
                'accrued_on' => now(),
            ]);
        }
    }
}
