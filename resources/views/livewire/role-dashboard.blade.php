<div style="display: flex; flex-direction: column; gap: 1.75rem;">

    <!-- Welcome Header & Quick Actions -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);">
        <div>
            <h1 style="color: #0f172a; font-size: 1.65rem; font-weight: 800; margin: 0;">Welcome, {{ $user->name }}!</h1>
            <p style="color: #64748b; font-size: 0.88rem; margin-top: 0.25rem; margin-bottom: 0;">
                Role assigned: <strong style="color: #10b981;">{{ $user->role_name }}</strong>
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <button type="button" onclick="openLeaveRequestModal()" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #ffffff; border: none; border-radius: 10px; padding: 0.65rem 1.25rem; font-weight: 700; font-size: 0.88rem; cursor: pointer; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25); display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="bx bx-calendar-event" style="font-size: 1.15rem;"></i> Request Leave
            </button>
            <a href="{{ route('leave.portal') }}" style="background: #ffffff; border: 1px solid #cbd5e1; color: #334155; border-radius: 10px; padding: 0.65rem 1.15rem; font-weight: 700; font-size: 0.88rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="bx bx-list-ul"></i> Leave Portal
            </a>
        </div>
    </div>

    <!-- Real-Time Metrics & Stat Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1.25rem;">
        <!-- Total Applications -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02); display: flex; align-items: center; gap: 1rem;">
            <div style="background: #eff6ff; color: #3b82f6; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="bx bx-file"></i>
            </div>
            <div>
                <span style="color: #64748b; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em;">Applications</span>
                @if($newApplicationsCount > 0)
                    <span style="background: #ef4444; color: #ffffff; padding: 0.15rem 0.55rem; border-radius: 12px; font-size: 0.72rem; font-weight: 800; margin-left: 0.35rem; display: inline-block;">+{{ $newApplicationsCount }} New</span>
                @endif
                <h2 style="color: #0f172a; font-size: 1.65rem; font-weight: 800; margin: 0.1rem 0;">{{ $totalApplications }}</h2>
                <span style="color: #3b82f6; font-size: 0.75rem; font-weight: 600;">Total Submissions</span>
            </div>
        </div>

        <!-- Pending Approvals -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02); display: flex; align-items: center; gap: 1rem;">
            <div style="background: #fff7ed; color: #f97316; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="bx bx-time-five"></i>
            </div>
            <div>
                <span style="color: #64748b; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em;">Pending Review</span>
                <h2 style="color: #0f172a; font-size: 1.65rem; font-weight: 800; margin: 0.1rem 0;">{{ $pendingCount }}</h2>
                <span style="color: #f97316; font-size: 0.75rem; font-weight: 600;">Awaiting Action</span>
            </div>
        </div>

        <!-- Approved Applications -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02); display: flex; align-items: center; gap: 1rem;">
            <div style="background: #ecfdf5; color: #10b981; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="bx bx-check-circle"></i>
            </div>
            <div>
                <span style="color: #64748b; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em;">Approved</span>
                <h2 style="color: #0f172a; font-size: 1.65rem; font-weight: 800; margin: 0.1rem 0;">{{ $approvedCount }}</h2>
                <span style="color: #10b981; font-size: 0.75rem; font-weight: 600;">Verified Success</span>
            </div>
        </div>

        <!-- Projects Metrics -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02); display: flex; align-items: center; gap: 1rem;">
            <div style="background: #f3e8ff; color: #8b5cf6; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="bx bx-folder"></i>
            </div>
            <div>
                <span style="color: #64748b; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em;">Running Projects</span>
                <h2 style="color: #0f172a; font-size: 1.65rem; font-weight: 800; margin: 0.1rem 0;">{{ $runningProjects }}</h2>
                <span style="color: #8b5cf6; font-size: 0.75rem; font-weight: 600;">Active Pipeline</span>
            </div>
        </div>
    </div>

    <!-- Live Leave Balances & Status -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h2 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 0.4rem;">
                <i class="bx bx-wallet" style="color: #10b981;"></i> My Leave Balances & Status
            </h2>
            <span style="{{ $currentLeave['badge_style'] }} padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.78rem; font-weight: 700;">
                Today's Status: {{ $currentLeave['status'] }} {{ $currentLeave['dates'] ? '('.$currentLeave['dates'].')' : '' }}
            </span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            @forelse($myLeaveBalances as $bal)
                @php $type = $bal->leaveType; @endphp
                @if($type)
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                            <span style="font-size: 0.75rem; font-weight: 800; color: #64748b;">{{ $type->leave_code }}</span>
                            <span style="font-size: 0.7rem; background: #ecfdf5; color: #059669; padding: 0.1rem 0.45rem; border-radius: 8px; font-weight: 700;">{{ $type->accrual_type }}</span>
                        </div>
                        <div style="font-weight: 700; color: #0f172a; font-size: 0.9rem; margin-bottom: 0.5rem;">{{ $type->leave_name }}</div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-end; border-top: 1px solid #e2e8f0; padding-top: 0.5rem;">
                            <div>
                                <span style="font-size: 0.68rem; color: #94a3b8; font-weight: 600; display: block;">Available</span>
                                <span style="font-size: 1.25rem; font-weight: 800; color: #10b981;">{{ $bal->balance_days }} <span style="font-size: 0.7rem; color: #64748b;">days</span></span>
                            </div>
                            <div style="text-align: right; font-size: 0.78rem; font-weight: 700; color: #475569;">
                                {{ $bal->used_days }} / {{ $bal->allocated_days + $bal->carried_forward_days }}
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div style="color: #94a3b8; font-size: 0.88rem;">No leave balances initialized.</div>
            @endforelse
        </div>
    </div>

    <!-- Pending Leave Approvals Queue (If Approver Role) -->
    @if(in_array($user->role, ['super_admin', 'coo', 'hod', 'project_manager', 'social_aid']) || $user->hasAdminAccess())
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <h2 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0;">
                    Live Pending Leave Approvals Queue
                </h2>
                <a href="{{ route('leave.admin') }}" style="font-size: 0.82rem; font-weight: 700; color: #10b981; text-decoration: none;">View All Queue &rarr;</a>
            </div>

            <div style="overflow-x: auto;">
                <table class="table-custom" style="width: 100%; border-collapse: collapse; font-size: 0.88rem;">
                    <thead>
                        <tr style="background: #10b981; color: #ffffff;">
                            <th style="padding: 0.75rem 1rem; text-align: left;">Staff</th>
                            <th style="padding: 0.75rem 1rem; text-align: left;">Leave Type</th>
                            <th style="padding: 0.75rem 1rem; text-align: left;">Dates</th>
                            <th style="padding: 0.75rem 1rem; text-align: center;">Days</th>
                            <th style="padding: 0.75rem 1rem; text-align: center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingLeaveApprovals as $pReq)
                            @php
                                $pu = $pReq->user;
                                $pBalances = $pu ? $pu->leaveBalances : collect();
                                $activeTypesList = \App\Models\LeaveType::where('is_active', true)->get();
                                $pTakenDays = $pu ? $pu->leaveRequests->where('status', 'Approved')->sum('total_days') : 0;
                            @endphp
                            <tr wire:key="pending-leave-row-{{ $pReq->id }}" style="border-bottom: 1px solid #f1f5f9; transition: background 0.3s ease;">
                                <td style="padding: 0.85rem 1rem; font-weight: 700; color: #0f172a;">
                                    <div class="staff-hover-container">
                                        <div style="cursor: pointer; display: flex; align-items: center; gap: 0.35rem;" onclick="openStaffLeaveModal({{ $pu->id }})" title="Click to view full leave history modal">
                                            <span>{{ $pu->name ?? 'Staff' }}</span>
                                            <i class="bx bx-info-circle" style="color: #3b82f6; font-size: 0.9rem;"></i>
                                        </div>
                                        <div class="staff-leave-popover">
                                            <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem; margin-bottom: 0.85rem;">
                                                <div>
                                                    <h4 style="margin: 0; font-size: 0.95rem; font-weight: 800; color: #0f172a;">{{ $pu->name ?? 'Staff' }}</h4>
                                                    <span style="font-size: 0.75rem; color: #64748b; font-weight: 600;">{{ $pu->designation ?? $pu->email }}</span>
                                                </div>
                                                <span style="background: #eff6ff; color: #2563eb; padding: 0.25rem 0.65rem; border-radius: 12px; font-size: 0.74rem; font-weight: 800;">
                                                    Taken: {{ $pTakenDays }} day(s)
                                                </span>
                                            </div>

                                            <div style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 0.03em; margin-bottom: 0.6rem; text-align: left;">
                                                Available Leave Balances
                                            </div>

                                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.55rem; text-align: left;">
                                                @foreach($activeTypesList as $t)
                                                    @php
                                                        $isEligible = $pu ? $pu->isEligibleFor($t) : false;
                                                        $bal = $pBalances->firstWhere('leave_type_id', $t->id);
                                                        $avail = $bal ? $bal->balance_days : $t->getAccruedEntitlementToDate();
                                                        $used = $bal ? $bal->used_days : 0;
                                                        $totalAlloc = $bal ? ($t->accrual_type === 'Monthly' ? $bal->accrued_days : ($bal->allocated_days + $bal->carried_forward_days)) : $t->getAccruedEntitlementToDate();
                                                    @endphp
                                                    <div style="background: {{ $isEligible ? '#f8fafc' : '#fff5f5' }}; border: 1px solid {{ $isEligible ? '#e2e8f0' : '#fed7d7' }}; border-radius: 10px; padding: 0.5rem 0.6rem;">
                                                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; font-weight: 800;">
                                                            <span style="color: {{ $isEligible ? '#0f172a' : '#991b1b' }};">{{ $t->code }}</span>
                                                            @if($isEligible)
                                                                <span style="color: #10b981; font-weight: 800;">{{ $avail }}d avail</span>
                                                            @else
                                                                <span style="color: #ef4444; font-size: 0.68rem; font-weight: 700;">Ineligible</span>
                                                            @endif
                                                        </div>
                                                        <div style="font-size: 0.7rem; color: #64748b; margin-top: 0.15rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ $t->name }}
                                                        </div>
                                                        @if($isEligible)
                                                            <div style="font-size: 0.68rem; color: #475569; margin-top: 0.2rem; font-weight: 600;">
                                                                Used: {{ $used }} / {{ $totalAlloc }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 0.85rem 1rem;"><x-leave-type-badge :type="$pReq->leaveType" /></td>
                                <td style="padding: 0.85rem 1rem; font-weight: 600;">{{ $pReq->start_date->format('M d') }} &mdash; {{ $pReq->end_date->format('M d, Y') }}</td>
                                <td style="padding: 0.85rem 1rem; text-align: center; font-weight: 700; color: #2563eb;">{{ $pReq->total_days }}</td>
                                <td style="padding: 0.85rem 1rem; text-align: center;"><x-leave-status-badge :status="$pReq->status" /></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 2rem; color: #94a3b8;">No pending leave requests in queue.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
