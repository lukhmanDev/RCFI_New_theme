@php
    $authUser = auth()->user();
    $isCoo = ($authUser && ($authUser->isCoo() || strtolower($authUser->designation ?? '') === 'coo'));
    $isHod = ($authUser && ($authUser->isHod() || strtolower($authUser->designation ?? '') === 'hod'));
    $isSuperAdmin = ($authUser && $authUser->isSuperAdmin());
    $canCreateProject = $isCoo || $isHod || $isSuperAdmin;
@endphp
@extends('layouts.admin')

@section('title', 'Orphan Care Project List')

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
            <h1 style="font-size: 1.3rem; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.02em;">Orphan Care Projects</h1>
            <span id="projectCountBadge" style="font-size: 0.75rem; font-weight: 800; background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 0.2rem 0.65rem; border-radius: 20px;">
                {{ count($projects) }} {{ count($projects) === 1 ? 'Project' : 'Projects' }}
            </span>
        </div>
        <p style="font-size: 0.82rem; color: #64748b; margin: 0.2rem 0 0 0;">Manage, filter, monitor progress, and export orphan care beneficiary projects.</p>
    </div>

    <!-- Action Buttons -->
    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
        @if(Auth::user() && Auth::user()->canDownloadExcel())
        <a id="excelExportBtn" href="{{ route('projects.export', 'orphan-care') }}" class="btn-action-animated excel-export-btn" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; padding: 0.55rem 1.25rem; border-radius: 10px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.45rem; font-size: 0.85rem; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);" title="Download Excel report with all filtered data">
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
    $filterStates = collect();
    $filterDistricts = collect();
    $filterAgencies = collect();
    $filterClusters = collect();
    $filterGenders = collect();

    foreach($projects as $p) {
        $pApp = $p->application;
        $pMeta = $pApp->meta ?? [];

        $st = $pApp?->state ?? ($pMeta['state'] ?? null);
        if ($st && trim($st) !== '') $filterStates->push(trim($st));

        $dt = $pApp?->district ?? ($pMeta['district'] ?? null);
        if ($dt && trim($dt) !== '') $filterDistricts->push(trim($dt));

        $ag = $pApp?->agency_name 
            ?? ($pMeta['agency_name'] ?? null) 
            ?? ($p->donor?->name ?? null) 
            ?? ($p->agency_name ?? null);
        if ((!$ag || is_numeric($ag)) && $p->donor_id) {
            $ag = \App\Models\Donor::find($p->donor_id)?->name ?? $ag;
        }
        if ($ag && is_numeric($ag)) {
            $ag = \App\Models\Donor::find($ag)?->name;
        }
        if ($ag && trim($ag) !== '' && strtolower($ag) !== 'n/a' && !is_numeric($ag)) $filterAgencies->push(trim($ag));

        $cl = $pApp?->cluster?->name ?? ($pMeta['cluster'] ?? null);
        if ($cl && trim($cl) !== '') $filterClusters->push(trim($cl));

        $gn = $pApp?->gender ?? ($pMeta['gender'] ?? null);
        if ($gn && trim($gn) !== '') $filterGenders->push(trim($gn));
    }

    $filterStates = $filterStates->unique()->sort()->values();
    $filterDistricts = $filterDistricts->unique()->sort()->values();
    $filterAgencies = $filterAgencies->unique()->sort()->values();
    $filterClusters = $filterClusters->unique()->sort()->values();
    $filterGenders = $filterGenders->unique()->sort()->values();
@endphp

<!-- FLOATING FILTER & SEARCH TOOLBAR -->
<div class="no-auto-align" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.03);">
    <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end; justify-content: space-between;">
        
        <!-- Filter Dropdowns Grid -->
        <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: flex-end; flex: 1;">
            
            <!-- State -->
            <div style="display: flex; flex-direction: column; gap: 0.3rem; min-width: 130px; flex: 1;">
                <label for="stateFilter" style="font-size: 0.72rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">
                    State
                </label>
                <select id="stateFilter" onchange="filterTable()" class="filter-select-modern">
                    <option value="all">All States</option>
                    @foreach($filterStates as $st)
                        <option value="{{ strtolower($st) }}">{{ $st }}</option>
                    @endforeach
                </select>
            </div>

            <!-- District -->
            <div style="display: flex; flex-direction: column; gap: 0.3rem; min-width: 130px; flex: 1;">
                <label for="districtFilter" style="font-size: 0.72rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">
                    District
                </label>
                <select id="districtFilter" onchange="filterTable()" class="filter-select-modern">
                    <option value="all">All Districts</option>
                    @foreach($filterDistricts as $dt)
                        <option value="{{ strtolower($dt) }}">{{ $dt }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Agency -->
            <div style="display: flex; flex-direction: column; gap: 0.3rem; min-width: 140px; flex: 1;">
                <label for="agencyFilter" style="font-size: 0.72rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">
                    Agency
                </label>
                <select id="agencyFilter" onchange="filterTable()" class="filter-select-modern">
                    <option value="all">All Agencies</option>
                    @foreach($filterAgencies as $ag)
                        <option value="{{ strtolower($ag) }}">{{ $ag }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Cluster -->
            <div style="display: flex; flex-direction: column; gap: 0.3rem; min-width: 130px; flex: 1;">
                <label for="clusterFilter" style="font-size: 0.72rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">
                    Cluster
                </label>
                <select id="clusterFilter" onchange="filterTable()" class="filter-select-modern">
                    <option value="all">All Clusters</option>
                    @foreach($filterClusters as $cl)
                        <option value="{{ strtolower($cl) }}">{{ $cl }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Gender -->
            <div style="display: flex; flex-direction: column; gap: 0.3rem; min-width: 110px; flex: 1;">
                <label for="genderFilter" style="font-size: 0.72rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">
                    Gender
                </label>
                <select id="genderFilter" onchange="filterTable()" class="filter-select-modern">
                    <option value="all">All Genders</option>
                    @foreach($filterGenders as $gn)
                        <option value="{{ strtolower($gn) }}">{{ ucfirst($gn) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status -->
            <div style="display: flex; flex-direction: column; gap: 0.3rem; min-width: 120px; flex: 1;">
                <label for="statusFilter" style="font-size: 0.72rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">
                    Status
                </label>
                <select id="statusFilter" onchange="filterTable()" class="filter-select-modern">
                    <option value="all">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="suspended">Suspended</option>
                    <option value="completed">Completed</option>
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
                    <th style="width: 60px; text-align: center;">S.No</th>
                    <th>RCFI ID</th>
                    <th>Agency ID</th>
                    <th>Agency</th>
                    <th>Name of Orphan</th>
                    <th>Mother Name</th>
                    <th>Mobile</th>
                    <th style="text-align: center; width: 110px;">Status</th>
                    <th style="text-align: center; width: 200px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $index => $project)
                    @php
                        $app = $project->application;
                        $appMeta = $app->meta ?? [];
                        
                        $rowAgencyName = $app?->agency_name 
                            ?? ($appMeta['agency_name'] ?? null) 
                            ?? ($project->donor?->name ?? null) 
                            ?? ($project->agency_name ?? null);
                        if ((!$rowAgencyName || is_numeric($rowAgencyName)) && $project->donor_id) {
                            $rowAgencyName = \App\Models\Donor::find($project->donor_id)?->name ?? $rowAgencyName;
                        }
                        if ($rowAgencyName && is_numeric($rowAgencyName)) {
                            $rowAgencyName = \App\Models\Donor::find($rowAgencyName)?->name ?? 'N/A';
                        }
                        $rowAgencyName = ($rowAgencyName && trim($rowAgencyName) !== '' && strtolower($rowAgencyName) !== 'n/a') ? $rowAgencyName : 'N/A';

                        $searchTerms = [
                            $project->project_id,
                            $project->project_name,
                            $project->sponsor,
                            $project->agency_project_no,
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

                        $rowState = strtolower(trim($app?->state ?? ($appMeta['state'] ?? '')));
                        $rowDistrict = strtolower(trim($app?->district ?? ($appMeta['district'] ?? '')));
                        $rowAgency = strtolower(trim($rowAgencyName !== 'N/A' ? $rowAgencyName : ''));
                        $rowCluster = strtolower(trim($app?->cluster?->name ?? ($appMeta['cluster'] ?? '')));
                        $rowGender = strtolower(trim($app?->gender ?? ($appMeta['gender'] ?? '')));
                        $rowStatus = strtolower(trim($project->status ?? 'active'));
                    @endphp
                    <tr class="project-row" 
                        data-search="{{ $searchString }}"
                        data-state="{{ $rowState }}"
                        data-district="{{ $rowDistrict }}"
                        data-agency="{{ $rowAgency }}"
                        data-cluster="{{ $rowCluster }}"
                        data-gender="{{ $rowGender }}"
                        data-status="{{ $rowStatus }}">
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="font-weight: 600; color: var(--accent-cyan);">
                            <a href="{{ route('projects.show', $project->id) }}?type={{ urlencode($project->type_of_project ?? 'Orphan Care') }}" style="color: var(--accent-cyan); font-weight: 700; text-decoration: underline;" title="View Project Details">
                                {{ $project->project_id }}
                            </a>
                        </td>
                        <td>{{ $project->agency_project_no ?? ($app?->agency_number ?? ($appMeta['agency_number'] ?? 'N/A')) }}</td>
                        <td>{{ $rowAgencyName }}</td>
                        <td>{{ $project->project_name ?? ($app?->applicant_name ?? ($appMeta['applicant_name'] ?? 'N/A')) }}</td>
                        <td>{{ $app?->mother_name ?? ($appMeta['mother_name'] ?? 'N/A') }}</td>
                        <td>{{ $app?->mobile_1 ?? ($appMeta['mobile_1'] ?? ($appMeta['contact_number_1'] ?? ($app?->mobile_2 ?? ($appMeta['mobile_2'] ?? 'N/A')))) }}</td>
                        <td style="text-align: center;">
                            @php
                                $status = $project->status ?? 'Active';
                                $badgeStyle = match($status) {
                                    'Suspended' => 'background:rgba(239,68,68,0.12);color:#ef4444;border:1px solid rgba(239,68,68,0.35);',
                                    'Completed' => 'background:rgba(52,211,153,0.12);color:#34d399;border:1px solid rgba(52,211,153,0.35);',
                                    default     => 'background:rgba(16,185,129,0.12);color:#10b981;border:1px solid rgba(16,185,129,0.35);',
                                };
                                $dotColor = match($status) {
                                    'Suspended' => '#ef4444',
                                    'Completed' => '#34d399',
                                    default     => '#10b981',
                                };
                            @endphp
                            <span id="status-badge-{{ $project->id }}"
                                  data-project-id="{{ $project->id }}"
                                  data-status="{{ $status }}"
                                  style="{{ $badgeStyle }} padding:0.25rem 0.65rem; border-radius:999px; font-size:0.75rem; font-weight:600; white-space:nowrap; display:inline-flex; align-items:center; gap:4px;">
                                <span style="display:inline-block; width:6px; height:6px; border-radius:50%; background-color:{{ $dotColor }};"></span>
                                {{ $status }}
                            </span>
                        </td>
                        <td style="text-align: center; white-space: nowrap;">
                            @if(Auth::user()->hasAdminAccess())
                            <a href="{{ route('projects.show', $project->id) }}?type={{ urlencode($project->type_of_project ?? 'Orphan Care') }}" class="btn-action-icon btn-dots" title="Details">
                                <i class="bx bx-dots-horizontal-rounded"></i>
                            </a>

                            <!-- PDF Report Button -->
                            <a href="{{ route('projects.pdf', [$project->id, 'category' => 'orphan-care']) }}" onclick="downloadDirectPdf(event, this.href)" class="btn-action-icon btn-pdf" title="Download PDF Report">
                                <i class="bx bxs-file-pdf"></i>
                            </a>

                            <button type="button"
                                id="suspend-btn-{{ $project->id }}"
                                class="btn-action-icon"
                                data-project-id="{{ $project->id }}"
                                data-url="{{ route('projects.orphan_care.toggle_suspend', $project->id) }}"
                                data-status="{{ $project->status ?? 'Active' }}"
                                title="{{ ($project->status ?? 'Active') === 'Suspended' ? 'Reactivate Project' : 'Suspend Project' }}"
                                style="background-color: {{ ($project->status ?? 'Active') === 'Suspended' ? 'rgba(16,185,129,0.15)' : 'rgba(245,158,11,0.15)' }}; color: {{ ($project->status ?? 'Active') === 'Suspended' ? '#10b981' : '#f59e0b' }}; border: 1px solid {{ ($project->status ?? 'Active') === 'Suspended' ? 'rgba(16,185,129,0.4)' : 'rgba(245,158,11,0.4)' }};"
                                onclick="toggleSuspend(this)">
                                <i class="bx {{ ($project->status ?? 'Active') === 'Suspended' ? 'bx-lock-open-alt' : 'bx-lock-alt' }}"></i>
                            </button>
                            @endif

                            <button type="button" data-id="{{ $project->id }}" data-name="{{ $app?->applicant_name ?? ($appMeta['applicant_name'] ?? '') }}" data-agency="{{ $project->agency_project_no ?? '' }}" onclick="openAddProgrammeModal(this)" class="btn-action-icon btn-add-prog" title="Add Programme" style="background-color: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); cursor: pointer;">
                                <i class="bx bx-plus-circle"></i>
                            </button>

                            <a href="{{ route('projects.show', $project->id) }}?type={{ urlencode($project->type_of_project ?? 'Orphan Care') }}" class="btn-action-icon btn-view" title="Project Details">
                                <i class="bx bx-show-alt"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 2rem; color: var(--text-muted);">No orphan care projects registered yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>

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
            const selState = (document.getElementById('stateFilter')?.value || 'all').toLowerCase().trim();
            const selDistrict = (document.getElementById('districtFilter')?.value || 'all').toLowerCase().trim();
            const selAgency = (document.getElementById('agencyFilter')?.value || 'all').toLowerCase().trim();
            const selCluster = (document.getElementById('clusterFilter')?.value || 'all').toLowerCase().trim();
            const selGender = (document.getElementById('genderFilter')?.value || 'all').toLowerCase().trim();
            const selStatus = (document.getElementById('statusFilter')?.value || 'all').toLowerCase().trim();

            const table = document.getElementById('projectsTable');
            if (!table) return;

            let visibleCount = 0;
            const rows = table.querySelectorAll('tbody tr.project-row');
            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const rSearch = (row.getAttribute('data-search') || row.textContent || '').toLowerCase();
                const rState = (row.getAttribute('data-state') || '').toLowerCase();
                const rDistrict = (row.getAttribute('data-district') || '').toLowerCase();
                const rAgency = (row.getAttribute('data-agency') || '').toLowerCase();
                const rCluster = (row.getAttribute('data-cluster') || '').toLowerCase();
                const rGender = (row.getAttribute('data-gender') || '').toLowerCase();
                const rStatus = (row.getAttribute('data-status') || '').toLowerCase();

                const matchesSearch = !filter || rSearch.includes(filter);
                const matchesState = selState === 'all' || rState === selState;
                const matchesDistrict = selDistrict === 'all' || rDistrict === selDistrict;
                const matchesAgency = selAgency === 'all' || rAgency === selAgency;
                const matchesCluster = selCluster === 'all' || rCluster === selCluster;
                const matchesGender = selGender === 'all' || rGender === selGender;
                const matchesStatus = selStatus === 'all' || rStatus === selStatus;

                if (matchesSearch && matchesState && matchesDistrict && matchesAgency && matchesCluster && matchesGender && matchesStatus) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            }

            const countBadge = document.getElementById('projectCountBadge');
            if (countBadge) {
                countBadge.textContent = `${visibleCount} ${visibleCount === 1 ? 'Project' : 'Projects'}`;
            }

            if (typeof updateExportUrl === 'function') {
                updateExportUrl();
            }
        }

    function updateExportUrl() {
        const btns = document.querySelectorAll('.btn-export-csv, #excelExportBtn');
        if (!btns.length) return;
        const baseUrl = "{{ route('projects.export', 'orphan-care') }}";
        const params = new URLSearchParams();
        
        const searchVal = document.getElementById('tableSearch')?.value;
        const stateVal = document.getElementById('stateFilter')?.value;
        const districtVal = document.getElementById('districtFilter')?.value;
        const agencyVal = document.getElementById('agencyFilter')?.value;
        const clusterVal = document.getElementById('clusterFilter')?.value;
        const genderVal = document.getElementById('genderFilter')?.value;
        const statusVal = document.getElementById('statusFilter')?.value;

        if (searchVal) params.append('search', searchVal);
        if (stateVal && stateVal !== 'all') params.append('state', stateVal);
        if (districtVal && districtVal !== 'all') params.append('district', districtVal);
        if (agencyVal && agencyVal !== 'all') params.append('agency', agencyVal);
        if (clusterVal && clusterVal !== 'all') params.append('cluster', clusterVal);
        if (genderVal && genderVal !== 'all') params.append('gender', genderVal);
        if (statusVal && statusVal !== 'all') params.append('status', statusVal);

        const queryString = params.toString();
        const fullUrl = queryString ? `${baseUrl}?${queryString}` : baseUrl;
        btns.forEach(btn => {
            if (btn.classList.contains('excel-export-btn') || btn.id === 'excelExportBtn') {
                btn.href = fullUrl;
            }
        });
    }
    window.updateExportUrl = updateExportUrl;

    function resetFilters() {
        if (document.getElementById('tableSearch')) document.getElementById('tableSearch').value = '';
        if (document.getElementById('stateFilter')) document.getElementById('stateFilter').value = 'all';
        if (document.getElementById('districtFilter')) document.getElementById('districtFilter').value = 'all';
        if (document.getElementById('agencyFilter')) document.getElementById('agencyFilter').value = 'all';
        if (document.getElementById('clusterFilter')) document.getElementById('clusterFilter').value = 'all';
        if (document.getElementById('genderFilter')) document.getElementById('genderFilter').value = 'all';
        if (document.getElementById('statusFilter')) document.getElementById('statusFilter').value = 'all';
        filterTable();
    }
    window.resetFilters = resetFilters;

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
                    form.action = `/admin/projects/orphan-care/${projectId}/add-programme`;
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
                    <select name="programme_name" id="orphan_add_prog_name_select" required class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" onchange="if(typeof toggleSpecifyProgrammeField === 'function') toggleSpecifyProgrammeField(this, 'orphan_add_prog_other_name_wrapper', 'orphan_add_prog_other_name_input')">
                        <option value="" disabled selected>-- Select Programme --</option>
                        <option value="Cluster Camp">Cluster Camp</option>
                        <option value="Report Collection Programme">Report Collection Programme</option>
                        <option value="Others">Others</option>
                    </select>
                </div>
                <div id="orphan_add_prog_other_name_wrapper" style="grid-column: span 2; display: none;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem; font-weight: 600;">Specify Programme Name *</label>
                    <input type="text" id="orphan_add_prog_other_name_input" name="other_programme_name" placeholder="Enter custom programme name..." class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;">
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

<script>
function openStageDetailsModal(id, type, projectIdCode) {
    window.location.href = `/admin/projects/${id}?type=${type}`;
}
window.openStageDetailsModal = openStageDetailsModal;

function closeProgrammeModal() {
    const modal = document.getElementById('addProgrammeModal');
    if (modal) modal.style.display = 'none';
}
window.closeProgrammeModal = closeProgrammeModal;
</script>

@endsection

