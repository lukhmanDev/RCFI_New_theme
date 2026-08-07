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

    function calculateLeaveDays() {
        const fromVal = document.getElementById('leave_from_date').value;
        const toVal = document.getElementById('leave_to_date').value;
        const box = document.getElementById('leave_duration_box');
        const countSpan = document.getElementById('leave_days_count');

        if (fromVal && toVal) {
            const d1 = new Date(fromVal);
            const d2 = new Date(toVal);
            if (d2 >= d1) {
                const diffTime = Math.abs(d2 - d1);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                countSpan.innerText = diffDays;
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
