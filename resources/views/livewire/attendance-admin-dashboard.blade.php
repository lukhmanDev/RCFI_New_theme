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

    <!-- Header & Nav Tabs -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="color: #0f172a; font-size: 1.75rem; font-weight: 800; margin: 0;">Attendance Management</h1>
            <p style="color: #64748b; font-size: 0.88rem; margin-top: 0.25rem; margin-bottom: 0;">Mark daily staff attendance, view monthly register matrix, and generate attendance reports.</p>
        </div>

        <div style="display: flex; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 0.25rem; gap: 0.25rem; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
            <button type="button" wire:click="setTab('daily')" style="padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.85rem; font-weight: 700; border: none; cursor: pointer; transition: all 0.2s ease; {{ $activeTab === 'daily' ? 'background: #10b981; color: #ffffff;' : 'background: transparent; color: #64748b;' }}">
                <i class="bx bx-calendar-check" style="vertical-align: middle;"></i> Daily Attendance Sheet
            </button>
            <button type="button" wire:click="setTab('monthly')" style="padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.85rem; font-weight: 700; border: none; cursor: pointer; transition: all 0.2s ease; {{ $activeTab === 'monthly' ? 'background: #10b981; color: #ffffff;' : 'background: transparent; color: #64748b;' }}">
                <i class="bx bx-grid-alt" style="vertical-align: middle;"></i> Monthly Matrix Register
            </button>
            <button type="button" wire:click="setTab('reports')" style="padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.85rem; font-weight: 700; border: none; cursor: pointer; transition: all 0.2s ease; {{ $activeTab === 'reports' ? 'background: #10b981; color: #ffffff;' : 'background: transparent; color: #64748b;' }}">
                <i class="bx bx-download" style="vertical-align: middle;"></i> Export Reports
            </button>
        </div>
    </div>

    <!-- Overview Stat Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1rem; display: flex; align-items: center; gap: 0.85rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);">
            <div style="background: #eff6ff; color: #3b82f6; width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
                <i class="bx bx-group"></i>
            </div>
            <div>
                <span style="font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Total Staff</span>
                <h3 style="font-size: 1.35rem; font-weight: 800; color: #0f172a; margin: 0;">{{ $totalStaff }}</h3>
            </div>
        </div>

        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1rem; display: flex; align-items: center; gap: 0.85rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);">
            <div style="background: #ecfdf5; color: #059669; width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
                <i class="bx bx-user-check"></i>
            </div>
            <div>
                <span style="font-size: 0.72rem; font-weight: 700; color: #059669; text-transform: uppercase;">Present Today</span>
                <h3 style="font-size: 1.35rem; font-weight: 800; color: #059669; margin: 0;">{{ $presentToday }}</h3>
            </div>
        </div>

        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1rem; display: flex; align-items: center; gap: 0.85rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);">
            <div style="background: #fffbeb; color: #d97706; width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
                <i class="bx bx-time"></i>
            </div>
            <div>
                <span style="font-size: 0.72rem; font-weight: 700; color: #d97706; text-transform: uppercase;">Late Today</span>
                <h3 style="font-size: 1.35rem; font-weight: 800; color: #d97706; margin: 0;">{{ $lateToday }}</h3>
            </div>
        </div>

        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1rem; display: flex; align-items: center; gap: 0.85rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);">
            <div style="background: #fef2f2; color: #ef4444; width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
                <i class="bx bx-user-x"></i>
            </div>
            <div>
                <span style="font-size: 0.72rem; font-weight: 700; color: #ef4444; text-transform: uppercase;">Absent Today</span>
                <h3 style="font-size: 1.35rem; font-weight: 800; color: #ef4444; margin: 0;">{{ $absentToday }}</h3>
            </div>
        </div>

        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1rem; display: flex; align-items: center; gap: 0.85rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);">
            <div style="background: #f3e8ff; color: #9333ea; width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
                <i class="bx bx-calendar"></i>
            </div>
            <div>
                <span style="font-size: 0.72rem; font-weight: 700; color: #9333ea; text-transform: uppercase;">On Leave Today</span>
                <h3 style="font-size: 1.35rem; font-weight: 800; color: #9333ea; margin: 0;">{{ $onLeaveToday }}</h3>
            </div>
        </div>
    </div>

    <!-- TAB 1: DAILY ATTENDANCE MARKING SHEET -->
    @if($activeTab === 'daily')
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02); overflow: hidden;">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; background: #f8fafc;">
                <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #475569; margin-bottom: 0.2rem;">Select Date</label>
                        <input type="date" wire:model.live="selectedDate" style="padding: 0.5rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-weight: 700; color: #0f172a; background: white;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #475569; margin-bottom: 0.2rem;">Search Staff</label>
                        <input type="text" wire:model.live="searchStaff" placeholder="Search by name/email..." style="padding: 0.5rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.85rem; background: white;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #475569; margin-bottom: 0.2rem;">Filter Role</label>
                        <select wire:model.live="roleFilter" style="padding: 0.5rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.85rem; background: white;">
                            <option value="">All Roles</option>
                            <option value="super_admin">Super Admin</option>
                            <option value="coo">COO</option>
                            <option value="hod">HOD</option>
                            <option value="project_manager">Project Manager</option>
                            <option value="engineer">Engineer</option>
                            <option value="social_aid">Social Aid</option>
                            <option value="reception">Reception</option>
                            <option value="others">Others</option>
                        </select>
                    </div>
                </div>

                <div style="display: flex; gap: 0.5rem;">
                    <button type="button" wire:click="markAllPresent" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #2563eb; padding: 0.55rem 1rem; border-radius: 8px; font-weight: 700; font-size: 0.82rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem;">
                        <i class="bx bx-check-double"></i> Mark All Present
                    </button>

                    <button type="button" wire:click="saveAllDailyRecords" style="background: #10b981; color: #ffffff; border: none; padding: 0.55rem 1.25rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);">
                        <i class="bx bx-save"></i> Save All Records
                    </button>
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table class="table-custom" style="width: 100%; border-collapse: collapse; font-size: 0.88rem;">
                    <thead>
                        <tr style="background: #10b981; color: #ffffff;">
                            <th style="padding: 0.85rem 1.25rem; text-align: left;">Staff Member</th>
                            <th style="padding: 0.85rem 1.25rem; text-align: center;">Status</th>
                            <th style="padding: 0.85rem 1.25rem; text-align: center;">Clock In</th>
                            <th style="padding: 0.85rem 1.25rem; text-align: center;">Clock Out</th>
                            <th style="padding: 0.85rem 1.25rem; text-align: left;">Remarks / Notes</th>
                            <th style="padding: 0.85rem 1.25rem; text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 0.85rem 1.25rem;">
                                    <div style="font-weight: 700; color: #0f172a;">{{ $u->name }}</div>
                                    <div style="font-size: 0.78rem; color: #64748b;">{{ $u->designation ?? $u->email }} &bull; <strong style="color: #10b981;">{{ strtoupper($u->role) }}</strong></div>
                                </td>

                                <td style="padding: 0.85rem 1.25rem; text-align: center;">
                                    <select wire:model="attendanceData.{{ $u->id }}.status" style="padding: 0.4rem 0.65rem; border: 1px solid #cbd5e1; border-radius: 8px; font-weight: 700; font-size: 0.82rem; background: white;">
                                        <option value="Present">Present</option>
                                        <option value="Late">Late</option>
                                        <option value="HalfDay">Half Day</option>
                                        <option value="Absent">Absent</option>
                                        <option value="OnLeave">On Leave</option>
                                        <option value="Holiday">Holiday</option>
                                    </select>
                                </td>

                                <td style="padding: 0.85rem 1.25rem; text-align: center;">
                                    <input type="time" wire:model="attendanceData.{{ $u->id }}.clock_in" style="padding: 0.4rem 0.5rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.82rem;">
                                </td>

                                <td style="padding: 0.85rem 1.25rem; text-align: center;">
                                    <input type="time" wire:model="attendanceData.{{ $u->id }}.clock_out" style="padding: 0.4rem 0.5rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.82rem;">
                                </td>

                                <td style="padding: 0.85rem 1.25rem;">
                                    <input type="text" wire:model="attendanceData.{{ $u->id }}.notes" placeholder="Optional notes..." style="width: 100%; padding: 0.4rem 0.65rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.82rem;">
                                </td>

                                <td style="padding: 0.85rem 1.25rem; text-align: center;">
                                    <button type="button" wire:click="saveDailyRecord({{ $u->id }})" style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #059669; padding: 0.35rem 0.75rem; border-radius: 6px; font-weight: 700; font-size: 0.78rem; cursor: pointer;">
                                        <i class="bx bx-check"></i> Save
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 2.5rem; color: #94a3b8;">No staff members matching filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($users, 'links'))
                <div style="padding: 1rem 1.5rem; border-top: 1px solid #e2e8f0;">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- TAB 2: MONTHLY MATRIX REGISTER -->
    @if($activeTab === 'monthly')
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
                <h2 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0;">
                    Monthly Register Matrix &mdash; {{ \Carbon\Carbon::create($matrixYear, $matrixMonth, 1)->format('F Y') }}
                </h2>
                <div style="display: flex; gap: 0.5rem;">
                    <button type="button" wire:click="prevMatrixMonth" style="background: #f1f5f9; border: 1px solid #cbd5e1; padding: 0.4rem 0.85rem; border-radius: 8px; font-weight: 700; cursor: pointer; color: #334155;"><i class="bx bx-chevron-left"></i> Prev</button>
                    <button type="button" wire:click="nextMatrixMonth" style="background: #f1f5f9; border: 1px solid #cbd5e1; padding: 0.4rem 0.85rem; border-radius: 8px; font-weight: 700; cursor: pointer; color: #334155;">Next <i class="bx bx-chevron-right"></i></button>
                </div>
            </div>

            <!-- Legend Bar -->
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem; background: #f8fafc; padding: 0.75rem 1rem; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 0.78rem; font-weight: 700;">
                <span style="display: inline-flex; align-items: center; gap: 0.35rem;"><span style="width: 12px; height: 12px; background: #10b981; border-radius: 3px; display: inline-block;"></span> P = Present</span>
                <span style="display: inline-flex; align-items: center; gap: 0.35rem;"><span style="width: 12px; height: 12px; background: #f59e0b; border-radius: 3px; display: inline-block;"></span> L = Late</span>
                <span style="display: inline-flex; align-items: center; gap: 0.35rem;"><span style="width: 12px; height: 12px; background: #9333ea; border-radius: 3px; display: inline-block;"></span> H = Half Day</span>
                <span style="display: inline-flex; align-items: center; gap: 0.35rem;"><span style="width: 12px; height: 12px; background: #ef4444; border-radius: 3px; display: inline-block;"></span> A = Absent</span>
                <span style="display: inline-flex; align-items: center; gap: 0.35rem;"><span style="width: 12px; height: 12px; background: #3b82f6; border-radius: 3px; display: inline-block;"></span> O = On Leave</span>
            </div>

            <div style="overflow-x: auto;">
                <table class="table-custom" style="width: 100%; border-collapse: collapse; font-size: 0.75rem;">
                    <thead>
                        <tr style="background: #10b981; color: #ffffff;">
                            <th style="padding: 0.5rem; text-align: left; min-width: 140px;">Staff Member</th>
                            @for($d = 1; $d <= $daysInMonth; $d++)
                                <th style="padding: 0.4rem; text-align: center; width: 26px;">{{ $d }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $u)
                            @php
                                $userLogs = $monthAttendances->get($u->id, collect())->keyBy(function($item) {
                                    return (int)$item->date->format('d');
                                });
                            @endphp
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 0.5rem; font-weight: 700; color: #0f172a;">{{ $u->name }}</td>
                                @for($d = 1; $d <= $daysInMonth; $d++)
                                    @php $att = $userLogs->get($d); @endphp
                                    <td style="padding: 0.2rem; text-align: center;">
                                        @if($att)
                                            @if($att->status === 'Present')
                                                <span style="background: #ecfdf5; color: #059669; font-weight: 800; border-radius: 4px; padding: 0.15rem 0.3rem; display: block;" title="Present: {{ $att->formatted_clock_in }}">P</span>
                                            @elseif($att->status === 'Late')
                                                <span style="background: #fffbeb; color: #d97706; font-weight: 800; border-radius: 4px; padding: 0.15rem 0.3rem; display: block;" title="Late: {{ $att->formatted_clock_in }}">L</span>
                                            @elseif($att->status === 'HalfDay')
                                                <span style="background: #f3e8ff; color: #9333ea; font-weight: 800; border-radius: 4px; padding: 0.15rem 0.3rem; display: block;" title="Half Day">H</span>
                                            @elseif($att->status === 'Absent')
                                                <span style="background: #fef2f2; color: #ef4444; font-weight: 800; border-radius: 4px; padding: 0.15rem 0.3rem; display: block;" title="Absent">A</span>
                                            @else
                                                <span style="background: #eff6ff; color: #2563eb; font-weight: 800; border-radius: 4px; padding: 0.15rem 0.3rem; display: block;" title="{{ $att->status }}">O</span>
                                            @endif
                                        @else
                                            <span style="color: #cbd5e1;">&bull;</span>
                                        @endif
                                    </td>
                                @endfor
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- TAB 3: REPORTS & CSV EXPORT -->
    @if($activeTab === 'reports')
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
                <div>
                    <h2 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0;">Attendance CSV Export</h2>
                    <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.2rem;">Download raw attendance logs including Clock In, Clock Out, and Total Working Hours.</p>
                </div>

                <button type="button" wire:click="exportReportCsv" style="background: #10b981; color: #ffffff; border: none; padding: 0.65rem 1.35rem; border-radius: 10px; font-weight: 700; font-size: 0.88rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);">
                    <i class="bx bx-download" style="font-size: 1.1rem;"></i> Export Month CSV ({{ \Carbon\Carbon::create($matrixYear, $matrixMonth, 1)->format('F Y') }})
                </button>
            </div>
        </div>
    @endif
</div>
