
        var allApplicationsData = [];

        async function toggleChecklistDocument(button, docName) {
            const icon = button.querySelector('i');
            const isTicked = icon && icon.className.includes('bxs-checkbox-checked');

            if (isTicked) {
                showCustomConfirm('Are you sure you want to untick ' + docName + '?', function() {
                    performToggleChecklistDocument(button, docName);
                });
            } else {
                performToggleChecklistDocument(button, docName);
            }
        }

        async function performToggleChecklistDocument(button, docName) {
            button.disabled = true;
            try {
                const response = await fetch("1", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "1",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({ document_name: docName })
                });
                
                const data = await response.json();
                if (data.success) {
                    const icon = button.querySelector('i');
                    if (data.ticked) {
                        icon.className = 'bx bxs-checkbox-checked';
                        icon.style.color = 'var(--accent-green)';
                    } else {
                        icon.className = 'bx bx-checkbox';
                        icon.style.color = 'var(--text-muted)';
                    }

                    const cellId = 'ticked-at-' + docName.replace(/ /g, '_');
                    const cell = document.getElementById(cellId);
                    if (cell) {
                        cell.innerText = data.ticked_at ? data.ticked_at : '-';
                    }

                    if (typeof showToast === 'function') {
                        showToast(data.message, 'success');
                    }
                } else {
                    if (typeof showToast === 'function') {
                        showToast(data.error || 'Failed to toggle document.', 'danger');
                    }
                }
            } catch (e) {
                console.error(e);
                if (typeof showToast === 'function') {
                    showToast('Network error occurred.', 'danger');
                }
            } finally {
                button.disabled = false;
            }
        }

        function onPhaseSelectChange() {
            const sel = document.getElementById('project-phase-select');
            const box = document.getElementById('phase-custom-box');
            if (sel && box) {
                box.style.display = sel.value === 'Other' ? '' : 'none';
            }
        }

        async function saveProjectPhase() {
            const sel    = document.getElementById('project-phase-select');
            const custom = document.getElementById('project-phase-custom');
            const phase  = sel ? sel.value : '';
            if (!phase) {
                if (typeof showToast === 'function') showToast('Please select a phase first.', 'warning');
                return;
            }
            if (phase === 'Other' && (!custom || !custom.value.trim())) {
                if (typeof showToast === 'function') showToast('Please describe the custom status.', 'warning');
                custom && custom.focus();
                return;
            }
            try {
                const resp = await fetch("1", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "1",
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        project_phase:        phase,
                        project_phase_custom: custom ? custom.value.trim() : '',
                    }),
                });
                const data = await resp.json();
                if (data.success) {
                    const badge = document.getElementById('current-phase-badge');
                    const label = phase === 'Other' ? data.custom : phase;
                    if (badge) {
                        badge.innerHTML = `<span style="display:inline-flex;align-items:center;gap:0.4rem;background:rgba(6,182,212,0.12);border:1px solid var(--accent-cyan);color:var(--accent-cyan);padding:0.4rem 1rem;border-radius:20px;font-size:0.85rem;font-weight:600;"><i class="bx bx-radio-circle-marked" style="font-size:1rem;"></i>${label}</span>`;
                    }

                    const gridStatus = document.getElementById('grid-project-status');
                    if (gridStatus) {
                        gridStatus.innerText = label;
                    }

                    const container = document.getElementById('status-updated-time-container');
                    const timeSpan = document.getElementById('status-updated-at');
                    const humanSpan = document.getElementById('status-updated-human');
                    if (container && timeSpan && humanSpan) {
                        timeSpan.innerText = data.updated_at;
                        humanSpan.innerText = data.updated_human;
                        container.style.display = 'inline-flex';
                    }

                    if (typeof showToast === 'function') showToast(data.message, 'success');
                } else {
                    if (typeof showToast === 'function') showToast(data.error || 'Failed to update status.', 'danger');
                }
            } catch (e) {
                console.error(e);
                if (typeof showToast === 'function') showToast('Network error occurred.', 'danger');
            }
        }

        function updateRealtimeApplicationDetails(selectedId) {
            const container = document.getElementById('realtime-application-details-container');
            if (!container) return;

            if (!selectedId) {
                container.innerHTML = `
                    <div style="text-align: center; padding: 3rem; background-color: rgba(255, 255, 255, 0.02); border-radius: 8px; border: 1px dashed var(--panel-border); margin: 2rem 0;">
                        <i class="bx bx-link-external" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
                        <h3 style="color: var(--text-main); font-size: 1.2rem; margin-bottom: 0.5rem;">No Application Connected</h3>
                        <p style="color: var(--text-muted); font-size: 0.9rem; max-width: 400px; margin: 0 auto;">Please connect this project to an application using the form below to view application details.</p>
                    </div>
                `;
                return;
            }

            const apps = (typeof allApplicationsData !== 'undefined' && Array.isArray(allApplicationsData)) ? allApplicationsData : [];
            const app = apps.find(a => a.id == selectedId);
            if (!app) return;

            const formatVal = (val) => val ? val : '<span style="color: var(--text-muted); font-style: italic;">N/A</span>';
            
            let meta = {};
            if (app.meta) {
                if (typeof app.meta === 'object') {
                    meta = app.meta;
                } else {
                    try {
                        meta = JSON.parse(app.meta) || {};
                    } catch(e) {
                        meta = {};
                    }
                }
            }

                        const incomeText = meta.monthly_income ? '₹' + Number(meta.monthly_income).toLocaleString() : 'N/A';
            const expenseText = meta.monthly_expense ? '₹' + Number(meta.monthly_expense).toLocaleString() : 'N/A';
            const costText = meta.monthly_cost ? '₹' + Number(meta.monthly_cost).toLocaleString() : 'N/A';

            if (projectType === 'Orphan Care') {
                container.innerHTML = `
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                        <div>
                            <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">1. Orphan & Family Details</h4>
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">Orphan Name:</td><td style="color: var(--text-main); font-weight: 600;">\${formatVal(app.applicant_name)} (\${formatVal(meta.gender)})</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Date of Birth / Age:</td><td>\${formatVal(meta.dob)} / \${formatVal(meta.age)} yrs</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Aadhar Number:</td><td>\${formatVal(meta.aadhar_number)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Father's Name:</td><td>\${formatVal(meta.father_name)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Grandfather's Name:</td><td>\${formatVal(meta.grandfather_name)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Mother's Name:</td><td>\${formatVal(meta.mother_name)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Mother's Father Name:</td><td>\${formatVal(meta.mothers_father_name)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Guardian / Relation:</td><td>\${formatVal(meta.guardian_name)} (\${formatVal(meta.guardian_relation)})</td></tr>
                            </table>

                            <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">2. Parental Death & Sibling Details</h4>
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">Father's Death Date:</td><td>\${formatVal(meta.father_death_date)} <span style="font-size: 0.8rem; color: var(--text-muted);">(\${formatVal(meta.father_death_cause)})</span></td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Mother Alive Status:</td><td>\${formatVal(meta.mother_alive_status)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Mother's Death Date:</td><td>\${formatVal(meta.mother_death_date)} <span style="font-size: 0.8rem; color: var(--text-muted);">(\${formatVal(meta.mother_death_cause)})</span></td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Mother Re-Married?</td><td>\${formatVal(meta.mother_remarried_status)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Brothers & Sisters:</td><td>Total: \${formatVal(meta.siblings_total)} (M: \${formatVal(meta.siblings_male)} / F: \${formatVal(meta.siblings_female)})</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Monthly Income:</td><td>\${incomeText}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Monthly Expense:</td><td>\${expenseText}</td></tr>
                            </table>
                        </div>

                        <div>
                            <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">3. Education & House Details</h4>
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">Type Of House:</td><td>\${formatVal(meta.house_type)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">School Name:</td><td>\${formatVal(meta.school_name)} (Class: \${formatVal(meta.school_class)})</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Madrassa Name:</td><td>\${formatVal(meta.madrassa_name)} (Class: \${formatVal(meta.madrassa_class)})</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">If Not Studying, Reason:</td><td>\${formatVal(meta.not_studying_reason)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Health Status:</td><td>\${formatVal(meta.health_status)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Sponsorship Details:</td><td>\${formatVal(meta.sponsorship_details)}</td></tr>
                            </table>

                            <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">4. Address & Contact Details</h4>
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">House Name / Place:</td><td>\${formatVal(meta.house_name)} / \${formatVal(app.place)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Town / Post Office:</td><td>\${formatVal(meta.town)} / \${formatVal(meta.post_office)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">District / State / Pin:</td><td>\${formatVal(meta.district)} / \${formatVal(meta.state)} / \${formatVal(meta.pin_code)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Mobile 1 / 2:</td><td>\${formatVal(meta.mobile_1)} / \${formatVal(meta.mobile_2)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Review Status:</td><td style="font-weight: 600; color: var(--text-main);">\${app.status}</td></tr>
                            </table>
                        </div>
                    </div>

                    <div style="margin-top: 1.5rem; border-top: 1px solid var(--panel-border); padding-top: 1rem;">
                        <h5 style="color: var(--accent-cyan); font-size: 0.85rem; margin-bottom: 0.5rem; text-transform: uppercase; font-weight: 700;">Additional Notes:</h5>
                        <p style="color: var(--text-muted); line-height: 1.5; font-size: 0.85rem; margin: 0; background-color: #121824; padding: 0.75rem; border-radius: 6px; border: 1px solid var(--panel-border); min-height: 50px;">
                            \${formatVal(app.details)}
                        </p>
                    </div>
                `;
            } else if (projectType === 'Differently Abled') {
                container.innerHTML = `
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                        <div>
                            <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">1. Personal Details of Applicant</h4>
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">Applicant Name:</td><td style="color: var(--text-main); font-weight: 600;">\${formatVal(app.applicant_name)} (\${formatVal(meta.gender)})</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Date of Birth / Age:</td><td>\${formatVal(meta.dob)} / \${formatVal(meta.age)} yrs</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Aadhaar / Marital Status:</td><td>\${formatVal(meta.aadhar_number)} / \${formatVal(meta.marital_status)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Father's Name:</td><td>\${formatVal(meta.father_name)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Father's Father:</td><td>\${formatVal(meta.fathers_father)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Mother's Name:</td><td>\${formatVal(meta.mother_name)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Guardian / Relation:</td><td>\${formatVal(meta.guardian_name)} (\${formatVal(meta.guardian_relation)})</td></tr>
                            </table>

                            <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">2. Family & Economic Details</h4>
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">Male / Female Members:</td><td>M: \${formatVal(meta.male_members)} / F: \${formatVal(meta.female_members)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Total Members:</td><td style="font-weight: 600; color: #ffffff;">\${formatVal(meta.total_members)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">People with Disabilities:</td><td>\${formatVal(meta.people_with_disabilities)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Monthly Income:</td><td>\${incomeText}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Monthly Cost:</td><td>\${costText}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Source of Income:</td><td>\${formatVal(meta.income_source)}</td></tr>
                            </table>
                        </div>

                        <div>
                            <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">3. Education & Disability Details</h4>
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">Studying Institution:</td><td>\${formatVal(meta.studying_institution)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">If not study, reason:</td><td>\${formatVal(meta.not_studying_reason)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Health Status:</td><td>\${formatVal(meta.health_status)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Disability Type:</td><td style="font-weight: 600; color: #ffffff;">\${formatVal(meta.disability_type)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Disability Percentage:</td><td>\${formatVal(meta.disability_percentage)}%</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Date/Year of Disability:</td><td>\${formatVal(meta.disability_date)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Level of Disability:</td><td>\${formatVal(meta.disability_level)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Anyone else help?</td><td>\${formatVal(meta.other_help)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Accommodation:</td><td>\${formatVal(meta.accommodation)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Description:</td><td>\${formatVal(meta.description)}</td></tr>
                            </table>

                            <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">4. Address & Contact Details</h4>
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">House Name / Place:</td><td>\${formatVal(meta.house_name)} / \${formatVal(app.place)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Panchayat / District:</td><td>\${formatVal(meta.panchayat)} / \${formatVal(meta.district)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Pincode / Mobile:</td><td>\${formatVal(meta.pincode)} / \${formatVal(meta.mobile)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Review Status:</td><td style="font-weight: 600; color: var(--text-main);">\${app.status}</td></tr>
                            </table>
                        </div>
                    </div>

                    <div style="margin-top: 1.5rem; border-top: 1px solid var(--panel-border); padding-top: 1rem;">
                        <h5 style="color: var(--accent-cyan); font-size: 0.85rem; margin-bottom: 0.5rem; text-transform: uppercase; font-weight: 700;">Additional Notes:</h5>
                        <p style="color: var(--text-muted); line-height: 1.5; font-size: 0.85rem; margin: 0; background-color: #121824; padding: 0.75rem; border-radius: 6px; border: 1px solid var(--panel-border); min-height: 50px;">
                            \${formatVal(app.details)}
                        </p>
                    </div>
                `;
            } else if (projectType === 'Family Aid') {
                container.innerHTML = `
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                        <div>
                            <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">1. Personal Details of Applicant</h4>
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">Applicant Name:</td><td style="color: var(--text-main); font-weight: 600;">\${formatVal(app.applicant_name)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Date of Birth / Age:</td><td>\${formatVal(meta.dob)} / \${formatVal(meta.age)} yrs</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Aadhaar Number:</td><td>\${formatVal(meta.aadhar_number)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Father's Name:</td><td>\${formatVal(meta.father_name)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Father's Father:</td><td>\${formatVal(meta.fathers_father)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Mother's Name:</td><td>\${formatVal(meta.mother_name)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">House / Location:</td><td>\${formatVal(meta.house_name)} / \${formatVal(meta.location)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">PO / Panchayat / Dist:</td><td>\${formatVal(meta.post_office)} / \${formatVal(meta.panchayat)} / \${formatVal(meta.district)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Pin Code / Contact:</td><td>Pin: \${formatVal(meta.pin_code)} / Mob: \${formatVal(meta.mobile_1)} \${meta.mobile_2 ? ', ' + meta.mobile_2 : ''}</td></tr>
                            </table>

                            <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">2. Family & Income Details</h4>
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">Children in Family:</td><td>Total: \${formatVal(meta.children_total)} (M: \${formatVal(meta.children_male)} / F: \${formatVal(meta.children_female)})</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">NRI Status:</td><td>\${formatVal(meta.nri_status)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Occupation:</td><td>\${formatVal(meta.occupation)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Monthly Income:</td><td>\${incomeText}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Other Income Sources:</td><td>\${formatVal(meta.other_income_sources)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Health & Disability:</td><td>Health: \${formatVal(meta.health_status)} / Disability: \${formatVal(meta.disability_status)}</td></tr>
                            </table>
                        </div>

                        <div>
                            <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">3. Health & Residence Details</h4>
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">Routine Treatment:</td><td>\${formatVal(meta.routine_treatment_explanation)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Chronic Patients:</td><td>\${formatVal(meta.chronic_patients_description)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Residence Information:</td><td style="font-weight: 600; color: #ffffff;">\${formatVal(meta.residence_info)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Own House Condition:</td><td>\${formatVal(meta.own_house_condition)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Own Place / Size:</td><td>Place: \${formatVal(meta.own_place_status)} / Size: \${formatVal(meta.own_place_size)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Is there a sequel?</td><td>\${formatVal(meta.sequel_status)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Welfare Areas:</td><td>\${formatVal(meta.welfare_assistance_areas)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Review Status:</td><td style="font-weight: 600; color: var(--text-main);">\${app.status}</td></tr>
                            </table>
                        </div>
                    </div>

                    <div style="margin-top: 1.5rem; border-top: 1px solid var(--panel-border); padding-top: 1rem;">
                        <h5 style="color: var(--accent-cyan); font-size: 0.85rem; margin-bottom: 0.5rem; text-transform: uppercase; font-weight: 700;">Additional Notes:</h5>
                        <p style="color: var(--text-muted); line-height: 1.5; font-size: 0.85rem; margin: 0; background-color: #121824; padding: 0.75rem; border-radius: 6px; border: 1px solid var(--panel-border); min-height: 50px;">
                            \${formatVal(app.details)}
                        </p>
                    </div>
                `;
            }
        }

        var activeProjectId = 1;
        var activeProjectStage = 1;
        var isProjectApproved = "1";
        var hasApplication = "1";
        var projectType = "1";

        function switchStage(stageNum) {
            let isLocked = false;
            const isSixStage = ['Education Center', 'Cultural Center', 'Hospital or Clinics', 'Shops and Others', 'House', 'Drinking Water - Group Level', 'Drinking Water - Individual Level', 'General'].includes(projectType);
            if (['Orphan Care', 'Differently Abled', 'Family Aid'].includes(projectType)) {

                isLocked = false;
            } else if (isSixStage) {
                if (stageNum <= 2) {
                    isLocked = false;
                } else if (stageNum === 3 || stageNum === 4) {
                    isLocked = (hasApplication !== '1');
                } else {
                    // Stage 5 or 6 unlocks when project stage >= 5 or approved
                    isLocked = (activeProjectStage < 5 && isProjectApproved !== '1');
                }
            } else {
                if (stageNum !== 1 && isProjectApproved !== '1') {
                    isLocked = true;
                }
            }

            if (isLocked) {
                const msg = isSixStage 
                ? "Access Locked: This stage is not yet unlocked." 
                : "Access Locked: This stage is only accessible after COO approval.";
                if (typeof showToast === 'function') {
                    showToast(msg, "danger");
                } else {
                    alert(msg);
                }
                return;
            }

            // Save selected stage to sessionStorage
            sessionStorage.setItem('current_project_stage_1', stageNum);

            // Remove active highlight from all stage tabs
            const tabs = document.querySelectorAll('.stage-tab');
            tabs.forEach(tab => tab.classList.remove('active'));

            // Highlight clicked stage tab
            const clickedTab = document.getElementById('tab-' + stageNum);
            if (clickedTab) {
                clickedTab.classList.add('active');
            }

            // Hide all stage panels
            const panels = document.querySelectorAll('.stage-content-panel');
            panels.forEach(panel => panel.style.display = 'none');

            // Show selected stage panel
            const targetPanel = document.getElementById('stage-content-' + stageNum);
            if (targetPanel) {
                targetPanel.style.display = 'block';
            }
        }
        window.switchStage = switchStage;

        // Initialize display to show the stage panel
        function initStageDisplay() {
            const savedStage = sessionStorage.getItem('current_project_stage_1');
            let stageToLoad = 1;
            if (savedStage) {
                const stageNum = Number(savedStage);
                let isLocked = false;
                const isSixStage = ['Education Center', 'Cultural Center', 'Hospital or Clinics', 'Shops and Others', 'House', 'Drinking Water - Group Level', 'Drinking Water - Individual Level', 'General'].includes(projectType);
                if (['Orphan Care', 'Differently Abled', 'Family Aid'].includes(projectType)) {
                    isLocked = false;
                } else if (isSixStage) {
                    if (stageNum <= 2) {
                        isLocked = false;
                    } else if (stageNum === 3 || stageNum === 4) {
                        isLocked = (hasApplication !== '1');
                    } else {
                        isLocked = (activeProjectStage < 5 && isProjectApproved !== '1');
                    }
                } else {
                    if (stageNum !== 1 && isProjectApproved !== '1') {
                        isLocked = true;
                    }
                }
                if (!isLocked) {
                    stageToLoad = stageNum;
                }
            }
            switchStage(stageToLoad);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initStageDisplay);
        } else {
            initStageDisplay();
        }

        // Material Management Modal Controls
        function openAddMaterialModal() {
            document.getElementById('addMaterialModal').style.display = 'flex';
        }
        function closeAddMaterialModal() {
            document.getElementById('addMaterialModal').style.display = 'none';
        }
        function openEditMaterialModal(index, name, amount) {
            const form = document.getElementById('editMaterialForm');
            form.setAttribute('action', `/admin/projects/1/materials/${index}`);
            document.getElementById('editMaterialName').value = name;
            document.getElementById('editMaterialAmount').value = amount;
            document.getElementById('editMaterialModal').style.display = 'flex';
        }
        function closeEditMaterialModal() {
            document.getElementById('editMaterialModal').style.display = 'none';
        }

        // Community Contribution Modal Controls
        function openAddCommContribModal() {
            document.getElementById('addCommContribModal').style.display = 'flex';
        }
        function closeAddCommContribModal() {
            document.getElementById('addCommContribModal').style.display = 'none';
        }
        function openEditCommContribModal(index, item, amount) {
            const form = document.getElementById('editCommContribForm');
            form.setAttribute('action', `/admin/projects/1/community-contributions/${index}`);
            document.getElementById('editCommContribName').value = item;
            document.getElementById('editCommContribAmount').value = amount;
            document.getElementById('editCommContribModal').style.display = 'flex';
        }
        function closeEditCommContribModal() {
            document.getElementById('editCommContribModal').style.display = 'none';
        }

        // Expense Management Modal Controls
        function openAddExpenseModal(materialIndex, materialName) {
            document.getElementById('addExpenseFormMaterialIndex').value = materialIndex;
            document.getElementById('addExpenseModalMaterialName').innerText = materialName;
            document.getElementById('addExpenseModal').style.display = 'flex';
        }
        function closeAddExpenseModal() {
            document.getElementById('addExpenseModal').style.display = 'none';
        }
        function openEditExpenseModal(index, materialIndex, name, quantity, amount) {
            const form = document.getElementById('editExpenseForm');
            form.setAttribute('action', `/admin/projects/1/expenses/${index}`);
            document.getElementById('editExpenseFormMaterialIndex').value = materialIndex;
            document.getElementById('editExpenseName').value = name;
            document.getElementById('editExpenseQuantity').value = quantity;
            document.getElementById('editExpenseAmount').value = amount;
            document.getElementById('editExpenseModal').style.display = 'flex';
        }
        function closeEditExpenseModal() {
            document.getElementById('editExpenseModal').style.display = 'none';
        }

        // Community Contribution Expense Management
        function openAddCommExpenseModal(commIndex, commName) {
            document.getElementById('addCommExpenseFormIndex').value = commIndex;
            document.getElementById('addCommExpenseModalName').innerText = commName;
            document.getElementById('addCommExpenseModal').style.display = 'flex';
        }
        function closeAddCommExpenseModal() {
            document.getElementById('addCommExpenseModal').style.display = 'none';
        }
        function openEditCommExpenseModal(index, commIndex, name, quantity, amount) {
            const form = document.getElementById('editCommExpenseForm');
            form.setAttribute('action', `/admin/projects/1/expenses/${index}`);
            document.getElementById('editCommExpenseFormIndex').value = commIndex;
            document.getElementById('editCommExpenseName').value = name;
            document.getElementById('editCommExpenseQuantity').value = quantity;
            document.getElementById('editCommExpenseAmount').value = amount;
            document.getElementById('editCommExpenseModal').style.display = 'flex';
        }
        function closeEditCommExpenseModal() {
            document.getElementById('editCommExpenseModal').style.display = 'none';
        }
    