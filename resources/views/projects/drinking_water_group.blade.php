@php
    $authUser = auth()->user();
    $isCoo = ($authUser && ($authUser->isCoo() || strtolower($authUser->designation ?? '') === 'coo'));
    $isHod = ($authUser && ($authUser->isHod() || strtolower($authUser->designation ?? '') === 'hod'));
    $isSuperAdmin = ($authUser && $authUser->isSuperAdmin());
    $canCreateProject = $isCoo || $isHod || $isSuperAdmin;
@endphp
@extends('layouts.admin')

@section('title', 'Drinking Water - Group Level Project List')

@section('content')

<!-- Back Button Header -->
<div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
    <a href="{{ route('projects.index') }}" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); padding: 0.5rem 1rem;">
        <i class="bx bx-left-arrow-alt"></i> Back to Dashboard
    </a>
    <h3 style="color: #ffffff; font-size: 1.25rem; font-weight: 600;">Drinking Water - Group Level Project Registry</h3>
</div>

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

<div class="group-header-panel">
    Drinking Water - Group Level PROJECT LIST
</div>

@if (session('success'))
    <div class="alert alert-success" style="background-color: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent-green); color: #8cf5c6; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500;">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid var(--accent-red); color: #ff9999; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500;">
        <i class="bx bx-error-circle" style="margin-right: 0.4rem;"></i> {{ session('error') }}
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

<div class="controls-row">
    <div style="display: flex; gap: 0.75rem;">
        <a href="{{ route('projects.export', 'drinking-water-group-level') }}" class="btn-custom" style="background: linear-gradient(135deg, #2ecc71, #27ae60); text-decoration: none;">
            <i class="bx bx-download"></i> Download Excel
        </a>
        @if($canCreateProject)
        <button onclick="openModal()" class="btn-custom">
            <i class="bx bx-plus-circle"></i> Add Project
        </button>
        @endif
    </div>

    <div class="search-container">
        <span>Search:</span>
        <input type="text" id="tableSearch" onkeyup="filterTable()" class="form-control-dark" style="width: 200px; padding: 0.4rem 0.8rem; font-size: 0.85rem;" placeholder="Search projects...">
    </div>
</div>

<div class="panel" style="width: 100%;">
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
                            @if(Auth::user()->hasAdminAccess())
                            <button onclick="alert('Project Details:\nID: {{ $project->project_id }}\nName: {{ $project->project_name ?? 'N/A' }}\nSponsor: {{ $project->sponsor ?? 'N/A' }}\nTheme: {{ $project->theme ?? 'N/A' }}\nSubtheme: {{ $project->subtheme ?? 'N/A' }}\nActivity: {{ $project->activity ?? 'N/A' }}\nSpec: {{ $project->project_spec ?? 'N/A' }}\nAgency No: {{ $project->agency_project_no }}\nDonor: {{ $project->donor ? $project->donor->name : 'N/A' }}\nManager: {{ $project->projectManager ? $project->projectManager->name : 'N/A' }}\nEngineer: {{ $project->engineer ? $project->engineer->name : 'N/A' }}\nUnit: {{ $project->unit ?? 'RCFI' }}\nBudget: ₹{{ number_format($project->available_budget, 2) }}\nRemarks: {{ $project->remarks }}')" class="btn-action-icon btn-dots" title="Details">
                                <i class="bx bx-dots-horizontal-rounded"></i>
                            </button>

                            <button onclick="openEditModal({{ json_encode($project) }})" class="btn-action-icon btn-edit" title="Edit">
                                <i class="bx bx-pencil"></i>
                            </button>

                            <form action="{{ route('projects.destroy', $project->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this project?');" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="redirect_category" value="drinking-water-group-level">
                                <input type="hidden" name="type_of_project" value="{{ $project->type_of_project }}">
                                <button type="submit" class="btn-action-icon btn-delete" title="Delete">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </form>
                            @endif

                            <a href="{{ route('projects.show', $project->id) }}?type={{ urlencode($project->type_of_project) }}" class="btn-action-icon btn-view" title="Stage Details">
                                <i class="bx bx-show-alt"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 2rem; color: var(--text-muted);">No drinking water - group level projects registered yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="addProjectModal">
    <div class="modal-content-custom">
        <div class="modal-header-custom">
            <h3>Add Drinking Water - Group Level Project</h3>
            <button onclick="closeModal()" class="modal-close-btn">&times;</button>
        </div>
        <form action="{{ route('projects.store') }}" method="POST">
            @csrf
            <input type="hidden" name="redirect_category" value="drinking-water-group-level">

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

                <div class="form-group-custom">
                    <label for="agency_project_no">Agency Project No.</label>
                    <input type="text" name="agency_project_no" id="agency_project_no" required placeholder="Enter agency project number">
                </div>

                <div class="form-group-custom">
                    <label for="donor_id">Donor Name</label>
                    <select name="donor_id" id="donor_id" required>
                        <option value="">Select a donor</option>
                        @foreach($donors as $donor)
                            <option value="{{ $donor->id }}">{{ $donor->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-custom">
                    <label for="project_manager_id">Project Manager</label>
                    <select name="project_manager_id" id="project_manager_id" required>
                        <option value="">Select a manager</option>
                        @foreach($managers as $manager)
                            <option value="{{ $manager->id }}">{{ $manager->name }} ({{ $manager->designation ?? 'Staff' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-custom">
                    <label for="engineer_id">Engineer</label>
                    <select name="engineer_id" id="engineer_id">
                        <option value="">Select an engineer</option>
                        @foreach($engineers as $engineer)
                            <option value="{{ $engineer->id }}">{{ $engineer->name }} ({{ $engineer->designation ?? 'Staff' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-custom">
                    <label for="engineer_id">Engineer</label>
                    <select name="engineer_id" id="engineer_id">
                        <option value="">Select an engineer</option>
                        @foreach($engineers as $engineer)
                            <option value="{{ $engineer->id }}">{{ $engineer->name }} ({{ $engineer->designation ?? 'Staff' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-custom">
                    <label for="unit">Unit</label>
                    <select name="unit" id="unit" required>
                        <option value="RCFI">RCFI</option>
                        <option value="MARKAZ">MARKAZ</option>
                    </select>
                </div>

                <div class="form-group-custom">
                    <label for="available_budget">Available Budget</label>
                    <input type="number" step="0.01" name="available_budget" id="available_budget" required placeholder="Enter available budget">
                </div>
                <div class="form-group-custom">
                    <label for="add_theme">Theme</label>
                    <select name="theme" id="add_theme" required onchange="populateSubthemes('add_theme', 'add_subtheme')">
                        <option value="">Select Theme</option>
                        @foreach($themes as $t)
                            <option value="{{ $t->name }}" data-theme-id="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-custom">
                    <label for="add_subtheme">Subtheme</label>
                    <select name="subtheme" id="add_subtheme" required>
                        <option value="">Select Subtheme</option>
                    </select>
                </div>

                <div class="form-group-custom">
                    <label for="add_activity">Activity</label>
                    <input type="text" name="activity" id="add_activity" required placeholder="Enter activity">
                </div>

                <div class="form-group-custom">
                    <label for="type_of_project">Type of Project</label>
                    <input type="text" value="Drinking Water - Group Level" disabled style="background-color: var(--bg-color); border: 1px solid var(--panel-border); color: var(--text-muted);">
                    <input type="hidden" name="type_of_project" value="Drinking Water - Group Level">
                </div>

                <div class="form-group-custom">
                    <label for="remarks">Remarks</label>
                    <textarea name="remarks" id="remarks" rows="3" placeholder="Enter remarks..."></textarea>
                </div>

                <div style="text-align: center; margin-top: 1.5rem;">
                    <button type="submit" class="btn-custom" style="padding: 0.75rem 2rem;">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="editProjectModal">
    <div class="modal-content-custom">
        <div class="modal-header-custom">
            <h3>Edit Drinking Water - Group Level Project</h3>
            <button onclick="closeEditModal()" class="modal-close-btn">&times;</button>
        </div>
        <form id="editProjectForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="redirect_category" value="drinking-water-group-level">

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

                <div class="form-group-custom">
                    <label for="edit_agency_project_no">Agency Project No.</label>
                    <input type="text" name="agency_project_no" id="edit_agency_project_no" required>
                </div>

                <div class="form-group-custom">
                    <label for="edit_donor_id">Donor Name</label>
                    <select name="donor_id" id="edit_donor_id" required>
                        <option value="">Select a donor</option>
                        @foreach($donors as $donor)
                            <option value="{{ $donor->id }}">{{ $donor->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-custom">
                    <label for="edit_project_manager_id">Project Manager</label>
                    <select name="project_manager_id" id="edit_project_manager_id" required>
                        <option value="">Select a manager</option>
                        @foreach($managers as $manager)
                            <option value="{{ $manager->id }}">{{ $manager->name }} ({{ $manager->designation ?? 'Staff' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-custom">
                    <label for="edit_engineer_id">Engineer</label>
                    <select name="engineer_id" id="edit_engineer_id">
                        <option value="">Select an engineer</option>
                        @foreach($engineers as $engineer)
                            <option value="{{ $engineer->id }}">{{ $engineer->name }} ({{ $engineer->designation ?? 'Staff' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-custom">
                    <label for="edit_engineer_id">Engineer</label>
                    <select name="engineer_id" id="edit_engineer_id">
                        <option value="">Select an engineer</option>
                        @foreach($engineers as $engineer)
                            <option value="{{ $engineer->id }}">{{ $engineer->name }} ({{ $engineer->designation ?? 'Staff' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-custom">
                    <label for="edit_unit">Unit</label>
                    <select name="unit" id="edit_unit" required>
                        <option value="RCFI">RCFI</option>
                        <option value="MARKAZ">MARKAZ</option>
                    </select>
                </div>

                <div class="form-group-custom">
                    <label for="edit_available_budget">Available Budget</label>
                    <input type="number" step="0.01" name="available_budget" id="edit_available_budget" required>
                </div>
                <div class="form-group-custom">
                    <label for="edit_theme">Theme</label>
                    <select name="theme" id="edit_theme" required onchange="populateSubthemes('edit_theme', 'edit_subtheme')">
                        <option value="">Select Theme</option>
                        @foreach($themes as $t)
                            <option value="{{ $t->name }}" data-theme-id="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-custom">
                    <label for="edit_subtheme">Subtheme</label>
                    <select name="subtheme" id="edit_subtheme" required>
                        <option value="">Select Subtheme</option>
                    </select>
                </div>

                <div class="form-group-custom">
                    <label for="edit_activity">Activity</label>
                    <input type="text" name="activity" id="edit_activity" required placeholder="Enter activity">
                </div>

                <div class="form-group-custom">
                    <label for="edit_type_of_project">Type of Project</label>
                    <input type="text" value="Drinking Water - Group Level" disabled style="background-color: var(--bg-color); border: 1px solid var(--panel-border); color: var(--text-muted);">
                    <input type="hidden" name="type_of_project" value="Drinking Water - Group Level">
                </div>

                <div class="form-group-custom">
                    <label for="edit_remarks">Remarks</label>
                    <textarea name="remarks" id="edit_remarks" rows="3"></textarea>
                </div>

                <div style="text-align: center; margin-top: 1.5rem;">
                    <button type="submit" class="btn-custom" style="padding: 0.75rem 2rem;">Update Project</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('addProjectModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('addProjectModal').style.display = 'none';
    }

    function openEditModal(projectData) {
        const form = document.getElementById('editProjectForm');
        form.setAttribute('action', `/admin/projects/${projectData.id}`);

        document.getElementById('edit_project_name').value = projectData.project_name || '';
        document.getElementById('edit_sponsor').value = projectData.sponsor || '';
        document.getElementById('edit_project_spec').value = projectData.project_spec || '';
        document.getElementById('edit_agency_project_no').value = projectData.agency_project_no || '';
        document.getElementById('edit_donor_id').value = projectData.donor_id || '';
        document.getElementById('edit_project_manager_id').value = projectData.project_manager_id || '';
        document.getElementById('edit_engineer_id').value = projectData.engineer_id || '';
        document.getElementById('edit_engineer_id').value = projectData.engineer_id || '';
        document.getElementById('edit_unit').value = projectData.unit || 'RCFI';
        document.getElementById('edit_available_budget').value = projectData.available_budget || '';
        document.getElementById('edit_remarks').value = projectData.remarks || '';
        const currentProj = (typeof project !== 'undefined' ? project : (typeof projectData !== 'undefined' ? projectData : {}));
        document.getElementById('edit_theme').value = currentProj.theme || '';
        populateSubthemes('edit_theme', 'edit_subtheme', currentProj.subtheme || '');
        document.getElementById('edit_activity').value = currentProj.activity || '';

        document.getElementById('editProjectModal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editProjectModal').style.display = 'none';
    }

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

    const themesData = {
        @foreach($themes as $t)
            "{{ $t->id }}": [
                @foreach($subthemes->where('theme_id', $t->id) as $st)
                    {!! json_encode($st->name) !!},
                @endforeach
            ],
        @endforeach
    };

    function populateSubthemes(themeId, subthemeId, selectedSubtheme = '') {
        const themeSelect = document.getElementById(themeId);
        const subthemeSelect = document.getElementById(subthemeId);
        if (!themeSelect || !subthemeSelect) return;

        const selectedOption = themeSelect.options[themeSelect.selectedIndex];
        const themeIdVal = selectedOption ? selectedOption.getAttribute('data-theme-id') : null;
        subthemeSelect.innerHTML = '<option value="">Select Subtheme</option>';

        if (themeIdVal && themesData[themeIdVal]) {
            themesData[themeIdVal].forEach(sub => {
                const option = document.createElement('option');
                option.value = sub;
                option.textContent = sub;
                if (sub === selectedSubtheme) {
                    option.selected = true;
                }
                subthemeSelect.appendChild(option);
            });
        }
    }

</script>


@endsection

