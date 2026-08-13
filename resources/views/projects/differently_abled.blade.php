@php
    $authUser = auth()->user();
    $isCoo = ($authUser && ($authUser->isCoo() || strtolower($authUser->designation ?? '') === 'coo'));
    $isHod = ($authUser && ($authUser->isHod() || strtolower($authUser->designation ?? '') === 'hod'));
    $isSuperAdmin = ($authUser && $authUser->isSuperAdmin());
    $canCreateProject = $isCoo || $isHod || $isSuperAdmin;
@endphp
@extends('layouts.admin')

@section('title', 'Differently Abled Project List')

@section('content')

<link rel="stylesheet" href="{{ asset('css/projects_common.css') }}">
<script src="{{ asset('js/projects_common.js') }}"></script>
<div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
    <a href="{{ route('projects.index') }}" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); padding: 0.5rem 1rem;">
        <i class="bx bx-left-arrow-alt"></i> Back to Dashboard
    </a>
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
    .btn-action-icon.btn-delete, .btn-action-icon.btn-pdf {
        background-color: #eb3b5a;
        color: #ffffff;
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
    Differently Abled PROJECT LIST
</div>

@if (session('success'))
    <div class="alert alert-success" style="background-color: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent-green); color: #047857; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500;">
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
        @if(Auth::user() && Auth::user()->canDownloadExcel())
        <a href="{{ route('projects.export', 'differently-abled') }}" id="excelExportBtn" class="excel-export-btn btn-custom" style="background: linear-gradient(135deg, #2ecc71, #27ae60); text-decoration: none;">
            <i class="bx bx-download"></i> Download Excel
        </a>
        @endif
    </div>


            <!-- Search & Filter Toolbar -->
        <div style="margin-bottom: 1.25rem; display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center; justify-content: flex-end;">
            <select id="filterManager" onchange="filterTable()" style="padding: 0.45rem 0.75rem; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; color: var(--text-main); font-size: 0.85rem; outline: none; min-width: 150px;">
                <option value="">All Project Managers</option>
            </select>

            <select id="filterAgency" onchange="filterTable()" style="padding: 0.45rem 0.75rem; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; color: var(--text-main); font-size: 0.85rem; outline: none; min-width: 140px;">
                <option value="">All Agencies</option>
            </select>

            <select id="filterDistrict" onchange="filterTable()" style="padding: 0.45rem 0.75rem; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; color: var(--text-main); font-size: 0.85rem; outline: none; min-width: 130px;">
                <option value="">All Districts</option>
            </select>

            <select id="filterState" onchange="filterTable()" style="padding: 0.45rem 0.75rem; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; color: var(--text-main); font-size: 0.85rem; outline: none; min-width: 120px;">
                <option value="">All States</option>
            </select>

            <div style="position: relative; width: 100%; max-width: 220px;">
                <span style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.1rem;"><i class="bx bx-search"></i></span>
                <input type="text" id="tableSearch" placeholder="Search projects..." style="width: 100%; padding: 0.45rem 0.75rem 0.45rem 2.25rem; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; color: var(--text-main); font-size: 0.85rem; outline: none; transition: border-color 0.2s;" onkeyup="filterTable()">
            </div>
        </div>
</div>

<div class="panel" style="width: 100%;">
    <div style="overflow-x: auto;">
        <table class="table-custom" id="projectsTable">
            <thead>
                <tr>
                    <th style="width: 60px; text-align: center;">S.NO</th>
                    <th>RCFI ID</th>
                    <th class="col-agency">AGENCY NO</th>
                    <th class="col-agency-name">AGENCY NAME</th>
                    <th class="col-activity">ACTIVITY</th>
                    <th class="col-place">PLACE</th>
                    <th class="col-district">DISTRICT</th>
                    <th class="col-manager">PROJECT MANAGER</th>
                    <th class="col-allocated" style="text-align: right;">TOTAL ALLOCATED</th>
                    <th class="col-balance" style="text-align: right;">TOTAL BALANCE</th>
                    <th style="text-align: center; width: 180px;">ACTION</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $index => $project)
                    @php
                        $app = $project->application;
                        $appMeta = $app->meta ?? [];
                        $searchTerms = [
                            $project->project_id,
                            $project->project_name,
                            $project->sponsor,
                            $project->agency_project_no,
                            $project->donor?->name,
                            $project->projectManager?->name,
                            $project->remarks,
                            $app?->application_id ?? ($app ? 'APLRCFI' . $app->id : null),
                            $app?->applicant_name,
                            $app?->father_name ?? ($appMeta['father_name'] ?? null),
                            $app?->mother_name ?? ($appMeta['mother_name'] ?? null),
                            $app?->place ?? ($appMeta['place'] ?? null),
                            $app?->district ?? ($appMeta['district'] ?? null),
                            $app?->state ?? ($appMeta['state'] ?? null),
                            $app?->agency_number ?? ($appMeta['agency_number'] ?? null),
                            $app?->cluster?->name,
                            $app?->mobile_1 ?? ($appMeta['mobile_1'] ?? ($appMeta['contact_number_1'] ?? null)),
                            $app?->mobile_2 ?? ($appMeta['mobile_2'] ?? ($appMeta['contact_number_2'] ?? null)),
                        ];
                        $searchString = strtolower(implode(' ', array_filter($searchTerms)));
                    @endphp
                    <tr class="project-row" data-search="{{ $searchString }}">
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="font-weight: 600; color: var(--accent-cyan);">
                            {{ $project->project_id }}
                        </td>
                        <td class="col-agency">{{ $project->agency_project_no ?? 'N/A' }}</td>
                        <td class="col-agency-name">{{ $project->sponsor ?? ($project->donor ? $project->donor->name : ($project->agency_name ?? 'N/A')) }}</td>
                        <td class="col-activity">{{ $project->activity ?? $project->project_name ?? $project->type_of_project ?? 'N/A' }}</td>
                        <td class="col-place">{{ $project->place ?? $project->location ?? ($project->application ? ($project->application->place ?? $project->application->location ?? 'N/A') : 'N/A') }}</td>
                        <td class="col-district">{{ $project->district ?? ($project->application ? ($project->application->district ?? ($project->application->meta['district'] ?? ($project->application->meta['locality_district'] ?? 'N/A'))) : 'N/A') }}</td>
                        <td class="col-manager">{{ $project->projectManager ? $project->projectManager->name : 'N/A' }}</td>
                        <td class="col-allocated" style="text-align: right;">{!! "&#x20B9;" !!}{{ number_format($project->total_allocated ?? 0, 2) }}</td>
                        <td class="col-balance" style="text-align: right;">{!! "&#x20B9;" !!}{{ number_format(($project->total_allocated ?? 0) - ($project->total_spent ?? 0), 2) }}</td>
                        <td style="text-align: center; white-space: nowrap;">
                            @if(Auth::user()->hasAdminAccess())
                            <button onclick="alert('Project Details:\nID: {{ $project->project_id }}\nName: {{ $project->project_name ?? 'N/A' }}\nSponsor: {{ $project->sponsor ?? 'N/A' }}\nTheme: {{ $project->theme ?? 'N/A' }}\nSubtheme: {{ $project->subtheme ?? 'N/A' }}\nActivity: {{ $project->activity ?? 'N/A' }}\nSpec: {{ $project->project_spec ?? 'N/A' }}\nAgency No: {{ $project->agency_project_no }}\nAgency: {{ $project->donor ? $project->donor->name : 'N/A' }}\nManager: {{ $project->projectManager ? $project->projectManager->name : 'N/A' }}\nBudget: ₹{{ number_format($project->available_budget, 2) }}\nRemarks: {{ $project->remarks }}')" class="btn-action-icon btn-dots" title="Details">
                                <i class="bx bx-dots-horizontal-rounded"></i>
                            </button>

                            <!-- PDF Report Button -->
                            <a href="{{ route('projects.pdf', [$project->id, 'category' => 'differently-abled']) }}" onclick="downloadDirectPdf(event, this.href)" class="btn-action-icon btn-pdf" title="Download PDF Report">
                                <i class="bx bxs-file-pdf"></i>
                            </a>

                            <button type="button"
                                id="suspend-btn-{{ $project->id }}"
                                class="btn-action-icon"
                                data-project-id="{{ $project->id }}"
                                data-url="{{ route('projects.differently_abled.toggle_suspend', $project->id) }}"
                                data-status="{{ $project->status ?? 'Active' }}"
                                title="{{ ($project->status ?? 'Active') === 'Suspended' ? 'Reactivate Project' : 'Suspend Project' }}"
                                style="background-color: {{ ($project->status ?? 'Active') === 'Suspended' ? 'rgba(16,185,129,0.15)' : 'rgba(245,158,11,0.15)' }}; color: {{ ($project->status ?? 'Active') === 'Suspended' ? '#10b981' : '#f59e0b' }}; border: 1px solid {{ ($project->status ?? 'Active') === 'Suspended' ? 'rgba(16,185,129,0.4)' : 'rgba(245,158,11,0.4)' }};"
                                onclick="toggleSuspend(this)">
                                <i class="bx {{ ($project->status ?? 'Active') === 'Suspended' ? 'bx-lock-open-alt' : 'bx-lock-alt' }}"></i>
                            </button>
                            @endif

                            <button type="button" data-id="{{ $project->id }}" data-name="{{ $project->project_name ?? '' }}" data-agency="{{ $project->agency_project_no ?? '' }}" onclick="openAddProgrammeModal(this)" class="btn-action-icon btn-add-prog" title="Add Programme" style="background-color: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); cursor: pointer;">
                                <i class="bx bx-plus-circle"></i>
                            </button>

                            <a href="{{ route('projects.show', $project->id) }}?type={{ urlencode($project->type_of_project) }}" class="btn-action-icon btn-view" title="Stage Details">
                                <i class="bx bx-show-alt"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 2rem; color: var(--text-muted);">No differently abled projects registered yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="addProjectModal">
    <div class="modal-content-custom">
        <div class="modal-header-custom">
            <h3>Add Differently Abled Project</h3>
            <button type="button" class="modal-close-btn" onclick="closeModal()" title="Close">&times;</button>
        </div>
        <form action="{{ route('projects.store') }}" method="POST">
            @csrf
            <input type="hidden" name="redirect_category" value="differently-abled">

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
                    <label for="donor_id">Agency Name</label>
                    <select name="donor_id" id="donor_id" required>
                        <option value="">Select an agency</option>
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
                            <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-custom">
                    <label for="engineer_id">Engineer</label>
                    <select name="engineer_id" id="engineer_id">
                        <option value="">Select an engineer</option>
                        @foreach($engineers as $engineer)
                            <option value="{{ $engineer->id }}">{{ $engineer->name }}</option>
                        @endforeach
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
                    <input type="text" value="Differently Abled" disabled style="background-color: var(--bg-color); border: 1px solid var(--panel-border); color: var(--text-muted);">
                    <input type="hidden" name="type_of_project" value="Differently Abled">
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
            <h3>Edit Differently Abled Project</h3>
            <button type="button" class="modal-close-btn" onclick="closeEditModal()" title="Close">&times;</button>
        </div>
        <form id="editProjectForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="redirect_category" value="differently-abled">

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
                    <label for="edit_donor_id">Agency Name</label>
                    <select name="donor_id" id="edit_donor_id" required>
                        <option value="">Select an agency</option>
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
                            <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-custom">
                    <label for="edit_engineer_id">Engineer</label>
                    <select name="engineer_id" id="edit_engineer_id">
                        <option value="">Select an engineer</option>
                        @foreach($engineers as $engineer)
                            <option value="{{ $engineer->id }}">{{ $engineer->name }}</option>
                        @endforeach
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
                    <input type="text" value="Differently Abled" disabled style="background-color: var(--bg-color); border: 1px solid var(--panel-border); color: var(--text-muted);">
                    <input type="hidden" name="type_of_project" value="Differently Abled">
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
        const modal = document.getElementById('addProjectModal') || document.getElementById("addAppModal") || document.getElementById("addProjectModal") || document.getElementById("addModal");
        if (modal) modal.style.display = "flex";
    }

    function closeModal() {
        const modal = document.getElementById('addProjectModal') || document.getElementById('addAppModal') || document.getElementById('addModal');
        if (modal) {
            modal.style.display = 'none';
        } else {
            document.querySelectorAll('.modal-overlay').forEach(m => m.style.display = 'none');
        }
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
        const currentProj = (typeof project !== 'undefined' ? project : (typeof projectData !== 'undefined' ? projectData : {}));
        document.getElementById('edit_theme').value = currentProj.theme || '';
        populateSubthemes('edit_theme', 'edit_subtheme', currentProj.subtheme || '');
        document.getElementById('edit_activity').value = currentProj.activity || '';

        document.getElementById('editProjectModal').style.display = 'flex';
    }

    function closeEditModal() {
        const modal = document.getElementById('editProjectModal') || document.getElementById('editAppModal') || document.getElementById('editModal');
        if (modal) {
            modal.style.display = 'none';
        } else {
            document.querySelectorAll('.modal-overlay').forEach(m => m.style.display = 'none');
        }
    }

    function initProjectFilters() {
            const filterManager = document.getElementById('filterManager');
            const filterAgency = document.getElementById('filterAgency');
            const filterDistrict = document.getElementById('filterDistrict');
            const filterState = document.getElementById('filterState');
            
            if (!filterManager && !filterAgency && !filterDistrict && !filterState) return;

            const managers = new Set();
            const agencies = new Set();
            const districts = new Set();
            const states = new Set();

            const rows = document.querySelectorAll('#projectsTable tbody tr.project-row');
            rows.forEach(row => {
                const m = row.getAttribute('data-manager-display') || row.getAttribute('data-manager');
                const a = row.getAttribute('data-agency-display') || row.getAttribute('data-agency');
                const d = row.getAttribute('data-district-display') || row.getAttribute('data-district');
                const s = row.getAttribute('data-state-display') || row.getAttribute('data-state');

                if (m && m.toLowerCase() !== 'n/a') managers.add(m.trim());
                if (a && a.toLowerCase() !== 'n/a') agencies.add(a.trim());
                if (d && d.toLowerCase() !== 'n/a') districts.add(d.trim());
                if (s && s.toLowerCase() !== 'n/a') states.add(s.trim());
            });

            populateSelectOptions(filterManager, managers, 'All Project Managers');
            populateSelectOptions(filterAgency, agencies, 'All Agencies');
            populateSelectOptions(filterDistrict, districts, 'All Districts');
            populateSelectOptions(filterState, states, 'All States');
        }

        function populateSelectOptions(selectEl, setValues, defaultText) {
            if (!selectEl) return;
            const currentVal = selectEl.value;
            selectEl.innerHTML = `<option value="">${defaultText}</option>`;
            Array.from(setValues).sort().forEach(val => {
                const opt = document.createElement('option');
                opt.value = val.toLowerCase();
                opt.textContent = val;
                if (val.toLowerCase() === currentVal.toLowerCase()) {
                    opt.selected = true;
                }
                selectEl.appendChild(opt);
            });
        }

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

        document.addEventListener('DOMContentLoaded', function() {
            initProjectFilters();
        });

    var themesData = {
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

    async function handleAddProgrammeSubmit(e) {
        e.preventDefault();
        const form = e.target;
        
        // Close modal IMMEDIATELY
        if (typeof closeAddProgrammeModal === 'function') {
            closeAddProgrammeModal();
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
        }

        try {
            const formData = new FormData(form);
            const actionUrl = form.action || window.location.href;
            const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}';
            
            const response = await fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            let data = {};
            try {
                data = await response.json();
            } catch(jsonErr) {}

            form.reset();
            const msg = data.message || 'Programme added successfully!';
            if (typeof showToast === 'function') {
                showToast(msg, 'success');
            } else {
                alert(msg);
            }
            window.location.reload();
        } catch (err) {
            console.error(err);
            form.reset();
            window.location.reload();
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
            }
        }
    }
    window.handleAddProgrammeSubmit = handleAddProgrammeSubmit;

    async function handleDeleteProgramme(btnElement, progId, deleteUrl) {
        if (!confirm('Are you sure you want to delete this programme? This action cannot be undone.')) {
            return;
        }

        const row = btnElement ? btnElement.closest('tr') : null;
        if (row) {
            row.style.opacity = '0.5';
            row.style.pointerEvents = 'none';
        }

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}';
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
                        if (typeof updateProgrammeTableSerialNumbers === 'function') {
                            updateProgrammeTableSerialNumbers();
                        }
                    }, 300);
                } else {
                    window.location.reload();
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
    window.handleDeleteProgramme = handleDeleteProgramme;

    async function toggleProgrammeChecklistTick(btnElement, progIndex, field) {
        window.toggleProgrammeChecklistTick = toggleProgrammeChecklistTick;
        const icon = btnElement ? btnElement.querySelector('i') : null;
        const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}';

        try {
            if (btnElement) {
                btnElement.style.transform = 'scale(0.9)';
                setTimeout(() => btnElement.style.transform = 'scale(1)', 150);
            }

            const response = await fetch(`/admin/projects/{{ $projectRouteSlug ?? "orphan-care" }}/{{ $project->id ?? 0 }}/toggle-programme-tick`, {
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
                if (btnElement) {
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
    window.toggleProgrammeChecklistTick = toggleProgrammeChecklistTick;



    window.openAddProgrammeModal = function openAddProgrammeModal(param) {
        let projectId, projectName, agencyNo;

        if (param && param.dataset) {
            projectId = param.dataset.id;
            projectName = param.dataset.name;
            agencyNo = param.dataset.agency;
        } else if (typeof param === 'object' && param !== null) {
            projectId = param.id;
            projectName = param.project_name;
            agencyNo = param.agency_project_no;
        } else {
            projectId = param;
        }

        if (!projectId) {
            projectId = "{{ $project->id ?? '' }}";
        }

        const modal = document.getElementById('addProgrammeModal');
        if (modal) {
            if (modal.parentElement !== document.body) {
                document.body.appendChild(modal);
            }
            modal.style.setProperty('z-index', '999999', 'important');
            modal.style.setProperty('display', 'flex', 'important');

            if (projectId) {
                const form = document.getElementById('addProgrammeForm');
                if (form) {
                    form.action = `/admin/projects/differently-abled/${projectId}/add-programme`;
                }
            }

            const nameElem = document.getElementById('prog_modal_student_name');
            if (nameElem && projectName) nameElem.textContent = projectName;

            const agencyElem = document.getElementById('prog_modal_agency_no');
            if (agencyElem && agencyNo) agencyElem.textContent = agencyNo;
        }
    };

    function closeAddProgrammeModal() {
        const modal = document.getElementById("addProgrammeModal");
        if (modal) modal.style.display = "none";
    }
    window.closeAddProgrammeModal = closeAddProgrammeModal;

    function toggleSuspend(btn) {
        const projectId = btn.dataset.projectId;
        const url = btn.dataset.url;
        const currentStatus = btn.dataset.status;
        const isSuspended = currentStatus === 'Suspended';
        const confirmMsg = isSuspended ? 'Reactivate this project?' : 'Suspend this project?';

        const doToggle = async () => {
            btn.disabled = true;
            btn.style.opacity = '0.6';

            try {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();
                if (!res.ok || !data.success) throw new Error(data.message || 'Error');

                const newStatus = data.status;
                if (typeof updateProjectStatusUI === 'function') {
                    updateProjectStatusUI(projectId, newStatus);
                }

                if (typeof showToast === 'function') showToast(data.message, 'success');
            } catch (err) {
                alert(err.message || 'Failed to update project status.');
            } finally {
                btn.disabled = false;
                btn.style.opacity = '1';
            }
        };

        if (typeof showCustomConfirm === 'function') {
            showCustomConfirm(confirmMsg, doToggle);
        } else if (confirm(confirmMsg)) {
            doToggle();
        }
    }
    window.toggleSuspend = toggleSuspend;


        // Global Window Bindings
        window.openModal = openModal;
        window.closeModal = closeModal;
        window.openEditModal = openEditModal;
        window.closeEditModal = closeEditModal;
    
    function closeProgrammeModal() {
        const modal = document.getElementById('addProgrammeModal');
        if (modal) modal.style.display = 'none';
    }

</script>

<!-- Add Programme Modal -->
<div class="modal-overlay" id="addProgrammeModal" style="display: none;">
    <div class="modal-content-custom" style="max-width: 600px; max-height: 90vh; overflow-y: auto; background-color: var(--panel-bg); border: 1px solid var(--panel-border); padding: 2rem; border-radius: 12px;">
        <div class="modal-header-custom" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0; color: var(--text-main); font-size: 1.2rem; font-weight: 700; text-transform: uppercase;">ADD NEW PROGRAMME</h3>
            
        </div>

        <form id="addProgrammeForm" method="POST" action="" onsubmit="handleAddProgrammeSubmit(event); return false;">
            @csrf
            <!-- Student / Beneficiary Name & Agency Project No Banner -->
            <div style="background-color: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 8px; padding: 0.85rem 1rem; margin-bottom: 1.25rem; display: flex; flex-wrap: wrap; justify-content: space-between; gap: 0.5rem; font-size: 0.85rem;">
                <div>
                    <span style="color: #475569; font-weight: 600;">Student / Beneficiary Name:</span>
                    <span id="prog_modal_student_name" style="color: #0284c7; font-weight: 700; margin-left: 0.35rem;">-</span>
                </div>
                <div>
                    <span style="color: #475569; font-weight: 600;">Agency Project No:</span>
                    <span id="prog_modal_agency_no" style="color: #0f172a; font-weight: 700; margin-left: 0.35rem;">-</span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="grid-column: span 2;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem; font-weight: 600;">Programme Name *</label>
                    <select name="programme_name" id="diff_add_prog_name_select" required class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" onchange="if(typeof toggleSpecifyProgrammeField === 'function') toggleSpecifyProgrammeField(this, 'diff_add_prog_other_name_wrapper', 'diff_add_prog_other_name_input')">
                        <option value="" disabled selected>-- Select Programme --</option>
                        <option value="Cluster Camp">Cluster Camp</option>
                        <option value="Report Collection Programme">Report Collection Programme</option>
                        <option value="Others">Others</option>
                    </select>
                </div>
                <div id="diff_add_prog_other_name_wrapper" style="grid-column: span 2; display: none;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem; font-weight: 600;">Specify Programme Name *</label>
                    <input type="text" id="diff_add_prog_other_name_input" name="other_programme_name" placeholder="Enter custom programme name..." class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;">
                </div>
                <div>
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem; font-weight: 600;">Date *</label>
                    <input type="date" name="date" required class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;">
                </div>
                <div>
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem; font-weight: 600;">Place</label>
                    <input type="text" name="place" placeholder="e.g. Main Auditorium" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;">
                </div>
                <div style="grid-column: span 2;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem; font-weight: 600;">Remarks</label>
                    <input type="text" name="remarks" placeholder="Enter remarks (optional)..." class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;">
                </div>
            </div>

            <h4 style="color: var(--accent-cyan); font-size: 0.9rem; text-transform: uppercase; margin: 1.5rem 0 1rem 0; font-weight: 700; border-bottom: 1px solid var(--panel-border); padding-bottom: 0.4rem;">TICK CHECKLIST (SELECT COMPLETED ITEMS)</h4>
            
            <div style="display: grid; grid-template-columns: 1fr; gap: 0.85rem; margin-bottom: 1.5rem; background: rgba(255,255,255,0.02); padding: 1rem; border-radius: 8px; border: 1px solid var(--panel-border);">
                <label style="display: flex; align-items: center; gap: 0.75rem; color: var(--text-main); font-size: 0.9rem; font-weight: 600; cursor: pointer; padding: 0.25rem 0;">
                    <input type="checkbox" name="present_ticked" value="1" style="width: 18px; height: 18px; cursor: pointer;">
                    Present / Attendance
                </label>
                <label style="display: flex; align-items: center; gap: 0.75rem; color: var(--text-main); font-size: 0.9rem; font-weight: 600; cursor: pointer; padding: 0.25rem 0;">
                    <input type="checkbox" name="photo_ticked" value="1" style="width: 18px; height: 18px; cursor: pointer;">
                    Photo
                </label>
                <label style="display: flex; align-items: center; gap: 0.75rem; color: var(--text-main); font-size: 0.9rem; font-weight: 600; cursor: pointer; padding: 0.25rem 0;">
                    <input type="checkbox" name="marklist_ticked" value="1" style="width: 18px; height: 18px; cursor: pointer;">
                    Marklist
                </label>
                <label style="display: flex; align-items: center; gap: 0.75rem; color: var(--text-main); font-size: 0.9rem; font-weight: 600; cursor: pointer; padding: 0.25rem 0;">
                    <input type="checkbox" name="thanks_letter_ticked" value="1" style="width: 18px; height: 18px; cursor: pointer;">
                    Thanks Letter
                </label>
                <label style="display: flex; align-items: center; gap: 0.75rem; color: var(--text-main); font-size: 0.9rem; font-weight: 600; cursor: pointer; padding: 0.25rem 0;">
                    <input type="checkbox" name="report_form_ticked" value="1" style="width: 18px; height: 18px; cursor: pointer;">
                    Report Form
                </label>
                <label style="display: flex; align-items: center; gap: 0.75rem; color: var(--text-main); font-size: 0.9rem; font-weight: 600; cursor: pointer; padding: 0.25rem 0;">
                    <input type="checkbox" name="medical_certificate_ticked" value="1" style="width: 18px; height: 18px; cursor: pointer;">
                    Medical Certificate
                </label>
                <label style="display: flex; align-items: center; gap: 0.75rem; color: var(--text-main); font-size: 0.9rem; font-weight: 600; cursor: pointer; padding: 0.25rem 0;">
                    <input type="checkbox" name="other_document_ticked" value="1" style="width: 18px; height: 18px; cursor: pointer;">
                    Other Document
                </label>
            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                <button type="button" onclick="closeAddProgrammeModal()" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); cursor: pointer; padding: 0.5rem 1.25rem;">Cancel</button>
                <button type="submit" class="btn-custom" style="background: linear-gradient(135deg, #10b981, #059669); border: none; color: #ffffff; cursor: pointer; padding: 0.5rem 1.25rem; font-weight: 600; border-radius: 6px;">Add Programme</button>
            </div>
        </form>
    </div>
</div>


@endsection

