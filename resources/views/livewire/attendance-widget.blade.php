<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- Notifications -->
    @if($successMessage)
        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #059669; padding: 0.85rem 1.15rem; border-radius: 12px; font-size: 0.88rem; font-weight: 600; display: flex; align-items: center; justify-content: space-between;">
            <div><i class="bx bx-check-circle" style="font-size: 1.1rem; vertical-align: middle; margin-right: 0.35rem;"></i> {{ $successMessage }}</div>
            <button type="button" wire:click="$set('successMessage', '')" style="background: none; border: none; cursor: pointer; color: #059669;"><i class="bx bx-x"></i></button>
        </div>
    @endif

    @if($errorMessage)
        <div style="background: #fef2f2; border: 1px solid #fee2e2; color: #ef4444; padding: 0.85rem 1.15rem; border-radius: 12px; font-size: 0.88rem; font-weight: 600; display: flex; align-items: center; justify-content: space-between;">
            <div><i class="bx bx-error-circle" style="font-size: 1.1rem; vertical-align: middle; margin-right: 0.35rem;"></i> {{ $errorMessage }}</div>
            <button type="button" wire:click="$set('errorMessage', '')" style="background: none; border: none; cursor: pointer; color: #ef4444;"><i class="bx bx-x"></i></button>
        </div>
    @endif

    <!-- MAIN ATTENDANCE CARD -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
            <div>
                <h2 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 0.4rem;">
                    <i class="bx bx-time" style="color: #10b981;"></i> Mark Daily Attendance
                </h2>
                <p style="color: #64748b; font-size: 0.82rem; margin-top: 0.25rem; margin-bottom: 0;">
                    Today is <strong>{{ now()->format('l, F d, Y') }}</strong>
                </p>
            </div>

            <!-- Current Attendance Status Badge -->
            <div>
                @if(!$todayAttendance)
                    <span style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; padding: 0.35rem 0.85rem; border-radius: 20px; font-size: 0.82rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;">
                        <i class="bx bx-minus-circle"></i> Not Clocked In Today
                    </span>
                @elseif($todayAttendance->clock_in && !$todayAttendance->clock_out)
                    <span style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 0.35rem 0.85rem; border-radius: 20px; font-size: 0.82rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;">
                        <i class="bx bx-check-double"></i> Clocked In ({{ $todayAttendance->status }})
                    </span>
                @else
                    <span style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; padding: 0.35rem 0.85rem; border-radius: 20px; font-size: 0.82rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;">
                        <i class="bx bx-log-out-circle"></i> Clocked Out & Done
                    </span>
                @endif
            </div>
        </div>

        <!-- Clock Details & Action Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; align-items: center; background: #f8fafc; padding: 1.25rem; border-radius: 14px; border: 1px solid #e2e8f0;">
            <!-- Timings display -->
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; background: #ffffff; padding: 0.65rem 0.85rem; border-radius: 10px; border: 1px solid #e2e8f0;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #475569;"><i class="bx bx-log-in-circle" style="color: #10b981; font-size: 1rem; vertical-align: middle;"></i> Clock In</span>
                    <strong style="font-size: 0.95rem; color: #0f172a;">{{ $todayAttendance ? $todayAttendance->formatted_clock_in : '--:--' }}</strong>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; background: #ffffff; padding: 0.65rem 0.85rem; border-radius: 10px; border: 1px solid #e2e8f0;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #475569;"><i class="bx bx-log-out-circle" style="color: #ef4444; font-size: 1rem; vertical-align: middle;"></i> Clock Out</span>
                    <strong style="font-size: 0.95rem; color: #0f172a;">{{ $todayAttendance ? $todayAttendance->formatted_clock_out : '--:--' }}</strong>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; background: #ffffff; padding: 0.65rem 0.85rem; border-radius: 10px; border: 1px solid #e2e8f0;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: #475569;"><i class="bx bx-stopwatch" style="color: #3b82f6; font-size: 1rem; vertical-align: middle;"></i> Working Hours</span>
                    <strong style="font-size: 0.95rem; color: #2563eb;">{{ $todayAttendance ? $todayAttendance->working_hours : '--' }}</strong>
                </div>
            </div>

            <!-- Notes & Buttons -->
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <input type="text" wire:model="notes" placeholder="Add attendance note / remark (optional)..." style="width: 100%; padding: 0.6rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 0.85rem; outline: none; background: white;">

                <div style="display: flex; gap: 0.75rem;">
                    @if(!$todayAttendance || !$todayAttendance->clock_in)
                        <button type="button" wire:click="clockIn" style="flex: 1; background: #10b981; color: #ffffff; border: none; padding: 0.75rem 1rem; border-radius: 10px; font-weight: 800; font-size: 0.9rem; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25); transition: all 0.2s;">
                            <i class="bx bx-log-in" style="font-size: 1.2rem;"></i> CLOCK IN
                        </button>
                    @elseif($todayAttendance->clock_in && !$todayAttendance->clock_out)
                        <button type="button" wire:click="clockOut" style="flex: 1; background: #ef4444; color: #ffffff; border: none; padding: 0.75rem 1rem; border-radius: 10px; font-weight: 800; font-size: 0.9rem; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25); transition: all 0.2s;">
                            <i class="bx bx-log-out" style="font-size: 1.2rem;"></i> CLOCK OUT
                        </button>
                    @else
                        <button type="button" disabled style="flex: 1; background: #94a3b8; color: #ffffff; border: none; padding: 0.75rem 1rem; border-radius: 10px; font-weight: 700; font-size: 0.88rem; cursor: not-allowed; display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem;">
                            <i class="bx bx-check" style="font-size: 1.2rem;"></i> DAY COMPLETED
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- MONTHLY SUMMARY STATS & RECENT LOGS -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">

        <!-- Monthly Summary Card -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);">
            <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.4rem;">
                <i class="bx bx-pie-chart-alt-2" style="color: #3b82f6;"></i> Monthly Attendance Summary ({{ now()->format('F Y') }})
            </h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem;">
                <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 12px; padding: 0.85rem; text-align: center;">
                    <span style="font-size: 0.72rem; font-weight: 700; color: #059669; text-transform: uppercase;">Present Days</span>
                    <h2 style="font-size: 1.5rem; font-weight: 800; color: #059669; margin: 0.2rem 0 0 0;">{{ $presentDays }}</h2>
                </div>

                <div style="background: #fef2f2; border: 1px solid #fee2e2; border-radius: 12px; padding: 0.85rem; text-align: center;">
                    <span style="font-size: 0.72rem; font-weight: 700; color: #ef4444; text-transform: uppercase;">Absent Days</span>
                    <h2 style="font-size: 1.5rem; font-weight: 800; color: #ef4444; margin: 0.2rem 0 0 0;">{{ $absentDays }}</h2>
                </div>

                <div style="background: #f3e8ff; border: 1px solid #e9d5ff; border-radius: 12px; padding: 0.85rem; text-align: center;">
                    <span style="font-size: 0.72rem; font-weight: 700; color: #9333ea; text-transform: uppercase;">Half Days</span>
                    <h2 style="font-size: 1.5rem; font-weight: 800; color: #9333ea; margin: 0.2rem 0 0 0;">{{ $halfDays }}</h2>
                </div>

                <div style="background: #fffbeb; border: 1px solid #fef3c7; border-radius: 12px; padding: 0.85rem; text-align: center;">
                    <span style="font-size: 0.72rem; font-weight: 700; color: #d97706; text-transform: uppercase;">On Leave</span>
                    <h2 style="font-size: 1.5rem; font-weight: 800; color: #d97706; margin: 0.2rem 0 0 0;">{{ $leaveDays }}</h2>
                </div>
            </div>
        </div>

        <!-- Recent Logs Table -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);">
            <h3 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.4rem;">
                <i class="bx bx-history" style="color: #6366f1;"></i> Recent 7 Days Logs
            </h3>

            <table class="table-custom" style="width: 100%; border-collapse: collapse; font-size: 0.82rem;">
                <thead>
                    <tr style="background: #10b981; color: #ffffff;">
                        <th style="padding: 0.6rem 0.75rem; text-align: left;">Date</th>
                        <th style="padding: 0.6rem 0.75rem; text-align: center;">Clock In</th>
                        <th style="padding: 0.6rem 0.75rem; text-align: center;">Clock Out</th>
                        <th style="padding: 0.6rem 0.75rem; text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentAttendance as $log)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 0.65rem 0.75rem; font-weight: 700; color: #0f172a;">
                                {{ $log->date->format('M d, Y') }}
                            </td>
                            <td style="padding: 0.65rem 0.75rem; text-align: center; color: #334155; font-weight: 600;">
                                {{ $log->formatted_clock_in }}
                            </td>
                            <td style="padding: 0.65rem 0.75rem; text-align: center; color: #334155; font-weight: 600;">
                                {{ $log->formatted_clock_out }}
                            </td>
                            <td style="padding: 0.65rem 0.75rem; text-align: center;">
                                @if($log->status === 'Present')
                                    <span style="background: #ecfdf5; color: #059669; padding: 0.15rem 0.5rem; border-radius: 10px; font-weight: 700; font-size: 0.72rem;">Present</span>
                                @elseif($log->status === 'Late')
                                    <span style="background: #fffbeb; color: #d97706; padding: 0.15rem 0.5rem; border-radius: 10px; font-weight: 700; font-size: 0.72rem;">Late</span>
                                @elseif($log->status === 'HalfDay')
                                    <span style="background: #f3e8ff; color: #9333ea; padding: 0.15rem 0.5rem; border-radius: 10px; font-weight: 700; font-size: 0.72rem;">Half Day</span>
                                @elseif($log->status === 'Absent')
                                    <span style="background: #fef2f2; color: #ef4444; padding: 0.15rem 0.5rem; border-radius: 10px; font-weight: 700; font-size: 0.72rem;">Absent</span>
                                @else
                                    <span style="background: #eff6ff; color: #2563eb; padding: 0.15rem 0.5rem; border-radius: 10px; font-weight: 700; font-size: 0.72rem;">{{ $log->status }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 1.5rem; color: #94a3b8;">No attendance records logged yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
