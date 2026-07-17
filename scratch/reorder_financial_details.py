import os

directory = r"d:\LUKMAN\RCFI\New folder\rcfi\resources\views\admin\project_detail"

# Target form block with standardized newlines normalized to \n
target_form = """                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">Total Project Cost (₹)</label>
                                    <input type="number" name="total_project_cost" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" value="{{ old('total_project_cost', $compDetails['total_project_cost'] ?? $project->available_budget) }}">
                                </div>
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">Total Amount (₹)</label>
                                    <input type="number" name="total_amount" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" value="{{ old('total_amount', $compDetails['total_amount'] ?? $project->available_budget) }}">
                                </div>
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">Amount Paid by Donor (₹)</label>
                                    <input type="number" name="amount_paid_by_donor" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" value="{{ old('amount_paid_by_donor', $compDetails['amount_paid_by_donor'] ?? 0) }}">
                                </div>
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">Community Contribution (₹)</label>
                                    <input type="number" name="community_contribution" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" value="{{ old('community_contribution', $compDetails['community_contribution'] ?? 0) }}">
                                </div>
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">Any Other (₹)</label>
                                    <input type="number" name="any_other" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" value="{{ old('any_other', $compDetails['any_other'] ?? 0) }}">
                                </div>
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">Deductions (₹)</label>
                                    <input type="number" name="deductions" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" value="{{ old('deductions', $compDetails['deductions'] ?? 0) }}">
                                </div>
                            </div>"""

new_form_content = """                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
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

target_grid = """                        <div class="details-grid">
                            <div class="details-label">Total Project Cost</div><div class="details-colon">:</div>
                            <div class="details-value">₹{{ number_format($compDetails['total_project_cost'] ?? $project->available_budget, 2) }}</div>

                            <div class="details-label">Total Amount</div><div class="details-colon">:</div>
                            <div class="details-value">₹{{ number_format($compDetails['total_amount'] ?? $project->available_budget, 2) }}</div>

                            <div class="details-label">Amount Paid by Donor</div><div class="details-colon">:</div>
                            <div class="details-value" style="color: var(--accent-cyan);">₹{{ number_format($compDetails['amount_paid_by_donor'] ?? 0, 2) }}</div>

                            <div class="details-label">Community Contribution</div><div class="details-colon">:</div>
                            <div class="details-value" style="color: var(--accent-cyan);">₹{{ number_format($compDetails['community_contribution'] ?? 0, 2) }}</div>

                            <div class="details-label">Any Other</div><div class="details-colon">:</div>
                            <div class="details-value">₹{{ number_format($compDetails['any_other'] ?? 0, 2) }}</div>

                            <div class="details-label">Deductions</div><div class="details-colon">:</div>
                            <div class="details-value" style="color: var(--accent-red);">₹{{ number_format($compDetails['deductions'] ?? 0, 2) }}</div>"""

new_grid_content = """                        <div class="details-grid">
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

# Normalize target strings to \n
target_form = target_form.replace("\r\n", "\n")
new_form_content = new_form_content.replace("\r\n", "\n")
target_grid = target_grid.replace("\r\n", "\n")
new_grid_content = new_grid_content.replace("\r\n", "\n")

for filename in os.listdir(directory):
    if filename.endswith(".blade.php"):
        filepath = os.path.join(directory, filename)
        with open(filepath, "r", encoding="utf-8") as f:
            content = f.read()
        
        # Normalize file line endings to \n for strict matching
        normalized_content = content.replace("\r\n", "\n")
        
        has_form = target_form in normalized_content
        has_grid = target_grid in normalized_content
        
        if has_form:
            normalized_content = normalized_content.replace(target_form, new_form_content)
        if has_grid:
            normalized_content = normalized_content.replace(target_grid, new_grid_content)
            
        if has_form or has_grid:
            # Write back maintaining the original CRLF line endings of Windows if needed, 
            # or just write standard file. Git automatically handles CRLF/LF on commit.
            with open(filepath, "w", encoding="utf-8", newline="\r\n") as f:
                f.write(normalized_content)
            print(f"Processed: {filename} (form: {has_form}, grid: {has_grid})")
        else:
            print(f"Skipped/Not matched: {filename}")
