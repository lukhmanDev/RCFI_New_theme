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

    <!-- Header & Navigation Tabs -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="color: #0f172a; font-size: 1.75rem; font-weight: 800; margin: 0;">Leave Management Dashboard</h1>
            <p style="color: #64748b; font-size: 0.88rem; margin-top: 0.25rem; margin-bottom: 0;">Real-time queue approvals, team leave calendar, policy setup, and reports.</p>
        </div>

        <!-- Navigation Tabs -->
        <div style="display: flex; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 0.25rem; gap: 0.25rem; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
            <button type="button" wire:click="setTab('pending')" style="padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.85rem; font-weight: 700; border: none; cursor: pointer; transition: all 0.2s ease; {{ $activeTab === 'pending' ? 'background: #10b981; color: #ffffff;' : 'background: transparent; color: #64748b;' }}">
                <i class="bx bx-time-five" style="vertical-align: middle;"></i> Pending Queue
            </button>
            <button type="button" wire:click="setTab('calendar')" style="padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.85rem; font-weight: 700; border: none; cursor: pointer; transition: all 0.2s ease; {{ $activeTab === 'calendar' ? 'background: #10b981; color: #ffffff;' : 'background: transparent; color: #64748b;' }}">
                <i class="bx bx-calendar" style="vertical-align: middle;"></i> Team Calendar
            </button>
            @if(Auth::user() && Auth::user()->isSuperAdmin())
                <button type="button" wire:click="setTab('types')" style="padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.85rem; font-weight: 700; border: none; cursor: pointer; transition: all 0.2s ease; {{ $activeTab === 'types' ? 'background: #10b981; color: #ffffff;' : 'background: transparent; color: #64748b;' }}">
                    <i class="bx bx-slider-alt" style="vertical-align: middle;"></i> Leave Types
                </button>
            @endif
            <button type="button" wire:click="setTab('reports')" style="padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.85rem; font-weight: 700; border: none; cursor: pointer; transition: all 0.2s ease; {{ $activeTab === 'reports' ? 'background: #10b981; color: #ffffff;' : 'background: transparent; color: #64748b;' }}">
                <i class="bx bx-bar-chart-alt-2" style="vertical-align: middle;"></i> Reports & CSV
            </button>
        </div>
    </div>

    <!-- TAB 1: PENDING APPROVALS QUEUE -->
    @if($activeTab === 'pending')
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02); overflow: hidden;">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin: 0;">Pending Approvals Queue</h2>
                <span style="background: #fff7ed; color: #c2410c; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.78rem; font-weight: 700;">
                    Live Queue: {{ $pendingRequests->total() }} pending
                </span>
            </div>

            <div style="overflow-x: auto;">
                <table class="table-custom" style="width: 100%; border-collapse: collapse; font-size: 0.88rem;">
                    <thead>
                        <tr style="background: #10b981; color: #ffffff;">
                            <th style="padding: 0.85rem 1.25rem; font-weight: 700; text-align: left;">Staff Member</th>
                            <th style="padding: 0.85rem 1.25rem; font-weight: 700; text-align: left;">Leave Type</th>
                            <th style="padding: 0.85rem 1.25rem; font-weight: 700; text-align: left;">Dates & Days</th>
                            <th style="padding: 0.85rem 1.25rem; font-weight: 700; text-align: left;">Reason</th>
                            <th style="padding: 0.85rem 1.25rem; font-weight: 700; text-align: center;">Applied On</th>
                            <th style="padding: 0.85rem 1.25rem; font-weight: 700; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingRequests as $req)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 1rem 1.25rem;">
                                    <div style="font-weight: 700; color: #0f172a;">{{ $req->user->name ?? 'User' }}</div>
                                    <div style="font-size: 0.78rem; color: #64748b;">{{ $req->user->designation ?? $req->user->email }}</div>
                                </td>
                                <td style="padding: 1rem 1.25rem;"><x-leave-type-badge :type="$req->leaveType" /></td>
                                <td style="padding: 1rem 1.25rem; font-weight: 600; color: #0f172a;">
                                    {{ $req->start_date->format('d/m/Y') }} &mdash; {{ $req->end_date->format('d/m/Y') }}
                                    <div style="font-size: 0.75rem; color: #2563eb; font-weight: 700;">({{ $req->total_days }} day(s))</div>
                                </td>
                                <td style="padding: 1rem 1.25rem; max-width: 260px; color: #475569;">{{ $req->reason }}</td>
                                <td style="padding: 1rem 1.25rem; text-align: center; color: #64748b; font-size: 0.82rem;">
                                    {{ $req->applied_on ? $req->applied_on->format('d/m/Y h:i A') : $req->created_at->format('d/m/Y h:i A') }}
                                </td>
                                <td style="padding: 1rem 1.25rem; text-align: center;">
                                    <div style="display: flex; gap: 0.4rem; justify-content: center;">
                                        <button type="button" wire:click="approveRequest({{ $req->id }})" style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #059669; padding: 0.4rem 0.85rem; border-radius: 8px; font-size: 0.78rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem;">
                                            <i class="bx bx-check"></i> Approve
                                        </button>
                                        <button type="button" wire:click="openRejectModal({{ $req->id }})" style="background: #fef2f2; border: 1px solid #fee2e2; color: #ef4444; padding: 0.4rem 0.85rem; border-radius: 8px; font-size: 0.78rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem;">
                                            <i class="bx bx-x"></i> Reject
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 3rem 1.5rem; color: #94a3b8; font-weight: 500;">
                                    <i class="bx bx-check-double" style="font-size: 2.5rem; color: #cbd5e1; display: block; margin-bottom: 0.5rem;"></i>
                                    All clear! No pending leave requests in the queue.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($pendingRequests, 'links'))
                <div style="padding: 1rem 1.5rem; border-top: 1px solid #e2e8f0;">
                    {{ $pendingRequests->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- TAB 2: TEAM LEAVE CALENDAR -->
    @if($activeTab === 'calendar')
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin: 0;">
                    Team Leave Calendar &mdash; {{ \Carbon\Carbon::create($calendarYear, $calendarMonth, 1)->format('F Y') }}
                </h2>
                <div style="display: flex; gap: 0.5rem;">
                    <button type="button" wire:click="prevMonth" style="background: #f1f5f9; border: 1px solid #e2e8f0; padding: 0.4rem 0.85rem; border-radius: 8px; font-weight: 700; cursor: pointer; color: #334155;"><i class="bx bx-chevron-left"></i> Prev</button>
                    <button type="button" wire:click="nextMonth" style="background: #f1f5f9; border: 1px solid #e2e8f0; padding: 0.4rem 0.85rem; border-radius: 8px; font-weight: 700; cursor: pointer; color: #334155;">Next <i class="bx bx-chevron-right"></i></button>
                </div>
            </div>

            <!-- Approved Leaves Grid List for current month -->
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @forelse($calendarApproved as $app)
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #10b981; padding: 1rem 1.25rem; border-radius: 10px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                            <div style="background: #ecfdf5; color: #059669; font-weight: 800; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                {{ strtoupper(substr($app->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight: 700; color: #0f172a;">{{ $app->user->name ?? 'User' }}</div>
                                <div style="font-size: 0.78rem; color: #64748b;">{{ $app->user->designation ?? $app->user->email }}</div>
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <x-leave-type-badge :type="$app->leaveType" />
                            <div style="font-size: 0.88rem; font-weight: 700; color: #0f172a;">
                                {{ $app->start_date->format('d/m/Y') }} &mdash; {{ $app->end_date->format('d/m/Y') }}
                                <span style="font-size: 0.75rem; color: #2563eb; font-weight: 700; display: block; text-align: right;">{{ $app->total_days }} day(s)</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 3rem; color: #94a3b8;">
                        <i class="bx bx-calendar-check" style="font-size: 2.5rem; color: #cbd5e1; display: block; margin-bottom: 0.5rem;"></i>
                        No approved leave scheduled for {{ \Carbon\Carbon::create($calendarYear, $calendarMonth, 1)->format('F Y') }}.
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- TAB 3: LEAVE TYPE MANAGEMENT (Super Admin Only) -->
    @if($activeTab === 'types' && Auth::user() && Auth::user()->isSuperAdmin())
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
                <h2 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0;">Policy Configuration (Leave Types)</h2>
                <button type="button" wire:click="openCreateModal" style="background: #10b981; color: #ffffff; border: none; padding: 0.55rem 1.15rem; border-radius: 10px; font-weight: 700; font-size: 0.85rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);">
                    <i class="bx bx-plus" style="font-size: 1.1rem;"></i> Add Leave Type
                </button>
            </div>

            <table class="table-custom" style="width: 100%; border-collapse: collapse; font-size: 0.88rem;">
                <thead>
                    <tr style="background: #10b981; color: #ffffff;">
                        <th style="padding: 0.85rem; text-align: left;">Code & Name</th>
                        <th style="padding: 0.85rem; text-align: center;">Accrual Type</th>
                        <th style="padding: 0.85rem; text-align: center;">Max/Year</th>
                        <th style="padding: 0.85rem; text-align: center;">Max Lifetime</th>
                        <th style="padding: 0.85rem; text-align: center;">Gender / Marital</th>
                        <th style="padding: 0.85rem; text-align: center;">Status</th>
                        <th style="padding: 0.85rem; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaveTypes as $lt)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 0.85rem;">
                                <x-leave-type-badge :type="$lt" />
                                <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.2rem;">{{ $lt->description }}</div>
                            </td>
                            <td style="padding: 0.85rem; text-align: center; font-weight: 700; color: #334155;">{{ $lt->accrual_type }}</td>
                            <td style="padding: 0.85rem; text-align: center; font-weight: 700;">{{ $lt->max_days_per_year ?? '&mdash;' }}</td>
                            <td style="padding: 0.85rem; text-align: center; font-weight: 700;">{{ $lt->max_days_lifetime ?? '&mdash;' }}</td>
                            <td style="padding: 0.85rem; text-align: center; font-size: 0.78rem; color: #475569;">
                                {{ $lt->applicable_gender }} / {{ $lt->requires_marital_status }}
                            </td>
                            <td style="padding: 0.85rem; text-align: center;">
                                <button type="button" wire:click="toggleTypeActive({{ $lt->id }})" style="background: {{ $lt->is_active ? '#ecfdf5' : '#fef2f2' }}; color: {{ $lt->is_active ? '#059669' : '#ef4444' }}; border: 1px solid {{ $lt->is_active ? '#a7f3d0' : '#fee2e2' }}; padding: 0.25rem 0.65rem; border-radius: 12px; font-weight: 700; font-size: 0.75rem; cursor: pointer;">
                                    {{ $lt->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td style="padding: 0.85rem; text-align: center;">
                                <button type="button" wire:click="editType({{ $lt->id }})" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #2563eb; padding: 0.25rem 0.65rem; border-radius: 8px; font-weight: 700; font-size: 0.75rem; cursor: pointer;">Edit Caps</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($editingTypeId)
                <div style="margin-top: 1.5rem; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 12px; padding: 1.25rem;">
                    <h3 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-top: 0;">Edit Caps & Requirements</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155;">Max Days / Year</label>
                            <input type="number" step="0.5" wire:model="editMaxYear" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 8px;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155;">Max Days Lifetime</label>
                            <input type="number" step="0.5" wire:model="editMaxLifetime" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 8px;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155;">Min Service Years</label>
                            <input type="number" wire:model="editMinService" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 8px;">
                        </div>
                    </div>
                    <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                        <button type="button" wire:click="$set('editingTypeId', null)" style="background: #ffffff; border: 1px solid #cbd5e1; padding: 0.4rem 1rem; border-radius: 8px; font-weight: 600;">Cancel</button>
                        <button type="button" wire:click="saveType" style="background: #10b981; color: white; border: none; padding: 0.4rem 1.2rem; border-radius: 8px; font-weight: 700;">Save Changes</button>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- TAB 4: REPORTS & CSV EXPORT -->
    @if($activeTab === 'reports')
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
                <h2 style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0;">Leave Usage Reports</h2>
                <button type="button" wire:click="exportReportCsv" style="background: #10b981; color: #ffffff; border: none; padding: 0.6rem 1.25rem; border-radius: 10px; font-weight: 700; font-size: 0.88rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);">
                    <i class="bx bx-download"></i> Export CSV Report
                </button>
            </div>

            <!-- Filters -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; background: #f8fafc; padding: 1rem; border-radius: 12px; border: 1px solid #e2e8f0;">
                <div>
                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Status Filter</label>
                    <select wire:model.live="reportStatus" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 8px; background: #ffffff;">
                        <option value="">All Statuses</option>
                        <option value="Pending">Pending</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Leave Type</label>
                    <select wire:model.live="reportLeaveType" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 8px; background: #ffffff;">
                        <option value="">All Leave Types</option>
                        @foreach($leaveTypes as $t)
                            <option value="{{ $t->id }}">{{ $t->leave_code }} &mdash; {{ $t->leave_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">Start Date</label>
                    <input type="date" wire:model.live="reportStartDate" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 8px; background: #ffffff;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem;">End Date</label>
                    <input type="date" wire:model.live="reportEndDate" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 8px; background: #ffffff;">
                </div>
            </div>

            <!-- Report Results Table -->
            <table class="table-custom" style="width: 100%; border-collapse: collapse; font-size: 0.88rem;">
                <thead>
                    <tr style="background: #10b981; color: #ffffff;">
                        <th style="padding: 0.85rem; text-align: left;">Staff</th>
                        <th style="padding: 0.85rem; text-align: left;">Leave Type</th>
                        <th style="padding: 0.85rem; text-align: left;">Dates</th>
                        <th style="padding: 0.85rem; text-align: center;">Days</th>
                        <th style="padding: 0.85rem; text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportsQuery as $r)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 0.85rem; font-weight: 600; color: #0f172a;">{{ $r->user->name ?? 'N/A' }}</td>
                            <td style="padding: 0.85rem;"><x-leave-type-badge :type="$r->leaveType" /></td>
                            <td style="padding: 0.85rem;">{{ $r->start_date->format('d/m/Y') }} &mdash; {{ $r->end_date->format('d/m/Y') }}</td>
                            <td style="padding: 0.85rem; text-align: center; font-weight: 700; color: #2563eb;">{{ $r->total_days }}</td>
                            <td style="padding: 0.85rem; text-align: center;"><x-leave-status-badge :status="$r->status" /></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem; color: #94a3b8;">No report records found for selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if(method_exists($reportsQuery, 'links'))
                <div style="padding: 1rem 0 0 0;">
                    {{ $reportsQuery->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- Reject Remarks Modal -->
    @if($showRejectModal)
        <div style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(15, 23, 42, 0.5); backdrop-filter: blur(6px); display: flex; align-items: center; justify-content: center; z-index: 1100; padding: 1.5rem;">
            <div style="background: #ffffff; border-radius: 16px; width: 100%; max-width: 480px; padding: 1.75rem; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 0.5rem;">Reject Leave Request</h3>
                <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 1rem;">State remarks / reason for rejecting this leave request:</p>

                <textarea wire:model="rejectRemarks" rows="3" placeholder="Provide reason for rejection..." style="width: 100%; padding: 0.65rem 1rem; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 0.9rem; outline: none; margin-bottom: 1rem; font-family: inherit;"></textarea>
                @error('rejectRemarks') <span style="color: #ef4444; font-size: 0.75rem; display: block; margin-bottom: 0.75rem;">{{ $message }}</span> @enderror

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" wire:click="closeRejectModal" style="background: #ffffff; border: 1px solid #cbd5e1; color: #475569; padding: 0.6rem 1.15rem; border-radius: 8px; font-weight: 600; font-size: 0.88rem; cursor: pointer;">Cancel</button>
                    <button type="button" wire:click="confirmReject" style="background: #ef4444; color: #ffffff; border: none; border-radius: 8px; padding: 0.6rem 1.25rem; font-weight: 700; font-size: 0.88rem; cursor: pointer;">Confirm Rejection</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Create Leave Type Modal -->
    @if($showCreateModal)
        <div style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(15, 23, 42, 0.5); backdrop-filter: blur(6px); display: flex; align-items: center; justify-content: center; z-index: 1100; padding: 1.5rem; overflow-y: auto;">
            <div style="background: #ffffff; border-radius: 16px; width: 100%; max-width: 580px; padding: 1.75rem; box-shadow: 0 20px 40px rgba(0,0,0,0.15); max-height: 90vh; overflow-y: auto;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                    <h3 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin: 0;"><i class="bx bx-plus-circle" style="color: #10b981; vertical-align: middle;"></i> Add New Leave Type</h3>
                    <button type="button" wire:click="closeCreateModal" style="background: none; border: none; cursor: pointer; color: #64748b; font-size: 1.25rem;"><i class="bx bx-x"></i></button>
                </div>

                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Leave Code *</label>
                            <input type="text" wire:model="newLeaveCode" placeholder="e.g. AL" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; text-transform: uppercase;">
                            @error('newLeaveCode') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Leave Name *</label>
                            <input type="text" wire:model="newLeaveName" placeholder="e.g. Annual Leave" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem;">
                            @error('newLeaveName') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Description</label>
                        <input type="text" wire:model="newDescription" placeholder="Brief description of leave policy" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem;">
                        @error('newDescription') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Accrual Type *</label>
                            <select wire:model="newAccrualType" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; background: white;">
                                <option value="Monthly">Monthly</option>
                                <option value="Annual">Annual</option>
                                <option value="OneTime">OneTime</option>
                            </select>
                            @error('newAccrualType') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Max Days / Year</label>
                            <input type="number" step="0.5" wire:model="newMaxYear" placeholder="e.g. 12" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem;">
                            @error('newMaxYear') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Max Days Lifetime</label>
                            <input type="number" step="0.5" wire:model="newMaxLifetime" placeholder="e.g. 30" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem;">
                            @error('newMaxLifetime') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Applicable Gender</label>
                            <select wire:model="newApplicableGender" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; background: white;">
                                <option value="All">All Genders</option>
                                <option value="Male">Male Only</option>
                                <option value="Female">Female Only</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Marital Status</label>
                            <select wire:model="newRequiresMaritalStatus" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; background: white;">
                                <option value="Any">Any Status</option>
                                <option value="Married">Married Only</option>
                                <option value="Single">Single Only</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Min Service (Years)</label>
                            <input type="number" wire:model="newMinServiceYears" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem;">
                        </div>
                    </div>

                    <div style="display: flex; gap: 1.5rem; margin-top: 0.5rem;">
                        <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.85rem; font-weight: 600; color: #334155; cursor: pointer;">
                            <input type="checkbox" wire:model="newCarryForward" style="accent-color: #10b981; width: 16px; height: 16px;"> Carry Forward to Next Year
                        </label>
                        <label style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.85rem; font-weight: 600; color: #334155; cursor: pointer;">
                            <input type="checkbox" wire:model="newIsLifetimeOnly" style="accent-color: #10b981; width: 16px; height: 16px;"> Lifetime Only Allowance
                        </label>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
                    <button type="button" wire:click="closeCreateModal" style="background: #ffffff; border: 1px solid #cbd5e1; color: #475569; padding: 0.6rem 1.15rem; border-radius: 8px; font-weight: 600; font-size: 0.88rem; cursor: pointer;">Cancel</button>
                    <button type="button" wire:click="createType" style="background: #10b981; color: #ffffff; border: none; border-radius: 8px; padding: 0.6rem 1.25rem; font-weight: 700; font-size: 0.88rem; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);">Save Leave Type</button>
                </div>
            </div>
        </div>
    @endif
</div>
