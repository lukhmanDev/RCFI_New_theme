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

            let isLocked = false;
            if (stageNum <= 3) {
                isLocked = false;
            } else {
                isLocked = (activeProjectStage < 4 && isProjectApproved !== '1');
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
        @for($i = 1; $i <= 5; $i++)
            @php
                $isActive = $project->stage == $i;
                $isCompleted = $project->stage > $i;
                $class = $isActive ? 'active' : ($isCompleted ? 'completed' : '');
                
                if ($i <= 3) {
                    $isLocked = false;
                } else { // stage 4 or 5
                    $isLocked = ($project->stage < 4 && $project->status !== 'Approved' && $project->status !== 'Completed');
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
        $isSixStage = false; // General project uses 5 stages
        $isStage4Approved = ($project->stage >= 4 || in_array($project->status, ['Approved', 'Completed']));
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
        <div class="stage-content-panel" id="stage-content-1" style="display: {{ ($project->stage == 1 || empty($project->stage)) ? 'block' : 'none' }};">
            <div class="detail-header-panel">
                <h2>PROJECT DETAIL</h2>
            </div>
            <div style="padding: 1.5rem;">
                {{-- Stage 1: No approval required for construction or non-construction projects --}}
                @if(!in_array($project->type_of_project, ['Education Center', 'Cultural Center', 'Hospital or Clinics', 'Shops and Others', 'House', 'Drinking Water - Group Level', 'Drinking Water - Individual Level', 'General']))
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
                    <div class="details-label">Available Budget</div><div class="details-colon">:</div><div class="details-value">{!! "&#x20B9;" !!}{{ number_format($project->available_budget, 2) }}</div>
                    <div class="details-label">Type of Project</div><div class="details-colon">:</div><div class="details-value">{{ $project->type_of_project }}</div>
                    <div class="details-label">Theme</div><div class="details-colon">:</div><div class="details-value">{{ $project->theme ?? 'N/A' }}</div>
                    <div class="details-label">Subtheme</div><div class="details-colon">:</div><div class="details-value">{{ $project->subtheme ?? 'N/A' }}</div>
                    <div class="details-label">Activity</div><div class="details-colon">:</div><div class="details-value">{{ $project->activity ?? 'N/A' }}</div>
                    <div class="details-label">Remarks</div><div class="details-colon">:</div><div class="details-value" style="font-weight: normal; color: var(--text-muted);">{{ $project->remarks ?? 'N/A' }}</div>
                    <div class="details-label">Project Status</div><div class="details-colon">:</div><div class="details-value" id="grid-project-status" style="font-weight: 600; color: var(--accent-cyan);">{{ $project->status === 'Completed' ? 'Completed' : ($project->project_phase === 'Other' ? ($project->project_phase_custom ?: 'Other') : $project->project_phase) }}</div>
                </div>

                {{-- ===== PROJECT PHASE / STATUS SELECTOR ===== --}}
                @php
                    $phases = [
                        'Project Assigned',
                        'Site identified',
                        'Documents verified',
                        'Drawing',
                        'Tender',
                        'Agreement',
                        'Foundation',
                        'Column',
                        'Slab',
                        'Mason work',
                        'Plastering',
                        'Flooring, Painting, Joinery and MEP',
                        'Completed',
                        'Inaugurated',
                        'Finance settled and Project phase off',
                        'Other',
                    ];
                    $currentPhase  = $project->project_phase ?? '';
                    $currentCustom = $project->project_phase_custom ?? '';

                    

                @endphp
                <div style="margin-top: 2rem; border-top: 1px solid var(--panel-border); padding-top: 1.5rem;">
                    <h3 style="color: var(--text-main); font-size: 1rem; margin-bottom: 1rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
                        
                        Project Status
                    </h3>

                    @php
                        $statusRecord = $project->projectStatus;
                        $statusUpdatedAt = $statusRecord && $statusRecord->updated_at ? \Carbon\Carbon::parse($statusRecord->updated_at)->timezone('Asia/Kolkata') : null;
                    @endphp

                    {{-- Current phase badge & last updated time --}}
                    <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
                        <!-- <div id="current-phase-badge">
                            @if($currentPhase)
                                <span style="display: inline-flex; align-items: center; gap: 0.4rem; background: rgba(6,182,212,0.12); border: 1px solid var(--accent-cyan); color: var(--accent-cyan); padding: 0.4rem 1rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                                    <i class="bx bx-radio-circle-marked" style="font-size: 1rem;"></i>
                                    {{ $currentPhase === 'Other' ? $currentCustom : $currentPhase }}
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 0.4rem; background: rgba(107,114,128,0.1); border: 1px solid rgba(107,114,128,0.3); color: var(--text-muted); padding: 0.4rem 1rem; border-radius: 20px; font-size: 0.85rem; font-weight: 500;">
                                    <i class="bx bx-minus-circle"></i> Not set
                                </span>
                            @endif
                        </div> -->

                        <div id="status-updated-time-container" style="font-size: 0.85rem; color: var(--text-muted); display: {{ $statusUpdatedAt ? 'inline-flex' : 'none' }}; align-items: center; gap: 0.35rem;">
                            <i class="bx bx-calendar-event" style="font-size: 1rem; color: #10b981;"></i>
                            <span>Last Updated: <strong id="status-updated-at" style="color: var(--text-main);">{{ $statusUpdatedAt ? $statusUpdatedAt->format('d/m/Y h:i A') : '' }}</strong> (<span id="status-updated-human" style="color: #10b981;">{{ $statusUpdatedAt ? $statusUpdatedAt->diffForHumans() : '' }}</span>)</span>
                        </div>
                    </div>

                    @if(strtolower($currentPhase ?? '') === 'completed' || strtolower($project->status ?? '') === 'completed')
                    <div style="display: inline-flex; align-items: center; gap: 0.6rem; background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.3); padding: 0.6rem 1.1rem; border-radius: 8px;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25);"></span>
                        <span style="color: #10b981; font-weight: 800; font-size: 0.92rem; text-transform: uppercase; letter-spacing: 0.03em;">Completed &amp; Handed Over</span>
                        <span style="color: var(--text-muted); font-size: 0.8rem; font-weight: 500; margin-left: 0.25rem;">(Status Locked)</span>
                    </div>
                    @elseif($canEditStatus && $hasApplication)
                    <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: flex-end; max-width: 560px;">
                        <div style="flex: 1; min-width: 220px;">
                            <label style="display: block; color: var(--text-muted); font-size: 0.82rem; margin-bottom: 0.35rem;">Select Phase</label>
                            <select id="project-phase-select" onchange="onPhaseSelectChange()" style="width: 100%; padding: 0.55rem 0.85rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: var(--text-main); font-size: 0.9rem; outline: none; cursor: pointer;">
                                <option value="">â€” Select phase â€”</option>
                                @foreach($phases as $phase)
                                    <option value="{{ $phase }}" {{ $currentPhase === $phase ? 'selected' : '' }}>{{ $phase }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="phase-custom-box" style="flex: 1; min-width: 180px; {{ $currentPhase === 'Other' ? '' : 'display: none;' }}">
                            <label style="display: block; color: var(--text-muted); font-size: 0.82rem; margin-bottom: 0.35rem;">Describe (Other)</label>
                            <input type="text" id="project-phase-custom" placeholder="Enter custom statusâ€¦" maxlength="255"
                                   value="{{ $currentCustom }}"
                                   style="width: 100%; padding: 0.55rem 0.85rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: var(--text-main); font-size: 0.9rem; outline: none; box-sizing: border-box;">
                        </div>
                        <button onclick="saveProjectPhase()" style="padding: 0.55rem 1.25rem; border-radius: 6px; background: linear-gradient(135deg, #10b981, #059669); border: none; color: #ffffff; font-weight: 700; font-size: 0.85rem; cursor: pointer; white-space: nowrap; display: inline-flex; align-items: center; gap: 0.4rem; transition: opacity 0.2s; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);" onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                            <i class="bx bx-save"></i> Save Status
                        </button>
                    </div>
                    @else
                        @if(empty($project->application_id))
                            <div style="background-color: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); color: #f59e0b; padding: 0.85rem 1.25rem; border-radius: 6px; font-size: 0.9rem; font-weight: 600; display: inline-block;">
                                <i class="bx bx-error" style="vertical-align: middle; margin-right: 0.35rem; font-size: 1.1rem;"></i> Project status updates are disabled. Please assign/connect an application in Stage 2 first.
                            </div>
                        @else
                            <p style="color: var(--text-muted); font-size: 0.9rem; font-style: italic;">
                                You are not authorized to edit the project status.
                            </p>
                        @endif
                    @endif
                </div>

                <!-- Connect Application Form -->
                @if($canAssignApplication && !in_array($project->type_of_project, ['Education Center', 'Cultural Center', 'Hospital or Clinics', 'Shops and Others', 'House', 'Drinking Water - Group Level', 'Drinking Water - Individual Level', 'General']))
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
                                $formattedAppId = $app ? 'APLRCFI' . $appYear . $prefix . str_pad($app->id, 5, '0', STR_PAD_LEFT) : 'â€”';
                                $applicantName = $app ? $app->applicant_name : 'â€”';
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
                        <form action="{{ route('projects.assign_application', $project->id) }}" method="POST" style="display: flex; flex-direction: column; gap: 0.85rem; max-width: 600px; background: rgba(255,255,255,0.02); border: 1px solid var(--panel-border); padding: 1.15rem; border-radius: 8px;">
                            @csrf
                            <div>
                                <label style="font-size: 0.78rem; font-weight: 700; color: #94a3b8; display: block; margin-bottom: 0.35rem;">Select Application <span style="color:#ef4444;">*</span></label>
                                <select name="application_id" onchange="updateRealtimeApplicationDetails(this.value)" style="background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #ffffff; padding: 0.55rem 0.85rem; border-radius: 6px; width: 100%; outline: none; font-size: 0.9rem;" required>
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
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem;">
                                <div>
                                    <label style="font-size: 0.78rem; font-weight: 700; color: #94a3b8; display: block; margin-bottom: 0.35rem;">Total Beneficiary Peoples <span style="color:#ef4444;">*</span></label>
                                    <input type="number" min="0" name="total_beneficiary_peoples" value="{{ old('total_beneficiary_peoples', $project->total_beneficiary_peoples ?? $project->num_benefited_people ?? '') }}" placeholder="Enter total beneficiary peoples" style="background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #ffffff; padding: 0.55rem 0.85rem; border-radius: 6px; width: 100%; outline: none; font-size: 0.88rem;" required>
                                </div>
                                <div>
                                    <label style="font-size: 0.78rem; font-weight: 700; color: #94a3b8; display: block; margin-bottom: 0.35rem;">Total Family <span style="color:#ef4444;">*</span></label>
                                    <input type="number" min="0" name="total_family" value="{{ old('total_family', $project->total_family ?? '') }}" placeholder="Enter total family" style="background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #ffffff; padding: 0.55rem 0.85rem; border-radius: 6px; width: 100%; outline: none; font-size: 0.88rem;" required>
                                </div>
                            </div>
                            <button type="submit" class="btn-custom" style="padding: 0.55rem 1.25rem; white-space: nowrap; cursor: pointer; align-self: flex-start; margin-top: 0.25rem;">
                                Assign Application
                            </button>
                        </form>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- ================= STAGE 2 PANEL (APPLICANT DETAIL) ================= -->
        <div class="stage-content-panel" id="stage-content-2">
            @php
                $appYear = ($application && !empty($application->created_at)) ? date('y', strtotime($application->created_at)) : '24';
                $prefixes = [
                    'Education Center' => 'EC', 'Cultural Center' => 'CC', 'Hospital or Clinics' => 'HC',
                    'Shops and Others' => 'SO', 'House' => 'HS',
                    'Drinking Water - Group Level' => 'DWG', 'Drinking Water - Individual Level' => 'DWI',
                    'Orphan Care' => 'OC', 'Differently Abled' => 'DA', 'Family Aid' => 'FA', 'General' => 'GN'
                ];
                $prefix = $prefixes[$project->type_of_project] ?? 'APP';
                $appId = $application ? ('APLRCFI' . $appYear . $prefix . str_pad($application->id, 5, '0', STR_PAD_LEFT)) : 'N/A';
            @endphp
            <div class="detail-header-panel" style="display: flex; justify-content: space-between; align-items: center;">
                <h2>APPLICANT DETAIL</h2>
                @if($application)
                <span style="font-size: 0.8rem; font-weight: 700; color: #10b981; background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3); padding: 0.3rem 0.85rem; border-radius: 20px; letter-spacing: 0.04em; white-space: nowrap;">
                    App ID: {{ $appId }}
                </span>
                @endif
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
                @if($canAssignApplication && in_array($project->type_of_project, ['Education Center', 'Cultural Center', 'Hospital or Clinics', 'Shops and Others', 'House', 'Drinking Water - Group Level', 'Drinking Water - Individual Level', 'General']) && $project->status !== 'Completed')
                <div style="margin-bottom: 2rem; border-bottom: 1px solid var(--panel-border); padding-bottom: 1.5rem;">
                    <h3 style="color: var(--text-main); font-size: 1.1rem; margin-bottom: 1rem;">Connect Application</h3>
                    @php
                        // PMs and authorized users can assign or change application when project is active
                        $userCanChange = $isProjectManager && $project->status !== 'Completed';
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
                                $formattedAppId = $app ? 'APLRCFI' . $appYear . $prefix . str_pad($app->id, 5, '0', STR_PAD_LEFT) : 'â€”';
                                $applicantName = $app ? $app->applicant_name : 'â€”';
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
                        <form action="{{ route('projects.assign_application', $project->id) }}" method="POST" style="display: flex; flex-direction: column; gap: 0.85rem; max-width: 600px; background: rgba(255,255,255,0.02); border: 1px solid var(--panel-border); padding: 1.15rem; border-radius: 8px;">
                            @csrf
                            <div>
                                <label style="font-size: 0.78rem; font-weight: 700; color: #94a3b8; display: block; margin-bottom: 0.35rem;">Select Application <span style="color:#ef4444;">*</span></label>
                                <select name="application_id" onchange="updateRealtimeApplicationDetails(this.value)" style="background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #ffffff; padding: 0.55rem 0.85rem; border-radius: 6px; width: 100%; outline: none; font-size: 0.9rem;" required>
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
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem;">
                                <div>
                                    <label style="font-size: 0.78rem; font-weight: 700; color: #94a3b8; display: block; margin-bottom: 0.35rem;">Total Beneficiary Peoples <span style="color:#ef4444;">*</span></label>
                                    <input type="number" min="0" name="total_beneficiary_peoples" value="{{ old('total_beneficiary_peoples', $project->total_beneficiary_peoples ?? $project->num_benefited_people ?? '') }}" placeholder="Enter total beneficiary peoples" style="background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #ffffff; padding: 0.55rem 0.85rem; border-radius: 6px; width: 100%; outline: none; font-size: 0.88rem;" required>
                                </div>
                                <div>
                                    <label style="font-size: 0.78rem; font-weight: 700; color: #94a3b8; display: block; margin-bottom: 0.35rem;">Total Family <span style="color:#ef4444;">*</span></label>
                                    <input type="number" min="0" name="total_family" value="{{ old('total_family', $project->total_family ?? '') }}" placeholder="Enter total family" style="background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #ffffff; padding: 0.55rem 0.85rem; border-radius: 6px; width: 100%; outline: none; font-size: 0.88rem;" required>
                                </div>
                            </div>
                            <button type="submit" class="btn-custom" style="padding: 0.55rem 1.25rem; white-space: nowrap; cursor: pointer; align-self: flex-start; margin-top: 0.25rem;">
                                {{ !empty($project->application_id) ? 'Change & Save Details' : 'Assign Application' }}
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
                                <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">1. Applicant & Committee</h4>
                                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px; color: var(--text-muted);">Applicant Name:</td><td style="color: var(--text-main); font-weight: 600;">{!! $formatVal($application->applicant_name) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Committee Name:</td><td>{!! $formatVal($metaData['committee_name'] ?? $metaData['mahallu_name'] ?? $metaData['place'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Reg. Number:</td><td>{!! $formatVal($metaData['reg_number'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Year:</td><td>{!! $formatVal($metaData['year'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Place:</td><td>{!! $formatVal($metaData['location'] ?? $metaData['place'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Village:</td><td>{!! $formatVal($metaData['village'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Post:</td><td>{!! $formatVal($metaData['post'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Panchayath:</td><td>{!! $formatVal($metaData['panchayath'] ?? $metaData['panchayat'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">District:</td><td>{!! $formatVal($metaData['locality_district'] ?? ($metaData['district'] ?? null)) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">State:</td><td>{!! $formatVal($metaData['locality_state'] ?? ($metaData['state'] ?? null)) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Contact Number 1:</td><td>{!! $formatVal($metaData['contact_number_1'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Contact Number 2:</td><td>{!! $formatVal($metaData['contact_number_2'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Submitted Before?</td><td>{!! $formatVal($metaData['submitted_before'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">RCFI Support?</td><td>{!! $formatVal($metaData['received_support_before'] ?? null) !!}</td></tr>
                                </table>

                                <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">2. Mahallu Locality Details</h4>
                                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px; color: var(--text-muted);">Mahallu Name:</td><td>{!! $formatVal($metaData['mahallu_name'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Place:</td><td>{!! $formatVal($metaData['locality_place'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Village:</td><td>{!! $formatVal($metaData['locality_village'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">District:</td><td>{!! $formatVal($metaData['locality_district'] ?? ($metaData['district'] ?? null)) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">State:</td><td>{!! $formatVal($metaData['locality_state'] ?? ($metaData['state'] ?? null)) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Families Count:</td><td>{!! $formatVal($metaData['families_in_mahallu'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Requirement:</td><td>{!! $formatVal($metaData['requirement'] ?? null) !!}</td></tr>
                                </table>
                            </div>

                            <!-- Col 2 -->
                            <div>
                                <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">3. Current Status & Students</h4>
                                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px; color: var(--text-muted);">Has Building?</td><td>{!! $formatVal($metaData['site_has_building'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Building Status:</td><td>{!! $formatVal($metaData['status_of_current_building'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Boys Count:</td><td>{!! $formatVal($metaData['students_boys'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Girls Count:</td><td>{!! $formatVal($metaData['students_girls'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Center Nearby?</td><td>{!! $formatVal($metaData['education_center_nearby'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Distance to CC (KM):</td><td>{!! $formatVal($metaData['distance_cultural_centre'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Syllabus:</td><td>{!! $formatVal($metaData['syllabus'] ?? null) !!}</td></tr>
                                </table>

                                <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">4. Proposed Project Details</h4>
                                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px; color: var(--text-muted);">Project Type:</td><td style="text-transform: capitalize; font-weight: 600; color: var(--text-main);">{!! $formatVal($metaData['project_type'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Building Area (Sq):</td><td>{!! $formatVal($metaData['building_area_sq'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Land Area (Sq):</td><td>{!! $formatVal($metaData['land_area_sq'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Classrooms Count:</td><td>{!! $formatVal($metaData['num_classrooms'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Proposed Students:</td><td>{!! $formatVal($metaData['num_students'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Proposed Budget:</td><td style="color: var(--accent-green); font-weight: 600;">{{ $application->amount_requested ? '&#x20B9;' . number_format($application->amount_requested) : 'N/A' }}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Legal Approvals:</td><td>{!! $formatVal($metaData['legal_approvals_status'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Permitted Area:</td><td>{!! $formatVal($metaData['area'] ?? null) !!}</td></tr>
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

        <!-- ================= STAGE 3 PANEL (PROJECT APPROVAL & FUNDS ALLOCATED) ================= -->
        <div class="stage-content-panel" id="stage-content-3" style="display: {{ $project->stage == 3 ? 'block' : 'none' }};">
            <div class="detail-header-panel">
                <h2>PROJECT APPROVAL & FUNDS ALLOCATED</h2>
            </div>
            <div style="padding: 1.5rem;">
                @if(empty($project->application_id))
                    <div style="background-color: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); color: #f59e0b; padding: 0.85rem 1.25rem; border-radius: 6px; font-size: 0.9rem; font-weight: 600; margin-bottom: 1.5rem;">
                        <i class="bx bx-error" style="vertical-align: middle; margin-right: 0.35rem; font-size: 1.1rem;"></i> Approval actions are disabled. Please assign/connect an application in Stage 2 first.
                    </div>
                @endif

                @if($project->stage <= 3 && $project->status !== 'Approved' && $project->status !== 'Completed')
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
                                        <i class="bx bx-check-circle"></i> Approve Project &amp; Proceed to Stage 4
                                    </button>
                                </form>

                                <!-- Reject Form -->
                                <form action="{{ route('projects.approve', $project->id) }}" method="POST" style="display: flex; gap: 0.75rem; flex-grow: 1; align-items: center; margin: 0;">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <input type="text" name="remarks" placeholder="Provide rejection reason (optional)â€¦" style="background-color: var(--bg-color); border: 1px solid var(--panel-border); color: var(--text-main); padding: 0.5rem; border-radius: 6px; flex-grow: 1; font-size: 0.85rem; outline: none;">
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
                                    <button type="submit" class="btn-custom" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; color: #ffffff; font-weight: 700; padding: 0.6rem 1.8rem; cursor: pointer; border-radius: 6px;">
                                        <i class="bx bx-send"></i> Submit for HOD/COO Approval
                                    </button>
                                </form>
                            @elseif($project->status === 'Pending Approval')
                                <div style="background-color: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #8cf5c6; padding: 0.85rem 1.25rem; border-radius: 6px; font-size: 0.9rem; font-weight: 600; display: inline-block;">
                                    <i class="bx bx-check-circle"></i> Submitted â€” awaiting HOD/COO Approval.
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

                    // Expenses calculation variables
                    $stage5Materials = $materials;
                    $totalAllocatedAmount = $totalAmount;

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
                    
                    $stage5Circumference = 314.16;
                    $stage5SpentDashoffset = $stage5Circumference - ($stage5Circumference * ($stage5SpentPercentage / 100));

                    $stage5CommContribs = $commContribs;
                    $stage5CommTotal = $commTotal;

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

                <!-- Real-time Budget Metrics Bar -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                    <!-- Project Budget Card -->
                    <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(16, 185, 129, 0.2); padding: 1.25rem; border-radius: 8px; border-left: 4px solid #10b981;">
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Project Budget</div>
                        <div style="font-size: 1.3rem; font-weight: 700; color: var(--text-main);">{!! "&#x20B9;" !!}{{ number_format($project->available_budget, 2) }}</div>
                    </div>

                    <!-- Total Allocated Card -->
                    <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(6, 182, 212, 0.2); padding: 1.25rem; border-radius: 8px; border-left: 4px solid var(--accent-cyan);">
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Total Allocated</div>
                        <div style="font-size: 1.3rem; font-weight: 700; color: var(--accent-cyan);">{!! "&#x20B9;" !!}{{ number_format($totalAmount, 2) }}</div>
                    </div>

                    <!-- Total Card (Allocated + Community Contribution) -->
                    <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(129, 140, 248, 0.2); padding: 1.25rem; border-radius: 8px; border-left: 4px solid #818cf8;">
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Total</div>
                        <div style="font-size: 1.3rem; font-weight: 700; color: #818cf8;">{!! "&#x20B9;" !!}{{ number_format($grandTotal, 2) }}</div>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                    @if($isProjectManager && !$isLockedForEditing)
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
                                        <span>&#x20B9; {{ number_format($item['amount'], 2) }}</span>
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
                            <td style="text-align: right; font-weight: 700; color: var(--accent-cyan);">&#x20B9; {{ number_format($totalAmount, 2) }}</td>
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
                    @if($isProjectManager && !$isLockedForEditing)
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
                                        <span>&#x20B9; {{ number_format($item['amount'], 2) }}</span>
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
                            <td style="text-align: right; font-weight: 700; color: var(--accent-cyan);">&#x20B9; {{ number_format($commTotal, 2) }}</td>
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
                    @if($isProjectManager && !$isLockedForEditing)
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
                                        <button type="button" onclick="openEditContractorModal({{ $index }})" class="btn-custom" style="background: transparent; color: var(--accent-cyan); border: 1px solid var(--accent-cyan); padding: 0.25rem; font-size: 0.85rem; border-radius: 4px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; margin: 0;" title="Edit">
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
                @endif
            </div>
        </div>

        <!-- ================= STAGE 4 PANEL (ALLOCATED ITEMS & EXPENSES) ================= -->
        <div class="stage-content-panel" id="stage-content-4" style="display: {{ $project->stage == 4 ? 'block' : 'none' }};">
            <div class="detail-header-panel">
                <h2>ALLOCATED ITEMS & EXPENSES</h2>
            </div>
            <div style="padding: 1.5rem;">
                @if(empty($project->application_id))
                    <div style="background-color: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); color: #f59e0b; padding: 0.85rem 1.25rem; border-radius: 6px; font-size: 0.9rem; font-weight: 600; margin-bottom: 1.5rem;">
                        <i class="bx bx-error" style="vertical-align: middle; margin-right: 0.35rem; font-size: 1.1rem;"></i> Expense management is disabled. Please assign/connect an application in Stage 2 first.
                    </div>
                @endif




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
                                    <span style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);">{!! "&#x20B9;" !!}{{ number_format($stage5TotalBudget, 2) }}</span>
                                </div>
                                <!-- Balance Card -->
                                <div style="background: rgba(255,255,255,0.01); border: 1px solid rgba(6, 182, 212, 0.2); padding: 0.5rem 0.75rem; border-radius: 6px;">
                                    <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.15rem;">
                                        <span style="display: inline-block; width: 6px; height: 6px; background-color: var(--accent-cyan); border-radius: 50%;"></span>
                                        <span style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600;">Total Balance</span>
                                    </div>
                                    <span style="font-size: 0.95rem; font-weight: 700; color: var(--accent-cyan);">{!! "&#x20B9;" !!}{{ number_format($stage5BalanceAmount, 2) }}</span>
                                    <div style="font-size: 0.65rem; color: var(--text-muted); margin-top: 0.1rem;">{{ number_format($stage5BalancePercentage, 1) }}% left</div>
                                </div>
                                <!-- Expense Card -->
                                <div style="background: rgba(255,255,255,0.01); border: 1px solid rgba(239, 68, 68, 0.2); padding: 0.5rem 0.75rem; border-radius: 6px;">
                                    <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.15rem;">
                                        <span style="display: inline-block; width: 6px; height: 6px; background-color: var(--accent-red); border-radius: 50%;"></span>
                                        <span style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600;">Total Expenses</span>
                                    </div>
                                    <span style="font-size: 0.95rem; font-weight: 700; color: var(--accent-red);">{!! "&#x20B9;" !!}{{ number_format($stage5SpentAmount, 2) }}</span>
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
                                    <span style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);">{!! "&#x20B9;" !!}{{ number_format($stage5CommTotal, 2) }}</span>
                                </div>
                                <!-- Balance Card -->
                                <div style="background: rgba(255,255,255,0.01); border: 1px solid rgba(6, 182, 212, 0.2); padding: 0.5rem 0.75rem; border-radius: 6px;">
                                    <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.15rem;">
                                        <span style="display: inline-block; width: 6px; height: 6px; background-color: var(--accent-cyan); border-radius: 50%;"></span>
                                        <span style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600;">Total Balance</span>
                                    </div>
                                    <span style="font-size: 0.95rem; font-weight: 700; color: var(--accent-cyan);">{!! "&#x20B9;" !!}{{ number_format($stage5CommBalance, 2) }}</span>
                                    <div style="font-size: 0.65rem; color: var(--text-muted); margin-top: 0.1rem;">{{ number_format($stage5CommBalancePercentage, 1) }}% left</div>
                                </div>
                                <!-- Expense Card -->
                                <div style="background: rgba(255,255,255,0.01); border: 1px solid rgba(239, 68, 68, 0.2); padding: 0.5rem 0.75rem; border-radius: 6px;">
                                    <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.15rem;">
                                        <span style="display: inline-block; width: 6px; height: 6px; background-color: var(--accent-red); border-radius: 50%;"></span>
                                        <span style="font-size: 0.7rem; color: var(--text-muted); font-weight: 600;">Total Expenses</span>
                                    </div>
                                    <span style="font-size: 0.95rem; font-weight: 700; color: var(--accent-red);">{!! "&#x20B9;" !!}{{ number_format($stage5CommSpent, 2) }}</span>
                                    <div style="font-size: 0.65rem; color: var(--text-muted); margin-top: 0.1rem;">{{ number_format($stage5CommSpentPercentage, 1) }}% spent</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <!-- Expenses Section -->
                @if(in_array($project->type_of_project, ['Education Center', 'Cultural Center', 'Hospital or Clinics', 'Shops and Others', 'House', 'Drinking Water - Group Level', 'Drinking Water - Individual Level', 'General']))
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
                                    <td style="text-align: right; font-weight: 600; color: var(--text-main); vertical-align: middle;">{!! "&#x20B9;" !!}{{ number_format($material['amount'], 2) }}</td>
                                    <td style="text-align: right; font-weight: 600; color: var(--accent-red); vertical-align: middle;">{!! "&#x20B9;" !!}{{ number_format($itemTotalSpent, 2) }}</td>
                                    <td style="text-align: right; font-weight: 600; color: {{ $itemBalance >= 0 ? 'var(--accent-cyan)' : 'var(--accent-red)' }}; vertical-align: middle;">
                                        {!! "&#x20B9;" !!}{{ number_format($itemBalance, 2) }}
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
                                            <td style="text-align: right; color: var(--text-muted); font-size: 0.85rem; vertical-align: middle;">{!! "&#x20B9;" !!}{{ number_format($expense['amount'], 2) }}</td>
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
                                    <td style="text-align: right; font-weight: 600; color: var(--text-main); vertical-align: middle;">{!! "&#x20B9;" !!}{{ number_format($comm['amount'], 2) }}</td>
                                    <td style="text-align: right; font-weight: 600; color: var(--accent-red); vertical-align: middle;">{!! "&#x20B9;" !!}{{ number_format($itemTotalCommSpent, 2) }}</td>
                                    <td style="text-align: right; font-weight: 600; color: {{ $itemCommBalance >= 0 ? 'var(--accent-cyan)' : 'var(--accent-red)' }}; vertical-align: middle;">
                                        {!! "&#x20B9;" !!}{{ number_format($itemCommBalance, 2) }}
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
                                            <td style="text-align: right; color: var(--text-muted); font-size: 0.85rem; vertical-align: middle;">{!! "&#x20B9;" !!}{{ number_format($expense['amount'], 2) }}</td>
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
                                        <td style="color: var(--text-main); vertical-align: middle;">{{ \Carbon\Carbon::parse($inspection->date)->format('d/m/Y') }}</td>
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

                
                @if(($project->stage == 4 || $project->status === 'Approved') && $project->stage < 5)
                    <div style="margin-top: 2rem; border-top: 1px solid var(--panel-border); padding-top: 1.5rem;">
                        <form action="{{ route('projects.approve', $project->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="action" value="promote_to_stage6">
                            <button type="submit" class="btn-custom" style="background: linear-gradient(135deg, #10b981, #059669); border: none; color: #ffffff; font-weight: 700; padding: 0.6rem 1.8rem; cursor: pointer; border-radius: 6px; display: inline-flex; align-items: center; gap: 0.4rem;">
                                <i class="bx bx-right-arrow-alt"></i> Proceed to Stage 5 (Completion Stage)
                            </button>
                        </form>
                    </div>
                @endif

                
            </div>
        </div>

        <!-- ================= STAGE 5 PANEL (COMPLETION) ================= -->
        <div class="stage-content-panel" id="stage-content-5" style="display: {{ $project->stage >= 5 ? 'block' : 'none' }};">
            <div class="detail-header-panel">
                <h2>COMPLETION STAGE</h2>
            </div>
            <div style="padding: 1.5rem;">

                @php
                    $docRecord = $project->files_with_timestamps;
                    
                    $compCert = $docRecord ? $docRecord->completion_certificate : null;
                    if ($compCert === '0') { $compCert = null; }
                    $compCertTimeDate = $docRecord ? $docRecord->completion_certificate_ticked_at : null;
                    $compCertTime = $compCertTimeDate ? \Carbon\Carbon::parse($compCertTimeDate)->timezone('Asia/Kolkata')->format('d/m/Y h:i A') : null;

                    $measBook = $docRecord ? $docRecord->measurement_book : null;
                    if ($measBook === '0') { $measBook = null; }
                    $measBookTimeDate = $docRecord ? $docRecord->measurement_book_ticked_at : null;
                    $measBookTime = $measBookTimeDate ? \Carbon\Carbon::parse($measBookTimeDate)->timezone('Asia/Kolkata')->format('d/m/Y h:i A') : null;

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

                
                <!-- Completion Location -->
                <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--panel-border); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
                    <h3 style="color: var(--text-main); font-size: 1rem; margin-top: 0; margin-bottom: 1.25rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--panel-border); padding-bottom: 0.75rem;"> Location Details</h3>

                    <!-- Location Map Link row -->
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; padding: 0.75rem 0;">
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
                                    <input type="url" name="location_map_link" placeholder="Paste Google Maps URL hereâ€¦" required style="background-color: #ffffff !important; color: #000000 !important; border: 1px solid #cccccc; padding: 0.45rem 0.75rem; border-radius: 6px; font-size: 0.8rem; width: 220px; outline: none;" value="{{ $locationMapLink }}">
                                    <button type="submit" class="btn-custom" style="padding: 0.45rem 1rem; font-size: 0.85rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem;">
                                        <i class="bx bx-save"></i> Save Link
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <style>
    .photo-gallery-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
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
        max-height: 600px;
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
                                'before' => ['title' => 'Photo 1', 'photos' => $beforePhotos],
                                'inbetween' => ['title' => 'Photo 2', 'photos' => $inbetweenPhotos],
                                'after' => ['title' => 'Photo 3', 'photos' => $afterPhotos],
                            ];
                        @endphp

                        @foreach($columns as $key => $colData)
                            <div class="photo-card" data-category="{{ $key }}">
                                <h4 class="photo-card-header">
                                    <span class="photo-card-title">{{ $colData['title'] }}</span>
                                    <span style="font-size: 0.75rem; background: rgba(255,255,255,0.05); padding: 0.15rem 0.4rem; border-radius: 4px; color: var(--text-muted); flex-shrink: 0;">{{ count($colData['photos']) }} / 3</span>
                                </h4>

                                @if($isProjectManager)
                                    @if(count($colData['photos']) < 3)
                                        <form action="{{ route('projects.upload_photo', $project->id) }}" method="POST" enctype="multipart/form-data" style="margin-bottom: 0.75rem; display: flex; flex-direction: column; gap: 0.4rem;">
                                            @csrf
                                            <input type="hidden" name="category" value="{{ $key }}">
                                            <input type="file" name="photos[]" multiple accept="image/*" required style="font-size: 0.75rem; color: var(--text-muted); width: 100%;">
                                            <button type="submit" class="btn-custom" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 0.3rem; width: 100%;">
                                                <i class="bx bx-upload"></i> Upload Photos (Max 3)
                                            </button>
                                        </form>
                                    @else
                                        <div style="font-size: 0.75rem; color: #f59e0b; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); padding: 0.35rem 0.5rem; border-radius: 6px; text-align: center; margin-bottom: 0.75rem; font-weight: 600;">
                                            Maximum 3 photos reached.
                                        </div>
                                    @endif
                                @endif

                                <div class="photo-list-container">
                                    @if(empty($colData['photos']))
                                        <div class="photo-empty-state">
                                            No {{ strtolower($colData['title']) }} uploaded yet.
                                        </div>
                                    @else
                                        @foreach($colData['photos'] as $idx => $photoPath)
                                            <div style="position: relative; background: var(--bg-color); border: 1px solid var(--panel-border); border-radius: 6px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.3); transition: transform 0.2s ease;">
                                                <a href="{{ asset($photoPath) }}" target="_blank" style="display: block; line-height: 0;">
                                                    <img src="{{ asset($photoPath) }}" style="width: 100%; max-height: 280px; object-fit: contain; background: rgba(0, 0, 0, 0.3); display: block;" alt="{{ $colData['title'] }} photo {{ $idx + 1 }}">
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


                <!-- Financial & Handover Details -->
                <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--panel-border); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; margin-top: 2.5rem;">
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
                        $compDetails = $pFilesData['completion_details'] ?? [];
                    @endphp

                    @if($isProjectManager && $project->status !== 'Completed')
                        <form action="{{ route('projects.save_completion_details', $project->id) }}" method="POST" style="margin: 0;">
                            @csrf
                            <input type="hidden" name="total_amount" id="fin_total_amount" value="{{ $displayGrants }}">
                            <input type="hidden" name="community_contribution" id="fin_community_contribution" value="{{ $displayComm }}">
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">
                                        Total Grants (&#x20B9;)
                                        <span style="font-size: 0.75rem; color: var(--accent-cyan); margin-left: 0.3rem;">(auto)</span>
                                    </label>
                                    <input type="text" readonly class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: rgba(6,182,212,0.05); color: var(--accent-cyan); cursor: not-allowed; font-weight: 600;" value="{{ number_format($displayGrants, 2) }}">
                                </div>
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">
                                        Community Contribution (&#x20B9;)
                                        <span style="font-size: 0.75rem; color: var(--accent-cyan); margin-left: 0.3rem;">(auto)</span>
                                    </label>
                                    <input type="text" readonly class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: rgba(6,182,212,0.05); color: var(--accent-cyan); cursor: not-allowed; font-weight: 600;" value="{{ number_format($displayComm, 2) }}">
                                </div>
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">Leverage (&#x20B9;)</label>
                                    <input type="number" name="amount_paid_by_donor" id="fin_amount_paid_by_donor" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" value="{{ old('amount_paid_by_donor', $compDetails['amount_paid_by_donor'] ?? 0) }}">
                                </div>
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">Any Other (&#x20B9;)</label>
                                    <input type="number" name="any_other" id="fin_any_other" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" value="{{ old('any_other', $compDetails['any_other'] ?? 0) }}">
                                </div>
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">Deductions (&#x20B9;)</label>
                                    <input type="number" name="deductions" id="fin_deductions" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" value="{{ old('deductions', $compDetails['deductions'] ?? 0) }}">
                                </div>
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">
                                        Total Project Cost (&#x20B9;)
                                        <span style="font-size: 0.75rem; color: #10b981; margin-left: 0.3rem;">(auto)</span>
                                    </label>
                                    <input type="number" name="total_project_cost" id="fin_total_project_cost" readonly class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid rgba(16,185,129,0.4); background-color: rgba(16,185,129,0.05); color: #10b981; cursor: not-allowed; font-weight: 700; font-size: 1rem;" value="{{ old('total_project_cost', !empty($compDetails['total_project_cost']) && $compDetails['total_project_cost'] > 0 ? $compDetails['total_project_cost'] : $totalProjectCost) }}">
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
                            <div class="details-label">Total Grants</div><div class="details-colon">:</div>
                            <div class="details-value">{!! "&#x20B9;" !!}{{ number_format($displayGrants, 2) }}</div>

                            <div class="details-label">Community Contribution</div><div class="details-colon">:</div>
                            <div class="details-value" style="color: var(--accent-cyan);">{!! "&#x20B9;" !!}{{ number_format($displayComm, 2) }}</div>

                            <div class="details-label">Leverage</div><div class="details-colon">:</div>
                            <div class="details-value" style="color: var(--accent-cyan);">{!! "&#x20B9;" !!}{{ number_format($leverage, 2) }}</div>

                            <div class="details-label">Any Other</div><div class="details-colon">:</div>
                            <div class="details-value">{!! "&#x20B9;" !!}{{ number_format($anyOther, 2) }}</div>

                            <div class="details-label">Deductions</div><div class="details-colon">:</div>
                            <div class="details-value" style="color: var(--accent-red);">{!! "&#x20B9;" !!}{{ number_format($deductions, 2) }}</div>

                            <div class="details-label" style="font-weight: 700;">Total Project Cost</div><div class="details-colon">:</div>
                            <div class="details-value" style="font-weight: 700; color: #10b981;">{!! "&#x20B9;" !!}{{ number_format($totalProjectCost, 2) }}</div>

                            <div class="details-label">Benefited People</div><div class="details-colon">:</div>
                            <div class="details-value" style="font-weight: 700; color: #0284c7;">{{ number_format($compDetails['total_beneficiary_peoples'] ?? ($project->total_beneficiary_peoples ?? ($project->num_benefited_people ?? 0))) }} <span style="font-size: 0.8rem; font-weight: 500; color: var(--text-muted);">people</span></div>

                            <div class="details-label">Benefited Families</div><div class="details-colon">:</div>
                            <div class="details-value" style="font-weight: 700; color: #8b5cf6;">{{ number_format($compDetails['total_family'] ?? ($project->total_family ?? 0)) }} <span style="font-size: 0.8rem; font-weight: 500; color: var(--text-muted);">families</span></div>

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
                <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--panel-border); padding: 1.5rem; border-radius: 8px; margin-top: 2rem;">
                    <h3 style="color: var(--text-main); font-size: 1rem; margin-top: 0; margin-bottom: 1.25rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--panel-border); padding-bottom: 0.75rem;">Final Approval & Stage Completion</h3>

                    @if($project->status === 'Completed')
                        <div style="background-color: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent-green); color: #8cf5c6; padding: 1.5rem; border-radius: 8px; display: flex; flex-direction: column; gap: 0.75rem;">
                            <h4 style="margin: 0; font-size: 1.05rem; font-weight: 700; text-transform: uppercase;">âœ“ Project Completed & Finalized</h4>
                            @php
                                $cooStatus = $project->projectStatus;
                                $cooApprovedAt = $cooStatus ? $cooStatus->coo_approved_at : null;
                                $cooApprover = $cooStatus && $cooStatus->approver ? $cooStatus->approver->name : 'COO';
                                $cooRemarks = $cooStatus ? $cooStatus->coo_remarks : null;
                                $cooApprovedAtStr = $cooApprovedAt ? \Carbon\Carbon::parse($cooApprovedAt)->timezone('Asia/Kolkata')->format('d/m/Y h:i A') : 'N/A';
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
                            <h4 style="color: var(--text-main); font-size: 0.95rem; font-weight: 700; margin: 0 0 1rem 0; text-transform: uppercase;">Final Approval & Completion</h4>
                            @if($isCoo || $isHod || $isSuperAdmin || $isPmOnly || $isEngineerOnly)
                                <form action="{{ route('projects.approve', $project->id) }}" method="POST" style="margin: 0; display: flex; flex-direction: column; gap: 1rem; align-items: flex-start;">
                                    @csrf
                                    <input type="hidden" name="action" value="finalize_approval">
                                    <div style="width: 100%; max-width: 500px;">
                                        <label for="remarks" style="display: block; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem;">Approval Remarks (Optional):</label>
                                        <textarea name="remarks" id="remarks" rows="3" placeholder="Enter final approval remarksâ€¦" style="width: 100%; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #ffffff; padding: 0.75rem; border-radius: 6px; font-size: 0.85rem; outline: none; resize: vertical;"></textarea>
                                    </div>
                                    <button type="submit" class="btn-custom" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; color: #ffffff; cursor: pointer; font-weight: 700; padding: 0.6rem 1.8rem; border-radius: 6px; display: inline-flex; align-items: center; gap: 0.4rem;">
                                        <i class="bx bx-check-circle"></i> Finalize Project Approval & Complete
                                    </button>
                                </form>
                            @else
                                <div style="background-color: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); color: #f59e0b; padding: 0.85rem 1.25rem; border-radius: 6px; font-size: 0.9rem; font-weight: 600; display: inline-block;">
                                    <i class="bx bx-time-five"></i> Pending Final Approval
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--panel-border); background: var(--panel-bg);">
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
            <a href="{{ route('projects.category', $categorySlug) }}" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="bx bx-arrow-back"></i> Back to Project List
            </a>
        </div>
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
                return null;
            }

            function renderPhotoInDOM(data) {
                const category = data.category || 'after';
                const photos = data.photos || [];
                const totalPhotos = data.total_photos !== undefined ? data.total_photos : photos.length;

                const targetCard = findTargetPhotoCard(category);
                if (!targetCard) return;

                const badge = targetCard.querySelector('.photo-card-header span:last-child');
                if (badge) badge.textContent = `${totalPhotos} / 3`;

                let form = targetCard.querySelector('form[action*="upload_photo"], form[action*="upload-photo"]');
                let maxMsg = targetCard.querySelector('.max-photos-msg');

                if (totalPhotos >= 3) {
                    if (form) form.style.display = 'none';
                    if (!maxMsg) {
                        maxMsg = document.createElement('div');
                        maxMsg.className = 'max-photos-msg';
                        maxMsg.style.cssText = 'font-size: 0.75rem; color: #f59e0b; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); padding: 0.35rem 0.5rem; border-radius: 6px; text-align: center; margin-bottom: 0.75rem; font-weight: 600;';
                        maxMsg.textContent = 'Maximum 3 photos reached.';
                        if (form) form.parentNode.insertBefore(maxMsg, form.nextSibling);
                    } else {
                        maxMsg.style.display = 'block';
                    }
                } else {
                    if (form) form.style.display = 'flex';
                    if (maxMsg) maxMsg.style.display = 'none';
                }

                const container = targetCard.querySelector('.photo-list-container');
                if (!container) return;

                container.innerHTML = '';

                if (photos.length === 0) {
                    const cardTitle = targetCard.querySelector('.photo-card-title')?.textContent?.toLowerCase() || '';
                    container.innerHTML = `<div class="photo-empty-state">No ${cardTitle} photos yet.</div>`;
                    return;
                }

                photos.forEach((p, idx) => {
                    const photoDiv = document.createElement('div');
                    photoDiv.style.cssText = 'position: relative; background: var(--bg-color); border: 1px solid var(--panel-border); border-radius: 6px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.3); transition: all 0.3s ease;';

                    photoDiv.innerHTML = `
                        <a href="${p.url}" target="_blank" style="display: block; line-height: 0;">
                            <img src="${p.url}" style="width: 100%; height: 120px; object-fit: cover; display: block;" alt="Photo ${idx + 1}">
                        </a>
                        <form action="${p.delete_url}" method="POST" style="position: absolute; top: 0.3rem; right: 0.3rem; margin: 0;">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" style="width: 24px; height: 24px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; background: rgba(231,76,60,0.9); border: none; color: #fff; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.5);" title="Delete Photo">
                                <i class="bx bx-trash" style="font-size: 0.8rem;"></i>
                            </button>
                        </form>
                        <div style="padding: 0.3rem 0.5rem; font-size: 0.72rem; color: var(--text-muted);">
                            Photo ${idx + 1}
                        </div>
                    `;
                    container.appendChild(photoDiv);
                });
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

        async function performToggleChecklistDocument(button, docName) {
            button.disabled = true;
            try {
                const response = await fetch("{{ route('projects.toggle_file', $project->id) }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
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

                    const cellId = 'ticked-at-' + docName.replace(/ /g, '_').toLowerCase();
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
            const committeeName = formatVal(getVal(['committee_name', 'mahallu_name', 'place']));
            const regNumber = formatVal(getVal(['reg_number']));
            const year = formatVal(getVal(['year']));
            const location = formatVal(getVal(['location', 'place']));
            const village = formatVal(getVal(['village']));
            const post = formatVal(getVal(['post', 'post_office']));
            const panchayath = formatVal(getVal(['panchayath', 'panchayat']));
            const dist = formatVal(getVal(['district']));
            const st = formatVal(getVal(['state']));
            const pinCode = formatVal(getVal(['pin_code', 'pin']));
            const c1 = formatVal(getVal(['contact_number_1', 'mobile_1', 'mobile', 'contact1']));
            const c2 = formatVal(getVal(['contact_number_2', 'mobile_2', 'contact2']));
            const localityPlace = formatVal(getVal(['locality_place', 'locality_location', 'location']));
            const localityVillage = formatVal(getVal(['locality_village', 'village']));
            const localityPost = formatVal(getVal(['locality_post', 'post']));
            const localityPanchayath = formatVal(getVal(['locality_panchayath', 'panchayath']));
            const localityDist = formatVal(getVal(['locality_district', 'district']));
            const localitySt = formatVal(getVal(['locality_state', 'state']));
            const localityPin = formatVal(getVal(['locality_pin_code', 'locality_pin']));
            const submittedBefore = formatVal(getVal(['submitted_before']));
            const rcfiSupport = formatVal(getVal(['received_support_before']));

            const mahalluName = formatVal(getVal(['mahallu_name']));
            const localityLocation = formatVal(getVal(['locality_place', 'location']));
            const localityVillage = formatVal(getVal(['locality_village', 'village']));
            const lDist = getVal(['locality_district', 'district']);
            const lSt = getVal(['locality_state', 'state']);
            const localityDistState = (lDist || lSt) ? `${formatVal(lDist)} / ${formatVal(lSt)}` : '<span style="color: var(--text-muted); font-style: italic;">N/A</span>';
            const familiesCount = formatVal(getVal(['families_in_mahallu']));
            const requirement = formatVal(getVal(['requirement']));

            const siteHasBuilding = formatVal(getVal(['site_has_building']));
            const buildingStatus = formatVal(getVal(['status_of_current_building']));

            const projectType = formatVal(getVal(['project_type', 'applying_for', 'office_application_type']));
            const buildingArea = formatVal(getVal(['building_area_sq']));
            const landArea = formatVal(getVal(['land_area_sq']));
            const budget = app.amount_requested ? '&#x20B9;' + Number(app.amount_requested).toLocaleString() : 'N/A';
            const legalApprovals = formatVal(getVal(['legal_approvals_status']));
            const areaZone = formatVal(getVal(['area', 'project_area']));
            const appStatus = app.status || 'Pending';

            container.innerHTML = `
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <!-- Col 1 -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">1. Applicant & Committee</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px; color: var(--text-muted);">Applicant Name:</td><td style="color: var(--text-main); font-weight: 600;">${applicantName}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Committee Name:</td><td>${committeeName}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Reg. Number:</td><td>${regNumber}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Year:</td><td>${year}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Place:</td><td>${location}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Village:</td><td>${village}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Post:</td><td>${post}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Panchayath:</td><td>${panchayath}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">District:</td><td>${dist}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">State:</td><td>${st}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Pin Code:</td><td>${pinCode}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Contact Number 1:</td><td>${c1}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Contact Number 2:</td><td>${c2}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Submitted Before?</td><td>${submittedBefore}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">RCFI Support?</td><td>${rcfiSupport}</td></tr>
                        </table>

                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">2. Mahallu Locality Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px; color: var(--text-muted);">Mahallu Name:</td><td>${mahalluName}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Place:</td><td>${localityPlace}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Village:</td><td>${localityVillage}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Post:</td><td>${localityPost}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Panchayath:</td><td>${localityPanchayath}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">District:</td><td>${localityDist}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">State:</td><td>${localitySt}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Pin Code:</td><td>${localityPin}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Families Count:</td><td>${familiesCount}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Requirement:</td><td>${requirement}</td></tr>
                        </table>
                    </div>

                    <!-- Col 2 -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">3. Current Status Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px; color: var(--text-muted);">Has Building?</td><td>${siteHasBuilding}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Building Status:</td><td>${buildingStatus}</td></tr>
                        </table>

                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">4. Proposed Project Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px; color: var(--text-muted);">Project Type:</td><td style="text-transform: capitalize; font-weight: 600; color: var(--text-main);">${projectType}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Building Area (Sq):</td><td>${buildingArea}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Land Area (Sq):</td><td>${landArea}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Proposed Budget:</td><td style="color: var(--accent-green); font-weight: 600;">${budget}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Legal Approvals:</td><td>${legalApprovals}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Permitted Area:</td><td>${areaZone}</td></tr>
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
            if (stageNum <= 3) {
                isLocked = false;
            } else {
                isLocked = (activeProjectStage < 4 && isProjectApproved !== '1');
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
        window.switchStage = switchStage;
        


        // Initialize display to show the stage panel
        function initStageDisplay() {
            const savedStage = sessionStorage.getItem('current_project_stage_{{ $project->id }}');
            let stageToLoad = {{ min($project->stage ?? 1, 5) }};
            if (savedStage) {
                const stageNum = Number(savedStage);
                let isLocked = false;
                if (stageNum <= 3) {
                    isLocked = false;
                } else {
                    isLocked = (activeProjectStage < 4 && isProjectApproved !== '1');
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
            switchStage(3);
            const modal = document.getElementById('addMaterialModal');
            if (modal) modal.style.display = 'flex';
        }
        function closeAddMaterialModal() {
            const modal = document.getElementById('addMaterialModal');
            if (modal) modal.style.display = 'none';
        }
        function openEditMaterialModal(index, name, amount) {
            switchStage(3);
            const form = document.getElementById('editMaterialForm');
            if (form) form.setAttribute('action', `/admin/projects/{{ $project->id }}/materials/${index}`);
            const elName = document.getElementById('editMaterialName');
            if (elName) elName.value = name;
            const elAmt = document.getElementById('editMaterialAmount');
            if (elAmt) elAmt.value = amount;
            const modal = document.getElementById('editMaterialModal');
            if (modal) modal.style.display = 'flex';
        }
        function closeEditMaterialModal() {
            const modal = document.getElementById('editMaterialModal');
            if (modal) modal.style.display = 'none';
        }

        // Community Contribution Modal Controls
        function openAddCommContribModal() {
            switchStage(3);
            const modal = document.getElementById('addCommContribModal');
            if (modal) modal.style.display = 'flex';
        }
        function closeAddCommContribModal() {
            const modal = document.getElementById('addCommContribModal');
            if (modal) modal.style.display = 'none';
        }
        function openEditCommContribModal(index, item, amount) {
            switchStage(3);
            const form = document.getElementById('editCommContribForm');
            if (form) form.setAttribute('action', `/admin/projects/{{ $project->id }}/community-contributions/${index}`);
            const elName = document.getElementById('editCommContribName');
            if (elName) elName.value = item;
            const elAmt = document.getElementById('editCommContribAmount');
            if (elAmt) elAmt.value = amount;
            const modal = document.getElementById('editCommContribModal');
            if (modal) modal.style.display = 'flex';
        }
        function closeEditCommContribModal() {
            const modal = document.getElementById('editCommContribModal');
            if (modal) modal.style.display = 'none';
        }

        // Expense Management Modal Controls
        function openAddExpenseModal(materialIndex, materialName) {
            switchStage(4);
            const elIdx = document.getElementById('addExpenseFormMaterialIndex');
            if (elIdx) elIdx.value = materialIndex;
            const elName = document.getElementById('addExpenseModalMaterialName');
            if (elName) elName.innerText = materialName;
            const modal = document.getElementById('addExpenseModal');
            if (modal) modal.style.display = 'flex';
        }
        function closeAddExpenseModal() {
            const modal = document.getElementById('addExpenseModal');
            if (modal) modal.style.display = 'none';
        }
        function openEditExpenseModal(index, materialIndex, name, quantity, amount) {
            switchStage(4);
            const form = document.getElementById('editExpenseForm');
            if (form) form.setAttribute('action', `/admin/projects/{{ $project->id }}/expenses/${index}`);
            const elIdx = document.getElementById('editExpenseFormMaterialIndex');
            if (elIdx) elIdx.value = materialIndex;
            const elName = document.getElementById('editExpenseName');
            if (elName) elName.value = name;
            const elQty = document.getElementById('editExpenseQuantity');
            if (elQty) elQty.value = quantity;
            const elAmt = document.getElementById('editExpenseAmount');
            if (elAmt) elAmt.value = amount;
            const modal = document.getElementById('editExpenseModal');
            if (modal) modal.style.display = 'flex';
        }
        function closeEditExpenseModal() {
            const modal = document.getElementById('editExpenseModal');
            if (modal) modal.style.display = 'none';
        }

        // Community Contribution Expense Management
        function openAddCommExpenseModal(commIndex, commName) {
            switchStage(4);
            const elIdx = document.getElementById('addCommExpenseFormIndex');
            if (elIdx) elIdx.value = commIndex;
            const elName = document.getElementById('addCommExpenseModalName');
            if (elName) elName.innerText = commName;
            const modal = document.getElementById('addCommExpenseModal');
            if (modal) modal.style.display = 'flex';
        }
        function closeAddCommExpenseModal() {
            const modal = document.getElementById('addCommExpenseModal');
            if (modal) modal.style.display = 'none';
        }
        function openEditCommExpenseModal(index, commIndex, name, quantity, amount) {
            switchStage(4);
            const form = document.getElementById('editCommExpenseForm');
            if (form) form.setAttribute('action', `/admin/projects/{{ $project->id }}/expenses/${index}`);
            const elIdx = document.getElementById('editCommExpenseFormIndex');
            if (elIdx) elIdx.value = commIndex;
            const elName = document.getElementById('editCommExpenseName');
            if (elName) elName.value = name;
            const elQty = document.getElementById('editCommExpenseQuantity');
            if (elQty) elQty.value = quantity;
            const elAmt = document.getElementById('editCommExpenseAmount');
            if (elAmt) elAmt.value = amount;
            const modal = document.getElementById('editCommExpenseModal');
            if (modal) modal.style.display = 'flex';
        }
        function closeEditCommExpenseModal() {
            const modal = document.getElementById('editCommExpenseModal');
            if (modal) modal.style.display = 'none';
        }

        // Contractor Modal Controls
        function openAddContractorModal() {
            switchStage(3);
            const modal = document.getElementById('addContractorModal');
            if (modal) modal.style.display = 'flex';
        }
        function closeAddContractorModal() {
            const modal = document.getElementById('addContractorModal');
            if (modal) modal.style.display = 'none';
        }
        window.projectContractorsList = @json(array_values($contractors ?? []));

        function openEditContractorModal(index, contractorData) {
            if (!contractorData && window.projectContractorsList && window.projectContractorsList[index]) {
                contractorData = window.projectContractorsList[index];
            }
            switchStage(3);
            const form = document.getElementById('editContractorForm');
            if (form) form.setAttribute('action', `/admin/projects/{{ $project->id }}/contractors/${index}`);
            const select = document.getElementById('edit_contractor_select');
            if (select && contractorData) {
                select.value = contractorData.contractor_id || contractorData.id || '';
                updateEditContractorDetails();
            }
            const modal = document.getElementById('editContractorModal');
            if (modal) modal.style.display = 'flex';
        }
        function closeEditContractorModal() {
            const modal = document.getElementById('editContractorModal');
            if (modal) modal.style.display = 'none';
        }
        function updateAddContractorDetails() {
            const select = document.getElementById('add_contractor_select');
            if (!select) return;
            const option = select.options[select.selectedIndex];
            const card = document.getElementById('add_contractor_details_card');
            if (select.value && card && option) {
                const elCompany = document.getElementById('add_c_company');
                if (elCompany) elCompany.innerText = option.getAttribute('data-company') || 'N/A';
                const elPhone = document.getElementById('add_c_phone');
                if (elPhone) elPhone.innerText = option.getAttribute('data-phone') || 'N/A';
                const elAddr = document.getElementById('add_c_address');
                if (elAddr) elAddr.innerText = option.getAttribute('data-address') || 'N/A';
                card.style.display = 'block';
            } else if (card) {
                card.style.display = 'none';
            }
        }
        function updateEditContractorDetails() {
            const select = document.getElementById('edit_contractor_select');
            if (!select) return;
            const option = select.options[select.selectedIndex];
            const card = document.getElementById('edit_contractor_details_card');
            if (select.value && card && option) {
                const elCompany = document.getElementById('edit_c_company');
                if (elCompany) elCompany.innerText = option.getAttribute('data-company') || 'N/A';
                const elPhone = document.getElementById('edit_c_phone');
                if (elPhone) elPhone.innerText = option.getAttribute('data-phone') || 'N/A';
                const elAddr = document.getElementById('edit_c_address');
                if (elAddr) elAddr.innerText = option.getAttribute('data-address') || 'N/A';
                card.style.display = 'block';
            } else if (card) {
                card.style.display = 'none';
            }
        }
    </script>

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
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Amount (&#x20B9;)</label>
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
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Amount (&#x20B9;)</label>
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
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Amount (&#x20B9;)</label>
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
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Amount (&#x20B9;)</label>
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
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Amount (&#x20B9;)</label>
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
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Amount (&#x20B9;)</label>
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
        // Inspection Modal Controls
        function openAddInspectionModal() {
            switchStage(4);
            const modal = document.getElementById('addInspectionModal');
            if (modal) modal.style.display = 'flex';
        }
        function closeAddInspectionModal() {
            const modal = document.getElementById('addInspectionModal');
            if (modal) modal.style.display = 'none';
        }
        function openEditInspectionModal(id, name, designation, date, remarks) {
            switchStage(4);
            const form = document.getElementById('editInspectionForm');
            if (form) form.setAttribute('action', `/admin/projects/${activeProjectId}/inspections/${id}`);
            const elName = document.getElementById('edit_inspection_name');
            if (elName) elName.value = name;
            const elDesig = document.getElementById('edit_inspection_designation');
            if (elDesig) elDesig.value = designation;
            const elDate = document.getElementById('edit_inspection_date');
            if (elDate) elDate.value = date;
            const elRem = document.getElementById('edit_inspection_remarks');
            if (elRem) elRem.value = remarks;
            const modal = document.getElementById('editInspectionModal');
            if (modal) modal.style.display = 'flex';
        }
        function closeEditInspectionModal() {
            const modal = document.getElementById('editInspectionModal');
            if (modal) modal.style.display = 'none';
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
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Amount (&#x20B9;)</label>
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
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Amount (&#x20B9;)</label>
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

@endsection

