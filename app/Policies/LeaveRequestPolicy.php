<?php

namespace App\Policies;

use App\Models\User;
use App\Models\LeaveRequest;

class LeaveRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->hasAdminAccess() || $leaveRequest->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return !$user->is_suspended;
    }

    public function approve(User $user, LeaveRequest $leaveRequest): bool
    {
        if ($user->is_suspended) {
            return false;
        }

        if ($user->isSuperAdmin() || $user->isCoo()) {
            return true;
        }

        if ($user->isHod()) {
            // HR-designated HOD can approve ALL leave requests
            if ($user->is_hr) {
                return true;
            }

            // Cannot self-approve own leave request
            if ($leaveRequest->user_id === $user->id) {
                return false;
            }

            // Regular HOD can approve if applicant's assigned HOD is this HOD
            if ($leaveRequest->user && $leaveRequest->user->assigned_hod_id === $user->id) {
                return true;
            }

            // Auto-escalation: if assigned HOD is currently on leave
            $assignedHod = $leaveRequest->user ? $leaveRequest->user->assignedHod : null;
            if ($assignedHod && $this->isUserOnLeave($assignedHod)) {
                return true;
            }
        }

        return false;
    }

    public function reject(User $user, LeaveRequest $leaveRequest): bool
    {
        return $this->approve($user, $leaveRequest);
    }

    private function isUserOnLeave(User $u): bool
    {
        $today = now()->format('Y-m-d');
        return $u->leaveRequests()
            ->where('status', 'Approved')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->exists();
    }

    public function cancel(User $user, LeaveRequest $leaveRequest): bool
    {
        return $leaveRequest->user_id === $user->id && $leaveRequest->status === 'Pending';
    }
}
