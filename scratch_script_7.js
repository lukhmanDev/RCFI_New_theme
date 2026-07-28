
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddProgrammeModal();
                closeEditProgrammeModal();
            }
        });

        function openAddProgrammeModal() {
            const modal = document.getElementById('addProgrammeModal');
            if (modal) modal.style.display = 'flex';
        }
        function closeAddProgrammeModal() {
            const modal = document.getElementById('addProgrammeModal');
            if (modal) modal.style.display = 'none';
        }

        function openEditProgrammeModal(btnElement) {
            const rawProg = btnElement.getAttribute('data-prog');
            if (!rawProg) return;
            try {
                const prog = JSON.parse(rawProg);
                const modal = document.getElementById('editProgrammeModal');
                const form = document.getElementById('editProgrammeForm');
                if (modal && form && prog) {
                    form.action = `/admin/projects/1/1/update-programme/${prog.id}`;
                    document.getElementById('edit_prog_name').value = prog.programme_name || '';
                    document.getElementById('edit_prog_date').value = prog.date || '';
                    document.getElementById('edit_prog_place').value = prog.place || '';

                    // Handle checkbox values
                    const fields = ['present', 'photo', 'marklist', 'thanks_letter', 'report_form', 'other_document'];
                    fields.forEach(f => {
                        const checkbox = document.getElementById(`edit_prog_${f}_ticked`);
                        if (checkbox) {
                            checkbox.checked = !!(prog[f + '_ticked']);
                        }
                    });

                    modal.style.display = 'flex';
                }
            } catch (e) {
                console.error('Error opening edit programme modal:', e);
            }
        }

        function closeEditProgrammeModal() {
            const modal = document.getElementById('editProgrammeModal');
            if (modal) modal.style.display = 'none';
        }

        async function handleAddProgrammeSubmit(e) {
            e.preventDefault();
            const form = e.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Adding...';
            }

            try {
                const formData = new FormData(form);
                const response = await fetch(`/admin/projects/1/1/add-programme`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const data = await response.json();
                if (response.ok && data.success) {
                    closeAddProgrammeModal();
                    form.reset();
                    if (typeof showToast === 'function') {
                        showToast(data.message || 'Programme added successfully!', 'success');
                    }
                    setTimeout(() => window.location.reload(), 300);
                } else {
                    alert(data.error || 'Failed to add programme.');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Add Programme';
                    }
                }
            } catch (err) {
                console.error(err);
                alert('An error occurred while submitting.');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Add Programme';
                }
            }
        }

        async function handleEditProgrammeSubmit(e) {
            e.preventDefault();
            const form = e.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Saving...';
            }

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const data = await response.json();
                if (response.ok && data.success) {
                    closeEditProgrammeModal();
                    if (typeof showToast === 'function') {
                        showToast(data.message || 'Programme updated successfully!', 'success');
                    }
                    setTimeout(() => window.location.reload(), 300);
                } else {
                    alert(data.error || 'Failed to update programme.');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Save Changes';
                    }
                }
            } catch (err) {
                console.error(err);
                alert('An error occurred while saving changes.');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Save Changes';
                }
            }
        }

        // Laravel Reverb / Echo Realtime Broadcast Listener
        if (typeof window.Echo !== 'undefined') {
            window.Echo.channel('project.1')
                .listen('.programme.updated', (e) => {
                    if (typeof showToast === 'function') {
                        showToast('Realtime update received', 'info');
                    }
                    window.location.reload();
                });
        }

        async function handleDeleteProgramme(btnElement, progId, deleteUrl) {
            if (!confirm('Are you sure you want to delete this programme? This action cannot be undone.')) {
                return;
            }

            const row = btnElement.closest('tr');
            if (row) {
                row.style.opacity = '0.5';
                row.style.pointerEvents = 'none';
            }

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch(deleteUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        _method: 'DELETE'
                    })
                });

                const data = await response.json();
                if (response.ok && data.success) {
                    if (row) {
                        row.style.transition = 'all 0.3s ease';
                        row.style.opacity = '0';
                        row.style.transform = 'translateX(20px)';
                        setTimeout(() => {
                            row.remove();
                            updateProgrammeTableSerialNumbers();
                        }, 300);
                    }
                    if (typeof showToast === 'function') {
                        showToast(data.message || 'Programme deleted successfully!', 'success');
                    }
                } else {
                    if (row) {
                        row.style.opacity = '1';
                        row.style.pointerEvents = 'auto';
                    }
                    alert(data.error || 'Failed to delete programme.');
                }
            } catch (err) {
                console.error(err);
                if (row) {
                    row.style.opacity = '1';
                    row.style.pointerEvents = 'auto';
                }
                alert('An error occurred while deleting programme.');
            }
        }

        function updateProgrammeTableSerialNumbers() {
            const tbody = document.getElementById('social-aid-programmes-tbody');
            if (!tbody) return;
            const rows = tbody.querySelectorAll('tr.programme-table-row');
            if (rows.length === 0) {
                tbody.innerHTML = `
                    <tr id="no-programmes-row">
                        <td colspan="6" style="padding: 2.5rem 1rem; text-align: center; color: var(--text-muted); font-style: italic;">
                            No programme records found. Click "Add Programme" to add one.
                        </td>
                    </tr>
                `;
                return;
            }
            rows.forEach((r, idx) => {
                const serialCell = r.querySelector('.serial-no-cell');
                if (serialCell) {
                    serialCell.innerText = idx + 1;
                }
            });
        }



        async function toggleProgrammeChecklistTick(btnElement, progIndex, field) {
            const icon = btnElement.querySelector('i');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                // Instantly scale/rotate slightly for feedback
                btnElement.style.transform = 'scale(0.9)';
                setTimeout(() => btnElement.style.transform = 'scale(1)', 150);

                const response = await fetch(`/admin/projects/1/1/toggle-programme-tick`, {

                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        programme_id: progIndex,
                        field: field
                    })
                });

                const result = await response.json();
                if (response.ok && result.success) {
                    if (result.is_ticked) {
                        if (icon) icon.className = 'bx bxs-check-circle';
                        btnElement.style.backgroundColor = 'rgba(16, 185, 129, 0.15)';
                        btnElement.style.borderColor = 'rgba(16, 185, 129, 0.35)';
                        btnElement.style.color = '#059669';
                    } else {
                        if (icon) icon.className = 'bx bx-circle';
                        btnElement.style.backgroundColor = 'rgba(245, 158, 11, 0.1)';
                        btnElement.style.borderColor = 'rgba(245, 158, 11, 0.3)';
                        btnElement.style.color = '#d97706';
                    }

                    if (typeof showToast === 'function') {
                        showToast(result.message, 'success');
                    }
                } else {
                    alert(result.error || 'Failed to toggle tick status.');
                }
            } catch (err) {
                console.error(err);
                alert('An error occurred while updating status.');
            }
        }
    