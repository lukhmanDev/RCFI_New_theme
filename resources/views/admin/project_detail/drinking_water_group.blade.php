@extends('layouts.admin')

@section('title', 'Project Details')

@section('content')

    <style>
        /* Stages Tabs Styling */
        .stages-tabs {
            display: flex;
            border-bottom: 2px solid var(--panel-border);
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .stage-tab {
            padding: 0.75rem 1.5rem;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-muted);
            border-bottom: 3px solid transparent;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            transition: all 0.2s;
            text-decoration: none;
        }

        .stage-tab.active {
            color: var(--accent-green) !important;
            border-bottom-color: var(--accent-green) !important;
            background-color: rgba(16, 185, 129, 0.08);
            border-top-left-radius: 6px;
            border-top-right-radius: 6px;
        }

        .stage-tab.completed {
            color: var(--accent-cyan);
        }

        .stage-tab.locked {
            color: var(--text-muted) !important;
            opacity: 0.5;
            cursor: not-allowed !important;
            border-bottom-color: transparent !important;
            background-color: transparent !important;
        }
        .stage-tab.locked:hover {
            color: var(--text-muted) !important;
            background-color: transparent !important;
        }

        /* Project Detail Panel Header */
        .detail-header-panel {
            background-color: #2c3e50;
            border: 1px solid var(--panel-border);
            color: #ffffff;
            padding: 1.5rem;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            margin-bottom: 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .detail-header-panel h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 0.05em;
        }

        /* Warning Box */
        .warning-box {
            background-color: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.25);
            color: #b3b5f7;
            padding: 1.25rem;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
        }

        /* Success banner within stages */
        .stage-success-banner {
            background-color: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #8cf5c6;
            padding: 0.85rem 1.25rem;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        /* Details list layout */
        .details-grid {
            display: grid;
            grid-template-columns: 200px 20px 1fr;
            row-gap: 1.25rem;
            align-items: center;
            font-size: 1rem;
            color: var(--text-main);
            padding: 1.5rem 0;
        }

        .details-label {
            font-weight: 600;
            color: var(--text-muted);
        }

        .details-colon {
            color: var(--text-muted);
            text-align: center;
        }

        .details-value {
            font-weight: 600;
            color: #ffffff;
        }

        /* Stage Content container show/hide style */
        .stage-content-panel {
            display: none;
            animation: fadeIn 0.35s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Table custom updates inside stages */
        .stage-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            color: var(--text-main);
        }

        .stage-table th {
            text-align: left;
            padding: 0.85rem 1rem;
            background-color: rgba(255,255,255,0.02);
            border-bottom: 2px solid var(--panel-border);
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .stage-table td {
            padding: 0.95rem 1rem;
            border-bottom: 1px solid var(--panel-border);
            font-size: 0.9rem;
        }

        .stage-table tr:hover td {
            background-color: rgba(255,255,255,0.01);
        }
    </style>

    <script>
        function switchStage(stageNum) {
            const activeProjectStage = {{ $project->stage ?? 1 }};
            const isProjectApproved = "{{ ($project->status === 'Approved' || $project->status === 'Completed') ? '1' : '0' }}";
            const hasApplication = "{{ empty($project->application_id) ? '0' : '1' }}";
            const projectType = "{{ $project->type_of_project ?? '' }}";

            let isLocked = false;
            const isSixStage = ['Education Center', 'Cultural Center', 'Hospital or Clinics', 'Shops and Others', 'House', 'Drinking Water - Group Level', 'Drinking Water - Individual Level', 'General'].includes(projectType);
            if (['Orphan Care', 'Differently Abled', 'Family Aid'].includes(projectType)) {
                isLocked = false;
            } else if (isSixStage) {
                if (stageNum <= 3) {
                    isLocked = false;
                } else {
                    isLocked = (activeProjectStage < 4 && isProjectApproved !== '1');
                }
            } else {
                if (stageNum !== 1 && isProjectApproved !== '1') {
                    isLocked = true;
                }
            }

            if (isLocked) {
                const msg = "Access Locked: This stage is not yet unlocked.";
                if (typeof showToast === 'function') {
                    showToast(msg, "danger");
                } else {
                    alert(msg);
                }
                return;
            }

            try {
                sessionStorage.setItem('current_project_stage_{{ $project->id ?? 0 }}', stageNum);
            } catch(e) {}

            const tabs = document.querySelectorAll('.stage-tab');
            tabs.forEach(tab => tab.classList.remove('active'));

            const clickedTab = document.getElementById('tab-' + stageNum);
            if (clickedTab) {
                clickedTab.classList.add('active');
            }

            const panels = document.querySelectorAll('.stage-content-panel');
            panels.forEach(panel => panel.style.display = 'none');

            const targetPanel = document.getElementById('stage-content-' + stageNum);
            if (targetPanel) {
                targetPanel.style.display = 'block';
            }
        }
        window.switchStage = switchStage;
        function restoreActiveStage() {
            try {
                const savedStage = sessionStorage.getItem('current_project_stage_{{ $project->id ?? 0 }}');
                if (savedStage) {
                    switchStage(parseInt(savedStage, 10));
                }
            } catch(e) {}
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', restoreActiveStage);
        } else {
            restoreActiveStage();
        }

    </script>

    <!-- Stage Navigation Tabs (Interactive Navigation) -->
    <div class="stages-tabs">
        @for($i = 1; $i <= 6; $i++)
            @php
                $isActive = $project->stage == $i;
                $isCompleted = $project->stage > $i;
                $class = $isActive ? 'active' : ($isCompleted ? 'completed' : '');
                
                //   Stage 1 & Stage 2: always accessible
                //   Stage 3 & Stage 4: unlocks when an application is assigned in Stage 2
                //   Stage 5 & Stage 6: unlocks when Stage 4 is approved
                if (in_array($project->type_of_project, ['Education Center', 'Cultural Center', 'Hospital or Clinics', 'Shops and Others', 'House', 'Drinking Water - Group Level', 'Drinking Water - Individual Level'])) {
                    if ($i <= 2) {
                        $isLocked = false;
                    } elseif ($i == 3 || $i == 4) {
                        $isLocked = empty($project->application_id);
                    } else { // stage 5 or 6
                        $isLocked = empty($project->application_id) || ($project->stage < 5 && $project->status !== 'Approved' && $project->status !== 'Completed');
                    }
                } else {
                    $isLocked = ($project->status !== 'Approved' && $project->status !== 'Completed' && $i > 1);
                }
                if ($isLocked) {
                    $class .= ' locked';
                }
            @endphp
            <div class="stage-tab {{ $class }}" id="tab-{{ $i }}" onclick="switchStage({{ $i }})">
                @if($isLocked)
                    <i class="bx bx-lock-alt" style="margin-right: 0.25rem;"></i>
                @endif
                Stage {{ $i }}
            </div>
        @endfor
    </div>

    @php
        $authUser = auth()->user();
        $isSuperAdmin = ($authUser && ($authUser->isSuperAdmin() || $authUser->role == 1 || $authUser->role === 'super_admin'));
        $designationLower = strtolower($authUser->designation ?? '');
        $isCoo = ($authUser && ($authUser->isCoo() || $designationLower === 'coo' || str_contains($designationLower, 'chief operating officer') || str_contains($designationLower, 'coo')));
        $isHod = ($authUser && ($authUser->isHod() || $designationLower === 'hod' || str_contains($designationLower, 'head of department') || str_contains($designationLower, 'hod')));
        $isPmOnly = ($authUser && ($authUser->isPm() || str_contains($designationLower, 'project manager') || $designationLower === 'project manager'));
        $isEngineerOnly = ($authUser && ($authUser->isEngineer() || strtolower($authUser->designation ?? '') === 'engineer'));
        
        $isProjectManager = ($authUser && ($isSuperAdmin || $isCoo || $isHod || $isPmOnly || $isEngineerOnly || in_array($authUser->role, [1, 2, 3, 4, 6, 'super_admin', 'coo', 'project_manager', 'hod', 'engineer']) || in_array(strtolower($authUser->designation ?? ''), ['project manager', 'engineer', 'coo', 'hod', 'super admin', 'admin'])));
        $isLockedForEditing = ($project->status === 'Completed' && !$isSuperAdmin);
        $canEditStatus = ($isCoo || $isHod || $isSuperAdmin) && !$isLockedForEditing;
        $isSixStage = in_array($project->type_of_project, ['Education Center', 'Cultural Center', 'Hospital or Clinics', 'Shops and Others', 'House', 'Drinking Water - Group Level', 'Drinking Water - Individual Level']);
        $isStage4Approved = false;
        if ($isSixStage) {
            $isStage4Approved = ($project->stage >= 5 || in_array($project->status, ['Approved', 'Completed']));
        }
        
        if ($isSixStage) {
            $canAssignApplication = ($authUser && $authUser->canAssignApplications()) && !$isStage4Approved;
        } else {
            $canAssignApplication = ($authUser && $authUser->canAssignApplications()) && !$isLockedForEditing;
        }
        $hasApplication = !empty($project->application_id);
    @endphp

    <!-- Success Panel -->
    @if (session('success'))
        <div class=\"alert alert-success\" style="background-color: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent-green); color: #8cf5c6; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500;">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class=\"alert alert-danger\" style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid var(--accent-red); color: #ff8a8a; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Project Detail Panel -->
    <div class="panel" style="width: 100%; padding: 0; overflow: hidden; border-radius: 8px;">


       

        <!-- ================= STAGE 1 PANEL ================= -->
        <div class="stage-content-panel" id="stage-content-1">
            <div class="detail-header-panel">
                <h2>PROJECT DETAIL</h2>
            </div>
            <div style="padding: 1.5rem;">
                {{-- Stage 1: No approval required for construction or non-construction projects --}}
                @if(!in_array($project->type_of_project, ['Education Center', 'Cultural Center', 'Hospital or Clinics', 'Shops and Others', 'House', 'Drinking Water - Group Level', 'Drinking Water - Individual Level']))
                    @if($project->status === 'Approved')
                        <div style="margin-bottom: 1.5rem; background-color: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #8cf5c6; padding: 0.85rem 1.25rem; border-radius: 6px; font-size: 0.9rem; font-weight: 600; display: inline-block;">
                            <i class="bx bx-check-circle"></i> Project Approved & Active
                        </div>
                    @endif
                @endif

                <div class="details-grid">
                    <div class="details-label">RCFI ID</div><div class="details-colon">:</div><div class="details-value" style="color: var(--accent-cyan);">{{ $project->project_id }}</div>
                    <div class="details-label">Project Name</div><div class="details-colon">:</div><div class="details-value">{{ $project->project_name ?? 'N/A' }}</div>
                    <div class="details-label">Sponsor</div><div class="details-colon">:</div><div class="details-value">{{ $project->sponsor ?? 'N/A' }}</div>
                    <div class="details-label">Project Spec</div><div class="details-colon">:</div><div class="details-value" style="white-space: pre-wrap;">{{ $project->project_spec ?? 'N/A' }}</div>
                    <div class="details-label">Agency Project No</div><div class="details-colon">:</div><div class="details-value">{{ $project->agency_project_no ?? 'N/A' }}</div>
                    <div class="details-label">Agency Name</div><div class="details-colon">:</div><div class="details-value">{{ $project->donor ? $project->donor->name : 'N/A' }}</div>
                    <div class="details-label">Project Manager</div><div class="details-colon">:</div><div class="details-value">{{ $project->projectManager ? $project->projectManager->name : 'N/A' }}</div>
                    <div class="details-label">Available Budget</div><div class="details-colon">:</div><div class="details-value">₹{{ number_format($project->available_budget, 2) }}</div>
                    <div class="details-label">Type of Project</div><div class="details-colon">:</div><div class="details-value">{{ $project->type_of_project }}</div>
                    <div class="details-label">Theme</div><div class="details-colon">:</div><div class="details-value">{{ $project->theme ?? 'N/A' }}</div>
                    <div class="details-label">Subtheme</div><div class="details-colon">:</div><div class="details-value">{{ $project->subtheme ?? 'N/A' }}</div>
                    <div class="details-label">Activity</div><div class="details-colon">:</div><div class="details-value">{{ $project->activity ?? 'N/A' }}</div>
                    <div class="details-label">Remarks</div><div class="details-colon">:</div><div class="details-value" style="font-weight: normal; color: var(--text-muted);">{{ $project->remarks ?? 'N/A' }}</div>
                    <div class="details-label">Project Status</div><div class="details-colon">:</div><div class="details-value" id="grid-project-status" style="font-weight: 600; color: var(--accent-cyan);">{{ $project->status ?? 'Pending' }}</div>
                </div>

                <!-- Connect Application Form -->
                @if($canAssignApplication && !in_array($project->type_of_project, ['Education Center', 'Cultural Center', 'Hospital or Clinics', 'Shops and Others', 'House', 'Drinking Water - Group Level', 'Drinking Water - Individual Level']))
                <div style="margin-top: 2rem; border-top: 1px solid var(--panel-border); padding-top: 1.5rem;">
                    <h3 style="color: var(--text-main); font-size: 1.1rem; margin-bottom: 1rem;">Connect Application</h3>
                    @if(!empty($project->application_id))
                        <div style="display: flex; gap: 0.75rem; align-items: center; max-width: 500px;">
                            @php
                                $app = $application;
                                $appYear = !empty($app->created_at) ? date('y', strtotime($app->created_at)) : '24';
                                $prefixes = [
                                    'Education Center' => 'EC',
                                    'Cultural Center' => 'CC',
                                    'Hospital or Clinics' => 'HC',
                                    'Shops and Others' => 'SO',
                                    'House' => 'HS',
                                    'Drinking Water - Group Level' => 'DWG',
                                    'Drinking Water - Individual Level' => 'DWI',
                                    'Orphan Care' => 'OC',
                                    'Differently Abled' => 'DA',
                                    'Family Aid' => 'FA',
                                    'General' => 'GN'
                                ];
                                $prefix = $prefixes[$project->type_of_project] ?? 'APP';
                                $formattedAppId = $app ? 'APLRCFI' . $appYear . $prefix . str_pad($app->id, 5, '0', STR_PAD_LEFT) : '—';
                                $applicantName = $app ? $app->applicant_name : '—';
                            @endphp
                            <div onclick="if(typeof showToast === 'function') { showToast('Assigned application is locked and cannot be changed.', 'warning'); } else { alert('Assigned application is locked and cannot be changed.'); }" style="cursor: pointer; flex-grow: 1;">
                                <select name="application_id" disabled style="background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #ffffff; padding: 0.5rem 1rem; border-radius: 6px; width: 100%; outline: none; font-size: 0.9rem; pointer-events: none; opacity: 0.75;" required>
                                    <option value="{{ $project->application_id }}" selected>{{ $formattedAppId }} - {{ $applicantName }}</option>
                                </select>
                            </div>
                            <button type="button" onclick="if(typeof showToast === 'function') { showToast('Assigned application is locked and cannot be changed.', 'warning'); } else { alert('Assigned application is locked and cannot be changed.'); }" class="btn-custom" style="padding: 0.55rem 1.25rem; white-space: nowrap; cursor: pointer; opacity: 0.6;">
                                Assign
                            </button>
                        </div>
                    @else
                        <form action="{{ route('projects.assign_application', $project->id) }}" method="POST" style="display: flex; gap: 0.75rem; align-items: center; max-width: 500px;">
                            @csrf
                            <select name="application_id" onchange="updateRealtimeApplicationDetails(this.value)" style="background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #ffffff; padding: 0.5rem 1rem; border-radius: 6px; flex-grow: 1; outline: none; font-size: 0.9rem;" required>
                                <option value="">Select an application to assign...</option>
                                @foreach($allApplications as $app)
                                    @php
                                        $appYear = !empty($app->created_at) ? date('y', strtotime($app->created_at)) : '24';
                                        $prefixes = [
                                            'Education Center' => 'EC',
                                            'Cultural Center' => 'CC',
                                            'Hospital or Clinics' => 'HC',
                                            'Shops and Others' => 'SO',
                                            'House' => 'HS',
                                            'Drinking Water - Group Level' => 'DWG',
                                            'Drinking Water - Individual Level' => 'DWI',
                                            'Orphan Care' => 'OC',
                                            'Differently Abled' => 'DA',
                                            'Family Aid' => 'FA',
                                            'General' => 'GN'
                                        ];
                                        $prefix = $prefixes[$project->type_of_project] ?? 'APP';
                                        $formattedAppId = 'APLRCFI' . $appYear . $prefix . str_pad($app->id, 5, '0', STR_PAD_LEFT);
                                        $isSelected = $project->application_id == $app->id ? 'selected' : '';
                                    @endphp
                                    <option value="{{ $app->id }}" {{ $isSelected }}>
                                        {{ $formattedAppId }} - {{ $app->applicant_name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn-custom" style="padding: 0.55rem 1.25rem; white-space: nowrap; cursor: pointer;">
                                Assign
                            </button>
                        </form>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- ================= STAGE 2 PANEL (APPLICANT DETAIL) ================= -->
        <div class="stage-content-panel" id="stage-content-2">
            <div class="detail-header-panel">
                <h2>APPLICANT DETAIL</h2>
            </div>
            <div style="padding: 1.5rem;">
                @php
                    $appYear = ($application && !empty($application->created_at)) ? date('y', strtotime($application->created_at)) : '24';
                    $prefixes = [
                        'Education Center' => 'EC',
                        'Cultural Center' => 'CC',
                        'Hospital or Clinics' => 'HC',
                        'Shops and Others' => 'SO',
                        'House' => 'HS',
                        'Drinking Water - Group Level' => 'DWG',
                        'Drinking Water - Individual Level' => 'DWI',
                        'Orphan Care' => 'OC',
                        'Differently Abled' => 'DA',
                        'Family Aid' => 'FA',
                        'General' => 'GN'
                    ];
                    $prefix = $prefixes[$project->type_of_project] ?? 'APP';
                    $appId = $application ? ('APLRCFI' . $appYear . $prefix . str_pad($application->id, 5, '0', STR_PAD_LEFT)) : 'N/A';
                @endphp

                {{-- Stage 2: No approval required for construction or non-construction projects --}}

                <!-- Connect Application Form inside Stage 2 for 6-stage projects (Show First) -->
                @if($canAssignApplication && in_array($project->type_of_project, ['Education Center', 'Cultural Center', 'Hospital or Clinics', 'Shops and Others', 'House', 'Drinking Water - Group Level', 'Drinking Water - Individual Level']) && $project->status !== 'Completed')
                <div style="margin-bottom: 2rem; border-bottom: 1px solid var(--panel-border); padding-bottom: 1.5rem;">
                    <h3 style="color: var(--text-main); font-size: 1.1rem; margin-bottom: 1rem;">Connect Application</h3>
                    @php
                        // PM can change if stage < 6. HOD, COO, and Super Admin can change anytime.
                        $userCanChange = ($isCoo || $isHod || $isSuperAdmin) || ($isPmOnly && $project->stage < 6);
                    @endphp

                    @if(!empty($project->application_id) && !$userCanChange)
                        <div style="display: flex; gap: 0.75rem; align-items: center; max-width: 500px;">
                            @php
                                $app = $application;
                                $appYear = !empty($app->created_at) ? date('y', strtotime($app->created_at)) : '24';
                                $prefixes = [
                                    'Education Center' => 'EC',
                                    'Cultural Center' => 'CC',
                                    'Hospital or Clinics' => 'HC',
                                    'Shops and Others' => 'SO',
                                    'House' => 'HS',
                                    'Drinking Water - Group Level' => 'DWG',
                                    'Drinking Water - Individual Level' => 'DWI',
                                    'Orphan Care' => 'OC',
                                    'Differently Abled' => 'DA',
                                    'Family Aid' => 'FA',
                                    'General' => 'GN'
                                ];
                                $prefix = $prefixes[$project->type_of_project] ?? 'APP';
                                $formattedAppId = $app ? 'APLRCFI' . $appYear . $prefix . str_pad($app->id, 5, '0', STR_PAD_LEFT) : '—';
                                $applicantName = $app ? $app->applicant_name : '—';
                            @endphp
                            <div onclick="if(typeof showToast === 'function') { showToast('Assigned application is locked after Stage 4 approval.', 'warning'); } else { alert('Assigned application is locked after Stage 4 approval.'); }" style="cursor: pointer; flex-grow: 1;">
                                <select name="application_id" disabled style="background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #ffffff; padding: 0.5rem 1rem; border-radius: 6px; width: 100%; outline: none; font-size: 0.9rem; pointer-events: none; opacity: 0.75;" required>
                                    <option value="{{ $project->application_id }}" selected>{{ $formattedAppId }} - {{ $applicantName }}</option>
                                </select>
                            </div>
                            <button type="button" onclick="if(typeof showToast === 'function') { showToast('Assigned application is locked after Stage 4 approval.', 'warning'); } else { alert('Assigned application is locked after Stage 4 approval.'); }" class="btn-custom" style="padding: 0.55rem 1.25rem; white-space: nowrap; cursor: pointer; opacity: 0.6;">
                                Change
                            </button>
                        </div>
                    @else
                        <form action="{{ route('projects.assign_application', $project->id) }}" method="POST" style="display: flex; gap: 0.75rem; align-items: center; max-width: 500px;">
                            @csrf
                            <select name="application_id" onchange="updateRealtimeApplicationDetails(this.value)" style="background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #ffffff; padding: 0.5rem 1rem; border-radius: 6px; flex-grow: 1; outline: none; font-size: 0.9rem;" required>
                                <option value="">Select an application to assign...</option>
                                @foreach($allApplications as $app)
                                    @php
                                        $appYear = !empty($app->created_at) ? date('y', strtotime($app->created_at)) : '24';
                                        $prefixes = [
                                            'Education Center' => 'EC',
                                            'Cultural Center' => 'CC',
                                            'Hospital or Clinics' => 'HC',
                                            'Shops and Others' => 'SO',
                                            'House' => 'HS',
                                            'Drinking Water - Group Level' => 'DWG',
                                            'Drinking Water - Individual Level' => 'DWI',
                                            'Orphan Care' => 'OC',
                                            'Differently Abled' => 'DA',
                                            'Family Aid' => 'FA',
                                            'General' => 'GN'
                                        ];
                                        $prefix = $prefixes[$project->type_of_project] ?? 'APP';
                                        $formattedAppId = 'APLRCFI' . $appYear . $prefix . str_pad($app->id, 5, '0', STR_PAD_LEFT);
                                        $isSelected = $project->application_id == $app->id ? 'selected' : '';
                                    @endphp
                                    <option value="{{ $app->id }}" {{ $isSelected }}>
                                        {{ $formattedAppId }} - {{ $app->applicant_name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn-custom" style="padding: 0.55rem 1.25rem; white-space: nowrap; cursor: pointer;">
                                {{ !empty($project->application_id) ? 'Change' : 'Assign' }}
                            </button>
                        </form>
                    @endif
                </div>
                @endif

                @php
                    $metaData = [];
                    if ($application) {
                        if (is_object($application)) {
                            $metaData = method_exists($application, 'toArray') ? $application->toArray() : (array) $application;
                            if (isset($application->meta)) {
                                $metaArr = is_array($application->meta) ? $application->meta : (json_decode($application->meta, true) ?? []);
                                $metaData = array_merge($metaData, $metaArr);
                            }
                            if (method_exists($application, 'address') && \Illuminate\Support\Facades\Schema::hasTable('applicant_addresses') && ($addrObj = $application->address()->first())) {
                                $metaData = array_merge($metaData, array_filter($addrObj->toArray()));
                            }
                        } elseif (is_array($application)) {
                            $metaData = $application;
                        }

                        $metaData['mobile_1'] = $metaData['mobile_1'] ?? $metaData['contact_number_1'] ?? $metaData['mobile'] ?? null;
                        $metaData['mobile_2'] = $metaData['mobile_2'] ?? $metaData['contact_number_2'] ?? null;
                        $metaData['post_office'] = $metaData['post_office'] ?? $metaData['post'] ?? null;
                        $metaData['panchayat'] = $metaData['panchayat'] ?? $metaData['panchayath'] ?? null;
                        $metaData['pin_code'] = $metaData['pin_code'] ?? $metaData['pincode'] ?? $metaData['pin'] ?? null;
                    }

                    $formatVal = function($val) {
                        return !empty($val) ? $val : '<span style="color: var(--text-muted); font-style: italic;">N/A</span>';
                    };
                @endphp

                                <div id="realtime-application-details-container">
                    @if($application)
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                            <!-- Col 1 -->
                            <div>
                                <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">1. Personal Details of Applicant</h4>
                                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px; color: var(--text-muted);">Applicant Name:</td><td style="color: var(--text-main); font-weight: 600;">{!! $formatVal($application->applicant_name) !!}</td></tr>
                                    @if(!empty($metaData['committee_name']) || !empty($metaData['mahallu_name']))
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Committee Name:</td><td>{!! $formatVal($metaData['committee_name'] ?? $metaData['mahallu_name'] ?? null) !!}</td></tr>
                                    @endif
                                    @if(!empty($metaData['father_name']))
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Father Name:</td><td>{!! $formatVal($metaData['father_name']) !!}</td></tr>
                                    @endif
                                    @if(!empty($metaData['mother_name']))
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Mother Name:</td><td>{!! $formatVal($metaData['mother_name']) !!}</td></tr>
                                    @endif
                                    @if(!empty($metaData['fathers_father']))
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Father's Father:</td><td>{!! $formatVal($metaData['fathers_father']) !!}</td></tr>
                                    @endif
                                    @if(!empty($metaData['gender']) || !empty($metaData['age']) || !empty($metaData['dob']))
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Gender / Age / DOB:</td><td>{!! $formatVal($metaData['gender'] ?? null) !!} {!! !empty($metaData['age']) ? '/ ' . $metaData['age'] . ' yrs' : '' !!} {!! !empty($metaData['dob']) ? '/ ' . $metaData['dob'] : '' !!}</td></tr>
                                    @endif
                                    @if(!empty($metaData['aadhar_number']))
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Aadhaar Number:</td><td>{!! $formatVal($metaData['aadhar_number']) !!}</td></tr>
                                    @endif
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Location / Address:</td><td>{!! $formatVal($metaData['location'] ?? $metaData['place'] ?? null) !!} {!! !empty($metaData['address']) ? '/ ' . $metaData['address'] : '' !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Village / Post / Panch:</td><td>{!! $formatVal($metaData['village'] ?? null) !!} / {!! $formatVal($metaData['post'] ?? null) !!} / {!! $formatVal($metaData['panchayath'] ?? $metaData['panchayat'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">District:</td><td>{!! $formatVal($metaData['district'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">State:</td><td>{!! $formatVal($metaData['state'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Pin Code:</td><td>{!! $formatVal($metaData['pin_code'] ?? ($metaData['pin'] ?? null)) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Contact Number 1:</td><td>{!! $formatVal($metaData['contact_number_1'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Contact Number 2:</td><td>{!! $formatVal($metaData['contact_number_2'] ?? null) !!}</td></tr>
                                </table>

                                <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">2. Beneficiary Details Summary</h4>
                                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                    @if(!empty($metaData['male_adults']) || !empty($metaData['male_children']))
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px; color: var(--text-muted);">Male Adults / Children:</td><td>Adults: {!! $formatVal($metaData['male_adults'] ?? null) !!} / Children: {!! $formatVal($metaData['male_children'] ?? null) !!}</td></tr>
                                    @endif
                                    @if(!empty($metaData['female_adults']) || !empty($metaData['female_children']))
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Female Adults / Children:</td><td>Adults: {!! $formatVal($metaData['female_adults'] ?? null) !!} / Children: {!! $formatVal($metaData['female_children'] ?? null) !!}</td></tr>
                                    @endif
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Total Benefited People:</td><td style="font-weight: 600; color: var(--text-main);">{!! $formatVal($metaData['num_benefited_people'] ?? $metaData['families_in_mahallu'] ?? null) !!} {!! !empty($metaData['num_benefited_people']) || !empty($metaData['families_in_mahallu']) ? 'people / families' : '' !!}</td></tr>
                                </table>
                            </div>

                            <!-- Col 2 -->
                            <div>
                                <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">3. Owner of the Proposed Land</h4>
                                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px; color: var(--text-muted);">Land Owner:</td><td style="font-weight: 600; color: var(--text-main);">{!! $formatVal($metaData['land_owner_name'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Owner Address:</td><td>{!! $formatVal($metaData['land_owner_address'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Place / Post / Panch:</td><td>{!! $formatVal($metaData['land_owner_place'] ?? null) !!} / {!! $formatVal($metaData['land_owner_post'] ?? null) !!} / {!! $formatVal($metaData['land_owner_panchayath'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">District / Mobile:</td><td>{!! $formatVal($metaData['land_owner_district'] ?? null) !!} / {!! $formatVal($metaData['land_owner_mobile'] ?? null) !!}</td></tr>
                                </table>

                                <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">4. Project & Well Details</h4>
                                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px; color: var(--text-muted);">Well Type:</td><td style="font-weight: 600; color: var(--text-main);">{!! $formatVal($metaData['well_type'] ?? $metaData['project_type'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Expected Depth:</td><td>{!! !empty($metaData['well_depth']) ? $metaData['well_depth'] . ' ft' : '<span style="color: var(--text-muted); font-style: italic;">N/A</span>' !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Legal Permissions:</td><td>{!! $formatVal($metaData['legal_permissions'] ?? $metaData['legal_approvals_status'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Estimated Cost:</td><td style="color: var(--accent-green); font-weight: 600;">{{ $application->amount_requested ? '₹' . number_format($application->amount_requested) : 'N/A' }}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Review Status:</td><td style="font-weight: 600; color: var(--text-main);">{{ $application->status }}</td></tr>
                                </table>
                            </div>
                        </div>

                        <div style="margin-top: 1.5rem; border-top: 1px solid var(--panel-border); padding-top: 1rem;">
                            <h5 style="color: var(--accent-cyan); font-size: 0.85rem; margin-bottom: 0.5rem; text-transform: uppercase; font-weight: 700;">Additional Notes:</h5>
                            <p style="color: var(--text-muted); line-height: 1.5; font-size: 0.85rem; margin: 0; background-color: #121824; padding: 0.75rem; border-radius: 6px; border: 1px solid var(--panel-border); min-height: 50px;">
                                {{ $application->details ? $application->details : 'No additional notes provided.' }}
                            </p>
                        </div>
                        @php
                            $recName = $metaData['recommender_name'] ?? ($metaData['recommendation_name'] ?? null);
                            $recOrg = $metaData['recommender_org'] ?? ($metaData['recommendation_organization'] ?? null);
                            $recOrgOther = $metaData['recommender_org_other'] ?? ($metaData['recommendation_organization_other'] ?? null);
                            $recPhone = $metaData['recommender_phone'] ?? ($metaData['recommendation_phone'] ?? null);
                            $recPos = $metaData['recommender_position'] ?? ($metaData['recommendation_position'] ?? null);
                            $displayOrg = ($recOrg === 'Others') ? ($recOrgOther ?: 'Others') : $recOrg;
                        @endphp

                        @if($recName || $recOrg || $recPhone || $recPos)
                        <div style="margin-top: 1.5rem; border-top: 1px solid var(--panel-border); padding-top: 1rem;">
                            <h5 style="color: var(--accent-cyan); font-size: 0.85rem; margin-bottom: 0.75rem; text-transform: uppercase; font-weight: 700;">Recommendation Details</h5>
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                @if($recName)<tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.4rem 0; font-weight: 600; width: 140px; color: var(--text-muted);">Recommender Name:</td><td>{!! $formatVal($recName) !!}</td></tr>@endif
                                @if($recOrg)<tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.4rem 0; font-weight: 600; color: var(--text-muted);">Organization:</td><td>{!! $formatVal($displayOrg) !!}</td></tr>@endif
                                @if($recPhone)<tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.4rem 0; font-weight: 600; color: var(--text-muted);">Phone:</td><td>{!! $formatVal($recPhone) !!}</td></tr>@endif
                                @if($recPos)<tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.4rem 0; font-weight: 600; color: var(--text-muted);">Position / Designation:</td><td>{!! $formatVal($recPos) !!}</td></tr>@endif
                            </table>
                        </div>
                        @endif

                    @else
                        <div style="text-align: center; padding: 3rem; background-color: rgba(255, 255, 255, 0.02); border-radius: 8px; border: 1px dashed var(--panel-border); margin: 2rem 0;">
                            <i class="bx bx-link-external" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
                            <h3 style="color: var(--text-main); font-size: 1.2rem; margin-bottom: 0.5rem;">No Application Connected</h3>
                            <p style="color: var(--text-muted); font-size: 0.9rem; max-width: 400px; margin: 0 auto;">Please connect this project to an application using the form below to view application details.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- ================= STAGE 3 PANEL (FILES) ================= -->
        <div class="stage-content-panel" id="stage-content-3">
            <div class="detail-header-panel">
                <h2>FILES</h2>
            </div>
            <div style="padding: 1.5rem;">
                @if(empty($project->application_id))
                    <div style="background-color: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); color: #f59e0b; padding: 0.85rem 1.25rem; border-radius: 6px; font-size: 0.9rem; font-weight: 600; margin-bottom: 1.5rem;">
                        <i class="bx bx-error" style="vertical-align: middle; margin-right: 0.35rem; font-size: 1.1rem;"></i> Checklist ticking is disabled. Please assign/connect an application in Stage 2 first.
                    </div>
                @endif

                @if($project->stage == 3 && $project->status === 'Rejected')
                    <div style="margin-bottom: 1.5rem;">
                        @if($isPmOnly || $isEngineerOnly || $isSuperAdmin)
                            <form action="{{ route('projects.approve', $project->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="action" value="submit_corrections">
                                <button type="submit" class="btn-custom" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border: none; color: #ffffff; font-weight: 700; padding: 0.6rem 1.8rem; cursor: pointer; border-radius: 6px;">
                                    Submit Corrections & Proceed to Stage 4
                                </button>
                            </form>
                        @else
                            <div style="background-color: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #ff8a8a; padding: 0.85rem 1.25rem; border-radius: 6px; font-size: 0.9rem; font-weight: 600; display: inline-block;">
                                <i class="bx bx-error-circle"></i> Rejected. Pending corrections from Project Manager/Engineer.
                            </div>
                        @endif
                    </div>
                @endif


                <table class="stage-table">
                    <thead>
                        <tr>
                            <th>Document Name</th>
                            <th>Remark</th>
                            <th style="width: 200px;">Ticked At</th>
                            <th style="width: 120px; text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $docs = [
                                'Recommendation letter',
                                'Copy of the land document ( well site)',
                                'Copy of the land document ( water tank site)',
                                'No objection certificate of both land owners for implementing community drinking water project in their land',
                                '3 Quotations of the well, tank, pump house and house connection works',
                                'Agreement with committee',
                                'Agreement with contractor',
                                'Site study report'
                            ];
                            $docRecord = $project->files_with_timestamps;
                            $pFiles = $project->files ?? [];
                            $siteStudyData = $project->projectSiteStudy ?? null;
                        @endphp
                        @foreach($docs as $doc)
                            @php
                                $column = \App\Models\ProjectDocument::$docColumnMap[$doc] ?? null;
                                $filePath = ($docRecord && $column) ? $docRecord->$column : null;
                                $timeColumn = $column ? $column . '_ticked_at' : null;
                                $tickedAtDate = ($docRecord && $timeColumn) ? $docRecord->$timeColumn : null;
                                $tickedAt = $tickedAtDate ? \Carbon\Carbon::parse($tickedAtDate)->timezone('Asia/Kolkata')->format('d-M-Y h:i A') : null;
                                
                                if ($filePath === '0') {
                                    $filePath = null;
                                }

                                $docKey = str_replace(' ', '_', strtolower($doc));
                                $docRemark = $pFiles['doc_remarks'][$docKey] ?? ($pFiles['checklist'][$docKey]['remarks'] ?? '');
                            @endphp
                            <tr>
                                <td style="font-weight: 600; color: var(--text-main); vertical-align: middle;">
                                    {{ $doc }}
                                    @if($doc === 'Site study' || $doc === 'Site study report')
                                        <button type="button" 
                                                onclick="openSiteStudyModal()" 
                                                title="View/Edit Full Site Study Report (1000+ words supported)"
                                                style="background: #0d9488; color: white; border: none; padding: 0.25rem 0.55rem; border-radius: 4px; font-size: 0.78rem; cursor: pointer; margin-left: 0.5rem; display: inline-flex; align-items: center; gap: 0.3rem;">
                                            <i class="bx bx-file"></i> Open Report (1000+ words)
                                        </button>
                                    @endif
                                </td>
                                <td style="vertical-align: middle;" id="remark-cell-{{ str_replace(' ', '_', strtolower($doc)) }}">
                                    @if($isProjectManager && !$isLockedForEditing)
                                        @if(!empty($filePath))
                                            <span style="font-size: 0.85rem; color: var(--text-main);">{{ $docRemark ?: '-' }}</span>
                                        @else
                                            <input type="text" 
                                                   id="remark-input-{{ str_replace(' ', '_', strtolower($doc)) }}"
                                                   value="{{ $docRemark }}" 
                                                   placeholder="Add remark..." 
                                                   onchange="saveDocRemark('{{ $doc }}', this.value, this)"
                                                   style="background-color: #ffffff !important; color: #000000 !important; border: 1px solid #cccccc; padding: 0.35rem 0.65rem; border-radius: 4px; font-size: 0.85rem; width: 100%; max-width: 260px; outline: none;">
                                        @endif
                                    @else
                                        <span style="font-size: 0.85rem; color: var(--text-main);">{{ $docRemark ?: '-' }}</span>
                                    @endif
                                </td>
                                <td id="ticked-at-{{ str_replace(' ', '_', strtolower($doc)) }}" style="color: var(--text-muted); font-size: 0.9rem; vertical-align: middle;">
                                    {{ $tickedAt ?? '-' }}
                                </td>
                                <td style="vertical-align: middle; text-align: center; display: flex; justify-content: center;">
                                    @if($isProjectManager && !$isLockedForEditing)
                                        <button type="button" onclick="toggleChecklistDocument(this, '{{ $doc }}')" style="background: transparent; border: none; cursor: pointer; padding: 0; outline: none; display: flex; align-items: center; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'">
                                            @if(!empty($filePath))
                                                <i class="bx bxs-checkbox-checked" style="color: var(--accent-green); font-size: 2.2rem;"></i>
                                            @else
                                                <i class="bx bx-checkbox" style="color: var(--text-muted); font-size: 2.2rem;"></i>
                                            @endif
                                        </button>
                                    @else
                                        @if(!empty($filePath))
                                            <span style="color: var(--accent-green); font-weight: 600; display: inline-flex; align-items: center; gap: 0.35rem; background: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent-green); padding: 0.3rem 0.65rem; border-radius: 6px; font-size: 0.8rem;">
                                                <i class="bx bx-check-circle" style="font-size: 1rem;"></i> Completed
                                            </span>
                                        @else
                                            <span style="color: var(--accent-red); font-weight: 500; display: inline-flex; align-items: center; gap: 0.35rem; background: rgba(239, 68, 68, 0.1); border: 1px solid var(--accent-red); padding: 0.3rem 0.65rem; border-radius: 6px; font-size: 0.8rem;">
                                                <i class="bx bx-x-circle" style="font-size: 1rem;"></i> Pending
                                            </span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ================= STAGE 4 PANEL (FUNDS ALLOCATED) ================= -->
        <div class="stage-content-panel" id="stage-content-4">
            <div class="detail-header-panel">
                <h2>FUNDS ALLOCATED</h2>
            </div>
            <div style="padding: 1.5rem;">
                @if(empty($project->application_id))
                    <div style="background-color: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); color: #f59e0b; padding: 0.85rem 1.25rem; border-radius: 6px; font-size: 0.9rem; font-weight: 600; margin-bottom: 1.5rem;">
                        <i class="bx bx-error" style="vertical-align: middle; margin-right: 0.35rem; font-size: 1.1rem;"></i> Budget allocation editing is disabled. Please assign/connect an application in Stage 2 first.
                    </div>
                @endif

                @php
                    $materials = $project->materials;
                    if (empty($materials)) {
                        $materials = [];
                    }
                    $totalAmount = 0;
                    foreach($materials as $item) {
                        $totalAmount += $item['amount'];
                    }

                    $pFiles = $project->files ?? [];
                    $commContribs = $pFiles['community_contributions'] ?? [];
                    if (empty($commContribs)) {
                        $commContribs = [];
                    }
                    $commTotal = 0;
                    foreach ($commContribs as $c) {
                        $commTotal += $c['amount'];
                    }
                    $grandTotal = $totalAmount + $commTotal;
                @endphp

                @if($project->stage <= 4 && $project->status !== 'Approved' && $project->status !== 'Completed')
                    <div style="margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 1rem; align-items: flex-start;">

                        {{-- COO / HOD / SuperAdmin: Always see Review & Approval Actions --}}
                        @if($isCoo || $isHod || $isSuperAdmin)
                            <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; width: 100%; max-width: 700px; background: rgba(255,255,255,0.02); padding: 1.25rem; border: 1px solid var(--panel-border); border-radius: 8px;">
                                <h4 style="color: var(--text-main); font-size: 0.95rem; font-weight: 700; margin: 0 0 0.5rem 0; width: 100%; text-transform: uppercase;">
                                    <i class="bx bx-shield-check" style="color: #10b981; margin-right: 0.4rem;"></i>
                                    Review &amp; Approval Actions
                                    @if($project->status === 'Pending Approval')
                                        <span style="font-size: 0.75rem; background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); color: #10b981; padding: 0.2rem 0.6rem; border-radius: 20px; margin-left: 0.5rem; vertical-align: middle;">Submitted by PM</span>
                                    @elseif($project->status === 'Pending')
                                        <span style="font-size: 0.75rem; background: rgba(245,158,11,0.15); border: 1px solid rgba(245,158,11,0.3); color: #f59e0b; padding: 0.2rem 0.6rem; border-radius: 20px; margin-left: 0.5rem; vertical-align: middle;">Awaiting PM Submission</span>
                                    @elseif($project->status === 'Rejected')
                                        <span style="font-size: 0.75rem; background: rgba(235,59,90,0.15); border: 1px solid rgba(235,59,90,0.3); color: #eb3b5a; padding: 0.2rem 0.6rem; border-radius: 20px; margin-left: 0.5rem; vertical-align: middle;">Previously Rejected</span>
                                    @endif
                                </h4>

                                <!-- Approve Form -->
                                <form action="{{ route('projects.approve', $project->id) }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn-custom" style="background: linear-gradient(135deg, #2ecc71, #27ae60); border-color: #27ae60; color: #ffffff; cursor: pointer; font-weight: 700; padding: 0.55rem 1.5rem;">
                                        <i class="bx bx-check-circle"></i> Approve Project
                                    </button>
                                </form>

                                <!-- Reject Form -->
                                <form action="{{ route('projects.approve', $project->id) }}" method="POST" style="display: flex; gap: 0.75rem; flex-grow: 1; align-items: center; margin: 0;">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <input type="text" name="remarks" placeholder="Provide rejection reason (optional)…" style="background-color: var(--bg-color); border: 1px solid var(--panel-border); color: var(--text-main); padding: 0.5rem; border-radius: 6px; flex-grow: 1; font-size: 0.85rem; outline: none;">
                                    <button type="submit" class="btn-danger-custom" style="padding: 0.55rem 1.5rem; background: #eb3b5a; border-color: #eb3b5a; color: #ffffff; font-weight: 700; cursor: pointer;">
                                        <i class="bx bx-x-circle"></i> Reject
                                    </button>
                                </form>
                            </div>
                        @endif

                        {{-- PM / Engineer ONLY: Submit Button --}}
                        @if(!$isCoo && !$isHod && !$isSuperAdmin && ($isPmOnly || $isEngineerOnly))
                            @if($project->status === 'Pending' || $project->status === 'Rejected')
                                <form action="{{ route('projects.approve', $project->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="action" value="submit">
                                    <button type="submit" class="btn-custom" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border: none; color: #ffffff; font-weight: 700; padding: 0.6rem 1.8rem; cursor: pointer; border-radius: 6px;">
                                        <i class="bx bx-send"></i> Submit for HOD/COO Approval
                                    </button>
                                </form>
                            @elseif($project->status === 'Pending Approval')
                                <div style="background-color: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #8cf5c6; padding: 0.85rem 1.25rem; border-radius: 6px; font-size: 0.9rem; font-weight: 600; display: inline-block;">
                                    <i class="bx bx-check-circle"></i> Submitted — awaiting HOD/COO Approval.
                                </div>
                            @endif
                        @endif

                        {{-- Other roles: info message --}}
                        @if(!$isCoo && !$isHod && !$isSuperAdmin && !$isPmOnly)
                            <div style="background-color: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); color: #f59e0b; padding: 0.85rem 1.25rem; border-radius: 6px; font-size: 0.9rem; font-weight: 600; display: inline-block;">
                                <i class="bx bx-time-five"></i> Pending HOD/COO Approval.
                            </div>
                        @endif

                    </div>
                @endif

                <!-- Real-time Budget Metrics Bar -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                    <!-- Project Budget Card -->
                    <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(16, 185, 129, 0.2); padding: 1.25rem; border-radius: 8px; border-left: 4px solid #10b981;">
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Project Budget</div>
                        <div style="font-size: 1.3rem; font-weight: 700; color: var(--text-main);">₹{{ number_format($project->available_budget, 2) }}</div>
                    </div>

                    <!-- Total Allocated Card -->
                    <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(6, 182, 212, 0.2); padding: 1.25rem; border-radius: 8px; border-left: 4px solid var(--accent-cyan);">
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Total Allocated</div>
                        <div style="font-size: 1.3rem; font-weight: 700; color: var(--accent-cyan);">₹{{ number_format($totalAmount, 2) }}</div>
                    </div>

                    <!-- Total Card (Allocated + Community Contribution) -->
                    <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(129, 140, 248, 0.2); padding: 1.25rem; border-radius: 8px; border-left: 4px solid #818cf8;">
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Total</div>
                        <div style="font-size: 1.3rem; font-weight: 700; color: #818cf8;">₹{{ number_format($grandTotal, 2) }}</div>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem;">
                        <span style="color: var(--text-muted);">Search:</span>
                        <input type="text" placeholder="Search budget..." class="form-control-dark" style="width: 160px; padding: 0.35rem 0.75rem; border-radius: 4px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: var(--text-main);">
                    </div>
                    @if($isProjectManager && $hasApplication && !$isLockedForEditing)
                        <button onclick="openAddMaterialModal()" class="btn-custom" style="background: rgba(6, 182, 212, 0.1); border: 1px solid var(--accent-cyan); color: var(--accent-cyan); cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem;">
                            <i class="bx bx-plus"></i> Add Item
                        </button>
                    @endif
                </div>

                <table class="stage-table">
                    <thead>
                        <tr>
                            <th>Input</th>
                            <th style="text-align: right;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalAmount = 0;
                        @endphp
                        @foreach($materials as $index => $item)
                            @php $totalAmount += $item['amount']; @endphp
                            <tr>
                                <td style="font-weight: 600; color: var(--text-main); vertical-align: middle;">{{ $item['material'] }}</td>
                                <td style="text-align: right; font-weight: 600; color: var(--text-main); vertical-align: middle;">
                                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.5rem;">
                                        <span>₹ {{ number_format($item['amount'], 2) }}</span>
                                        @if($isProjectManager && $hasApplication && !$isLockedForEditing)
                                            <button onclick="openEditMaterialModal({{ $index }}, '{{ addslashes($item['material']) }}', {{ $item['amount'] }})" class="btn-custom" style="background: transparent; color: var(--accent-cyan); border: 1px solid var(--accent-cyan); padding: 0.25rem; font-size: 0.85rem; border-radius: 4px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; margin: 0;" title="Edit">
                                                <i class="bx bx-pencil"></i>
                                            </button>
                                            <form action="{{ route('projects.delete_material', [$project->id, $index]) }}" method="POST" style="display: inline-flex; margin: 0;" onsubmit="return confirm('Are you sure you want to delete this material?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-danger-custom" style="padding: 0.25rem; font-size: 0.85rem; display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px;" title="Delete">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        <tr style="border-top: 2px solid var(--panel-border);">
                            <td style="font-weight: 700; color: var(--accent-cyan);">Total</td>
                            <td style="text-align: right; font-weight: 700; color: var(--accent-cyan);">₹ {{ number_format($totalAmount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>

                @php
                    $pFiles = $project->files ?? [];
                    $commContribs = $pFiles['community_contributions'] ?? [];
                    if (empty($commContribs)) {
                        $commContribs = [];
                    }
                    $commTotal = 0;
                    foreach ($commContribs as $c) {
                        $commTotal += $c['amount'];
                    }
                @endphp

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 2.5rem; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                    <h3 style="color: var(--text-main); font-size: 1rem; margin: 0; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Community Contribution</h3>
                    @if($isProjectManager && $hasApplication && !$isLockedForEditing)
                        <button onclick="openAddCommContribModal()" class="btn-custom" style="background: rgba(6, 182, 212, 0.1); border: 1px solid var(--accent-cyan); color: var(--accent-cyan); cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem;">
                            <i class="bx bx-plus"></i> Add Item
                        </button>
                    @endif
                </div>

                <table class="stage-table" style="margin-bottom: 1.5rem;">
                    <thead>
                        <tr>
                            <th>Input</th>
                            <th style="text-align: right;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($commContribs as $index => $item)
                            <tr>
                                <td style="font-weight: 600; color: var(--text-main); vertical-align: middle;">{{ $item['item'] }}</td>
                                <td style="text-align: right; font-weight: 600; color: var(--text-main); vertical-align: middle;">
                                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.5rem;">
                                        <span>₹ {{ number_format($item['amount'], 2) }}</span>
                                        @if($isProjectManager && $hasApplication && !$isLockedForEditing)
                                            <button onclick="openEditCommContribModal({{ $index }}, '{{ addslashes($item['item']) }}', {{ $item['amount'] }})" class="btn-custom" style="background: transparent; color: var(--accent-cyan); border: 1px solid var(--accent-cyan); padding: 0.25rem; font-size: 0.85rem; border-radius: 4px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; margin: 0;" title="Edit">
                                                <i class="bx bx-pencil"></i>
                                            </button>
                                            <form action="{{ route('projects.delete_community_contribution', [$project->id, $index]) }}" method="POST" style="display: inline-flex; margin: 0;" onsubmit="return confirm('Are you sure you want to delete this community contribution?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-danger-custom" style="padding: 0.25rem; font-size: 0.85rem; display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px;" title="Delete">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        <tr style="border-top: 2px solid var(--panel-border);">
                            <td style="font-weight: 700; color: var(--accent-cyan);">Total</td>
                            <td style="text-align: right; font-weight: 700; color: var(--accent-cyan);">₹ {{ number_format($commTotal, 2) }}</td>
                        </tr>
                    </tbody>
                </table>

                @php
                    $contractors = $project->files['contractors'] ?? [];
                    // Keep backward compatibility if they had a single contractor_details saved
                    $legacyContractor = $project->files['contractor_details'] ?? null;
                    if (empty($contractors) && $legacyContractor) {
                        $contractors = [$legacyContractor];
                    }
                @endphp

                <!-- Contractor Details Section -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 2.5rem; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                    <h3 style="color: var(--text-main); font-size: 1rem; margin: 0; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Contractor Details</h3>
                    @if($isProjectManager && $hasApplication && !$isLockedForEditing)
                        <button onclick="openAddContractorModal()" class="btn-custom" style="background: rgba(6, 182, 212, 0.1); border: 1px solid var(--accent-cyan); color: var(--accent-cyan); cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem;">
                            <i class="bx bx-plus"></i> Add Contractor
                        </button>
                    @endif
                </div>

                @if(!empty($contractors))
                    <div style="display: flex; flex-direction: column; gap: 1.5rem; margin-bottom: 1.5rem;">
                        @foreach($contractors as $index => $contractor)
                            <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--panel-border); padding: 1.5rem; border-radius: 8px; position: relative;">
                                @if($isProjectManager && $hasApplication && !$isLockedForEditing)
                                    <div style="position: absolute; top: 1rem; right: 1rem; display: flex; gap: 0.5rem; z-index: 10;">
                                        <button onclick="openEditContractorModal({{ $index }}, {{ json_encode($contractor) }})" class="btn-custom" style="background: transparent; color: var(--accent-cyan); border: 1px solid var(--accent-cyan); padding: 0.25rem; font-size: 0.85rem; border-radius: 4px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; margin: 0;" title="Edit">
                                            <i class="bx bx-pencil"></i>
                                        </button>
                                        <form action="{{ route('projects.delete_contractor', [$project->id, $index]) }}" method="POST" style="display: inline-flex; margin: 0;" onsubmit="return confirm('Are you sure you want to delete this contractor?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-danger-custom" style="padding: 0.25rem; font-size: 0.85rem; display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px;" title="Delete">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; padding-right: 4rem;">
                                    <div>
                                        <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 0.25rem;">Contractor Name</span>
                                        <span style="color: var(--text-main); font-weight: 600; font-size: 0.95rem;">{{ $contractor['contractor_name'] }}</span>
                                    </div>
                                    <div>
                                        <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 0.25rem;">Phone Number</span>
                                        <span style="color: var(--text-main); font-weight: 600; font-size: 0.95rem;">{{ $contractor['phone'] }}</span>
                                    </div>
                                    <div>
                                        <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 0.25rem;">Company Name</span>
                                        <span style="color: var(--text-main); font-weight: 600; font-size: 0.95rem;">{{ $contractor['company_name'] }}</span>
                                    </div>
                                    <div>
                                        <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 0.25rem;">Type of Contract</span>
                                        <span style="color: var(--text-main); font-weight: 600; font-size: 0.95rem;">{{ $contractor['type_of_contract'] }}</span>
                                    </div>
                                </div>
                                <div style="margin-top: 1.25rem; border-top: 1px solid rgba(255, 255, 255, 0.05); padding-top: 1rem; display: grid; grid-template-columns: 1fr; gap: 1.25rem;">
                                    <div>
                                        <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 0.25rem;">Purpose of Contract</span>
                                        <p style="color: var(--text-main); margin: 0; font-size: 0.95rem; line-height: 1.5; white-space: pre-line;">{{ $contractor['purpose_of_contract'] }}</p>
                                    </div>
                                    <div>
                                        <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 0.25rem;">Address</span>
                                        <p style="color: var(--text-main); margin: 0; font-size: 0.95rem; line-height: 1.5; white-space: pre-line;">{{ $contractor['address'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="background: rgba(255, 255, 255, 0.01); border: 1px dashed var(--panel-border); padding: 2rem; border-radius: 8px; text-align: center; color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">
                        <i class="bx bx-info-circle" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem; color: var(--text-muted);"></i>
                        No contractor details added to this project yet.
                    </div>
                @endif
            </div>
        </div>

        <!-- ================= STAGE 5 PANEL (EVALUATION & INSPECTION) ================= -->
        <div class="stage-content-panel" id="stage-content-5">
            <div class="detail-header-panel">
                <h2>EVALUATION & INSPECTION</h2>
            </div>
            <div style="padding: 1.5rem;">
                @if(empty($project->application_id))
                    <div style="background-color: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); color: #f59e0b; padding: 0.85rem 1.25rem; border-radius: 6px; font-size: 0.9rem; font-weight: 600; margin-bottom: 1.5rem;">
                        <i class="bx bx-error" style="vertical-align: middle; margin-right: 0.35rem; font-size: 1.1rem;"></i> Expense management is disabled. Please assign/connect an application in Stage 2 first.
                    </div>
                @endif



                @php
                    $stage5Materials = $project->materials;
                    if (empty($stage5Materials)) {
                        $stage5Materials = [];
                    }
                    $totalAllocatedAmount = 0;
                    foreach ($stage5Materials as $item) {
                        $totalAllocatedAmount += $item['amount'];
                    }

                    $expenses = $project->expenses;
                    if (empty($expenses)) {
                        $expenses = [];
                    }
                    $totalExpensesAmount = 0;
                    foreach ($expenses as $item) {
                        if (!isset($item['comm_index'])) {
                            $totalExpensesAmount += $item['amount'];
                        }
                    }

                    $stage5TotalBudget = (float)$totalAllocatedAmount;
                    $stage5SpentAmount = (float)$totalExpensesAmount;
                    $stage5BalanceAmount = $stage5TotalBudget - $stage5SpentAmount;
                    
                    $stage5SpentPercentage = $stage5TotalBudget > 0 ? min(100, ($stage5SpentAmount / $stage5TotalBudget) * 100) : 0;
                    $stage5BalancePercentage = 100 - $stage5SpentPercentage;
                    
                    // SVG Circumference is 2 * pi * 50 = 314.16
                    $stage5Circumference = 314.16;
                    $stage5SpentDashoffset = $stage5Circumference - ($stage5Circumference * ($stage5SpentPercentage / 100));

                    // Community Contributions
                    $stage5CommContribs = $project->files['community_contributions'] ?? [];
                    if (empty($stage5CommContribs)) {
                        $stage5CommContribs = [];
                    }
                    $stage5CommTotal = 0;
                    foreach ($stage5CommContribs as $c) {
                        $stage5CommTotal += $c['amount'];
                    }

                    // Community Contribution Expenses
                    $stage5CommSpent = 0;
                    foreach ($expenses as $exp) {
                        if (isset($exp['comm_index'])) {
                            $stage5CommSpent += $exp['amount'];
                        }
                    }
                    $stage5CommBalance = $stage5CommTotal - $stage5CommSpent;
                    $stage5CommSpentPercentage = $stage5CommTotal > 0 ? min(100, ($stage5CommSpent / $stage5CommTotal) * 100) : 0;
                    $stage5CommBalancePercentage = 100 - $stage5CommSpentPercentage;
                    $stage5CommCircumference = 314.16;
                    $stage5CommSpentDashoffset = $stage5CommCircumference - ($stage5CommCircumference * ($stage5CommSpentPercentage / 100));
                @endphp



                <!-- Financial Summaries side by side -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                    <!-- Financial Summary (Allocated Budget) -->
                    <div style="display: flex; align-items: center; justify-content: center; gap: 1.5rem; background: rgba(255,255,255,0.02); border: 1px solid var(--panel-border); padding: 1.5rem; border-radius: 8px; flex-wrap: wrap;">
                        <!-- Circular Diagram -->
                        <div style="position: relative; width: 100px; height: 100px; flex-shrink: 0;">
                            <svg width="100" height="100" viewBox="0 0 120 120">
                                <!-- Background Circle (Balance - Cyan) -->
                                <circle cx="60" cy="60" r="50" fill="transparent" stroke="var(--accent-cyan)" stroke-width="12" />
                                <!-- Foreground Circle (Spent - Red/Orange) -->
                                <circle cx="60" cy="60" r="50" fill="transparent" stroke="var(--accent-red)" stroke-width="12"
                                        stroke-dasharray="314.16" stroke-dashoffset="{{ $stage5SpentDashoffset }}"
                                        stroke-linecap="round" transform="rotate(-90 60 60)"
                                        style="transition: stroke-dashoffset 0.5s ease-in-out;" />
                            </svg>
                            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--text-main);">
                                <span style="font-size: 1.15rem; font-weight: 700;">{{ number_format($stage5SpentPercentage, 0) }}%</span>
                                <span style="font-size: 0.6rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Spent</span>
                            </div>
                        </div>
                        <!-- Stats Details -->
                        <div style="flex-grow: 1; min-width: 250px;">
                            <h4 style="margin: 0 0 0.75rem 0; font-size: 0.9rem; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700;">Financial Summary (Allocated Budget)</h4>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(110px, 1fr)); gap: 0.75rem;">
                                <!-- Total Budget Card -->
                                <div style="background: rgba(255,255,255,0.01); border: 1px solid var(--panel-border); padding: 0.5rem 0.75rem; border-radius: 6px;">
                                    <div style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600; margin-bottom: 0.15rem;">Total Allocated</div>
                                    <span style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);">₹{{ number_format($stage5TotalBudget, 2) }}</span>
                                </div>
                                <!-- Balance Card -->
                                <div style="background: rgba(255,255,255,0.01); border: 1px solid rgba(6, 182, 212, 0.2); padding: 0.5rem 0.75rem; border-radius: 6px;">
                                    <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.15rem;">
                                        <span style="display: inline-block; width: 6px; height: 6px; background-color: var(--accent-cyan); border-radius: 50%;"></span>
                                        <span style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600;">Total Balance</span>
                                    </div>
                                    <span style="font-size: 0.95rem; font-weight: 700; color: var(--accent-cyan);">₹{{ number_format($stage5BalanceAmount, 2) }}</span>
                                    <div style="font-size: 0.65rem; color: var(--text-muted); margin-top: 0.1rem;">{{ number_format($stage5BalancePercentage, 1) }}% left</div>
                                </div>
                                <!-- Expense Card -->
                                <div style="background: rgba(255,255,255,0.01); border: 1px solid rgba(239, 68, 68, 0.2); padding: 0.5rem 0.75rem; border-radius: 6px;">
                                    <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.15rem;">
                                        <span style="display: inline-block; width: 6px; height: 6px; background-color: var(--accent-red); border-radius: 50%;"></span>
                                        <span style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600;">Total Expenses</span>
                                    </div>
                                    <span style="font-size: 0.95rem; font-weight: 700; color: var(--accent-red);">₹{{ number_format($stage5SpentAmount, 2) }}</span>
                                    <div style="font-size: 0.65rem; color: var(--text-muted); margin-top: 0.1rem;">{{ number_format($stage5SpentPercentage, 1) }}% spent</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Summary (Community Contribution) -->
                    <div style="display: flex; align-items: center; justify-content: center; gap: 1.5rem; background: rgba(255,255,255,0.02); border: 1px solid var(--panel-border); padding: 1.5rem; border-radius: 8px; flex-wrap: wrap;">
                        <!-- Circular Diagram -->
                        <div style="position: relative; width: 100px; height: 100px; flex-shrink: 0;">
                            <svg width="100" height="100" viewBox="0 0 120 120">
                                <!-- Background Circle (Balance - Cyan) -->
                                <circle cx="60" cy="60" r="50" fill="transparent" stroke="var(--accent-cyan)" stroke-width="12" />
                                <!-- Foreground Circle (Spent - Red/Orange) -->
                                <circle cx="60" cy="60" r="50" fill="transparent" stroke="var(--accent-red)" stroke-width="12"
                                        stroke-dasharray="314.16" stroke-dashoffset="{{ $stage5CommSpentDashoffset }}"
                                        stroke-linecap="round" transform="rotate(-90 60 60)"
                                        style="transition: stroke-dashoffset 0.5s ease-in-out;" />
                            </svg>
                            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--text-main);">
                                <span style="font-size: 1.15rem; font-weight: 700;">{{ number_format($stage5CommSpentPercentage, 0) }}%</span>
                                <span style="font-size: 0.6rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Spent</span>
                            </div>
                        </div>
                        <!-- Stats Details -->
                        <div style="flex-grow: 1; min-width: 250px;">
                            <h4 style="margin: 0 0 0.75rem 0; font-size: 0.9rem; color: var(--text-main); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700;">Financial Summary (Community Contribution)</h4>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(110px, 1fr)); gap: 0.75rem;">
                                <!-- Total Budget Card -->
                                <div style="background: rgba(255,255,255,0.01); border: 1px solid var(--panel-border); padding: 0.5rem 0.75rem; border-radius: 6px;">
                                    <div style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600; margin-bottom: 0.15rem;">Total Contribution</div>
                                    <span style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);">₹{{ number_format($stage5CommTotal, 2) }}</span>
                                </div>
                                <!-- Balance Card -->
                                <div style="background: rgba(255,255,255,0.01); border: 1px solid rgba(6, 182, 212, 0.2); padding: 0.5rem 0.75rem; border-radius: 6px;">
                                    <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.15rem;">
                                        <span style="display: inline-block; width: 6px; height: 6px; background-color: var(--accent-cyan); border-radius: 50%;"></span>
                                        <span style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600;">Total Balance</span>
                                    </div>
                                    <span style="font-size: 0.95rem; font-weight: 700; color: var(--accent-cyan);">₹{{ number_format($stage5CommBalance, 2) }}</span>
                                    <div style="font-size: 0.65rem; color: var(--text-muted); margin-top: 0.1rem;">{{ number_format($stage5CommBalancePercentage, 1) }}% left</div>
                                </div>
                                <!-- Expense Card -->
                                <div style="background: rgba(255,255,255,0.01); border: 1px solid rgba(239, 68, 68, 0.2); padding: 0.5rem 0.75rem; border-radius: 6px;">
                                    <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.15rem;">
                                        <span style="display: inline-block; width: 6px; height: 6px; background-color: var(--accent-red); border-radius: 50%;"></span>
                                        <span style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600;">Total Expenses</span>
                                    </div>
                                    <span style="font-size: 0.95rem; font-weight: 700; color: var(--accent-red);">₹{{ number_format($stage5CommSpent, 2) }}</span>
                                    <div style="font-size: 0.65rem; color: var(--text-muted); margin-top: 0.1rem;">{{ number_format($stage5CommSpentPercentage, 1) }}% spent</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Expenses Section -->
                @if(in_array($project->type_of_project, ['Education Center', 'Cultural Center', 'Hospital or Clinics', 'Shops and Others', 'House', 'Drinking Water - Group Level', 'Drinking Water - Individual Level']))
                <div style="margin-top: 2rem; border-top: 1px solid var(--panel-border); padding-top: 1.5rem; margin-bottom: 2rem;">
                    <h3 style="color: var(--text-main); font-size: 1.1rem; margin-bottom: 1.5rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Allocated Items & Spent Expenses</h3>

                    <table class="stage-table">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th style="text-align: right;">Allocated</th>
                                <th style="text-align: right;">Spent</th>
                                <th style="text-align: right;">Balance</th>
                                <th style="text-align: center; width: 140px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stage5Materials as $materialIdx => $material)
                                @php
                                    // Filter expenses for this material
                                    $itemExpenses = array_filter($expenses, function($exp) use ($materialIdx) {
                                        return isset($exp['material_index']) && $exp['material_index'] == $materialIdx;
                                    });
                                    $itemTotalSpent = 0;
                                    foreach($itemExpenses as $exp) {
                                        $itemTotalSpent += $exp['amount'];
                                    }
                                    $itemBalance = $material['amount'] - $itemTotalSpent;
                                @endphp
                                <!-- Material Header Row -->
                                <tr style="background-color: rgba(255, 255, 255, 0.01); border-bottom: 1px solid var(--panel-border);">
                                    <td style="font-weight: 700; color: var(--text-main); vertical-align: middle;">
                                        <i class="bx bx-package" style="color: var(--accent-cyan); margin-right: 0.5rem;"></i>{{ $material['material'] }}
                                    </td>
                                    <td style="text-align: right; font-weight: 600; color: var(--text-main); vertical-align: middle;">₹{{ number_format($material['amount'], 2) }}</td>
                                    <td style="text-align: right; font-weight: 600; color: var(--accent-red); vertical-align: middle;">₹{{ number_format($itemTotalSpent, 2) }}</td>
                                    <td style="text-align: right; font-weight: 600; color: {{ $itemBalance >= 0 ? 'var(--accent-cyan)' : 'var(--accent-red)' }}; vertical-align: middle;">
                                        ₹{{ number_format($itemBalance, 2) }}
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        @if($isProjectManager && $hasApplication && !$isLockedForEditing)
                                            <button onclick="openAddExpenseModal({{ $materialIdx }}, '{{ addslashes($material['material']) }}')" class="btn-custom" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; background: rgba(6, 182, 212, 0.1); border: 1px solid var(--accent-cyan); color: var(--accent-cyan); cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem; margin: 0;">
                                                <i class="bx bx-plus"></i> Expense
                                            </button>
                                        @else
                                            <span style="color: var(--text-muted); font-size: 0.8rem; font-style: italic;">No actions</span>
                                        @endif
                                    </td>
                                </tr>
                                <!-- Sub-table / Nested Expenses list -->
                                @if(!empty($itemExpenses))
                                    @foreach($itemExpenses as $expenseIdx => $expense)
                                        <tr style="background-color: rgba(0, 0, 0, 0.15);">
                                            <td style="padding-left: 2rem; color: var(--text-muted); font-size: 0.85rem; vertical-align: middle;">
                                                <span style="display: inline-block; width: 6px; height: 6px; background-color: var(--text-muted); border-radius: 50%; margin-right: 0.5rem; vertical-align: middle;"></span>
                                                {{ $expense['expense_name'] }}
                                            </td>
                                            <td style="text-align: right; color: var(--text-muted); font-size: 0.85rem; vertical-align: middle;">
                                                Qty: {{ $expense['quantity'] ?? 1 }}
                                            </td>
                                            <td style="text-align: right; color: var(--text-muted); font-size: 0.85rem; vertical-align: middle;">₹{{ number_format($expense['amount'], 2) }}</td>
                                            <td></td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                @if($isProjectManager && $hasApplication && !$isLockedForEditing)
                                                    <div style="display: inline-flex; gap: 0.4rem;">
                                                        <button onclick="openEditExpenseModal({{ $expenseIdx }}, {{ $materialIdx }}, '{{ addslashes($expense['expense_name']) }}', {{ $expense['quantity'] ?? 1 }}, {{ $expense['amount'] }})" class="btn-custom" style="background: transparent; color: var(--accent-cyan); border: 1px solid var(--accent-cyan); padding: 0.2rem; font-size: 0.75rem; border-radius: 4px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; margin: 0;" title="Edit Expense">
                                                            <i class="bx bx-pencil"></i>
                                                        </button>
                                                        <form action="{{ route('projects.delete_expense', [$project->id, $expenseIdx]) }}" method="POST" style="display: inline-flex; margin: 0;" onsubmit="return confirm('Are you sure you want to delete this expense?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn-danger-custom" style="padding: 0.2rem; font-size: 0.75rem; display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px;" title="Delete Expense">
                                                                <i class="bx bx-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr style="background-color: rgba(0, 0, 0, 0.05);">
                                        <td colspan="4" style="padding-left: 2rem; color: var(--text-muted); font-size: 0.8rem; font-style: italic;">No expenses recorded for this item.</td>
                                        <td></td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                <!-- Community Contribution Expenses Section -->
                <div style="margin-top: 2rem; border-top: 1px solid var(--panel-border); padding-top: 1.5rem; margin-bottom: 2rem;">
                    <h3 style="color: var(--text-main); font-size: 1.1rem; margin-bottom: 1.5rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Community Contribution Items & Spent Expenses</h3>

                    <table class="stage-table">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th style="text-align: right;">Allocated Contribution</th>
                                <th style="text-align: right;">Spent</th>
                                <th style="text-align: right;">Balance</th>
                                <th style="text-align: center; width: 140px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stage5CommContribs as $commIdx => $comm)
                                @php
                                    // Filter expenses for this community contribution item
                                    $itemCommExpenses = array_filter($expenses, function($exp) use ($commIdx) {
                                        return isset($exp['comm_index']) && $exp['comm_index'] == $commIdx;
                                    });
                                    $itemTotalCommSpent = 0;
                                    foreach($itemCommExpenses as $exp) {
                                        $itemTotalCommSpent += $exp['amount'];
                                    }
                                    $itemCommBalance = $comm['amount'] - $itemTotalCommSpent;
                                @endphp
                                <!-- Comm Header Row -->
                                <tr style="background-color: rgba(255, 255, 255, 0.01); border-bottom: 1px solid var(--panel-border);">
                                    <td style="font-weight: 700; color: var(--text-main); vertical-align: middle;">
                                        <i class="bx bx-group" style="color: var(--accent-cyan); margin-right: 0.5rem;"></i>{{ $comm['item'] }}
                                    </td>
                                    <td style="text-align: right; font-weight: 600; color: var(--text-main); vertical-align: middle;">₹{{ number_format($comm['amount'], 2) }}</td>
                                    <td style="text-align: right; font-weight: 600; color: var(--accent-red); vertical-align: middle;">₹{{ number_format($itemTotalCommSpent, 2) }}</td>
                                    <td style="text-align: right; font-weight: 600; color: {{ $itemCommBalance >= 0 ? 'var(--accent-cyan)' : 'var(--accent-red)' }}; vertical-align: middle;">
                                        ₹{{ number_format($itemCommBalance, 2) }}
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        @if($isProjectManager && $hasApplication && !$isLockedForEditing)
                                            <button onclick="openAddCommExpenseModal({{ $commIdx }}, '{{ addslashes($comm['item']) }}')" class="btn-custom" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; background: rgba(6, 182, 212, 0.1); border: 1px solid var(--accent-cyan); color: var(--accent-cyan); cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem; margin: 0;">
                                                <i class="bx bx-plus"></i> Expense
                                            </button>
                                        @else
                                            <span style="color: var(--text-muted); font-size: 0.8rem; font-style: italic;">No actions</span>
                                        @endif
                                    </td>
                                </tr>
                                <!-- Sub-table / Nested Expenses list -->
                                @if(!empty($itemCommExpenses))
                                    @foreach($itemCommExpenses as $expenseIdx => $expense)
                                        <tr style="background-color: rgba(0, 0, 0, 0.15);">
                                            <td style="padding-left: 2rem; color: var(--text-muted); font-size: 0.85rem; vertical-align: middle;">
                                                <span style="display: inline-block; width: 6px; height: 6px; background-color: var(--text-muted); border-radius: 50%; margin-right: 0.5rem; vertical-align: middle;"></span>
                                                {{ $expense['expense_name'] }}
                                            </td>
                                            <td style="text-align: right; color: var(--text-muted); font-size: 0.85rem; vertical-align: middle;">
                                                Qty: {{ $expense['quantity'] ?? 1 }}
                                            </td>
                                            <td style="text-align: right; color: var(--text-muted); font-size: 0.85rem; vertical-align: middle;">₹{{ number_format($expense['amount'], 2) }}</td>
                                            <td></td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                @if($isProjectManager && $hasApplication && !$isLockedForEditing)
                                                    <div style="display: inline-flex; gap: 0.4rem;">
                                                        <button onclick="openEditCommExpenseModal({{ $expenseIdx }}, {{ $commIdx }}, '{{ addslashes($expense['expense_name']) }}', {{ $expense['quantity'] ?? 1 }}, {{ $expense['amount'] }})" class="btn-custom" style="background: transparent; color: var(--accent-cyan); border: 1px solid var(--accent-cyan); padding: 0.2rem; font-size: 0.75rem; border-radius: 4px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; margin: 0;" title="Edit Expense">
                                                            <i class="bx bx-pencil"></i>
                                                        </button>
                                                        <form action="{{ route('projects.delete_expense', [$project->id, $expenseIdx]) }}" method="POST" style="display: inline-flex; margin: 0;" onsubmit="return confirm('Are you sure you want to delete this expense?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn-danger-custom" style="padding: 0.25rem; font-size: 0.75rem; display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px;" title="Delete Expense">
                                                                <i class="bx bx-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr style="background-color: rgba(0, 0, 0, 0.05);">
                                        <td colspan="4" style="padding-left: 2rem; color: var(--text-muted); font-size: 0.8rem; font-style: italic;">No expenses recorded for this contribution item.</td>
                                        <td></td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                                <div style="margin-top: 2rem; border-top: 1px solid var(--panel-border); padding-top: 1.5rem; margin-bottom: 2rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
                        <h3 style="color: var(--text-main); font-size: 1.1rem; margin: 0; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Monitoring Visit Reports</h3>
                        @if($isProjectManager && !$isLockedForEditing)
                            <button onclick="openAddInspectionModal()" class="btn-custom" style="background: rgba(6, 182, 212, 0.1); border: 1px solid var(--accent-cyan); color: var(--accent-cyan); cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem;">
                                <i class="bx bx-plus"></i> Add Row
                            </button>
                        @endif
                    </div>

                    <table class="stage-table" style="margin-bottom: 1.5rem;">
                        <thead>
                            <tr>
                                <th style="width: 60px; text-align: center;">S.No</th>
                                <th>Name</th>
                                <th>Designation</th>
                                <th style="width: 140px;">Date</th>
                                <th>Remarks</th>
                                @if($isProjectManager && !$isLockedForEditing)
                                    <th style="text-align: center; width: 100px;">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $inspections = $project->projectInspections()->orderBy('date', 'asc')->get();
                            @endphp
                            @if($inspections->isEmpty())
                                <tr>
                                    <td colspan="{{ ($isProjectManager && !$isLockedForEditing) ? 6 : 5 }}" style="text-align: center; color: var(--text-muted); font-style: italic; padding: 2rem;">
                                        No inspection reports logged yet.
                                    </td>
                                </tr>
                            @else
                                @foreach($inspections as $index => $inspection)
                                    <tr>
                                        <td style="text-align: center; font-weight: 600; color: var(--text-muted); vertical-align: middle;">{{ $loop->iteration }}</td>
                                        <td style="font-weight: 600; color: var(--text-main); vertical-align: middle;">{{ $inspection->name }}</td>
                                        <td style="color: var(--text-main); vertical-align: middle;">{{ $inspection->designation }}</td>
                                        <td style="color: var(--text-main); vertical-align: middle;">{{ \Carbon\Carbon::parse($inspection->date)->format('d-M-Y') }}</td>
                                        <td style="color: var(--text-muted); vertical-align: middle; white-space: pre-line;">{{ $inspection->remarks ?? '-' }}</td>
                                        @if($isProjectManager && !$isLockedForEditing)
                                            <td style="text-align: center; vertical-align: middle;">
                                                <div style="display: inline-flex; gap: 0.4rem;">
                                                    <button onclick="openEditInspectionModal({{ $inspection->id }}, '{{ addslashes($inspection->name) }}', '{{ addslashes($inspection->designation) }}', '{{ $inspection->date }}', '{{ addslashes(str_replace(["\r", "\n"], ['\r', '\n'], $inspection->remarks)) }}')" class="btn-custom" style="background: transparent; color: var(--accent-cyan); border: 1px solid var(--accent-cyan); padding: 0.25rem; font-size: 0.85rem; border-radius: 4px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; margin: 0;" title="Edit">
                                                        <i class="bx bx-pencil"></i>
                                                    </button>
                                                    <form action="{{ route('projects.delete_inspection', [$project->id, $inspection->id]) }}" method="POST" style="display: inline-flex; margin: 0;" onsubmit="return confirm('Are you sure you want to delete this inspection report?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn-danger-custom" style="padding: 0.25rem; font-size: 0.85rem; display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px;" title="Delete">
                                                            <i class="bx bx-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                
            </div>
        </div>

        <!-- ================= STAGE 6 PANEL (COMPLETION) ================= -->
        <div class="stage-content-panel" id="stage-content-6">
            <div class="detail-header-panel">
                <h2>COMPLETION STAGE</h2>
            </div>
            <div style="padding: 1.5rem;">

                @php
                    $docRecord = $project->files_with_timestamps;
                    
                    $compCert = $docRecord ? $docRecord->completion_certificate : null;
                    if ($compCert === '0') { $compCert = null; }
                    $compCertTimeDate = $docRecord ? $docRecord->completion_certificate_ticked_at : null;
                    $compCertTime = $compCertTimeDate ? \Carbon\Carbon::parse($compCertTimeDate)->timezone('Asia/Kolkata')->format('d-M-Y h:i A') : null;

                    $measBook = $docRecord ? $docRecord->measurement_book : null;
                    if ($measBook === '0') { $measBook = null; }
                    $measBookTimeDate = $docRecord ? $docRecord->measurement_book_ticked_at : null;
                    $measBookTime = $measBookTimeDate ? \Carbon\Carbon::parse($measBookTimeDate)->timezone('Asia/Kolkata')->format('d-M-Y h:i A') : null;

                    $locationMapLink = $docRecord ? $docRecord->location_map_link : null;
                    
                    $pFiles = $project->files ?? [];
                    $beforePhotos = array_values(array_unique(array_filter(array_merge((array)($pFiles['photos_before'] ?? []), (array)($pFiles['before_photos'] ?? []), (array)($pFiles['before'] ?? [])))));
                    $startingPhotos = array_values(array_unique(array_filter(array_merge((array)($pFiles['photos_starting'] ?? []), (array)($pFiles['starting_photos'] ?? []), (array)($pFiles['starting'] ?? [])))));
                    $inbetweenPhotos = array_values(array_unique(array_filter(array_merge((array)($pFiles['photos_inbetween'] ?? []), (array)($pFiles['inbetween_photos'] ?? []), (array)($pFiles['inbetween'] ?? [])))));
                    $afterPhotos = array_values(array_unique(array_filter(array_merge((array)($pFiles['photos_after'] ?? ($pFiles['photos'] ?? [])), (array)($pFiles['after_photos'] ?? []), (array)($pFiles['after'] ?? [])))));
                    $bannerPhotos = array_values(array_unique(array_filter(array_merge((array)($pFiles['photos_banner'] ?? []), (array)($pFiles['banner_photos'] ?? []), (array)($pFiles['banner'] ?? [])))));
                    $stonePhotos = array_values(array_unique(array_filter(array_merge((array)($pFiles['photos_stone'] ?? []), (array)($pFiles['stone_photos'] ?? []), (array)($pFiles['stone'] ?? [])))));
                    $inaugurationPhotos = array_values(array_unique(array_filter(array_merge((array)($pFiles['photos_inauguration'] ?? []), (array)($pFiles['inauguration_photos'] ?? []), (array)($pFiles['inauguration'] ?? [])))));
                    $compDetails = $pFiles['completion_details'] ?? [];
                @endphp

                
                <!-- Completion Documents (Stage 6 Upload/Reference) -->
                <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--panel-border); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
                    <h3 style="color: var(--text-main); font-size: 1rem; margin-top: 0; margin-bottom: 1.25rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--panel-border); padding-bottom: 0.75rem;">Completion Documents</h3>

                    @if($project->status === 'Completed')
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <span style="font-weight: 600;">Completion Certificate:</span>
                                @if(!empty($compCert))
                                    <a href="{{ asset($compCert) }}" target="_blank" class="btn-custom" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; background: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent-green); color: var(--accent-green); text-decoration: none;">View Certificate</a>
                                @else
                                    <span style="color: var(--accent-red); font-weight: 600;">Pending</span>
                                @endif
                            </div>

                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <span style="font-weight: 600;">Location Map Link:</span>
                                @if(!empty($locationMapLink))
                                    <a href="{{ $locationMapLink }}" target="_blank" class="btn-custom" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; background: rgba(6, 182, 212, 0.1); border: 1px solid var(--accent-cyan); color: var(--accent-cyan); text-decoration: none;">
                                        <i class="bx bx-map-alt"></i> Open Map
                                    </a>
                                @else
                                    <span style="color: var(--text-muted); font-style: italic;">Not added</span>
                                @endif
                            </div>
                        </div>
                    @else
                        <!-- Completion Certificate row -->
                        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; padding: 0.75rem 0; border-bottom: 1px solid var(--panel-border);">
                            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                <span style="font-weight: 600; color: var(--text-main); min-width: 200px;">Completion Certificate</span>
                                <span style="font-size: 0.75rem; color: var(--text-muted);" id="ticked-at-Completion_Certificate">
                                    @if($compCertTime)
                                        Ticked at: {{ $compCertTime }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                                @if($isProjectManager && !$isLockedForEditing)
                                    <button type="button" onclick="toggleChecklistDocument(this, 'Completion Certificate')" style="background: transparent; border: none; cursor: pointer; padding: 0; outline: none; display: flex; align-items: center; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'">
                                        <i class="bx {{ !empty($compCert) ? 'bxs-checkbox-checked' : 'bx-checkbox' }}" style="font-size: 1.8rem; color: {{ !empty($compCert) ? 'var(--accent-green)' : 'var(--text-muted)' }}; transition: color 0.2s;"></i>
                                    </button>
                                @else
                                    @if(!empty($compCert))
                                        <span style="color: var(--accent-green); font-weight: 600; display: inline-flex; align-items: center; gap: 0.35rem; background: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent-green); padding: 0.3rem 0.65rem; border-radius: 6px; font-size: 0.8rem;">
                                            <i class="bx bx-check-circle" style="font-size: 1rem;"></i> Ticked
                                        </span>
                                    @else
                                        <span style="color: var(--accent-red); font-weight: 500; display: inline-flex; align-items: center; gap: 0.35rem; background: rgba(239, 68, 68, 0.1); border: 1px solid var(--accent-red); padding: 0.3rem 0.65rem; border-radius: 6px; font-size: 0.8rem;">
                                            <i class="bx bx-x-circle" style="font-size: 1rem;"></i> Unticked
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>



                        <!-- Location Map Link row -->
                        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; padding: 0.75rem 0; border-top: 1px solid var(--panel-border);">
                            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                <span style="font-weight: 600; color: var(--text-main); min-width: 200px;">Location Map Link</span>
                                @if(!empty($locationMapLink))
                                    <span style="font-size: 0.75rem; color: var(--text-muted); overflow-wrap: anywhere; word-break: break-all; max-width: 400px; display: inline-block;">Current: <a href="{{ $locationMapLink }}" target="_blank" style="color: var(--accent-cyan); text-decoration: underline;">{{ $locationMapLink }}</a></span>
                                @else
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">Not added yet</span>
                                @endif
                            </div>
                            <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                                @if(!empty($locationMapLink))
                                    <a href="{{ $locationMapLink }}" target="_blank" class="btn-custom" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; background: rgba(6, 182, 212, 0.1); border: 1px solid var(--accent-cyan); color: var(--accent-cyan); cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem; text-decoration: none;">
                                        <i class="bx bx-map-alt"></i> Open Map
                                    </a>
                                    @if($isProjectManager && !$isLockedForEditing)
                                        <form action="{{ route('projects.update_map_link', $project->id) }}" method="POST" style="margin: 0; display: inline-flex;">
                                            @csrf
                                            <input type="hidden" name="location_map_link" value="">
                                            <button type="submit" class="btn-danger-custom" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.25rem;" title="Delete Link">
                                                <i class="bx bx-trash"></i> Delete
                                            </button>
                                        </form>
                                    @endif
                                @endif
                                
                                @if($isProjectManager && !$isLockedForEditing)
                                    <form action="{{ route('projects.update_map_link', $project->id) }}" method="POST" style="margin: 0; display: inline-flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
                                        @csrf
                                        <input type="url" name="location_map_link" placeholder="Paste Google Maps URL here…" required style="background-color: #ffffff !important; color: #000000 !important; border: 1px solid #cccccc; padding: 0.45rem 0.75rem; border-radius: 6px; font-size: 0.8rem; width: 220px; outline: none;" value="{{ $locationMapLink }}">
                                        <button type="submit" class="btn-custom" style="padding: 0.45rem 1rem; font-size: 0.85rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem;">
                                            <i class="bx bx-save"></i> Save Link
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <style>
    .photo-gallery-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
        align-items: stretch;
    }
    .photo-card {
        background: rgba(255, 255, 255, 0.01);
        border: 1px solid var(--panel-border);
        border-radius: 8px;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        height: 100%;
        min-height: 380px;
    }
    .photo-card-header {
        color: var(--text-main);
        font-size: 0.85rem;
        font-weight: 700;
        margin-top: 0;
        margin-bottom: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        border-bottom: 1px solid var(--panel-border);
        padding-bottom: 0.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        min-height: 42px;
    }
    .photo-card-title {
        line-height: 1.2;
        padding-right: 0.5rem;
    }
    .photo-list-container {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        flex-grow: 1;
        max-height: 260px;
        overflow-y: auto;
        padding-right: 0.25rem;
    }
    .photo-empty-state {
        text-align: center;
        color: var(--text-muted);
        font-style: italic;
        padding: 1.5rem 1rem;
        border: 1px dashed rgba(255, 255, 255, 0.05);
        border-radius: 6px;
        font-size: 0.8rem;
        flex-grow: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    @media (max-width: 1400px) {
        .photo-gallery-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    @media (max-width: 992px) {
        .photo-gallery-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 576px) {
        .photo-gallery-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
                <!-- Photo Gallery -->
                <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--panel-border); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
                    <div style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--panel-border); padding-bottom: 0.75rem;">
                        <h3 style="color: var(--text-main); font-size: 1rem; margin: 0; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Project Photos</h3>
                    </div>

                    <div class="photo-gallery-grid">
                        @php
                            $columns = [
                                'before' => ['title' => 'Before Implementation', 'photos' => $beforePhotos],
                                'starting' => ['title' => 'Starting', 'photos' => $startingPhotos],
                                'inbetween' => ['title' => 'In Between Project Implementation', 'photos' => $inbetweenPhotos],
                                'after' => ['title' => 'Final Photo', 'photos' => $afterPhotos],
                                'banner' => ['title' => 'Photo of Banner', 'photos' => $bannerPhotos],
                                'stone' => ['title' => 'Photo of Stone', 'photos' => $stonePhotos],
                                'inauguration' => ['title' => 'Photo of Inauguration', 'photos' => $inaugurationPhotos],
                            ];
                        @endphp

                        @foreach($columns as $key => $colData)
                            <div class="photo-card" data-category="{{ $key }}">
                                <h4 class="photo-card-header">
                                    <span class="photo-card-title">{{ $colData['title'] }}</span>
                                    <span style="font-size: 0.75rem; background: rgba(255,255,255,0.05); padding: 0.15rem 0.4rem; border-radius: 4px; color: var(--text-muted); flex-shrink: 0;">{{ count($colData['photos']) }}</span>
                                </h4>

                                @if($isProjectManager)
                                    <form action="{{ route('projects.upload_photo', $project->id) }}" method="POST" enctype="multipart/form-data" style="margin-bottom: 0.75rem; display: flex; flex-direction: column; gap: 0.4rem;">
                                        @csrf
                                        <input type="hidden" name="category" value="{{ $key }}">
                                        <input type="file" name="photo" accept="image/*" required style="font-size: 0.75rem; color: var(--text-muted); width: 100%;">
                                        <button type="submit" class="btn-custom" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 0.3rem; width: 100%;">
                                            <i class="bx bx-upload"></i> {{ !empty($colData['photos']) ? 'Replace Photo' : 'Upload Photo' }}
                                        </button>
                                    </form>
                                @endif

                                <div class="photo-list-container">
                                    @if(empty($colData['photos']))
                                        <div class="photo-empty-state">
                                            No {{ strtolower($colData['title']) }} photos yet.
                                        </div>
                                    @else
                                        @foreach($colData['photos'] as $idx => $photoPath)
                                            <div style="position: relative; background: var(--bg-color); border: 1px solid var(--panel-border); border-radius: 6px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.3); transition: transform 0.2s ease;">
                                                <a href="{{ asset($photoPath) }}" target="_blank" style="display: block; line-height: 0;">
                                                    <img src="{{ asset($photoPath) }}" style="width: 100%; height: 120px; object-fit: cover; display: block;" alt="{{ $colData['title'] }} photo {{ $idx + 1 }}">
                                                </a>
                                                @if($isProjectManager)
                                                    <form action="{{ route('projects.delete_photo', [$project->id, $idx]) }}?category={{ $key }}" method="POST" style="position: absolute; top: 0.3rem; right: 0.3rem; margin: 0;" onsubmit="return confirm('Delete this photo?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" style="width: 24px; height: 24px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; background: rgba(231,76,60,0.9); border: none; color: #fff; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.5);" title="Delete Photo">
                                                            <i class="bx bx-trash" style="font-size: 0.8rem;"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <div style="padding: 0.3rem 0.5rem; font-size: 0.72rem; color: var(--text-muted);">
                                                    Photo {{ $idx + 1 }}
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Financial & Completion Details -->
                <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--panel-border); padding: 1.5rem; border-radius: 8px;">
                    <h3 style="color: var(--text-main); font-size: 1rem; margin-top: 0; margin-bottom: 1.25rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--panel-border); padding-bottom: 0.75rem;">Financial & Handover Details</h3>

                    @php
                        $pFilesData = $project->files ?? [];
                        $allExpenses = $pFilesData['expenses'] ?? ($project->expenses ?? []);
                        if (!is_array($allExpenses)) { $allExpenses = []; }
                        
                        $s5AllocatedSpent = 0;
                        $s5CommSpent = 0;
                        foreach ($allExpenses as $exp) {
                            if (isset($exp['material_index'])) {
                                $s5AllocatedSpent += floatval($exp['amount'] ?? 0);
                            }
                            if (isset($exp['comm_index'])) {
                                $s5CommSpent += floatval($exp['amount'] ?? 0);
                            }
                        }
                        
                        $finTotalGrands = ($s5AllocatedSpent > 0) ? $s5AllocatedSpent : ($totalAmount ?? $project->available_budget ?? 0);
                        $finCommContrib = ($s5CommSpent > 0) ? $s5CommSpent : ($commTotal ?? 0);
                    @endphp

                    @if($isProjectManager && $project->status !== 'Completed')
                        <form action="{{ route('projects.save_completion_details', $project->id) }}" method="POST" style="margin: 0;">
                            @csrf
                            <input type="hidden" name="total_amount" id="fin_total_amount" value="{{ $finTotalGrands }}">
                            <input type="hidden" name="community_contribution" id="fin_community_contribution" value="{{ $finCommContrib }}">
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">
                                        Total Grands (₹)
                                        <span style="font-size: 0.75rem; color: var(--accent-cyan); margin-left: 0.3rem;">(auto)</span>
                                    </label>
                                    <input type="text" readonly class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: rgba(6,182,212,0.05); color: var(--accent-cyan); cursor: not-allowed; font-weight: 600;" value="{{ number_format($finTotalGrands, 2) }}">
                                </div>
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">
                                        Community Contribution (₹)
                                        <span style="font-size: 0.75rem; color: var(--accent-cyan); margin-left: 0.3rem;">(auto)</span>
                                    </label>
                                    <input type="text" readonly class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: rgba(6,182,212,0.05); color: var(--accent-cyan); cursor: not-allowed; font-weight: 600;" value="{{ number_format($finCommContrib, 2) }}">
                                </div>
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">Leverage (₹)</label>
                                    <input type="number" name="amount_paid_by_donor" id="fin_amount_paid_by_donor" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" value="{{ old('amount_paid_by_donor', $compDetails['amount_paid_by_donor'] ?? 0) }}">
                                </div>
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">Any Other (₹)</label>
                                    <input type="number" name="any_other" id="fin_any_other" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" value="{{ old('any_other', $compDetails['any_other'] ?? 0) }}">
                                </div>
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">Deductions (₹)</label>
                                    <input type="number" name="deductions" id="fin_deductions" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" value="{{ old('deductions', $compDetails['deductions'] ?? 0) }}">
                                </div>
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">
                                        Total Project Cost (₹)
                                        <span style="font-size: 0.75rem; color: #10b981; margin-left: 0.3rem;">(auto)</span>
                                    </label>
                                    <input type="number" name="total_project_cost" id="fin_total_project_cost" readonly class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid rgba(16,185,129,0.4); background-color: rgba(16,185,129,0.05); color: #10b981; cursor: not-allowed; font-weight: 700; font-size: 1rem;" value="{{ old('total_project_cost', $compDetails['total_project_cost'] ?? $project->available_budget) }}">
                                </div>
                            </div>
                            <button type="submit" class="btn-custom" style="padding: 0.5rem 1.5rem; cursor: pointer;">Save Details</button>
                        </form>

                        <script>
                        (function() {
                            function recalcSubFinancialTotal() {
                                var totalAmount = parseFloat(document.getElementById('fin_total_amount')?.value) || 0;
                                var comm = parseFloat(document.getElementById('fin_community_contribution')?.value) || 0;
                                var donor = parseFloat(document.getElementById('fin_amount_paid_by_donor')?.value) || 0;
                                var anyOther = parseFloat(document.getElementById('fin_any_other')?.value) || 0;
                                var deductions = parseFloat(document.getElementById('fin_deductions')?.value) || 0;

                                var total = totalAmount + comm + donor + anyOther - deductions;
                                if (total < 0) total = 0;

                                var elCost = document.getElementById('fin_total_project_cost');
                                if (elCost) {
                                    elCost.value = total.toFixed(2);
                                }
                            }

                            ['fin_total_amount', 'fin_community_contribution', 'fin_amount_paid_by_donor', 'fin_any_other', 'fin_deductions'].forEach(function(id) {
                                var el = document.getElementById(id);
                                if (el) {
                                    el.addEventListener('input', recalcSubFinancialTotal);
                                    el.addEventListener('change', recalcSubFinancialTotal);
                                }
                            });

                            recalcSubFinancialTotal();
                        })();
                        </script>
                    @else
                        <div class="details-grid">
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
                            <div class="details-value" style="font-weight: 700; color: #10b981;">₹{{ number_format(($compDetails['total_amount'] ?? $project->available_budget) + ($compDetails['community_contribution'] ?? 0) + ($compDetails['amount_paid_by_donor'] ?? 0) + ($compDetails['any_other'] ?? 0) - ($compDetails['deductions'] ?? 0), 2) }}</div>

                            <div class="details-label">Completion Status</div><div class="details-colon">:</div>
                            <div class="details-value">
                                @if($project->status === 'Approved')
                                    <span style="background-color: rgba(16,185,129,0.2); color: var(--accent-green); padding: 0.3rem 1rem; border-radius: 4px; font-size: 0.9rem; border: 1px solid rgba(16,185,129,0.3);">APPROVED & HANDED OVER</span>
                                @elseif($project->status === 'Completed')
                                    <span style="background-color: rgba(16,185,129,0.2); color: var(--accent-green); padding: 0.3rem 1rem; border-radius: 4px; font-size: 0.9rem; border: 1px solid rgba(16,185,129,0.3);">COMPLETED</span>
                                @else
                                    <span style="background-color: rgba(245,158,11,0.2); color: #f59e0b; padding: 0.3rem 1rem; border-radius: 4px; font-size: 0.9rem; border: 1px solid rgba(245,158,11,0.3);">PENDING FINAL SIGN-OFF</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

            </div>
                        <!-- Final Approval & Stage Completion Section -->
                <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--panel-border); padding: 1.5rem; border-radius: 8px; margin-top: 2rem;">
                    <h3 style="color: var(--text-main); font-size: 1rem; margin-top: 0; margin-bottom: 1.25rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--panel-border); padding-bottom: 0.75rem;">Final Approval & Stage Completion</h3>

                    @if($project->status === 'Completed')
                        <div style="background-color: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent-green); color: #8cf5c6; padding: 1.5rem; border-radius: 8px; display: flex; flex-direction: column; gap: 0.75rem;">
                            <h4 style="margin: 0; font-size: 1.05rem; font-weight: 700; text-transform: uppercase;">✓ Project Completed & Finalized</h4>
                            @php
                                $cooStatus = $project->projectStatus;
                                $cooApprovedAt = $cooStatus ? $cooStatus->coo_approved_at : null;
                                $cooApprover = $cooStatus && $cooStatus->approver ? $cooStatus->approver->name : 'COO';
                                $cooRemarks = $cooStatus ? $cooStatus->coo_remarks : null;
                                $cooApprovedAtStr = $cooApprovedAt ? \Carbon\Carbon::parse($cooApprovedAt)->timezone('Asia/Kolkata')->format('d-M-Y h:i A') : 'N/A';
                            @endphp
                            <div style="font-size: 0.9rem; color: var(--text-main);">
                                <p style="margin: 0.25rem 0;"><strong>Approved By:</strong> {{ $cooApprover }}</p>
                                <p style="margin: 0.25rem 0;"><strong>Approved At:</strong> {{ $cooApprovedAtStr }}</p>
                                <p style="margin: 0.25rem 0;"><strong>COO Remarks:</strong> {{ $cooRemarks ?: 'No remarks provided.' }}</p>
                            </div>

                            @if($isSuperAdmin)
                                <div style="margin-top: 1rem; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 1rem;">
                                    <form action="{{ route('projects.approve', $project->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="action" value="reopen">
                                        <button type="submit" class="btn-custom" style="background: linear-gradient(135deg, #eb3b5a, #d81b60); border: none; color: white; cursor: pointer; font-weight: 700; padding: 0.5rem 1.5rem;">
                                            Reopen Project
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @elseif($project->stage == 5 || $project->stage == 6)
                        <div style="background: rgba(255,255,255,0.01); border: 1px solid var(--panel-border); padding: 1.25rem; border-radius: 8px;">
                            <h4 style="color: var(--text-main); font-size: 0.95rem; font-weight: 700; margin: 0 0 1rem 0; text-transform: uppercase;">COO Final Approval</h4>
                            @if($isCoo || $isSuperAdmin)
                                <form action="{{ route('projects.approve', $project->id) }}" method="POST" style="margin: 0; display: flex; flex-direction: column; gap: 1rem; align-items: flex-start;">
                                    @csrf
                                    <input type="hidden" name="action" value="finalize_approval">
                                    <div style="width: 100%; max-width: 500px;">
                                        <label for="remarks" style="display: block; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem;">Approval Remarks:</label>
                                        <textarea name="remarks" id="remarks" rows="3" placeholder="Enter final approval remarks…" style="width: 100%; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #ffffff; padding: 0.75rem; border-radius: 6px; font-size: 0.85rem; outline: none; resize: vertical;" required></textarea>
                                    </div>
                                    <button type="submit" class="btn-custom" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; color: #ffffff; cursor: pointer; font-weight: 700; padding: 0.6rem 1.8rem; border-radius: 6px;">
                                        ✓ Finalize Project Approval & Complete
                                    </button>
                                </form>
                            @else
                                <div style="background-color: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); color: #f59e0b; padding: 0.85rem 1.25rem; border-radius: 6px; font-size: 0.9rem; font-weight: 600; display: inline-block;">
                                    <i class="bx bx-time-five"></i> Pending COO Final Approval
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

</div>

    </div>

    <!-- Back Button -->
    <div style="margin-top: 1.5rem;">
        @php
            $categorySlugs = [
                'Education Center' => 'education-center',
                'Cultural Center' => 'cultural-center',
                'Hospital or Clinics' => 'hospital-or-clinics',
                'Shops and Others' => 'shops-and-others',
                'House' => 'house',
                'Drinking Water - Group Level' => 'drinking-water-group-level',
                'Drinking Water - Individual Level' => 'drinking-water-individual-level',
                'Orphan Care' => 'orphan-care',
                'Differently Abled' => 'differently-abled',
                'Family Aid' => 'family-aid',
                'General' => 'general'
            ];
            $categorySlug = $categorySlugs[$project->type_of_project] ?? 'education-center';
        @endphp
        <a href="{{ route('projects.category', $categorySlug) }}" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted);">
            <i class="bx bx-arrow-back"></i> Back to Project List
        </a>
    </div>

                                                                <!-- Pure AJAX Photo Upload & Delete (Real-time DOM Update, No Page Refresh) -->
    <script>
        (function() {
            const csrfToken = "{{ csrf_token() }}";

            function findTargetPhotoCard(category) {
                if (!category) return null;
                const catLower = category.toLowerCase().trim();

                const cardByAttr = document.querySelector(`.photo-card[data-category="${category}"]`);
                if (cardByAttr) return cardByAttr;

                const categoryTitleMap = {
                    'before': 'before',
                    'starting': 'starting',
                    'inbetween': 'in between',
                    'after': 'final',
                    'banner': 'banner',
                    'stone': 'stone',
                    'inauguration': 'inauguration'
                };

                const searchKey = categoryTitleMap[catLower] || catLower;
                const cards = document.querySelectorAll('.photo-card');
                for (let c of cards) {
                    const titleText = c.querySelector('.photo-card-title')?.textContent?.toLowerCase() || '';
                    if (titleText.includes(searchKey) || c.getAttribute('data-category') === category) {
                        return c;
                    }
                }

                const categoryMap = {
                    'before': 0, 'starting': 1, 'inbetween': 2, 'after': 3, 'banner': 4, 'stone': 5, 'inauguration': 6
                };
                const idx = categoryMap[catLower];
                if (idx !== undefined && cards[idx]) return cards[idx];

                return null;
            }

            function renderPhotoInDOM(data) {
                const category = data.category || 'after';
                const photoUrl = data.photo_url || data.path;
                const deleteUrl = data.delete_url || (`{{ url('admin/projects/' . $project->id . '/delete-photo') }}/${data.photo_index ?? data.index}?category=${category}`);
                const totalPhotos = data.total_photos !== undefined ? data.total_photos : ((data.photo_index ?? data.index) + 1);

                const targetCard = findTargetPhotoCard(category);

                if (targetCard) {
                    const badge = targetCard.querySelector('.photo-card-header span:last-child');
                    if (badge) badge.textContent = totalPhotos;

                    const container = targetCard.querySelector('.photo-list-container');
                    if (container) {
                        container.innerHTML = '';
                        const photoDiv = document.createElement('div');
                        photoDiv.style.cssText = 'position: relative; background: var(--bg-color); border: 1px solid var(--panel-border); border-radius: 6px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.3); transition: all 0.3s ease;';
                        
                        photoDiv.innerHTML = `
                            <a href="${photoUrl}" target="_blank" style="display: block; line-height: 0;">
                                <img src="${photoUrl}" style="width: 100%; height: 120px; object-fit: cover; display: block;" alt="Photo ${totalPhotos}">
                            </a>
                            <form action="${deleteUrl}" method="POST" style="position: absolute; top: 0.3rem; right: 0.3rem; margin: 0;">
                                <input type="hidden" name="_token" value="${csrfToken}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" style="width: 24px; height: 24px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; background: rgba(231,76,60,0.9); border: none; color: #fff; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.5);" title="Delete Photo">
                                    <i class="bx bx-trash" style="font-size: 0.8rem;"></i>
                                </button>
                            </form>
                            <div style="padding: 0.3rem 0.5rem; font-size: 0.72rem; color: var(--text-muted);">
                                Photo ${totalPhotos}
                            </div>
                        `;
                        container.appendChild(photoDiv);
                    }
                }
            }

            if (window.__photoSubmitHandler) {
                document.removeEventListener('submit', window.__photoSubmitHandler, true);
            }

            window.__photoSubmitHandler = async function(e) {
                const form = e.target;
                if (!form || form.getAttribute('data-no-ajax') === 'true') return;

                const action = form.action || '';

                // A. AJAX PHOTO UPLOAD (Matches both upload-photo and upload_photo)
                if (action.includes('upload-photo') || action.includes('upload_photo')) {
                    e.preventDefault();
                    if (form.dataset.submitting === 'true') return;
                    form.dataset.submitting = 'true';

                    const submitBtn = form.querySelector('button[type="submit"]');
                    const origBtnText = submitBtn ? submitBtn.innerHTML : '';
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Uploading...';
                    }

                    try {
                        const response = await fetch(action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: new FormData(form)
                        });

                        const data = await response.json();

                        if (data.success) {
                            renderPhotoInDOM(data);
                            const fileInput = form.querySelector('input[type="file"]');
                            if (fileInput) fileInput.value = '';

                            if (typeof showToast === 'function') {
                                showToast(data.message || 'Photo uploaded successfully!', 'success');
                            }
                        } else {
                            if (typeof showToast === 'function') {
                                showToast(data.message || 'Photo upload failed.', 'danger');
                            } else {
                                alert(data.message || 'Photo upload failed.');
                            }
                        }
                    } catch (err) {
                        console.error('AJAX upload photo error:', err);
                        alert('Photo upload failed. Please try again.');
                    } finally {
                        delete form.dataset.submitting;
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = origBtnText;
                        }
                    }
                } else if (action.includes('delete-photo') || action.includes('delete_photo') || action.includes('/photos/')) {
                    e.preventDefault();
                    if (form.dataset.submitting === 'true') return;

                    const doDelete = async () => {
                        form.dataset.submitting = 'true';

                        const photoItem = form.closest('div[style*="position: relative"]');
                        const card = form.closest('.photo-card');

                        try {
                            const response = await fetch(action, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                },
                                body: new FormData(form)
                            });

                            const data = await response.json();

                            if (data.success) {
                                if (photoItem) {
                                    photoItem.style.transition = 'all 0.3s ease';
                                    photoItem.style.opacity = '0';
                                    photoItem.style.transform = 'scale(0.8)';
                                    setTimeout(() => {
                                        photoItem.remove();
                                        if (card) {
                                            const badge = card.querySelector('.photo-card-header span:last-child');
                                            if (badge && data.total_photos !== undefined) badge.textContent = data.total_photos;

                                            const container = card.querySelector('.photo-list-container');
                                            if (container && (data.total_photos === 0 || container.children.length === 0)) {
                                                const cardTitle = card.querySelector('.photo-card-title')?.textContent?.toLowerCase() || '';
                                                container.innerHTML = `<div class="photo-empty-state">No ${cardTitle} photos yet.</div>`;
                                            }
                                        }
                                    }, 300);
                                }
                                if (typeof showToast === 'function') {
                                    showToast(data.message || 'Photo deleted successfully!', 'success');
                                }
                            } else {
                                if (typeof showToast === 'function') {
                                    showToast(data.message || 'Photo delete failed.', 'danger');
                                } else {
                                    alert(data.message || 'Photo delete failed.');
                                }
                            }
                        } catch (err) {
                            console.error('AJAX delete photo error:', err);
                            alert('Photo delete failed. Please try again.');
                        } finally {
                            delete form.dataset.submitting;
                        }
                    };

                    if (typeof showCustomConfirm === 'function') {
                        showCustomConfirm('Delete this photo?', doDelete);
                    } else if (confirm('Delete this photo?')) {
                        doDelete();
                    }
                }
            };

            document.addEventListener('submit', window.__photoSubmitHandler, true);
        })();
    </script>
    <!-- Switch Stage Script -->
    <script>
        @php
            $appsForJson = collect($allApplications ?? []);
            if (!empty($application) && !$appsForJson->contains('id', $application->id)) {
                $appsForJson->push($application);
            }
        @endphp
        var allApplicationsData = @json($appsForJson);

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

        window.toggleChecklistDocument = toggleChecklistDocument;
        window.performToggleChecklistDocument = performToggleChecklistDocument;

        async function saveDocRemark(docName, remarkText, inputEl) {
            const csrfToken = "{{ csrf_token() }}";
            const updateUrl = "{{ route('projects.update_doc_remark', $project->id) }}";

            try {
                const response = await fetch(updateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        document_name: docName,
                        remark: remarkText
                    })
                });
                const data = await response.json();
                if (data.success) {
                    if (inputEl) {
                        inputEl.style.borderColor = '#10b981';
                        setTimeout(() => { inputEl.style.borderColor = '#cccccc'; }, 1500);
                    }
                }
            } catch (e) {
                console.error("Error saving remark:", e);
            }
        }
        window.saveDocRemark = saveDocRemark;

        async function performToggleChecklistDocument(button, docName) {
            button.disabled = true;
            const docKey = docName.replace(/ /g, '_').toLowerCase();
            const remarkInput = document.getElementById('remark-input-' + docKey);
            const remarkVal = remarkInput ? remarkInput.value : null;

            try {
                const response = await fetch("{{ route('projects.toggle_file', $project->id) }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({ 
                        document_name: docName,
                        remark: remarkVal
                    })
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

                    const cellId = 'ticked-at-' + docKey;
                    const cell = document.getElementById(cellId);
                    if (cell) {
                        cell.innerText = data.ticked_at ? data.ticked_at : '-';
                    }

                    const remarkCell = document.getElementById('remark-cell-' + docKey);
                    if (remarkCell) {
                        if (data.ticked) {
                            remarkCell.innerHTML = `<span style="font-size: 0.85rem; color: var(--text-main);">${data.remark || '-'}</span>`;
                        } else {
                            remarkCell.innerHTML = `<input type="text" id="remark-input-${docKey}" value="${data.remark || ''}" placeholder="Add remark..." onchange="saveDocRemark('${docName}', this.value, this)" style="background-color: #ffffff !important; color: #000000 !important; border: 1px solid #cccccc; padding: 0.35rem 0.65rem; border-radius: 4px; font-size: 0.85rem; width: 100%; max-width: 260px; outline: none;">`;
                        }
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
                const resp = await fetch("{{ route('projects.update_phase', $project->id) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
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

            const getVal = (keys) => {
                if (!Array.isArray(keys)) keys = [keys];
                for (let k of keys) {
                    if (meta[k] !== undefined && meta[k] !== null && meta[k] !== '') return meta[k];
                    if (app[k] !== undefined && app[k] !== null && app[k] !== '') return app[k];
                }
                return null;
            };
            const formatVal = (val) => (val !== null && val !== undefined && val !== '') ? val : '<span style="color: var(--text-muted); font-style: italic;">N/A</span>';

            const applicantName = formatVal(app.applicant_name || getVal(['applicant_name', 'name']));
            const age = formatVal(getVal(['age']));
            const gender = formatVal(getVal(['gender', 'sex']));
            const fatherName = formatVal(getVal(['father_name']));
            const motherName = formatVal(getVal(['mother_name']));
            const location = formatVal(getVal(['location', 'place']));
            const village = formatVal(getVal(['village']));
            const post = formatVal(getVal(['post', 'post_office']));
            const panchayath = formatVal(getVal(['panchayath', 'panchayat']));
            const dist = formatVal(getVal(['district']));
            const st = formatVal(getVal(['state']));
            const pinCode = formatVal(getVal(['pin_code', 'pin']));
            const c1 = formatVal(getVal(['contact_number_1', 'mobile_1', 'mobile', 'contact1']));
            const c2 = formatVal(getVal(['contact_number_2', 'mobile_2', 'contact2']));

            const landOwnerName = formatVal(getVal(['land_owner_name']));
            const landOwnerMobile = formatVal(getVal(['land_owner_mobile']));
            const landOwnerPlace = formatVal(getVal(['land_owner_place', 'land_owner_address']));
            const legalPermissions = formatVal(getVal(['legal_permissions']));

            const wellType = formatVal(getVal(['well_type']));
            const wellDepth = formatVal(getVal(['well_depth']));
            const beneficiaries = formatVal(getVal(['num_benefited_people', 'beneficiaries']));
            const budget = app.amount_requested ? '₹' + Number(app.amount_requested).toLocaleString() : 'N/A';
            const appStatus = app.status || 'Pending';

            container.innerHTML = `
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <!-- Col 1 -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">1. Applicant & Contact Info</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px; color: var(--text-muted);">Applicant Name:</td><td style="color: var(--text-main); font-weight: 600;">${applicantName}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Gender:</td><td>${gender}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Age:</td><td>${age} yrs</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Father Name:</td><td>${fatherName}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Mother Name:</td><td>${motherName}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Place:</td><td>${location}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Village:</td><td>${village}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Post:</td><td>${post}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Panchayath:</td><td>${panchayath}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">District:</td><td>${dist}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">State:</td><td>${st}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Pin Code:</td><td>${pinCode}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Contact Number 1:</td><td>${c1}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Contact Number 2:</td><td>${c2}</td></tr>
                        </table>

                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">2. Land Owner Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px; color: var(--text-muted);">Land Owner:</td><td>${landOwnerName}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Owner Mobile:</td><td>${landOwnerMobile}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Place / Address:</td><td>${landOwnerPlace}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Legal Permission:</td><td>${legalPermissions}</td></tr>
                        </table>
                    </div>

                    <!-- Col 2 -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">3. Project Specifications</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px; color: var(--text-muted);">Well Type:</td><td>${wellType}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Well Depth:</td><td>${wellDepth}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Beneficiaries:</td><td>${beneficiaries}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Proposed Budget:</td><td style="color: var(--accent-green); font-weight: 600;">${budget}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Review Status:</td><td style="font-weight: 600; color: var(--text-main);">${appStatus}</td></tr>
                        </table>
                    </div>
                </div>

                <div style="margin-top: 1.5rem; border-top: 1px solid var(--panel-border); padding-top: 1rem;">
                    <h5 style="color: var(--accent-cyan); font-size: 0.85rem; margin-bottom: 0.5rem; text-transform: uppercase; font-weight: 700;">Additional Notes:</h5>
                    <p style="color: var(--text-muted); line-height: 1.5; font-size: 0.85rem; margin: 0; background-color: #121824; padding: 0.75rem; border-radius: 6px; border: 1px solid var(--panel-border); min-height: 50px;">
                        ${formatVal(app.details)}
                    </p>
                </div>
            `;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const selectElem = document.querySelector('select[name="application_id"]:not([disabled])') || document.querySelector('select[name="application_id"]');
            if (selectElem && selectElem.value) {
                updateRealtimeApplicationDetails(selectElem.value);
            }
        });

        // Track the current actual project stage from the database
        var activeProjectId = {{ $project->id }};
        var activeProjectStage = {{ $project->stage }};
        var isProjectApproved = "{{ ($project->status === 'Approved' || $project->status === 'Completed') ? '1' : '0' }}";
        var hasApplication = "{{ empty($project->application_id) ? '0' : '1' }}";
        var projectType = "{{ $project->type_of_project }}";

        function switchStage(stageNum) {
            let isLocked = false;
            const isSixStage = ['Education Center', 'Cultural Center', 'Hospital or Clinics', 'Shops and Others', 'House', 'Drinking Water - Group Level', 'Drinking Water - Individual Level'].includes(projectType);
            if (isSixStage) {
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
            sessionStorage.setItem('current_project_stage_{{ $project->id }}', stageNum);

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

        // Initialize display to show the stage panel
        document.addEventListener('DOMContentLoaded', () => {
            const savedStage = sessionStorage.getItem('current_project_stage_{{ $project->id }}');
            if (savedStage) {
                const stageNum = Number(savedStage);
                let isLocked = false;
                const isSixStage = ['Education Center', 'Cultural Center', 'Hospital or Clinics', 'Shops and Others', 'House', 'Drinking Water - Group Level', 'Drinking Water - Individual Level'].includes(projectType);
                if (isSixStage) {
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
                    switchStage(stageNum);
                } else {
                    switchStage(1);
                }
            } else {
                switchStage(1);
            }
        });

        // Material Management Modal Controls
        function openAddMaterialModal() {
            document.getElementById('addMaterialModal').style.display = 'flex';
        }
        function closeAddMaterialModal() {
            document.getElementById('addMaterialModal').style.display = 'none';
        }
        function openEditMaterialModal(index, name, amount) {
            const form = document.getElementById('editMaterialForm');
            form.setAttribute('action', `/admin/projects/{{ $project->id }}/materials/${index}`);
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
            form.setAttribute('action', `/admin/projects/{{ $project->id }}/community-contributions/${index}`);
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
            form.setAttribute('action', `/admin/projects/{{ $project->id }}/expenses/${index}`);
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
            form.setAttribute('action', `/admin/projects/{{ $project->id }}/expenses/${index}`);
            document.getElementById('editCommExpenseFormIndex').value = commIndex;
            document.getElementById('editCommExpenseName').value = name;
            document.getElementById('editCommExpenseQuantity').value = quantity;
            document.getElementById('editCommExpenseAmount').value = amount;
            document.getElementById('editCommExpenseModal').style.display = 'flex';
        }
        function closeEditCommExpenseModal() {
            document.getElementById('editCommExpenseModal').style.display = 'none';
        }
    </script>

    @if($isProjectManager)
    <!-- Add Material Modal -->
    <div id="addMaterialModal" style="display: none; position: fixed; z-index: 1100; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.6); align-items: center; justify-content: center; padding: 1rem;">
        <div style="background-color: var(--panel-bg); border: 1px solid var(--panel-border); padding: 2rem; border-radius: 12px; width: 100%; max-width: 450px; box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
            <h3 style="color: var(--text-main); margin-top: 0; margin-bottom: 1.5rem; font-size: 1.2rem;">Add New Material / Budget Item</h3>
            <form action="{{ route('projects.add_material', $project->id) }}" method="POST" style="margin: 0;">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Material / Item Name</label>
                    <input type="text" name="material" required class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" placeholder="e.g. cement, bricks">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Amount (₹)</label>
                    <input type="number" name="amount" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" placeholder="e.g. 5000">
                </div>
                <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button type="button" onclick="closeAddMaterialModal()" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); cursor: pointer;">Cancel</button>
                    <button type="submit" class="btn-custom" style="cursor: pointer;">Add Item</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Material Modal -->
    <div id="editMaterialModal" style="display: none; position: fixed; z-index: 1100; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.6); align-items: center; justify-content: center; padding: 1rem;">
        <div style="background-color: var(--panel-bg); border: 1px solid var(--panel-border); padding: 2rem; border-radius: 12px; width: 100%; max-width: 450px; box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
            <h3 style="color: var(--text-main); margin-top: 0; margin-bottom: 1.5rem; font-size: 1.2rem;">Edit Material / Budget Item</h3>
            <form id="editMaterialForm" method="POST" style="margin: 0;">
                @csrf
                @method('PUT')
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Material / Item Name</label>
                    <input type="text" id="editMaterialName" name="material" required class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: var(--text-main);">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Amount (₹)</label>
                    <input type="number" id="editMaterialAmount" name="amount" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: var(--text-main);">
                </div>
                <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button type="button" onclick="closeEditMaterialModal()" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); cursor: pointer;">Cancel</button>
                    <button type="submit" class="btn-custom" style="cursor: pointer;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Expense Modal -->
    <div id="addExpenseModal" style="display: none; position: fixed; z-index: 1100; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.6); align-items: center; justify-content: center; padding: 1rem;">
        <div style="background-color: var(--panel-bg); border: 1px solid var(--panel-border); padding: 2rem; border-radius: 12px; width: 100%; max-width: 450px; box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
            <h3 style="color: var(--text-main); margin-top: 0; margin-bottom: 1.5rem; font-size: 1.2rem;">Add New Expense (<span id="addExpenseModalMaterialName" style="color: var(--accent-cyan);"></span>)</h3>
            <form action="{{ route('projects.add_expense', $project->id) }}" method="POST" style="margin: 0;">
                @csrf
                <input type="hidden" name="material_index" id="addExpenseFormMaterialIndex">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Expense Description / Item</label>
                    <input type="text" name="expense_name" required class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" placeholder="e.g. 50 bags purchased, worker payment">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Quantity</label>
                    <input type="number" name="quantity" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" placeholder="e.g. 50">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Amount (₹)</label>
                    <input type="number" name="amount" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" placeholder="e.g. 4000">
                </div>
                <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button type="button" onclick="closeAddExpenseModal()" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); cursor: pointer;">Cancel</button>
                    <button type="submit" class="btn-custom" style="cursor: pointer;">Add Expense</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Expense Modal -->
    <div id="editExpenseModal" style="display: none; position: fixed; z-index: 1100; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.6); align-items: center; justify-content: center; padding: 1rem;">
        <div style="background-color: var(--panel-bg); border: 1px solid var(--panel-border); padding: 2rem; border-radius: 12px; width: 100%; max-width: 450px; box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
            <h3 style="color: var(--text-main); margin-top: 0; margin-bottom: 1.5rem; font-size: 1.2rem;">Edit Expense</h3>
            <form id="editExpenseForm" method="POST" style="margin: 0;">
                @csrf
                @method('PUT')
                <input type="hidden" name="material_index" id="editExpenseFormMaterialIndex">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Expense Description / Item</label>
                    <input type="text" id="editExpenseName" name="expense_name" required class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: var(--text-main);">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Quantity</label>
                    <input type="number" id="editExpenseQuantity" name="quantity" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: var(--text-main);">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Amount (₹)</label>
                    <input type="number" id="editExpenseAmount" name="amount" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: var(--text-main);">
                </div>
                <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button type="button" onclick="closeEditExpenseModal()" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); cursor: pointer;">Cancel</button>
                    <button type="submit" class="btn-custom" style="cursor: pointer;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Community Contribution Modal -->
    <div id="addCommContribModal" style="display: none; position: fixed; z-index: 1100; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.6); align-items: center; justify-content: center; padding: 1rem;">
        <div style="background-color: var(--panel-bg); border: 1px solid var(--panel-border); padding: 2rem; border-radius: 12px; width: 100%; max-width: 450px; box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
            <h3 style="color: var(--text-main); margin-top: 0; margin-bottom: 1.5rem; font-size: 1.2rem;">Add New Community Contribution Item</h3>
            <form action="{{ route('projects.add_community_contribution', $project->id) }}" method="POST" style="margin: 0;">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Item Name / Description</label>
                    <input type="text" name="item" required class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" placeholder="e.g. Community Contribution, Other, Local Donations">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Amount (₹)</label>
                    <input type="number" name="amount" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" placeholder="e.g. 5000">
                </div>
                <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button type="button" onclick="closeAddCommContribModal()" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); cursor: pointer;">Cancel</button>
                    <button type="submit" class="btn-custom" style="cursor: pointer;">Add Item</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Community Contribution Modal -->
    <div id="editCommContribModal" style="display: none; position: fixed; z-index: 1100; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.6); align-items: center; justify-content: center; padding: 1rem;">
        <div style="background-color: var(--panel-bg); border: 1px solid var(--panel-border); padding: 2rem; border-radius: 12px; width: 100%; max-width: 450px; box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
            <h3 style="color: var(--text-main); margin-top: 0; margin-bottom: 1.5rem; font-size: 1.2rem;">Edit Community Contribution Item</h3>
            <form id="editCommContribForm" method="POST" style="margin: 0;">
                @csrf
                @method('PUT')
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Item Name / Description</label>
                    <input type="text" id="editCommContribName" name="item" required class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: var(--text-main);">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Amount (₹)</label>
                    <input type="number" id="editCommContribAmount" name="amount" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: var(--text-main);">
                </div>
                <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button type="button" onclick="closeEditCommContribModal()" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); cursor: pointer;">Cancel</button>
                    <button type="submit" class="btn-custom" style="cursor: pointer;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Contractor Modal -->
    <div id="addContractorModal" style="display: none; position: fixed; z-index: 1100; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.6); align-items: center; justify-content: center; padding: 1rem;">
        <div style="background-color: var(--panel-bg); border: 1px solid var(--panel-border); padding: 2rem; border-radius: 12px; width: 100%; max-width: 500px; box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
            <h3 style="color: var(--text-main); margin-top: 0; margin-bottom: 1.5rem; font-size: 1.2rem;">Add Contractor</h3>
            <form action="{{ route('projects.add_contractor', $project->id) }}" method="POST" style="margin: 0;">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Select Contractor</label>
                    <select name="contractor_id" id="add_contractor_select" required class="form-select-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" onchange="updateAddContractorDetails()">
                        <option value="">-- Choose Contractor --</option>
                        @foreach($allContractors as $c)
                            <option value="{{ $c->id }}" data-phone="{{ $c->phone }}" data-company="{{ $c->company_name }}" data-address="{{ $c->address }}">{{ $c->name }} ({{ $c->company_name }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Contractor Details Card (Dynamic) -->
                <div id="add_contractor_details_card" style="display: none; background: rgba(255, 255, 255, 0.02); border: 1px solid var(--panel-border); padding: 1rem; border-radius: 6px; margin-bottom: 1rem; font-size: 0.85rem;">
                    <div style="margin-bottom: 0.5rem;"><strong style="color: var(--accent-cyan);">Company:</strong> <span id="add_c_company"></span></div>
                    <div style="margin-bottom: 0.5rem;"><strong style="color: var(--accent-cyan);">Phone:</strong> <span id="add_c_phone"></span></div>
                    <div><strong style="color: var(--accent-cyan);">Address:</strong> <span id="add_c_address"></span></div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Type of Contract</label>
                    <input type="text" name="type_of_contract" required class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" placeholder="e.g. Turnkey, Labour, Material-based">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Purpose of Contract</label>
                    <textarea name="purpose_of_contract" required class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff; min-height: 80px;" placeholder="Describe purpose..."></textarea>
                </div>
                <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button type="button" onclick="closeAddContractorModal()" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); cursor: pointer;">Cancel</button>
                    <button type="submit" class="btn-custom" style="cursor: pointer;">Add Contractor</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Contractor Modal -->
    <div id="editContractorModal" style="display: none; position: fixed; z-index: 1100; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.6); align-items: center; justify-content: center; padding: 1rem;">
        <div style="background-color: var(--panel-bg); border: 1px solid var(--panel-border); padding: 2rem; border-radius: 12px; width: 100%; max-width: 500px; box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
            <h3 style="color: var(--text-main); margin-top: 0; margin-bottom: 1.5rem; font-size: 1.2rem;">Edit Contractor</h3>
            <form id="editContractorForm" method="POST" style="margin: 0;">
                @csrf
                @method('PUT')
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Select Contractor</label>
                    <select name="contractor_id" id="edit_contractor_select" required class="form-select-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" onchange="updateEditContractorDetails()">
                        <option value="">-- Choose Contractor --</option>
                        @foreach($allContractors as $c)
                            <option value="{{ $c->id }}" data-phone="{{ $c->phone }}" data-company="{{ $c->company_name }}" data-address="{{ $c->address }}">{{ $c->name }} ({{ $c->company_name }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Contractor Details Card (Dynamic) -->
                <div id="edit_contractor_details_card" style="display: none; background: rgba(255, 255, 255, 0.02); border: 1px solid var(--panel-border); padding: 1rem; border-radius: 6px; margin-bottom: 1rem; font-size: 0.85rem;">
                    <div style="margin-bottom: 0.5rem;"><strong style="color: var(--accent-cyan);">Company:</strong> <span id="edit_c_company"></span></div>
                    <div style="margin-bottom: 0.5rem;"><strong style="color: var(--accent-cyan);">Phone:</strong> <span id="edit_c_phone"></span></div>
                    <div><strong style="color: var(--accent-cyan);">Address:</strong> <span id="edit_c_address"></span></div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Type of Contract</label>
                    <input type="text" id="edit_contractor_type" name="type_of_contract" required class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: var(--text-main);">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Purpose of Contract</label>
                    <textarea id="edit_contractor_purpose" name="purpose_of_contract" required class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: var(--text-main); min-height: 80px;"></textarea>
                </div>
                <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button type="button" onclick="closeEditContractorModal()" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); cursor: pointer;">Cancel</button>
                    <button type="submit" class="btn-custom" style="cursor: pointer;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddContractorModal() {
            document.getElementById('addContractorModal').style.display = 'flex';
        }
        function closeAddContractorModal() {
            document.getElementById('addContractorModal').style.display = 'none';
        }
        function openEditContractorModal(index, contractor) {
            const form = document.getElementById('editContractorForm');
            form.setAttribute('action', `/admin/projects/{{ $project->id }}/contractors/${index}`);
            
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
</script>

    <!-- Add Comm Expense Modal -->
    <div id="addCommExpenseModal" style="display: none; position: fixed; z-index: 1100; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.6); align-items: center; justify-content: center; padding: 1rem;">
        <div style="background-color: var(--panel-bg); border: 1px solid var(--panel-border); padding: 2rem; border-radius: 12px; width: 100%; max-width: 450px; box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
            <h3 style="color: var(--text-main); margin-top: 0; margin-bottom: 1.5rem; font-size: 1.2rem;">Add Expense for (<span id="addCommExpenseModalName" style="color: var(--accent-cyan);"></span>)</h3>
            <form action="{{ route('projects.add_expense', $project->id) }}" method="POST" style="margin: 0;">
                @csrf
                <input type="hidden" name="comm_index" id="addCommExpenseFormIndex">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Expense Description / Item</label>
                    <input type="text" name="expense_name" required class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" placeholder="e.g. transport cost, helper fees">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Quantity</label>
                    <input type="number" name="quantity" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" placeholder="e.g. 50">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Amount (₹)</label>
                    <input type="number" name="amount" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" placeholder="e.g. 1500">
                </div>
                <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button type="button" onclick="closeAddCommExpenseModal()" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); cursor: pointer;">Cancel</button>
                    <button type="submit" class="btn-custom" style="cursor: pointer;">Add Expense</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Comm Expense Modal -->
    <div id="editCommExpenseModal" style="display: none; position: fixed; z-index: 1100; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.6); align-items: center; justify-content: center; padding: 1rem;">
        <div style="background-color: var(--panel-bg); border: 1px solid var(--panel-border); padding: 2rem; border-radius: 12px; width: 100%; max-width: 450px; box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
            <h3 style="color: var(--text-main); margin-top: 0; margin-bottom: 1.5rem; font-size: 1.2rem;">Edit Contribution Expense</h3>
            <form id="editCommExpenseForm" method="POST" style="margin: 0;">
                @csrf
                @method('PUT')
                <input type="hidden" name="comm_index" id="editCommExpenseFormIndex">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Expense Description / Item</label>
                    <input type="text" id="editCommExpenseName" name="expense_name" required class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: var(--text-main);">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Quantity</label>
                    <input type="number" id="editCommExpenseQuantity" name="quantity" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: var(--text-main);">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Amount (₹)</label>
                    <input type="number" id="editCommExpenseAmount" name="amount" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: var(--text-main);">
                </div>
                <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button type="button" onclick="closeEditCommExpenseModal()" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); cursor: pointer;">Cancel</button>
                    <button type="submit" class="btn-custom" style="cursor: pointer;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Add Inspection Modal -->
    <div id="addInspectionModal" style="display: none; position: fixed; z-index: 1100; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.6); align-items: center; justify-content: center; padding: 1rem;">
        <div style="background-color: var(--panel-bg); border: 1px solid var(--panel-border); padding: 2rem; border-radius: 12px; width: 100%; max-width: 500px; box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
            <h3 style="color: var(--text-main); margin-top: 0; margin-bottom: 1.5rem; font-size: 1.2rem; font-weight: 700; text-transform: uppercase;">Add Inspection Report</h3>
            <form action="{{ route('projects.add_inspection', $project->id) }}" method="POST" style="margin: 0;">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Inspector Name</label>
                    <input type="text" name="name" required class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" placeholder="Enter name...">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Designation</label>
                    <input type="text" name="designation" required class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" placeholder="e.g. Project Manager, Auditor">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Inspection Date</label>
                    <input type="date" name="date" required class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" value="{{ date('Y-m-d') }}">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Remarks</label>
                    <textarea name="remarks" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff; min-height: 80px;" placeholder="Enter inspection remarks..."></textarea>
                </div>
                <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button type="button" onclick="closeAddInspectionModal()" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); cursor: pointer;">Cancel</button>
                    <button type="submit" class="btn-custom" style="cursor: pointer;">Add Inspection</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Inspection Modal -->
    <div id="editInspectionModal" style="display: none; position: fixed; z-index: 1100; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.6); align-items: center; justify-content: center; padding: 1rem;">
        <div style="background-color: var(--panel-bg); border: 1px solid var(--panel-border); padding: 2rem; border-radius: 12px; width: 100%; max-width: 500px; box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
            <h3 style="color: var(--text-main); margin-top: 0; margin-bottom: 1.5rem; font-size: 1.2rem; font-weight: 700; text-transform: uppercase;">Edit Inspection Report</h3>
            <form id="editInspectionForm" method="POST" style="margin: 0;">
                @csrf
                @method('PUT')
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Inspector Name</label>
                    <input type="text" id="edit_inspection_name" name="name" required class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Designation</label>
                    <input type="text" id="edit_inspection_designation" name="designation" required class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Inspection Date</label>
                    <input type="date" id="edit_inspection_date" name="date" required class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Remarks</label>
                    <textarea id="edit_inspection_remarks" name="remarks" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff; min-height: 80px;"></textarea>
                </div>
                <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button type="button" onclick="closeEditInspectionModal()" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); cursor: pointer;">Cancel</button>
                    <button type="submit" class="btn-custom" style="cursor: pointer;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
@endif

<!-- Site Study Modal -->
<div id="siteStudyModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
    <div style="background: var(--bg-card, #ffffff); border-radius: 8px; width: 90%; max-width: 850px; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 10px 25px rgba(0,0,0,0.2); overflow: hidden;">
        <div style="padding: 1rem 1.5rem; background: #0d9488; color: white; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0; font-size: 1.1rem; color: white; display: flex; align-items: center; gap: 0.5rem;"><i class="bx bx-file-find"></i> Site Study Report (1000+ Words Supported)</h4>
            <button type="button" onclick="closeSiteStudyModal()" style="background: transparent; border: none; color: white; font-size: 1.5rem; cursor: pointer; line-height: 1;">&times;</button>
        </div>
        <form id="siteStudyForm" onsubmit="saveSiteStudyReport(event)" style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem; overflow-y: auto; flex: 1;">
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <label style="font-weight: 600; color: var(--text-main); font-size: 0.9rem;">Comprehensive Site Study Report</label>
                    <span id="siteStudyWordCount" style="font-size: 0.8rem; color: #0d9488; font-weight: 600;">Words: 0</span>
                </div>
                <textarea id="siteStudyReportText" 
                          name="report" 
                          rows="14" 
                          oninput="updateSiteStudyWordCount()"
                          placeholder="Type or paste comprehensive site study report here (supports 1000+ words)..." 
                          style="width: 100%; border: 1px solid #cccccc; border-radius: 6px; padding: 0.75rem; font-size: 0.9rem; font-family: inherit; color: #000000 !important; background-color: #ffffff !important; outline: none; line-height: 1.5; resize: vertical;">{{ $siteStudyData->report ?? '' }}</textarea>
            </div>
            <div style="display: flex; gap: 1rem; align-items: center; justify-content: space-between;">
                <div style="flex: 1;">
                    <label style="font-weight: 600; color: var(--text-main); font-size: 0.85rem; display: block; margin-bottom: 0.25rem;">Attach Site Study File (Optional)</label>
                    <input type="file" name="file" id="siteStudyFileInput" style="font-size: 0.85rem;">
                    @if(!empty($siteStudyData->file_path))
                        <div style="font-size: 0.8rem; margin-top: 0.35rem;">
                            <a href="{{ asset($siteStudyData->file_path) }}" target="_blank" style="color: #0d9488; text-decoration: underline;"><i class="bx bx-download"></i> View Attached Site Study File</a>
                        </div>
                    @endif
                </div>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem; border-top: 1px solid #eeeeee; padding-top: 1rem;">
                <button type="button" onclick="closeSiteStudyModal()" style="background: #e5e7eb; color: #374151; border: none; padding: 0.5rem 1.25rem; border-radius: 6px; font-weight: 500; cursor: pointer;">Cancel</button>
                <button type="submit" id="siteStudySubmitBtn" style="background: #0d9488; color: white; border: none; padding: 0.5rem 1.5rem; border-radius: 6px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem;">
                    <i class="bx bx-save"></i> Save Site Study Report
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openSiteStudyModal() {
        const modal = document.getElementById('siteStudyModal');
        if (modal) {
            modal.style.display = 'flex';
            updateSiteStudyWordCount();
        }
    }
    function closeSiteStudyModal() {
        const modal = document.getElementById('siteStudyModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }
    function updateSiteStudyWordCount() {
        const text = document.getElementById('siteStudyReportText')?.value || '';
        const words = text.trim() ? text.trim().split(/\s+/).length : 0;
        const countEl = document.getElementById('siteStudyWordCount');
        if (countEl) {
            countEl.innerText = `Words: ${words}`;
        }
    }
    async function saveSiteStudyReport(e) {
        e.preventDefault();
        const btn = document.getElementById('siteStudySubmitBtn');
        if (btn) btn.disabled = true;
        const formData = new FormData(document.getElementById('siteStudyForm'));
        const csrfToken = "{{ csrf_token() }}";
        
        try {
            const response = await fetch("{{ route('projects.update_site_study', $project->id) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                if (typeof showToast === 'function') {
                    showToast(data.message, 'success');
                } else {
                    alert(data.message);
                }
                closeSiteStudyModal();
                const cell = document.getElementById('ticked-at-Site_study') || document.getElementById('ticked-at-Site_study_report');
                if (cell && data.ticked_at) {
                    cell.innerText = data.ticked_at;
                }
            } else {
                alert(data.message || 'Failed to save site study report.');
            }
        } catch (err) {
            console.error(err);
            alert('Error occurred while saving site study report.');
        } finally {
            if (btn) btn.disabled = false;
        }
    }
                        document.querySelectorAll('button[onclick*="\'' + docName + '\'"]').forEach(btn => {
                            const icon = btn.querySelector('i');
                            if (icon) {
                                if (e.payload.ticked) {
                                    icon.className = 'bx bxs-checkbox-checked';
                                    icon.style.color = 'var(--accent-green)';
                                } else {
                                    icon.className = 'bx bx-checkbox';
                                    icon.style.color = 'var(--text-muted)';
                                }
                            }
                        });
                        if (e.user_id && e.user_id != {{ auth()->id() }}) {
                            if (typeof showToast === 'function') {
                                showToast((e.payload.ticked ? '✔ ' : '✘ ') + docName + ' updated by another user', 'info');
                            }
                        }
                    } else if (e.action !== 'toggle_file') {
                        if (typeof showToast === 'function') {
                            showToast('Page has updates — refresh to see latest changes.', 'info');
                        }
                    }
                });
        }
</script>

@endsection