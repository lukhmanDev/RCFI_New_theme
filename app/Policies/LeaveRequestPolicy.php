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
        $approverRoles = ['super_admin', 'coo', 'hod', 'project_manager', 'social_aid'];
        return !$user->is_suspended && (in_array($user->role, $approverRoles) || $user->isSuperAdmin() || $user->isCoo() || $user->isHod());
    }

    public function reject(User $user, LeaveRequest $leaveRequest): bool
    {
        $approverRoles = ['super_admin', 'coo', 'hod', 'project_manager', 'social_aid'];
        return !$user->is_suspended && (in_array($user->role, $approverRoles) || $user->isSuperAdmin() || $user->isCoo() || $user->isHod());
    }

    public function cancel(User $user, LeaveRequest $leaveRequest): bool
    {
        return $leaveRequest->user_id === $user->id && $leaveRequest->status === 'Pending';
    }
}
