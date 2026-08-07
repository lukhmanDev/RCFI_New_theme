<div style="display: flex; flex-direction: column; gap: 1.75rem;">

    <!-- Flash Notifications -->
    @if($successMessage)
        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #059669; padding: 1rem 1.25rem; border-radius: 12px; font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; justify-content: space-between;">
            <div><i class="bx bx-check-circle" style="font-size: 1.2rem; vertical-align: middle; margin-right: 0.35rem;"></i> {{ $successMessage }}</div>
            <button type="button" wire:click="$set('successMessage', '')" style="background: none; border: none; cursor: pointer; color: #059669;"><i class="bx bx-x" style="font-size: 1.2rem;"></i></button>
        </div>
    @endif

    @if($errorMessage)
        <div style="background: #fef2f2; border: 1px solid #fee2e2; color: #ef4444; padding: 1rem 1.25rem; border-radius: 12px; font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; justify-content: space-between;">
            <div><i class="bx bx-error-circle" style="font-size: 1.2rem; vertical-align: middle; margin-right: 0.35rem;"></i> {{ $errorMessage }}</div>
            <button type="button" wire:click="$set('errorMessage', '')" style="background: none; border: none; cursor: pointer; color: #ef4444;"><i class="bx bx-x" style="font-size: 1.2rem;"></i></button>
        </div>
    @endif

    <!-- Page Header Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="color: #0f172a; font-size: 1.75rem; font-weight: 800; margin: 0;">Employee Leave Portal</h1>
            <p style="color: #64748b; font-size: 0.88rem; margin-top: 0.25rem; margin-bottom: 0;">View your current balances and apply for leave requests.</p>
        </div>
        <button wire:click="openModal" class="btn-custom" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #ffffff; border: none; border-radius: 10px; padding: 0.65rem 1.35rem; font-weight: 600; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25);">
            <i class="bx bx-calendar-event" style="font-size: 1.15rem;"></i> Apply for Leave
        </button>
    </div>

    <!-- Balance Summary Cards Grid -->
    <div>
        <h2 style="font-size: 1.05rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.04em;">My Leave Balances</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 1.25rem;">
            @foreach($leaveTypes as $type)
                @php
                    $isEligible = $type->is_eligible;
                    $bal = $type->user_balance;
                    $avail = $bal ? $bal->balance_days : $type->getAccruedEntitlementToDate();
                    $totalAlloc = $bal ? ($type->accrual_type === 'Monthly' ? $bal->accrued_days : ($bal->allocated_days + $bal->carried_forward_days)) : $type->getAccruedEntitlementToDate();
                    $percent = $totalAlloc > 0 ? min(100, max(0, round(($avail / $totalAlloc) * 100))) : 0;
                @endphp
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; opacity: {{ $isEligible ? '1' : '0.6' }}; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02); position: relative;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                        <x-leave-type-badge :type="$type" />
                        @if(!$isEligible)
                            <span style="background: #f1f5f9; color: #94a3b8; padding: 0.15rem 0.5rem; border-radius: 12px; font-size: 0.7rem; font-weight: 700;">Not Eligible</span>
                        @endif
                    </div>

                    @if($isEligible)
                        <div style="margin-top: 0.5rem; margin-bottom: 0.75rem;">
                            <div style="font-size: 1.75rem; font-weight: 800; color: #0f172a; line-height: 1;">
                                {{ $avail }} <span style="font-size: 0.85rem; font-weight: 600; color: #64748b;">/ {{ $totalAlloc }} days</span>
                            </div>
                            <span style="font-size: 0.75rem; color: #64748b; font-weight: 500;">Available Balance</span>
                        </div>

                        <!-- Progress Bar -->
                        <div style="width: 100%; height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden;">
                            <div style="width: {{ $percent }}%; height: 100%; background: #10b981; transition: width 0.3s ease;"></div>
                        </div>
                    @else
                        <div style="margin-top: 1rem; color: #94a3b8; font-size: 0.82rem; font-weight: 500;">
                            Requirements not met (service years, gender, or marital status).
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- My Requests Table -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02); overflow: hidden; margin-top: 1rem;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <h2 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin: 0;">My Request History</h2>
        </div>

        <div style="overflow-x: auto;">
            <table class="table-custom" style="width: 100%; border-collapse: collapse; font-size: 0.88rem;">
                <thead>
                    <tr style="background: #10b981; color: #ffffff;">
                        <th style="padding: 0.85rem 1.25rem; font-weight: 700; text-align: left;">Leave Type</th>
                        <th style="padding: 0.85rem 1.25rem; font-weight: 700; text-align: left;">Dates</th>
                        <th style="padding: 0.85rem 1.25rem; font-weight: 700; text-align: center;">Days</th>
                        <th style="padding: 0.85rem 1.25rem; font-weight: 700; text-align: left;">Reason</th>
                        <th style="padding: 0.85rem 1.25rem; font-weight: 700; text-align: center;">Status</th>
                        <th style="padding: 0.85rem 1.25rem; font-weight: 700; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($myRequests as $req)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 1rem 1.25rem;"><x-leave-type-badge :type="$req->leaveType" /></td>
                            <td style="padding: 1rem 1.25rem; font-weight: 600; color: #0f172a;">
                                {{ $req->start_date->format('M d, Y') }} &mdash; {{ $req->end_date->format('M d, Y') }}
                            </td>
                            <td style="padding: 1rem 1.25rem; text-align: center; font-weight: 700; color: #2563eb;">
                                {{ $req->total_days }}
                            </td>
                            <td style="padding: 1rem 1.25rem; max-width: 250px; color: #475569;">
                                {{ $req->reason }}
                                @if($req->remarks)
                                    <div style="font-size: 0.75rem; color: #ef4444; font-weight: 600; margin-top: 0.2rem;">Remarks: {{ $req->remarks }}</div>
                                @endif
                            </td>
                            <td style="padding: 1rem 1.25rem; text-align: center;">
                                <x-leave-status-badge :status="$req->status" />
                            </td>
                            <td style="padding: 1rem 1.25rem; text-align: center;">
                                @if($req->status === 'Pending')
                                    <button wire:click="cancelLeave({{ $req->id }})" wire:confirm="Are you sure you want to cancel this pending leave request?" style="background: #f1f5f9; border: 1px solid #e2e8f0; color: #64748b; padding: 0.35rem 0.75rem; border-radius: 8px; font-size: 0.78rem; font-weight: 700; cursor: pointer;">
                                        Cancel
                                    </button>
                                @else
                                    <span style="color: #cbd5e1;">&mdash;</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2.5rem; color: #94a3b8; font-weight: 500;">
                                No leave requests submitted yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($myRequests, 'links'))
            <div style="padding: 1rem 1.5rem; border-top: 1px solid #e2e8f0;">
                {{ $myRequests->links() }}
            </div>
        @endif
    </div>

    <!-- Apply for Leave Modal Dialog -->
    @if($showModal)
        <div style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(15, 23, 42, 0.5); backdrop-filter: blur(6px); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 1.5rem;">
            <div style="background: #ffffff; border-radius: 16px; width: 100%; max-width: 540px; padding: 1.75rem; box-shadow: 0 20px 40px rgba(0,0,0,0.15); position: relative;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.75rem;">
                    <h3 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin: 0;"><i class="bx bx-calendar-event" style="color: #f59e0b; margin-right: 0.35rem;"></i> Apply for Leave</h3>
                    <button type="button" wire:click="closeModal" style="background: #f1f5f9; border: none; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #64748b;"><i class="bx bx-x" style="font-size: 1.25rem;"></i></button>
                </div>

                <form wire:submit.prevent="submitLeave">
                    <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.5rem;">
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 0.4rem;">Select Leave Type <span style="color:#ef4444;">*</span></label>
                            <select wire:model.live="leave_type_id" required style="width: 100%; padding: 0.65rem 1rem; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 0.9rem; outline: none; background: #ffffff; color: #0f172a;">
                                <option value="">-- Choose eligible leave type --</option>
                                @foreach($leaveTypes as $t)
                                    @if($t->is_eligible)
                                        <option value="{{ $t->id }}">{{ $t->leave_code }} &mdash; {{ $t->leave_name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('leave_type_id') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                        </div>

                        <x-date-range-picker startDateName="start_date" endDateName="end_date" />

                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 0.4rem;">Reason / Remarks</label>
                            <textarea wire:model="reason" rows="3" placeholder="State reason for your leave..." style="width: 100%; padding: 0.65rem 1rem; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 0.9rem; outline: none; background: #ffffff; color: #0f172a; font-family: inherit; resize: vertical;"></textarea>
                            @error('reason') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid #e2e8f0; padding-top: 1rem;">
                        <button type="button" wire:click="closeModal" style="background: #ffffff; border: 1px solid #cbd5e1; color: #475569; padding: 0.6rem 1.15rem; border-radius: 10px; font-weight: 600; font-size: 0.88rem; cursor: pointer;">Cancel</button>
                        <button type="submit" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #ffffff; border: none; border-radius: 10px; padding: 0.6rem 1.4rem; font-weight: 600; font-size: 0.88rem; cursor: pointer; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25);">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
