@extends('layouts.admin')

@section('title', 'Leave Requests')

@section('content')

<style>
    .staff-hover-container {
        position: relative;
        display: inline-flex;
        align-items: center;
    }
    .staff-leave-popover {
        display: none;
        position: fixed !important;
        z-index: 99999 !important;
        width: 350px;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 16px;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.22);
        padding: 1.25rem;
        font-size: 0.85rem;
        color: #1e293b;
        pointer-events: none;
    }
</style>

<script>
    document.addEventListener('mouseover', function(e) {
        const trigger = e.target.closest('.staff-hover-container');
        if (!trigger) return;
        
        const popover = trigger.querySelector('.staff-leave-popover');
        if (!popover) return;
        
        const rect = trigger.getBoundingClientRect();
        popover.style.display = 'block';
        popover.style.left = Math.max(10, rect.left) + 'px';
        
        const spaceBelow = window.innerHeight - rect.bottom;
        if (spaceBelow < 280) {
            popover.style.top = Math.max(10, rect.top - 280) + 'px';
        } else {
            popover.style.top = (rect.bottom + 8) + 'px';
        }
    });

    document.addEventListener('mouseout', function(e) {
        const trigger = e.target.closest('.staff-hover-container');
        if (!trigger) return;
        const popover = trigger.querySelector('.staff-leave-popover');
        if (popover) {
            popover.style.display = 'none';
        }
    });
</script>

    <!-- Page Header Title and Actions -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.75rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="color: #0f172a; font-size: 1.75rem; font-weight: 800; margin: 0;">Leave Requests</h1>
            <p style="color: #64748b; font-size: 0.88rem; margin-top: 0.25rem; margin-bottom: 0;">Dashboard &nbsp;•&nbsp; Leave Requests</p>
        </div>
        @if(!Auth::user()->isSuperAdmin())
            <button onclick="openLeaveRequestModal()" class="btn-custom" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #ffffff; border: none; border-radius: 10px; padding: 0.65rem 1.25rem; font-weight: 600; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25); transition: transform 0.1s ease;">
                <i class="bx bx-calendar-event" style="font-size: 1.15rem;"></i> Apply for Leave
            </button>
        @endif
    </div>

    @if(!Auth::user()->isSuperAdmin())
        <!-- My Available Leave Balances & Types Grid -->
        <div style="margin-bottom: 1.75rem;">
            <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.4rem;">
                <i class="bx bx-wallet" style="color: #10b981; font-size: 1.25rem;"></i> Available Leave Balances & Condition Eligibility
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1rem;">
                @forelse($leaveTypesData as $item)
                    @php
                        $type = $item->leaveType;
                        $isEligible = $item->is_eligible;
                    @endphp
                    <div style="background: {{ $isEligible ? '#ffffff' : '#f8fafc' }}; border: 1px solid {{ $isEligible ? '#e2e8f0' : '#e2e8f0' }}; border-radius: 16px; padding: 1.15rem; box-shadow: {{ $isEligible ? '0 4px 12px rgba(15, 23, 42, 0.02)' : 'none' }}; display: flex; flex-direction: column; justify-content: space-between; opacity: {{ $isEligible ? '1' : '0.78' }};">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.6rem;">
                                <span style="font-size: 0.78rem; font-weight: 800; color: {{ $isEligible ? '#64748b' : '#94a3b8' }}; text-transform: uppercase; letter-spacing: 0.04em;">
                                    {{ $type->leave_code }}
                                </span>
                                @if($isEligible)
                                    <span style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 0.15rem 0.55rem; border-radius: 12px; font-size: 0.72rem; font-weight: 700;">
                                        {{ $type->accrual_type }}
                                    </span>
                                @else
                                    <span style="background: #fef2f2; color: #ef4444; border: 1px solid #fee2e2; padding: 0.15rem 0.55rem; border-radius: 12px; font-size: 0.72rem; font-weight: 700;">
                                        Ineligible
                                    </span>
                                @endif
                            </div>
                            <h4 style="font-size: 0.95rem; font-weight: 700; color: {{ $isEligible ? '#0f172a' : '#64748b' }}; margin: 0 0 0.25rem 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $type->leave_name }}">
                                {{ $type->leave_name }}
                            </h4>
                        </div>

                        <div style="margin-top: 0.75rem; border-top: 1px solid #f1f5f9; padding-top: 0.65rem; display: flex; justify-content: space-between; align-items: flex-end;">
                            @if($isEligible)
                                <div>
                                    <span style="font-size: 0.7rem; color: #94a3b8; font-weight: 600; display: block;">Available</span>
                                    <span style="font-size: 1.4rem; font-weight: 800; color: #10b981;">{{ $item->available_days }} <span style="font-size: 0.75rem; font-weight: 600; color: #64748b;">days</span></span>
                                </div>
                                <div style="text-align: right;">
                                    <span style="font-size: 0.7rem; color: #94a3b8; font-weight: 600; display: block;">Used / Total</span>
                                    <span style="font-size: 0.85rem; font-weight: 700; color: #475569;">{{ $item->used_days }} / {{ $item->total_days }}</span>
                                </div>
                            @else
                                <div style="width: 100%;">
                                    <span style="font-size: 0.7rem; color: #ef4444; font-weight: 700; display: block; margin-bottom: 0.1rem;"><i class="bx bx-info-circle" style="vertical-align: middle;"></i> Policy Requirement:</span>
                                    <span style="font-size: 0.78rem; font-weight: 600; color: #64748b;">{{ $item->reason_ineligible }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; text-align: center; color: #64748b;">
                        No leave types currently configured.
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- Leave Requests Table Container -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02); overflow: hidden;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin: 0;">Submitted Leave Applications</h2>
            <span style="background: #f1f5f9; color: #475569; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.78rem; font-weight: 700;">
                Showing {{ count($leaveRequests) }} record(s)
            </span>
        </div>

        <div style="overflow-x: auto;">
            <table class="table-custom" style="width: 100%; border-collapse: collapse; font-size: 0.88rem;">
                <thead>
                    <tr style="background: #10b981; color: #ffffff;">
                        <th style="padding: 0.85rem 1.25rem; font-weight: 700; text-align: left; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.04em;">Staff Member</th>
                        <th style="padding: 0.85rem 1.25rem; font-weight: 700; text-align: left; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.04em;">Leave Type</th>
                        <th style="padding: 0.85rem 1.25rem; font-weight: 700; text-align: left; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.04em;">Duration & Dates</th>
                        <th style="padding: 0.85rem 1.25rem; font-weight: 700; text-align: left; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.04em;">Reason</th>
                        <th style="padding: 0.85rem 1.25rem; font-weight: 700; text-align: center; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.04em;">Status</th>
                        <th style="padding: 0.85rem 1.25rem; font-weight: 700; text-align: center; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.04em;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaveRequests as $req)
                        @php
                            $u = $req->user;
                            $initial = strtoupper(substr($u->name ?? 'User', 0, 1));
                            
                            $statusStyle = 'background: #fffbeb; color: #d97706; border: 1px solid #fef3c7;';
                            if ($req->status === 'Approved') {
                                $statusStyle = 'background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;';
                            } elseif ($req->status === 'Rejected') {
                                $statusStyle = 'background: #fef2f2; color: #ef4444; border: 1px solid #fee2e2;';
                            }

                            // Fetch balances for staff hover popover
                            $staffBalances = $u ? $u->leaveBalances : collect();
                            $activeTypesList = \App\Models\LeaveType::where('is_active', true)->get();
                            $staffApprovedDays = $u ? $u->leaveRequests->where('status', 'Approved')->sum('total_days') : 0;
                        @endphp
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s ease;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                            <!-- Staff Member Info with Hover Popover -->
                            <td style="padding: 1rem 1.25rem; vertical-align: middle;">
                                <div class="staff-hover-container">
                                    <div style="display: flex; align-items: center; gap: 0.85rem; cursor: pointer;" onclick="openStaffLeaveModal({{ $u->id }})" title="Click to view full leave history modal">
                                        @if($u && $u->profile && $u->profile->photo)
                                            <img src="{{ asset($u->profile->photo) }}" alt="{{ $u->name }}" style="width: 42px; height: 42px; border-radius: 50%; object-fit: cover; border: 2px solid #e2e8f0; flex-shrink: 0;">
                                        @else
                                            <div style="width: 42px; height: 42px; border-radius: 50%; background: #ecfdf5; color: #059669; font-weight: 800; font-size: 1.1rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 2px solid #d1fae5;">
                                                {{ $initial }}
                                            </div>
                                        @endif
                                        <div>
                                            <div style="font-weight: 700; color: #0f172a; font-size: 0.92rem; display: flex; align-items: center; gap: 0.35rem;">
                                                {{ $u->name ?? 'Unknown Staff' }}
                                                <i class="bx bx-info-circle" style="color: #3b82f6; font-size: 0.95rem;" title="Hover to view available leaves & taken leave details"></i>
                                            </div>
                                            <div style="font-size: 0.78rem; color: #64748b;">{{ $u->designation ?? $u->email }}</div>
                                        </div>
                                    </div>

                                    <!-- Hover Popover Card -->
                                    <div class="staff-leave-popover">
                                        <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem; margin-bottom: 0.85rem;">
                                            <div>
                                                <h4 style="margin: 0; font-size: 0.95rem; font-weight: 800; color: #0f172a;">{{ $u->name ?? 'Staff Details' }}</h4>
                                                <span style="font-size: 0.75rem; color: #64748b; font-weight: 600;">{{ $u->designation ?? $u->email }}</span>
                                            </div>
                                            <span style="background: #eff6ff; color: #2563eb; padding: 0.25rem 0.65rem; border-radius: 12px; font-size: 0.74rem; font-weight: 800;">
                                                Taken: {{ $staffApprovedDays }} day(s)
                                            </span>
                                        </div>

                                        <div style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 0.03em; margin-bottom: 0.6rem;">
                                            Available Leave Balances & Taken
                                        </div>

                                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.55rem;">
                                            @foreach($activeTypesList as $t)
                                                @php
                                                    $isEligible = $u ? $u->isEligibleFor($t) : false;
                                                    $bal = $staffBalances->firstWhere('leave_type_id', $t->id);
                                                    $avail = $bal ? $bal->balance_days : $t->getAccruedEntitlementToDate();
                                                    $used = $bal ? $bal->used_days : 0;
                                                    $totalAlloc = $bal ? ($t->accrual_type === 'Monthly' ? $bal->accrued_days : ($bal->allocated_days + $bal->carried_forward_days)) : $t->getAccruedEntitlementToDate();
                                                @endphp
                                                <div style="background: {{ $isEligible ? '#f8fafc' : '#fff5f5' }}; border: 1px solid {{ $isEligible ? '#e2e8f0' : '#fed7d7' }}; border-radius: 10px; padding: 0.55rem 0.65rem;">
                                                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.76rem; font-weight: 800;">
                                                        <span style="color: {{ $isEligible ? '#0f172a' : '#991b1b' }};">{{ $t->code }}</span>
                                                        @if($isEligible)
                                                            @if($t->leave_code === 'LWP' || $t->isUnlimited())
                                                                <span style="color: #ef4444; font-weight: 800; font-size: 0.74rem;">{{ $used }}d taken</span>
                                                            @else
                                                                <span style="color: #10b981; font-weight: 800; font-size: 0.74rem;">{{ $avail }}d avail</span>
                                                            @endif
                                                        @else
                                                            <span style="color: #ef4444; font-size: 0.68rem; font-weight: 700;">Ineligible</span>
                                                        @endif
                                                    </div>
                                                    <div style="font-size: 0.72rem; font-weight: 700; color: #334155; margin-top: 0.15rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                        {{ $t->name }}
                                                    </div>
                                                    @if($isEligible)
                                                        @if($t->leave_code === 'LWP' || $t->isUnlimited())
                                                            <div style="font-size: 0.7rem; color: #dc2626; margin-top: 0.25rem; font-weight: 600;">
                                                                Taken: <strong>{{ $used }}</strong> days (No limit)
                                                            </div>
                                                        @else
                                                            <div style="font-size: 0.7rem; color: #475569; margin-top: 0.25rem; font-weight: 600;">
                                                                Used: <strong>{{ $used }}</strong> / {{ $totalAlloc }} days
                                                            </div>
                                                        @endif
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Leave Type -->
                            <td style="padding: 1rem 1.25rem; vertical-align: middle;">
                                @if($req->leaveType)
                                    <x-leave-type-badge :type="$req->leaveType" />
                                @else
                                    <span style="background: #f1f5f9; color: #334155; padding: 0.3rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 700; border: 1px solid #e2e8f0; display: inline-block;">
                                        <i class="bx bx-tag-alt" style="color: #f59e0b; margin-right: 0.2rem;"></i> {{ $req->leave_type ?? 'Leave' }}
                                    </span>
                                @endif
                            </td>

                            <!-- Duration & Dates -->
                            <td style="padding: 1rem 1.25rem; vertical-align: middle;">
                                <div style="font-weight: 700; color: #0f172a; font-size: 0.88rem;">
                                    {{ \Carbon\Carbon::parse($req->start_date ?? $req->from_date)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($req->end_date ?? $req->to_date)->format('d/m/Y') }}
                                </div>
                                @if($req->is_half_day)
                                    <span style="background: #f3e8ff; color: #9333ea; border: 1px solid #d8b4fe; padding: 0.15rem 0.55rem; border-radius: 12px; font-size: 0.74rem; font-weight: 800; display: inline-block; margin-top: 0.25rem;">
                                        0.5 Day (Half Day &bull; {{ $req->half_day_session ?? 'First Half' }})
                                    </span>
                                @else
                                    <span style="background: #eff6ff; color: #2563eb; padding: 0.15rem 0.55rem; border-radius: 12px; font-size: 0.74rem; font-weight: 700; display: inline-block; margin-top: 0.25rem;">
                                        {{ $req->total_days ?? $req->days_count }} day(s)
                                    </span>
                                @endif
                            </td>

                            <!-- Reason -->
                            <td style="padding: 1rem 1.25rem; vertical-align: middle; max-width: 260px;">
                                <div style="color: #475569; font-size: 0.85rem; line-height: 1.45; max-height: 3.6em; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;" title="{{ $req->reason }}">
                                    {{ $req->reason }}
                                </div>
                                @if($req->rejection_reason)
                                    <div style="margin-top: 0.35rem; color: #ef4444; font-size: 0.75rem; font-weight: 600;">
                                        <strong>Decline Reason:</strong> {{ $req->rejection_reason }}
                                    </div>
                                @endif
                            </td>

                            <!-- Status -->
                            <td style="padding: 1rem 1.25rem; text-align: center; vertical-align: middle;">
                                <span style="{{ $statusStyle }} padding: 0.3rem 0.85rem; border-radius: 20px; font-size: 0.78rem; font-weight: 700; display: inline-block;">
                                    @if($req->status === 'Pending')
                                        <i class="bx bx-time-five" style="vertical-align: middle;"></i> PENDING
                                    @elseif($req->status === 'Approved')
                                        <i class="bx bx-check-circle" style="vertical-align: middle;"></i> APPROVED
                                    @else
                                        <i class="bx bx-x-circle" style="vertical-align: middle;"></i> REJECTED
                                    @endif
                                </span>
                            </td>

                            <!-- Actions -->
                            <td style="padding: 1rem 1.25rem; text-align: center; vertical-align: middle;">
                                <div style="display: flex; gap: 0.4rem; justify-content: center; align-items: center;">
                                    @if(Auth::user()->hasAdminAccess() && $req->status === 'Pending')
                                        <!-- Approve Form -->
                                        <form action="{{ route('leave.approve', $req->id) }}" method="POST" style="margin: 0;">
                                            @csrf
                                            <button type="submit" style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #059669; padding: 0.4rem 0.75rem; border-radius: 8px; font-size: 0.78rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem;" title="Approve Leave">
                                                <i class="bx bx-check" style="font-size: 1rem;"></i> Approve
                                            </button>
                                        </form>

                                        <!-- Reject Modal Prompt Button -->
                                        <button type="button" onclick="promptRejectLeave({{ $req->id }})" style="background: #fef2f2; border: 1px solid #fee2e2; color: #ef4444; padding: 0.4rem 0.75rem; border-radius: 8px; font-size: 0.78rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem;" title="Reject Leave">
                                            <i class="bx bx-x" style="font-size: 1rem;"></i> Reject
                                        </button>
                                    @endif

                                    @if($req->status === 'Pending')
                                        <!-- Delete Record -->
                                        <form action="{{ route('leave.destroy', $req->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this leave request record?')" style="margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background: #ffffff; border: 1px solid #cbd5e1; color: #64748b; padding: 0.4rem 0.6rem; border-radius: 8px; font-size: 0.85rem; cursor: pointer; display: inline-flex; align-items: center;" title="Delete Record">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem 1.5rem; color: #94a3b8; font-weight: 500;">
                                <i class="bx bx-calendar-x" style="font-size: 2.5rem; color: #cbd5e1; display: block; margin-bottom: 0.5rem;"></i>
                                No leave request records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Hidden Rejection Form Modal -->
    <div id="rejectLeaveModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(15, 23, 42, 0.5); backdrop-filter: blur(6px); display: none; align-items: center; justify-content: center; z-index: 1100;">
        <div style="background: #ffffff; border-radius: 16px; width: 100%; max-width: 480px; padding: 1.75rem; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
            <h3 style="font-size: 1.15rem; font-weight: 700; color: #0f172a; margin-top: 0; margin-bottom: 0.5rem;">Decline Leave Request</h3>
            <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 1rem;">Provide an optional reason for rejecting this leave request:</p>
            
            <form id="rejectLeaveForm" action="" method="POST">
                @csrf
                <textarea name="rejection_reason" rows="3" placeholder="State reason for rejection..." style="width: 100%; padding: 0.65rem 1rem; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 0.9rem; outline: none; margin-bottom: 1.25rem; font-family: inherit;"></textarea>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" onclick="document.getElementById('rejectLeaveModal').style.display='none'" style="background: #ffffff; border: 1px solid #cbd5e1; color: #475569; padding: 0.6rem 1.15rem; border-radius: 8px; font-weight: 600; font-size: 0.88rem; cursor: pointer;">
                        Cancel
                    </button>
                    <button type="submit" style="background: #ef4444; color: #ffffff; border: none; border-radius: 8px; padding: 0.6rem 1.25rem; font-weight: 600; font-size: 0.88rem; cursor: pointer;">
                        Confirm Decline
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Staff Leave Details & Complete History Modal Dialog -->
    <div id="staffLeaveHistoryModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(15, 23, 42, 0.55); backdrop-filter: blur(6px); display: none; align-items: center; justify-content: center; z-index: 10000; padding: 1rem;">
        <div style="background: #ffffff; border-radius: 20px; width: 100%; max-width: 780px; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 25px 50px rgba(0,0,0,0.25); overflow: hidden;">
            
            <!-- Modal Header -->
            <div style="padding: 1.25rem 1.75rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div id="modalStaffAvatar" style="width: 50px; height: 50px; border-radius: 50%; background: #ecfdf5; color: #059669; font-weight: 800; font-size: 1.25rem; display: flex; align-items: center; justify-content: center; border: 2px solid #a7f3d0; flex-shrink: 0;">
                        U
                    </div>
                    <div>
                        <h3 id="modalStaffName" style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #0f172a;">Staff Name</h3>
                        <span id="modalStaffRole" style="font-size: 0.8rem; color: #64748b; font-weight: 600;">Role / Email</span>
                    </div>
                </div>
                <button type="button" onclick="closeStaffLeaveModal()" style="background: none; border: none; font-size: 1.6rem; color: #94a3b8; cursor: pointer; padding: 0.25rem; line-height: 1;">&times;</button>
            </div>

            <!-- Modal Content Body -->
            <div style="padding: 1.5rem; overflow-y: auto; flex: 1;">
                
                <!-- Quick Metrics Bar -->
                <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 200px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 14px; padding: 1rem; display: flex; align-items: center; gap: 0.85rem;">
                        <div style="background: #2563eb; color: #ffffff; width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                            <i class="bx bx-calendar-check"></i>
                        </div>
                        <div>
                            <span style="font-size: 0.75rem; font-weight: 700; color: #1e40af; text-transform: uppercase;">Total Approved Taken</span>
                            <div id="modalStaffTakenDays" style="font-size: 1.4rem; font-weight: 800; color: #1e3a8a; margin-top: 0.1rem;">0 day(s)</div>
                        </div>
                    </div>

                    <div style="flex: 1; min-width: 200px; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 14px; padding: 1rem; display: flex; align-items: center; gap: 0.85rem;">
                        <div style="background: #10b981; color: #ffffff; width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                            <i class="bx bx-list-check"></i>
                        </div>
                        <div>
                            <span style="font-size: 0.75rem; font-weight: 700; color: #065f46; text-transform: uppercase;">Total Leave Records</span>
                            <div id="modalStaffTotalApps" style="font-size: 1.4rem; font-weight: 800; color: #064e3b; margin-top: 0.1rem;">0 record(s)</div>
                        </div>
                    </div>
                </div>

                <!-- Available Balances Grid -->
                <h4 style="font-size: 0.88rem; font-weight: 800; text-transform: uppercase; color: #475569; letter-spacing: 0.04em; margin-top: 0; margin-bottom: 0.75rem;">
                    Active Leave Balances & Allocation
                </h4>
                <div id="modalBalancesGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)); gap: 0.75rem; margin-bottom: 1.75rem;">
                    <!-- Dynamically populated -->
                </div>

                <!-- Complete History Table -->
                <h4 style="font-size: 0.88rem; font-weight: 800; text-transform: uppercase; color: #475569; letter-spacing: 0.04em; margin-top: 0; margin-bottom: 0.75rem;">
                    All Leave Request Records & History
                </h4>
                <div style="overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 12px;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                        <thead>
                            <tr style="background: #f8fafc; color: #475569; border-bottom: 1px solid #e2e8f0;">
                                <th style="padding: 0.65rem 0.85rem; text-align: left; font-weight: 700;">Leave Type</th>
                                <th style="padding: 0.65rem 0.85rem; text-align: left; font-weight: 700;">Dates & Duration</th>
                                <th style="padding: 0.65rem 0.85rem; text-align: left; font-weight: 700;">Reason</th>
                                <th style="padding: 0.65rem 0.85rem; text-align: center; font-weight: 700;">Status</th>
                            </tr>
                        </thead>
                        <tbody id="modalHistoryTbody">
                            <!-- Dynamically populated -->
                        </tbody>
                    </table>
                </div>

            </div>

            <!-- Modal Footer -->
            <div style="padding: 1rem 1.75rem; border-top: 1px solid #e2e8f0; background: #f8fafc; display: flex; justify-content: flex-end;">
                <button type="button" onclick="closeStaffLeaveModal()" style="background: #0f172a; color: #ffffff; border: none; padding: 0.6rem 1.4rem; border-radius: 10px; font-weight: 700; font-size: 0.88rem; cursor: pointer;">
                    Close
                </button>
            </div>

        </div>
    </div>

    <script>
        function promptRejectLeave(reqId) {
            const form = document.getElementById('rejectLeaveForm');
            form.action = '/admin/leave-requests/' + reqId + '/reject';
            document.getElementById('rejectLeaveModal').style.display = 'flex';
        }

        async function openStaffLeaveModal(staffId) {
            const modal = document.getElementById('staffLeaveHistoryModal');
            if (!modal) return;
            
            document.getElementById('modalStaffName').innerText = 'Loading Staff Leave History...';
            document.getElementById('modalStaffRole').innerText = 'Please wait...';
            document.getElementById('modalBalancesGrid').innerHTML = '<div style="color: #64748b; padding: 1rem;">Loading balances...</div>';
            document.getElementById('modalHistoryTbody').innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 1.5rem; color: #94a3b8;">Loading leave records...</td></tr>';
            
            modal.style.display = 'flex';
            
            try {
                const response = await fetch('/admin/staff/' + staffId + '/leave-history');
                const data = await response.json();
                
                if (!data.success) throw new Error('Failed to load history');
                
                const staff = data.staff;
                document.getElementById('modalStaffName').innerText = staff.name;
                document.getElementById('modalStaffRole').innerText = (staff.designation || 'Staff') + ' • ' + staff.email;
                document.getElementById('modalStaffTakenDays').innerText = staff.total_taken + ' day(s)';
                document.getElementById('modalStaffTotalApps').innerText = staff.total_applications + ' record(s)';
                
                const avatarEl = document.getElementById('modalStaffAvatar');
                if (staff.photo) {
                    avatarEl.innerHTML = `<img src="${staff.photo}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">`;
                } else {
                    avatarEl.innerText = staff.initial;
                }
                
                let balancesHtml = '';
                (data.balances || []).forEach(b => {
                    const isLwp = b.type_code === 'LWP';
                    const availHtml = isLwp ? `<span style="color: #dc2626;">${b.used}d taken</span>` : `<span style="color: #10b981;">${b.available}d avail</span>`;
                    const usedHtml = isLwp ? `Taken: ${b.used} days (No limit)` : `Used: ${b.used} / ${b.allocated} days`;
                    balancesHtml += `
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 0.65rem 0.85rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; font-weight: 800; font-size: 0.8rem; color: #0f172a;">
                                <span>${b.type_code}</span>
                                ${availHtml}
                            </div>
                            <div style="font-size: 0.72rem; color: #64748b; margin-top: 0.15rem;">${b.type_name}</div>
                            <div style="font-size: 0.7rem; color: #475569; margin-top: 0.25rem; font-weight: 600;">
                                ${usedHtml}
                            </div>
                        </div>
                    `;
                });
                document.getElementById('modalBalancesGrid').innerHTML = balancesHtml || '<div style="color: #94a3b8;">No balances found.</div>';
                
                let historyHtml = '';
                (data.history || []).forEach(h => {
                    let statusBg = '#fffbeb';
                    let statusColor = '#d97706';
                    if (h.status === 'Approved') {
                        statusBg = '#ecfdf5';
                        statusColor = '#059669';
                    } else if (h.status === 'Rejected') {
                        statusBg = '#fef2f2';
                        statusColor = '#ef4444';
                    }
                    
                    historyHtml += `
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 0.75rem 0.85rem; font-weight: 700; color: #0f172a;">
                                <span style="background: #eff6ff; color: #2563eb; padding: 0.2rem 0.5rem; border-radius: 8px; font-size: 0.72rem; font-weight: 800; margin-right: 0.3rem;">${h.leave_code}</span>
                                ${h.leave_type}
                            </td>
                            <td style="padding: 0.75rem 0.85rem;">
                                <div style="font-weight: 700; color: #0f172a;">${h.start_date} — ${h.end_date}</div>
                                <span style="font-size: 0.72rem; color: #2563eb; font-weight: 700;">${h.total_days} day(s)</span>
                            </td>
                            <td style="padding: 0.75rem 0.85rem; max-width: 200px; color: #475569;">
                                ${h.reason}
                            </td>
                            <td style="padding: 0.75rem 0.85rem; text-align: center;">
                                <span style="background: ${statusBg}; color: ${statusColor}; padding: 0.25rem 0.65rem; border-radius: 12px; font-size: 0.74rem; font-weight: 800;">
                                    ${h.status}
                                </span>
                            </td>
                        </tr>
                    `;
                });
                
                document.getElementById('modalHistoryTbody').innerHTML = historyHtml || '<tr><td colspan="4" style="text-align: center; padding: 1.5rem; color: #94a3b8;">No leave history records found.</td></tr>';

            } catch (e) {
                console.error('Error opening staff leave modal:', e);
                document.getElementById('modalStaffName').innerText = 'Error Loading Data';
            }
        }

        function closeStaffLeaveModal() {
            const modal = document.getElementById('staffLeaveHistoryModal');
            if (modal) modal.style.display = 'none';
        }

        window.openStaffLeaveModal = openStaffLeaveModal;
        window.closeStaffLeaveModal = closeStaffLeaveModal;
    </script>

    @include('partials.leave_request_modal')

@endsection
