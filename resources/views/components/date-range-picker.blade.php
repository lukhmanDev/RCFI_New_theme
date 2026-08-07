@props([
    'startDateName' => 'start_date',
    'endDateName'   => 'end_date',
    'startDateVal'  => '',
    'endDateVal'    => '',
])

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;" x-data="{
    start: '{{ $startDateVal }}',
    end: '{{ $endDateVal }}',
    workingDays: 0,
    calculateDays() {
        if (!this.start || !this.end) {
            this.workingDays = 0;
            return;
        }
        let d1 = new Date(this.start);
        let d2 = new Date(this.end);
        if (d2 < d1) {
            this.workingDays = 0;
            return;
        }
        let count = 0;
        let cur = new Date(d1);
        while (cur <= d2) {
            let day = cur.getDay();
            if (day !== 0 && day !== 6) { // Exclude Sunday (0) and Saturday (6)
                count++;
            }
            cur.setDate(cur.getDate() + 1);
        }
        this.workingDays = count > 0 ? count : (Math.ceil((d2 - d1) / (1000 * 60 * 60 * 24)) + 1);
    }
}" x-init="calculateDays()">
    <div>
        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 0.4rem;">Start Date <span style="color:#ef4444;">*</span></label>
        <input type="date" name="{{ $startDateName }}" wire:model.live="{{ $startDateName }}" x-model="start" @change="calculateDays()" required style="width: 100%; padding: 0.65rem 1rem; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 0.9rem; outline: none; background: #ffffff; color: #0f172a;">
    </div>
    <div>
        <label style="display: block; font-size: 0.85rem; font-weight: 700; color: #334155; margin-bottom: 0.4rem;">End Date <span style="color:#ef4444;">*</span></label>
        <input type="date" name="{{ $endDateName }}" wire:model.live="{{ $endDateName }}" x-model="end" @change="calculateDays()" required style="width: 100%; padding: 0.65rem 1rem; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 0.9rem; outline: none; background: #ffffff; color: #0f172a;">
    </div>

    <div x-show="workingDays > 0" style="grid-column: span 2; background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; padding: 0.6rem 1rem; border-radius: 10px; font-size: 0.85rem; font-weight: 600; display: flex; align-items: center; gap: 0.4rem;">
        <i class="bx bx-time-five" style="font-size: 1rem;"></i> Estimated Working Days: <span x-text="workingDays" style="font-weight: 800;"></span> day(s) <span style="font-size: 0.75rem; color: #60a5fa; margin-left: 0.25rem;">(excluding weekends)</span>
    </div>
</div>
