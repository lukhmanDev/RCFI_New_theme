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

        window.openAddProgrammeModal = function openAddProgrammeModal() {
            const modal = document.getElementById('addProgrammeModal');
            if (modal) {
                document.body.appendChild(modal);
                modal.style.setProperty('z-index', '999999', 'important');
                modal.style.setProperty('display', 'flex', 'important');
            }
        };
        window.closeAddProgrammeModal = function closeAddProgrammeModal() {
            const modal = document.getElementById('addProgrammeModal');
            if (modal) {
                modal.style.setProperty('display', 'none', 'important');
            }
        };

        document.addEventListener('click', function(e) {
            const btn = e.target.closest('#btn-add-programme-main, .btn-add-programme-trigger');
            if (btn) {
                e.preventDefault();
                e.stopPropagation();
                if (typeof window.openAddProgrammeModal === 'function') {
                    window.openAddProgrammeModal();
                } else {
                    const modal = document.getElementById('addProgrammeModal');
                    if (modal) {
                        document.body.appendChild(modal);
                        modal.style.setProperty('z-index', '999999', 'important');
                        modal.style.setProperty('display', 'flex', 'important');
                    }
                }
            }
        });

        function restoreActiveStage() {
            try {
                const savedStage = sessionStorage.getItem('current_project_stage_{{ $project->id ?? 0 }}');
                if (savedStage) {
                    switchStage(parseInt(savedStage, 10));
                }
            } catch(e) {}
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                restoreActiveStage();
                const addModal = document.getElementById('addProgrammeModal');
                if (addModal && addModal.parentElement !== document.body) {
                    document.body.appendChild(addModal);
                }
            });
        } else {
            restoreActiveStage();
            const addModal = document.getElementById('addProgrammeModal');
            if (addModal && addModal.parentElement !== document.body) {
                document.body.appendChild(addModal);
            }
        }

    </script>

    <!-- Stage Navigation Tabs (Interactive Navigation) -->
    <div class="stages-tabs" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--panel-border); margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; padding-bottom: 0.5rem;">
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            @php
                $isSocialAidProject = in_array($project->type_of_project, ['Orphan Care', 'Differently Abled', 'Family Aid']);
                $maxStages = $isSocialAidProject ? 3 : 6;
                $projectRouteKeys = [
                    'Orphan Care' => 'orphan_care',
                    'Differently Abled' => 'differently_abled',
                    'Family Aid' => 'family_aid',
                ];
                $projectRouteSlugs = [
                    'Orphan Care' => 'orphan-care',
                    'Differently Abled' => 'differently-abled',
                    'Family Aid' => 'family-aid',
                ];
                $projectRouteKey = $projectRouteKeys[$project->type_of_project] ?? 'orphan_care';
                $projectRouteSlug = $projectRouteSlugs[$project->type_of_project] ?? 'orphan-care';

                $socialAidStageLabels = [
                    1 => 'Profile',
                    2 => 'Scholarship',
                    3 => 'Report',
                ];
            @endphp
            @for($i = 1; $i <= $maxStages; $i++)
                @php
                    $isActive = $project->stage == $i;
                    $isCompleted = $project->stage > $i;
                    $class = $isActive ? 'active' : ($isCompleted ? 'completed' : '');
                    
                    //   Stage 1 & Stage 2: always accessible
                    //   Stage 3 & Stage 4: unlocks when an application is assigned in Stage 2
                    //   Stage 5 & Stage 6: unlocks when Stage 4 is approved
                    if (in_array($project->type_of_project, ['Education Center', 'Cultural Center', 'Hospital or Clinics', 'Shops and Others', 'House', 'Drinking Water - Group Level', 'Drinking Water - Individual Level', 'General'])) {
                        if ($i <= 2) {
                            $isLocked = false;
                        } elseif ($i == 3 || $i == 4) {
                            $isLocked = empty($project->application_id);
                        } else { // stage 5 or 6
                            $isLocked = empty($project->application_id) || ($project->stage < 5 && $project->status !== 'Approved' && $project->status !== 'Completed');
                        }
                    } else {
                        if ($isSocialAidProject) {
                            $isLocked = false;
                        } else {
                            $isLocked = ($project->status !== 'Approved' && $project->status !== 'Completed' && $i > 1);
                        }
                    }

                    if ($isLocked) {
                        $class .= ' locked';
                    }

                    $stageTabTitle = $isSocialAidProject ? ($socialAidStageLabels[$i] ?? "Stage {$i}") : "Stage {$i}";
                @endphp
                <div class="stage-tab {{ $class }}" id="tab-{{ $i }}" onclick="switchStage({{ $i }})">
                    @if($isLocked)
                        <i class="bx bx-lock-alt" style="margin-right: 0.25rem;"></i>
                    @endif
                    {{ $stageTabTitle }}
                </div>
            @endfor
        </div>
    </div>


    @php
        $authUser = auth()->user();
        $isSuperAdmin = ($authUser && ($authUser->isSuperAdmin() || $authUser->role == 1 || $authUser->role === 'super_admin'));
        $designationLower = strtolower($authUser->designation ?? '');
        $isCoo = ($authUser && ($authUser->isCoo() || $designationLower === 'coo' || str_contains($designationLower, 'chief operating officer') || str_contains($designationLower, 'coo')));
        $isHod = ($authUser && ($authUser->isHod() || $designationLower === 'hod' || str_contains($designationLower, 'head of department') || str_contains($designationLower, 'hod')));
        $isPmOnly = ($authUser && ($authUser->isPm() || str_contains($designationLower, 'project manager') || $designationLower === 'project manager'));
        $isEngineerOnly = ($authUser && ($authUser->isEngineer() || strtolower($authUser->designation ?? '') === 'engineer'));
        $isSocialAid = ($authUser && ((method_exists($authUser, 'isSocialAid') ? $authUser->isSocialAid() : false) || in_array($authUser->role, [8, '8', 'social_aid', 'Social Aid', 'Social Aid Manager']) || str_contains($designationLower, 'social aid')));
        $canManageFinance = ($isSuperAdmin || $isCoo || $isHod);
        $canDeleteFinanceRow = ($isSuperAdmin || $isCoo || $isHod);

        $isProjectManager = ($authUser && ($isSuperAdmin || $isCoo || $isHod || $isPmOnly || $isEngineerOnly || in_array($authUser->role, [1, 2, 3, 4, 6, 'super_admin', 'coo', 'project_manager', 'hod', 'engineer']) || in_array(strtolower($authUser->designation ?? ''), ['project manager', 'engineer', 'coo', 'hod', 'super admin', 'admin'])));
        $isLockedForEditing = ($project->status === 'Completed' && !$isSuperAdmin);
        
        $isLockedForEditing = ($project->status === 'Completed' || $project->status === 'Approved');
        $canEditStatus = ($isCoo || $isHod || $isSuperAdmin) && !$isLockedForEditing;
        $isSixStage = in_array($project->type_of_project, ['Education Center', 'Cultural Center', 'Hospital or Clinics', 'Shops and Others', 'House', 'Drinking Water - Group Level', 'Drinking Water - Individual Level', 'General']);
        $isStage4Approved = false;
        if ($isSixStage) {
            $isStage4Approved = ($project->stage >= 5 || in_array($project->status, ['Approved', 'Completed']));
        }
        
        if ($isSixStage) {
            $canAssignApplication = ($isPmOnly || $isEngineerOnly || $isHod || $isCoo || $isSuperAdmin) && !$isStage4Approved;
        } else {
            $canAssignApplication = ($isHod || $isCoo || $isSuperAdmin) && !$isLockedForEditing;
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
                @if(!in_array($project->type_of_project, ['Education Center', 'Cultural Center', 'Hospital or Clinics', 'Shops and Others', 'House', 'Drinking Water - Group Level', 'Drinking Water - Individual Level', 'General']))
                    @if($project->status === 'Approved')
                        <div style="margin-bottom: 1.5rem; background-color: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #8cf5c6; padding: 0.85rem 1.25rem; border-radius: 6px; font-size: 0.9rem; font-weight: 600; display: inline-block;">
                            <i class="bx bx-check-circle"></i> Project Approved & Active
                        </div>
                    @endif
                @endif

                @if($isSocialAidProject && $application)
                    @php
                        $studentPhoto = $application->student_photo 
                            ?? ($application->meta['student_photo'] ?? null) 
                            ?? ($application->applicantAddress ? $application->applicantAddress->student_photo : null);
                        $studentPhotoUrl = $studentPhoto ? (Str::startsWith($studentPhoto, ['http://', 'https://']) ? $studentPhoto : asset(ltrim($studentPhoto, '/'))) : null;
                    @endphp

                    <!-- Main Application Details Panel Container -->
                    <div style="background-color: var(--panel-bg); border: 1px solid var(--panel-border); border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.05); box-sizing: border-box; margin-bottom: 2.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--panel-border); padding-bottom: 0.75rem; margin-bottom: 1.25rem;">
                            <h4 style="color: var(--accent-cyan); font-size: 0.95rem; font-weight: 700; text-transform: uppercase; margin: 0; letter-spacing: 0.05em;">
                                <i class="bx bx-user-pin" style="font-size: 1.1rem; vertical-align: middle; margin-right: 0.35rem;"></i> {{ $project->type_of_project === 'Orphan Care' ? 'Student & Application Details' : 'Beneficiary & Application Details' }}
                            </h4>
                            <button type="button" id="edit-address-btn" onclick="toggleAddressEdit()" class="btn-custom" style="padding: 0.4rem 0.85rem; font-size: 0.8rem; border-radius: 6px;">
                                <i class="bx bx-edit"></i> Edit Details
                            </button>
                        </div>

                        <!-- Display Details View (Grid with Details on Left & Photo Card on Right) -->
                        <div id="address-display-view">
                            <div style="display: grid; grid-template-columns: 1fr 240px; gap: 1.5rem; align-items: start;">
                                <!-- Left Column: Details Key-Value List -->
                                <div class="details-grid">
                                    <div class="details-label">Applicant Name</div><div class="details-colon">:</div><div class="details-value" style="font-weight: 700; color: #ffffff;">{{ $application->applicant_name ?? 'N/A' }}</div>
                                    <div class="details-label">Gender</div><div class="details-colon">:</div><div class="details-value">{{ $application->gender ?? 'N/A' }}</div>
                                    <div class="details-label">Date of Birth</div><div class="details-colon">:</div><div class="details-value">{{ !empty($application->dob) ? date('d-M-Y', strtotime($application->dob)) : 'N/A' }} @if(!empty($application->age))(Age: {{ $application->age }})@endif</div>
                                    <div class="details-label">Aadhar Number</div><div class="details-colon">:</div><div class="details-value">{{ $application->aadhar_number ?? 'N/A' }}</div>
                                    
                                    <div class="details-label">Father's Name</div><div class="details-colon">:</div><div class="details-value">{{ $application->father_name ?? 'N/A' }}</div>
                                    @if(!empty($application->grandfather_name))
                                        <div class="details-label">Grandfather's Name</div><div class="details-colon">:</div><div class="details-value">{{ $application->grandfather_name }}</div>
                                    @endif
                                    <div class="details-label">Mother's Name</div><div class="details-colon">:</div><div class="details-value">{{ $application->mother_name ?? 'N/A' }}</div>
                                    @if(!empty($application->mothers_father_name))
                                        <div class="details-label">Mother's Father Name</div><div class="details-colon">:</div><div class="details-value">{{ $application->mothers_father_name }}</div>
                                    @endif
                                    <div class="details-label">Guardian</div><div class="details-colon">:</div><div class="details-value">{{ $application->guardian_name ?? 'N/A' }} @if(!empty($application->guardian_relation))(Relation: {{ $application->guardian_relation }})@endif</div>

                                    @if(!empty($application->father_death_date) || !empty($application->father_death_cause))
                                        <div class="details-label">Father Death Info</div><div class="details-colon">:</div>
                                        <div class="details-value">Date: {{ !empty($application->father_death_date) ? date('d-M-Y', strtotime($application->father_death_date)) : 'N/A' }} | Cause: {{ $application->father_death_cause ?? 'N/A' }}</div>
                                    @endif
                                    @if(!empty($application->mother_alive_status))
                                        <div class="details-label">Mother Status</div><div class="details-colon">:</div>
                                        <div class="details-value">Alive: {{ $application->mother_alive_status }} @if(!empty($application->mother_remarried_status))| Remarried: {{ $application->mother_remarried_status }}@endif @if(!empty($application->mother_death_date))| Death Date: {{ date('d-M-Y', strtotime($application->mother_death_date)) }}@endif</div>
                                    @endif

                                    @if(!empty($application->siblings_total) || !empty($application->siblings_male) || !empty($application->siblings_female))
                                        <div class="details-label">Siblings</div><div class="details-colon">:</div>
                                        <div class="details-value">Total: {{ $application->siblings_total ?? (($application->siblings_male ?? 0) + ($application->siblings_female ?? 0)) }} (Brothers: {{ $application->siblings_male ?? 0 }}, Sisters: {{ $application->siblings_female ?? 0 }})</div>
                                    @endif
                                    @if(!empty($application->current_beneficiaries))
                                        <div class="details-label">Current Beneficiaries</div><div class="details-colon">:</div><div class="details-value">{{ $application->current_beneficiaries }}</div>
                                    @endif

                                    <div class="details-label">House Name</div><div class="details-colon">:</div><div class="details-value" id="display-house_name">{{ $application->house_name ?? 'N/A' }}</div>
                                    <div class="details-label">Place</div><div class="details-colon">:</div><div class="details-value" id="display-place">{{ $application->place ?? 'N/A' }}</div>
                                    <div class="details-label">Post Office</div><div class="details-colon">:</div><div class="details-value" id="display-post_office">{{ $application->post_office ?? 'N/A' }}</div>
                                    <div class="details-label">Village</div><div class="details-colon">:</div><div class="details-value" id="display-village">{{ $application->village ?? 'N/A' }}</div>
                                    <div class="details-label">Panchayat</div><div class="details-colon">:</div><div class="details-value" id="display-panchayat">{{ $application->panchayat ?? 'N/A' }}</div>
                                    <div class="details-label">District</div><div class="details-colon">:</div><div class="details-value" id="display-district">{{ $application->district ?? 'N/A' }}</div>
                                    <div class="details-label">State</div><div class="details-colon">:</div><div class="details-value" id="display-state">{{ $application->state ?? 'N/A' }}</div>
                                    @if(!empty($application->pin_code))
                                        <div class="details-label">PIN Code</div><div class="details-colon">:</div><div class="details-value">{{ $application->pin_code }}</div>
                                    @endif
                                    <div class="details-label">Mobile 1</div><div class="details-colon">:</div><div class="details-value" id="display-mobile_1">{{ $application->mobile_1 ?? $application->contact_number_1 ?? $application->mobile ?? optional($application->applicantAddress)->contact_number_1 ?? 'N/A' }}</div>
                                    <div class="details-label">Mobile 2</div><div class="details-colon">:</div><div class="details-value" id="display-mobile_2">{{ $application->mobile_2 ?? $application->contact_number_2 ?? optional($application->applicantAddress)->contact_number_2 ?? 'N/A' }}</div>
                                    @if(!empty($application->whatsapp_number))
                                        <div class="details-label">WhatsApp Number</div><div class="details-colon">:</div><div class="details-value">{{ $application->whatsapp_number }}</div>
                                    @endif

                                    @if(!empty($application->school_name) || !empty($application->school_class))
                                        <div class="details-label">School Education</div><div class="details-colon">:</div><div class="details-value">{{ $application->school_name ?? 'N/A' }} @if(!empty($application->school_class))(Class: {{ $application->school_class }})@endif</div>
                                    @endif
                                    @if(!empty($application->madrassa_name) || !empty($application->madrassa_class))
                                        <div class="details-label">Madrassa Education</div><div class="details-colon">:</div><div class="details-value">{{ $application->madrassa_name ?? 'N/A' }} @if(!empty($application->madrassa_class))(Class: {{ $application->madrassa_class }})@endif</div>
                                    @endif
                                    @if(!empty($application->not_studying_reason))
                                        <div class="details-label">Not Studying Reason</div><div class="details-colon">:</div><div class="details-value" style="color: #ff8a8a;">{{ $application->not_studying_reason }}</div>
                                    @endif
                                    @if(!empty($application->health_status))
                                        <div class="details-label">Health Status</div><div class="details-colon">:</div><div class="details-value">{{ $application->health_status }}</div>
                                    @endif

                                    @if(!empty($application->monthly_income) || !empty($application->monthly_expense))
                                        <div class="details-label">Monthly Finance</div><div class="details-colon">:</div><div class="details-value">Income: ₹{{ $application->monthly_income ?? '0' }} / Expense: ₹{{ $application->monthly_expense ?? '0' }}</div>
                                    @endif
                                    @if(!empty($application->house_type))
                                        <div class="details-label">House Type</div><div class="details-colon">:</div><div class="details-value">{{ $application->house_type }}</div>
                                    @endif
                                    @if(!empty($application->sponsorship_details))
                                        <div class="details-label">Sponsorship Info</div><div class="details-colon">:</div><div class="details-value">{{ $application->sponsorship_details }}</div>
                                    @endif

                                    @if(!empty($application->recommender_name))
                                        <div class="details-label">Recommender</div><div class="details-colon">:</div>
                                        <div class="details-value">{{ $application->recommender_name }} @if(!empty($application->recommender_org))({{ $application->recommender_org }} - {{ $application->recommender_position }})@endif @if(!empty($application->recommender_phone))Ph: {{ $application->recommender_phone }}@endif</div>
                                    @endif
                                </div>

                                <!-- Right Column: Student/Beneficiary Photo Card (Inside Application Details) -->
                                <div style="background-color: rgba(255,255,255,0.02); border: 1px solid var(--panel-border); border-radius: 12px; padding: 1.25rem; text-align: center; box-sizing: border-box;">
                                    <h5 style="color: var(--accent-cyan); font-size: 0.85rem; font-weight: 700; text-transform: uppercase; margin-top: 0; margin-bottom: 1rem; letter-spacing: 0.05em;">{{ $project->type_of_project === 'Orphan Care' ? 'Student Photo' : 'Beneficiary Photo' }}</h5>
                                    
                                    <div style="width: 160px; height: 160px; border-radius: 12px; border: 2px dashed var(--panel-border); margin: 0 auto 1.25rem auto; display: flex; align-items: center; justify-content: center; overflow: hidden; background-color: rgba(0,0,0,0.1);">
                                        @if($studentPhotoUrl)
                                            <img src="{{ $studentPhotoUrl }}" onerror="this.onerror=null; this.parentNode.innerHTML='<div style=\'text-align: center; color: var(--text-muted); padding: 1rem;\'><i class=\'bx bx-image-add\' style=\'font-size: 2.5rem; margin-bottom: 0.5rem; display: block; color: var(--accent-cyan);\'></i><span style=\'font-size: 0.75rem;\'>No Photo Uploaded</span></div>';" alt="Photo" style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            <div style="text-align: center; color: var(--text-muted); padding: 1rem;">
                                                <i class="bx bx-image-add" style="font-size: 2.5rem; margin-bottom: 0.5rem; display: block; color: var(--accent-cyan);"></i>
                                                <span style="font-size: 0.75rem;">No Photo Uploaded</span>
                                            </div>
                                        @endif
                                    </div>

                                    <form action="{{ route('projects.' . $projectRouteKey . '.upload_photo', $project->id) }}" method="POST" enctype="multipart/form-data" style="margin-top: 0.75rem;">
                                        @csrf
                                        <button type="button" class="btn-custom" onclick="document.getElementById('student_photo_input').click()" style="width: 100%; margin-bottom: 0.5rem; justify-content: center; border-radius: 6px; padding: 0.45rem; font-size: 0.8rem; background: linear-gradient(135deg, #10b981, #059669); border: none; color: #fff; font-weight: 600;">
                                            <i class="bx bx-upload"></i> {{ $studentPhotoUrl ? 'Change Photo' : 'Upload Photo' }}
                                        </button>
                                        <input type="file" name="student_photo" id="student_photo_input" accept="image/*" style="display: none;" onchange="this.form.submit()">
                                    </form>
                                    @if($studentPhotoUrl && ($isSuperAdmin || $isCoo || $isHod))
                                        <form action="{{ route('projects.' . $projectRouteKey . '.delete_photo', $project->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this photo?')" style="margin-top: 0.35rem;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-custom" style="width: 100%; background: linear-gradient(135deg, #ef4444, #dc2626); box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15); justify-content: center; border-radius: 6px; padding: 0.45rem; font-size: 0.8rem; color: #fff; font-weight: 600; border: none;">
                                                <i class="bx bx-trash"></i> Delete Photo
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                            <!-- Edit Details Form -->
                            <form id="address-edit-form" action="{{ route('projects.' . $projectRouteKey . '.update_address', $project->id) }}" method="POST" onsubmit="handleAddressSubmit(event)" style="display: none;">
                                @csrf
                                
                                <!-- Section 1: Basic & Personal Info -->
                                <h5 style="color: var(--accent-cyan); font-size: 0.85rem; font-weight: 700; text-transform: uppercase; margin-top: 0; margin-bottom: 0.85rem; letter-spacing: 0.05em; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 0.4rem;">
                                    1. Basic &amp; Personal Info
                                </h5>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                                    <div class="form-group-custom" style="grid-column: span 2; margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Applicant Name *</label>
                                        <input type="text" name="applicant_name" value="{{ $application->applicant_name }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;" required>
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Father's Name *</label>
                                        <input type="text" name="father_name" value="{{ $application->father_name }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Grandfather's Name</label>
                                        <input type="text" name="grandfather_name" value="{{ $application->grandfather_name }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Mother's Name *</label>
                                        <input type="text" name="mother_name" value="{{ $application->mother_name }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Mother's Father Name</label>
                                        <input type="text" name="mothers_father_name" value="{{ $application->mothers_father_name }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>

                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Gender</label>
                                        <select name="gender" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                            <option value="">Select Gender</option>
                                            <option value="Male" {{ ($application->gender ?? '') === 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ ($application->gender ?? '') === 'Female' ? 'selected' : '' }}>Female</option>
                                            <option value="Other" {{ ($application->gender ?? '') === 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Date of Birth</label>
                                        <input type="date" name="dob" value="{{ !empty($application->dob) ? date('Y-m-d', strtotime($application->dob)) : '' }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Age</label>
                                        <input type="text" name="age" value="{{ $application->age }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Aadhaar Number</label>
                                        <input type="text" name="aadhar_number" value="{{ $application->aadhar_number }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>

                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Guardian Name</label>
                                        <input type="text" name="guardian_name" value="{{ $application->guardian_name }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Guardian Relation</label>
                                        <input type="text" name="guardian_relation" value="{{ $application->guardian_relation }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>
                                </div>

                                <!-- Section 2: Parental Death & Family Details -->
                                <h5 style="color: var(--accent-cyan); font-size: 0.85rem; font-weight: 700; text-transform: uppercase; margin-top: 1rem; margin-bottom: 0.85rem; letter-spacing: 0.05em; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 0.4rem;">
                                    2. Parental Death &amp; Family Details
                                </h5>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Father Death Date</label>
                                        <input type="date" name="father_death_date" value="{{ !empty($application->father_death_date) ? date('Y-m-d', strtotime($application->father_death_date)) : '' }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Father Cause of Death</label>
                                        <input type="text" name="father_death_cause" value="{{ $application->father_death_cause }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>

                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Mother Alive Status</label>
                                        <select name="mother_alive_status" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                            <option value="Yes" {{ ($application->mother_alive_status ?? 'Yes') === 'Yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="No" {{ ($application->mother_alive_status ?? '') === 'No' ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Mother Re-Married Status</label>
                                        <input type="text" name="mother_remarried_status" value="{{ $application->mother_remarried_status }}" placeholder="e.g. Yes / No" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>

                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Mother Death Date</label>
                                        <input type="date" name="mother_death_date" value="{{ !empty($application->mother_death_date) ? date('Y-m-d', strtotime($application->mother_death_date)) : '' }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Mother Cause of Death</label>
                                        <input type="text" name="mother_death_cause" value="{{ $application->mother_death_cause }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>

                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Brothers</label>
                                        <input type="number" name="siblings_male" value="{{ $application->siblings_male }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Sisters</label>
                                        <input type="number" name="siblings_female" value="{{ $application->siblings_female }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>

                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Current Beneficiaries</label>
                                        <input type="number" name="current_beneficiaries" value="{{ $application->current_beneficiaries }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Sponsorship Details</label>
                                        <input type="text" name="sponsorship_details" value="{{ $application->sponsorship_details }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>

                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Monthly Income (₹)</label>
                                        <input type="text" name="monthly_income" value="{{ $application->monthly_income }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Monthly Expense (₹)</label>
                                        <input type="text" name="monthly_expense" value="{{ $application->monthly_expense }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>
                                </div>

                                <!-- Section 3: Education & Health Details -->
                                <h5 style="color: var(--accent-cyan); font-size: 0.85rem; font-weight: 700; text-transform: uppercase; margin-top: 1rem; margin-bottom: 0.85rem; letter-spacing: 0.05em; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 0.4rem;">
                                    3. Education &amp; Health Details
                                </h5>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                                    <div class="form-group-custom" style="grid-column: span 2; margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Type of House</label>
                                        <select name="house_type" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                            <option value="">Select House Type</option>
                                            <option value="Own House" {{ ($application->house_type ?? '') === 'Own House' ? 'selected' : '' }}>Own House</option>
                                            <option value="Family House" {{ ($application->house_type ?? '') === 'Family House' ? 'selected' : '' }}>Family House</option>
                                            <option value="Rental" {{ ($application->house_type ?? '') === 'Rental' ? 'selected' : '' }}>Rental</option>
                                            <option value="Flat" {{ ($application->house_type ?? '') === 'Flat' ? 'selected' : '' }}>Flat</option>
                                            <option value="Others" {{ ($application->house_type ?? '') === 'Others' ? 'selected' : '' }}>Others</option>
                                        </select>
                                    </div>

                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">School Name</label>
                                        <input type="text" name="school_name" value="{{ $application->school_name }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">School Class</label>
                                        <input type="text" name="school_class" value="{{ $application->school_class }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>

                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Madrassa Name</label>
                                        <input type="text" name="madrassa_name" value="{{ $application->madrassa_name }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Madrassa Class</label>
                                        <input type="text" name="madrassa_class" value="{{ $application->madrassa_class }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>

                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Not Studying Reason</label>
                                        <input type="text" name="not_studying_reason" value="{{ $application->not_studying_reason }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Health Status</label>
                                        <input type="text" name="health_status" value="{{ $application->health_status }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>
                                </div>

                                <!-- Section 4: Address & Contact Details -->
                                <h5 style="color: var(--accent-cyan); font-size: 0.85rem; font-weight: 700; text-transform: uppercase; margin-top: 1rem; margin-bottom: 0.85rem; letter-spacing: 0.05em; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 0.4rem;">
                                    4. Address &amp; Contact Details
                                </h5>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">House Name</label>
                                        <input type="text" name="house_name" value="{{ $application->house_name }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Place</label>
                                        <input type="text" name="place" value="{{ $application->place }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Post Office</label>
                                        <input type="text" name="post_office" value="{{ $application->post_office }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Village</label>
                                        <input type="text" name="village" value="{{ $application->village }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Panchayat</label>
                                        <input type="text" name="panchayat" value="{{ $application->panchayat }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">District</label>
                                        <input type="text" name="district" value="{{ $application->district }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">State</label>
                                        <input type="text" name="state" value="{{ $application->state }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">PIN Code</label>
                                        <input type="text" name="pin_code" value="{{ $application->pin_code }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Mobile 1</label>
                                        <input type="text" name="mobile_1" value="{{ $application->mobile_1 ?? $application->contact_number_1 ?? $application->mobile ?? optional($application->applicantAddress)->contact_number_1 ?? '' }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Mobile 2</label>
                                        <input type="text" name="mobile_2" value="{{ $application->mobile_2 ?? $application->contact_number_2 ?? optional($application->applicantAddress)->contact_number_2 ?? '' }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>
                                    <div class="form-group-custom" style="grid-column: span 2; margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">WhatsApp Number</label>
                                        <input type="text" name="whatsapp_number" value="{{ $application->whatsapp_number }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>

                                    <div class="form-group-custom" style="grid-column: span 2; margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Additional Notes</label>
                                        <textarea name="additional_note" rows="2" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">{{ $application->additional_note }}</textarea>
                                    </div>
                                </div>

                                <!-- Section 5: Recommendation Details -->
                                <h5 style="color: var(--accent-cyan); font-size: 0.85rem; font-weight: 700; text-transform: uppercase; margin-top: 1rem; margin-bottom: 0.85rem; letter-spacing: 0.05em; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 0.4rem;">
                                    5. Recommendation Details
                                </h5>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Recommender Name</label>
                                        <input type="text" name="recommender_name" value="{{ $application->recommender_name }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Organization</label>
                                        <select name="recommender_org" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                            <option value="">Select Organization</option>
                                            <option value="KMJ" {{ ($application->recommender_org ?? '') === 'KMJ' ? 'selected' : '' }}>KMJ</option>
                                            <option value="SYS" {{ ($application->recommender_org ?? '') === 'SYS' ? 'selected' : '' }}>SYS</option>
                                            <option value="SSF" {{ ($application->recommender_org ?? '') === 'SSF' ? 'selected' : '' }}>SSF</option>
                                            <option value="Others" {{ ($application->recommender_org ?? '') === 'Others' ? 'selected' : '' }}>Others</option>
                                        </select>
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Position</label>
                                        <input type="text" name="recommender_position" value="{{ $application->recommender_position }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Phone Number</label>
                                        <input type="text" name="recommender_phone" value="{{ $application->recommender_phone }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>
                                </div>

                                <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 1.5rem; border-top: 1px solid var(--panel-border); padding-top: 1rem;">
                                    <button type="button" onclick="closeAddressEdit()" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); border-radius: 6px; padding: 0.5rem 1rem; font-size: 0.85rem;">Cancel</button>
                                    <button type="submit" class="btn-custom" style="border-radius: 6px; padding: 0.5rem 1.25rem; font-size: 0.85rem; background: linear-gradient(135deg, #10b981, #059669); border: none; color: #fff; font-weight: 700;">Save Details</button>
                                </div>
                            </form>
                        </div>

                    <script>
                        function toggleAddressEdit() {
                            const display = document.getElementById('address-display-view');
                            const form = document.getElementById('address-edit-form');
                            const btn = document.getElementById('edit-address-btn');
                            if (form.style.display === 'none') {
                                form.style.display = 'block';
                                display.style.display = 'none';
                                btn.style.display = 'none';
                            } else {
                                closeAddressEdit();
                            }
                        }

                        function closeAddressEdit() {
                            const display = document.getElementById('address-display-view');
                            const form = document.getElementById('address-edit-form');
                            const btn = document.getElementById('edit-address-btn');
                            if (form) form.style.display = 'none';
                            if (display) display.style.display = 'block';
                            if (btn) btn.style.display = 'inline-flex';
                        }

                        function handleAddressSubmit(event) {
                            event.preventDefault();
                            const form = event.target;
                            const formData = new FormData(form);

                            fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    closeAddressEdit();
                                    window.location.reload();
                                } else {
                                    alert(data.message || 'Failed to update details.');
                                }
                            })
                            .catch(error => {
                                console.error('Error updating details:', error);
                                form.submit();
                            });
                        }
                    </script>
                @endif

                @if($isSocialAidProject && $application)
                    @php
                        $formattedAppId = 'APLRCFI' . (!empty($application->created_at) ? date('y', strtotime($application->created_at)) : '24') . ($project->type_of_project === 'Orphan Care' ? 'OC' : ($project->type_of_project === 'Differently Abled' ? 'DA' : 'FA')) . str_pad($application->id, 5, '0', STR_PAD_LEFT);
                        $appCatSlug = str_replace('_', '-', $projectRouteKey);
                        $appLinkUrl = route('applications.approved.category', $appCatSlug);

                        $agencyName = null;
                        if (!empty($project->sponsor) && $project->sponsor !== 'Sponsored') {
                            $agencyName = $project->sponsor;
                        } elseif (!empty($project->agency_name)) {
                            $agencyName = $project->agency_name;
                        } elseif (!empty($project->donor_name)) {
                            $agencyName = $project->donor_name;
                        } elseif (!empty($application->agency_name)) {
                            $agencyName = $application->agency_name;
                        } elseif (!empty($application->donor_name)) {
                            $agencyName = $application->donor_name;
                        } elseif (!empty($application->agency)) {
                            $agencyName = $application->agency;
                        } elseif ($project->donor) {
                            $agencyName = $project->donor->name;
                        }

                        $agencyId = $project->agency_project_no ?? $application->agency_number ?? $project->agency_id ?? 'N/A';

                        $clusterName = $application->cluster ? ($application->cluster->name . (!empty($application->cluster->code) ? ' (' . $application->cluster->code . ')' : '')) : ($project->cluster ? ($project->cluster->name . (!empty($project->cluster->code) ? ' (' . $project->cluster->code . ')' : '')) : ($application->cluster_name ?? $application->cluster ?? 'N/A'));

                        $spDateRaw = $project->sponsored_date ?? $project->sponsor_date ?? $application->sponsored_date ?? $project->created_at;
                        $sponsoredDate = !empty($spDateRaw) ? date('d-M-Y', strtotime($spDateRaw)) : 'N/A';

                        $projectLocation = $project->location ?? ($project->place ?? ($application->location ?? ($application->place ?? ($application->locality_location ?? ($application->meta['location'] ?? 'N/A')))));
                        $remarks = $project->remarks ?? $project->additional_note ?? 'N/A';
                    @endphp

                    <div style="background-color: var(--panel-bg); border: 1px solid var(--panel-border); border-radius: 12px; padding: 1.5rem; margin-top: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                        <h4 style="color: var(--accent-cyan); font-size: 0.95rem; font-weight: 700; text-transform: uppercase; margin-top: 0; margin-bottom: 1.25rem; letter-spacing: 0.05em; border-bottom: 1px solid var(--panel-border); padding-bottom: 0.75rem;">
                            <i class="bx bx-id-card" style="font-size: 1.1rem; vertical-align: middle; margin-right: 0.35rem;"></i> Project &amp; Agency Details
                        </h4>
                        <div class="details-grid">
                            <div class="details-label">Project ID</div><div class="details-colon">:</div><div class="details-value" style="color: var(--accent-cyan); font-weight: 700;">{{ $project->project_id ?? 'N/A' }}</div>
                            <div class="details-label">Agency Name</div><div class="details-colon">:</div><div class="details-value" style="color: var(--accent-cyan); font-weight: 700;">{{ $agencyName ?? 'N/A' }}</div>
                            <div class="details-label">Agency ID</div><div class="details-colon">:</div><div class="details-value" style="color: var(--accent-cyan); font-weight: 700;">{{ $agencyId }}</div>
                            <div class="details-label">Cluster Name</div><div class="details-colon">:</div><div class="details-value" style="color: #ffffff; font-weight: 600;">{{ $clusterName }}</div>
                            <div class="details-label">Sponsored Date</div><div class="details-colon">:</div><div class="details-value" style="color: #ffffff; font-weight: 600;">{{ $sponsoredDate }}</div>
                            <div class="details-label">Location</div><div class="details-colon">:</div>
                            <div class="details-value" style="color: #ffffff; font-weight: 600;">
                                <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;" id="location-display-wrapper">
                                        <span id="location-display-text">{{ $projectLocation }}</span>
                                        @if(!empty($projectLocation) && $projectLocation !== 'N/A')
                                            <a href="{{ Str::startsWith($projectLocation, ['http://', 'https://']) ? $projectLocation : 'https://www.google.com/maps/search/?api=1&query=' . urlencode($projectLocation) }}" 
                                               target="_blank" 
                                               class="btn-custom" 
                                               style="padding: 0.2rem 0.5rem; font-size: 0.75rem; border-radius: 4px; background: rgba(14, 165, 233, 0.15); border: 1px solid rgba(14, 165, 233, 0.35); color: #38bdf8; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;" 
                                               title="View on Google Maps">
                                                <i class="bx bx-map-pin"></i> Google Map
                                            </a>
                                        @endif
                                    </div>
                                    <button type="button" id="edit-location-btn" onclick="toggleLocationEdit()" class="btn-custom" style="padding: 0.2rem 0.5rem; font-size: 0.75rem; border-radius: 4px;" title="Edit Location">
                                        <i class="bx bx-edit"></i> Edit Location
                                    </button>
                                </div>
                            </div>

                            <div class="details-label">Remarks</div><div class="details-colon">:</div>
                            <div class="details-value" style="color: #ffffff; font-weight: 500;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem;">
                                    <span id="remarks-display-text" style="white-space: pre-wrap; flex: 1;">{{ $remarks }}</span>
                                    <button type="button" id="edit-remarks-btn" onclick="toggleRemarksEdit()" class="btn-custom" style="padding: 0.2rem 0.5rem; font-size: 0.75rem; border-radius: 4px;" title="Edit Remarks">
                                        <i class="bx bx-edit"></i> Edit Remarks
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Inline Edit Form for Location -->
                        <form id="location-edit-form" action="{{ route('projects.' . $projectRouteKey . '.update_address', $project->id) }}" method="POST" onsubmit="handleLocationSubmit(event)" style="display: none; margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed var(--panel-border);">
                            @csrf
                            <div style="margin-bottom: 0.75rem;">
                                <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Edit Location</label>
                                <input type="text" name="location" value="{{ $projectLocation !== 'N/A' ? $projectLocation : '' }}" placeholder="Enter location name or Google Maps URL..." class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                            </div>
                            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                <button type="button" onclick="closeLocationEdit()" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); border-radius: 6px; padding: 0.35rem 0.85rem; font-size: 0.8rem;">Cancel</button>
                                <button type="submit" class="btn-custom" style="border-radius: 6px; padding: 0.35rem 0.85rem; font-size: 0.8rem; background: linear-gradient(135deg, #10b981, #059669); border: none; color: #fff; font-weight: 700;">Save Location</button>
                            </div>
                        </form>

                        <!-- Inline Edit Form for Remarks -->
                        <form id="remarks-edit-form" action="{{ route('projects.' . $projectRouteKey . '.update_address', $project->id) }}" method="POST" onsubmit="handleRemarksSubmit(event)" style="display: none; margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed var(--panel-border);">
                            @csrf
                            <div style="margin-bottom: 0.75rem;">
                                <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Edit Remarks</label>
                                <textarea name="remarks" rows="3" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">{{ $remarks !== 'N/A' ? $remarks : '' }}</textarea>
                            </div>
                            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                <button type="button" onclick="closeRemarksEdit()" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); border-radius: 6px; padding: 0.35rem 0.85rem; font-size: 0.8rem;">Cancel</button>
                                <button type="submit" class="btn-custom" style="border-radius: 6px; padding: 0.35rem 0.85rem; font-size: 0.8rem; background: linear-gradient(135deg, #10b981, #059669); border: none; color: #fff; font-weight: 700;">Save Remarks</button>
                            </div>
                        </form>
                    </div>

                    <script>
                        function toggleLocationEdit() {
                            const form = document.getElementById('location-edit-form');
                            const btn = document.getElementById('edit-location-btn');
                            const remarksForm = document.getElementById('remarks-edit-form');
                            if (remarksForm) closeRemarksEdit();
                            if (form) {
                                if (form.style.display === 'none') {
                                    form.style.display = 'block';
                                    if (btn) btn.style.display = 'none';
                                } else {
                                    closeLocationEdit();
                                }
                            }
                        }

                        function closeLocationEdit() {
                            const form = document.getElementById('location-edit-form');
                            const btn = document.getElementById('edit-location-btn');
                            if (form) form.style.display = 'none';
                            if (btn) btn.style.display = 'inline-flex';
                        }

                        function toggleRemarksEdit() {
                            const form = document.getElementById('remarks-edit-form');
                            const text = document.getElementById('remarks-display-text');
                            const btn = document.getElementById('edit-remarks-btn');
                            const locForm = document.getElementById('location-edit-form');
                            if (locForm) closeLocationEdit();
                            if (form) {
                                if (form.style.display === 'none') {
                                    form.style.display = 'block';
                                    if (text) text.style.display = 'none';
                                    if (btn) btn.style.display = 'none';
                                } else {
                                    closeRemarksEdit();
                                }
                            }
                        }

                        function closeRemarksEdit() {
                            const form = document.getElementById('remarks-edit-form');
                            const text = document.getElementById('remarks-display-text');
                            const btn = document.getElementById('edit-remarks-btn');
                            if (form) form.style.display = 'none';
                            if (text) text.style.display = 'inline';
                            if (btn) btn.style.display = 'inline-flex';
                        }

                        async function handleLocationSubmit(event) {
                            event.preventDefault();
                            const form = event.target;
                            const formData = new FormData(form);
                            const submitBtn = form.querySelector('button[type="submit"]');
                            if (submitBtn) submitBtn.disabled = true;

                            try {
                                const response = await fetch(form.action, {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json'
                                    }
                                });
                                const data = await response.json();
                                if (data.success) {
                                    const locVal = data.location || formData.get('location') || '';
                                    const dispText = document.getElementById('location-display-text');
                                    if (dispText) dispText.textContent = locVal || 'N/A';

                                    const wrapper = document.getElementById('location-display-wrapper');
                                    if (wrapper) {
                                        let mapBtn = wrapper.querySelector('a');
                                        if (locVal && locVal !== 'N/A') {
                                            const mapsUrl = (locVal.startsWith('http://') || locVal.startsWith('https://')) ? locVal : 'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(locVal);
                                            if (!mapBtn) {
                                                mapBtn = document.createElement('a');
                                                mapBtn.target = '_blank';
                                                mapBtn.className = 'btn-custom';
                                                mapBtn.style.cssText = 'padding: 0.2rem 0.5rem; font-size: 0.75rem; border-radius: 4px; background: rgba(14, 165, 233, 0.15); border: 1px solid rgba(14, 165, 233, 0.35); color: #38bdf8; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem;';
                                                mapBtn.title = 'View on Google Maps';
                                                mapBtn.innerHTML = '<i class="bx bx-map-pin"></i> Google Map';
                                                wrapper.appendChild(mapBtn);
                                            }
                                            mapBtn.href = mapsUrl;
                                        } else if (mapBtn) {
                                            mapBtn.remove();
                                        }
                                    }
                                    closeLocationEdit();
                                    if (typeof showToast === 'function') {
                                        showToast(data.message || 'Location updated successfully!', 'success');
                                    }
                                } else {
                                    alert(data.error || 'Failed to update location');
                                }
                            } catch (e) {
                                console.error('Error updating location:', e);
                                alert('An error occurred while updating location.');
                            } finally {
                                if (submitBtn) submitBtn.disabled = false;
                            }
                        }

                        async function handleRemarksSubmit(event) {
                            event.preventDefault();
                            const form = event.target;
                            const formData = new FormData(form);
                            const submitBtn = form.querySelector('button[type="submit"]');
                            if (submitBtn) submitBtn.disabled = true;

                            try {
                                const response = await fetch(form.action, {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json'
                                    }
                                });
                                const data = await response.json();
                                if (data.success) {
                                    const remVal = data.remarks || formData.get('remarks') || '';
                                    const dispText = document.getElementById('remarks-display-text');
                                    if (dispText) dispText.textContent = remVal || 'N/A';
                                    closeRemarksEdit();
                                    if (typeof showToast === 'function') {
                                        showToast(data.message || 'Remarks updated successfully!', 'success');
                                    }
                                } else {
                                    alert(data.error || 'Failed to update remarks');
                                }
                            } catch (e) {
                                console.error('Error updating remarks:', e);
                                alert('An error occurred while updating remarks.');
                            } finally {
                                if (submitBtn) submitBtn.disabled = false;
                            }
                        }

                        async function handleAddressSubmit(event) {
                            event.preventDefault();
                            const form = event.target;
                            const formData = new FormData(form);
                            const submitBtn = form.querySelector('button[type="submit"]');
                            if (submitBtn) submitBtn.disabled = true;

                            try {
                                const response = await fetch(form.action, {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json'
                                    }
                                });
                                const data = await response.json();
                                if (data.success) {
                                    closeAddressEdit();
                                    if (typeof showToast === 'function') {
                                        showToast(data.message || 'Details updated successfully!', 'success');
                                    }
                                } else {
                                    alert(data.error || 'Failed to update details');
                                }
                            } catch (e) {
                                console.error('Error updating details:', e);
                                alert('An error occurred while updating details.');
                            } finally {
                                if (submitBtn) submitBtn.disabled = false;
                            }
                        }
                    </script>

                    <!-- Application Full Details Modal -->
                    <div id="appDetailsModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(4px); z-index: 9000; align-items: center; justify-content: center; padding: 1.5rem;">
                        <div style="background-color: var(--panel-bg); border: 1px solid var(--panel-border); border-radius: 12px; padding: 1.75rem; width: 100%; max-width: 700px; max-height: 85vh; overflow-y: auto; box-shadow: 0 15px 35px rgba(0,0,0,0.4); position: relative;">
                            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.25rem;">
                                <h3 style="color: var(--text-main); font-size: 1.15rem; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="bx bx-file-find" style="color: var(--accent-cyan);"></i> Complete Application Details
                                </h3>
                                <button type="button" onclick="closeAppDetailsModal()" style="background: transparent; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; line-height: 1;">&times;</button>
                            </div>

                            <div style="display: grid; grid-template-columns: 160px 10px 1fr; gap: 0.6rem 0.5rem; font-size: 0.9rem; margin-bottom: 1.5rem;">
                                <div style="color: var(--text-muted); font-weight: 500;">Applicant ID</div><div>:</div><div style="color: var(--accent-cyan); font-weight: 700;">{{ $formattedAppId }}</div>
                                <div style="color: var(--text-muted); font-weight: 500;">Applicant Name</div><div>:</div><div style="color: var(--text-main); font-weight: 600;">{{ $application->applicant_name ?? 'N/A' }}</div>
                                <div style="color: var(--text-muted); font-weight: 500;">Gender</div><div>:</div><div>{{ $application->gender ?? 'N/A' }}</div>
                                <div style="color: var(--text-muted); font-weight: 500;">Date of Birth</div><div>:</div><div>{{ !empty($application->dob) ? date('d-M-Y', strtotime($application->dob)) : 'N/A' }} @if(!empty($application->age))(Age: {{ $application->age }})@endif</div>
                                @if(!empty($application->aadhar_number))
                                    <div style="color: var(--text-muted); font-weight: 500;">Aadhar Number</div><div>:</div><div>{{ $application->aadhar_number }}</div>
                                @endif
                                @if(!empty($application->health_status))
                                    <div style="color: var(--text-muted); font-weight: 500;">Health Status</div><div>:</div><div>{{ $application->health_status }}</div>
                                @endif
                                <div style="color: var(--text-muted); font-weight: 500;">Father's Name</div><div>:</div><div>{{ $application->father_name ?? 'N/A' }} @if(!empty($application->father_death_date) || !empty($application->father_death_cause))(Deceased: {{ !empty($application->father_death_date) ? date('d-M-Y', strtotime($application->father_death_date)) : '' }} {{ !empty($application->father_death_cause) ? '- ' . $application->father_death_cause : '' }})@endif</div>
                                <div style="color: var(--text-muted); font-weight: 500;">Mother's Name</div><div>:</div><div>{{ $application->mother_name ?? 'N/A' }} @if(!empty($application->mother_alive_status))({{ $application->mother_alive_status }})@endif @if(!empty($application->mother_death_date) || !empty($application->mother_death_cause))(Deceased: {{ !empty($application->mother_death_date) ? date('d-M-Y', strtotime($application->mother_death_date)) : '' }} {{ !empty($application->mother_death_cause) ? '- ' . $application->mother_death_cause : '' }})@endif</div>
                                <div style="color: var(--text-muted); font-weight: 500;">Guardian</div><div>:</div><div>{{ $application->guardian_name ?? 'N/A' }} @if(!empty($application->guardian_relation))(Relation: {{ $application->guardian_relation }})@endif</div>
                                @if(!empty($application->grandfather_name))
                                    <div style="color: var(--text-muted); font-weight: 500;">Grandfather's Name</div><div>:</div><div>{{ $application->grandfather_name }}</div>
                                @endif
                                @if(!empty($application->mothers_father_name))
                                    <div style="color: var(--text-muted); font-weight: 500;">Mother's Father Name</div><div>:</div><div>{{ $application->mothers_father_name }}</div>
                                @endif
                                @if(!empty($application->siblings_total) || !empty($application->siblings_male) || !empty($application->siblings_female))
                                    <div style="color: var(--text-muted); font-weight: 500;">Siblings</div><div>:</div><div>Total: {{ $application->siblings_total ?? (($application->siblings_male ?? 0) + ($application->siblings_female ?? 0)) }} (Male: {{ $application->siblings_male ?? 0 }}, Female: {{ $application->siblings_female ?? 0 }})</div>
                                @endif
                                <div style="color: var(--text-muted); font-weight: 500;">House Name</div><div>:</div><div>{{ $application->house_name ?? 'N/A' }}</div>
                                <div style="color: var(--text-muted); font-weight: 500;">Place</div><div>:</div><div>{{ $application->place ?? 'N/A' }}</div>
                                <div style="color: var(--text-muted); font-weight: 500;">Post Office</div><div>:</div><div>{{ $application->post_office ?? 'N/A' }}</div>
                                <div style="color: var(--text-muted); font-weight: 500;">Village / Panchayat</div><div>:</div><div>{{ $application->village ?? 'N/A' }} / {{ $application->panchayat ?? 'N/A' }}</div>
                                <div style="color: var(--text-muted); font-weight: 500;">District / State</div><div>:</div><div>{{ $application->district ?? 'N/A' }}, {{ $application->state ?? 'N/A' }}</div>
                                <div style="color: var(--text-muted); font-weight: 500;">Contact Numbers</div><div>:</div><div>{{ $application->mobile_1 ?? $application->contact_number_1 ?? 'N/A' }} {{ !empty($application->mobile_2 ?? $application->contact_number_2) ? '/ ' . ($application->mobile_2 ?? $application->contact_number_2) : '' }}</div>
                                @if(!empty($application->school_name) || !empty($application->school_class))
                                    <div style="color: var(--text-muted); font-weight: 500;">School Education</div><div>:</div><div>{{ $application->school_name ?? 'N/A' }} @if(!empty($application->school_class))(Class: {{ $application->school_class }})@endif</div>
                                @endif
                                @if(!empty($application->madrassa_name) || !empty($application->madrassa_class))
                                    <div style="color: var(--text-muted); font-weight: 500;">Madrassa Education</div><div>:</div><div>{{ $application->madrassa_name ?? 'N/A' }} @if(!empty($application->madrassa_class))(Class: {{ $application->madrassa_class }})@endif</div>
                                @endif
                                @if(!empty($application->monthly_income) || !empty($application->monthly_expense))
                                    <div style="color: var(--text-muted); font-weight: 500;">Monthly Finance</div><div>:</div><div>Income: ₹{{ $application->monthly_income ?? '0' }} / Expense: ₹{{ $application->monthly_expense ?? '0' }}</div>
                                @endif
                                @if(!empty($application->cluster))
                                    <div style="color: var(--text-muted); font-weight: 500;">Cluster</div><div>:</div><div>{{ $application->cluster->name }} ({{ $application->cluster->code }})</div>
                                @endif
                                <div style="color: var(--text-muted); font-weight: 500;">Sponsor Status</div><div>:</div><div style="color: var(--accent-cyan); font-weight: 700;">{{ $application->sponsor_status ?? 'N/A' }}</div>
                                <div style="color: var(--text-muted); font-weight: 500;">Application Status</div><div>:</div><div style="color: #10b981; font-weight: 700;">{{ $application->status ?? 'Approved' }}</div>
                                @if(!empty($application->additional_note))
                                    <div style="color: var(--text-muted); font-weight: 500;">Additional Note</div><div>:</div><div style="white-space: pre-wrap;">{{ $application->additional_note }}</div>
                                @endif
                            </div>

                            <div style="display: flex; gap: 0.75rem; justify-content: flex-end; border-top: 1px solid var(--panel-border); padding-top: 1.25rem;">
                                <a href="{{ $appLinkUrl }}" class="btn-custom" style="text-decoration: none; border-radius: 6px; padding: 0.5rem 1.25rem; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.4rem;">
                                    <i class="bx bx-external-link"></i> Go to Applications Page
                                </a>
                                <button type="button" onclick="closeAppDetailsModal()" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); border-radius: 6px; padding: 0.5rem 1rem; font-size: 0.85rem;">Close</button>
                            </div>
                        </div>
                    </div>

                    <script>
                        function openAppDetailsModal(e) {
                            if (e) e.preventDefault();
                            document.getElementById('appDetailsModal').style.display = 'flex';
                        }
                        function closeAppDetailsModal() {
                            document.getElementById('appDetailsModal').style.display = 'none';
                        }
                    </script>
                @else
                    <div class="details-grid">
                        <div class="details-label">Project ID</div><div class="details-colon">:</div><div class="details-value" style="color: var(--accent-cyan);">{{ $project->project_id }}</div>
                        <div class="details-label">Project Name</div><div class="details-colon">:</div><div class="details-value">{{ $project->project_name ?? 'N/A' }}</div>
                        <div class="details-label">Sponsor</div><div class="details-colon">:</div><div class="details-value">{{ $project->sponsor ?? 'N/A' }}</div>
                        <div class="details-label">Project Spec</div><div class="details-colon">:</div><div class="details-value" style="white-space: pre-wrap;">{{ $project->project_spec ?? 'N/A' }}</div>
                        <div class="details-label">Agency Project No</div><div class="details-colon">:</div><div class="details-value">{{ $project->agency_project_no ?? 'N/A' }}</div>
                        <div class="details-label">Donor Name</div><div class="details-colon">:</div><div class="details-value">{{ $project->donor ? $project->donor->name : 'N/A' }}</div>
                        <div class="details-label">Project Manager</div><div class="details-colon">:</div><div class="details-value">{{ $project->projectManager ? $project->projectManager->name : 'N/A' }}</div>
                        <div class="details-label">Available Budget</div><div class="details-colon">:</div><div class="details-value">₹{{ number_format($project->available_budget, 2) }}</div>
                        <div class="details-label">Type of Project</div><div class="details-colon">:</div><div class="details-value">{{ $project->type_of_project }}</div>
                        <div class="details-label">Theme</div><div class="details-colon">:</div><div class="details-value">{{ $project->theme ?? 'N/A' }}</div>
                        <div class="details-label">Subtheme</div><div class="details-colon">:</div><div class="details-value">{{ $project->subtheme ?? 'N/A' }}</div>
                        <div class="details-label">Activity</div><div class="details-colon">:</div><div class="details-value">{{ $project->activity ?? 'N/A' }}</div>
                        <div class="details-label">Remarks</div><div class="details-colon">:</div><div class="details-value" style="font-weight: normal; color: var(--text-muted);">{{ $project->remarks ?? 'N/A' }}</div>
                        <div class="details-label">Project Status</div><div class="details-colon">:</div><div class="details-value" id="grid-project-status" style="font-weight: 600; color: var(--accent-cyan);">{{ in_array($project->status, ['Approved', 'Completed']) ? $project->status : ($project->project_phase === 'Other' ? ($project->project_phase_custom ?: 'Other') : $project->project_phase) }}</div>
                    </div>
                @endif

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
                @if(!$isSocialAidProject)

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
                            <span>Last Updated: <strong id="status-updated-at" style="color: var(--text-main);">{{ $statusUpdatedAt ? $statusUpdatedAt->format('d-M-Y h:i A') : '' }}</strong> (<span id="status-updated-human" style="color: #10b981;">{{ $statusUpdatedAt ? $statusUpdatedAt->diffForHumans() : '' }}</span>)</span>
                        </div>
                    </div>

                    @if($canEditStatus && $hasApplication)
                    <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: flex-end; max-width: 560px;">
                        <div style="flex: 1; min-width: 220px;">
                            <label style="display: block; color: var(--text-muted); font-size: 0.82rem; margin-bottom: 0.35rem;">Select Phase</label>
                            <select id="project-phase-select" onchange="onPhaseSelectChange()" style="width: 100%; padding: 0.55rem 0.85rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: var(--text-main); font-size: 0.9rem; outline: none; cursor: pointer;">
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
                @endif
            </div>
        </div>

        <!-- ================= STAGE 2 PANEL (APPLICANT DETAIL / FINANCIAL DATA) ================= -->
        <div class="stage-content-panel" id="stage-content-2">
            <div class="detail-header-panel">
                <h2>{{ $isSocialAidProject ? 'FINANCIAL DATA' : 'APPLICANT DETAIL' }}</h2>
            </div>
            <div style="padding: 1.5rem;">
                @if($isSocialAidProject)
                    <!-- Financial Data Table -->
                    <div style="background-color: var(--panel-bg); border: 1px solid var(--panel-border); border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 2rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                            <h4 style="color: var(--accent-cyan); font-size: 0.95rem; font-weight: 700; text-transform: uppercase; margin: 0; letter-spacing: 0.05em;">Fund Transfers</h4>
                            @if($canManageFinance)
                                <button type="button" class="btn-custom" onclick="openAddFundModal()" style="border-radius: 6px; padding: 0.5rem 1rem; font-size: 0.85rem;">
                                    <i class="bx bx-plus"></i> Add New Row
                                </button>
                            @endif
                        </div>

                        <div class="table-responsive-custom" style="overflow-x: auto;">
                            <table class="table-dark-custom" style="width: 100%; border-collapse: collapse; text-align: left;">
                                <thead>
                                    <tr style="border-bottom: 2px solid var(--panel-border); color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">
                                        <th style="padding: 0.75rem 1rem; font-weight: 700;">Serial No</th>
                                        <th style="padding: 0.75rem 1rem; font-weight: 700;">Date of Fund Transferred</th>
                                        <th style="padding: 0.75rem 1rem; font-weight: 700; text-align: right;">Amount</th>
                                        <th style="padding: 0.75rem 1rem; font-weight: 700;">Agency</th>
                                        <th style="padding: 0.75rem 1rem; font-weight: 700;">Account Name</th>
                                        <th style="padding: 0.75rem 1rem; font-weight: 700;">Account Number</th>
                                        <th style="padding: 0.75rem 1rem; font-weight: 700;">IFSC Number</th>
                                        <th style="padding: 0.75rem 1rem; font-weight: 700; text-align: center; width: 100px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="fund-transfers-tbody">
                                    @php
                                        $financials = $project->funds ?? collect();
                                    @endphp
                                    @forelse($financials as $index => $row)
                                        <tr id="fund-row-{{ $row->id }}" class="fund-table-row" style="border-bottom: 1px solid var(--panel-border); font-size: 0.9rem; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.02)'" onmouseout="this.style.backgroundColor='transparent'">
                                            <td class="fund-serial-no" style="padding: 0.75rem 1rem; color: var(--text-muted);">{{ $index + 1 }}</td>
                                            <td style="padding: 0.75rem 1rem;">{{ !empty($row->date) ? date('d-M-Y', strtotime($row->date)) : 'N/A' }}</td>
                                            <td class="fund-amount-cell" data-amount="{{ $row->amount }}" style="padding: 0.75rem 1rem; text-align: right; font-weight: 600; color: #10b981;">₹{{ number_format($row->amount, 2) }}</td>
                                            <td style="padding: 0.75rem 1rem;">
                                                @if($row->donorModel)
                                                    {{ $row->donorModel->name }} {{ $row->donorModel->short_name ? '('.$row->donorModel->short_name.')' : '' }}
                                                @else
                                                    {{ $row->donor ?? $row->agency ?? 'N/A' }}
                                                @endif
                                            </td>
                                            <td style="padding: 0.75rem 1rem;">{{ $row->account_name ?? 'N/A' }}</td>
                                            <td style="padding: 0.75rem 1rem;">{{ $row->account_number ?? 'N/A' }}</td>
                                            <td style="padding: 0.75rem 1rem; font-family: monospace;">{{ $row->ifsc_number ?? 'N/A' }}</td>
                                            <td style="padding: 0.75rem 1rem; text-align: center;">
                                                @if($canDeleteFinanceRow)
                                                    <button type="button" onclick="handleDeleteFund(this, {{ $row->id }}, '{{ route('projects.' . $projectRouteKey . '.delete_fund', [$project->id, $row->id]) }}')" style="background: transparent; border: none; color: #ef4444; cursor: pointer; padding: 0.25rem; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'" title="Delete Row">
                                                        <i class="bx bx-trash" style="font-size: 1.15rem;"></i>
                                                    </button>
                                                @else
                                                    <span style="color: var(--text-muted); font-size: 0.8rem;">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="no-funds-row">
                                            <td colspan="8" style="padding: 2rem; text-align: center; color: var(--text-muted); font-style: italic;">
                                                No fund transfer records found. Click "Add New Row" to add one.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot id="fund-transfers-tfoot" style="display: {{ $financials->count() > 0 ? 'table-footer-group' : 'none' }};">
                                    <tr style="border-top: 2px solid var(--panel-border); font-weight: 700; font-size: 0.95rem;">
                                        <td colspan="2" style="padding: 0.75rem 1rem; color: var(--text-main); text-align: left;">Total</td>
                                        <td id="fund-total-amount" style="padding: 0.75rem 1rem; text-align: right; color: var(--accent-cyan);">₹{{ number_format($financials->sum('amount'), 2) }}</td>
                                        <td colspan="5" style="padding: 0.75rem 1rem;"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Add Fund Row Modal -->
                    <div id="addFundModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center; padding: 1.5rem;">
                        <div style="background-color: var(--panel-bg); border: 1px solid var(--panel-border); border-radius: 12px; padding: 1.75rem; width: 100%; max-width: 520px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); box-sizing: border-box; position: relative;">
                            <h3 style="color: var(--text-main); font-size: 1.1rem; margin-top: 0; margin-bottom: 1.5rem; border-bottom: 1px solid var(--panel-border); padding-bottom: 0.75rem;">
                                Add Fund Transfer Row
                            </h3>
                            <form id="addFundForm" action="{{ route('projects.' . $projectRouteKey . '.add_fund', $project->id) }}" method="POST" onsubmit="handleAddFundSubmit(event); return false;">

                                @csrf
                                <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.75rem;">
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Date of Fund Transferred</label>
                                        <input type="date" name="date" required class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.6rem; border-radius: 6px; outline: none;">
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Rs (Amount)</label>
                                        <input type="number" name="amount" step="0.01" required min="0.01" placeholder="Enter amount..." class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.6rem; border-radius: 6px; outline: none;">
                                    </div>
                                    @php
                                        $donorsList = \App\Models\Donor::orderBy('name')->get();
                                    @endphp
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Agency / Donor</label>
                                        <select name="donor" required class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.6rem; border-radius: 6px; outline: none;">
                                            <option value="">Select Agency / Donor...</option>
                                            @foreach($donorsList as $dItem)
                                                <option value="{{ $dItem->name }}">{{ $dItem->name }} {{ $dItem->short_name ? '('.$dItem->short_name.')' : '' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Account Name</label>
                                        <input type="text" name="account_name" placeholder="Enter account holder name..." class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.6rem; border-radius: 6px; outline: none;">
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Account Number</label>
                                        <input type="number" name="account_number" placeholder="Enter account number..." class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.6rem; border-radius: 6px; outline: none;">
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">IFSC Number</label>
                                        <input type="text" name="ifsc_number" placeholder="Enter IFSC code..." class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.6rem; border-radius: 6px; outline: none; text-transform: uppercase;">
                                    </div>
                                </div>
                                <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                                    <button type="button" onclick="closeAddFundModal()" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); border-radius: 6px; padding: 0.5rem 1rem; font-size: 0.85rem;">Cancel</button>
                                    <button type="submit" class="btn-custom" style="border-radius: 6px; padding: 0.5rem 1rem; font-size: 0.85rem;">Add Row</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <script>
                        function openAddFundModal() {
                            document.getElementById('addFundModal').style.display = 'flex';
                        }
                        function closeAddFundModal() {
                            document.getElementById('addFundModal').style.display = 'none';
                        }

                        async function handleAddFundSubmit(e) {
                            if (e) {
                                e.preventDefault();
                                e.stopPropagation();
                            }
                            const form = document.getElementById('addFundForm');
                            if (!form) return false;
                            if (form.dataset.isSubmitting === 'true') return false;
                            form.dataset.isSubmitting = 'true';

                            const submitBtn = form.querySelector('button[type="submit"]');
                            if (submitBtn) {
                                submitBtn.disabled = true;
                                submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Adding...';
                            }

                            try {
                                const formData = new FormData(form);
                                const response = await fetch(form.action, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: formData
                                });

                                const data = await response.json();
                                if (response.ok && data.success) {
                                    closeAddFundModal();
                                    form.reset();
                                    if (typeof showToast === 'function') {
                                        showToast(data.message || 'Fund transfer record added successfully!', 'success');
                                    }
                                    appendFundRowToTable(data.fund, data.formatted_date, data.formatted_amount, data.total_amount, '{{ route("projects." . $projectRouteKey . ".delete_fund", [$project->id, 0]) }}');
                                } else {
                                    alert(data.error || 'Failed to add fund record.');
                                }
                            } catch (err) {
                                console.error(err);
                                alert('An error occurred while adding fund record.');
                            } finally {
                                form.dataset.isSubmitting = 'false';
                                if (submitBtn) {
                                    submitBtn.disabled = false;
                                    submitBtn.innerHTML = 'Add Row';
                                }
                            }
                            return false;
                        }

                        function appendFundRowToTable(fund, formattedDate, formattedAmount, totalAmountFormatted, deleteBaseUrl) {
                            const tbody = document.getElementById('fund-transfers-tbody');
                            const tfoot = document.getElementById('fund-transfers-tfoot');
                            const totalElem = document.getElementById('fund-total-amount');
                            if (!tbody) return;

                            const noRow = document.getElementById('no-funds-row');
                            if (noRow) noRow.remove();

                            const rowsCount = tbody.querySelectorAll('tr.fund-table-row').length;
                            const deleteUrl = deleteBaseUrl.replace('/0', '/' + fund.id);

                            const tr = document.createElement('tr');
                            tr.id = `fund-row-${fund.id}`;
                            tr.className = 'fund-table-row';
                            tr.style.cssText = 'border-bottom: 1px solid var(--panel-border); font-size: 0.9rem; transition: all 0.3s ease; opacity: 0; transform: translateY(-10px);';
                            tr.setAttribute('onmouseover', "this.style.backgroundColor='rgba(255,255,255,0.02)'");
                            tr.setAttribute('onmouseout', "this.style.backgroundColor='transparent'");

                            tr.innerHTML = `
                                <td class="fund-serial-no" style="padding: 0.75rem 1rem; color: var(--text-muted);">${rowsCount + 1}</td>
                                <td style="padding: 0.75rem 1rem;">${formattedDate}</td>
                                <td class="fund-amount-cell" data-amount="${fund.amount}" style="padding: 0.75rem 1rem; text-align: right; font-weight: 600; color: #10b981;">₹${formattedAmount}</td>
                                <td style="padding: 0.75rem 1rem;">${fund.donor || fund.agency || 'N/A'}</td>
                                <td style="padding: 0.75rem 1rem;">${fund.account_name || 'N/A'}</td>
                                <td style="padding: 0.75rem 1rem;">${fund.account_number || 'N/A'}</td>
                                <td style="padding: 0.75rem 1rem; font-family: monospace;">${fund.ifsc_number || 'N/A'}</td>
                                <td style="padding: 0.75rem 1rem; text-align: center;">
                                    ${ {{ $canDeleteFinanceRow ? 'true' : 'false' }} ? `
                                        <button type="button" onclick="handleDeleteFund(this, ${fund.id}, '${deleteUrl}')" style="background: transparent; border: none; color: #ef4444; cursor: pointer; padding: 0.25rem; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'" title="Delete Row">
                                            <i class="bx bx-trash" style="font-size: 1.15rem;"></i>
                                        </button>
                                    ` : '<span style="color: var(--text-muted); font-size: 0.8rem;">-</span>' }
                                </td>
                            `;

                            tbody.appendChild(tr);
                            requestAnimationFrame(() => {
                                tr.style.opacity = '1';
                                tr.style.transform = 'translateY(0)';
                            });

                            if (tfoot) tfoot.style.display = 'table-footer-group';
                            if (totalElem) totalElem.innerText = `₹${totalAmountFormatted}`;
                        }

                        async function handleDeleteFund(btnElement, fundId, deleteUrl) {
                            if (!confirm('Are you sure you want to delete this fund transfer record?')) return;

                            const row = btnElement.closest('tr');
                            if (row) {
                                row.style.transition = 'all 0.3s ease';
                                row.style.opacity = '0.4';
                                row.style.pointerEvents = 'none';
                            }

                            try {
                                const response = await fetch(deleteUrl, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: JSON.stringify({ _method: 'DELETE' })
                                });

                                const data = await response.json();
                                if (response.ok && data.success) {
                                    if (row) {
                                        row.style.transform = 'translateX(20px)';
                                        row.style.opacity = '0';
                                        setTimeout(() => {
                                            row.remove();
                                            updateFundTableSerialNumbersAndTotal(data.total_amount);
                                        }, 300);
                                    }
                                    if (typeof showToast === 'function') {
                                        showToast(data.message || 'Fund transfer record deleted successfully!', 'success');
                                    }
                                } else {
                                    if (row) {
                                        row.style.opacity = '1';
                                        row.style.pointerEvents = 'auto';
                                    }
                                    alert(data.error || 'Failed to delete fund record.');
                                }
                            } catch (err) {
                                console.error(err);
                                if (row) {
                                    row.style.opacity = '1';
                                    row.style.pointerEvents = 'auto';
                                }
                                alert('An error occurred while deleting fund record.');
                            }
                        }

                        function updateFundTableSerialNumbersAndTotal(totalAmountFormatted) {
                            const tbody = document.getElementById('fund-transfers-tbody');
                            const tfoot = document.getElementById('fund-transfers-tfoot');
                            const totalElem = document.getElementById('fund-total-amount');
                            if (!tbody) return;

                            const rows = tbody.querySelectorAll('tr.fund-table-row');
                            if (rows.length === 0) {
                                tbody.innerHTML = `
                                    <tr id="no-funds-row">
                                        <td colspan="5" style="padding: 2rem; text-align: center; color: var(--text-muted); font-style: italic;">
                                            No fund transfer records found. Click "Add New Row" to add one.
                                        </td>
                                    </tr>
                                `;
                                if (tfoot) tfoot.style.display = 'none';
                                return;
                            }

                            rows.forEach((r, idx) => {
                                const serialCell = r.querySelector('.fund-serial-no');
                                if (serialCell) serialCell.innerText = idx + 1;
                            });

                            if (totalElem) totalElem.innerText = `₹${totalAmountFormatted}`;
                        }
                    </script>
                @else
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
                        @if($project->type_of_project === 'Orphan Care')
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                                <!-- Col 1 -->
                                <div>
                                    <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">1. Orphan & Family Details</h4>
                                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">Orphan Name:</td><td style="color: var(--text-main); font-weight: 600;">{!! $formatVal($application->applicant_name) !!} ({!! $formatVal($metaData['gender'] ?? null) !!})</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Date of Birth / Age:</td><td>{!! $formatVal($metaData['dob'] ?? null) !!} / {!! $formatVal($metaData['age'] ?? null) !!} yrs</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Aadhar Number:</td><td>{!! $formatVal($metaData['aadhar_number'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Father's Name:</td><td>{!! $formatVal($metaData['father_name'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Grandfather's Name:</td><td>{!! $formatVal($metaData['grandfather_name'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Mother's Name:</td><td>{!! $formatVal($metaData['mother_name'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Mother's Father Name:</td><td>{!! $formatVal($metaData['mothers_father_name'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Guardian / Relation:</td><td>{!! $formatVal($metaData['guardian_name'] ?? null) !!} ({!! $formatVal($metaData['guardian_relation'] ?? null) !!})</td></tr>
                                    </table>

                                    <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">2. Parental Death & Sibling Details</h4>
                                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">Father's Death Date:</td><td>{!! $formatVal($metaData['father_death_date'] ?? null) !!} <span style="font-size: 0.8rem; color: var(--text-muted);">({!! $formatVal($metaData['father_death_cause'] ?? null) !!})</span></td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Mother Alive Status:</td><td>{!! $formatVal($metaData['mother_alive_status'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Mother's Death Date:</td><td>{!! $formatVal($metaData['mother_death_date'] ?? null) !!} <span style="font-size: 0.8rem; color: var(--text-muted);">({!! $formatVal($metaData['mother_death_cause'] ?? null) !!})</span></td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Mother Re-Married?</td><td>{!! $formatVal($metaData['mother_remarried_status'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Brothers & Sisters:</td><td>Total: {!! $formatVal($metaData['siblings_total'] ?? null) !!} (M: {!! $formatVal($metaData['siblings_male'] ?? null) !!} / F: {!! $formatVal($metaData['siblings_female'] ?? null) !!})</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Monthly Income:</td><td>{{ isset($metaData['monthly_income']) ? '₹' . number_format($metaData['monthly_income']) : 'N/A' }}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Monthly Expense:</td><td>{{ isset($metaData['monthly_expense']) ? '₹' . number_format($metaData['monthly_expense']) : 'N/A' }}</td></tr>
                                    </table>
                                </div>

                                <!-- Col 2 -->
                                <div>
                                    <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">3. Education & House Details</h4>
                                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">Type Of House:</td><td>{!! $formatVal($metaData['house_type'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">School Name:</td><td>{!! $formatVal($metaData['school_name'] ?? null) !!} (Class: {!! $formatVal($metaData['school_class'] ?? null) !!})</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Madrassa Name:</td><td>{!! $formatVal($metaData['madrassa_name'] ?? null) !!} (Class: {!! $formatVal($metaData['madrassa_class'] ?? null) !!})</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">If Not Studying, Reason:</td><td>{!! $formatVal($metaData['not_studying_reason'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Health Status:</td><td>{!! $formatVal($metaData['health_status'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Sponsorship Details:</td><td>{!! $formatVal($metaData['sponsorship_details'] ?? null) !!}</td></tr>
                                    </table>

                                    <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">4. Address & Contact Details</h4>
                                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">House Name / Place:</td><td>{!! $formatVal($metaData['house_name'] ?? null) !!} / {!! $formatVal($application->place ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Town / Post Office:</td><td>{!! $formatVal($metaData['town'] ?? null) !!} / {!! $formatVal($metaData['post_office'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">District / State / Pin:</td><td>{!! $formatVal($metaData['district'] ?? null) !!} / {!! $formatVal($metaData['state'] ?? null) !!} / {!! $formatVal($metaData['pin_code'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Mobile 1 / 2:</td><td>{!! $formatVal($metaData['mobile_1'] ?? null) !!} / {!! $formatVal($metaData['mobile_2'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Review Status:</td><td style="font-weight: 600; color: var(--text-main);">{{ $application->status }}</td></tr>
                                    </table>
                                </div>
                            </div>
                        @elseif($project->type_of_project === 'Differently Abled')
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                                <!-- Col 1 -->
                                <div>
                                    <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">1. Personal Details of Applicant</h4>
                                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">Applicant Name:</td><td style="color: var(--text-main); font-weight: 600;">{!! $formatVal($application->applicant_name) !!} ({!! $formatVal($metaData['gender'] ?? null) !!})</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Date of Birth / Age:</td><td>{!! $formatVal($metaData['dob'] ?? null) !!} / {!! $formatVal($metaData['age'] ?? null) !!} yrs</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Aadhaar / Marital Status:</td><td>{!! $formatVal($metaData['aadhar_number'] ?? null) !!} / {!! $formatVal($metaData['marital_status'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Father's Name:</td><td>{!! $formatVal($metaData['father_name'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Father's Father:</td><td>{!! $formatVal($metaData['fathers_father'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Mother's Name:</td><td>{!! $formatVal($metaData['mother_name'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Guardian / Relation:</td><td>{!! $formatVal($metaData['guardian_name'] ?? null) !!} ({!! $formatVal($metaData['guardian_relation'] ?? null) !!})</td></tr>
                                    </table>

                                    <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">2. Family & Economic Details</h4>
                                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">Male / Female Members:</td><td>M: {!! $formatVal($metaData['male_members'] ?? null) !!} / F: {!! $formatVal($metaData['female_members'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Total Members:</td><td style="font-weight: 600; color: #ffffff;">{!! $formatVal($metaData['total_members'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">People with Disabilities:</td><td>{!! $formatVal($metaData['people_with_disabilities'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Monthly Income:</td><td>{{ isset($metaData['monthly_income']) ? '₹' . number_format($metaData['monthly_income']) : 'N/A' }}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Monthly Cost:</td><td>{{ isset($metaData['monthly_cost']) ? '₹' . number_format($metaData['monthly_cost']) : 'N/A' }}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Source of Income:</td><td>{!! $formatVal($metaData['income_source'] ?? null) !!}</td></tr>
                                    </table>
                                </div>

                                <!-- Col 2 -->
                                <div>
                                    <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">3. Education & Disability Details</h4>
                                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">Studying Institution:</td><td>{!! $formatVal($metaData['studying_institution'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">If not study, reason:</td><td>{!! $formatVal($metaData['not_studying_reason'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Health Status:</td><td>{!! $formatVal($metaData['health_status'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Disability Type:</td><td style="font-weight: 600; color: #ffffff;">{!! $formatVal($metaData['disability_type'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Disability Percentage:</td><td>{!! $formatVal($metaData['disability_percentage'] ?? null) !!}%</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Date/Year of Disability:</td><td>{!! $formatVal($metaData['disability_date'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Level of Disability:</td><td>{!! $formatVal($metaData['disability_level'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Anyone else help?</td><td>{!! $formatVal($metaData['other_help'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Accommodation:</td><td>{!! $formatVal($metaData['accommodation'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Description:</td><td>{!! $formatVal($metaData['description'] ?? null) !!}</td></tr>
                                    </table>

                                    <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">4. Address & Contact Details</h4>
                                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">House Name / Place:</td><td>{!! $formatVal($metaData['house_name'] ?? null) !!} / {!! $formatVal($application->place ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Panchayat / District:</td><td>{!! $formatVal($metaData['panchayat'] ?? null) !!} / {!! $formatVal($metaData['district'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Pincode / Mobile:</td><td>{!! $formatVal($metaData['pincode'] ?? null) !!} / {!! $formatVal($metaData['mobile'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Review Status:</td><td style="font-weight: 600; color: var(--text-main);">{{ $application->status }}</td></tr>
                                    </table>
                                </div>
                            </div>
                        @elseif($project->type_of_project === 'Family Aid')
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                                <!-- Col 1 -->
                                <div>
                                    <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">1. Personal Details of Applicant</h4>
                                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">Applicant Name:</td><td style="color: var(--text-main); font-weight: 600;">{!! $formatVal($application->applicant_name) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Date of Birth / Age:</td><td>{!! $formatVal($metaData['dob'] ?? null) !!} / {!! $formatVal($metaData['age'] ?? null) !!} yrs</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Aadhaar Number:</td><td>{!! $formatVal($metaData['aadhar_number'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Father's Name:</td><td>{!! $formatVal($metaData['father_name'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Father's Father:</td><td>{!! $formatVal($metaData['fathers_father'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Mother's Name:</td><td>{!! $formatVal($metaData['mother_name'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">House / Location:</td><td>{!! $formatVal($metaData['house_name'] ?? null) !!} / {!! $formatVal($metaData['location'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">PO / Panchayat / Dist:</td><td>{!! $formatVal($metaData['post_office'] ?? null) !!} / {!! $formatVal($metaData['panchayat'] ?? null) !!} / {!! $formatVal($metaData['district'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Pin Code / Contact:</td><td>Pin: {!! $formatVal($metaData['pin_code'] ?? null) !!} / Mob: {!! $formatVal($metaData['mobile_1'] ?? null) !!} {{ !empty($metaData['mobile_2']) ? ', ' . $metaData['mobile_2'] : '' }}</td></tr>
                                    </table>

                                    <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">2. Family & Income Details</h4>
                                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">Children in Family:</td><td>Total: {!! $formatVal($metaData['children_total'] ?? null) !!} (M: {!! $formatVal($metaData['children_male'] ?? null) !!} / F: {!! $formatVal($metaData['children_female'] ?? null) !!})</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">NRI Status:</td><td>{!! $formatVal($metaData['nri_status'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Occupation:</td><td>{!! $formatVal($metaData['occupation'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Monthly Income:</td><td>{{ isset($metaData['monthly_income']) ? '₹' . number_format($metaData['monthly_income']) : 'N/A' }}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Other Income Sources:</td><td>{!! $formatVal($metaData['other_income_sources'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Health & Disability:</td><td>Health: {!! $formatVal($metaData['health_status'] ?? null) !!} / Disability: {!! $formatVal($metaData['disability_status'] ?? null) !!}</td></tr>
                                    </table>
                                </div>

                                <!-- Col 2 -->
                                <div>
                                    <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">3. Health & Residence Details</h4>
                                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">Routine Treatment:</td><td>{!! $formatVal($metaData['routine_treatment_explanation'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Chronic Patients:</td><td>{!! $formatVal($metaData['chronic_patients_description'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Residence Information:</td><td style="font-weight: 600; color: #ffffff;">{!! $formatVal($metaData['residence_info'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Own House Condition:</td><td>{!! $formatVal($metaData['own_house_condition'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Own Place / Size:</td><td>Place: {!! $formatVal($metaData['own_place_status'] ?? null) !!} / Size: {!! $formatVal($metaData['own_place_size'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Is there a sequel?</td><td>{!! $formatVal($metaData['sequel_status'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Welfare Areas:</td><td>{!! $formatVal($metaData['welfare_assistance_areas'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Review Status:</td><td style="font-weight: 600; color: var(--text-main);">{{ $application->status }}</td></tr>
                                    </table>
                                </div>
                            </div>
                        @endif
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
                            <h3 style="color: var(--text-main); font-size: 1.2rem; margin-bottom: 0.5rem;">No Application Connected</h3>
                            <p style="color: var(--text-muted); font-size: 0.9rem; max-width: 400px; margin: 0 auto;">Please connect this project to an application using the form below to view application details.</p>
                        </div>
                    @endif
                @endif
                </div>
            </div>
        </div>

        <!-- ================= STAGE 3 PANEL (FILES / PROGRAMMES) ================= -->
        <div class="stage-content-panel" id="stage-content-3">
            <div class="detail-header-panel" style="display: flex; justify-content: space-between; align-items: center;">
                <h2>{{ $isSocialAidProject ? 'PROGRAMME DETAILS' : 'FILES' }}</h2>
                @if($isSocialAidProject && $isProjectManager && !$isLockedForEditing)
                    <button type="button" id="btn-add-programme-main" onclick="openAddProgrammeModal(); return false;" class="btn-custom btn-add-programme-trigger" style="padding: 0.5rem 1.25rem; font-size: 0.85rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.4rem; background: linear-gradient(135deg, #10b981, #059669); border: none; color: #ffffff; border-radius: 6px; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);">
                        <i class="bx bx-plus-circle" style="font-size: 1.1rem;"></i> Add Programme
                    </button>
                @endif
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

                @if($isSocialAidProject)
                    @php
                        $programmes = $project->programmes ? $project->programmes->sortByDesc('created_at')->values() : collect();
                    @endphp

                    <!-- Social Aid Programmes Table -->
                    <div style="background-color: var(--panel-bg); border: 1px solid var(--panel-border); border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 2rem;">
                        <div class="table-responsive-custom" style="overflow-x: auto;">

                            <table class="table-dark-custom" style="width: 100%; border-collapse: collapse; text-align: left;">
                                <thead>
                                    <tr style="border-bottom: 2px solid var(--panel-border); color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase;">
                                        <th style="padding: 0.85rem 1rem; font-weight: 700; width: 70px; text-align: center; white-space: nowrap; vertical-align: middle;">Serial No</th>
                                        <th style="padding: 0.85rem 1rem; font-weight: 700; white-space: nowrap; vertical-align: middle;">Programme Name</th>
                                        <th style="padding: 0.85rem 1rem; font-weight: 700; white-space: nowrap; vertical-align: middle;">Date</th>
                                        <th style="padding: 0.85rem 1rem; font-weight: 700; white-space: nowrap; vertical-align: middle;">Place</th>
                                        <th style="padding: 0.85rem 1rem; font-weight: 700; white-space: nowrap; vertical-align: middle;">Remarks</th>
                                        <th style="padding: 0.85rem 1rem; font-weight: 700; text-align: center; white-space: nowrap; vertical-align: middle;">Checklist &amp; Documents</th>
                                        <th style="padding: 0.85rem 1rem; font-weight: 700; text-align: center; width: 110px; white-space: nowrap; vertical-align: middle;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="social-aid-programmes-tbody">
                                    @forelse($programmes as $idx => $prog)
                                        <tr id="programme-row-{{ $prog->id }}" class="programme-table-row" style="border-bottom: 1px solid var(--panel-border); font-size: 0.875rem; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.02)'" onmouseout="this.style.backgroundColor='transparent'">
                                            <td class="serial-no-cell" style="padding: 0.85rem 1rem; text-align: center; font-weight: 600; color: var(--text-muted); vertical-align: middle;">{{ $idx + 1 }}</td>
                                            <td style="padding: 0.85rem 1rem; font-weight: 700; color: var(--text-main); vertical-align: middle;">{{ $prog->programme_name ?? 'Untitled Programme' }}</td>
                                            <td style="padding: 0.85rem 1rem; color: var(--text-main); white-space: nowrap; vertical-align: middle;">{{ !empty($prog->date) ? date('d-M-Y', strtotime($prog->date)) : '-' }}</td>
                                            <td style="padding: 0.85rem 1rem; color: var(--text-main); vertical-align: middle;">{{ $prog->place ?? '-' }}</td>
                                            <td style="padding: 0.85rem 1rem; color: var(--text-main); vertical-align: middle;">{{ $prog->remarks ?? '-' }}</td>
                                            <td style="padding: 0.85rem 1rem; text-align: center; vertical-align: middle;">

                                                @php
                                                    $items = [
                                                        'present' => 'Present',
                                                        'photo' => 'Photo',
                                                        'marklist' => 'Marklist',
                                                        'thanks_letter' => 'Thanks Letter',
                                                        'report_form' => 'Report Form',
                                                        'other_document' => 'Other Doc'
                                                    ];
                                                @endphp
                                                <div style="display: flex; gap: 0.3rem; flex-wrap: nowrap; justify-content: center; align-items: center; white-space: nowrap; margin: 0 auto;">
                                                    @foreach($items as $key => $label)
                                                        @php $isTicked = $prog->{$key . '_ticked'} ?? false; @endphp
                                                        @if($isProjectManager && !$isLockedForEditing)
                                                            <button type="button" onclick="toggleProgrammeChecklistTick(this, {{ $prog->id }}, '{{ $key }}')"
                                                                title="{{ $label }}: {{ $isTicked ? 'Ticked (Click to untick)' : 'Not ticked (Click to tick)' }}"
                                                                style="display: inline-flex; align-items: center; gap: 0.2rem; padding: 0.25rem 0.5rem; border-radius: 20px; font-size: 0.73rem; font-weight: 600; cursor: pointer; transition: all 0.2s; outline: none; white-space: nowrap; flex-shrink: 0;
                                                                    {{ $isTicked ? 'background-color: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.35); color: #059669;' : 'background-color: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3); color: #d97706;' }}">
                                                                <i class="bx {{ $isTicked ? 'bxs-check-circle' : 'bx-circle' }}" style="font-size: 0.8rem;"></i>
                                                                {{ $label }}
                                                            </button>
                                                        @else
                                                            <span style="display: inline-flex; align-items: center; gap: 0.2rem; padding: 0.25rem 0.5rem; border-radius: 20px; font-size: 0.73rem; font-weight: 600; white-space: nowrap; flex-shrink: 0;
                                                                {{ $isTicked ? 'background-color: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.35); color: #059669;' : 'background-color: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3); color: #d97706;' }}">
                                                                <i class="bx {{ $isTicked ? 'bxs-check-circle' : 'bx-circle' }}" style="font-size: 0.8rem;"></i>
                                                                {{ $label }}
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td style="padding: 0.85rem 1rem; text-align: center; white-space: nowrap; vertical-align: middle;">
                                                <!-- View Button (Visible to all users) -->
                                                <button type="button" onclick="openViewProgrammeModal(this)" data-prog="{{ json_encode($prog) }}" style="background: transparent; border: none; color: #10b981; cursor: pointer; padding: 0.3rem; margin-right: 0.25rem; border-radius: 4px; transition: background 0.2s;" title="View Details" onmouseover="this.style.background='rgba(16, 185, 129, 0.1)'" onmouseout="this.style.background='transparent'">
                                                    <i class="bx bx-show" style="font-size: 1.15rem; vertical-align: middle;"></i>
                                                </button>

                                                @if($isProjectManager && !$isLockedForEditing)
                                                    <button type="button" onclick="openEditProgrammeModal(this)" data-prog="{{ json_encode($prog) }}" style="background: transparent; border: none; color: #0284c7; cursor: pointer; padding: 0.3rem; margin-right: 0.25rem; border-radius: 4px; transition: background 0.2s;" title="Edit Programme" onmouseover="this.style.background='rgba(2, 132, 199, 0.1)'" onmouseout="this.style.background='transparent'">
                                                        <i class="bx bx-pencil" style="font-size: 1.15rem; vertical-align: middle;"></i>
                                                    </button>
                                                @endif

                                                @if($isSuperAdmin || $isHod || $isCoo)
                                                    <button type="button" onclick="handleDeleteProgramme(this, {{ $prog->id }}, '{{ route('projects.' . $projectRouteKey . '.delete_programme', [$project->id, $prog->id]) }}')" style="background: transparent; border: none; color: #ef4444; cursor: pointer; padding: 0.3rem; border-radius: 4px; transition: background 0.2s;" title="Delete Programme" onmouseover="this.style.background='rgba(239, 68, 68, 0.1)'" onmouseout="this.style.background='transparent'">
                                                        <i class="bx bx-trash" style="font-size: 1.15rem; vertical-align: middle;"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="no-programmes-row">
                                            <td colspan="7" style="padding: 2.5rem 1rem; text-align: center; color: var(--text-muted); font-style: italic;">
                                                No programme records found. Click "Add Programme" to add one.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>

                    </div>

                @else
                    <table class="stage-table">
                        <thead>
                            <tr>
                                <th>Document Name</th>
                                <th style="width: 250px;">Ticked At</th>
                                <th style="width: 150px; text-align: center;">Action</th>
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
                                $docRecord = $project->files_with_timestamps;
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
                                @endphp
                                <tr>
                                    <td style="font-weight: 600; color: var(--text-main); vertical-align: middle;">{{ $doc }}</td>
                                    <td id="ticked-at-{{ str_replace(' ', '_', $doc) }}" style="color: var(--text-muted); font-size: 0.9rem; vertical-align: middle;">
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
                @endif
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

                        {{-- COO / HOD: Always see Approve & Reject at Stage 4 --}}
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

                        {{-- PM: Submit button (if not yet submitted) --}}
                        @if($isPmOnly || $isSuperAdmin)
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
                    $beforePhotos = $pFiles['photos_before'] ?? [];
                    $startingPhotos = $pFiles['photos_starting'] ?? [];
                    $inbetweenPhotos = $pFiles['photos_inbetween'] ?? [];
                    $afterPhotos = $pFiles['photos_after'] ?? ($pFiles['photos'] ?? []);
                    $bannerPhotos = $pFiles['photos_banner'] ?? [];
                    $stonePhotos = $pFiles['photos_stone'] ?? [];
                    $inaugurationPhotos = $pFiles['photos_inauguration'] ?? [];
                    $compDetails = $pFiles['completion_details'] ?? [];
                @endphp

                <!-- Completion Documents (Stage 6 Upload/Reference) -->
                <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--panel-border); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
                    <h3 style="color: var(--text-main); font-size: 1rem; margin-top: 0; margin-bottom: 1.25rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--panel-border); padding-bottom: 0.75rem;">Completion Documents</h3>

                    @if($project->status === 'Completed' || $project->status === 'Approved')
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <span style="font-weight: 600;">Completion Certificate:</span>
                                @if(!empty($compCert))
                                    <a href="{{ asset($compCert) }}" target="_blank" class="btn-custom" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; background: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent-green); color: var(--accent-green); text-decoration: none;">View Certificate</a>
                                @else
                                    <span style="color: var(--accent-red); font-weight: 600;">Pending</span>
                                @endif
                            </div>
                            <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <span style="font-weight: 600;">Measurement Book:</span>
                                @if(!empty($measBook))
                                    <a href="{{ asset($measBook) }}" target="_blank" class="btn-custom" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; background: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent-green); color: var(--accent-green); text-decoration: none;">View Book</a>
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
                                @if($compCertTime)
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">Uploaded at: {{ $compCertTime }}</span>
                                @endif
                            </div>
                            <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                                @if(!empty($compCert) && $compCert !== "1")
                                    <a href="{{ asset($compCert) }}" target="_blank" class="btn-custom" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; background: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent-green); color: var(--accent-green); cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem; text-decoration: none;">
                                        <i class="bx bx-show"></i> View Certificate
                                    </a>
                                    @if($isProjectManager && !$isLockedForEditing)
                                        <form action="{{ route('projects.toggle_file', $project->id) }}" method="POST" style="margin: 0; display: inline-flex;">
                                            @csrf
                                            <input type="hidden" name="document_name" value="Completion Certificate">
                                            <button type="submit" class="btn-danger-custom" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.25rem;" title="Delete File">
                                                <i class="bx bx-trash"></i> Delete
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    @if($isProjectManager && !$isLockedForEditing)
                                        <form action="{{ route('projects.upload_file', $project->id) }}" method="POST" enctype="multipart/form-data" style="margin: 0; display: inline-flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
                                            @csrf
                                            <input type="hidden" name="document_name" value="Completion Certificate">
                                            <input type="file" name="file" required style="font-size: 0.8rem; max-width: 220px; color: var(--text-muted);">
                                            <button type="submit" class="btn-custom" style="padding: 0.4rem 1rem; font-size: 0.85rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem;">
                                                <i class="bx bx-upload"></i> Upload
                                            </button>
                                        </form>
                                    @else
                                        <span style="color: var(--accent-red); font-weight: 500; display: inline-flex; align-items: center; gap: 0.35rem; background: rgba(239, 68, 68, 0.1); border: 1px solid var(--accent-red); padding: 0.3rem 0.65rem; border-radius: 6px; font-size: 0.8rem;">
                                            <i class="bx bx-x-circle" style="font-size: 1rem;"></i> Pending Upload
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <!-- Measurement Book row -->
                        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; padding: 0.75rem 0;">
                            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                <span style="font-weight: 600; color: var(--text-main); min-width: 200px;">Measurement Book</span>
                                @if($measBookTime)
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">Uploaded at: {{ $measBookTime }}</span>
                                @endif
                            </div>
                            <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                                @if(!empty($measBook) && $measBook !== "1")
                                    <a href="{{ asset($measBook) }}" target="_blank" class="btn-custom" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; background: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent-green); color: var(--accent-green); cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem; text-decoration: none;">
                                        <i class="bx bx-show"></i> View Book
                                    </a>
                                    @if($isProjectManager && !$isLockedForEditing)
                                        <form action="{{ route('projects.toggle_file', $project->id) }}" method="POST" style="margin: 0; display: inline-flex;">
                                            @csrf
                                            <input type="hidden" name="document_name" value="Measurement Book">
                                            <button type="submit" class="btn-danger-custom" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.25rem;" title="Delete File">
                                                <i class="bx bx-trash"></i> Delete
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    @if($isProjectManager && !$isLockedForEditing)
                                        <form action="{{ route('projects.upload_file', $project->id) }}" method="POST" enctype="multipart/form-data" style="margin: 0; display: inline-flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
                                            @csrf
                                            <input type="hidden" name="document_name" value="Measurement Book">
                                            <input type="file" name="file" required style="font-size: 0.8rem; max-width: 220px; color: var(--text-muted);">
                                            <button type="submit" class="btn-custom" style="padding: 0.4rem 1rem; font-size: 0.85rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem;">
                                                <i class="bx bx-upload"></i> Upload
                                            </button>
                                        </form>
                                    @else
                                        <span style="color: var(--accent-red); font-weight: 500; display: inline-flex; align-items: center; gap: 0.35rem; background: rgba(239, 68, 68, 0.1); border: 1px solid var(--accent-red); padding: 0.3rem 0.65rem; border-radius: 6px; font-size: 0.8rem;">
                                            <i class="bx bx-x-circle" style="font-size: 1rem;"></i> Pending Upload
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
                                        <input type="url" name="location_map_link" placeholder="Paste Google Maps URL here…" required style="background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #ffffff; padding: 0.45rem 0.75rem; border-radius: 6px; font-size: 0.8rem; width: 220px; outline: none;" value="{{ $locationMapLink }}">
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
                                            <i class="bx bx-upload"></i> Upload Photo
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

                    @if($isProjectManager && !$isLockedForEditing)
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
                    @elseif($project->stage == 6)
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
                    @elseif($project->stage == 5)
                        <div style="background: rgba(255,255,255,0.01); border: 1px solid var(--panel-border); padding: 1.25rem; border-radius: 8px;">
                            <h4 style="color: var(--text-main); font-size: 0.95rem; font-weight: 700; margin: 0 0 0.5rem 0; text-transform: uppercase;">Promote to Stage 6</h4>
                            @if($isPmOnly || $isEngineerOnly || $isSuperAdmin)
                                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.5rem;">
                                    Once all expenses have been logged and the evaluation is complete, you can promote this project to Stage 6 (Completion Stage).
                                </p>
                                <form action="{{ route('projects.approve', $project->id) }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <input type="hidden" name="action" value="promote_to_stage6">
                                    <button type="submit" class="btn-custom" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border: none; color: #ffffff; font-weight: 700; padding: 0.6rem 1.8rem; cursor: pointer; border-radius: 6px;">
                                        <i class="bx bx-right-arrow-alt"></i> Complete Stage 5 & Move to Stage 6
                                    </button>
                                </form>
                            @else
                                <div style="background-color: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); color: #f59e0b; padding: 0.85rem 1.25rem; border-radius: 6px; font-size: 0.9rem; font-weight: 600; display: inline-block;">
                                    <i class="bx bx-time-five"></i> Awaiting Project Manager or Engineer to complete Stage 5 and promote to Stage 6.
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

            </div>
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
        var allApplicationsData = @json($allApplications);

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

                    const cellId = 'ticked-at-' + docName.replace(/ /g, '_');
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

                        const incomeText = meta.monthly_income ? '₹' + Number(meta.monthly_income).toLocaleString() : 'N/A';
            const expenseText = meta.monthly_expense ? '₹' + Number(meta.monthly_expense).toLocaleString() : 'N/A';
            const costText = meta.monthly_cost ? '₹' + Number(meta.monthly_cost).toLocaleString() : 'N/A';

            if (projectType === 'Orphan Care') {
                container.innerHTML = `
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                        <div>
                            <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">1. Orphan & Family Details</h4>
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">Orphan Name:</td><td style="color: var(--text-main); font-weight: 600;">${formatVal(app.applicant_name)} (${formatVal(meta.gender)})</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Date of Birth / Age:</td><td>${formatVal(meta.dob)} / ${formatVal(meta.age)} yrs</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Aadhar Number:</td><td>${formatVal(meta.aadhar_number)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Father's Name:</td><td>${formatVal(meta.father_name)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Grandfather's Name:</td><td>${formatVal(meta.grandfather_name)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Mother's Name:</td><td>${formatVal(meta.mother_name)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Mother's Father Name:</td><td>${formatVal(meta.mothers_father_name)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Guardian / Relation:</td><td>${formatVal(meta.guardian_name)} (${formatVal(meta.guardian_relation)})</td></tr>
                            </table>

                            <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">2. Parental Death & Sibling Details</h4>
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">Father's Death Date:</td><td>${formatVal(meta.father_death_date)} <span style="font-size: 0.8rem; color: var(--text-muted);">(${formatVal(meta.father_death_cause)})</span></td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Mother Alive Status:</td><td>${formatVal(meta.mother_alive_status)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Mother's Death Date:</td><td>${formatVal(meta.mother_death_date)} <span style="font-size: 0.8rem; color: var(--text-muted);">(${formatVal(meta.mother_death_cause)})</span></td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Mother Re-Married?</td><td>${formatVal(meta.mother_remarried_status)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Brothers & Sisters:</td><td>Total: ${formatVal(meta.siblings_total)} (M: ${formatVal(meta.siblings_male)} / F: ${formatVal(meta.siblings_female)})</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Monthly Income:</td><td>${incomeText}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Monthly Expense:</td><td>${expenseText}</td></tr>
                            </table>
                        </div>

                        <div>
                            <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">3. Education & House Details</h4>
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">Type Of House:</td><td>${formatVal(meta.house_type)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">School Name:</td><td>${formatVal(meta.school_name)} (Class: ${formatVal(meta.school_class)})</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Madrassa Name:</td><td>${formatVal(meta.madrassa_name)} (Class: ${formatVal(meta.madrassa_class)})</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">If Not Studying, Reason:</td><td>${formatVal(meta.not_studying_reason)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Health Status:</td><td>${formatVal(meta.health_status)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Sponsorship Details:</td><td>${formatVal(meta.sponsorship_details)}</td></tr>
                            </table>

                            <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">4. Address & Contact Details</h4>
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">House Name / Place:</td><td>${formatVal(meta.house_name)} / ${formatVal(app.place)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Town / Post Office:</td><td>${formatVal(meta.town)} / ${formatVal(meta.post_office)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">District / State / Pin:</td><td>${formatVal(meta.district)} / ${formatVal(meta.state)} / ${formatVal(meta.pin_code)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Mobile 1 / 2:</td><td>${formatVal(meta.mobile_1)} / ${formatVal(meta.mobile_2)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Review Status:</td><td style="font-weight: 600; color: var(--text-main);">${app.status}</td></tr>
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
            } else if (projectType === 'Differently Abled') {
                container.innerHTML = `
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                        <div>
                            <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">1. Personal Details of Applicant</h4>
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">Applicant Name:</td><td style="color: var(--text-main); font-weight: 600;">${formatVal(app.applicant_name)} (${formatVal(meta.gender)})</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Date of Birth / Age:</td><td>${formatVal(meta.dob)} / ${formatVal(meta.age)} yrs</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Aadhaar / Marital Status:</td><td>${formatVal(meta.aadhar_number)} / ${formatVal(meta.marital_status)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Father's Name:</td><td>${formatVal(meta.father_name)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Father's Father:</td><td>${formatVal(meta.fathers_father)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Mother's Name:</td><td>${formatVal(meta.mother_name)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Guardian / Relation:</td><td>${formatVal(meta.guardian_name)} (${formatVal(meta.guardian_relation)})</td></tr>
                            </table>

                            <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">2. Family & Economic Details</h4>
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">Male / Female Members:</td><td>M: ${formatVal(meta.male_members)} / F: ${formatVal(meta.female_members)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Total Members:</td><td style="font-weight: 600; color: #ffffff;">${formatVal(meta.total_members)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">People with Disabilities:</td><td>${formatVal(meta.people_with_disabilities)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Monthly Income:</td><td>${incomeText}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Monthly Cost:</td><td>${costText}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Source of Income:</td><td>${formatVal(meta.income_source)}</td></tr>
                            </table>
                        </div>

                        <div>
                            <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">3. Education & Disability Details</h4>
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">Studying Institution:</td><td>${formatVal(meta.studying_institution)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">If not study, reason:</td><td>${formatVal(meta.not_studying_reason)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Health Status:</td><td>${formatVal(meta.health_status)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Disability Type:</td><td style="font-weight: 600; color: #ffffff;">${formatVal(meta.disability_type)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Disability Percentage:</td><td>${formatVal(meta.disability_percentage)}%</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Date/Year of Disability:</td><td>${formatVal(meta.disability_date)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Level of Disability:</td><td>${formatVal(meta.disability_level)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Anyone else help?</td><td>${formatVal(meta.other_help)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Accommodation:</td><td>${formatVal(meta.accommodation)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Description:</td><td>${formatVal(meta.description)}</td></tr>
                            </table>

                            <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">4. Address & Contact Details</h4>
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">House Name / Place:</td><td>${formatVal(meta.house_name)} / ${formatVal(app.place)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Panchayat / District:</td><td>${formatVal(meta.panchayat)} / ${formatVal(meta.district)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Pincode / Mobile:</td><td>${formatVal(meta.pincode)} / ${formatVal(meta.mobile)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Review Status:</td><td style="font-weight: 600; color: var(--text-main);">${app.status}</td></tr>
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
            } else if (projectType === 'Family Aid') {
                container.innerHTML = `
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                        <div>
                            <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">1. Personal Details of Applicant</h4>
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">Applicant Name:</td><td style="color: var(--text-main); font-weight: 600;">${formatVal(app.applicant_name)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Date of Birth / Age:</td><td>${formatVal(meta.dob)} / ${formatVal(meta.age)} yrs</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Aadhaar Number:</td><td>${formatVal(meta.aadhar_number)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Father's Name:</td><td>${formatVal(meta.father_name)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Father's Father:</td><td>${formatVal(meta.fathers_father)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Mother's Name:</td><td>${formatVal(meta.mother_name)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">House / Location:</td><td>${formatVal(meta.house_name)} / ${formatVal(meta.location)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">PO / Panchayat / Dist:</td><td>${formatVal(meta.post_office)} / ${formatVal(meta.panchayat)} / ${formatVal(meta.district)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Pin Code / Contact:</td><td>Pin: ${formatVal(meta.pin_code)} / Mob: ${formatVal(meta.mobile_1)} ${meta.mobile_2 ? ', ' + meta.mobile_2 : ''}</td></tr>
                            </table>

                            <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">2. Family & Income Details</h4>
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">Children in Family:</td><td>Total: ${formatVal(meta.children_total)} (M: ${formatVal(meta.children_male)} / F: ${formatVal(meta.children_female)})</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">NRI Status:</td><td>${formatVal(meta.nri_status)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Occupation:</td><td>${formatVal(meta.occupation)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Monthly Income:</td><td>${incomeText}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Other Income Sources:</td><td>${formatVal(meta.other_income_sources)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Health & Disability:</td><td>Health: ${formatVal(meta.health_status)} / Disability: ${formatVal(meta.disability_status)}</td></tr>
                            </table>
                        </div>

                        <div>
                            <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">3. Health & Residence Details</h4>
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">Routine Treatment:</td><td>${formatVal(meta.routine_treatment_explanation)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Chronic Patients:</td><td>${formatVal(meta.chronic_patients_description)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Residence Information:</td><td style="font-weight: 600; color: #ffffff;">${formatVal(meta.residence_info)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Own House Condition:</td><td>${formatVal(meta.own_house_condition)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Own Place / Size:</td><td>Place: ${formatVal(meta.own_place_status)} / Size: ${formatVal(meta.own_place_size)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Is there a sequel?</td><td>${formatVal(meta.sequel_status)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Welfare Areas:</td><td>${formatVal(meta.welfare_assistance_areas)}</td></tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Review Status:</td><td style="font-weight: 600; color: var(--text-main);">${app.status}</td></tr>
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
        }

        var activeProjectId = {{ $project->id }};
        var activeProjectStage = {{ $project->stage }};
        var isProjectApproved = "{{ ($project->status === 'Approved' || $project->status === 'Completed') ? '1' : '0' }}";
        var hasApplication = "{{ empty($project->application_id) ? '0' : '1' }}";
        var projectType = "{{ $project->type_of_project }}";

        function switchStage(stageNum) {
            let isLocked = false;
            const isSixStage = ['Education Center', 'Cultural Center', 'Hospital or Clinics', 'Shops and Others', 'House', 'Drinking Water - Group Level', 'Drinking Water - Individual Level', 'General'].includes(projectType);
            if (['Orphan Care', 'Differently Abled', 'Family Aid'].includes(projectType)) {

                isLocked = false;
            } else if (isSixStage) {
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


        // Initialize display to show the stage panel
        function initStageDisplay() {
            const savedStage = sessionStorage.getItem('current_project_stage_{{ $project->id }}');
            let stageToLoad = 1;
            if (savedStage) {
                const stageNum = Number(savedStage);
                let isLocked = false;
                const isSixStage = ['Education Center', 'Cultural Center', 'Hospital or Clinics', 'Shops and Others', 'House', 'Drinking Water - Group Level', 'Drinking Water - Individual Level', 'General'].includes(projectType);
                if (['Orphan Care', 'Differently Abled', 'Family Aid'].includes(projectType)) {
                    isLocked = false;
                } else if (isSixStage) {
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

    <!-- Add Programme Modal -->
    <div id="addProgrammeModal" style="display: none; position: fixed; z-index: 1100; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.6); align-items: center; justify-content: center; padding: 1rem;">
        <div style="background-color: var(--panel-bg); border: 1px solid var(--panel-border); padding: 2rem; border-radius: 12px; width: 100%; max-width: 600px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); max-height: 90vh; overflow-y: auto; position: relative;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="color: var(--text-main); margin: 0; font-size: 1.2rem; font-weight: 700; text-transform: uppercase;">Add New Programme</h3>
                <button type="button" onclick="closeAddProgrammeModal()" style="background: transparent; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; padding: 0.2rem; display: flex; align-items: center; justify-content: center; transition: color 0.2s;" onmouseover="this.style.color='var(--accent-red)'" onmouseout="this.style.color='var(--text-muted)'" title="Close Modal">
                    <i class="bx bx-x"></i>
                </button>
            </div>
            <form id="addProgrammeForm" action="{{ route('projects.' . $projectRouteKey . '.add_programme', $project->id) }}" method="POST" onsubmit="handleAddProgrammeSubmit(event); return false;" style="margin: 0;">

                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <div style="grid-column: span 2;">
                        <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem; font-weight: 600;">Programme Name *</label>
                        <select name="programme_name" id="add_prog_name_select" required class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" onchange="toggleSpecifyProgrammeField(this, 'add_prog_other_name_wrapper', 'add_prog_other_name_input')">
                            <option value="" disabled selected>-- Select Programme --</option>
                            <option value="Cluster Camp">Cluster Camp</option>
                            <option value="Report Collection Programme">Report Collection Programme</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                    <div id="add_prog_other_name_wrapper" style="grid-column: span 2; display: none;">
                        <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem; font-weight: 600;">Specify Programme Name *</label>
                        <input type="text" id="add_prog_other_name_input" name="other_programme_name" placeholder="Enter custom programme name..." class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;">
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

                <h4 style="color: var(--accent-cyan); font-size: 0.9rem; text-transform: uppercase; margin: 1.5rem 0 1rem 0; font-weight: 700; border-bottom: 1px solid var(--panel-border); padding-bottom: 0.4rem;">Tick Checklist (Select Completed Items)</h4>
                
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
                    <button type="button" onclick="closeAddProgrammeModal()" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); cursor: pointer;">Cancel</button>
                    <button type="submit" class="btn-custom" style="background: linear-gradient(135deg, #10b981, #059669); border: none; color: #ffffff; cursor: pointer;">Add Programme</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Programme Modal -->
    <div id="editProgrammeModal" style="display: none; position: fixed; z-index: 1100; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.6); align-items: center; justify-content: center; padding: 1rem;">
        <div style="background-color: var(--panel-bg); border: 1px solid var(--panel-border); padding: 2rem; border-radius: 12px; width: 100%; max-width: 600px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); max-height: 90vh; overflow-y: auto; position: relative;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="color: var(--text-main); margin: 0; font-size: 1.2rem; font-weight: 700; text-transform: uppercase;">Edit Programme</h3>
                <button type="button" onclick="closeEditProgrammeModal()" style="background: transparent; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; padding: 0.2rem; display: flex; align-items: center; justify-content: center; transition: color 0.2s;" onmouseover="this.style.color='var(--accent-red)'" onmouseout="this.style.color='var(--text-muted)'" title="Close Modal">
                    <i class="bx bx-x"></i>
                </button>
            </div>
            <form id="editProgrammeForm" method="POST" onsubmit="handleEditProgrammeSubmit(event)" style="margin: 0;">


                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <div style="grid-column: span 2;">
                        <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem; font-weight: 600;">Programme Name *</label>
                        <select name="programme_name" id="edit_prog_name_select" required class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" onchange="toggleSpecifyProgrammeField(this, 'edit_prog_other_name_wrapper', 'edit_prog_other_name_input')">
                            <option value="" disabled>-- Select Programme --</option>
                            <option value="Cluster Camp">Cluster Camp</option>
                            <option value="Report Collection Programme">Report Collection Programme</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                    <div id="edit_prog_other_name_wrapper" style="grid-column: span 2; display: none;">
                        <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem; font-weight: 600;">Specify Programme Name *</label>
                        <input type="text" id="edit_prog_other_name_input" name="other_programme_name" placeholder="Enter custom programme name..." class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;">
                    </div>
                    <div>
                        <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem; font-weight: 600;">Date *</label>
                        <input type="date" id="edit_prog_date" name="date" required class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;">
                    </div>
                    <div>
                        <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem; font-weight: 600;">Place</label>
                        <input type="text" id="edit_prog_place" name="place" class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;">
                    </div>
                    <div style="grid-column: span 2;">
                        <label style="display: block; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 0.5rem; font-weight: 600;">Remarks</label>
                        <input type="text" id="edit_prog_remarks" name="remarks" placeholder="Enter remarks (optional)..." class="form-control-dark" style="width: 100%; padding: 0.6rem 0.8rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;">
                    </div>
                </div>

                <h4 style="color: var(--accent-cyan); font-size: 0.9rem; text-transform: uppercase; margin: 1.5rem 0 1rem 0; font-weight: 700; border-bottom: 1px solid var(--panel-border); padding-bottom: 0.4rem;">Tick Checklist (Select Completed Items)</h4>
                
                <div style="display: grid; grid-template-columns: 1fr; gap: 0.85rem; margin-bottom: 1.5rem; background: rgba(255,255,255,0.02); padding: 1rem; border-radius: 8px; border: 1px solid var(--panel-border);">
                    <label style="display: flex; align-items: center; gap: 0.75rem; color: var(--text-main); font-size: 0.9rem; font-weight: 600; cursor: pointer; padding: 0.25rem 0;">
                        <input type="checkbox" id="edit_prog_present_ticked" name="present_ticked" value="1" style="width: 18px; height: 18px; cursor: pointer;">
                        Present / Attendance
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.75rem; color: var(--text-main); font-size: 0.9rem; font-weight: 600; cursor: pointer; padding: 0.25rem 0;">
                        <input type="checkbox" id="edit_prog_photo_ticked" name="photo_ticked" value="1" style="width: 18px; height: 18px; cursor: pointer;">
                        Photo
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.75rem; color: var(--text-main); font-size: 0.9rem; font-weight: 600; cursor: pointer; padding: 0.25rem 0;">
                        <input type="checkbox" id="edit_prog_marklist_ticked" name="marklist_ticked" value="1" style="width: 18px; height: 18px; cursor: pointer;">
                        Marklist
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.75rem; color: var(--text-main); font-size: 0.9rem; font-weight: 600; cursor: pointer; padding: 0.25rem 0;">
                        <input type="checkbox" id="edit_prog_thanks_letter_ticked" name="thanks_letter_ticked" value="1" style="width: 18px; height: 18px; cursor: pointer;">
                        Thanks Letter
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.75rem; color: var(--text-main); font-size: 0.9rem; font-weight: 600; cursor: pointer; padding: 0.25rem 0;">
                        <input type="checkbox" id="edit_prog_report_form_ticked" name="report_form_ticked" value="1" style="width: 18px; height: 18px; cursor: pointer;">
                        Report Form
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.75rem; color: var(--text-main); font-size: 0.9rem; font-weight: 600; cursor: pointer; padding: 0.25rem 0;">
                        <input type="checkbox" id="edit_prog_other_document_ticked" name="other_document_ticked" value="1" style="width: 18px; height: 18px; cursor: pointer;">
                        Other Document
                    </label>
                </div>

                <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button type="button" onclick="closeEditProgrammeModal()" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); cursor: pointer;">Cancel</button>
                    <button type="submit" class="btn-custom" style="background: linear-gradient(135deg, #0284c7, #0369a1); border: none; color: #ffffff; cursor: pointer;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Programme Modal -->
    <div id="viewProgrammeModal" style="display: none; position: fixed; z-index: 1100; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.6); align-items: center; justify-content: center; padding: 1rem;">
        <div style="background-color: var(--panel-bg); border: 1px solid var(--panel-border); padding: 2rem; border-radius: 12px; width: 100%; max-width: 600px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); max-height: 90vh; overflow-y: auto; position: relative;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem;">
                <h3 style="color: var(--text-main); margin: 0; font-size: 1.2rem; font-weight: 700; text-transform: uppercase;">Programme Details</h3>
                <button type="button" onclick="closeViewProgrammeModal()" style="background: transparent; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; padding: 0.2rem; display: flex; align-items: center; justify-content: center; transition: color 0.2s;" onmouseover="this.style.color='var(--accent-red)'" onmouseout="this.style.color='var(--text-muted)'" title="Close Modal">
                    <i class="bx bx-x"></i>
                </button>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="grid-column: span 2; background: rgba(255,255,255,0.02); padding: 1rem; border-radius: 8px; border: 1px solid var(--panel-border);">
                    <span style="display: block; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; font-weight: 700; margin-bottom: 0.25rem;">Programme Name</span>
                    <span id="view_prog_name" style="color: var(--text-main); font-size: 1.05rem; font-weight: 700;">-</span>
                </div>
                <div style="background: rgba(255,255,255,0.02); padding: 1rem; border-radius: 8px; border: 1px solid var(--panel-border);">
                    <span style="display: block; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; font-weight: 700; margin-bottom: 0.25rem;">Date</span>
                    <span id="view_prog_date" style="color: var(--text-main); font-size: 0.95rem; font-weight: 600;">-</span>
                </div>
                <div style="background: rgba(255,255,255,0.02); padding: 1rem; border-radius: 8px; border: 1px solid var(--panel-border);">
                    <span style="display: block; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; font-weight: 700; margin-bottom: 0.25rem;">Place</span>
                    <span id="view_prog_place" style="color: var(--text-main); font-size: 0.95rem; font-weight: 600;">-</span>
                </div>
                <div style="grid-column: span 2; background: rgba(255,255,255,0.02); padding: 1rem; border-radius: 8px; border: 1px solid var(--panel-border);">
                    <span style="display: block; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; font-weight: 700; margin-bottom: 0.25rem;">Remarks</span>
                    <span id="view_prog_remarks" style="color: var(--text-main); font-size: 0.9rem; line-height: 1.4;">-</span>
                </div>
            </div>

            <h4 style="color: var(--accent-cyan); font-size: 0.9rem; text-transform: uppercase; margin: 1.5rem 0 1rem 0; font-weight: 700; border-bottom: 1px solid var(--panel-border); padding-bottom: 0.4rem;">Checklist &amp; Document Status</h4>

            <div id="view_prog_checklist_container" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.5rem; background: rgba(255,255,255,0.02); padding: 1rem; border-radius: 8px; border: 1px solid var(--panel-border);">
            </div>

            <div style="display: flex; justify-content: flex-end;">
                <button type="button" onclick="closeViewProgrammeModal()" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); cursor: pointer; padding: 0.5rem 1.5rem;">Close</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddProgrammeModal();
                closeEditProgrammeModal();
                closeViewProgrammeModal();
            }
        });

        function openViewProgrammeModal(btnElement) {
            const rawProg = btnElement.getAttribute('data-prog');
            if (!rawProg) return;
            try {
                const prog = JSON.parse(rawProg);
                const modal = document.getElementById('viewProgrammeModal');
                if (modal && prog) {
                    document.body.appendChild(modal);
                    modal.style.setProperty('z-index', '999999', 'important');
                    modal.style.setProperty('display', 'flex', 'important');

                    document.getElementById('view_prog_name').innerText = prog.programme_name || 'N/A';
                    document.getElementById('view_prog_date').innerText = prog.date ? new Date(prog.date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : 'N/A';
                    document.getElementById('view_prog_place').innerText = prog.place || 'N/A';
                    document.getElementById('view_prog_remarks').innerText = prog.remarks || 'No remarks provided';

                    const items = {
                        'present': 'Present / Attendance',
                        'photo': 'Photo',
                        'marklist': 'Marklist',
                        'thanks_letter': 'Thanks Letter',
                        'report_form': 'Report Form',
                        'other_document': 'Other Document'
                    };

                    const container = document.getElementById('view_prog_checklist_container');
                    if (container) {
                        let html = '';
                        for (const [key, label] of Object.entries(items)) {
                            const isTicked = !!(prog[key + '_ticked']);
                            html += `
                                <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0.75rem; border-radius: 6px; ${isTicked ? 'background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3); color: #059669;' : 'background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.25); color: #d97706;'}">
                                    <span style="font-size: 0.85rem; font-weight: 600;">${label}</span>
                                    <span style="font-size: 0.8rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.25rem;">
                                        <i class="bx ${isTicked ? 'bxs-check-circle' : 'bx-x-circle'}" style="font-size: 1rem;"></i> ${isTicked ? 'Completed' : 'Pending'}
                                    </span>
                                </div>
                            `;
                        }
                        container.innerHTML = html;
                    }
                }
            } catch(e) {
                console.error('Error opening view programme modal:', e);
            }
        }
        window.openViewProgrammeModal = openViewProgrammeModal;

        function closeViewProgrammeModal() {
            const modal = document.getElementById('viewProgrammeModal');
            if (modal) modal.style.setProperty('display', 'none', 'important');
        }
        window.closeViewProgrammeModal = closeViewProgrammeModal;

        function toggleSpecifyProgrammeField(selectElem, wrapperId, inputId) {
            const wrapper = document.getElementById(wrapperId);
            const input = document.getElementById(inputId);
            if (!wrapper || !input) return;

            if (selectElem.value === 'Others') {
                wrapper.style.display = 'block';
                input.required = true;
            } else {
                wrapper.style.display = 'none';
                input.required = false;
                input.value = '';
            }
        }
        window.toggleSpecifyProgrammeField = toggleSpecifyProgrammeField;

        window.openAddProgrammeModal = function openAddProgrammeModal() {
            const modal = document.getElementById('addProgrammeModal');
            if (modal) {
                document.body.appendChild(modal);
                modal.style.setProperty('z-index', '999999', 'important');
                modal.style.setProperty('display', 'flex', 'important');
                const selectElem = document.getElementById('add_prog_name_select');
                if (selectElem) {
                    selectElem.selectedIndex = 0;
                    toggleSpecifyProgrammeField(selectElem, 'add_prog_other_name_wrapper', 'add_prog_other_name_input');
                }
            }
        };
        function closeAddProgrammeModal() {
            const modal = document.getElementById("addProgrammeModal");
            if (modal) modal.style.setProperty('display', 'none', 'important');
        }
        window.closeAddProgrammeModal = closeAddProgrammeModal;

        function openEditProgrammeModal(btnElement) {
            const rawProg = btnElement.getAttribute('data-prog');
            if (!rawProg) return;
            try {
                const prog = JSON.parse(rawProg);
                const modal = document.getElementById('editProgrammeModal');
                const form = document.getElementById('editProgrammeForm');
                if (modal && form && prog) {
                    document.body.appendChild(modal);
                    modal.style.setProperty('z-index', '999999', 'important');
                    form.action = `/admin/projects/{{ $projectRouteSlug }}/{{ $project->id }}/update-programme/${prog.id}`;

                    const selectElem = document.getElementById('edit_prog_name_select');
                    const knownOptions = ['Cluster Camp', 'Report Collection Programme'];
                    const progName = prog.programme_name || '';

                    if (knownOptions.includes(progName)) {
                        if (selectElem) selectElem.value = progName;
                        toggleSpecifyProgrammeField(selectElem, 'edit_prog_other_name_wrapper', 'edit_prog_other_name_input');
                    } else {
                        if (selectElem) selectElem.value = 'Others';
                        toggleSpecifyProgrammeField(selectElem, 'edit_prog_other_name_wrapper', 'edit_prog_other_name_input');
                        const input = document.getElementById('edit_prog_other_name_input');
                        if (input) input.value = progName;
                    }

                    document.getElementById('edit_prog_date').value = prog.date || '';
                    document.getElementById('edit_prog_place').value = prog.place || '';
                    if (document.getElementById('edit_prog_remarks')) {
                        document.getElementById('edit_prog_remarks').value = prog.remarks || '';
                    }

                    // Handle checkbox values
                    const fields = ['present', 'photo', 'marklist', 'thanks_letter', 'report_form', 'other_document'];
                    fields.forEach(f => {
                        const checkbox = document.getElementById(`edit_prog_${f}_ticked`);
                        if (checkbox) {
                            checkbox.checked = !!(prog[f + '_ticked']);
                        }
                    });

                    modal.style.setProperty('display', 'flex', 'important');
                }
            } catch (e) {
                console.error('Error opening edit programme modal:', e);
            }
        }

        function closeEditProgrammeModal() {
            const modal = document.getElementById('editProgrammeModal');
            if (modal) modal.style.display = 'none';
        }

        async function handleAddProgrammeSubmit(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            const form = e.target || document.getElementById('addProgrammeForm');
            if (!form) return false;

            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Adding...';
            }

            try {
                const formData = new FormData(form);
                if (formData.get('programme_name') === 'Others' && formData.get('other_programme_name')) {
                    formData.set('programme_name', formData.get('other_programme_name').trim());
                }
                const actionUrl = form.action || `/admin/projects/{{ $projectRouteSlug }}/{{ $project->id }}/add-programme`;
                const response = await fetch(actionUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const data = await response.json();
                if (response.ok && data.success) {
                    closeAddProgrammeModal();
                    form.reset();
                    if (typeof showToast === 'function') {
                        showToast(data.message || 'Programme added successfully!', 'success');
                    }
                    window.location.reload();
                } else {
                    alert(data.error || 'Failed to add programme.');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Add Programme';
                    }
                }
            } catch (err) {
                console.error(err);
                alert('An error occurred while submitting.');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Add Programme';
                }
            }
            return false;
        }

        async function handleEditProgrammeSubmit(e) {
            e.preventDefault();
            const form = e.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Saving...';
            }

            try {
                const formData = new FormData(form);
                if (formData.get('programme_name') === 'Others' && formData.get('other_programme_name')) {
                    formData.set('programme_name', formData.get('other_programme_name').trim());
                }
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const data = await response.json();
                if (response.ok && data.success) {
                    closeEditProgrammeModal();
                    if (typeof showToast === 'function') {
                        showToast(data.message || 'Programme updated successfully!', 'success');
                    }
                    window.location.reload();
                } else {
                    alert(data.error || 'Failed to update programme.');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Save Changes';
                    }
                }
            } catch (err) {
                console.error(err);
                alert('An error occurred while saving changes.');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Save Changes';
                }
            }
        }

        // Laravel Reverb / Echo Realtime Broadcast Listener
        if (typeof window.Echo !== 'undefined') {
            window.Echo.channel('project.{{ $project->id }}')
                .listen('.programme.updated', (e) => {
                    if (typeof showToast === 'function') {
                        showToast('Realtime update received', 'info');
                    }
                    window.location.reload();
                });
        }

        async function handleDeleteProgramme(btnElement, progId, deleteUrl) {
    window.handleDeleteProgramme = handleDeleteProgramme;
            if (!confirm('Are you sure you want to delete this programme? This action cannot be undone.')) {
                return;
            }

            const row = btnElement.closest('tr');
            if (row) {
                row.style.opacity = '0.5';
                row.style.pointerEvents = 'none';
            }

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
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
                            updateProgrammeTableSerialNumbers();
                        }, 300);
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

        function updateProgrammeTableSerialNumbers() {
            const tbody = document.getElementById('social-aid-programmes-tbody');
            if (!tbody) return;
            const rows = tbody.querySelectorAll('tr.programme-table-row');
            if (rows.length === 0) {
                tbody.innerHTML = `
                    <tr id="no-programmes-row">
                        <td colspan="6" style="padding: 2.5rem 1rem; text-align: center; color: var(--text-muted); font-style: italic;">
                            No programme records found. Click "Add Programme" to add one.
                        </td>
                    </tr>
                `;
                return;
            }
            rows.forEach((r, idx) => {
                const serialCell = r.querySelector('.serial-no-cell');
                if (serialCell) {
                    serialCell.innerText = idx + 1;
                }
            });
        }



        async function toggleProgrammeChecklistTick(btnElement, progIndex, field) {
            window.toggleProgrammeChecklistTick = toggleProgrammeChecklistTick;
            const icon = btnElement.querySelector('i');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                // Instantly scale/rotate slightly for feedback
                btnElement.style.transform = 'scale(0.9)';
                setTimeout(() => btnElement.style.transform = 'scale(1)', 150);

                const response = await fetch(`/admin/projects/{{ $projectRouteSlug }}/{{ $project->id }}/toggle-programme-tick`, {

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
    </script>
@endif

@endsection
