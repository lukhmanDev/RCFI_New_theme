import os

directory = r"d:\LUKMAN\RCFI\New folder\rcfi\resources\views\admin\project_detail"

# Target form block from the previous step (normalized to \n)
target_form = """                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">Total Grands (₹)</label>
                                    <input type="number" name="total_amount" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" value="{{ old('total_amount', $compDetails['total_amount'] ?? $project->available_budget) }}">
                                </div>
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">Community Contribution (₹)</label>
                                    <input type="number" name="community_contribution" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" value="{{ old('community_contribution', $compDetails['community_contribution'] ?? 0) }}">
                                </div>
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">Leverage (₹)</label>
                                    <input type="number" name="amount_paid_by_donor" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" value="{{ old('amount_paid_by_donor', $compDetails['amount_paid_by_donor'] ?? 0) }}">
                                </div>
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">Any Other (₹)</label>
                                    <input type="number" name="any_other" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" value="{{ old('any_other', $compDetails['any_other'] ?? 0) }}">
                                </div>
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">Deductions (₹)</label>
                                    <input type="number" name="deductions" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" value="{{ old('deductions', $compDetails['deductions'] ?? 0) }}">
                                </div>
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">Total Project Cost (₹)</label>
                                    <input type="number" name="total_project_cost" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" value="{{ old('total_project_cost', $compDetails['total_project_cost'] ?? $project->available_budget) }}">
                                </div>
                            </div>"""

new_form_content = """                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
                                {{-- 1. Total Grands (read-only, auto-calculated from allocated expenses) --}}
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">
                                        Total Grands (₹)
                                        <span style="font-size: 0.75rem; color: var(--accent-cyan); margin-left: 0.3rem;">(auto)</span>
                                    </label>
                                    <input type="number" name="total_amount" id="sub_total_grands" readonly class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: rgba(6,182,212,0.05); color: var(--accent-cyan); cursor: not-allowed; font-weight: 600;" value="{{ $totalAmount ?? 0 }}">
                                </div>

                                {{-- 2. Community Contribution (read-only, auto-calculated from community contribution expenses) --}}
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">
                                        Community Contribution (₹)
                                        <span style="font-size: 0.75rem; color: var(--accent-cyan); margin-left: 0.3rem;">(auto)</span>
                                    </label>
                                    <input type="number" name="community_contribution" id="sub_comm_contribution" readonly class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: rgba(6,182,212,0.05); color: var(--accent-cyan); cursor: not-allowed; font-weight: 600;" value="{{ $commTotal ?? 0 }}">
                                </div>

                                {{-- 3. Leverage (manual input) --}}
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">Leverage (₹)</label>
                                    <input type="number" name="amount_paid_by_donor" id="sub_leverage" min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" value="{{ old('amount_paid_by_donor', $compDetails['amount_paid_by_donor'] ?? 0) }}" oninput="recalcSubFinancialTotal()">
                                </div>

                                {{-- 4. Any Other (manual input) --}}
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">Any Other (₹)</label>
                                    <input type="number" name="any_other" id="sub_any_other" min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" value="{{ old('any_other', $compDetails['any_other'] ?? 0) }}" oninput="recalcSubFinancialTotal()">
                                </div>

                                {{-- 5. Deductions (manual input) --}}
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">Deductions (₹)</label>
                                    <input type="number" name="deductions" id="sub_deductions" min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" value="{{ old('deductions', $compDetails['deductions'] ?? 0) }}" oninput="recalcSubFinancialTotal()">
                                </div>

                                {{-- 6. Total Project Cost (read-only, auto-calculated) --}}
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">
                                        Total Project Cost (₹)
                                        <span style="font-size: 0.75rem; color: #10b981; margin-left: 0.3rem;">(auto)</span>
                                    </label>
                                    <input type="number" name="total_project_cost" id="sub_total_cost" readonly class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid rgba(16,185,129,0.4); background-color: rgba(16,185,129,0.05); color: #10b981; cursor: not-allowed; font-weight: 700; font-size: 1rem;" value="0">
                                </div>
                            </div>"""

target_btn_form = """                            <button type="submit" class="btn-custom" style="padding: 0.5rem 1.5rem; cursor: pointer;">Save Details</button>
                        </form>"""

new_btn_form = """                            <button type="submit" class="btn-custom" style="padding: 0.5rem 1.5rem; cursor: pointer;">Save Details</button>
                        </form>

                        <script>
                        (function() {
                            var grands = {{ $totalAmount ?? 0 }};
                            var comm = {{ $commTotal ?? 0 }};

                            function recalcSubFinancialTotal() {
                                var leverage = parseFloat(document.getElementById('sub_leverage').value) || 0;
                                var anyOther = parseFloat(document.getElementById('sub_any_other').value) || 0;
                                var deduction = parseFloat(document.getElementById('sub_deductions').value) || 0;
                                var total = grands + comm + leverage + anyOther - deduction;
                                if (total < 0) total = 0;
                                document.getElementById('sub_total_cost').value = total.toFixed(2);
                            }

                            window.recalcSubFinancialTotal = recalcSubFinancialTotal;
                            recalcSubFinancialTotal(); // run on page load
                        })();
                        </script>"""

target_grid = """                        <div class="details-grid">
                            <div class="details-label">Total Grands</div><div class="details-colon">:</div>
                            <div class="details-value">₹{{ number_format($compDetails['total_amount'] ?? $project->available_budget, 2) }}</div>

                            <div class="details-label">Community Contribution</div><div class="details-colon">:</div>
                            <div class="details-value" style="color: var(--accent-cyan);">₹{{ number_format($compDetails['community_contribution'] ?? 0, 2) }}</div>

                            <div class="details-label">Leverage</div><div class="details-colon">:</div>
                            <div class="details-value" style="color: var(--accent-cyan);">₹{{ number_format($compDetails['amount_paid_by_donor'] ?? 0, 2) }}</div>

                            <div class="details-label">Any Other</div><div class="details-colon">:</div>
                            <div class="details-value">₹{{ number_format($compDetails['any_other'] ?? 0, 2) }}</div>

                            <div class="details-label">Deductions</div><div class="details-colon">:</div>
                            <div class="details-value" style="color: var(--accent-red);">₹{{ number_format($compDetails['deductions'] ?? 0, 2) }}</div>

                            <div class="details-label" style="font-weight: 700;">Total Project Cost</div><div class="details-colon">:</div>
                            <div class="details-value" style="font-weight: 700; color: #ffffff;">₹{{ number_format($compDetails['total_project_cost'] ?? $project->available_budget, 2) }}</div>"""

new_grid_content = """                        <div class="details-grid">
                            <div class="details-label">Total Grands</div><div class="details-colon">:</div>
                            <div class="details-value" style="color: var(--accent-cyan); font-weight: 600;">₹{{ number_format($compDetails['total_amount'] ?? ($totalAmount ?? 0), 2) }}</div>

                            <div class="details-label">Community Contribution</div><div class="details-colon">:</div>
                            <div class="details-value" style="color: var(--accent-cyan); font-weight: 600;">₹{{ number_format($compDetails['community_contribution'] ?? ($commTotal ?? 0), 2) }}</div>

                            <div class="details-label">Leverage</div><div class="details-colon">:</div>
                            <div class="details-value">₹{{ number_format($compDetails['amount_paid_by_donor'] ?? 0, 2) }}</div>

                            <div class="details-label">Any Other</div><div class="details-colon">:</div>
                            <div class="details-value">₹{{ number_format($compDetails['any_other'] ?? 0, 2) }}</div>

                            <div class="details-label">Deductions</div><div class="details-colon">:</div>
                            <div class="details-value" style="color: var(--accent-red);">₹{{ number_format($compDetails['deductions'] ?? 0, 2) }}</div>

                            <div class="details-label" style="font-weight: 700;">Total Project Cost</div><div class="details-colon">:</div>
                            <div class="details-value" style="color: #10b981; font-weight: 700; font-size: 1rem;">₹{{ number_format($compDetails['total_project_cost'] ?? (($totalAmount ?? 0) + ($commTotal ?? 0)), 2) }}</div>"""

# Normalize line endings
target_form = target_form.replace("\r\n", "\n")
new_form_content = new_form_content.replace("\r\n", "\n")
target_btn_form = target_btn_form.replace("\r\n", "\n")
new_btn_form = new_btn_form.replace("\r\n", "\n")
target_grid = target_grid.replace("\r\n", "\n")
new_grid_content = new_grid_content.replace("\r\n", "\n")

for filename in os.listdir(directory):
    if filename.endswith(".blade.php"):
        filepath = os.path.join(directory, filename)
        with open(filepath, "r", encoding="utf-8") as f:
            content = f.read()
        
        normalized_content = content.replace("\r\n", "\n")
        
        has_form = target_form in normalized_content
        has_btn = target_btn_form in normalized_content
        has_grid = target_grid in normalized_content
        
        if has_form:
            normalized_content = normalized_content.replace(target_form, new_form_content)
        if has_btn:
            normalized_content = normalized_content.replace(target_btn_form, new_btn_form)
        if has_grid:
            normalized_content = normalized_content.replace(target_grid, new_grid_content)
            
        if has_form or has_btn or has_grid:
            with open(filepath, "w", encoding="utf-8", newline="\r\n") as f:
                f.write(normalized_content)
            print(f"Processed: {filename} (form: {has_form}, btn: {has_btn}, grid: {has_grid})")
        else:
            print(f"Skipped/Not matched: {filename}")
