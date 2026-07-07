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

    <!-- Stage Navigation Tabs (Interactive Navigation) -->
    <div class="stages-tabs">
        @for($i = 1; $i <= 6; $i++)
            @php
                $isActive = $project->stage === $i;
                $isCompleted = $project->stage > $i;
                $class = $isActive ? 'active' : ($isCompleted ? 'completed' : '');
                
                // Lock other stages if the project is not Approved by COO
                // For Education Center projects, unlock stages up to the current stage
                if ($project->type_of_project === 'Education Center') {
                    $isLocked = ($project->status !== 'Approved' && $i > $project->stage);
                } else {
                    $isLocked = ($project->status !== 'Approved' && $i > 1);
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
        $isCoo = ($authUser && ($authUser->role == 2 || strtolower($authUser->designation ?? '') === 'coo'));
        $isProjectManager = ($authUser && ($authUser->role == 3 || $authUser->role == 1 || strtolower($authUser->designation ?? '') === 'project manager'));
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
                @if($project->type_of_project !== 'Education Center')
                    @if($project->status !== 'Approved')
                        <div style="margin-bottom: 1.5rem;">
                            @if($isCoo)
                                <form action="{{ route('projects.approve', $project->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-custom" style="background: #eb3b5a; cursor: pointer; font-weight: 700; padding: 0.75rem 1.5rem;">
                                        Approve Project
                                    </button>
                                </form>
                            @else
                                <div style="background-color: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); color: #f59e0b; padding: 0.85rem 1.25rem; border-radius: 6px; font-size: 0.9rem; font-weight: 600; display: inline-block;">
                                    <i class="bx bx-time-five"></i> Pending COO Approval
                                </div>
                            @endif
                        </div>
                    @else
                        <div style="margin-bottom: 1.5rem; background-color: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #8cf5c6; padding: 0.85rem 1.25rem; border-radius: 6px; font-size: 0.9rem; font-weight: 600; display: inline-block;">
                            <i class="bx bx-check-circle"></i> Project Approved & Active
                        </div>
                    @endif
                @endif

                <div class="details-grid">
                    <div class="details-label">Project ID</div><div class="details-colon">:</div><div class="details-value" style="color: var(--accent-cyan);">{{ $project->project_id }}</div>
                    <div class="details-label">Agency Project No</div><div class="details-colon">:</div><div class="details-value">{{ $project->agency_project_no ?? 'N/A' }}</div>
                    <div class="details-label">Donor Name</div><div class="details-colon">:</div><div class="details-value">{{ $project->donor ? $project->donor->name : 'N/A' }}</div>
                    <div class="details-label">Project Manager</div><div class="details-colon">:</div><div class="details-value">{{ $project->projectManager ? $project->projectManager->name : 'N/A' }}</div>
                    <div class="details-label">Available Budget</div><div class="details-colon">:</div><div class="details-value">₹{{ number_format($project->available_budget, 2) }}</div>
                    <div class="details-label">Type of Project</div><div class="details-colon">:</div><div class="details-value">{{ $project->type_of_project }}</div>
                    <div class="details-label">Remarks</div><div class="details-colon">:</div><div class="details-value" style="font-weight: normal; color: var(--text-muted);">{{ $project->remarks ?? 'N/A' }}</div>
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
                    <h3 style="color: #ffffff; font-size: 1rem; margin-bottom: 1rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
                        <i class="bx bx-git-branch" style="color: var(--accent-cyan); margin-right: 0.4rem;"></i>
                        Project Status
                    </h3>

                    {{-- Current phase badge --}}
                    <div id="current-phase-badge" style="margin-bottom: 1rem;">
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
                    </div>

                    @if($isProjectManager || $isCoo)
                    <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: flex-end; max-width: 560px;">
                        <div style="flex: 1; min-width: 220px;">
                            <label style="display: block; color: var(--text-muted); font-size: 0.82rem; margin-bottom: 0.35rem;">Select Phase</label>
                            <select id="project-phase-select" onchange="onPhaseSelectChange()" style="width: 100%; padding: 0.55rem 0.85rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff; font-size: 0.9rem; outline: none; cursor: pointer;">
                                <option value="">— Select phase —</option>
                                @foreach($phases as $phase)
                                    <option value="{{ $phase }}" {{ $currentPhase === $phase ? 'selected' : '' }}>{{ $phase }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="phase-custom-box" style="flex: 1; min-width: 180px; {{ $currentPhase === 'Other' ? '' : 'display: none;' }}">
                            <label style="display: block; color: var(--text-muted); font-size: 0.82rem; margin-bottom: 0.35rem;">Describe (Other)</label>
                            <input type="text" id="project-phase-custom" placeholder="Enter custom status…" maxlength="255"
                                   value="{{ $currentCustom }}"
                                   style="width: 100%; padding: 0.55rem 0.85rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff; font-size: 0.9rem; outline: none; box-sizing: border-box;">
                        </div>
                        <button onclick="saveProjectPhase()" style="padding: 0.55rem 1.25rem; border-radius: 6px; background: linear-gradient(135deg, var(--accent-cyan), #0891b2); border: none; color: #000; font-weight: 700; font-size: 0.85rem; cursor: pointer; white-space: nowrap; display: inline-flex; align-items: center; gap: 0.4rem; transition: opacity 0.2s;" onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                            <i class="bx bx-save"></i> Save Status
                        </button>
                    </div>
                    @endif
                </div>

                <!-- Connect Application Form -->
                @if($isCoo && $project->type_of_project !== 'Education Center')
                <div style="margin-top: 2rem; border-top: 1px solid var(--panel-border); padding-top: 1.5rem;">
                    <h3 style="color: #ffffff; font-size: 1.1rem; margin-bottom: 1rem;">Connect Application</h3>
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

                @if($project->type_of_project !== 'Education Center')
                    <div class="stage-success-banner">
                        Applicant ID {{ $appId }} has been Approved
                    </div>

                    @if($project->stage === 2)
                        <div style="margin-bottom: 1.5rem;">
                            <form action="{{ route('projects.approve', $project->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-custom" style="background: #eb3b5a;">
                                    Approve & Promote to Stage 3
                                </button>
                            </form>
                        </div>
                    @endif
                @endif

                <!-- Connect Application Form inside Stage 2 for Education Center project (Show First) -->
                @if(($isProjectManager || $isCoo) && $project->type_of_project === 'Education Center' && $project->status !== 'Approved')
                <div style="margin-bottom: 2rem; border-bottom: 1px solid var(--panel-border); padding-bottom: 1.5rem;">
                    <h3 style="color: #ffffff; font-size: 1.1rem; margin-bottom: 1rem;">Connect Application</h3>
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

                @php
                    $metaData = [];
                    if ($application) {
                        if (is_array($application->meta)) {
                            $metaData = $application->meta;
                        } elseif (is_string($application->meta)) {
                            $metaData = json_decode($application->meta, true) ?? [];
                        }
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
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px; color: var(--text-muted);">Applicant Name:</td><td style="color: #ffffff; font-weight: 600;">{!! $formatVal($application->applicant_name) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Committee Name:</td><td>{!! $formatVal($metaData['committee_name'] ?? $metaData['mahallu_name'] ?? $metaData['place'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Reg. Number:</td><td>{!! $formatVal($metaData['reg_number'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Year:</td><td>{!! $formatVal($metaData['year'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Location:</td><td>{!! $formatVal($metaData['location'] ?? $metaData['place'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Village:</td><td>{!! $formatVal($metaData['village'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Post:</td><td>{!! $formatVal($metaData['post'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Panchayath:</td><td>{!! $formatVal($metaData['panchayath'] ?? $metaData['panchayat'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">District / State:</td><td>{!! $formatVal($metaData['district'] ?? null) !!} / {!! $formatVal($metaData['state'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Contact 1 / 2:</td><td>{!! $formatVal($metaData['contact_number_1'] ?? $metaData['contact1'] ?? $metaData['mobile'] ?? null) !!} / {!! $formatVal($metaData['contact_number_2'] ?? $metaData['contact2'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Submitted Before?</td><td>{!! $formatVal($metaData['submitted_before'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">RCFI Support?</td><td>{!! $formatVal($metaData['received_support_before'] ?? null) !!}</td></tr>
                                </table>

                                <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">2. Mahallu Locality Details</h4>
                                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px; color: var(--text-muted);">Mahallu Name:</td><td>{!! $formatVal($metaData['mahallu_name'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Location:</td><td>{!! $formatVal($metaData['locality_location'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Village:</td><td>{!! $formatVal($metaData['locality_village'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">District / State:</td><td>{!! $formatVal($metaData['locality_district'] ?? null) !!} / {!! $formatVal($metaData['locality_state'] ?? null) !!}</td></tr>
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
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px; color: var(--text-muted);">Project Type:</td><td style="text-transform: capitalize; font-weight: 600; color: #ffffff;">{!! $formatVal($metaData['project_type'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Building Area (Sq):</td><td>{!! $formatVal($metaData['building_area_sq'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Land Area (Sq):</td><td>{!! $formatVal($metaData['land_area_sq'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Classrooms Count:</td><td>{!! $formatVal($metaData['num_classrooms'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Proposed Students:</td><td>{!! $formatVal($metaData['num_students'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Proposed Budget:</td><td style="color: var(--accent-green); font-weight: 600;">{{ $application->amount_requested ? '₹' . number_format($application->amount_requested) : 'N/A' }}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Legal Approvals:</td><td>{!! $formatVal($metaData['legal_approvals_status'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Area / Zone:</td><td>{!! $formatVal($metaData['area'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Review Status:</td><td style="font-weight: 600; color: #ffffff;">{{ $application->status }}</td></tr>
                                </table>
                            </div>
                        </div>

                        <div style="margin-top: 1.5rem; border-top: 1px solid var(--panel-border); padding-top: 1rem;">
                            <h5 style="color: var(--accent-cyan); font-size: 0.85rem; margin-bottom: 0.5rem; text-transform: uppercase; font-weight: 700;">Additional Notes:</h5>
                            <p style="color: var(--text-muted); line-height: 1.5; font-size: 0.85rem; margin: 0; background-color: #121824; padding: 0.75rem; border-radius: 6px; border: 1px solid var(--panel-border); min-height: 50px;">
                                {{ $application->details ? $application->details : 'No additional notes provided.' }}
                            </p>
                        </div>
                    @else
                        <div style="text-align: center; padding: 3rem; background-color: rgba(255, 255, 255, 0.02); border-radius: 8px; border: 1px dashed var(--panel-border); margin: 2rem 0;">
                            <i class="bx bx-link-external" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
                            <h3 style="color: #ffffff; font-size: 1.2rem; margin-bottom: 0.5rem;">No Application Connected</h3>
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


                <table class="stage-table">
                    <thead>
                        <tr>
                            <th>Document Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $docs = [
                                'Land document', 
                                'Possession certificate', 
                                'Recommendation letter',
                                'Committee minutes', 
                                'Permit copy', 
                                'Plan', 
                                'Tender schedule sheet',
                                'Site study', 
                                'Quotations', 
                                'Quotations approval form',
                                'Work order letter',
                                'Meeting minutes copy',
                                'Agreement with contractor',
                                'Agreement with committee',
                                'Project summary form'
                            ];
                            $projectFiles = $project->files ?? [];
                        @endphp
                        @foreach($docs as $doc)
                            @php
                                $filePath = $projectFiles[$doc] ?? null;
                            @endphp
                            <tr>
                                <td style="font-weight: 600; color: #ffffff; vertical-align: middle;">{{ $doc }}</td>
                                <td style="vertical-align: middle;">
                                    @if($isProjectManager)
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
                <div class="stage-success-banner">
                    Fund Allocated are Approved
                </div>

                @php
                    $materials = $project->materials;
                    if (empty($materials)) {
                        $materials = [
                            ['material' => 'cement', 'amount' => 8000],
                            ['material' => 'metal', 'amount' => 8000]
                        ];
                    }
                    $totalAmount = 0;
                    foreach($materials as $item) {
                        $totalAmount += $item['amount'];
                    }
                @endphp

                @if($project->stage === 4)
                    <div style="margin-bottom: 1.5rem;">
                        @if($isCoo)
                            <form action="{{ route('projects.approve', $project->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-custom" style="background: #eb3b5a;">
                                    Approve & Promote to Stage 5
                                </button>
                            </form>
                        @else
                            <div style="background-color: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); color: #f59e0b; padding: 0.85rem 1.25rem; border-radius: 6px; font-size: 0.9rem; font-weight: 600; display: inline-block;">
                                <i class="bx bx-time-five"></i> Pending COO Approval
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Real-time Budget Metrics Bar -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                    <!-- Project Budget Card -->
                    <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(16, 185, 129, 0.2); padding: 1.25rem; border-radius: 8px; border-left: 4px solid #10b981;">
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Project Budget</div>
                        <div style="font-size: 1.3rem; font-weight: 700; color: #ffffff;">₹{{ number_format($project->available_budget, 2) }}</div>
                    </div>
                    <!-- Proposed Budget Card -->
                    <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(99, 102, 241, 0.2); padding: 1.25rem; border-radius: 8px; border-left: 4px solid #6366f1;">
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Application Proposed Budget</div>
                        <div style="font-size: 1.3rem; font-weight: 700; color: #ffffff;">{{ $application && $application->amount_requested ? '₹' . number_format($application->amount_requested, 2) : 'N/A' }}</div>
                    </div>
                    <!-- Total Allocated Card -->
                    <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(6, 182, 212, 0.2); padding: 1.25rem; border-radius: 8px; border-left: 4px solid var(--accent-cyan);">
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Total Allocated</div>
                        <div style="font-size: 1.3rem; font-weight: 700; color: var(--accent-cyan);">₹{{ number_format($totalAmount, 2) }}</div>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                    <div style="display: flex; gap: 0.75rem; align-items: center;">
                        <button class="btn-custom" style="background: #4b6584;" onclick="alert('Exporting stage budget to Excel...')">
                            Download Excel
                        </button>
                        @if($isProjectManager)
                            <button onclick="openAddMaterialModal()" class="btn-custom" style="background: rgba(6, 182, 212, 0.1); border: 1px solid var(--accent-cyan); color: var(--accent-cyan); cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem;">
                                <i class="bx bx-plus"></i> Add Item
                            </button>
                        @endif
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem;">
                        <span style="color: var(--text-muted);">Search:</span>
                        <input type="text" placeholder="Search budget..." class="form-control-dark" style="width: 160px; padding: 0.35rem 0.75rem; border-radius: 4px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;">
                    </div>
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
                                <td style="font-weight: 600; color: #ffffff; vertical-align: middle;">{{ $item['material'] }}</td>
                                <td style="text-align: right; font-weight: 600; color: #ffffff; vertical-align: middle;">
                                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.5rem;">
                                        <span>₹ {{ number_format($item['amount'], 2) }}</span>
                                        @if($isProjectManager)
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
            </div>
        </div>

        <!-- ================= STAGE 5 PANEL (EVALUATION & INSPECTION) ================= -->
        <div class="stage-content-panel" id="stage-content-5">
            <div class="detail-header-panel">
                <h2>EVALUATION & INSPECTION</h2>
            </div>
            <div style="padding: 1.5rem;">
                <div class="stage-success-banner">
                    Evaluation & Site Inspections
                </div>

                @php
                    $stage5Materials = $project->materials;
                    if (empty($stage5Materials)) {
                        $stage5Materials = [
                            ['material' => 'cement', 'amount' => 8000],
                            ['material' => 'metal', 'amount' => 8000]
                        ];
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
                        $totalExpensesAmount += $item['amount'];
                    }

                    $stage5TotalBudget = (float)$totalAllocatedAmount;
                    $stage5SpentAmount = (float)$totalExpensesAmount;
                    $stage5BalanceAmount = $stage5TotalBudget - $stage5SpentAmount;
                    
                    $stage5SpentPercentage = $stage5TotalBudget > 0 ? min(100, ($stage5SpentAmount / $stage5TotalBudget) * 100) : 0;
                    $stage5BalancePercentage = 100 - $stage5SpentPercentage;
                    
                    // SVG Circumference is 2 * pi * 50 = 314.16
                    $stage5Circumference = 314.16;
                    $stage5SpentDashoffset = $stage5Circumference - ($stage5Circumference * ($stage5SpentPercentage / 100));
                @endphp

                @if($project->stage === 5)
                    <div style="margin-bottom: 1.5rem;">
                        @if($isCoo)
                            <form action="{{ route('projects.approve', $project->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-custom" style="background: #eb3b5a;">
                                    Approve & Promote to Stage 6
                                </button>
                            </form>
                        @else
                            <div style="background-color: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); color: #f59e0b; padding: 0.85rem 1.25rem; border-radius: 6px; font-size: 0.9rem; font-weight: 600; display: inline-block;">
                                <i class="bx bx-time-five"></i> Pending COO Approval
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Beautiful Financial Breakdown and Diagram -->
                <div style="display: flex; align-items: center; justify-content: center; gap: 2rem; background: rgba(255,255,255,0.02); border: 1px solid var(--panel-border); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; flex-wrap: wrap;">
                    <!-- Circular Diagram -->
                    <div style="position: relative; width: 120px; height: 120px; flex-shrink: 0;">
                        <svg width="120" height="120" viewBox="0 0 120 120">
                            <!-- Background Circle (Balance - Cyan) -->
                            <circle cx="60" cy="60" r="50" fill="transparent" stroke="var(--accent-cyan)" stroke-width="12" />
                            <!-- Foreground Circle (Spent - Red/Orange) -->
                            <circle cx="60" cy="60" r="50" fill="transparent" stroke="var(--accent-red)" stroke-width="12"
                                    stroke-dasharray="314.16" stroke-dashoffset="{{ $stage5SpentDashoffset }}"
                                    stroke-linecap="round" transform="rotate(-90 60 60)"
                                    style="transition: stroke-dashoffset 0.5s ease-in-out;" />
                        </svg>
                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #ffffff;">
                            <span style="font-size: 1.25rem; font-weight: 700;">{{ number_format($stage5SpentPercentage, 0) }}%</span>
                            <span style="font-size: 0.65rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Spent</span>
                        </div>
                    </div>
                    <!-- Stats Details -->
                    <div style="flex-grow: 1; min-width: 250px;">
                        <h4 style="margin: 0 0 1rem 0; font-size: 1rem; color: #ffffff; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700;">Financial Summary (Allocated Budget)</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem;">
                            <!-- Total Budget Card -->
                            <div style="background: rgba(255,255,255,0.01); border: 1px solid var(--panel-border); padding: 0.75rem; border-radius: 6px;">
                                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; margin-bottom: 0.25rem;">Total Allocated</div>
                                <span style="font-size: 1.1rem; font-weight: 700; color: #ffffff;">₹{{ number_format($stage5TotalBudget, 2) }}</span>
                            </div>
                            <!-- Balance Card -->
                            <div style="background: rgba(255,255,255,0.01); border: 1px solid rgba(6, 182, 212, 0.2); padding: 0.75rem; border-radius: 6px;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                                    <span style="display: inline-block; width: 8px; height: 8px; background-color: var(--accent-cyan); border-radius: 50%;"></span>
                                    <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Total Balance</span>
                                </div>
                                <span style="font-size: 1.1rem; font-weight: 700; color: var(--accent-cyan);">₹{{ number_format($stage5BalanceAmount, 2) }}</span>
                                <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 0.15rem;">{{ number_format($stage5BalancePercentage, 1) }}% left</div>
                            </div>
                            <!-- Expense Card -->
                            <div style="background: rgba(255,255,255,0.01); border: 1px solid rgba(239, 68, 68, 0.2); padding: 0.75rem; border-radius: 6px;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                                    <span style="display: inline-block; width: 8px; height: 8px; background-color: var(--accent-red); border-radius: 50%;"></span>
                                    <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Total Expenses</span>
                                </div>
                                <span style="font-size: 1.1rem; font-weight: 700; color: var(--accent-red);">₹{{ number_format($stage5SpentAmount, 2) }}</span>
                                <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 0.15rem;">{{ number_format($stage5SpentPercentage, 1) }}% spent</div>
                            </div>
                        </div>
                    </div>
                </div>

                @php
                    $materialsChart = [];
                    $chartMaxValue = 1;
                    foreach($stage5Materials as $materialIdx => $material) {
                        $itemExpenses = array_filter($expenses, function($exp) use ($materialIdx) {
                            return isset($exp['material_index']) && $exp['material_index'] == $materialIdx;
                        });
                        $itemTotalSpent = 0;
                        foreach($itemExpenses as $exp) {
                            $itemTotalSpent += $exp['amount'];
                        }
                        $allocated = (float)$material['amount'];
                        $spent = (float)$itemTotalSpent;
                        $chartMaxValue = max($chartMaxValue, $allocated, $spent);
                        $materialsChart[] = [
                            'material' => $material['material'],
                            'allocated' => $allocated,
                            'spent' => $spent,
                        ];
                    }
                @endphp

                <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--panel-border); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
                    <h4 style="margin: 0 0 1rem 0; font-size: 1rem; color: #ffffff; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700;">Materials Spend Graph</h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; align-items: end;">
                        @foreach($materialsChart as $materialData)
                            @php
                                $allocatedPercent = $chartMaxValue > 0 ? ($materialData['allocated'] / $chartMaxValue) * 100 : 0;
                                $spentPercent = $chartMaxValue > 0 ? ($materialData['spent'] / $chartMaxValue) * 100 : 0;
                            @endphp
                            <div style="background: rgba(255,255,255,0.01); border: 1px solid var(--panel-border); padding: 1rem; border-radius: 10px; display: flex; flex-direction: column; gap: 1rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.5rem;">
                                    <span style="font-weight: 700; color: #ffffff;">{{ $materialData['material'] }}</span>
                                    <span style="font-size: 0.85rem; color: var(--text-muted);">₹{{ number_format($materialData['spent'], 2) }} / ₹{{ number_format($materialData['allocated'], 2) }}</span>
                                </div>
                                <div style="display: flex; align-items: flex-end; gap: 0.75rem; min-height: 180px;">
                                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                                        <div style="width: 100%; height: 100%; background: rgba(248,113,113,0.15); border-radius: 14px; position: relative; display: flex; align-items: flex-end;">
                                            <div style="width: 100%; height: {{ $spentPercent }}%; background: rgba(239,68,68,0.95); border-radius: 14px 14px 0 0; transition: height 0.4s ease-in-out;"></div>
                                        </div>
                                        <span style="font-size: 0.75rem; color: var(--text-muted);">Spent</span>
                                    </div>
                                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                                        <div style="width: 100%; height: 100%; background: rgba(16,185,129,0.15); border-radius: 14px; position: relative; display: flex; align-items: flex-end;">
                                            <div style="width: 100%; height: {{ $allocatedPercent }}%; background: rgba(6,182,212,0.95); border-radius: 14px 14px 0 0; transition: height 0.4s ease-in-out;"></div>
                                        </div>
                                        <span style="font-size: 0.75rem; color: var(--text-muted);">Allocated</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Expenses Section -->
                @if($project->type_of_project === 'Education Center')
                <div style="margin-top: 2rem; border-top: 1px solid var(--panel-border); padding-top: 1.5rem; margin-bottom: 2rem;">
                    <h3 style="color: #ffffff; font-size: 1.1rem; margin-bottom: 1.5rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Allocated Items & Spent Expenses</h3>

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
                                    <td style="font-weight: 700; color: #ffffff; vertical-align: middle;">
                                        <i class="bx bx-package" style="color: var(--accent-cyan); margin-right: 0.5rem;"></i>{{ $material['material'] }}
                                    </td>
                                    <td style="text-align: right; font-weight: 600; color: #ffffff; vertical-align: middle;">₹{{ number_format($material['amount'], 2) }}</td>
                                    <td style="text-align: right; font-weight: 600; color: var(--accent-red); vertical-align: middle;">₹{{ number_format($itemTotalSpent, 2) }}</td>
                                    <td style="text-align: right; font-weight: 600; color: {{ $itemBalance >= 0 ? 'var(--accent-cyan)' : 'var(--accent-red)' }}; vertical-align: middle;">
                                        ₹{{ number_format($itemBalance, 2) }}
                                    </td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        @if($isProjectManager)
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
                                            <td></td>
                                            <td style="text-align: right; color: var(--text-muted); font-size: 0.85rem; vertical-align: middle;">₹{{ number_format($expense['amount'], 2) }}</td>
                                            <td></td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                @if($isProjectManager)
                                                    <div style="display: inline-flex; gap: 0.4rem;">
                                                        <button onclick="openEditExpenseModal({{ $expenseIdx }}, {{ $materialIdx }}, '{{ addslashes($expense['expense_name']) }}', {{ $expense['amount'] }})" class="btn-custom" style="background: transparent; color: var(--accent-cyan); border: 1px solid var(--accent-cyan); padding: 0.2rem; font-size: 0.75rem; border-radius: 4px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; margin: 0;" title="Edit Expense">
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
                                        <td style="text-align: center; vertical-align: middle;">
                                            @if($isProjectManager)
                                                <button onclick="openAddExpenseModal({{ $materialIdx }}, '{{ addslashes($material['material']) }}')" class="btn-custom" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; background: rgba(6, 182, 212, 0.1); border: 1px solid var(--accent-cyan); color: var(--accent-cyan); cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem; margin: 0;">
                                                    <i class="bx bx-plus"></i> Add Expense
                                                </button>
                                            @else
                                                <span style="color: var(--text-muted); font-size: 0.8rem; font-style: italic;">No actions</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                @if($project->type_of_project !== 'Education Center')
                <table class="stage-table">
                    <thead>
                        <tr>
                            <th>Inspection Parameter</th>
                            <th>Inspected By</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="font-weight: 600; color: #ffffff;">Foundation Inspection</td>
                            <td>{{ $project->projectManager ? $project->projectManager->name : 'Project Manager' }}</td>
                            <td><span style="background-color: rgba(16, 185, 129, 0.15); color: var(--accent-green); padding: 0.25rem 0.5rem; border-radius: 4px; font-weight: 600; font-size: 0.85rem;">Completed</span></td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; color: #ffffff;">Superstructure Check</td>
                            <td>{{ $project->projectManager ? $project->projectManager->name : 'Project Manager' }}</td>
                            <td><span style="background-color: rgba(245, 158, 11, 0.15); color: #f59e0b; padding: 0.25rem 0.5rem; border-radius: 4px; font-weight: 600; font-size: 0.85rem;">In Progress</span></td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; color: #ffffff;">Safety Verification</td>
                            <td>Structural Auditor</td>
                            <td><span style="background-color: rgba(239, 68, 68, 0.15); color: var(--accent-red); padding: 0.25rem 0.5rem; border-radius: 4px; font-weight: 600; font-size: 0.85rem;">Pending</span></td>
                        </tr>
                    </tbody>
                </table>
                @endif
            </div>
        </div>

        <!-- ================= STAGE 6 PANEL (COMPLETION) ================= -->
        <div class="stage-content-panel" id="stage-content-6">
            <div class="detail-header-panel">
                <h2>COMPLETION STAGE</h2>
            </div>
            <div style="padding: 1.5rem;">

                @php
                    $pFiles = $project->files ?? [];
                    $compCert = $pFiles['Completion Certificate'] ?? null;
                    $measBook = $pFiles['Measurement Book'] ?? null;
                    $compPhotos = $pFiles['photos'] ?? [];
                    $compDetails = $pFiles['completion_details'] ?? [];
                @endphp

                @if($project->status === 'Approved')
                    <div style="margin-bottom: 1.5rem; background-color: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent-green); color: #8cf5c6; padding: 1rem 1.5rem; border-radius: 8px; font-weight: 600;">
                        ✓ Project successfully approved
                    </div>
                @else
                    <div class="stage-success-banner" style="margin-bottom: 1.5rem;">
                        Final Completion & Handover Ceremony (Pending Approval)
                    </div>
                @endif

                @if($project->stage === 6 && $project->status !== 'Approved')
                    <div style="margin-bottom: 2rem;">
                        @if($isCoo)
                            <form action="{{ route('projects.approve', $project->id) }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" class="btn-custom" style="background: linear-gradient(135deg, #2ecc71, #27ae60); border-color: #27ae60; color: #ffffff; cursor: pointer; font-weight: 700; padding: 0.6rem 1.8rem;">
                                    ✓ Finalize Project Approval
                                </button>
                            </form>
                        @else
                            <div style="background-color: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); color: #f59e0b; padding: 0.85rem 1.25rem; border-radius: 6px; font-size: 0.9rem; font-weight: 600; display: inline-block;">
                                <i class="bx bx-time-five"></i> Pending COO Final Approval
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Completion Documents -->
                <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--panel-border); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
                    <h3 style="color: #ffffff; font-size: 1rem; margin-top: 0; margin-bottom: 1.25rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--panel-border); padding-bottom: 0.75rem;">Completion Documents</h3>

                    <!-- Completion Certificate row -->
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; padding: 0.75rem 0; border-bottom: 1px solid var(--panel-border);">
                        <span style="font-weight: 600; color: #e0e0e0; min-width: 200px;">Completion Certificate</span>
                        <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                            @if($isProjectManager && $project->status !== 'Approved')
                                <button type="button" onclick="toggleChecklistDocument(this, 'Completion Certificate')" style="background: transparent; border: none; cursor: pointer; padding: 0; outline: none; display: flex; align-items: center; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'">
                                    @if($compCert)
                                        <i class="bx bxs-checkbox-checked" style="color: var(--accent-green); font-size: 2.2rem;"></i>
                                    @else
                                        <i class="bx bx-checkbox" style="color: var(--text-muted); font-size: 2.2rem;"></i>
                                    @endif
                                </button>
                            @else
                                @if($compCert)
                                    <span style="color: var(--accent-green); font-weight: 600; display: inline-flex; align-items: center; gap: 0.35rem; background: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent-green); padding: 0.3rem 0.65rem; border-radius: 6px; font-size: 0.8rem;">
                                        <i class="bx bx-check-circle" style="font-size: 1rem;"></i> Completed
                                    </span>
                                @else
                                    <span style="color: var(--accent-red); font-weight: 500; display: inline-flex; align-items: center; gap: 0.35rem; background: rgba(239, 68, 68, 0.1); border: 1px solid var(--accent-red); padding: 0.3rem 0.65rem; border-radius: 6px; font-size: 0.8rem;">
                                        <i class="bx bx-x-circle" style="font-size: 1rem;"></i> Pending
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- Measurement Book row -->
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; padding: 0.75rem 0;">
                        <span style="font-weight: 600; color: #e0e0e0; min-width: 200px;">Measurement Book</span>
                        <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                            @if($isProjectManager && $project->status !== 'Approved')
                                <button type="button" onclick="toggleChecklistDocument(this, 'Measurement Book')" style="background: transparent; border: none; cursor: pointer; padding: 0; outline: none; display: flex; align-items: center; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'">
                                    @if($measBook)
                                        <i class="bx bxs-checkbox-checked" style="color: var(--accent-green); font-size: 2.2rem;"></i>
                                    @else
                                        <i class="bx bx-checkbox" style="color: var(--text-muted); font-size: 2.2rem;"></i>
                                    @endif
                                </button>
                            @else
                                @if($measBook)
                                    <span style="color: var(--accent-green); font-weight: 600; display: inline-flex; align-items: center; gap: 0.35rem; background: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent-green); padding: 0.3rem 0.65rem; border-radius: 6px; font-size: 0.8rem;">
                                        <i class="bx bx-check-circle" style="font-size: 1rem;"></i> Completed
                                    </span>
                                @else
                                    <span style="color: var(--accent-red); font-weight: 500; display: inline-flex; align-items: center; gap: 0.35rem; background: rgba(239, 68, 68, 0.1); border: 1px solid var(--accent-red); padding: 0.3rem 0.65rem; border-radius: 6px; font-size: 0.8rem;">
                                        <i class="bx bx-x-circle" style="font-size: 1rem;"></i> Pending
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Photo Gallery -->
                <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--panel-border); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid var(--panel-border); padding-bottom: 0.75rem;">
                        <h3 style="color: #ffffff; font-size: 1rem; margin: 0; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Completion Photos</h3>
                        @if($isProjectManager && $project->status !== 'Approved')
                            <form action="{{ route('projects.upload_photo', $project->id) }}" method="POST" enctype="multipart/form-data" style="margin: 0; display: inline-flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
                                @csrf
                                <input type="file" name="photo" accept="image/*" required style="font-size: 0.8rem; max-width: 220px; color: var(--text-muted);">
                                <button type="submit" class="btn-custom" style="padding: 0.4rem 1rem; font-size: 0.85rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem;">
                                    <i class="bx bx-upload"></i> Upload Photo
                                </button>
                            </form>
                        @endif
                    </div>

                    @if(empty($compPhotos))
                        <div style="text-align: center; color: var(--text-muted); font-style: italic; padding: 2.5rem; border: 1px dashed var(--panel-border); border-radius: 6px;">
                            <i class="bx bx-image-add" style="font-size: 2rem; display: block; margin-bottom: 0.5rem; opacity: 0.4;"></i>
                            No completion photos uploaded yet.
                        </div>
                    @else
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.25rem;">
                            @foreach($compPhotos as $idx => $photoPath)
                                <div style="position: relative; background: var(--bg-color); border: 1px solid var(--panel-border); border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.3); transition: transform 0.2s ease;" onmouseover="this.style.transform='scale(1.01)'" onmouseout="this.style.transform='scale(1)'">
                                    <a href="{{ asset($photoPath) }}" target="_blank">
                                        <img src="{{ asset($photoPath) }}" style="width: 100%; height: 200px; object-fit: cover; display: block;" alt="Completion photo {{ $idx + 1 }}">
                                    </a>
                                    @if($isProjectManager && $project->status !== 'Approved')
                                        <form action="{{ route('projects.delete_photo', [$project->id, $idx]) }}" method="POST" style="position: absolute; top: 0.5rem; right: 0.5rem; margin: 0;" onsubmit="return confirm('Delete this photo?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="width: 30px; height: 30px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; background: rgba(231,76,60,0.85); border: none; color: #fff; cursor: pointer; box-shadow: 0 2px 6px rgba(0,0,0,0.5);" title="Delete Photo">
                                                <i class="bx bx-trash" style="font-size: 1rem;"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <div style="padding: 0.4rem 0.75rem; font-size: 0.78rem; color: var(--text-muted);">Photo {{ $idx + 1 }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Financial & Completion Details -->
                <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--panel-border); padding: 1.5rem; border-radius: 8px;">
                    <h3 style="color: #ffffff; font-size: 1rem; margin-top: 0; margin-bottom: 1.25rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--panel-border); padding-bottom: 0.75rem;">Financial & Handover Details</h3>

                    @if($isProjectManager && $project->status !== 'Approved')
                        <form action="{{ route('projects.save_completion_details', $project->id) }}" method="POST" style="margin: 0;">
                            @csrf
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">Total Amount (₹)</label>
                                    <input type="number" name="total_amount" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" value="{{ old('total_amount', $compDetails['total_amount'] ?? $project->available_budget) }}">
                                </div>
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">Amount Paid by Donor (₹)</label>
                                    <input type="number" name="amount_paid_by_donor" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" value="{{ old('amount_paid_by_donor', $compDetails['amount_paid_by_donor'] ?? 0) }}">
                                </div>
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">Community Contribution (₹)</label>
                                    <input type="number" name="community_contribution" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" value="{{ old('community_contribution', $compDetails['community_contribution'] ?? 0) }}">
                                </div>
                                <div>
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">Any Other (₹)</label>
                                    <input type="number" name="any_other" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" value="{{ old('any_other', $compDetails['any_other'] ?? 0) }}">
                                </div>
                                <div style="grid-column: span 2;">
                                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.4rem;">Geo Location (Link / Coordinates)</label>
                                    <input type="text" name="geo_location" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" placeholder="e.g. https://maps.google.com/..." value="{{ old('geo_location', $compDetails['geo_location'] ?? '') }}">
                                </div>
                            </div>
                            <button type="submit" class="btn-custom" style="padding: 0.5rem 1.5rem; cursor: pointer;">Save Details</button>
                        </form>
                    @else
                        <div class="details-grid">
                            <div class="details-label">Total Amount</div><div class="details-colon">:</div>
                            <div class="details-value">₹{{ number_format($compDetails['total_amount'] ?? $project->available_budget, 2) }}</div>

                            <div class="details-label">Amount Paid by Donor</div><div class="details-colon">:</div>
                            <div class="details-value" style="color: var(--accent-cyan);">₹{{ number_format($compDetails['amount_paid_by_donor'] ?? 0, 2) }}</div>

                            <div class="details-label">Community Contribution</div><div class="details-colon">:</div>
                            <div class="details-value" style="color: var(--accent-cyan);">₹{{ number_format($compDetails['community_contribution'] ?? 0, 2) }}</div>

                            <div class="details-label">Any Other</div><div class="details-colon">:</div>
                            <div class="details-value">₹{{ number_format($compDetails['any_other'] ?? 0, 2) }}</div>

                            <div class="details-label">Geo Location</div><div class="details-colon">:</div>
                            <div class="details-value">
                                @if(!empty($compDetails['geo_location']))
                                    <a href="{{ $compDetails['geo_location'] }}" target="_blank" style="color: var(--accent-cyan); text-decoration: underline;">{{ Str::limit($compDetails['geo_location'], 60) }}</a>
                                @else
                                    <span style="color: var(--text-muted); font-style: italic;">Not provided</span>
                                @endif
                            </div>

                            <div class="details-label">Completion Status</div><div class="details-colon">:</div>
                            <div class="details-value">
                                @if($project->status === 'Approved')
                                    <span style="background-color: rgba(16,185,129,0.2); color: var(--accent-green); padding: 0.3rem 1rem; border-radius: 4px; font-size: 0.9rem; border: 1px solid rgba(16,185,129,0.3);">APPROVED & HANDED OVER</span>
                                @else
                                    <span style="background-color: rgba(245,158,11,0.2); color: #f59e0b; padding: 0.3rem 1rem; border-radius: 4px; font-size: 0.9rem; border: 1px solid rgba(245,158,11,0.3);">PENDING FINAL SIGN-OFF</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

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

    <!-- Switch Stage Script -->
    <script>
        const allApplicationsData = @json($allApplications);

        async function toggleChecklistDocument(button, docName) {
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
                        <h3 style="color: #ffffff; font-size: 1.2rem; margin-bottom: 0.5rem;">No Application Connected</h3>
                        <p style="color: var(--text-muted); font-size: 0.9rem; max-width: 400px; margin: 0 auto;">Please connect this project to an application using the form below to view application details.</p>
                    </div>
                `;
                return;
            }

            const app = allApplicationsData.find(a => a.id == selectedId);
            if (!app) return;

            const formatVal = (val) => val ? val : '<span style="color: var(--text-muted); font-style: italic;">N/A</span>';
            
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

            const keys = Object.keys(meta).filter(k => k !== 'applicant_name' && k !== 'details');
            const half = Math.ceil(keys.length / 2);
            const col1Keys = keys.slice(0, half);
            const col2Keys = keys.slice(half);

            const formatKeyLabel = (key) => {
                return key.split('_').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
            };

            let col1Rows = `
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);">
                    <td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">Applicant Name:</td>
                    <td style="color: #ffffff; font-weight: 600;">${formatVal(app.applicant_name)}</td>
                </tr>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);">
                    <td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Status:</td>
                    <td>
                        <span style="background-color: ${app.status === 'Approved' ? 'rgba(16, 185, 129, 0.2)' : 'rgba(245, 158, 11, 0.2)'}; color: ${app.status === 'Approved' ? 'var(--accent-green)' : '#f59e0b'}; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">
                            ${app.status}
                        </span>
                    </td>
                </tr>
            `;

            col1Keys.forEach(k => {
                col1Rows += `
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);">
                        <td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">${formatKeyLabel(k)}:</td>
                        <td>${formatVal(meta[k])}</td>
                    </tr>
                `;
            });

            let col2Rows = '';
            col2Keys.forEach(k => {
                col2Rows += `
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);">
                        <td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">${formatKeyLabel(k)}:</td>
                        <td>${formatVal(meta[k])}</td>
                    </tr>
                `;
            });

            const amountText = app.amount_requested ? '₹' + Number(app.amount_requested).toLocaleString() : 'N/A';

            container.innerHTML = `
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">1. Application Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                            ${col1Rows}
                        </table>
                    </div>
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">2. Additional Specifications</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                            ${col2Rows || '<tr><td colspan="2" style="color: var(--text-muted); font-style: italic;">No additional metadata keys available.</td></tr>'}
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);">
                                <td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Proposed Budget:</td>
                                <td style="color: var(--accent-green); font-weight: 600;">${amountText}</td>
                            </tr>
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

        // Track the current actual project stage from the database
        const activeProjectStage = {{ $project->stage }};
        const isProjectApproved = "{{ $project->status === 'Approved' ? '1' : '0' }}";
        const projectType = "{{ $project->type_of_project }}";

        function switchStage(stageNum) {
            let isLocked = false;
            if (projectType === 'Education Center') {
                if (stageNum > activeProjectStage && isProjectApproved !== '1') {
                    isLocked = true;
                }
            } else {
                if (stageNum !== 1 && isProjectApproved !== '1') {
                    isLocked = true;
                }
            }

            if (isLocked) {
                const msg = projectType === 'Education Center' 
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
                if (projectType === 'Education Center') {
                    if (stageNum > activeProjectStage && isProjectApproved !== '1') {
                        isLocked = true;
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
            form.action = `/admin/projects/{{ $project->id }}/materials/${index}`;
            document.getElementById('editMaterialName').value = name;
            document.getElementById('editMaterialAmount').value = amount;
            document.getElementById('editMaterialModal').style.display = 'flex';
        }
        function closeEditMaterialModal() {
            document.getElementById('editMaterialModal').style.display = 'none';
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
        function openEditExpenseModal(index, materialIndex, name, amount) {
            const form = document.getElementById('editExpenseForm');
            form.action = `/admin/projects/{{ $project->id }}/expenses/${index}`;
            document.getElementById('editExpenseFormMaterialIndex').value = materialIndex;
            document.getElementById('editExpenseName').value = name;
            document.getElementById('editExpenseAmount').value = amount;
            document.getElementById('editExpenseModal').style.display = 'flex';
        }
        function closeEditExpenseModal() {
            document.getElementById('editExpenseModal').style.display = 'none';
        }
    </script>

    @if($isProjectManager)
    <!-- Add Material Modal -->
    <div id="addMaterialModal" style="display: none; position: fixed; z-index: 1100; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.6); align-items: center; justify-content: center; padding: 1rem;">
        <div style="background-color: var(--panel-bg); border: 1px solid var(--panel-border); padding: 2rem; border-radius: 12px; width: 100%; max-width: 450px; box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
            <h3 style="color: #ffffff; margin-top: 0; margin-bottom: 1.5rem; font-size: 1.2rem;">Add New Material / Budget Item</h3>
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
            <h3 style="color: #ffffff; margin-top: 0; margin-bottom: 1.5rem; font-size: 1.2rem;">Edit Material / Budget Item</h3>
            <form id="editMaterialForm" method="POST" style="margin: 0;">
                @csrf
                @method('PUT')
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Material / Item Name</label>
                    <input type="text" id="editMaterialName" name="material" required class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Amount (₹)</label>
                    <input type="number" id="editMaterialAmount" name="amount" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;">
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
            <h3 style="color: #ffffff; margin-top: 0; margin-bottom: 1.5rem; font-size: 1.2rem;">Add New Expense (<span id="addExpenseModalMaterialName" style="color: var(--accent-cyan);"></span>)</h3>
            <form action="{{ route('projects.add_expense', $project->id) }}" method="POST" style="margin: 0;">
                @csrf
                <input type="hidden" name="material_index" id="addExpenseFormMaterialIndex">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Expense Description / Item</label>
                    <input type="text" name="expense_name" required class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" placeholder="e.g. 50 bags purchased, worker payment">
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
            <h3 style="color: #ffffff; margin-top: 0; margin-bottom: 1.5rem; font-size: 1.2rem;">Edit Expense</h3>
            <form id="editExpenseForm" method="POST" style="margin: 0;">
                @csrf
                @method('PUT')
                <input type="hidden" name="material_index" id="editExpenseFormMaterialIndex">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Expense Description / Item</label>
                    <input type="text" id="editExpenseName" name="expense_name" required class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem;">Amount (₹)</label>
                    <input type="number" id="editExpenseAmount" name="amount" required min="0" step="any" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;">
                </div>
                <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button type="button" onclick="closeEditExpenseModal()" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); cursor: pointer;">Cancel</button>
                    <button type="submit" class="btn-custom" style="cursor: pointer;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    @endif

@endsection
