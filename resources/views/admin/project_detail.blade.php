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
            @endphp
            <div class="stage-tab {{ $class }}" id="tab-{{ $i }}" onclick="switchStage({{ $i }})">
                Stage {{ $i }}
            </div>
        @endfor
    </div>

    @php
        $authUser = auth()->user();
        $isCoo = ($authUser && ($authUser->role === 2 || strtolower($authUser->designation) === 'coo'));
    @endphp

    <!-- Success Panel -->
    @if (session('success'))
        <div style="background-color: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent-green); color: #8cf5c6; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500;">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid var(--accent-red); color: #ff8a8a; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500;">
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
                <div class="warning-box">
                    <strong>Stage 1 (Verification):</strong> Please verify the project metadata details.
                </div>

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

                <div class="details-grid">
                    <div class="details-label">Project ID</div><div class="details-colon">:</div><div class="details-value" style="color: var(--accent-cyan);">{{ $project->project_id }}</div>
                    <div class="details-label">Agency Project No</div><div class="details-colon">:</div><div class="details-value">{{ $project->agency_project_no ?? 'N/A' }}</div>
                    <div class="details-label">Donor Name</div><div class="details-colon">:</div><div class="details-value">{{ $project->donor ? $project->donor->name : 'N/A' }}</div>
                    <div class="details-label">Project Manager</div><div class="details-colon">:</div><div class="details-value">{{ $project->projectManager ? $project->projectManager->name : 'N/A' }}</div>
                    <div class="details-label">Available Budget</div><div class="details-colon">:</div><div class="details-value">₹{{ number_format($project->available_budget, 2) }}</div>
                    <div class="details-label">Type of Project</div><div class="details-colon">:</div><div class="details-value">{{ $project->type_of_project }}</div>
                    <div class="details-label">Remarks</div><div class="details-colon">:</div><div class="details-value" style="font-weight: normal; color: var(--text-muted);">{{ $project->remarks ?? 'N/A' }}</div>
                </div>

                <!-- Connect Application Form -->
                <div style="margin-top: 2rem; border-top: 1px solid var(--panel-border); padding-top: 1.5rem;">
                    <h3 style="color: #ffffff; font-size: 1.1rem; margin-bottom: 1rem;">Connect Application</h3>
                    <form action="{{ route('projects.assign_application', $project->id) }}" method="POST" style="display: flex; gap: 0.75rem; align-items: center; max-width: 500px;">
                        @csrf
                        <select name="application_id" style="background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #ffffff; padding: 0.5rem 1rem; border-radius: 6px; flex-grow: 1; outline: none; font-size: 0.9rem;" required>
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
                </div>
            </div>
        </div>

        <!-- ================= STAGE 2 PANEL (APPLICANT DETAIL) ================= -->
        <div class="stage-content-panel" id="stage-content-2">
            <div class="detail-header-panel">
                <h2>APPLICANT DETAIL</h2>
            </div>
            <div style="padding: 1.5rem;">
                @php
                    $appYear = !empty($application->created_at) ? date('y', strtotime($application->created_at)) : '24';
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
                    $appId = $application ? ('APLRCFI' . $appYear . $prefix . str_pad($application->id, 5, '0', STR_PAD_LEFT)) : 'APLRCFI24EC00001';
                @endphp
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
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                        <!-- Col 1 -->
                        <div>
                            <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">1. Applicant & Committee</h4>
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px; color: var(--text-muted);">Applicant Name:</td><td style="color: #ffffff; font-weight: 600;">Jane Smith</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Committee Name:</td><td>North City</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Reg. Number:</td><td>456256</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Year:</td><td>1990</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Location:</td><td>North City</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Village:</td><td>North Village</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Post:</td><td>Mukkam</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Panchayath:</td><td>North Panchayat</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">District / State:</td><td>North District / New State</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Contact 1 / 2:</td><td>9876543209 / <span style="color: var(--text-muted); font-style: italic;">N/A</span></td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Submitted Before?</td><td>No</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">RCFI Support?</td><td>No</td></tr>
                            </table>

                            <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">2. Mahallu Locality Details</h4>
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px; color: var(--text-muted);">Mahallu Name:</td><td><span style="color: var(--text-muted); font-style: italic;">N/A</span></td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Location:</td><td><span style="color: var(--text-muted); font-style: italic;">N/A</span></td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Village:</td><td><span style="color: var(--text-muted); font-style: italic;">N/A</span></td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">District / State:</td><td><span style="color: var(--text-muted); font-style: italic;">N/A</span> / <span style="color: var(--text-muted); font-style: italic;">N/A</span></td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Families Count:</td><td><span style="color: var(--text-muted); font-style: italic;">N/A</span></td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Requirement:</td><td><span style="color: var(--text-muted); font-style: italic;">N/A</span></td></tr>
                            </table>
                        </div>

                        <!-- Col 2 -->
                        <div>
                            <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">3. Current Status & Students</h4>
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px; color: var(--text-muted);">Has Building?</td><td><span style="color: var(--text-muted); font-style: italic;">N/A</span></td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Building Status:</td><td><span style="color: var(--text-muted); font-style: italic;">N/A</span></td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Boys Count:</td><td><span style="color: var(--text-muted); font-style: italic;">N/A</span></td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Girls Count:</td><td><span style="color: var(--text-muted); font-style: italic;">N/A</span></td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Center Nearby?</td><td><span style="color: var(--text-muted); font-style: italic;">N/A</span></td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Distance to CC (KM):</td><td><span style="color: var(--text-muted); font-style: italic;">N/A</span></td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Syllabus:</td><td><span style="color: var(--text-muted); font-style: italic;">N/A</span></td></tr>
                            </table>

                            <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">4. Proposed Project Details</h4>
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px; color: var(--text-muted);">Project Type:</td><td style="text-transform: capitalize; font-weight: 600; color: #ffffff;">Education Center</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Building Area (Sq):</td><td><span style="color: var(--text-muted); font-style: italic;">N/A</span></td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Land Area (Sq):</td><td><span style="color: var(--text-muted); font-style: italic;">N/A</span></td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Classrooms Count:</td><td><span style="color: var(--text-muted); font-style: italic;">N/A</span></td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Proposed Students:</td><td><span style="color: var(--text-muted); font-style: italic;">N/A</span></td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Proposed Budget:</td><td style="color: var(--accent-green); font-weight: 600;">$25,000</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Legal Approvals:</td><td><span style="color: var(--text-muted); font-style: italic;">N/A</span></td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Area / Zone:</td><td><span style="color: var(--text-muted); font-style: italic;">N/A</span></td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Review Status:</td><td style="font-weight: 600; color: #ffffff;">Pending</td></tr>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- ================= STAGE 3 PANEL (FILES) ================= -->
        <div class="stage-content-panel" id="stage-content-3">
            <div class="detail-header-panel">
                <h2>FILES</h2>
            </div>
            <div style="padding: 1.5rem;">
                <div class="stage-success-banner">
                    Uploaded files are Approved
                </div>

                @if($project->stage === 3)
                    <div style="margin-bottom: 1.5rem;">
                        <form action="{{ route('projects.approve', $project->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-custom" style="background: #eb3b5a;">
                                Approve & Promote to Stage 4
                            </button>
                        </form>
                    </div>
                @endif

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
                                'Land document', 'Possession certificate', 'Recommendation letter',
                                'Committee minutes', 'Permit copy', 'Plan', 'Tender schedule sheet',
                                'Site study', 'Quotations', 'Quotations approval form'
                            ];
                        @endphp
                        @foreach($docs as $doc)
                            <tr>
                                <td style="font-weight: 600; color: #ffffff;">{{ $doc }}</td>
                                <td style="color: var(--accent-red); font-weight: 500;">No File</td>
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

                @if($project->stage === 4)
                    <div style="margin-bottom: 1.5rem;">
                        <form action="{{ route('projects.approve', $project->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-custom" style="background: #eb3b5a;">
                                Approve & Promote to Stage 5
                            </button>
                        </form>
                    </div>
                @endif

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                    <button class="btn-custom" style="background: #4b6584;" onclick="alert('Exporting stage budget to Excel...')">
                        Download Excel
                    </button>
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
                        <tr>
                            <td style="font-weight: 600; color: #ffffff;">cement</td>
                            <td style="text-align: right; font-weight: 600; color: #ffffff;">₹ 8000</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 600; color: #ffffff;">metal</td>
                            <td style="text-align: right; font-weight: 600; color: #ffffff;">₹ 8000</td>
                        </tr>
                        <tr style="border-top: 2px solid var(--panel-border);">
                            <td style="font-weight: 700; color: var(--accent-cyan);">Total</td>
                            <td style="text-align: right; font-weight: 700; color: var(--accent-cyan);">₹ 16000.00</td>
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

                @if($project->stage === 5)
                    <div style="margin-bottom: 1.5rem;">
                        <form action="{{ route('projects.approve', $project->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-custom" style="background: #eb3b5a;">
                                Approve & Promote to Stage 6
                            </button>
                        </form>
                    </div>
                @endif

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
            </div>
        </div>

        <!-- ================= STAGE 6 PANEL (FINAL HANDOVER) ================= -->
        <div class="stage-content-panel" id="stage-content-6">
            <div class="detail-header-panel">
                <h2>FINAL HANDOVER & COMPLETION</h2>
            </div>
            <div style="padding: 1.5rem;">
                <div class="stage-success-banner">
                    Final Completion & Handover Ceremony
                </div>

                @if($project->stage === 6 && $project->status !== 'Approved')
                    <div style="margin-bottom: 1.5rem;">
                        <form action="{{ route('projects.approve', $project->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-custom" style="background: #2ecc71;">
                                Finalize Project Approval
                            </button>
                        </form>
                    </div>
                @endif

                <div class="details-grid">
                    <div class="details-label">Completion Status</div><div class="details-colon">:</div>
                    <div class="details-value">
                        @if($project->status === 'Approved')
                            <span style="background-color: rgba(16, 185, 129, 0.2); color: var(--accent-green); padding: 0.35rem 1rem; border-radius: 4px; font-size: 0.9rem; border: 1px solid rgba(16, 185, 129, 0.3);">APPROVED & HANDED OVER</span>
                        @else
                            <span style="background-color: rgba(245, 158, 11, 0.2); color: #f59e0b; padding: 0.35rem 1rem; border-radius: 4px; font-size: 0.9rem; border: 1px solid rgba(245, 158, 11, 0.3);">PENDING FINAL SIGN-OFF</span>
                        @endif
                    </div>
                    <div class="details-label">Handover Certificate</div><div class="details-colon">:</div><div class="details-value" style="color: var(--accent-red);">No Certificate Uploaded</div>
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
        // Track the current actual project stage from the database
        const activeProjectStage = {{ $project->stage }};

        function switchStage(stageNum) {
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

        // Initialize display to show the active project stage panel
        document.addEventListener('DOMContentLoaded', () => {
            switchStage(activeProjectStage);
        });
    </script>

@endsection
