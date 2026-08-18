@extends('layouts.admin')

@section('title', 'Project Details')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/social_aid_project_details.css') }}">
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

    <!-- Section Navigation Tabs -->
    <div class="stages-tabs" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--panel-border); margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; padding-bottom: 0.5rem;">
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            @php
                $isSocialAidProject = in_array($project->type_of_project, ['Orphan Care', 'Differently Abled', 'Family Aid']);
                $maxStages = 3;
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

                $secondTabLabel = $project->type_of_project === 'Orphan Care' ? 'Scholarship' : ($project->type_of_project === 'Differently Abled' ? 'Aid & Programmes' : 'Aid & Programmes');
                $secondTabIcon = $project->type_of_project === 'Orphan Care' ? 'bx-book-reader' : 'bx-donate-heart';

                $socialAidStageLabels = [
                    1 => ['title' => 'Profile', 'icon' => 'bx-user-pin'],
                    2 => ['title' => $secondTabLabel, 'icon' => $secondTabIcon],
                    3 => ['title' => 'Reports & Documents', 'icon' => 'bx-file'],
                ];
            @endphp
            @for($i = 1; $i <= $maxStages; $i++)
                @php
                    $isActive = ($i == 1);
                    $class = $isActive ? 'active' : '';
                    $tabInfo = $socialAidStageLabels[$i] ?? ['title' => "Section {$i}", 'icon' => 'bx-folder'];
                @endphp
                <div class="stage-tab {{ $class }}" id="tab-{{ $i }}" onclick="switchStage({{ $i }})" style="cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem;">
                    <i class="bx {{ $tabInfo['icon'] }}"></i>
                    <span>{{ $tabInfo['title'] }}</span>
                </div>
            @endfor
        </div>
        <div>
            <a href="{{ route('projects.category', $projectRouteSlug) }}" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.45rem 0.9rem; font-size: 0.85rem; border-radius: 6px; text-decoration: none; transition: all 0.2s;">
                <i class="bx bx-arrow-back"></i> Back to Project List
            </a>
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
        $isLockedForEditing = ($project->status === 'Completed' || $project->status === 'Approved') && !$isSuperAdmin;
        $canEditStatus = ($isCoo || $isHod || $isSuperAdmin) && !$isLockedForEditing;
        $isSixStage = in_array($project->type_of_project, ['Education Center', 'Cultural Center', 'Hospital or Clinics', 'Shops and Others', 'House', 'Drinking Water - Group Level', 'Drinking Water - Individual Level', 'General']);
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
        <div class="alert alert-success" style="background-color: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent-green); color: #8cf5c6; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500;">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger" style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid var(--accent-red); color: #ff8a8a; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500;">
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
                            <div style="display: flex; gap: 0.5rem; align-items: center;">
                                <button type="button" id="edit-address-btn" onclick="toggleAddressEdit()" class="btn-custom" style="padding: 0.4rem 0.85rem; font-size: 0.8rem; border-radius: 6px;">
                                    <i class="bx bx-edit"></i> Edit Details
                                </button>
                            </div>
                        </div>

                        <!-- Display Details View (Grid with Details on Left & Photo Card on Right) -->
                        <div id="address-display-view">
                            <div style="display: grid; grid-template-columns: 1fr 240px; gap: 1.5rem; align-items: start;">
                                <!-- Left Column: Details Key-Value List -->
                                <div class="details-grid">
                                    <div class="details-label">Applicant Name</div><div class="details-colon">:</div><div class="details-value" style="font-weight: 700; color: #ffffff;">{{ $application->applicant_name ?? 'N/A' }}</div>
                                    <div class="details-label">Gender</div><div class="details-colon">:</div><div class="details-value">{{ $application->gender ?? 'N/A' }}</div>
                                    <div class="details-label">Date of Birth</div><div class="details-colon">:</div><div class="details-value">{{ !empty($application->dob) ? date('d/m/Y', strtotime($application->dob)) : 'N/A' }} @if(!empty($application->age))(Age: {{ $application->age }})@endif</div>
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
                                        <div class="details-value">Date: {{ !empty($application->father_death_date) ? date('d/m/Y', strtotime($application->father_death_date)) : 'N/A' }} | Cause: {{ $application->father_death_cause ?? 'N/A' }}</div>
                                    @endif
                                    @if(!empty($application->mother_alive_status))
                                        <div class="details-label">Mother Status</div><div class="details-colon">:</div>
                                        <div class="details-value">Alive: {{ $application->mother_alive_status }} @if(!empty($application->mother_remarried_status))| Remarried: {{ $application->mother_remarried_status }}@endif @if(!empty($application->mother_death_date))| Death Date: {{ date('d/m/Y', strtotime($application->mother_death_date)) }}@endif</div>
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
                                        <div class="details-label">Monthly Finance</div><div class="details-colon">:</div><div class="details-value">Income: {!! "&#x20B9;" !!}{{ $application->monthly_income ?? '0' }} / Expense: {!! "&#x20B9;" !!}{{ $application->monthly_expense ?? '0' }}</div>
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
                                    @if($studentPhotoUrl && Auth::check() && (Auth::user()->isSuperAdmin() || Auth::user()->isCoo() || Auth::user()->isHod() || Auth::user()->isSocialAid() || Auth::user()->hasAdminAccess()))
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
                                        <select name="mother_remarried_status" class="form-select-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                            <option value="No" {{ ($application->mother_remarried_status ?? 'No') === 'No' ? 'selected' : '' }}>No</option>
                                            <option value="Yes" {{ ($application->mother_remarried_status ?? '') === 'Yes' ? 'selected' : '' }}>Yes</option>
                                        </select>
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
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Monthly Income (&#x20B9;)</label>
                                        <input type="text" name="monthly_income" value="{{ $application->monthly_income }}" class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.5rem; border-radius: 6px; outline: none;">
                                    </div>
                                    <div class="form-group-custom" style="margin-bottom: 0 !important;">
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Monthly Expense (&#x20B9;)</label>
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
                        $sponsoredDate = !empty($spDateRaw) ? date('d/m/Y', strtotime($spDateRaw)) : 'N/A';

                        $projectLocation = $project->location ?? ($project->place ?? ($application->location ?? ($application->place ?? ($application->locality_place ?? ($application->meta['location'] ?? 'N/A')))));
                        $remarks = $project->remarks ?? $project->additional_note ?? 'N/A';
                    @endphp

                    <div style="background-color: var(--panel-bg); border: 1px solid var(--panel-border); border-radius: 12px; padding: 1.5rem; margin-top: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                        <h4 style="color: var(--accent-cyan); font-size: 0.95rem; font-weight: 700; text-transform: uppercase; margin-top: 0; margin-bottom: 1.25rem; letter-spacing: 0.05em; border-bottom: 1px solid var(--panel-border); padding-bottom: 0.75rem;">
                            <i class="bx bx-id-card" style="font-size: 1.1rem; vertical-align: middle; margin-right: 0.35rem;"></i> Project &amp; Agency Details
                        </h4>
                        <div class="details-grid">
                            <div class="details-label">RCFI ID</div><div class="details-colon">:</div><div class="details-value" style="color: var(--accent-cyan); font-weight: 700;">{{ $project->project_id ?? 'N/A' }}</div>
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
                                <div style="color: var(--text-muted); font-weight: 500;">Date of Birth</div><div>:</div><div>{{ !empty($application->dob) ? date('d/m/Y', strtotime($application->dob)) : 'N/A' }} @if(!empty($application->age))(Age: {{ $application->age }})@endif</div>
                                @if(!empty($application->aadhar_number))
                                    <div style="color: var(--text-muted); font-weight: 500;">Aadhar Number</div><div>:</div><div>{{ $application->aadhar_number }}</div>
                                @endif
                                @if(!empty($application->health_status))
                                    <div style="color: var(--text-muted); font-weight: 500;">Health Status</div><div>:</div><div>{{ $application->health_status }}</div>
                                @endif
                                <div style="color: var(--text-muted); font-weight: 500;">Father's Name</div><div>:</div><div>{{ $application->father_name ?? 'N/A' }} @if(!empty($application->father_death_date) || !empty($application->father_death_cause))(Deceased: {{ !empty($application->father_death_date) ? date('d/m/Y', strtotime($application->father_death_date)) : '' }} {{ !empty($application->father_death_cause) ? '- ' . $application->father_death_cause : '' }})@endif</div>
                                <div style="color: var(--text-muted); font-weight: 500;">Mother's Name</div><div>:</div><div>{{ $application->mother_name ?? 'N/A' }} @if(!empty($application->mother_alive_status))({{ $application->mother_alive_status }})@endif @if(!empty($application->mother_death_date) || !empty($application->mother_death_cause))(Deceased: {{ !empty($application->mother_death_date) ? date('d/m/Y', strtotime($application->mother_death_date)) : '' }} {{ !empty($application->mother_death_cause) ? '- ' . $application->mother_death_cause : '' }})@endif</div>
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
                                <div style="color: var(--text-muted); font-weight: 500;">Village</div><div>:</div><div>{{ $application->village ?? 'N/A' }}</div>
                                <div style="color: var(--text-muted); font-weight: 500;">Panchayat</div><div>:</div><div>{{ $application->panchayat ?? 'N/A' }}</div>
                                <div style="color: var(--text-muted); font-weight: 500;">District</div><div>:</div><div>{{ $application->district ?? 'N/A' }}</div>
                                <div style="color: var(--text-muted); font-weight: 500;">State</div><div>:</div><div>{{ $application->state ?? 'N/A' }}</div>
                                <div style="color: var(--text-muted); font-weight: 500;">Mobile 1</div><div>:</div><div>{{ $application->mobile_1 ?? $application->contact_number_1 ?? 'N/A' }}</div>
                                <div style="color: var(--text-muted); font-weight: 500;">Mobile 2</div><div>:</div><div>{{ $application->mobile_2 ?? $application->contact_number_2 ?? 'N/A' }}</div>
                                @if(!empty($application->school_name) || !empty($application->school_class))
                                    <div style="color: var(--text-muted); font-weight: 500;">School Education</div><div>:</div><div>{{ $application->school_name ?? 'N/A' }} @if(!empty($application->school_class))(Class: {{ $application->school_class }})@endif</div>
                                @endif
                                @if(!empty($application->madrassa_name) || !empty($application->madrassa_class))
                                    <div style="color: var(--text-muted); font-weight: 500;">Madrassa Education</div><div>:</div><div>{{ $application->madrassa_name ?? 'N/A' }} @if(!empty($application->madrassa_class))(Class: {{ $application->madrassa_class }})@endif</div>
                                @endif
                                @if(!empty($application->monthly_income) || !empty($application->monthly_expense))
                                    <div style="color: var(--text-muted); font-weight: 500;">Monthly Finance</div><div>:</div><div>Income: {!! "&#x20B9;" !!}{{ $application->monthly_income ?? '0' }} / Expense: {!! "&#x20B9;" !!}{{ $application->monthly_expense ?? '0' }}</div>
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
                            <input type="text" id="project-phase-custom" placeholder="Enter custom status..." maxlength="255"
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
                                            <td style="padding: 0.75rem 1rem;">{{ !empty($row->date) ? date('d/m/Y', strtotime($row->date)) : 'N/A' }}</td>
                                            <td class="fund-amount-cell" data-amount="{{ $row->amount }}" style="padding: 0.75rem 1rem; text-align: right; font-weight: 600; color: #10b981;">{!! "&#x20B9;" !!}{{ number_format($row->amount, 2) }}</td>
                                            <td style="padding: 0.75rem 1rem;">
                                                @if($row->donorModel)
                                                    {{ $row->donorModel->name }}
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
                                        <td id="fund-total-amount" style="padding: 0.75rem 1rem; text-align: right; color: var(--accent-cyan);">{!! "&#x20B9;" !!}{{ number_format($financials->sum('amount'), 2) }}</td>
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
                                        <label style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500; display: block; margin-bottom: 0.4rem;">Agency</label>
                                        <select name="donor" required class="form-control-dark" style="width: 100%; box-sizing: border-box; background-color: var(--bg-color); border: 1px solid var(--panel-border); color: #fff; padding: 0.6rem; border-radius: 6px; outline: none;">
                                            <option value="">Select Agency...</option>
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
                                <td class="fund-amount-cell" data-amount="${fund.amount}" style="padding: 0.75rem 1rem; text-align: right; font-weight: 600; color: #10b981;">&#x20B9;${formattedAmount}</td>
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
                            if (totalElem) totalElem.innerText = `&#x20B9;${totalAmountFormatted}`;
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

                            if (totalElem) totalElem.innerText = `&#x20B9;${totalAmountFormatted}`;
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
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Monthly Income:</td><td>{{ isset($metaData['monthly_income']) ? '&#x20B9;' . number_format($metaData['monthly_income']) : 'N/A' }}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Monthly Expense:</td><td>{{ isset($metaData['monthly_expense']) ? '&#x20B9;' . number_format($metaData['monthly_expense']) : 'N/A' }}</td></tr>
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
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">House Name:</td><td>{!! $formatVal($metaData['house_name'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Place:</td><td>{!! $formatVal($application->place ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Town:</td><td>{!! $formatVal($metaData['town'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Post Office:</td><td>{!! $formatVal($metaData['post_office'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">District:</td><td>{!! $formatVal($metaData['district'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">State:</td><td>{!! $formatVal($metaData['state'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">PIN Code:</td><td>{!! $formatVal($metaData['pin_code'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Mobile 1:</td><td>{!! $formatVal($metaData['mobile_1'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Mobile 2:</td><td>{!! $formatVal($metaData['mobile_2'] ?? null) !!}</td></tr>
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
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Monthly Income:</td><td>{{ isset($metaData['monthly_income']) ? '&#x20B9;' . number_format($metaData['monthly_income']) : 'N/A' }}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Monthly Cost:</td><td>{{ isset($metaData['monthly_cost']) ? '&#x20B9;' . number_format($metaData['monthly_cost']) : 'N/A' }}</td></tr>
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
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">House Name:</td><td>{!! $formatVal($metaData['house_name'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Place:</td><td>{!! $formatVal($application->place ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Panchayat:</td><td>{!! $formatVal($metaData['panchayat'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">District:</td><td>{!! $formatVal($metaData['district'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Pincode:</td><td>{!! $formatVal($metaData['pincode'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Mobile:</td><td>{!! $formatVal($metaData['mobile'] ?? null) !!}</td></tr>
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
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">House Name:</td><td>{!! $formatVal($metaData['house_name'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Place:</td><td>{!! $formatVal($metaData['location'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Post Office:</td><td>{!! $formatVal($metaData['post_office'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Panchayat:</td><td>{!! $formatVal($metaData['panchayat'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">District:</td><td>{!! $formatVal($metaData['district'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">PIN Code:</td><td>{!! $formatVal($metaData['pin_code'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Mobile 1:</td><td>{!! $formatVal($metaData['mobile_1'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Mobile 2:</td><td>{!! $formatVal($metaData['mobile_2'] ?? null) !!}</td></tr>
                                    </table>

                                    <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">2. Family & Income Details</h4>
                                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; color: var(--text-main);">
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">Children in Family:</td><td>Total: {!! $formatVal($metaData['children_total'] ?? null) !!} (M: {!! $formatVal($metaData['children_male'] ?? null) !!} / F: {!! $formatVal($metaData['children_female'] ?? null) !!})</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">NRI Status:</td><td>{!! $formatVal($metaData['nri_status'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Occupation:</td><td>{!! $formatVal($metaData['occupation'] ?? null) !!}</td></tr>
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Monthly Income:</td><td>{{ isset($metaData['monthly_income']) ? '&#x20B9;' . number_format($metaData['monthly_income']) : 'N/A' }}</td></tr>
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
</div>
                    @endif
                @endif
                </div>
            </div>
        </div>

        <!-- ================= STAGE 3 PANEL (FILES / PROGRAMMES) ================= -->
        <div class="stage-content-panel" id="stage-content-3">
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
                                <button type="submit" class="btn-custom" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; color: #ffffff; font-weight: 700; padding: 0.6rem 1.8rem; cursor: pointer; border-radius: 6px;">
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
                    <div style="background-color: var(--panel-bg, #ffffff); border: 1px solid var(--panel-border, #cbd5e1); border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); margin-bottom: 2rem;">
                        <style>
                            #social-aid-programmes-tbody tr:last-child td {
                                border-bottom: none !important;
                            }
                        </style>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                            <h4 style="color: #17a673; font-size: 0.95rem; font-weight: 700; text-transform: uppercase; margin: 0; letter-spacing: 0.05em;">PROGRAMME DETAILS</h4>
                            @if($isProjectManager && !$isLockedForEditing)
                                <button type="button" id="btn-add-programme-main" class="btn-add-programme-trigger" style="background-color: #17a673; color: #ffffff; border: none; border-radius: 20px; padding: 0.45rem 1.1rem; font-weight: 600; font-size: 0.85rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem; transition: background-color 0.2s ease; box-shadow: 0 2px 6px rgba(23, 166, 115, 0.2);" onmouseover="this.style.backgroundColor='#148f63'" onmouseout="this.style.backgroundColor='#17a673'">
                                    <i class="bx bx-plus" style="font-size: 1rem;"></i> Add Programme
                                </button>
                            @endif
                        </div>

                        <div class="table-responsive-custom" style="overflow-x: auto; border-radius: 8px; border: 1px solid var(--panel-border, #cbd5e1); overflow: hidden;">

                            <table class="table-dark-custom" style="width: 100%; border-collapse: collapse; text-align: left; background-color: transparent;">
                                <thead>
                                    <tr style="background-color: #17a673; color: #ffffff; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                        <th style="padding: 12px 16px; font-weight: 700; width: 75px; text-align: center; white-space: nowrap; color: #ffffff;">SERIAL NO</th>
                                        <th style="padding: 12px 16px; font-weight: 700; white-space: nowrap; color: #ffffff;">PROGRAMME NAME</th>
                                        <th style="padding: 12px 16px; font-weight: 700; white-space: nowrap; color: #ffffff;">DATE</th>
                                        <th style="padding: 12px 16px; font-weight: 700; white-space: nowrap; color: #ffffff;">PLACE</th>
                                        <th style="padding: 12px 16px; font-weight: 700; white-space: nowrap; color: #ffffff;">REMARKS</th>
                                        <th style="padding: 12px 16px; font-weight: 700; text-align: center; white-space: nowrap; color: #ffffff;">CHECKLIST &amp; DOCUMENTS</th>
                                        <th style="padding: 12px 16px; font-weight: 700; text-align: center; width: 110px; white-space: nowrap; color: #ffffff;">ACTIONS</th>
                                    </tr>
                                </thead>
                                <tbody id="social-aid-programmes-tbody">
                                    @forelse($programmes as $idx => $prog)
                                        <tr id="programme-row-{{ $prog->id }}" class="programme-table-row" style="border-bottom: 1px solid var(--panel-border, #e2e8f0); font-size: 0.875rem; transition: background-color 0.2s ease;" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.03)'" onmouseout="this.style.backgroundColor='transparent'">
                                            <td class="serial-no-cell" style="padding: 12px 16px; text-align: center; font-weight: 600; color: var(--text-muted, #64748b); vertical-align: middle;">{{ $idx + 1 }}</td>
                                            <td style="padding: 12px 16px; font-weight: 700; color: var(--text-main, #0f172a); vertical-align: middle;">{{ $prog->programme_name ?? 'Untitled Programme' }}</td>
                                            <td style="padding: 12px 16px; color: var(--text-main, #334155); white-space: nowrap; vertical-align: middle;">{{ !empty($prog->date) ? date('d/m/Y', strtotime($prog->date)) : '-' }}</td>
                                            <td style="padding: 12px 16px; color: var(--text-main, #334155); vertical-align: middle;">{{ $prog->place ?? '-' }}</td>
                                            <td style="padding: 12px 16px; color: var(--text-main, #334155); vertical-align: middle;">{{ $prog->remarks ?? '-' }}</td>
                                            <td style="padding: 12px 16px; text-align: center; vertical-align: middle;">

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
                                            <td style="padding: 12px 16px; text-align: center; white-space: nowrap; vertical-align: middle;">
                                                <!-- View Button -->
                                                <button type="button" onclick="openViewProgrammeModal(this)" data-prog="{{ e(json_encode($prog)) }}" style="background: transparent; border: none; color: #17a673; cursor: pointer; padding: 0.3rem; margin-right: 0.2rem; border-radius: 4px; transition: background 0.2s;" title="View Details">
                                                    <i class="bx bx-show" style="font-size: 1.15rem; vertical-align: middle;"></i>
                                                </button>

                                                @if($isProjectManager && !$isLockedForEditing)
                                                    <button type="button" onclick="openEditProgrammeModal(this)" data-prog="{{ e(json_encode($prog)) }}" style="background: transparent; border: none; color: #0284c7; cursor: pointer; padding: 0.3rem; margin-right: 0.2rem; border-radius: 4px; transition: background 0.2s;" title="Edit Programme">
                                                        <i class="bx bx-pencil" style="font-size: 1.15rem; vertical-align: middle;"></i>
                                                    </button>
                                                @endif

                                                @if($isSuperAdmin || $isHod || $isCoo)
                                                    <button type="button" onclick="handleDeleteProgramme(this, {{ $prog->id }}, '{{ route('projects.' . $projectRouteKey . '.delete_programme', [$project->id, $prog->id]) }}')" style="background: transparent; border: none; color: #ef4444; cursor: pointer; padding: 0.3rem; border-radius: 4px; transition: transform 0.2s;" title="Delete Programme" onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'">
                                                        <i class="bx bx-trash" style="font-size: 1.15rem; vertical-align: middle;"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="no-programmes-row">
                                            <td colspan="7" style="padding: 2.5rem 1rem; text-align: center; color: var(--text-muted, #64748b); font-style: italic;">
                                                No programme records found. Click "Add Programme" to add one.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>

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

        window.openAddProgrammeModal = function openAddProgrammeModal(btnElement) {
            let modal = document.getElementById('addProgrammeModal');
            if (!modal) {
                modal = document.querySelector('#addProgrammeModal');
            }
            if (modal) {
                if (modal.parentElement !== document.body) {
                    document.body.appendChild(modal);
                }
                modal.style.setProperty('z-index', '999999', 'important');
                modal.style.setProperty('display', 'flex', 'important');

                let pId = (btnElement && typeof btnElement.getAttribute === 'function') 
                    ? btnElement.getAttribute('data-id') 
                    : null;
                if (!pId) {
                    pId = "{{ $project->id ?? '' }}";
                }
                if (pId && document.getElementById('add_prog_project_id')) {
                    document.getElementById('add_prog_project_id').value = pId;
                }

                const selectElem = document.getElementById('add_prog_name_select');
                if (selectElem) {
                    selectElem.selectedIndex = 0;
                    toggleSpecifyProgrammeField(selectElem, 'add_prog_other_name_wrapper', 'add_prog_other_name_input');
                }
            } else {
                console.error('[openAddProgrammeModal] #addProgrammeModal not found in DOM');
                if (typeof showToast === 'function') {
                    showToast('Could not open the form. Please try again.', 'danger');
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

        function appendProgrammeRow(prog, formattedDate) {
            const tbody = document.getElementById('social-aid-programmes-tbody');
            if (!tbody) return;

            const noRow = document.getElementById('no-programmes-row');
            if (noRow) noRow.remove();

            const dateStr = formattedDate || (prog.date ? new Date(prog.date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }).replace(/ /g, '-') : '-');
            const isPm = @json($isProjectManager && !$isLockedForEditing);
            const isAdmin = @json($isSuperAdmin || $isHod || $isCoo);
            const progJson = JSON.stringify(prog).replace(/"/g, '&quot;');
            const deleteRoute = `/admin/projects/{{ $projectRouteKey }}/{{ $project->id }}/delete-programme/${prog.id}`;

            const items = [
                { key: 'present', label: 'Present' },
                { key: 'photo', label: 'Photo' },
                { key: 'marklist', label: 'Marklist' },
                { key: 'thanks_letter', label: 'Thanks Letter' },
                { key: 'report_form', label: 'Report Form' },
                { key: 'other_document', label: 'Other Doc' }
            ];

            let checklistHtml = '<div style="display: flex; gap: 0.3rem; flex-wrap: nowrap; justify-content: center; align-items: center; white-space: nowrap; margin: 0 auto;">';
            items.forEach(item => {
                const isTicked = !!prog[item.key + '_ticked'];
                const badgeStyle = isTicked 
                    ? 'background-color: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.35); color: #059669;' 
                    : 'background-color: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3); color: #d97706;';
                const iconClass = isTicked ? 'bxs-check-circle' : 'bx-circle';
                
                if (isPm) {
                    checklistHtml += `<button type="button" onclick="toggleProgrammeChecklistTick(this, ${prog.id}, '${item.key}')"
                        title="${item.label}: ${isTicked ? 'Ticked (Click to untick)' : 'Not ticked (Click to tick)'}"
                        style="display: inline-flex; align-items: center; gap: 0.2rem; padding: 0.25rem 0.5rem; border-radius: 20px; font-size: 0.73rem; font-weight: 600; cursor: pointer; transition: all 0.2s; outline: none; white-space: nowrap; flex-shrink: 0; ${badgeStyle}">
                        <i class="bx ${iconClass}" style="font-size: 0.8rem;"></i>
                        ${item.label}
                    </button>`;
                } else {
                    checklistHtml += `<span style="display: inline-flex; align-items: center; gap: 0.2rem; padding: 0.25rem 0.5rem; border-radius: 20px; font-size: 0.73rem; font-weight: 600; white-space: nowrap; flex-shrink: 0; ${badgeStyle}">
                        <i class="bx ${iconClass}" style="font-size: 0.8rem;"></i>
                        ${item.label}
                    </span>`;
                }
            });
            checklistHtml += '</div>';

            let actionsHtml = `<button type="button" onclick="openViewProgrammeModal(this)" data-prog="${progJson}" style="background: transparent; border: none; color: #10b981; cursor: pointer; padding: 0.3rem; margin-right: 0.25rem; border-radius: 4px; transition: background 0.2s;" title="View Details" onmouseover="this.style.background='rgba(16, 185, 129, 0.1)'" onmouseout="this.style.background='transparent'">
                <i class="bx bx-show" style="font-size: 1.15rem; vertical-align: middle;"></i>
            </button>`;

            if (isPm) {
                actionsHtml += `<button type="button" onclick="openEditProgrammeModal(this)" data-prog="${progJson}" style="background: transparent; border: none; color: #0284c7; cursor: pointer; padding: 0.3rem; margin-right: 0.25rem; border-radius: 4px; transition: background 0.2s;" title="Edit Programme" onmouseover="this.style.background='rgba(2, 132, 199, 0.1)'" onmouseout="this.style.background='transparent'">
                    <i class="bx bx-pencil" style="font-size: 1.15rem; vertical-align: middle;"></i>
                </button>`;
            }

            if (isAdmin) {
                actionsHtml += `<button type="button" onclick="handleDeleteProgramme(this, ${prog.id}, '${deleteRoute}')" style="background: transparent; border: none; color: #ef4444; cursor: pointer; padding: 0.3rem; border-radius: 4px; transition: background 0.2s;" title="Delete Programme" onmouseover="this.style.background='rgba(239, 68, 68, 0.1)'" onmouseout="this.style.background='transparent'">
                    <i class="bx bx-trash" style="font-size: 1.15rem; vertical-align: middle;"></i>
                </button>`;
            }

            const tr = document.createElement('tr');
            tr.id = `programme-row-${prog.id}`;
            tr.className = 'programme-table-row';
            tr.style.cssText = 'border-bottom: 1px solid var(--panel-border); font-size: 0.875rem; transition: all 0.3s ease; animation: fadeIn 0.4s ease;';
            tr.onmouseover = function() { this.style.backgroundColor = 'rgba(255,255,255,0.02)'; };
            tr.onmouseout = function() { this.style.backgroundColor = 'transparent'; };

            tr.innerHTML = `
                <td class="serial-no-cell" style="padding: 0.85rem 1rem; text-align: center; font-weight: 600; color: var(--text-muted); vertical-align: middle;">1</td>
                <td style="padding: 0.85rem 1rem; font-weight: 700; color: var(--text-main); vertical-align: middle;">${prog.programme_name || 'Untitled Programme'}</td>
                <td style="padding: 0.85rem 1rem; color: var(--text-main); white-space: nowrap; vertical-align: middle;">${dateStr}</td>
                <td style="padding: 0.85rem 1rem; color: var(--text-main); vertical-align: middle;">${prog.place || '-'}</td>
                <td style="padding: 0.85rem 1rem; color: var(--text-main); vertical-align: middle;">${prog.remarks || '-'}</td>
                <td style="padding: 0.85rem 1rem; text-align: center; vertical-align: middle;">${checklistHtml}</td>
                <td style="padding: 0.85rem 1rem; text-align: center; white-space: nowrap; vertical-align: middle;">${actionsHtml}</td>
            `;

            tbody.insertBefore(tr, tbody.firstChild);

            const rows = tbody.querySelectorAll('.programme-table-row');
            rows.forEach((row, i) => {
                const cell = row.querySelector('.serial-no-cell');
                if (cell) cell.innerText = i + 1;
            });
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
                    if (data.programme) {
                        appendProgrammeRow(data.programme, data.formatted_date);
                    }
                    if (typeof showToast === 'function') {
                        showToast(data.message || 'Programme added successfully!', 'success');
                    }
                } else {
                    alert(data.error || 'Failed to add programme.');
                }
            } catch (err) {
                console.error(err);
                alert('An error occurred while submitting.');
            } finally {
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
                        <td colspan="7" style="padding: 2.5rem 1rem; text-align: center; color: var(--text-muted, #64748b); font-style: italic;">
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

    <!-- Back to Project List Button -->
    <div style="margin-top: 2rem; margin-bottom: 1.5rem;">
        <a href="{{ route('projects.category', $projectRouteSlug) }}" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.55rem 1.25rem; border-radius: 6px; text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: all 0.2s;">
            <i class="bx bx-arrow-back"></i> Back to Project List
        </a>
    </div>

<!-- Site Study Modal -->
<div id="siteStudyModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
    <div style="background: var(--bg-card, #ffffff); border-radius: 8px; width: 90%; max-width: 850px; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 10px 25px rgba(0,0,0,0.2); overflow: hidden;">
        <div style="padding: 1rem 1.5rem; background: var(--accent-green, #10b981); color: white; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0; font-size: 1.1rem; color: white; display: flex; align-items: center; gap: 0.5rem;"><i class="bx bx-file-find"></i> Site Study Report (1000+ Words Supported)</h4>
            <button type="button" onclick="closeSiteStudyModal()" style="background: transparent; border: none; color: white; font-size: 1.5rem; cursor: pointer; line-height: 1;">&times;</button>
        </div>
        <form id="siteStudyForm" onsubmit="saveSiteStudyReport(event)" data-no-ajax style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem; overflow-y: auto; flex: 1;">
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <label style="font-weight: 600; color: var(--text-main); font-size: 0.9rem;">Comprehensive Site Study Report</label>
                    <span id="siteStudyWordCount" style="font-size: 0.8rem; color: var(--accent-green, #10b981); font-weight: 600;">Words: 0</span>
                </div>
                <textarea id="siteStudyReportText" 
                          name="report" 
                          rows="14" 
                          oninput="updateSiteStudyWordCount()"
                          placeholder="Type or paste comprehensive site study report here (supports 1000+ words)..." 
                          style="width: 100%; border: 1px solid #cccccc; border-radius: 6px; padding: 0.75rem; font-size: 0.9rem; font-family: inherit; color: #000000 !important; background-color: #ffffff !important; outline: none; line-height: 1.5; resize: vertical;">{{ $siteStudyData->report ?? '' }}</textarea>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.5rem; border-top: 1px solid #eeeeee; padding-top: 1rem;">
                <button type="button" onclick="closeSiteStudyModal()" style="background: #e5e7eb; color: #374151; border: none; padding: 0.5rem 1.25rem; border-radius: 6px; font-weight: 500; cursor: pointer;">Cancel</button>
                <button type="submit" id="siteStudySubmitBtn" style="background: var(--accent-green, #10b981); color: white; border: none; padding: 0.5rem 1.5rem; border-radius: 6px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem;">
                    <i class="bx bx-save"></i> Save Site Study Report
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    window.openSiteStudyModal = function() {
        const modal = document.getElementById('siteStudyModal');
        if (modal) {
            modal.style.display = 'flex';
            if (typeof window.updateSiteStudyWordCount === 'function') {
                window.updateSiteStudyWordCount();
            }
        }
    };
    window.closeSiteStudyModal = function() {
        const modal = document.getElementById('siteStudyModal');
        if (modal) {
            modal.style.display = 'none';
        }
    };
    window.updateSiteStudyWordCount = function() {
        const text = document.getElementById('siteStudyReportText')?.value || '';
        const words = text.trim() ? text.trim().split(/\s+/).length : 0;
        const countEl = document.getElementById('siteStudyWordCount');
        if (countEl) {
            countEl.innerText = `Words: ${words}`;
        }
    };
    window.saveSiteStudyReport = async function(e) {
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
                window.closeSiteStudyModal();
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
    };
</script>

@endsection
