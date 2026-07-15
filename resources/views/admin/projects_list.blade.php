@extends('layouts.admin')

@section('title', $categoryName . ' Project List')

@section('content')

    <style>
        .group-header-panel {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #ffffff;
            padding: 1.2rem;
            border-radius: 8px;
            text-align: center;
            font-size: 1.4rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
        }

        .controls-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .controls-row .btn-custom {
            height: 40px !important;
            box-sizing: border-box;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .search-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-muted);
            font-size: 0.9rem;
            height: 40px;
        }

        .search-container input {
            height: 40px !important;
            padding: 0.65rem 1rem !important;
            box-sizing: border-box;
        }

        .table-custom th, .table-custom td {
            vertical-align: middle !important;
        }

        .btn-action-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            color: #ffffff;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 1rem;
            text-decoration: none;
        }

        .btn-action-icon:hover {
            filter: brightness(1.2);
            transform: translateY(-1px);
        }

        .btn-action-icon.btn-dots {
            background-color: #4b7bec;
        }
        .btn-action-icon.btn-edit {
            background-color: #fa8231;
        }
        .btn-action-icon.btn-delete {
            background-color: #eb3b5a;
        }
        .btn-action-icon.btn-view {
            background-color: #2bcbba;
        }

        /* Modal styling */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 1rem;
        }

        .modal-content-custom {
            background-color: var(--panel-bg);
            border: 1px solid var(--panel-border);
            width: 100%;
            max-width: 600px;
            border-radius: 12px;
            overflow: hidden;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header-custom {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #ffffff;
            padding: 1.25rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header-custom h3 {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .modal-close-btn {
            background: transparent;
            border: none;
            color: #ffffff;
            font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.2s;
        }

        .modal-close-btn:hover {
            color: #ff9999;
        }

        .modal-body-custom {
            padding: 1.5rem;
        overflow-y: auto;
        max-height: calc(90vh - 80px);
        }

        .form-group-custom {
            margin-bottom: 1.25rem;
        }

        .form-group-custom label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        .form-group-custom input, 
        .form-group-custom select, 
        .form-group-custom textarea {
            width: 100%;
            background-color: var(--bg-color);
            border: 1px solid var(--panel-border);
            color: #ffffff;
            padding: 0.65rem 1rem;
            border-radius: 6px;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-group-custom input:focus, 
        .form-group-custom select:focus, 
        .form-group-custom textarea:focus {
            border-color: var(--accent-cyan);
        }

        /* Progressively hide columns from right-to-left as screen size decreases, keeping S.No, Project ID, and Actions always visible */
        @media (max-width: 1600px) {
            .col-remarks { display: none !important; }
        }
        @media (max-width: 1450px) {
            .col-type { display: none !important; }
        }
        @media (max-width: 1300px) {
            .col-budget { display: none !important; }
        }
        @media (max-width: 1100px) {
            .col-manager { display: none !important; }
        }
        @media (max-width: 900px) {
            .col-donor { display: none !important; }
        }
        @media (max-width: 700px) {
            .col-agency { display: none !important; }
        }
    
    /* Styled scrollbar for modal body */
    .modal-body-custom::-webkit-scrollbar {
        width: 6px;
    }
    .modal-body-custom::-webkit-scrollbar-track {
        background: var(--bg-color);
        border-radius: 3px;
    }
    .modal-body-custom::-webkit-scrollbar-thumb {
        background: #10b981;
        border-radius: 3px;
    }
    .modal-body-custom::-webkit-scrollbar-thumb:hover {
        background: #059669;
    }

    /* Restrict textarea resize to vertical only */
    .form-group-custom textarea {
        resize: vertical;
        min-height: 80px;
    }
</style>

    <!-- Success & Error Alert Panels -->
    @if (session('success'))
        <div class="alert alert-success" style="background-color: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent-green); color: #8cf5c6; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500;">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid var(--accent-red); color: #ff9999; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500;">
            <ul style="list-style-position: inside; margin: 0; padding: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Table Panel -->
    <div class="panel" style="width: 100%;">
        <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 class="panel-title" style="margin: 0; font-size: 1.25rem; font-weight: 700; color: var(--text-main);">{{ $categoryName }} Projects List</h2>
            <div style="display: flex; gap: 0.75rem;">
                <a href="{{ route('projects.export', $categorySlug) }}" class="btn-custom" style="background: linear-gradient(135deg, #2ecc71, #27ae60); text-decoration: none;">
                    <i class="bx bx-download"></i> Download Excel
                </a>
                <button onclick="openModal()" class="btn-custom">
                    <i class="bx bx-plus-circle"></i> Add Project
                </button>
            </div>
        </div>

        <!-- Search Toolbar -->
        <div style="margin-bottom: 1.25rem; display: flex; justify-content: flex-end;">
            <div style="position: relative; width: 100%; max-width: 320px;">
                <span style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.1rem;"><i class="bx bx-search"></i></span>
                <input type="text" id="tableSearch" placeholder="Search projects..." style="width: 100%; padding: 0.5rem 1rem 0.5rem 2.25rem; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; color: var(--text-main); font-size: 0.875rem; outline: none; transition: border-color 0.2s;" onkeyup="filterTable()">
            </div>
        </div>
        <div style="overflow-x: auto;">
            <table class="table-custom" id="projectsTable">
                <thead>
                    <tr>
                        <th style="width: 60px; text-align: center;">S.No</th>
                        <th>Project ID</th>
                        <th>Project Name</th>
                        <th>Sponsor</th>
                        <th class="col-agency">Agency Project No</th>
                        <th class="col-donor">Donor Name</th>
                        <th class="col-manager">Project Manager</th>
                        <th class="col-budget" style="text-align: right;">Available Budget</th>
                        <th class="col-remarks">Remarks</th>
                        <th style="text-align: center; width: 180px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $index => $project)
                        <tr>
                            <td style="text-align: center;">{{ $index + 1 }}</td>
                            <td style="font-weight: 600; color: var(--accent-cyan);">
                                {{ $project->project_id }}
                            </td>
                            <td>{{ $project->project_name ?? 'N/A' }}</td>
                            <td>{{ $project->sponsor ?? 'N/A' }}</td>
                            <td class="col-agency">{{ $project->agency_project_no ?? 'N/A' }}</td>
                            <td class="col-donor">{{ $project->donor ? $project->donor->name : 'N/A' }}</td>
                            <td class="col-manager">{{ $project->projectManager ? $project->projectManager->name : 'N/A' }}</td>
                            <td class="col-budget" style="text-align: right;">₹{{ number_format($project->available_budget, 2) }}</td>
                            <td class="col-remarks" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $project->remarks ?? 'N/A' }}
                            </td>
                            <td style="text-align: center; white-space: nowrap;">
                                <!-- Details Button -->
                                <button onclick="alert('Project Details:\nID: {{ $project->project_id }}\nName: {{ $project->project_name ?? 'N/A' }}\nSponsor: {{ $project->sponsor ?? 'N/A' }}\nSpec: {{ $project->project_spec ?? 'N/A' }}\nAgency No: {{ $project->agency_project_no }}\nDonor: {{ $project->donor ? $project->donor->name : 'N/A' }}\nManager: {{ $project->projectManager ? $project->projectManager->name : 'N/A' }}\nBudget: ₹{{ number_format($project->available_budget, 2) }}\nRemarks: {{ $project->remarks }}')" class="btn-action-icon btn-dots" title="Details">
                                    <i class="bx bx-dots-horizontal-rounded"></i>
                                </button>

                                @if(in_array(Auth::user()->role, [1, 2, 4]))
                                <!-- Edit Button -->
                                <button onclick="openEditModal({{ json_encode($project) }})" class="btn-action-icon btn-edit" title="Edit">
                                    <i class="bx bx-pencil"></i>
                                </button>

                                <!-- Delete Button -->
                                <form action="{{ route('projects.destroy', $project->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this project?');" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="redirect_category" value="{{ $categorySlug }}">
                                    <input type="hidden" name="type_of_project" value="{{ $project->type_of_project }}">
                                    <button type="submit" class="btn-action-icon btn-delete" title="Delete">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                                @endif

                                <!-- View Details Stage Button -->
                                <a href="{{ route('projects.show', $project->id) }}?type={{ urlencode($project->type_of_project) }}" class="btn-action-icon btn-view" title="Stage Details">
                                    <i class="bx bx-show-alt"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 2rem; color: var(--text-muted);">No projects found in this category.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ADD PROJECT MODAL -->
    <div class="modal-overlay" id="addProjectModal">
        <div class="modal-content-custom">
            <div class="modal-header-custom">
                <h3>Add Project</h3>
                <button onclick="closeModal()" class="modal-close-btn">&times;</button>
            </div>
            <form action="{{ route('projects.store') }}" method="POST">
                @csrf
                <input type="hidden" name="redirect_category" value="{{ $categorySlug }}">
                
                <div class="modal-body-custom">
                    <div class="form-group-custom">
                        <label for="project_name">Project Name</label>
                        <input type="text" name="project_name" id="project_name" required placeholder="Enter project name">
                    </div>

                    <div class="form-group-custom">
                        <label for="sponsor">Sponsor</label>
                        <input type="text" name="sponsor" id="sponsor" required placeholder="Enter sponsor name">
                    </div>

                    <div class="form-group-custom">
                        <label for="project_spec">Project Spec</label>
                        <textarea name="project_spec" id="project_spec" rows="3" placeholder="Enter project specifications"></textarea>
                    </div>

                    <!-- Agency Project No -->
                    <div class="form-group-custom">
                        <label for="agency_project_no">Agency Project No.</label>
                        <input type="text" name="agency_project_no" id="agency_project_no" required placeholder="Enter agency project number">
                    </div>

                    <!-- Donor Name -->
                    <div class="form-group-custom">
                        <label for="donor_id">Donor Name</label>
                        <select name="donor_id" id="donor_id" required>
                            <option value="">Select a donor</option>
                            @foreach($donors as $donor)
                                <option value="{{ $donor->id }}">{{ $donor->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Project Manager -->
                    <div class="form-group-custom">
                        <label for="project_manager_id">Project Manager</label>
                        <select name="project_manager_id" id="project_manager_id" required>
                            <option value="">Select a manager</option>
                            @foreach($managers as $manager)
                                <option value="{{ $manager->id }}">{{ $manager->name }} ({{ $manager->designation ?? 'Staff' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Engineer -->
                    <div class="form-group-custom">
                        <label for="engineer_id">Engineer</label>
                        <select name="engineer_id" id="engineer_id">
                            <option value="">Select an engineer</option>
                            @foreach($engineers as $engineer)
                                <option value="{{ $engineer->id }}">{{ $engineer->name }} ({{ $engineer->designation ?? 'Staff' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Available Budget -->
                    <div class="form-group-custom">
                        <label for="available_budget">Available Budget</label>
                        <input type="number" step="0.01" name="available_budget" id="available_budget" required placeholder="Enter available budget">
                    </div>

                    <!-- Type of Project (Auto-locked) -->
                    <div class="form-group-custom">
                        <label for="type_of_project">Type of Project</label>
                        <input type="text" value="{{ $categoryName }}" disabled style="background-color: var(--bg-color); border: 1px solid var(--panel-border); color: var(--text-muted);">
                        <input type="hidden" name="type_of_project" value="{{ $categoryName }}">
                    </div>

                    <!-- Remarks -->
                    <div class="form-group-custom">
                        <label for="remarks">Remarks</label>
                        <textarea name="remarks" id="remarks" rows="3" placeholder="Enter remarks..."></textarea>
                    </div>

                    <!-- Submit -->
                    <div style="text-align: center; margin-top: 1.5rem;">
                        <button type="submit" class="btn-custom" style="padding: 0.75rem 2rem;">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT PROJECT MODAL -->
    <div class="modal-overlay" id="editProjectModal">
        <div class="modal-content-custom">
            <div class="modal-header-custom">
                <h3>Edit Project</h3>
                <button onclick="closeEditModal()" class="modal-close-btn">&times;</button>
            </div>
            <form id="editProjectForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="redirect_category" value="{{ $categorySlug }}">
                
                <div class="modal-body-custom">
                    <div class="form-group-custom">
                        <label for="edit_project_name">Project Name</label>
                        <input type="text" name="project_name" id="edit_project_name" required placeholder="Enter project name">
                    </div>

                    <div class="form-group-custom">
                        <label for="edit_sponsor">Sponsor</label>
                        <input type="text" name="sponsor" id="edit_sponsor" required placeholder="Enter sponsor name">
                    </div>

                    <div class="form-group-custom">
                        <label for="edit_project_spec">Project Spec</label>
                        <textarea name="project_spec" id="edit_project_spec" rows="3" placeholder="Enter project specifications"></textarea>
                    </div>

                    <!-- Agency Project No -->
                    <div class="form-group-custom">
                        <label for="edit_agency_project_no">Agency Project No.</label>
                        <input type="text" name="agency_project_no" id="edit_agency_project_no" required>
                    </div>

                    <!-- Donor Name -->
                    <div class="form-group-custom">
                        <label for="edit_donor_id">Donor Name</label>
                        <select name="donor_id" id="edit_donor_id" required>
                            <option value="">Select a donor</option>
                            @foreach($donors as $donor)
                                <option value="{{ $donor->id }}">{{ $donor->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Project Manager -->
                    <div class="form-group-custom">
                        <label for="edit_project_manager_id">Project Manager</label>
                        <select name="project_manager_id" id="edit_project_manager_id" required>
                            <option value="">Select a manager</option>
                            @foreach($managers as $manager)
                                <option value="{{ $manager->id }}">{{ $manager->name }} ({{ $manager->designation ?? 'Staff' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Engineer -->
                    <div class="form-group-custom">
                        <label for="edit_engineer_id">Engineer</label>
                        <select name="engineer_id" id="edit_engineer_id">
                            <option value="">Select an engineer</option>
                            @foreach($engineers as $engineer)
                                <option value="{{ $engineer->id }}">{{ $engineer->name }} ({{ $engineer->designation ?? 'Staff' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Available Budget -->
                    <div class="form-group-custom">
                        <label for="edit_available_budget">Available Budget</label>
                        <input type="number" step="0.01" name="available_budget" id="edit_available_budget" required>
                    </div>

                    <!-- Type of Project (Auto-locked) -->
                    <div class="form-group-custom">
                        <label for="edit_type_of_project">Type of Project</label>
                        <input type="text" value="{{ $categoryName }}" disabled style="background-color: var(--bg-color); border: 1px solid var(--panel-border); color: var(--text-muted);">
                        <input type="hidden" name="type_of_project" value="{{ $categoryName }}">
                    </div>

                    <!-- Remarks -->
                    <div class="form-group-custom">
                        <label for="edit_remarks">Remarks</label>
                        <textarea name="remarks" id="edit_remarks" rows="3"></textarea>
                    </div>

                    <!-- Submit -->
                    <div style="text-align: center; margin-top: 1.5rem;">
                        <button type="submit" class="btn-custom" style="padding: 0.75rem 2rem;">Update Project</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Client-side Scripts -->
    <script>
        // Modal functions
        function openModal() {
            document.getElementById('addProjectModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('addProjectModal').style.display = 'none';
        }

        function openEditModal(project) {
            const form = document.getElementById('editProjectForm');
            form.setAttribute('action', `/admin/projects/${project.id}`);

            document.getElementById('edit_project_name').value = project.project_name || '';
            document.getElementById('edit_sponsor').value = project.sponsor || '';
            document.getElementById('edit_project_spec').value = project.project_spec || '';
            document.getElementById('edit_agency_project_no').value = project.agency_project_no || '';
            document.getElementById('edit_donor_id').value = project.donor_id || '';
            document.getElementById('edit_project_manager_id').value = project.project_manager_id || '';
            document.getElementById('edit_engineer_id').value = project.engineer_id || '';
            document.getElementById('edit_available_budget').value = project.available_budget || '';
            document.getElementById('edit_remarks').value = project.remarks || '';

            document.getElementById('editProjectModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editProjectModal').style.display = 'none';
        }

        // Live table search filtering
        function filterTable() {
            const input = document.getElementById('tableSearch');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('projectsTable');
            const trs = table.getElementsByTagName('tr');

            for (let i = 1; i < trs.length; i++) {
                let match = false;
                const tds = trs[i].getElementsByTagName('td');
                for (let j = 0; j < tds.length - 1; j++) {
                    if (tds[j]) {
                        const txtValue = tds[j].textContent || tds[j].innerText;
                        if (txtValue.toLowerCase().indexOf(filter) > -1) {
                            match = true;
                            break;
                        }
                    }
                }
                trs[i].style.display = match ? '' : 'none';
            }
        }

        // Download Excel as CSV client-side
        function downloadExcel() {
            const table = document.getElementById('projectsTable');
            let csv = [];
            const rows = table.querySelectorAll('tr');
            
            for (let i = 0; i < rows.length; i++) {
                const row = [], cols = rows[i].querySelectorAll('td, th');
                
                // Exclude last column (Action column)
                for (let j = 0; j < cols.length - 1; j++) {
                    let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '').replace(/(\s\s+)/gm, ' ');
                    data = data.replace(/"/g, '""');
                    row.push('"' + data + '"');
                }
                csv.push(row.join(','));
            }
            
            const csvContent = "data:text/csv;charset=utf-8," + csv.join('\n');
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "{{ str_replace(' ', '_', strtolower($categoryName)) }}_project_list.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>

@endsection
