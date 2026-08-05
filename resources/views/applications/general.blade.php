@extends('layouts.admin')

@section('title', 'General Applications')

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
            <h2 class="panel-title">General Applications List</h2>
            <div style="display: flex; gap: 0.75rem;">
                <a href="{{ route('applications.export', $categorySlug) }}" class="btn-custom" style="background: linear-gradient(135deg, #2ecc71, #27ae60); text-decoration: none;">
                    <i class="bx bx-download"></i> Download Excel
                </a>
                <button onclick="openModal()" class="btn-custom">
                    <i class="bx bx-plus-circle"></i> Add Application
                </button>
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
                        <th>Type</th>
                        <th>Name</th>
                        <th>Institute / Org &amp; Unit</th>
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
                            $appId = 'APLRCFI' . $appYear . 'G' . str_pad($appItem->id, 5, '0', STR_PAD_LEFT);
                            $appType = $appItem->application_type ?? $meta['application_type'] ?? 'Individual';
                            $orgName = $appItem->organization_name ?? $meta['organization_name'] ?? '-';
                            $unitVal = $appItem->unit ?? $meta['unit'] ?? '-';
                            
                            $searchTerms = [
                                $appId,
                                $appType,
                                $appItem->applicant_name ?? '',
                                $orgName,
                                $unitVal,
                                $appItem->place ?? '',
                                $appItem->district ?? $meta['district'] ?? '',
                                $meta['project_type'] ?? $appItem->project_type ?? '',
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

                            <!-- Application Type -->
                            <td>
                                <span style="display: inline-block; padding: 0.2rem 0.55rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700; {{ $appType === 'Group' ? 'background: rgba(168, 85, 247, 0.15); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.3);' : 'background: rgba(255,255,255,0.05); color: var(--text-muted); border: 1px solid var(--panel-border);' }}">
                                    {{ $appType }}
                                </span>
                            </td>

                            <!-- Name -->
                            <td style="font-weight: 600; color: #ffffff;">{{ $appItem->applicant_name }}</td>

                            <!-- Institute / Org & Unit -->
                            <td>
                                @if($appType === 'Group' && $orgName !== '-')
                                    <div style="font-weight: 600; color: #ffffff;">{{ $orgName }}</div>
                                    @if($unitVal !== '-')
                                        <div style="font-size: 0.75rem; color: var(--text-muted);">Unit: {{ $unitVal }}</div>
                                    @endif
                                @else
                                    <span style="color: var(--text-muted); font-style: italic;">-</span>
                                @endif
                            </td>

                            <!-- Place -->
                            <td>{{ $appItem->place ?? 'N/A' }}</td>

                            <!-- District -->
                            <td>{{ $appItem->district ?? $meta['district'] ?? $meta['locality_district'] ?? 'N/A' }}</td>

                            <!-- Project Type -->
                            <td>{{ !empty($meta['project_type']) ? ucwords($meta['project_type']) : (!empty($appItem->application_type) ? ucwords($appItem->application_type) : (!empty($meta['application_type']) ? ucwords($meta['application_type']) : 'N/A')) }}</td>

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
                                <button onclick="openDetailsModal({{ json_encode($appItem) }})" class="btn-custom" style="background: transparent; color: var(--accent-green); border: 1px solid var(--accent-green); padding: 0.4rem; font-size: 1rem; border-radius: 6px; cursor: pointer; transition: all 0.2s; margin-right: 0.5rem; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px;" title="Details"><i class="bx bx-show"></i></button>

                                @if($appItem->status !== 'Approved' && Auth::user()->canApproveApplications())
                                    @if($appItem->status === 'Pending' && !isset($projectsMap[$appItem->id]))
                                        <!-- Approve -->
                                        <form action="{{ route('applications.approve', [$categorySlug, $appItem->id]) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            <button type="submit" class="btn-custom" style="background: transparent; color: var(--accent-green); border: 1px solid var(--accent-green); padding: 0.4rem; font-size: 1rem; border-radius: 6px; cursor: pointer; transition: all 0.2s; margin-right: 0.5rem; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px;" title="Approve">
                                                <i class="bx bx-check"></i>
                                            </button>
                                        </form>

                                        <!-- Reject -->
                                        <form action="{{ route('applications.reject', [$categorySlug, $appItem->id]) }}" method="POST" style="display: inline-block;" onsubmit="confirmApplicationRejection(event, this); return false;">
                                            @csrf
                                            <button type="submit" class="btn-danger-custom" style="padding: 0.4rem; font-size: 1rem; margin-right: 0.5rem; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px;" title="Reject">
                                                <i class="bx bx-x"></i>
                                            </button>
                                        </form>

                                    @endif
                                @endif


                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 2rem;">No general applications registered yet.</td>
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
                <h2 class="panel-title" style="font-size: 1.25rem;">Add General Application</h2>
            </div>

            <form action="{{ route('applications.store') }}" method="POST">
                @csrf
                
                <!-- Hidden Category and redirect tags -->
                <input type="hidden" name="category" value="{{ $categoryName }}">
                <input type="hidden" name="redirect_category" value="{{ $categorySlug }}">

                <!-- Form Section 1: Applicant Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">1. Personal Details of Applicant</h4>
                    
                    <!-- Application Type: Individual or Group -->
                    <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--panel-border); border-radius: 8px; padding: 1rem; margin-bottom: 1.25rem;">
                        <label class="form-label" style="margin-bottom: 0.5rem; display: block; font-weight: 700; color: var(--text-main);">Application Type *</label>
                        <div style="display: flex; gap: 2rem; align-items: center;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; color: #ffffff; cursor: pointer; font-weight: 600;">
                                <input type="radio" name="meta[application_type]" value="Individual" id="add_app_type_individual" onchange="toggleGroupFields('add')" {{ old('meta.application_type', 'Individual') === 'Individual' ? 'checked' : '' }}> Individual
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; color: #ffffff; cursor: pointer; font-weight: 600;">
                                <input type="radio" name="meta[application_type]" value="Group" id="add_app_type_group" onchange="toggleGroupFields('add')" {{ old('meta.application_type') === 'Group' ? 'checked' : '' }}> Group
                            </label>
                        </div>

                        <!-- Group Fields (Institute/Organization & Unit) -->
                        <div id="add_group_fields" style="display: {{ old('meta.application_type') === 'Group' ? 'grid' : 'none' }}; grid-template-columns: 2fr 1fr; gap: 1rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed var(--panel-border);">
                            <div>
                                <label class="form-label" for="organization_name">Institute / Organization Name *</label>
                                <input type="text" class="form-control-dark" id="organization_name" name="meta[organization_name]" value="{{ old('meta.organization_name') }}" placeholder="e.g. Al-Huda Educational Trust">
                            </div>
                            <div>
                                <label class="form-label" for="unit">Unit *</label>
                                <input type="text" class="form-control-dark" id="unit" name="meta[unit]" value="{{ old('meta.unit') }}" placeholder="e.g. Unit 4 / Malappuram">
                            </div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="applicant_name">Name *</label>
                            <input type="text" class="form-control-dark" id="applicant_name" name="applicant_name" value="{{ old('applicant_name') }}" required>
                        </div>

                        <div>
                            <label class="form-label" for="age">Age *</label>
                            <input type="number" class="form-control-dark" id="age" name="meta[age]" value="{{ old('meta.age') }}" placeholder="Enter age" required>
                        </div>
                    </div>

                    <!-- Address & Contact Details -->
                    <div style="margin-bottom: 1rem;">
                        @include('applications.address_form_fields', ['idPrefix' => '', 'app' => null])
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; align-items: center;">
                        <div>
                            <label class="form-label" style="margin-bottom: 0.5rem; display: block;">Sex of Applicant *</label>
                            <div style="display: flex; gap: 1.5rem; align-items: center; margin-top: 0.5rem;">
                                <label style="display: flex; align-items: center; gap: 0.5rem; color: #ffffff; cursor: pointer;">
                                    <input type="radio" name="meta[sex]" value="Male" required {{ old('meta.sex') === 'Male' ? 'checked' : '' }}> Male
                                </label>
                                <label style="display: flex; align-items: center; gap: 0.5rem; color: #ffffff; cursor: pointer;">
                                    <input type="radio" name="meta[sex]" value="Female" required {{ old('meta.sex') === 'Female' ? 'checked' : '' }}> Female
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="form-label" for="status_of_applicant">Status of Applicant *</label>
                            <select class="form-select-dark" id="status_of_applicant" name="meta[status_of_applicant]" required>
                                <option value="With family">With family</option>
                                <option value="Widow">Widow</option>
                                <option value="Single">Single</option>
                                <option value="Orphan">Orphan</option>
                                <option value="Chronic deceased">Chronic deceased</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="education">Education Level of the Applicant *</label>
                        <input type="text" class="form-control-dark" id="education" name="meta[education]" value="{{ old('meta.education') }}" required>
                    </div>
                </div>

                <!-- Form Section 2: Family & Economic Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">2. Family & Economic Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="num_male_family">Male Family Members *</label>
                            <input type="number" class="form-control-dark" id="num_male_family" name="meta[num_male_family]" value="{{ old('meta.num_male_family') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="num_female_family">Female Family Members *</label>
                            <input type="number" class="form-control-dark" id="num_female_family" name="meta[num_female_family]" value="{{ old('meta.num_female_family') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="num_total_family">Total Family Members *</label>
                            <input type="number" class="form-control-dark" id="num_total_family" name="meta[num_total_family]" value="{{ old('meta.num_total_family') }}" readonly required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="num_earning_members">No. of Earning Members *</label>
                            <input type="number" class="form-control-dark" id="num_earning_members" name="meta[num_earning_members]" value="{{ old('meta.num_earning_members') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="average_monthly_income">Average Monthly Income (₹) *</label>
                            <input type="number" class="form-control-dark" id="average_monthly_income" name="meta[average_monthly_income]" value="{{ old('meta.average_monthly_income') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="applying_for">Applying for *</label>
                            <input type="text" class="form-control-dark" id="applying_for" name="meta[applying_for]" value="{{ old('meta.applying_for') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="monthly_income_detail">Monthly Income (₹) *</label>
                            <input type="number" class="form-control-dark" id="monthly_income_detail" name="meta[monthly_income_detail]" value="{{ old('meta.monthly_income_detail') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="recommended_by">Recommended by *</label>
                            <input type="text" class="form-control-dark" id="recommended_by" name="meta[recommended_by]" value="{{ old('meta.recommended_by') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="recommended_phone">Phone Number *</label>
                            <input type="text" class="form-control-dark" id="recommended_phone" name="meta[recommended_phone]" value="{{ old('meta.recommended_phone') }}" required>
                        </div>
                    </div>
                </div>

                <!-- Form Section 3: Office Use -->
                <div style="margin-bottom: 2rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">3. For Office Use Only</h4>
                    
                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="office_application_type">Select Type Of Application *</label>
                        <select class="form-select-dark" id="office_application_type" name="meta[office_application_type]" required>
                            <option value="">Select Type Of Application</option>
                            <option value="WheelChair">WheelChair</option>
                            <option value="Tailoring Machine">Tailoring Machine</option>
                            <option value="Spects">Spects</option>
                            <option value="Hearing Aid">Hearing Aid</option>
                            <option value="Family Aid">Family Aid</option>
                            <option value="Support For Differently Abled">Support For Differently Abled</option>
                            <option value="Eye Surgery">Eye Surgery</option>
                            <option value="Medical Aid">Medical Aid</option>
                            <option value="Marriage Aid">Marriage Aid</option>
                            <option value="House">House</option>
                            <option value="Cycle">Cycle</option>
                            <option value="House Infrastructure">House Infrastructure</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="details">Additional Notes</label>
                        <textarea class="form-control-dark" id="details" name="details" style="height: 60px; resize: vertical;">{{ old('details') }}</textarea>
                    </div>

                    <input type="hidden" name="status" value="Pending">
                    <input type="hidden" name="amount_requested" value="0">
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
                <h2 class="panel-title" style="font-size: 1.25rem;">Edit General Application</h2>
            </div>

            <form id="editAppForm" action="" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Hidden Category and redirect tags -->
                <input type="hidden" name="category" value="{{ $categoryName }}">
                <input type="hidden" name="redirect_category" value="{{ $categorySlug }}">
                <input type="hidden" name="amount_requested" value="0">

                <!-- Form Section 1: Applicant Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">1. Personal Details of Applicant</h4>
                    
                    <!-- Application Type: Individual or Group -->
                    <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--panel-border); border-radius: 8px; padding: 1rem; margin-bottom: 1.25rem;">
                        <label class="form-label" style="margin-bottom: 0.5rem; display: block; font-weight: 700; color: var(--text-main);">Application Type *</label>
                        <div style="display: flex; gap: 2rem; align-items: center;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; color: #ffffff; cursor: pointer; font-weight: 600;">
                                <input type="radio" name="meta[application_type]" value="Individual" id="edit_app_type_individual" onchange="toggleGroupFields('edit')"> Individual
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; color: #ffffff; cursor: pointer; font-weight: 600;">
                                <input type="radio" name="meta[application_type]" value="Group" id="edit_app_type_group" onchange="toggleGroupFields('edit')"> Group
                            </label>
                        </div>

                        <!-- Group Fields (Institute/Organization & Unit) -->
                        <div id="edit_group_fields" style="display: none; grid-template-columns: 2fr 1fr; gap: 1rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed var(--panel-border);">
                            <div>
                                <label class="form-label" for="edit_organization_name">Institute / Organization Name *</label>
                                <input type="text" class="form-control-dark" id="edit_organization_name" name="meta[organization_name]" placeholder="e.g. Al-Huda Educational Trust">
                            </div>
                            <div>
                                <label class="form-label" for="edit_unit">Unit *</label>
                                <input type="text" class="form-control-dark" id="edit_unit" name="meta[unit]" placeholder="e.g. Unit 4 / Malappuram">
                            </div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_applicant_name">Name *</label>
                            <input type="text" class="form-control-dark" id="edit_applicant_name" name="applicant_name" required>
                        </div>

                        <div>
                            <label class="form-label" for="edit_age">Age *</label>
                            <input type="number" class="form-control-dark" id="edit_age" name="meta[age]" placeholder="Enter age" required>
                        </div>
                    </div>

                    <!-- Address & Contact Details -->
                    <div style="margin-bottom: 1rem;">
                        @include('applications.address_form_fields', ['idPrefix' => 'edit_', 'app' => null])
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; align-items: center;">
                        <div>
                            <label class="form-label" style="margin-bottom: 0.5rem; display: block;">Sex of Applicant *</label>
                            <div style="display: flex; gap: 1.5rem; align-items: center; margin-top: 0.5rem;">
                                <label style="display: flex; align-items: center; gap: 0.5rem; color: #ffffff; cursor: pointer;">
                                    <input type="radio" id="edit_sex_male" name="meta[sex]" value="Male" required> Male
                                </label>
                                <label style="display: flex; align-items: center; gap: 0.5rem; color: #ffffff; cursor: pointer;">
                                    <input type="radio" id="edit_sex_female" name="meta[sex]" value="Female" required> Female
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="form-label" for="edit_status_of_applicant">Status of Applicant *</label>
                            <select class="form-select-dark" id="edit_status_of_applicant" name="meta[status_of_applicant]" required>
                                <option value="With family">With family</option>
                                <option value="Widow">Widow</option>
                                <option value="Single">Single</option>
                                <option value="Orphan">Orphan</option>
                                <option value="Chronic deceased">Chronic deceased</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="edit_education">Education Level of the Applicant *</label>
                        <input type="text" class="form-control-dark" id="edit_education" name="meta[education]" required>
                    </div>
                </div>

                <!-- Form Section 2: Family & Economic Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">2. Family & Economic Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_num_male_family">Male Family Members *</label>
                            <input type="number" class="form-control-dark" id="edit_num_male_family" name="meta[num_male_family]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_num_female_family">Female Family Members *</label>
                            <input type="number" class="form-control-dark" id="edit_num_female_family" name="meta[num_female_family]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_num_total_family">Total Family Members *</label>
                            <input type="number" class="form-control-dark" id="edit_num_total_family" name="meta[num_total_family]" readonly required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_num_earning_members">No. of Earning Members *</label>
                            <input type="number" class="form-control-dark" id="edit_num_earning_members" name="meta[num_earning_members]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_average_monthly_income">Average Monthly Income (₹) *</label>
                            <input type="number" class="form-control-dark" id="edit_average_monthly_income" name="meta[average_monthly_income]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_applying_for">Applying for *</label>
                            <input type="text" class="form-control-dark" id="edit_applying_for" name="meta[applying_for]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_monthly_income_detail">Monthly Income (₹) *</label>
                            <input type="number" class="form-control-dark" id="edit_monthly_income_detail" name="meta[monthly_income_detail]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="edit_recommended_by">Recommended by *</label>
                            <input type="text" class="form-control-dark" id="edit_recommended_by" name="meta[recommended_by]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_recommended_phone">Phone Number *</label>
                            <input type="text" class="form-control-dark" id="edit_recommended_phone" name="meta[recommended_phone]" required>
                        </div>
                    </div>
                </div>

                <!-- Form Section 3: Office Use -->
                <div style="margin-bottom: 2rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">3. For Office Use Only</h4>
                    
                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="edit_office_application_type">Select Type Of Application *</label>
                        <select class="form-select-dark" id="edit_office_application_type" name="meta[office_application_type]" required>
                            <option value="">Select Type Of Application</option>
                            <option value="WheelChair">WheelChair</option>
                            <option value="Tailoring Machine">Tailoring Machine</option>
                            <option value="Spects">Spects</option>
                            <option value="Hearing Aid">Hearing Aid</option>
                            <option value="Family Aid">Family Aid</option>
                            <option value="Support For Differently Abled">Support For Differently Abled</option>
                            <option value="Eye Surgery">Eye Surgery</option>
                            <option value="Medical Aid">Medical Aid</option>
                            <option value="Marriage Aid">Marriage Aid</option>
                            <option value="House">House</option>
                            <option value="Cycle">Cycle</option>
                            <option value="House Infrastructure">House Infrastructure</option>
                        </select>
                    </div>

                    <input type="hidden" name="status" id="edit_status">

                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="edit_details">Additional Notes</label>
                        <textarea class="form-control-dark" id="edit_details" name="details" style="height: 60px; resize: vertical;"></textarea>
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

    <!-- Modal Scripts -->
    <script>
        var projectsMap = @json($projectsMap ?? []); window.projectsMap = projectsMap;

        function toggleGroupFields(mode) {
            const isGroup = mode === 'add' 
                ? (document.getElementById('add_app_type_group') && document.getElementById('add_app_type_group').checked)
                : (document.getElementById('edit_app_type_group') && document.getElementById('edit_app_type_group').checked);

            const fieldsDiv = document.getElementById(mode + '_group_fields');
            const orgInput = document.getElementById(mode === 'add' ? 'organization_name' : 'edit_organization_name');
            const unitInput = document.getElementById(mode === 'add' ? 'unit' : 'edit_unit');

            if (fieldsDiv) {
                fieldsDiv.style.display = isGroup ? 'grid' : 'none';
            }
            if (orgInput) {
                orgInput.required = isGroup;
            }
            if (unitInput) {
                unitInput.required = isGroup;
            }

            // Target fields: Age, Sex of Applicant, Status of Applicant, Education Level, Average Monthly Income, Monthly Income
            const prefix = mode === 'add' ? '' : 'edit_';

            const ageInput = document.getElementById(prefix + 'age');
            const statusSelect = document.getElementById(prefix + 'status_of_applicant');
            const eduInput = document.getElementById(prefix + 'education');
            const avgIncomeInput = document.getElementById(prefix + 'average_monthly_income');
            const monthIncomeInput = document.getElementById(prefix + 'monthly_income_detail');

            let sexRadios = [];
            if (mode === 'add') {
                sexRadios = Array.from(document.querySelectorAll('input[name="meta[sex]"]'));
            } else {
                const male = document.getElementById('edit_sex_male');
                const female = document.getElementById('edit_sex_female');
                if (male) sexRadios.push(male);
                if (female) sexRadios.push(female);
            }

            const textInputs = [ageInput, eduInput, avgIncomeInput, monthIncomeInput];
            textInputs.forEach(input => {
                if (input) {
                    input.readOnly = isGroup;
                    input.required = !isGroup;
                    if (isGroup) {
                        input.style.backgroundColor = 'rgba(255, 255, 255, 0.03)';
                        input.style.cursor = 'not-allowed';
                        input.style.opacity = '0.6';
                    } else {
                        input.style.backgroundColor = '';
                        input.style.cursor = '';
                        input.style.opacity = '1';
                    }
                }
            });

            if (statusSelect) {
                statusSelect.disabled = isGroup;
                statusSelect.required = !isGroup;
                if (isGroup) {
                    statusSelect.style.backgroundColor = 'rgba(255, 255, 255, 0.03)';
                    statusSelect.style.cursor = 'not-allowed';
                    statusSelect.style.opacity = '0.6';
                } else {
                    statusSelect.style.backgroundColor = '';
                    statusSelect.style.cursor = '';
                    statusSelect.style.opacity = '1';
                }
            }

            sexRadios.forEach(radio => {
                if (radio) {
                    radio.disabled = isGroup;
                    radio.required = !isGroup;
                    if (radio.parentElement) {
                        radio.parentElement.style.cursor = isGroup ? 'not-allowed' : 'pointer';
                        radio.parentElement.style.opacity = isGroup ? '0.6' : '1';
                    }
                }
            });
        }

        // Add Application Modal Toggle
        function openModal() {
            document.getElementById('addAppModal').style.display = 'flex';
            toggleGroupFields('add');
        }

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
            
            // Radio mapping
            if (document.getElementById('edit_age')) document.getElementById('edit_age').value = meta.age || '';
            if (meta.sex === 'Male') {
                document.getElementById('edit_sex_male').checked = true;
            } else if (meta.sex === 'Female') {
                document.getElementById('edit_sex_female').checked = true;
            }
            
            document.getElementById('edit_status_of_applicant').value = meta.status_of_applicant || 'With family';
            document.getElementById('edit_education').value = meta.education || '';
            
            document.getElementById('edit_num_male_family').value = meta.num_male_family || '';
            document.getElementById('edit_num_female_family').value = meta.num_female_family || '';
            document.getElementById('edit_num_total_family').value = meta.num_total_family || '';
            document.getElementById('edit_num_earning_members').value = meta.num_earning_members || '';
            document.getElementById('edit_average_monthly_income').value = meta.average_monthly_income || '';
            document.getElementById('edit_applying_for').value = meta.applying_for || '';
            document.getElementById('edit_monthly_income_detail').value = meta.monthly_income_detail || '';
            document.getElementById('edit_recommended_by').value = meta.recommended_by || '';
            document.getElementById('edit_recommended_phone').value = meta.recommended_phone || '';
            document.getElementById('edit_office_application_type').value = meta.office_application_type || '';

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
                const approveUrl = `{{ url('admin/applications') }}/{{ $categorySlug }}/${appItem.id}/approve`;
                const rejectUrl = `{{ url('admin/applications') }}/{{ $categorySlug }}/${appItem.id}/reject`;
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                if (appItem.status === 'Pending') {
                    statusHtml = `
                        <form action="${approveUrl}" method="POST" style="display: inline-block;">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <button type="submit" class="btn-custom" style="background: linear-gradient(135deg, #2ecc71, #27ae60); padding: 0.6rem 1.5rem; display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                                <i class="bx bx-check"></i> Approve Application
                            </button>
                        </form>
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
                        <form action="${approveUrl}" method="POST" style="display: inline-block;">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <button type="submit" class="btn-custom" style="background: linear-gradient(135deg, #2ecc71, #27ae60); padding: 0.6rem 1.5rem; display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                                <i class="bx bx-check"></i> Approve Application
                            </button>
                        </form>
                    `;
                }
                statusActionsContainer.innerHTML = statusHtml;
            }
            const meta = appItem.meta || {};
            const addr = appItem.address || {};
            const houseName = meta.house_name || addr.house_name || appItem.house_name || '';
            const placeName = meta.place || addr.place || appItem.place || meta.address || '';
            const villageName = meta.village || addr.village || appItem.village || '';
            const postOffice = meta.post_office || meta.post || addr.post_office || appItem.post_office || '';
            const panchayatName = meta.panchayat || addr.panchayat || appItem.panchayat || meta.panchayat_municipality_corporation || '';
            const districtName = meta.district || addr.district || appItem.district || '';
            const stateName = meta.state || addr.state || appItem.state || '';
            const pinCode = meta.pin_code || meta.pincode || meta.pin || addr.pin_code || appItem.pin_code || '';
            const mob1 = meta.mobile_1 || meta.mobile || meta.contact_number_1 || addr.contact_number_1 || addr.mobile_1 || appItem.mobile_1 || '';
            const mob2 = meta.mobile_2 || meta.contact_number_2 || addr.contact_number_2 || addr.mobile_2 || appItem.mobile_2 || '';

            const formatVal = (val) => val ? val : '<span style="color: var(--text-muted); font-style: italic;">N/A</span>';
            const appTypeVal = meta.application_type || appItem.application_type || 'Individual';
            const orgNameVal = meta.organization_name || appItem.organization_name || '';
            const unitVal = meta.unit || appItem.unit || '';
            
            let html = `
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <!-- Col 1 -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">1. Personal Details of Applicant</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px;">Application Type:</td><td><span style="display: inline-block; padding: 0.15rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700; ${appTypeVal === 'Group' ? 'background: rgba(168, 85, 247, 0.15); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.3);' : 'background: rgba(255,255,255,0.05); color: var(--text-muted); border: 1px solid var(--panel-border);'}">${appTypeVal}</span></td></tr>
                            ${appTypeVal === 'Group' ? `
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Institute/Org Name:</td><td style="font-weight: 600; color: #ffffff;">${formatVal(orgNameVal)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Unit:</td><td>${formatVal(unitVal)}</td></tr>
                            ` : ''}
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px;">Applicant Name:</td><td>${formatVal(appItem.applicant_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Age / Sex:</td><td>${formatVal(meta.age)} yrs / ${formatVal(meta.sex)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">House Name / Place:</td><td>${formatVal(houseName)} / ${formatVal(placeName)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Village / P.O.:</td><td>${formatVal(villageName)} / ${formatVal(postOffice)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Panchayath / District:</td><td>${formatVal(panchayatName)} / ${formatVal(districtName)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">State / Pin Code:</td><td>${formatVal(stateName)} / ${formatVal(pinCode)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Mobile 1 / Mobile 2:</td><td>${formatVal(mob1)} / ${formatVal(mob2)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Status of Applicant:</td><td>${formatVal(meta.status_of_applicant)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Education Level:</td><td>${formatVal(meta.education)}</td></tr>
                        </table>
                    </div>

                    <!-- Col 2 -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">2. Family & Economic Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px;">Male / Female Members:</td><td>M: ${formatVal(meta.num_male_family)} / F: ${formatVal(meta.num_female_family)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Total Family Members:</td><td style="font-weight: 600; color: #ffffff;">${formatVal(meta.num_total_family)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">No. of Earning Members:</td><td>${formatVal(meta.num_earning_members)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Average Monthly Income:</td><td>${meta.average_monthly_income ? '₹' + Number(meta.average_monthly_income).toLocaleString() : 'N/A'}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Applying for:</td><td style="font-weight: 600; color: #ffffff;">${formatVal(meta.applying_for)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Monthly Income:</td><td>${meta.monthly_income_detail ? '₹' + Number(meta.monthly_income_detail).toLocaleString() : 'N/A'}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Recommended by:</td><td>${formatVal(meta.recommended_by)} <span style="font-size: 0.8rem; color: var(--text-muted);">(${formatVal(meta.recommended_phone)})</span></td></tr>
                        </table>

                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">3. For Office Use Only</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px;">Office Application Type:</td><td style="font-weight: 600; color: var(--accent-cyan);">${formatVal(meta.office_application_type)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Review Status:</td><td style="font-weight: 600; color: #ffffff;">${appItem.status}</td></tr>
                        </table>
                    </div>
                </div>

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

        // Realtime calculation of family members count
        document.addEventListener("DOMContentLoaded", function() {
            // Add Modal
            const maleInput = document.getElementById('num_male_family');
            const femaleInput = document.getElementById('num_female_family');
            const totalInput = document.getElementById('num_total_family');

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
            const editMaleInput = document.getElementById('edit_num_male_family');
            const editFemaleInput = document.getElementById('edit_num_female_family');
            const editTotalInput = document.getElementById('edit_num_total_family');

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
        window.toggleOrgOther = toggleOrgOther;
    </script>

@endsection
