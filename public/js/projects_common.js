/* Projects Common JavaScript */

function filterTable() {
    const input = document.getElementById('tableSearch');
    const filter = input ? input.value.toLowerCase().trim() : '';
    const selManager = (document.getElementById('filterManager')?.value || '').toLowerCase().trim();
    const selAgency = (document.getElementById('filterAgency')?.value || '').toLowerCase().trim();
    const selDistrict = (document.getElementById('filterDistrict')?.value || '').toLowerCase().trim();
    const selState = (document.getElementById('filterState')?.value || '').toLowerCase().trim();

    const table = document.getElementById('projectsTable');
    if (!table) return;

    const rows = table.querySelectorAll('tbody tr.project-row');
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const rManager = (row.getAttribute('data-manager') || '').toLowerCase();
        const rAgency = (row.getAttribute('data-agency') || '').toLowerCase();
        const rDistrict = (row.getAttribute('data-district') || '').toLowerCase();
        const rState = (row.getAttribute('data-state') || '').toLowerCase();
        const rText = (row.textContent || row.innerText || '').toLowerCase();

        const matchesSearch = !filter || rText.includes(filter);
        const matchesManager = !selManager || rManager === selManager;
        const matchesAgency = !selAgency || rAgency === selAgency;
        const matchesDistrict = !selDistrict || rDistrict === selDistrict;
        const matchesState = !selState || rState === selState;

        if (matchesSearch && matchesManager && matchesAgency && matchesDistrict && matchesState) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
}
window.filterTable = filterTable;

function resetFilters() {
    if (document.getElementById('tableSearch')) document.getElementById('tableSearch').value = '';
    if (document.getElementById('filterState')) document.getElementById('filterState').value = '';
    if (document.getElementById('filterDistrict')) document.getElementById('filterDistrict').value = '';
    if (document.getElementById('filterAgency')) document.getElementById('filterAgency').value = '';
    if (document.getElementById('filterManager')) document.getElementById('filterManager').value = '';
    filterTable();
}
window.resetFilters = resetFilters;
