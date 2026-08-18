@php
    $authUser = auth()->user();
    $isCoo = ($authUser && ($authUser->isCoo() || strtolower($authUser->designation ?? '') === 'coo'));
    $isHod = ($authUser && ($authUser->isHod() || strtolower($authUser->designation ?? '') === 'hod'));
    $isSuperAdmin = ($authUser && $authUser->isSuperAdmin());
    $canCreateProject = $isCoo || $isHod || $isSuperAdmin;
@endphp
@extends('layouts.admin')

@section('title', 'Education Center Project List')

@section('content')

<link rel="stylesheet" href="{{ asset('css/projects_common.css') }}">
<script src="{{ asset('js/projects_common.js') }}"></script>
<div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
    <a href="{{ route('projects.index') }}" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); padding: 0.5rem 1rem;">
        <i class="bx bx-left-arrow-alt"></i> Back to Dashboard
    </a>
</div>

<style>
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

    /* Executive Toolbar Styling */
    .filter-select-modern {
        height: 38px;
        padding: 0 0.75rem;
        font-size: 0.84rem;
        font-weight: 600;
        border-radius: 8px;
        background-color: #f8fafc;
        color: #1e293b;
        border: 1px solid #cbd5e1;
        outline: none;
        cursor: pointer;
        transition: all 0.2s ease;
        width: 100%;
    }
    .filter-select-modern:focus, .filter-select-modern:hover {
        border-color: #10b981;
        background-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
    }
    .btn-action-animated {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-action-animated:hover {
        transform: translateY(-1px);
    }
    .btn-action-animated:active {
        transform: translateY(0);
    }
</style>

<!-- EXECUTIVE PAGE HEADER -->
<div class="no-auto-align" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; background: #ffffff; padding: 1.25rem 1.5rem; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.03);">
    <div>
        <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
            <h1 style="font-size: 1.3rem; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.02em;">Education Center Projects</h1>
            <span id="projectCountBadge" style="font-size: 0.75rem; font-weight: 800; background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 0.2rem 0.65rem; border-radius: 20px;">
                {{ count($projects) }} {{ count($projects) === 1 ? 'Project' : 'Projects' }}
            </span>
        </div>
        <p style="font-size: 0.82rem; color: #64748b; margin: 0.2rem 0 0 0;">Manage, filter, monitor progress, and export education center projects.</p>
    </div>

    <!-- Action Buttons -->
    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
        @if(Auth::user() && Auth::user()->canAddEditProjects())
        <button type="button" onclick="openModal()" class="btn-action-animated" style="background: #ffffff; border: 1px solid #cbd5e1; color: #334155; padding: 0.55rem 1.1rem; border-radius: 10px; font-weight: 700; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            Add Project
        </button>
        @endif

        @if(Auth::user() && Auth::user()->canDownloadExcel())
        <a id="excelExportBtn" href="{{ route('projects.export', 'education-center') }}" class="btn-action-animated excel-export-btn" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; padding: 0.55rem 1.25rem; border-radius: 10px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.45rem; font-size: 0.85rem; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);" title="Download Excel report with all filtered data">
            Export Excel
        </a>
        @endif
    </div>
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

@php
    $uniqueManagers = collect();
    $uniqueAgencies = collect();
    $uniqueDistricts = collect();
    $uniqueStates = collect();

    foreach($projects as $p) {
        $m = $p->projectManager ? $p->projectManager->name : ($p->manager ? $p->manager->name : null);
        if ($m && strtolower($m) !== 'n/a') $uniqueManagers->push(trim($m));

        $a = $p->donor ? $p->donor->name : ($p->agency_name ?? ($p->application ? ($p->application->agency_name ?? null) : null));
        if ((!$a || is_numeric($a)) && $p->donor_id) {
            $a = \App\Models\Donor::find($p->donor_id)?->name ?? $a;
        }
        if ($a && is_numeric($a)) {
            $a = \App\Models\Donor::find($a)?->name;
        }
        if ($a && strtolower($a) !== 'n/a' && !is_numeric($a)) $uniqueAgencies->push(trim($a));

        $d = $p->district ?? ($p->application ? ($p->application->district ?? ($p->application->meta['district'] ?? ($p->application->meta['locality_district'] ?? null))) : null);
        if ($d && strtolower($d) !== 'n/a') $uniqueDistricts->push(trim($d));

        $s = $p->state ?? ($p->application ? ($p->application->state ?? ($p->application->meta['state'] ?? ($p->application->meta['locality_state'] ?? null))) : null);
        if ($s && strtolower($s) !== 'n/a') $uniqueStates->push(trim($s));
    }

    $uniqueManagers = $uniqueManagers->unique()->sort()->values();
    $uniqueAgencies = $uniqueAgencies->unique()->sort()->values();
    $uniqueDistricts = $uniqueDistricts->unique()->sort()->values();
    $uniqueStates = $uniqueStates->unique()->sort()->values();
@endphp

<!-- FLOATING FILTER & SEARCH TOOLBAR -->
<div class="no-auto-align" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.03);">
    <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end; justify-content: space-between;">
        
        <!-- Filter Dropdowns Grid -->
        <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: flex-end; flex: 1;">
            
            <!-- State -->
            <div style="display: flex; flex-direction: column; gap: 0.3rem; min-width: 120px; flex: 1;">
                <label for="filterState" style="font-size: 0.72rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">
                    State
                </label>
                <select id="filterState" onchange="filterTable()" class="filter-select-modern">
                    <option value="all">All States</option>
                    @foreach($uniqueStates as $us)
                        <option value="{{ strtolower($us) }}">{{ $us }}</option>
                    @endforeach
                </select>
            </div>

            <!-- District -->
            <div style="display: flex; flex-direction: column; gap: 0.3rem; min-width: 130px; flex: 1;">
                <label for="filterDistrict" style="font-size: 0.72rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">
                    District
                </label>
                <select id="filterDistrict" onchange="filterTable()" class="filter-select-modern">
                    <option value="all">All Districts</option>
                    @foreach($uniqueDistricts as $ud)
                        <option value="{{ strtolower($ud) }}">{{ $ud }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Agency -->
            <div style="display: flex; flex-direction: column; gap: 0.3rem; min-width: 140px; flex: 1;">
                <label for="filterAgency" style="font-size: 0.72rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">
                    Agency
                </label>
                <select id="filterAgency" onchange="filterTable()" class="filter-select-modern">
                    <option value="all">All Agencies</option>
                    @foreach($uniqueAgencies as $ua)
                        <option value="{{ strtolower($ua) }}">{{ $ua }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Project Manager -->
            <div style="display: flex; flex-direction: column; gap: 0.3rem; min-width: 140px; flex: 1;">
                <label for="filterManager" style="font-size: 0.72rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">
                    Project Manager
                </label>
                <select id="filterManager" onchange="filterTable()" class="filter-select-modern">
                    <option value="all">All Project Managers</option>
                    @foreach($uniqueManagers as $um)
                        <option value="{{ strtolower($um) }}">{{ $um }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Reset Button -->
            <div style="display: flex; flex-direction: column; justify-content: flex-end;">
                <button type="button" onclick="resetFilters()" class="btn-action-animated" style="height: 38px; background: #f8fafc; border: 1px solid #cbd5e1; color: #475569; padding: 0 0.85rem; border-radius: 8px; font-size: 0.82rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem; cursor: pointer;" title="Reset all filters">
                    Reset
                </button>
            </div>
        </div>

        <!-- Live Search Bar -->
        <div style="display: flex; align-items: flex-end; min-width: 250px;">
            <div style="position: relative; width: 100%;">
                <input type="text" id="tableSearch" onkeyup="filterTable()" placeholder="Search projects..." style="width: 100%; height: 38px; padding: 0 1rem; font-size: 0.85rem; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; color: #1e293b; outline: none; transition: all 0.2s;" onfocus="this.style.borderColor='#10b981'; this.style.backgroundColor='#ffffff'; this.style.boxShadow='0 0 0 3px rgba(16, 185, 129, 0.1)';" onblur="this.style.borderColor='#cbd5e1'; this.style.backgroundColor='#f8fafc'; this.style.boxShadow='none';">
            </div>
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
                        $mgrName = $project->projectManager ? $project->projectManager->name : ($project->manager ? $project->manager->name : 'N/A');
                        $agencyName = $project->donor ? $project->donor->name : ($project->agency_name ?? ($project->application ? ($project->application->agency_name ?? 'N/A') : 'N/A'));
                        if (($agencyName === 'N/A' || is_numeric($agencyName)) && $project->donor_id) {
                            $agencyName = \App\Models\Donor::find($project->donor_id)?->name ?? 'N/A';
                        }
                        if (is_numeric($agencyName)) {
                            $agencyName = \App\Models\Donor::find($agencyName)?->name ?? 'N/A';
                        }
                        $districtName = $project->district ?? ($project->application ? ($project->application->district ?? ($project->application->meta['district'] ?? ($project->application->meta['locality_district'] ?? 'N/A'))) : 'N/A');
                        $stateName = $project->state ?? ($project->application ? ($project->application->state ?? ($project->application->meta['state'] ?? ($project->application->meta['locality_state'] ?? 'N/A'))) : 'N/A');
                    @endphp
                    <tr class="project-row" 
                        data-manager="{{ strtolower($mgrName) }}" 
                        data-manager-display="{{ $mgrName }}" 
                        data-agency="{{ strtolower($agencyName) }}" 
                        data-agency-display="{{ $agencyName }}" 
                        data-district="{{ strtolower($districtName) }}" 
                        data-district-display="{{ $districtName }}" 
                        data-state="{{ strtolower($stateName) }}" 
                        data-state-display="{{ $stateName }}">
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="font-weight: 600; color: var(--accent-cyan);">
                            <a href="{{ route('projects.show', $project->id) }}?type={{ urlencode($project->type_of_project ?? 'Education Center') }}" style="color: var(--accent-cyan); font-weight: 700; text-decoration: underline;" title="View Project Details">
                                {{ $project->project_id }}
                            </a>
                        </td>
                        <td class="col-agency">{{ $project->agency_project_no ?? 'N/A' }}</td>
                        <td class="col-agency-name">{{ $agencyName }}</td>
                        <td class="col-activity">{{ $project->activity ?? $project->project_name ?? $project->type_of_project ?? 'N/A' }}</td>
                        <td class="col-place">{{ $project->place ?? $project->location ?? ($project->application ? ($project->application->place ?? $project->application->location ?? 'N/A') : 'N/A') }}</td>
                        <td class="col-district">{{ $project->district ?? ($project->application ? ($project->application->district ?? ($project->application->meta['district'] ?? ($project->application->meta['locality_district'] ?? 'N/A'))) : 'N/A') }}</td>
                        <td class="col-manager">{{ $project->projectManager ? $project->projectManager->name : 'N/A' }}</td>
                        <td class="col-allocated" style="text-align: right;">{!! "&#x20B9;" !!}{{ number_format($project->total_allocated ?? 0, 2) }}</td>
                        <td class="col-balance" style="text-align: right;">{!! "&#x20B9;" !!}{{ number_format(($project->total_allocated ?? 0) - ($project->total_spent ?? 0), 2) }}</td>
                        <td style="text-align: center; white-space: nowrap;">
                            @if(Auth::user()->hasAdminAccess())
                            <button onclick="alert('Project Details:\nID: {{ $project->project_id }}\nAgency No: {{ $project->agency_project_no }}\nAgency: {{ $project->donor ? $project->donor->name : 'N/A' }}\nManager: {{ $project->projectManager ? $project->projectManager->name : 'N/A' }}\nEngineer: {{ $project->engineer ? $project->engineer->name : 'N/A' }}\nUnit: {{ $project->unit ?? 'RCFI' }}\nBudget: ₹{{ number_format($project->available_budget, 2) }}\nRemarks: {{ $project->remarks }}')" class="btn-action-icon btn-dots" title="Details">
                                <i class="bx bx-dots-horizontal-rounded"></i>
                            </button>

                            @if(Auth::user() && Auth::user()->canAddEditProjects())
                            <button type="button" onclick="openEditModal({{ $project->id }})" class="btn-action-icon btn-edit" title="Edit">
                                <i class="bx bx-pencil"></i>
                            </button>
                            @endif

                            <!-- PDF Report Button -->
                            <a href="{{ route('projects.pdf', [$project->id, 'category' => 'education-center']) }}" onclick="downloadDirectPdf(event, this.href)" class="btn-action-icon btn-pdf" title="Download PDF Report">
                                <i class="bx bxs-file-pdf"></i>
                            </a>
                            @endif

                            <a href="{{ route('projects.show', $project->id) }}?type={{ urlencode($project->type_of_project) }}" class="btn-action-icon btn-view" title="Stage Details">
                                <i class="bx bx-show-alt"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" style="text-align: center; padding: 2rem; color: var(--text-muted);">No education center projects registered yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="addProjectModal">
    <div class="modal-content-custom">
        <div class="modal-header-custom">
            <h3>Add Education Center Project</h3>
            <button type="button" class="modal-close-btn" onclick="closeModal()" title="Close">&times;</button>
        </div>
        <form action="{{ route('projects.store') }}" method="POST">
            @csrf
            <input type="hidden" name="redirect_category" value="education-center">

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
                    <select name="project_manager_id" id="project_manager_id">
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
                    <label for="total_beneficiary_peoples">Total Benefited People</label>
                    <input type="number" min="0" name="total_beneficiary_peoples" id="total_beneficiary_peoples" placeholder="Enter total benefited people count">
                </div>

                <div class="form-group-custom">
                    <label for="total_family">Total Benefited Families</label>
                    <input type="number" min="0" name="total_family" id="total_family" placeholder="Enter total benefited families count">
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
                    <input type="text" value="Education Center" disabled style="background-color: var(--bg-color); border: 1px solid var(--panel-border); color: var(--text-muted);">
                    <input type="hidden" name="type_of_project" value="Education Center">
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
            <h3>Edit Education Center Project</h3>
            <button type="button" class="modal-close-btn" onclick="closeEditModal()" title="Close">&times;</button>
        </div>
        <form id="editProjectForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="redirect_category" value="education-center">

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
                    <select name="project_manager_id" id="edit_project_manager_id">
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
                    <label for="edit_application_info">Connected Application</label>
                    <input type="text" id="edit_application_info" readonly style="background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #38bdf8; font-weight: 600;" value="No application connected">
                </div>

                <div class="form-group-custom">
                    <label for="edit_total_beneficiary_peoples">Total Benefited People</label>
                    <input type="number" min="0" name="total_beneficiary_peoples" id="edit_total_beneficiary_peoples" placeholder="Enter total benefited people count">
                </div>

                <div class="form-group-custom">
                    <label for="edit_total_family">Total Benefited Families</label>
                    <input type="number" min="0" name="total_family" id="edit_total_family" placeholder="Enter total benefited families count">
                </div>

                <div class="form-group-custom">
                    <label for="edit_theme">Theme</label>
                    <select name="theme" id="edit_theme" onchange="populateSubthemes('edit_theme', 'edit_subtheme')">
                        <option value="">Select Theme</option>
                        @foreach($themes as $t)
                            <option value="{{ $t->name }}" data-theme-id="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-custom">
                    <label for="edit_subtheme">Subtheme</label>
                    <select name="subtheme" id="edit_subtheme">
                        <option value="">Select Subtheme</option>
                    </select>
                </div>

                <div class="form-group-custom">
                    <label for="edit_activity">Activity</label>
                    <input type="text" name="activity" id="edit_activity" placeholder="Enter activity">
                </div>

                <div class="form-group-custom">
                    <label for="edit_type_of_project">Type of Project</label>
                    <input type="text" value="Education Center" disabled style="background-color: var(--bg-color); border: 1px solid var(--panel-border); color: var(--text-muted);">
                    <input type="hidden" name="type_of_project" value="Education Center">
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
        const modal = document.getElementById('addProjectModal');
        if (modal) modal.style.display = "flex";
    }

    function closeModal() {
        const modal = document.getElementById('addProjectModal');
        if (modal) modal.style.display = 'none';
    }

    window.allProjectsDataList = @json(method_exists($projects, 'items') ? $projects->items() : (is_array($projects) ? $projects : $projects->values()));

    function openEditModal(projectOrId) {
        let project = projectOrId;
        if (typeof projectOrId === 'number' || typeof projectOrId === 'string') {
            project = (window.allProjectsDataList || []).find(p => p.id == projectOrId);
        }
        if (!project) return;

        const form = document.getElementById('editProjectForm');
        form.setAttribute('action', `/admin/projects/${project.id}`);

        document.getElementById('edit_project_name').value = project.project_name || '';
        document.getElementById('edit_sponsor').value = project.sponsor || '';
        document.getElementById('edit_project_spec').value = project.project_spec || '';
        document.getElementById('edit_agency_project_no').value = project.agency_project_no || '';
        document.getElementById('edit_donor_id').value = project.donor_id || '';
        document.getElementById('edit_project_manager_id').value = project.project_manager_id || '';
        document.getElementById('edit_engineer_id').value = project.engineer_id || '';
        document.getElementById('edit_unit').value = project.unit || 'RCFI';
        document.getElementById('edit_available_budget').value = project.available_budget || '';

        if (document.getElementById('edit_application_info')) {
            if (project.application) {
                const appObj = project.application;
                document.getElementById('edit_application_info').value = (appObj.applicant_name ? appObj.applicant_name : 'Application') + ' (ID: ' + appObj.id + ')';
            } else if (project.application_id) {
                document.getElementById('edit_application_info').value = 'Application #' + project.application_id;
            } else {
                document.getElementById('edit_application_info').value = 'No application connected';
            }
        }

        if (document.getElementById('edit_total_beneficiary_peoples')) {
            document.getElementById('edit_total_beneficiary_peoples').value = project.total_beneficiary_peoples || project.num_benefited_people || '';
        }
        if (document.getElementById('edit_total_family')) {
            document.getElementById('edit_total_family').value = project.total_family || '';
        }
        document.getElementById('edit_remarks').value = project.remarks || '';
        
        const themeEl = document.getElementById('edit_theme');
        if (themeEl) {
            themeEl.value = project.theme || '';
            if (!themeEl.value && project.theme) {
                const tTrim = (project.theme || '').trim().toLowerCase();
                for (let i = 0; i < themeEl.options.length; i++) {
                    if (themeEl.options[i].value.trim().toLowerCase() === tTrim) {
                        themeEl.selectedIndex = i;
                        break;
                    }
                }
            }
        }
        populateSubthemes('edit_theme', 'edit_subtheme', project.subtheme || '');
        document.getElementById('edit_activity').value = project.activity || '';

        document.getElementById('editProjectModal').style.display = 'flex';
    }

    function closeEditModal() {
        const modal = document.getElementById('editProjectModal');
        if (modal) modal.style.display = 'none';
    }

    function filterTable() {
        const input = document.getElementById('tableSearch');
        const filter = input ? input.value.toLowerCase().trim() : '';
        const selManager = (document.getElementById('filterManager')?.value || 'all').toLowerCase().trim();
        const selAgency = (document.getElementById('filterAgency')?.value || 'all').toLowerCase().trim();
        const selDistrict = (document.getElementById('filterDistrict')?.value || 'all').toLowerCase().trim();
        const selState = (document.getElementById('filterState')?.value || 'all').toLowerCase().trim();

        const table = document.getElementById('projectsTable');
        if (!table) return;

        let visibleCount = 0;
        const rows = table.querySelectorAll('tbody tr.project-row');
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const rManager = (row.getAttribute('data-manager') || '').toLowerCase();
            const rAgency = (row.getAttribute('data-agency') || '').toLowerCase();
            const rDistrict = (row.getAttribute('data-district') || '').toLowerCase();
            const rState = (row.getAttribute('data-state') || '').toLowerCase();
            const text = row.textContent.toLowerCase();

            const matchesSearch = !filter || text.includes(filter);
            const matchesManager = (selManager === 'all') || (rManager === selManager);
            const matchesAgency = (selAgency === 'all') || (rAgency === selAgency);
            const matchesDistrict = (selDistrict === 'all') || (rDistrict === selDistrict);
            const matchesState = (selState === 'all') || (rState === selState);

            if (matchesSearch && matchesManager && matchesAgency && matchesDistrict && matchesState) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        }

        const noDataRow = document.getElementById('noResultsRow');
        if (noDataRow) {
            noDataRow.style.display = (visibleCount === 0) ? '' : 'none';
        }

        const countBadge = document.getElementById('projectCountBadge');
        if (countBadge) {
            countBadge.textContent = `${visibleCount} ${visibleCount === 1 ? 'Project' : 'Projects'}`;
        }

        updateExportUrl();
    }

    function updateExportUrl() {
        const btns = document.querySelectorAll('.excel-export-btn, #excelExportBtn');
        if (!btns.length) return;
        const baseUrl = "{{ route('projects.export', 'education-center') }}";
        const params = new URLSearchParams();

        const searchVal = document.getElementById('tableSearch')?.value;
        const managerVal = document.getElementById('filterManager')?.value;
        const agencyVal = document.getElementById('filterAgency')?.value;
        const districtVal = document.getElementById('filterDistrict')?.value;
        const stateVal = document.getElementById('filterState')?.value;

        if (searchVal) params.append('search', searchVal);
        if (managerVal && managerVal !== 'all') params.append('pm_id', managerVal);
        if (agencyVal && agencyVal !== 'all') params.append('agency', agencyVal);
        if (districtVal && districtVal !== 'all') params.append('district', districtVal);
        if (stateVal && stateVal !== 'all') params.append('state', stateVal);

        const queryString = params.toString();
        const fullUrl = queryString ? `${baseUrl}?${queryString}` : baseUrl;
        btns.forEach(btn => {
            if (btn.tagName === 'A') btn.href = fullUrl;
        });
    }

    function resetFilters() {
        const searchInput = document.getElementById('tableSearch');
        if (searchInput) searchInput.value = '';
        const managerSelect = document.getElementById('filterManager');
        if (managerSelect) managerSelect.value = 'all';
        const agencySelect = document.getElementById('filterAgency');
        if (agencySelect) agencySelect.value = 'all';
        const districtSelect = document.getElementById('filterDistrict');
        if (districtSelect) districtSelect.value = 'all';
        const stateSelect = document.getElementById('filterState');
        if (stateSelect) stateSelect.value = 'all';

        filterTable();
    }

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

    // Global Window Bindings
    window.openModal = openModal;
    window.closeModal = closeModal;
    window.openEditModal = openEditModal;
    window.closeEditModal = closeEditModal;
    window.filterTable = filterTable;
</script>

@endsection
