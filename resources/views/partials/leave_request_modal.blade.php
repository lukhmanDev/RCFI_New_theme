<!-- Leave Request Modal Dialog -->
<div id="leaveRequestModal" onclick="if(event.target === this) closeLeaveRequestModal()" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(15, 23, 42, 0.5); backdrop-filter: blur(6px); display: none; align-items: flex-start; justify-content: center; z-index: 1000; overflow-y: auto; padding: 2rem 1rem;">
    <div class="panel" style="width: 100%; max-width: 580px; margin: auto; position: relative; box-shadow: 0 20px 40px rgba(15, 23, 42, 0.15); border-color: var(--panel-border); background: #ffffff; border-radius: 16px; padding: 0 2.25rem 2rem; overflow: visible;">

        <!-- Sticky Header -->
        <div style="position: sticky; top: 0; background: #ffffff; z-index: 30; padding: 1.75rem 0 1rem 0; border-bottom: 1px solid #e2e8f0; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <h2 class="panel-title" style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0;"><i class="bx bx-calendar-event" style="color:#f59e0b; margin-right:0.4rem;"></i> Request Leave</h2>
                <p style="color:#64748b; font-size:0.85rem; margin: 0.25rem 0 0;">Submit your leave application for management approval.</p>
            </div>
            <button type="button" onclick="closeLeaveRequestModal()" style="background: #f1f5f9; border: none; color: #64748b; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; cursor: pointer; transition: all 0.2s ease; flex-shrink: 0;" onmouseover="this.style.background='#e2e8f0'; this.style.color='#0f172a';" onmouseout="this.style.background='#f1f5f9'; this.style.color='#64748b';">
                <i class="bx bx-x"></i>
            </button>
        </div>

        <form action="{{ route('leave.request') }}" method="POST">
            @csrf

            <div style="display: flex; flex-direction: column; gap: 1.15rem; margin-bottom: 1.5rem;">
                @if(Auth::user() && (Auth::user()->isSuperAdmin() || Auth::user()->isCoo() || (bool)Auth::user()->is_hr || Auth::user()->isHod()))
                    <div>
                        <label class="form-label" for="leave_target_user_id" style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 0.4rem;">Apply For Staff Member Profile</label>
                        <select class="form-select-dark" id="leave_target_user_id" name="user_id" style="width: 100%; padding: 0.65rem 1rem; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 0.9rem; outline: none; background: #ffffff; color: #0f172a;">
                            <option value="{{ Auth::id() }}">Myself ({{ Auth::user()->name }})</option>
                            @php
                                $staffOptions = (Auth::user()->isSuperAdmin() || Auth::user()->isCoo() || (bool)Auth::user()->is_hr)
                                    ? \App\Models\User::nonSuperAdmin()->where('id', '!=', Auth::id())->orderBy('name')->get()
                                    : \App\Models\User::nonSuperAdmin()->where('assigned_hod_id', Auth::id())->where('id', '!=', Auth::id())->orderBy('name')->get();
                            @endphp
                            @foreach($staffOptions as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->role_name }} &bull; {{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label class="form-label" for="leave_type_id" style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 0.4rem;">Leave Type <span style="color:#ef4444;">*</span></label>
                    <select class="form-select-dark" id="leave_type_id" name="leave_type_id" required style="width: 100%; padding: 0.65rem 1rem; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 0.9rem; outline: none; background: #ffffff; color: #0f172a;">
                        <option value="" disabled selected>Select type of leave</option>
                        @foreach(\App\Models\LeaveType::where('is_active', true)->get() as $lt)
                            @if(Auth::user() && Auth::user()->isEligibleFor($lt))
                                <option value="{{ $lt->id }}">{{ $lt->leave_name }} ({{ $lt->leave_code }})</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label class="form-label" for="leave_from_date" style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 0.4rem;">From Date <span style="color:#ef4444;">*</span></label>
                        <input type="date" class="form-control-dark" id="leave_from_date" name="from_date" required onchange="calculateLeaveDays()" style="width: 100%; padding: 0.65rem 1rem; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 0.9rem; outline: none; background: #ffffff; color: #0f172a;">
                    </div>
                    <div>
                        <label class="form-label" for="leave_to_date" style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 0.4rem;">To Date <span style="color:#ef4444;">*</span></label>
                        <input type="date" class="form-control-dark" id="leave_to_date" name="to_date" required onchange="calculateLeaveDays()" style="width: 100%; padding: 0.65rem 1rem; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 0.9rem; outline: none; background: #ffffff; color: #0f172a;">
                    </div>
                </div>

                <!-- Half Day Leave Option -->
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.85rem 1rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <label for="is_half_day_checkbox" style="font-size: 0.85rem; font-weight: 700; color: #334155; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" id="is_half_day_checkbox" name="is_half_day" value="1" onchange="toggleHalfDaySessionOptions(); calculateLeaveDays();" style="width: 17px; height: 17px; accent-color: #f59e0b; cursor: pointer;">
                            Apply for Half Day Leave
                        </label>
                        <span style="background: #f3e8ff; color: #9333ea; font-size: 0.72rem; font-weight: 800; padding: 0.15rem 0.55rem; border-radius: 8px;">0.5 Day</span>
                    </div>

                    <div id="half_day_session_container" style="display: none; margin-top: 0.75rem; border-top: 1px dashed #cbd5e1; padding-top: 0.65rem;">
                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.35rem;">Half Day Session:</label>
                        <div style="display: flex; gap: 1rem;">
                            <label style="font-size: 0.82rem; font-weight: 600; color: #1e293b; cursor: pointer; display: flex; align-items: center; gap: 0.35rem;">
                                <input type="radio" name="half_day_session" value="First Half" checked style="accent-color: #f59e0b;"> First Half (Morning)
                            </label>
                            <label style="font-size: 0.82rem; font-weight: 600; color: #1e293b; cursor: pointer; display: flex; align-items: center; gap: 0.35rem;">
                                <input type="radio" name="half_day_session" value="Second Half" style="accent-color: #f59e0b;"> Second Half (Afternoon)
                            </label>
                        </div>
                    </div>
                </div>

                <div id="leave_duration_box" style="display: none; background: #fffbeb; border: 1px solid #fef3c7; color: #d97706; padding: 0.65rem 1rem; border-radius: 10px; font-size: 0.85rem; font-weight: 600;">
                    <i class="bx bx-time-five" style="vertical-align: middle; font-size: 1rem;"></i> Total Duration: <span id="leave_days_count">0</span> day(s)
                </div>

                <div>
                    <label class="form-label" for="leave_reason" style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 0.4rem;">Reason / Remarks <span style="color:#ef4444;">*</span></label>
                    <textarea class="form-control-dark" id="leave_reason" name="reason" rows="3" placeholder="State reason for your leave request..." required style="width: 100%; padding: 0.65rem 1rem; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 0.9rem; outline: none; background: #ffffff; color: #0f172a; font-family: inherit; resize: vertical;"></textarea>
                </div>
            </div>

            <!-- Sticky Footer -->
            <div style="position: sticky; bottom: 0; background: #ffffff; z-index: 30; padding: 1rem 0 0.5rem 0; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" onclick="closeLeaveRequestModal()" style="background: #ffffff; border: 1px solid #cbd5e1; color: #475569; padding: 0.65rem 1.25rem; border-radius: 10px; font-weight: 600; font-size: 0.88rem; cursor: pointer;">
                    Cancel
                </button>
                <button type="submit" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #ffffff; border: none; border-radius: 10px; padding: 0.65rem 1.5rem; font-weight: 600; font-size: 0.88rem; cursor: pointer; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);">
                    <i class="bx bx-paper-plane" style="font-size: 1.1rem; vertical-align: middle; margin-right: 0.25rem;"></i> Submit Request
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openLeaveRequestModal() {
        const modal = document.getElementById('leaveRequestModal');
        if (modal) modal.style.display = 'flex';
    }

    function closeLeaveRequestModal() {
        const modal = document.getElementById('leaveRequestModal');
        if (modal) modal.style.display = 'none';
    }

    function toggleHalfDaySessionOptions() {
        const checkbox = document.getElementById('is_half_day_checkbox');
        const container = document.getElementById('half_day_session_container');
        if (checkbox && container) {
            container.style.display = checkbox.checked ? 'block' : 'none';
        }
    }

    function calculateLeaveDays() {
        const fromVal = document.getElementById('leave_from_date').value;
        const toVal = document.getElementById('leave_to_date').value;
        const isHalfDay = document.getElementById('is_half_day_checkbox')?.checked;
        const box = document.getElementById('leave_duration_box');
        const countSpan = document.getElementById('leave_days_count');

        if (fromVal && toVal) {
            const d1 = new Date(fromVal);
            const d2 = new Date(toVal);
            if (d2 >= d1) {
                const diffTime = Math.abs(d2 - d1);
                let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                if (isHalfDay) {
                    diffDays = d1.getTime() === d2.getTime() ? 0.5 : Math.max(0.5, diffDays - 0.5);
                }
                countSpan.innerText = diffDays + (isHalfDay ? ' (Half Day)' : '');
                box.style.display = 'block';
            } else {
                box.style.display = 'none';
            }
        } else {
            box.style.display = 'none';
        }
    }

    window.openLeaveRequestModal = openLeaveRequestModal;
    window.closeLeaveRequestModal = closeLeaveRequestModal;

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLeaveRequestModal();
        }
    });
</script>
