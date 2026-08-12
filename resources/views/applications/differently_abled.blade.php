@extends('layouts.admin')

@section('title', 'Differently Abled Applications')

@section('content')

    <!-- Back Button Header -->
    <div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
        <a href="{{ route('applications.index') }}" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); padding: 0.5rem 1rem;">
            <i class="bx bx-left-arrow-alt"></i> Back to Dashboard
        </a>
            </div>

    <!-- Success & Error Alert Panels -->
    @if (session('success'))
        <div class=\"alert alert-success\" style="background-color: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent-green); color: #047857; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500;">
            {{ session('success') }}
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

    <style>
        /* Progressively hide columns from right-to-left as screen size decreases, keeping Application ID, Applicant Name and Actions always visible */
        @media (max-width: 1800px) {
            .col-contact2 { display: none !important; }
        }
        @media (max-width: 1700px) {
            .col-contact1 { display: none !important; }
        }
        @media (max-width: 1600px) {
            .col-state { display: none !important; }
        }
        @media (max-width: 1500px) {
            .col-district { display: none !important; }
        }
        @media (max-width: 1400px) {
            .col-panchayath { display: none !important; }
        }
        @media (max-width: 1300px) {
            .col-post { display: none !important; }
        }
        @media (max-width: 1200px) {
            .col-village { display: none !important; }
        }
        @media (max-width: 1100px) {
            .col-location { display: none !important; }
        }
        @media (max-width: 1000px) {
            .col-year { display: none !important; }
        }
        @media (max-width: 900px) {
            .col-reg { display: none !important; }
        }
        @media (max-width: 800px) {
            .col-committee { display: none !important; }
        }
    </style>

    <div class="panel" style="width: 100%;">
        <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2 class="panel-title">Differently Abled Applications List</h2>
            <div style="display: flex; gap: 0.75rem;">
                @if(auth()->user() && auth()->user()->canDownloadExcel())
                <a href="{{ route('applications.export', $categorySlug) }}" class="btn-custom" style="background: linear-gradient(135deg, #2ecc71, #27ae60); text-decoration: none;">
                    <i class="bx bx-download"></i> Download Excel
                </a>
                @endif
                @if(auth()->user() && auth()->user()->canAddApplications())
                <button onclick="openModal()" class="btn-custom">
                    <i class="bx bx-plus-circle"></i> Add Application
                </button>
                @endif
            </div>
        </div>
        
        <!-- Search Toolbar -->
        <div style="margin-bottom: 1.25rem; display: flex; justify-content: flex-end;">
            <div style="position: relative; width: 100%; max-width: 320px;">
                <span style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 1.1rem;"><i class="bx bx-search"></i></span>
                <input type="text" id="tableSearchInput" placeholder="Search applications..." style="width: 100%; padding: 0.5rem 1rem 0.5rem 2.25rem; background-color: #111c2d; border: 1px solid #2a3547; border-radius: 6px; color: #ffffff; font-size: 0.875rem; outline: none; transition: border-color 0.2s;" onkeyup="filterTable()">
            </div>
        </div>

        <div style="overflow-x: auto;">
                        <table class="table-custom">
                <thead>
                    <!-- Column header row -->
                    <tr>
                        <th>Application ID</th>
                        <th>Name</th>
                        <th>Father Name</th>
                        <th>Gender</th>
                        <th>Age</th>
                        <th>Disability</th>
                        <th>Place</th>
                        <th>District</th>
                        <th>Project Type</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $appItem)
                        @php
                            $meta = $appItem->meta ?? [];
                            $appYear = !empty($appItem->created_at) ? date('y', strtotime($appItem->created_at)) : '24';
                            $appId = 'APLRCFI' . $appYear . 'DA' . str_pad($appItem->id, 5, '0', STR_PAD_LEFT);
                            
                            $searchTerms = [
                                $appId,
                                $appItem->applicant_name ?? '',
                                $appItem->place ?? '',
                                $appItem->district ?? $meta['district'] ?? '',
                                $meta['project_type'] ?? $meta['disability_type'] ?? $appItem->project_type ?? '',
                                $appItem->village ?? $appItem->town ?? '',
                                $appItem->panchayat ?? $appItem->panchayath ?? '',
                                $appItem->status ?? '',
                                $appItem->rejected_reason ?? '',
                                $appItem->details ?? '',
                            ];
                            if (is_array($meta)) {
                                foreach ($meta as $val) {
                                    if (is_scalar($val)) {
                                        $searchTerms[] = (string)$val;
                                    }
                                }
                            }
                            $searchStr = strtolower(implode(' ', array_filter($searchTerms)));
                        @endphp
                        <tr class="app-row" data-search="{{ $searchStr }}">
                            <!-- Application ID -->
                            <td style="font-weight: 600; color: var(--accent-cyan);">
                                {{ $appId }}
                            </td>

                            <!-- Name -->
                            <td style="font-weight: 600; color: #ffffff;">{{ $appItem->applicant_name }}</td>

                            <!-- Father Name -->
                            <td>{{ $meta['father_name'] ?? 'N/A' }}</td>

                            <!-- Gender -->
                            <td>{{ $meta['gender'] ?? 'N/A' }}</td>

                            <!-- Age -->
                            <td>{{ $meta['age'] ?? 'N/A' }}</td>

                            <!-- Disability -->
                            <td>{{ $meta['disability_type'] ?? 'N/A' }}</td>

                            <!-- Place -->
                            <td>{{ $appItem->place ?? 'N/A' }}</td>

                            <!-- District -->
                            <td>{{ $appItem->district ?? $meta['district'] ?? $meta['locality_district'] ?? 'N/A' }}</td>

                            <!-- Project Type -->
                            <td>{{ !empty($meta['project_type']) ? ucwords($meta['project_type']) : (!empty($meta['disability_type']) ? ucwords($meta['disability_type']) : (!empty($appItem->project_type) ? ucwords($appItem->project_type) : 'N/A')) }}</td>

                            <!-- Status -->
                            <td style="text-align: center;">
                                @php
                                    $statusColors = [
                                        'Pending' => ['bg' => 'rgba(245, 158, 11, 0.2)', 'text' => '#f59e0b'],
                                        'Approved' => ['bg' => 'rgba(16, 185, 129, 0.2)', 'text' => 'var(--accent-green)'],
                                        'Rejected' => ['bg' => 'rgba(239, 68, 68, 0.2)', 'text' => 'var(--accent-red)'],
                                    ];
                                    $color = $statusColors[$appItem->status] ?? ['bg' => 'rgba(156, 163, 175, 0.2)', 'text' => 'var(--text-muted)'];
                                @endphp
                                <span style="background-color: {{ $color['bg'] }}; color: {{ $color['text'] }}; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">
                                    {{ $appItem->status }}
                                </span>
                            </td>

                            <td style="text-align: center; white-space: nowrap;">
                                <button onclick="openDetailsModal({{ e(json_encode($appItem)) }})" class="btn-custom" style="background: transparent; color: var(--accent-green); border: 1px solid var(--accent-green); padding: 0.4rem; font-size: 1rem; border-radius: 6px; cursor: pointer; transition: all 0.2s; margin-right: 0.5rem; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px;" title="Details"><i class="bx bx-show"></i></button>

                                @if($appItem->status !== 'Approved' && Auth::user()->canApproveApplications())
                                    @if($appItem->status === 'Pending' && !isset($projectsMap[$appItem->id]))
                                        <!-- Approve -->
                                        <button type="button" onclick="openApproveModal({{ $appItem->id }}, '{{ $appItem->cluster_id }}', '{{ $appItem->agency_number }}', '{{ addslashes($meta['agency_name'] ?? '') }}', '{{ $meta['application_date'] ?? date('Y-m-d') }}')" class="btn-custom" style="background: transparent; color: var(--accent-green); border: 1px solid var(--accent-green); padding: 0.4rem; font-size: 1rem; border-radius: 6px; cursor: pointer; transition: all 0.2s; margin-right: 0.5rem; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px;" title="Approve">
                                            <i class="bx bx-check"></i>
                                        </button>

                                        <!-- Reject -->
                                        @if(($appItem->sponsor_status ?? 'Not Sponsored') !== 'Sponsored')
                                        <form action="{{ route('applications.reject', [$categorySlug, $appItem->id]) }}" method="POST" style="display: inline-block;" onsubmit="confirmApplicationRejection(event, this); return false;">
                                            @csrf
                                            <button type="submit" class="btn-danger-custom" style="padding: 0.4rem; font-size: 1rem; margin-right: 0.5rem; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px;" title="Reject">
                                                <i class="bx bx-x"></i>
                                            </button>
                                        </form>
                                        @endif

                                    @endif
                                @endif


                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 2rem;">No differently abled applications registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- View Full Details Modal Dialog -->
    <div id="detailsAppModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.75); display: none; align-items: center; justify-content: center; z-index: 1100; overflow-y: auto;" onclick="closeDetailsModal()">
        <div class="panel" style="width: 100%; max-width: 850px; margin: 2rem auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #2a3547; max-height: 90vh; overflow-y: auto;" onclick="event.stopPropagation()">
            
            <button onclick="closeDetailsModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; z-index: 10;"><i class="bx bx-x"></i></button>
            
            <div class="panel-header" style="margin-bottom: 1.5rem;">
                <h2 class="panel-title" style="font-size: 1.25rem;"><i class="bx bx-detail" style="vertical-align: middle; margin-right: 0.5rem; color: var(--accent-green);"></i> Application Details</h2>
            </div>

            <!-- Details content dynamically populated by JS -->
            <div id="details_content" style="color: var(--text-main); font-size: 0.9rem;">
                <!-- Tables populated by script -->
            </div>
            
                                    <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--panel-border); padding-top: 1.5rem; flex-wrap: wrap;">
                @if(Auth::user()->canApproveApplications())
                    <span id="modal_status_actions" style="display: inline-flex; gap: 0.75rem;"></span>
                @endif
                @if(Auth::user()->hasAdminAccess())
                    <button id="modal_edit_btn" onclick="editFromDetails()" class="btn-custom" style="background: transparent; color: var(--accent-cyan); border: 1px solid var(--accent-cyan); padding: 0.6rem 1.5rem;">
                        <i class="bx bx-pencil"></i> Edit
                    </button>
                @endif
                @if(Auth::user()->canDeleteApplications())
                    <button id="modal_delete_btn" onclick="deleteFromDetails()" class="btn-danger-custom" style="padding: 0.6rem 1.5rem;">
                        <i class="bx bx-trash"></i> Delete
                    </button>
                @endif
                <button onclick="closeDetailsModal()" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); padding: 0.6rem 1.5rem;">Close Details</button>
            </div>
        </div>
    </div>

    <!-- Add Application Modal Dialog -->
    <div id="addAppModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.75); display: none; align-items: center; justify-content: center; z-index: 1000; overflow-y: auto;" onclick="closeModal()">
        <div class="panel" style="width: 100%; max-width: 750px; margin: 2rem auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #2a3547; max-height: 90vh; overflow-y: auto;" onclick="event.stopPropagation()">
            
            <button onclick="closeModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; z-index: 10;"><i class="bx bx-x"></i></button>
            
            <div class="panel-header" style="margin-bottom: 1.5rem;">
                <h2 class="panel-title" style="font-size: 1.25rem;">Add Differently Abled Application</h2>
            </div>

            <form action="{{ route('applications.store') }}" method="POST">
                @csrf
                
                <!-- Hidden Category and redirect tags -->
                <input type="hidden" name="category" value="{{ $categoryName }}">
                <input type="hidden" name="redirect_category" value="{{ $categorySlug }}">
                <input type="hidden" name="amount_requested" value="0">

                <!-- Form Section 1: Personal Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">1. Personal Details of Applicant</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="applicant_name">Applicant Name *</label>
                            <input type="text" class="form-control-dark" id="applicant_name" name="applicant_name" value="{{ old('applicant_name') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="father_name">Father's Name *</label>
                            <input type="text" class="form-control-dark" id="father_name" name="meta[father_name]" value="{{ old('meta.father_name') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="fathers_father">Father's Father</label>
                            <input type="text" class="form-control-dark" id="fathers_father" name="meta[fathers_father]" value="{{ old('meta.fathers_father') }}">
                        </div>
                        <div>
                            <label class="form-label" for="mother_name">Mother's Name *</label>
                            <input type="text" class="form-control-dark" id="mother_name" name="meta[mother_name]" value="{{ old('meta.mother_name') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="gender">Gender *</label>
                            <select class="form-select-dark" id="gender" name="meta[gender]" required>
                                <option value="Male" {{ old('meta.gender') === 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('meta.gender') === 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="aadhar_number">Aadhaar Number *</label>
                            <input type="text" class="form-control-dark" id="aadhar_number" name="meta[aadhar_number]" value="{{ old('meta.aadhar_number') }}" placeholder="XXXX XXXX XXXX" maxlength="14" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1.5fr 1fr 1.5fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="dob">Date of Birth *</label>
                            <input type="date" class="form-control-dark" id="dob" name="meta[dob]" value="{{ old('meta.dob') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="age">Age *</label>
                            <input type="number" class="form-control-dark" id="age" name="meta[age]" value="{{ old('meta.age') }}" readonly style="background-color: rgba(255, 255, 255, 0.05); cursor: not-allowed;">
                        </div>
                        <div>
                            <label class="form-label" for="marital_status">Marital Status *</label>
                            <select class="form-select-dark" id="marital_status" name="meta[marital_status]" required>
                                <option value="Single" {{ old('meta.marital_status') === 'Single' ? 'selected' : '' }}>Single</option>
                                <option value="Married" {{ old('meta.marital_status') === 'Married' ? 'selected' : '' }}>Married</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="guardian_name">Name of Guardian *</label>
                            <input type="text" class="form-control-dark" id="guardian_name" name="meta[guardian_name]" value="{{ old('meta.guardian_name') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="guardian_relation">Relationship *</label>
                            <input type="text" class="form-control-dark" id="guardian_relation" name="meta[guardian_relation]" value="{{ old('meta.guardian_relation') }}" required>
                        </div>
                    </div>
                </div>

                <!-- Form Section 2: Family & Economic Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">2. Family & Economic Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1.5fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="male_members">Male Members *</label>
                            <input type="number" class="form-control-dark" id="male_members" name="meta[male_members]" value="{{ old('meta.male_members') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="female_members">Female Members *</label>
                            <input type="number" class="form-control-dark" id="female_members" name="meta[female_members]" value="{{ old('meta.female_members') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="total_members">Total Members *</label>
                            <input type="number" class="form-control-dark" id="total_members" name="meta[total_members]" value="{{ old('meta.total_members') }}" readonly>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="people_with_disabilities">People with Disabilities *</label>
                            <input type="number" class="form-control-dark" id="people_with_disabilities" name="meta[people_with_disabilities]" value="{{ old('meta.people_with_disabilities') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="monthly_income">Monthly Income (₹) *</label>
                            <input type="number" class="form-control-dark" id="monthly_income" name="meta[monthly_income]" value="{{ old('meta.monthly_income') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="monthly_cost">Monthly Cost (₹) *</label>
                            <input type="number" class="form-control-dark" id="monthly_cost" name="meta[monthly_cost]" value="{{ old('meta.monthly_cost') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="income_source">Source of Income *</label>
                            <input type="text" class="form-control-dark" id="income_source" name="meta[income_source]" value="{{ old('meta.income_source') }}" required>
                        </div>
                    </div>
                </div>

                <!-- Form Section 3: Education & Disability Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">3. Education & Disability Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="studying_institution">Name of Studying Institution</label>
                            <input type="text" class="form-control-dark" id="studying_institution" name="meta[studying_institution]" value="{{ old('meta.studying_institution') }}">
                        </div>
                        <div>
                            <label class="form-label" for="not_studying_reason">If you don't study, reason</label>
                            <input type="text" class="form-control-dark" id="not_studying_reason" name="meta[not_studying_reason]" value="{{ old('meta.not_studying_reason') }}">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="health_status">Health Status *</label>
                            <input type="text" class="form-control-dark" id="health_status" name="meta[health_status]" value="{{ old('meta.health_status') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="disability_type">Disability *</label>
                            <select class="form-select-dark" id="disability_type" name="meta[disability_type]" required>
                                <option value="Mute" {{ old('meta.disability_type') === 'Mute' ? 'selected' : '' }}>Mute</option>
                                <option value="Deafness" {{ old('meta.disability_type') === 'Deafness' ? 'selected' : '' }}>Deafness</option>
                                <option value="Blindness" {{ old('meta.disability_type') === 'Blindness' ? 'selected' : '' }}>Blindness</option>
                                <option value="Others" {{ old('meta.disability_type') === 'Others' ? 'selected' : '' }}>Others</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="disability_percentage">Percentage of Disability (%) *</label>
                            <input type="number" class="form-control-dark" id="disability_percentage" name="meta[disability_percentage]" min="0" max="100" value="{{ old('meta.disability_percentage') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="disability_date">Date/Year of Disability *</label>
                            <input type="date" class="form-control-dark" id="disability_date" name="meta[disability_date]" value="{{ old('meta.disability_date') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="disability_level">Level of Disability *</label>
                            <select class="form-select-dark" id="disability_level" name="meta[disability_level]" required>
                                <option value="Simple" {{ old('meta.disability_level') === 'Simple' ? 'selected' : '' }}>Simple</option>
                                <option value="Hard" {{ old('meta.disability_level') === 'Hard' ? 'selected' : '' }}>Hard</option>
                                <option value="Very Hard" {{ old('meta.disability_level') === 'Very Hard' ? 'selected' : '' }}>Very Hard</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="other_help">Anyone else help?</label>
                            <input type="text" class="form-control-dark" id="other_help" name="meta[other_help]" placeholder="e.g. NGO, Govt or None" value="{{ old('meta.other_help') }}">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; align-items: center;">
                        <div>
                            <label class="form-label" for="description">Description if any</label>
                            <textarea class="form-control-dark" id="description" name="meta[description]" style="height: 50px;">{{ old('meta.description') }}</textarea>
                        </div>
                        <div>
                            <label class="form-label" for="accommodation">Accommodation Details *</label>
                            <select class="form-select-dark" id="accommodation" name="meta[accommodation]" required>
                                <option value="Own House" {{ old('meta.accommodation') === 'Own House' ? 'selected' : '' }}>Own House</option>
                                <option value="Ancestral Home" {{ old('meta.accommodation') === 'Ancestral Home' ? 'selected' : '' }}>Ancestral Home</option>
                                <option value="Rental House" {{ old('meta.accommodation') === 'Rental House' ? 'selected' : '' }}>Rental House</option>
                                <option value="Other" {{ old('meta.accommodation') === 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Form Section 4: Address & Contact Details -->
                <div style="margin-bottom: 2rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">4. Address & Contact Details</h4>
                    
                    @include('applications.address_form_fields', ['idPrefix' => '', 'app' => null])

                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="details">Additional Notes</label>
                        <textarea class="form-control-dark" id="details" name="details" style="height: 60px; resize: vertical;">{{ old('details') }}</textarea>
                    </div>

                    <input type="hidden" name="status" value="Pending">
                </div>

                <!-- Form Section 5: Cluster & Agency Details -->
                <div style="margin-bottom: 2rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">5. Cluster & Agency Details (Optional)</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="cluster_id">Cluster</label>
                            <select class="form-select-dark" id="cluster_id" name="cluster_id" style="width: 100%; padding: 0.6rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: #111c2d; color: #ffffff;">
                                <option value="">-- Select Cluster --</option>
                                @foreach($clusters as $cl)
                                    <option value="{{ $cl->id }}" {{ old('cluster_id') == $cl->id ? 'selected' : '' }}>{{ $cl->name }} ({{ $cl->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="agency_number">Agency Number</label>
                            <input type="text" class="form-control-dark" id="agency_number" name="agency_number" value="{{ old('agency_number') }}">
                        </div>
                    </div>
                </div>


                <!-- Recommendation Details Section -->
                <div style="border-top: 1px solid var(--panel-border); padding-top: 1.25rem; margin-top: 0.5rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">Recommendation Details</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="recommendation_name">Recommender Name</label>
                            <input type="text" class="form-control-dark" id="recommendation_name" name="meta[recommender_name]" value="{{ old('meta.recommendation_name') }}" placeholder="Full name">
                        </div>
                        <div>
                            <label class="form-label" for="recommendation_organization">Organization</label>
                            <select class="form-select-dark" id="recommendation_organization" name="meta[recommender_org]" onchange="toggleOrgOther(this, 'recommendation_organization_other')">
                                <option value="">-- Select Organization --</option>
                                <option value="KMJ" {{ old('meta.recommendation_organization') == 'KMJ' ? 'selected' : '' }}>KMJ</option>
                                <option value="SYS" {{ old('meta.recommendation_organization') == 'SYS' ? 'selected' : '' }}>SYS</option>
                                <option value="SSF" {{ old('meta.recommendation_organization') == 'SSF' ? 'selected' : '' }}>SSF</option>
                                <option value="Others" {{ old('meta.recommendation_organization') == 'Others' ? 'selected' : '' }}>Others</option>
                            </select>
                            <input type="text" class="form-control-dark" id="recommendation_organization_other" name="meta[recommender_org_other]" value="{{ old('meta.recommendation_organization_other') }}" placeholder="Specify organization" style="margin-top: 0.5rem; display: {{ old('meta.recommendation_organization') == 'Others' ? 'block' : 'none' }};">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="recommendation_phone">Phone</label>
                            <input type="tel" class="form-control-dark" id="recommendation_phone" name="meta[recommender_phone]" value="{{ old('meta.recommendation_phone') }}" placeholder="Phone number">
                        </div>
                        <div>
                            <label class="form-label" for="recommendation_position">Position / Designation</label>
                            <input type="text" class="form-control-dark" id="recommendation_position" name="meta[recommender_position]" value="{{ old('meta.recommendation_position') }}" placeholder="Job title / Designation">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->                <button type="submit" class="btn-custom" style="width: 100%; padding: 0.75rem;">
                    Submit Application
                </button>
            </form>
        </div>
    </div>

    <!-- Edit Application Modal Dialog -->
    <div id="editAppModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.75); display: none; align-items: center; justify-content: center; z-index: 1000; overflow-y: auto;" onclick="closeEditModal()">
        <div class="panel" style="width: 100%; max-width: 750px; margin: 2rem auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #2a3547; max-height: 90vh; overflow-y: auto;" onclick="event.stopPropagation()">
            
            <button onclick="closeEditModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; z-index: 10;"><i class="bx bx-x"></i></button>
            
            <div class="panel-header" style="margin-bottom: 1.5rem;">
                <h2 class="panel-title" style="font-size: 1.25rem;">Edit Differently Abled Application</h2>
            </div>

            <form id="editAppForm" action="" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Hidden Category and redirect tags -->
                <input type="hidden" name="category" value="{{ $categoryName }}">
                <input type="hidden" name="redirect_category" value="{{ $categorySlug }}">
                <input type="hidden" name="amount_requested" value="0">

                <!-- Form Section 1: Personal Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">1. Personal Details of Applicant</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_applicant_name">Applicant Name *</label>
                            <input type="text" class="form-control-dark" id="edit_applicant_name" name="applicant_name" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_father_name">Father's Name *</label>
                            <input type="text" class="form-control-dark" id="edit_father_name" name="meta[father_name]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_fathers_father">Father's Father</label>
                            <input type="text" class="form-control-dark" id="edit_fathers_father" name="meta[fathers_father]">
                        </div>
                        <div>
                            <label class="form-label" for="edit_mother_name">Mother's Name *</label>
                            <input type="text" class="form-control-dark" id="edit_mother_name" name="meta[mother_name]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_gender">Gender *</label>
                            <select class="form-select-dark" id="edit_gender" name="meta[gender]" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="edit_aadhar_number">Aadhaar Number *</label>
                            <input type="text" class="form-control-dark" id="edit_aadhar_number" name="meta[aadhar_number]" placeholder="XXXX XXXX XXXX" maxlength="14" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1.5fr 1fr 1.5fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_dob">Date of Birth *</label>
                            <input type="date" class="form-control-dark" id="edit_dob" name="meta[dob]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_age">Age *</label>
                            <input type="number" class="form-control-dark" id="edit_age" name="meta[age]" readonly style="background-color: rgba(255, 255, 255, 0.05); cursor: not-allowed;">
                        </div>
                        <div>
                            <label class="form-label" for="edit_marital_status">Marital Status *</label>
                            <select class="form-select-dark" id="edit_marital_status" name="meta[marital_status]" required>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="edit_guardian_name">Name of Guardian *</label>
                            <input type="text" class="form-control-dark" id="edit_guardian_name" name="meta[guardian_name]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_guardian_relation">Relationship *</label>
                            <input type="text" class="form-control-dark" id="edit_guardian_relation" name="meta[guardian_relation]" required>
                        </div>
                    </div>
                </div>

                <!-- Form Section 2: Family & Economic Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">2. Family & Economic Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1.5fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_male_members">Male Members *</label>
                            <input type="number" class="form-control-dark" id="edit_male_members" name="meta[male_members]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_female_members">Female Members *</label>
                            <input type="number" class="form-control-dark" id="edit_female_members" name="meta[female_members]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_total_members">Total Members *</label>
                            <input type="number" class="form-control-dark" id="edit_total_members" name="meta[total_members]" readonly>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_people_with_disabilities">People with Disabilities *</label>
                            <input type="number" class="form-control-dark" id="edit_people_with_disabilities" name="meta[people_with_disabilities]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_monthly_income">Monthly Income (₹) *</label>
                            <input type="number" class="form-control-dark" id="edit_monthly_income" name="meta[monthly_income]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="edit_monthly_cost">Monthly Cost (₹) *</label>
                            <input type="number" class="form-control-dark" id="edit_monthly_cost" name="meta[monthly_cost]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_income_source">Source of Income *</label>
                            <input type="text" class="form-control-dark" id="edit_income_source" name="meta[income_source]" required>
                        </div>
                    </div>
                </div>

                <!-- Form Section 3: Education & Disability Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">3. Education & Disability Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_studying_institution">Name of Studying Institution</label>
                            <input type="text" class="form-control-dark" id="edit_studying_institution" name="meta[studying_institution]">
                        </div>
                        <div>
                            <label class="form-label" for="edit_not_studying_reason">If you don't study, reason</label>
                            <input type="text" class="form-control-dark" id="edit_not_studying_reason" name="meta[not_studying_reason]">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_health_status">Health Status *</label>
                            <input type="text" class="form-control-dark" id="edit_health_status" name="meta[health_status]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_disability_type">Disability *</label>
                            <select class="form-select-dark" id="edit_disability_type" name="meta[disability_type]" required>
                                <option value="Mute">Mute</option>
                                <option value="Deafness">Deafness</option>
                                <option value="Blindness">Blindness</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_disability_percentage">Percentage of Disability (%) *</label>
                            <input type="number" class="form-control-dark" id="edit_disability_percentage" name="meta[disability_percentage]" min="0" max="100" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_disability_date">Date/Year of Disability *</label>
                            <input type="date" class="form-control-dark" id="edit_disability_date" name="meta[disability_date]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_disability_level">Level of Disability *</label>
                            <select class="form-select-dark" id="edit_disability_level" name="meta[disability_level]" required>
                                <option value="Simple">Simple</option>
                                <option value="Hard">Hard</option>
                                <option value="Very Hard">Very Hard</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="edit_other_help">Anyone else help?</label>
                            <input type="text" class="form-control-dark" id="edit_other_help" name="meta[other_help]">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; align-items: center;">
                        <div>
                            <label class="form-label" for="edit_description">Description if any</label>
                            <textarea class="form-control-dark" id="edit_description" name="meta[description]" style="height: 50px;"></textarea>
                        </div>
                        <div>
                            <label class="form-label" for="edit_accommodation">Accommodation Details *</label>
                            <select class="form-select-dark" id="edit_accommodation" name="meta[accommodation]" required>
                                <option value="Own House">Own House</option>
                                <option value="Ancestral Home">Ancestral Home</option>
                                <option value="Rental House">Rental House</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Form Section 4: Address & Contact Details -->
                <div style="margin-bottom: 2rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">4. Address & Contact Details</h4>
                    
                    @include('applications.address_form_fields', ['idPrefix' => 'edit_', 'app' => null])

                    <input type="hidden" name="status" id="edit_status">

                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="edit_details">Additional Notes</label>
                        <textarea class="form-control-dark" id="edit_details" name="details" style="height: 60px; resize: vertical;"></textarea>
                    </div>
                </div>

                <!-- Form Section 5: Cluster & Agency Details -->
                <div style="margin-bottom: 2rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">5. Cluster & Agency Details (Optional)</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_cluster_id">Cluster</label>
                            <select class="form-select-dark" id="edit_cluster_id" name="cluster_id" style="width: 100%; padding: 0.6rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: #111c2d; color: #ffffff;">
                                <option value="">-- Select Cluster --</option>
                                @foreach($clusters as $cl)
                                    <option value="{{ $cl->id }}">{{ $cl->name }} ({{ $cl->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="edit_agency_number">Agency Number</label>
                            <input type="text" class="form-control-dark" id="edit_agency_number" name="agency_number">
                        </div>
                    </div>
                </div>


                <!-- Edit Recommendation Details Section -->
                <div style="border-top: 1px solid var(--panel-border); padding-top: 1.25rem; margin-top: 0.5rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">Recommendation Details</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_recommendation_name">Recommender Name</label>
                            <input type="text" class="form-control-dark" id="edit_recommendation_name" name="meta[recommender_name]" placeholder="Full name">
                        </div>
                        <div>
                            <label class="form-label" for="edit_recommendation_organization">Organization</label>
                            <select class="form-select-dark" id="edit_recommendation_organization" name="meta[recommender_org]" onchange="toggleOrgOther(this, 'edit_recommendation_organization_other')">
                                <option value="">-- Select Organization --</option>
                                <option value="KMJ">KMJ</option>
                                <option value="SYS">SYS</option>
                                <option value="SSF">SSF</option>
                                <option value="Others">Others</option>
                            </select>
                            <input type="text" class="form-control-dark" id="edit_recommendation_organization_other" name="meta[recommender_org_other]" placeholder="Specify organization" style="margin-top: 0.5rem; display: none;">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="edit_recommendation_phone">Phone</label>
                            <input type="tel" class="form-control-dark" id="edit_recommendation_phone" name="meta[recommender_phone]" placeholder="Phone number">
                        </div>
                        <div>
                            <label class="form-label" for="edit_recommendation_position">Position / Designation</label>
                            <input type="text" class="form-control-dark" id="edit_recommendation_position" name="meta[recommender_position]" placeholder="Job title / Designation">
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->                <button type="submit" class="btn-custom" style="width: 100%; padding: 0.75rem;">
                    Save Changes
                </button>
            </form>
        </div>
    </div>

    <!-- Approve Application Modal Dialog -->
    <div id="approveAppModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.75); display: none; align-items: center; justify-content: center; z-index: 1200; overflow-y: auto;" onclick="closeApproveModal()">
        <div class="panel" style="width: 100%; max-width: 500px; margin: 2rem auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #2a3547; overflow-y: auto;" onclick="event.stopPropagation()">
            
            <button onclick="closeApproveModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; z-index: 10;"><i class="bx bx-x"></i></button>
            
            <div class="panel-header" style="margin-bottom: 1.5rem;">
                <h2 class="panel-title" style="font-size: 1.25rem;"><i class="bx bx-check-circle" style="vertical-align: middle; margin-right: 0.5rem; color: var(--accent-green);"></i> Approve Application</h2>
            </div>

            <form id="approveAppForm" action="" method="POST">
                @csrf
                <div style="margin-bottom: 1.25rem;">
                    <label class="form-label" for="approve_cluster_id">Select Cluster *</label>
                    <select id="approve_cluster_id" name="cluster_id" class="form-control-dark" style="width: 100%; padding: 0.6rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: #111c2d; color: #ffffff;" required>
                        <option value="">-- Choose Cluster --</option>
                        @foreach($clusters as $cl)
                            <option value="{{ $cl->id }}">{{ $cl->name }} ({{ $cl->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom: 1.25rem;">
                    <label class="form-label" for="approve_agency_number">Agency Number *</label>
                    <input type="text" id="approve_agency_number" name="agency_number" class="form-control-dark" style="width: 100%; padding: 0.6rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: #111c2d; color: #ffffff;" required placeholder="Enter Agency Number">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <div>
                        <label class="form-label" for="approve_agency_name">Agency Name (Donor)</label>
                        <select id="approve_agency_name" name="meta[agency_name]" class="form-select-dark" style="width: 100%; padding: 0.6rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: #111c2d; color: #ffffff;">
                            <option value="">-- Select Agency --</option>
                            @foreach($donors as $d)
                                <option value="{{ $d->name }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label" for="approve_application_date">Application Date</label>
                        <input type="date" id="approve_application_date" name="meta[application_date]" class="form-control-dark" style="width: 100%; padding: 0.6rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: #111c2d; color: #ffffff;" value="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                    <button type="button" onclick="closeApproveModal()" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); padding: 0.6rem 1.5rem;">Cancel</button>
                    <button type="submit" class="btn-custom" style="background: linear-gradient(135deg, #2ecc71, #27ae60); border: none; padding: 0.6rem 1.5rem; font-weight: 600;">Approve Application</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Scripts -->
    <script>
        var projectsMap = @json($projectsMap ?? []); window.projectsMap = projectsMap;

        // Add Application Modal Toggle
        function openModal() {
            const modal = document.getElementById('addAppModal') || document.getElementById('addModal') || document.getElementById('createModal');
            if (modal) modal.style.display = 'flex';
        }

        // Close Modal Toggle
        function closeModal() {
        const modal = document.getElementById('addAppModal') || document.getElementById('addModal');
        if (modal) modal.style.display = 'none';
    }

        // Edit Application Modal Toggle
        function openEditModal(appItem) {
            if (appItem && appItem.status === 'Approved') {
                alert('Approved applications cannot be edited.');
                return;
            }
            const form = document.getElementById('editAppForm');
            form.action = "{{ url('admin/applications') }}/" + appItem.id;

            document.getElementById('edit_applicant_name').value = appItem.applicant_name;
            document.getElementById('edit_status').value = appItem.status;
            document.getElementById('edit_details').value = appItem.details || '';

            // Meta fields mapping
                        const meta = appItem.meta || {};
            
            const getVal = (primary, alts = []) => {
                if (meta[primary] !== undefined && meta[primary] !== null && meta[primary] !== '') return meta[primary];
                if (appItem[primary] !== undefined && appItem[primary] !== null && appItem[primary] !== '') return appItem[primary];
                for (let a of alts) {
                    if (meta[a] !== undefined && meta[a] !== null && meta[a] !== '') return meta[a];
                    if (appItem[a] !== undefined && appItem[a] !== null && appItem[a] !== '') return appItem[a];
                }
                return '';
            };

            const setField = (id, val) => {
                const el = document.getElementById(id);
                if (el) el.value = val;
            };

            setField('edit_house_name', getVal('house_name'));
            setField('edit_location', getVal('location', ['place']));
            setField('edit_place', getVal('place', ['location']));
            setField('edit_village', getVal('village'));
            setField('edit_post', getVal('post', ['post_office']));
            setField('edit_post_office', getVal('post_office', ['post']));
            setField('edit_panchayath', getVal('panchayath', ['panchayat']));
            setField('edit_panchayat', getVal('panchayat', ['panchayath']));
            setField('edit_district', getVal('district'));
            setField('edit_state', getVal('state'));
            setField('edit_pin_code', getVal('pin_code', ['pin', 'locality_pin_code']));
            setField('edit_pin', getVal('pin', ['pin_code']));

            setField('edit_committee_name', getVal('committee_name'));
            setField('edit_reg_number', getVal('reg_number'));
            setField('edit_year', getVal('year'));
            setField('edit_permitted_type', getVal('permitted_type'));
            setField('edit_area', getVal('area'));
            setField('edit_details', appItem.details || appItem.additional_note || meta.details || meta.additional_note || '');

            const recName = getVal('recommendation_name', ['recommender_name']);
            setField('edit_recommendation_name', recName);
            setField('edit_recommender_name', recName);

            const recOrg = getVal('recommendation_organization', ['recommender_org']);
            setField('edit_recommendation_organization', recOrg);
            setField('edit_recommender_org', recOrg);

            const recOrgOther = getVal('recommendation_organization_other', ['recommender_org_other']);
            setField('edit_recommendation_organization_other', recOrgOther);

            const recPhone = getVal('recommendation_phone', ['recommender_phone']);
            setField('edit_recommendation_phone', recPhone);
            setField('edit_recommender_phone', recPhone);

            const recPos = getVal('recommendation_position', ['recommender_position']);
            setField('edit_recommendation_position', recPos);
            setField('edit_recommender_position', recPos);

            if (document.getElementById('edit_pin_code')) { document.getElementById('edit_pin_code').value = pinCode; }
            if (document.getElementById('edit_mobile_1')) { document.getElementById('edit_mobile_1').value = mob1; }
            if (document.getElementById('edit_mobile_2')) { document.getElementById('edit_mobile_2').value = mob2; }
            document.getElementById('edit_cluster_id').value = appItem.cluster_id || '';
            document.getElementById('edit_agency_number').value = appItem.agency_number || '';

            if (document.getElementById('edit_recommendation_name')) document.getElementById('edit_recommendation_name').value = meta.recommendation_name || '';
            if (document.getElementById('edit_recommendation_organization')) {
                const orgSel = document.getElementById('edit_recommendation_organization');
                orgSel.value = meta.recommendation_organization || '';
                const orgOtherInput = document.getElementById('edit_recommendation_organization_other');
                if (orgOtherInput) {
                    orgOtherInput.value = meta.recommendation_organization_other || '';
                    orgOtherInput.style.display = meta.recommendation_organization === 'Others' ? 'block' : 'none';
                }
            }
            if (document.getElementById('edit_recommendation_phone')) document.getElementById('edit_recommendation_phone').value = meta.recommendation_phone || '';
            if (document.getElementById('edit_recommendation_position')) document.getElementById('edit_recommendation_position').value = meta.recommendation_position || '';

            document.getElementById('editAppModal').style.display = 'flex';
        }


        function toggleOrgOther(selectEl, otherId) {
            const otherInput = document.getElementById(otherId);
            if (otherInput) {
                otherInput.style.display = selectEl.value === 'Others' ? 'block' : 'none';
                if (selectEl.value !== 'Others') otherInput.value = '';
            }
        }
        function closeEditModal() {
        const modal = document.getElementById('editAppModal') || document.getElementById('editModal');
        if (modal) modal.style.display = 'none';
    }

        // View Details Modal Toggle
        function openDetailsModal(appItem) {
            currentDetailsAppItem = appItem;
            
            // Populate status actions in the modal footer dynamically
            const statusActionsContainer = document.getElementById('modal_status_actions');
            if (statusActionsContainer) {
                let statusHtml = '';
                const rejectUrl = `{{ url('admin/applications') }}/{{ $categorySlug }}/${appItem.id}/reject`;
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                if (appItem.status === 'Pending') {
                    statusHtml = `
                        <button type="button" onclick="closeDetailsModal(); openApproveModal(${appItem.id}, '${appItem.cluster_id || ''}', '${appItem.agency_number || ''}')" class="btn-custom" style="background: linear-gradient(135deg, #2ecc71, #27ae60); padding: 0.6rem 1.5rem; display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600; cursor: pointer; border: none;">
                            <i class="bx bx-check"></i> Approve Application
                        </button>
                        <form action="${rejectUrl}" method="POST" style="display: inline-block;" onsubmit="confirmApplicationRejection(event, this); return false;">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <button type="submit" class="btn-danger-custom" style="padding: 0.6rem 1.5rem; display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                                <i class="bx bx-x"></i> Reject
                            </button>
                        </form>
                    `;
                } else if (appItem.status === 'Approved') {
                    const isAssignedToProject = !!(typeof projectsMap !== 'undefined' && projectsMap[appItem.id]);
                    if (isAssignedToProject) {
                        statusHtml = `
                            <div style="background-color: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); color: #f59e0b; padding: 0.6rem 1rem; border-radius: 6px; font-size: 0.85rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem;">
                                <i class="bx bx-info-circle" style="font-size: 1.1rem;"></i> Application is assigned to a project and cannot be rejected.
                            </div>
                        `;
                    } else {
                        statusHtml = `
                            <form action="${rejectUrl}" method="POST" style="display: inline-block;" onsubmit="confirmApplicationRejection(event, this); return false;">
                                <input type="hidden" name="_token" value="${csrfToken}">
                                <button type="submit" class="btn-danger-custom" style="padding: 0.6rem 1.5rem; display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                                    <i class="bx bx-x"></i> Reject Application
                                </button>
                            </form>
                        `;
                    }
                } else if (appItem.status === 'Rejected') {
                    statusHtml = `
                        <button type="button" onclick="closeDetailsModal(); openApproveModal(${appItem.id}, '${appItem.cluster_id || ''}', '${appItem.agency_number || ''}')" class="btn-custom" style="background: linear-gradient(135deg, #2ecc71, #27ae60); padding: 0.6rem 1.5rem; display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600; cursor: pointer; border: none;">
                            <i class="bx bx-check"></i> Approve Application
                        </button>
                    `;
                }
                statusActionsContainer.innerHTML = statusHtml;
            }
            const meta = appItem.meta || {};
            const addr = appItem.address || {};
            const formatVal = (val) => val ? val : '<span style="color: var(--text-muted); font-style: italic;">N/A</span>';
            
            let html = `
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <!-- Col 1 -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">1. Personal Details of Applicant</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px;">Applicant Name:</td><td style="font-weight: 600; color: #ffffff;">${formatVal(appItem.applicant_name)} (${formatVal(meta.gender)})</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Date of Birth / Age:</td><td>${formatVal(meta.dob)} / ${formatVal(meta.age)} yrs</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Aadhaar / Marital Status:</td><td>${formatVal(meta.aadhar_number)} / ${formatVal(meta.marital_status)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Father's Name:</td><td>${formatVal(meta.father_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Father's Father:</td><td>${formatVal(meta.fathers_father)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Mother's Name:</td><td>${formatVal(meta.mother_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Guardian / Relation:</td><td>${formatVal(meta.guardian_name)} (${formatVal(meta.guardian_relation)})</td></tr>
                        </table>

                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">2. Family & Economic Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px;">Male / Female Members:</td><td>M: ${formatVal(meta.male_members)} / F: ${formatVal(meta.female_members)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Total Members:</td><td style="font-weight: 600; color: #ffffff;">${formatVal(meta.total_members)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">People with Disabilities:</td><td>${formatVal(meta.people_with_disabilities)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Monthly Income:</td><td>${meta.monthly_income ? '₹' + Number(meta.monthly_income).toLocaleString() : 'N/A'}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Monthly Cost:</td><td>${meta.monthly_cost ? '₹' + Number(meta.monthly_cost).toLocaleString() : 'N/A'}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Source of Income:</td><td>${formatVal(meta.income_source)}</td></tr>
                        </table>
                    </div>

                    <!-- Col 2 -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">3. Education & Disability Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px;">Studying Institution:</td><td>${formatVal(meta.studying_institution)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">If not study, reason:</td><td>${formatVal(meta.not_studying_reason)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Health Status:</td><td>${formatVal(meta.health_status)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Disability Type:</td><td style="font-weight: 600; color: #ffffff;">${formatVal(meta.disability_type)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Disability Percentage:</td><td>${formatVal(meta.disability_percentage)}%</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Date/Year of Disability:</td><td>${formatVal(meta.disability_date)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Level of Disability:</td><td>${formatVal(meta.disability_level)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Anyone else help?</td><td>${formatVal(meta.other_help)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Accommodation Details:</td><td>${formatVal(meta.accommodation)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Description:</td><td>${formatVal(meta.description)}</td></tr>
                        </table>

                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">4. Address & Contact Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px;">House Name / Place:</td><td>${formatVal(meta.house_name || addr.house_name)} / ${formatVal(meta.place || addr.place)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Village / P.O.:</td><td>${formatVal(meta.village || addr.village)} / ${formatVal(meta.post_office || meta.post || addr.post_office)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Panchayath / District:</td><td>${formatVal(meta.panchayat || addr.panchayat)} / ${formatVal(meta.district || addr.district)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">State / Pin Code:</td><td>${formatVal(meta.state || addr.state)} / ${formatVal(meta.pin_code || meta.pincode || addr.pin_code)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Mobile 1 / Mobile 2:</td><td>${formatVal(meta.mobile_1 || meta.mobile || addr.contact_number_1)} / ${formatVal(meta.mobile_2 || addr.contact_number_2)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Review Status:</td><td style="font-weight: 600; color: #ffffff;">${appItem.status}</td></tr>
                        </table>
                    </div>
                </div>

                ${appItem.status === 'Approved' ? `
                <div style="margin-top: 1.5rem; background: rgba(255,255,255,0.02); border: 1px solid var(--panel-border); padding: 1.25rem; border-radius: 8px;">
                    <h4 style="color: var(--accent-green); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">
                        Cluster &amp; Agency Number Details
                    </h4>
                    
                    <div id="modal-cluster-container">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 2rem;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);">
                                    <td style="padding: 0.5rem 0; font-weight: 600; width: 160px; color: var(--text-muted);">Assigned Cluster:</td>
                                    <td id="modal-cluster-display-name" style="font-weight: 600; color: #ffffff;">
                                        ${appItem.cluster ? `${appItem.cluster.name} (${appItem.cluster.code})` : '<span style="color: var(--text-muted); font-style: italic;">Not assigned</span>'}
                                    </td>
                                </tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);">
                                    <td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Agency Number:</td>
                                    <td id="modal-agency-display-number" style="font-weight: 600; color: #ffffff;">
                                        ${appItem.agency_number ? appItem.agency_number : '<span style="color: var(--text-muted); font-style: italic;">Not set</span>'}
                                    </td>
                                </tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);">
                                    <td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Agency Name (Donor):</td>
                                    <td id="modal-agency-display-name" style="font-weight: 600; color: #ffffff;">
                                        ${meta.agency_name ? meta.agency_name : '<span style="color: var(--text-muted); font-style: italic;">Not set</span>'}
                                    </td>
                                </tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);">
                                    <td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Application Date:</td>
                                    <td id="modal-agency-display-date" style="font-weight: 600; color: #ffffff;">
                                        ${meta.application_date ? meta.application_date : '<span style="color: var(--text-muted); font-style: italic;">Not set</span>'}
                                    </td>
                                </tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);">
                                    <td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Sponsor Status:</td>
                                    <td id="modal-sponsor-display-status" style="font-weight: 600; color: #ffffff;">
                                        ${appItem.sponsor_status === 'Sponsored' 
                                            ? '<span style="background-color: rgba(16, 185, 129, 0.2); color: var(--accent-green); padding: 0.15rem 0.4rem; border-radius: 4px; font-weight: 600;">Sponsored</span>' 
                                            : '<span style="background-color: rgba(245, 158, 11, 0.2); color: #f59e0b; padding: 0.15rem 0.4rem; border-radius: 4px; font-weight: 600;">Not Sponsored</span>'}
                                    </td>
                                </tr>
                            </table>
                            @if(Auth::user() && Auth::user()->isSuperAdmin())
                            <button type="button" onclick="toggleClusterEditForm()" class="btn-custom" style="background: transparent; border: 1px solid var(--accent-cyan); color: var(--accent-cyan); padding: 0.35rem 0.75rem; font-size: 0.8rem; border-radius: 6px; cursor: pointer; white-space: nowrap;">
                                <i class="bx bx-edit"></i> Edit Cluster
                            </button>
                            @endif
                        </div>
                    </div>

                    @if(Auth::user() && Auth::user()->isSuperAdmin())
                    <div id="modal-cluster-edit-form" style="display: none; margin-top: 0.5rem;">
                        <form action="{{ url('admin/applications') }}/${appItem.id}/update-cluster" method="POST" onsubmit="submitClusterForm(event, ${appItem.id})">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                                <div>
                                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.35rem;">Cluster</label>
                                    <select name="cluster_id" class="form-control-dark" style="width: 100%; padding: 0.5rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" required>
                                        <option value="">-- Choose Cluster --</option>
                                        @foreach($clusters as $cl)
                                            <option value="{{ $cl->id }}" ${appItem.cluster_id == {{ $cl->id }} ? 'selected' : ''}>{{ $cl->name }} ({{ $cl->code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.35rem;">Agency Number</label>
                                    <input type="text" name="agency_number" value="${appItem.agency_number || ''}" class="form-control-dark" style="width: 100%; padding: 0.5rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" required>
                                </div>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                                <div>
                                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.35rem;">Agency Name (Donor)</label>
                                    <select name="meta[agency_name]" class="form-select-dark" style="width: 100%; padding: 0.5rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;">
                                        <option value="">-- Select Agency --</option>
                                        @foreach($donors as $d)
                                            <option value="{{ $d->name }}" ${meta.agency_name == '{{ addslashes($d->name) }}' ? 'selected' : ''}>{{ $d->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.35rem;">Application Date</label>
                                    <input type="date" name="meta[application_date]" value="${meta.application_date || ''}" class="form-control-dark" style="width: 100%; padding: 0.5rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;">
                                </div>
                            </div>
                            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                <button type="button" onclick="toggleClusterEditForm()" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); padding: 0.35rem 0.75rem; font-size: 0.8rem;">Cancel</button>
                                <button type="submit" class="btn-custom" style="background: var(--accent-blue); color: #ffffff; border: none; padding: 0.35rem 0.75rem; font-size: 0.8rem;">Save Cluster Info</button>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
                ` : ''}

                ${(appItem.status === 'Rejected' && (appItem.rejected_reason || meta.rejected_reason)) ? `
                <div style="margin-top: 1.5rem; border-top: 1px solid var(--panel-border); padding-top: 1rem;">
                    <h5 style="color: var(--accent-red); font-size: 0.85rem; margin-bottom: 0.5rem; text-transform: uppercase; font-weight: 700;">Rejected Reason:</h5>
                    <p style="color: #ef4444; line-height: 1.5; font-size: 0.85rem; margin: 0; background-color: rgba(239, 68, 68, 0.08); padding: 0.75rem; border-radius: 6px; border: 1px solid rgba(239, 68, 68, 0.3); min-height: 50px; font-weight: 600;">
                        ${appItem.rejected_reason || meta.rejected_reason}
                    </p>
                </div>
                ` : ''}
                
                <div style="margin-top: 1.5rem; border-top: 1px solid var(--panel-border); padding-top: 1rem;">
                    <h5 style="color: var(--accent-cyan); font-size: 0.85rem; margin-bottom: 0.5rem; text-transform: uppercase; font-weight: 700;">Additional Notes:</h5>
                    <p style="color: var(--text-muted); line-height: 1.5; font-size: 0.85rem; margin: 0; background-color: #121824; padding: 0.75rem; border-radius: 6px; border: 1px solid var(--panel-border); min-height: 50px;">
                        ${appItem.details ? appItem.details : 'No additional notes provided.'}
                    </p>
                </div>
                ${(meta.recommendation_name || meta.recommendation_organization || meta.recommendation_phone || meta.recommendation_position) ? `
                <div style="margin-top: 1.5rem; border-top: 1px solid var(--panel-border); padding-top: 1rem;">
                    <h5 style="color: var(--accent-cyan); font-size: 0.85rem; margin-bottom: 0.75rem; text-transform: uppercase; font-weight: 700;">Recommendation Details:</h5>
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                        ${meta.recommendation_name ? `<tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.4rem 0; font-weight: 600; width: 140px;">Name:</td><td>${meta.recommendation_name}</td></tr>` : ''}
                        ${meta.recommendation_organization ? `<tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.4rem 0; font-weight: 600;">Organization:</td><td>${meta.recommendation_organization === 'Others' ? (meta.recommendation_organization_other || 'Others') : meta.recommendation_organization}</td></tr>` : ''}
                        ${meta.recommendation_phone ? `<tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.4rem 0; font-weight: 600;">Phone:</td><td>${meta.recommendation_phone}</td></tr>` : ''}
                        ${meta.recommendation_position ? `<tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.4rem 0; font-weight: 600;">Position:</td><td>${meta.recommendation_position}</td></tr>` : ''}
                    </table>
                </div>` : ''}
            `;
            
            document.getElementById('details_content').innerHTML = html;
            const editBtn = document.getElementById('modal_edit_btn');
            const deleteBtn = document.getElementById('modal_delete_btn');
            if (editBtn) editBtn.style.display = (appItem.status === 'Approved') ? 'none' : 'inline-block';
            if (deleteBtn) deleteBtn.style.display = (appItem.status === 'Approved') ? 'none' : 'inline-block';
            document.getElementById('detailsAppModal').style.display = 'flex';
        }

        var currentDetailsAppItem = null;

        function editFromDetails() {
            if (currentDetailsAppItem) {
                closeDetailsModal();
                openEditModal(currentDetailsAppItem);
            }
        }

        function deleteFromDetails() {
            if (currentDetailsAppItem) {
                if (currentDetailsAppItem.status === 'Approved') {
                    alert('Approved applications cannot be deleted.');
                    return;
                }
                showCustomConfirm('Are you sure you want to delete this application? This action cannot be undone.', function() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ url('admin/applications') }}/" + currentDetailsAppItem.id;

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    form.appendChild(csrfInput);

                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    form.appendChild(methodInput);

                    const redirectInput = document.createElement('input');
                    redirectInput.type = 'hidden';
                    redirectInput.name = 'redirect_category';
                    redirectInput.value = '{{ $categorySlug }}';
                form.appendChild(redirectInput);

                    document.body.appendChild(form);
                    if (typeof handleFormSubmit === 'function') {
                        handleFormSubmit({ target: form, preventDefault: () => {} });
                    } else {
                        form.submit();
                    }
                });
            }
        }

        function closeDetailsModal() {
            document.getElementById('detailsAppModal').style.display = 'none';
        }

        function filterTable() {
            const input = document.getElementById('tableSearchInput');
            const filter = input.value.toLowerCase().trim();
            const rows = document.querySelectorAll('.app-row');
            
            rows.forEach(row => {
                const searchText = row.getAttribute('data-search') || '';
                if (searchText.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Realtime calculation of total family members count and age from Date of Birth
        document.addEventListener("DOMContentLoaded", function() {
            // Age calculation helper
            function calcAge(dobVal) {
                if (!dobVal) return '';
                const birthDate = new Date(dobVal);
                if (isNaN(birthDate.getTime())) return '';
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const m = today.getMonth() - birthDate.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                return age >= 0 ? age : 0;
            }

            function bindAgeCalculation(dobId, ageId) {
                const dobInput = document.getElementById(dobId);
                const ageInput = document.getElementById(ageId);
                if (dobInput && ageInput) {
                    const updateAge = function() {
                        const calculated = calcAge(dobInput.value);
                        if (calculated !== '') {
                            ageInput.value = calculated;
                        }
                    };
                    dobInput.addEventListener('input', updateAge);
                    dobInput.addEventListener('change', updateAge);
                }
            }

            bindAgeCalculation('dob', 'age');
            bindAgeCalculation('edit_dob', 'edit_age');

            // Add Modal
            const maleInput = document.getElementById('male_members');
            const femaleInput = document.getElementById('female_members');
            const totalInput = document.getElementById('total_members');

            function calculateTotal() {
                const male = parseInt(maleInput.value) || 0;
                const female = parseInt(femaleInput.value) || 0;
                totalInput.value = male + female;
            }

            if (maleInput && femaleInput && totalInput) {
                maleInput.addEventListener('input', calculateTotal);
                femaleInput.addEventListener('input', calculateTotal);
            }

            // Edit Modal
            const editMaleInput = document.getElementById('edit_male_members');
            const editFemaleInput = document.getElementById('edit_female_members');
            const editTotalInput = document.getElementById('edit_total_members');

            function calculateEditTotal() {
                const male = parseInt(editMaleInput.value) || 0;
                const female = parseInt(editFemaleInput.value) || 0;
                editTotalInput.value = male + female;
            }

            if (editMaleInput && editFemaleInput && editTotalInput) {
                editMaleInput.addEventListener('input', calculateEditTotal);
                editFemaleInput.addEventListener('input', calculateEditTotal);
            }
        });

        // Automatically open add modal if validation error occurs on creation
        @if ($errors->any())
            document.addEventListener("DOMContentLoaded", function() {
                openModal();
            });
        @endif

        function openApproveModal(appId, clusterId = '', agencyNumber = '', agencyName = '', appDate = '') {
            const form = document.getElementById('approveAppForm');
            form.action = `{{ url('admin/applications') }}/{{ $categorySlug }}/${appId}/approve`;
            
            if (document.getElementById('approve_cluster_id')) document.getElementById('approve_cluster_id').value = clusterId || '';
            if (document.getElementById('approve_agency_number')) document.getElementById('approve_agency_number').value = agencyNumber || '';
            if (document.getElementById('approve_agency_name')) document.getElementById('approve_agency_name').value = agencyName || '';
            if (document.getElementById('approve_application_date')) document.getElementById('approve_application_date').value = appDate || '{{ date("Y-m-d") }}';
            
            document.getElementById('approveAppModal').style.display = 'flex';
        };

        function closeApproveModal() {
            document.getElementById('approveAppModal').style.display = 'none';
        }

        function toggleClusterEditForm() {
            const displayDiv = document.getElementById('modal-cluster-container');
            const editDiv = document.getElementById('modal-cluster-edit-form');
            if (displayDiv.style.display === 'none') {
                displayDiv.style.display = 'block';
                editDiv.style.display = 'none';
            } else {
                displayDiv.style.display = 'none';
                editDiv.style.display = 'block';
            }
        }

        async function submitClusterForm(event, appId) {
            event.preventDefault();
            const form = event.target;
            const clusterId = form.querySelector('[name="cluster_id"]').value;
            const agencyNumber = form.querySelector('[name="agency_number"]').value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const response = await fetch("{{ url('admin/applications') }}/" + appId + "/update-cluster", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        cluster_id: clusterId || null,
                        agency_number: agencyNumber || null
                    })
                });

                const result = await response.json();
                if (result.success) {
                    window.location.reload();
                } else {
                    alert(result.error || 'Failed to update Cluster and Agency Number.');
                }
            } catch (err) {
                console.error(err);
                alert('An error occurred while updating.');
            }
        }
    
        // Automatically open edit modal if query parameter edit is present
        @if(request()->has('edit'))
            document.addEventListener("DOMContentLoaded", function() {
                const editItem = {!! json_encode($applications->firstWhere('id', request()->get('edit'))) !!};
                if (editItem) {
                    openEditModal(editItem);
                }
            });
        @endif
    
        // Global Window Bindings
        window.openModal = openModal;
        window.closeModal = closeModal;
        window.openEditModal = openEditModal;
        window.closeEditModal = closeEditModal;
        window.openDetailsModal = openDetailsModal;
        window.closeDetailsModal = closeDetailsModal;
        window.openApproveModal = openApproveModal;
        window.closeApproveModal = closeApproveModal;
        window.toggleOrgOther = toggleOrgOther;
    </script>

@endsection
