<?php

namespace App\Services;

use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Events\LeaveRequestSubmitted;
use App\Events\LeaveRequestApproved;
use App\Events\LeaveRequestRejected;
use App\Events\LeaveRequestCancelled;
use App\Events\LeaveBalanceUpdated;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class LeaveService
{
    public function checkEligibility(User $user, LeaveType $type): bool
    {
        return $user->isEligibleFor($type);
    }

    public function submitRequest(User $user, LeaveType $type, $start, $end, ?string $reason): LeaveRequest
    {
        if ($user->is_suspended) {
            throw ValidationException::withMessages([
                'user' => ['Suspended users are not permitted to submit leave requests.'],
            ]);
        }

        if (!$this->checkEligibility($user, $type)) {
            throw ValidationException::withMessages([
                'leave_type_id' => ['You are not eligible for this type of leave.'],
            ]);
        }

        $startDate = Carbon::parse($start)->startOfDay();
        $endDate = Carbon::parse($end)->startOfDay();

        if ($endDate->lt($startDate)) {
            throw ValidationException::withMessages([
                'end_date' => ['End date must be on or after the start date.'],
            ]);
        }

        // Calculate total days (excluding weekends for standard calculation)
        $totalDays = $startDate->diffInDaysFiltered(function (Carbon $date) {
            return !$date->isWeekend();
        }, $endDate->copy()->addDay());

        if ($totalDays <= 0) {
            $totalDays = $startDate->diffInDays($endDate) + 1;
        }

        // Check for overlapping pending/approved leave requests
        $overlapExists = LeaveRequest::where('user_id', $user->id)
            ->whereIn('status', ['Pending', 'Approved'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate->format('Y-m-d'))
                          ->where('end_date', '>=', $endDate->format('Y-m-d'));
                    });
            })->exists();

        if ($overlapExists) {
            throw ValidationException::withMessages([
                'dates' => ['You already have a pending or approved leave request during these dates.'],
            ]);
        }

        // Fetch or initialize balance record
        $year = $type->accrual_type === 'OneTime' ? null : $startDate->year;
        $balance = LeaveBalance::firstOrCreate(
            [
                'user_id' => $user->id,
                'leave_type_id' => $type->id,
                'year' => $year,
            ],
            [
                'allocated_days' => $type->max_days_per_year ?? $type->max_days_lifetime ?? 0,
                'used_days' => 0,
                'carried_forward_days' => 0,
            ]
        );

        if ($balance->balance_days < $totalDays) {
            throw ValidationException::withMessages([
                'total_days' => ["Insufficient leave balance. You have {$balance->balance_days} day(s) available, but requested {$totalDays} day(s)."],
            ]);
        }

        $leaveRequest = LeaveRequest::create([
            'user_id' => $user->id,
            'leave_type_id' => $type->id,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'total_days' => $totalDays,
            'reason' => $reason,
            'status' => 'Pending',
            'applied_on' => now(),
        ]);

        event(new LeaveRequestSubmitted($leaveRequest));

        return $leaveRequest;
    }

    public function approve(LeaveRequest $request, User $approver): void
    {
        if ($request->status !== 'Pending') {
            throw ValidationException::withMessages([
                'status' => ['Only pending leave requests can be approved.'],
            ]);
        }

        $request->update([
            'status' => 'Approved',
            'approved_by' => $approver->id,
            'approved_on' => now(),
        ]);

        $year = $request->leaveType->accrual_type === 'OneTime' ? null : Carbon::parse($request->start_date)->year;

        $balance = LeaveBalance::firstOrCreate(
            [
                'user_id' => $request->user_id,
                'leave_type_id' => $request->leave_type_id,
                'year' => $year,
            ],
            [
                'allocated_days' => $request->leaveType->max_days_per_year ?? $request->leaveType->max_days_lifetime ?? 0,
                'used_days' => 0,
                'carried_forward_days' => 0,
            ]
        );

        $balance->increment('used_days', $request->total_days);
        $balance->refresh();

        event(new LeaveRequestApproved($request));
        event(new LeaveBalanceUpdated($balance));
    }

    public function reject(LeaveRequest $request, User $approver, string $remarks): void
    {
        if ($request->status !== 'Pending') {
            throw ValidationException::withMessages([
                'status' => ['Only pending leave requests can be rejected.'],
            ]);
        }

        $request->update([
            'status' => 'Rejected',
            'approved_by' => $approver->id,
            'approved_on' => now(),
            'remarks' => $remarks,
        ]);

        event(new LeaveRequestRejected($request));
    }

    public function cancel(LeaveRequest $request): void
    {
        if ($request->status !== 'Pending') {
            throw ValidationException::withMessages([
                'status' => ['Only pending leave requests can be cancelled.'],
            ]);
        }

        $request->update([
            'status' => 'Cancelled',
        ]);

        event(new LeaveRequestCancelled($request));
    }
}
