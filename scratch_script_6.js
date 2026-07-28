
        function openAddContractorModal() {
            document.getElementById('addContractorModal').style.display = 'flex';
        }
        function closeAddContractorModal() {
            document.getElementById('addContractorModal').style.display = 'none';
        }
        function openEditContractorModal(index, contractor) {
            const form = document.getElementById('editContractorForm');
            form.setAttribute('action', `/admin/projects/1/contractors/${index}`);
            
            const select = document.getElementById('edit_contractor_select');
            
            if (contractor.contractor_id) {
                select.value = contractor.contractor_id;
            } else {
                // Try name matching for legacy contractor records
                let matched = false;
                for (let i = 0; i < select.options.length; i++) {
                    const optName = select.options[i].text.split('(')[0].trim().toLowerCase();
                    const targetName = (contractor.contractor_name || '').trim().toLowerCase();
                    if (optName === targetName) {
                        select.selectedIndex = i;
                        matched = true;
                        break;
                    }
                }
                if (!matched) {
                    select.value = '';
                }
            }
            
            document.getElementById('edit_contractor_type').value = contractor.type_of_contract || '';
            document.getElementById('edit_contractor_purpose').value = contractor.purpose_of_contract || '';
            
            updateEditContractorDetails();
            
            document.getElementById('editContractorModal').style.display = 'flex';
        }
        function closeEditContractorModal() {
            document.getElementById('editContractorModal').style.display = 'none';
        }

        function updateAddContractorDetails() {
            const select = document.getElementById('add_contractor_select');
            const card = document.getElementById('add_contractor_details_card');
            const opt = select.options[select.selectedIndex];
            if (opt && opt.value) {
                document.getElementById('add_c_company').innerText = opt.getAttribute('data-company') || 'N/A';
                document.getElementById('add_c_phone').innerText = opt.getAttribute('data-phone') || 'N/A';
                document.getElementById('add_c_address').innerText = opt.getAttribute('data-address') || 'N/A';
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        }

        function updateEditContractorDetails() {
            const select = document.getElementById('edit_contractor_select');
            const card = document.getElementById('edit_contractor_details_card');
            const opt = select.options[select.selectedIndex];
            if (opt && opt.value) {
                document.getElementById('edit_c_company').innerText = opt.getAttribute('data-company') || 'N/A';
                document.getElementById('edit_c_phone').innerText = opt.getAttribute('data-phone') || 'N/A';
                document.getElementById('edit_c_address').innerText = opt.getAttribute('data-address') || 'N/A';
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        }
            // Inspection Modal Controls
        function openAddInspectionModal() {
            document.getElementById('addInspectionModal').style.display = 'flex';
        }
        function closeAddInspectionModal() {
            document.getElementById('addInspectionModal').style.display = 'none';
        }
        function openEditInspectionModal(id, name, designation, date, remarks) {
            const form = document.getElementById('editInspectionForm');
            form.setAttribute('action', `/admin/projects/${activeProjectId}/inspections/${id}`);
            document.getElementById('edit_inspection_name').value = name;
            document.getElementById('edit_inspection_designation').value = designation;
            document.getElementById('edit_inspection_date').value = date;
            document.getElementById('edit_inspection_remarks').value = remarks;
            document.getElementById('editInspectionModal').style.display = 'flex';
        }
        function closeEditInspectionModal() {
            document.getElementById('editInspectionModal').style.display = 'none';
        }
