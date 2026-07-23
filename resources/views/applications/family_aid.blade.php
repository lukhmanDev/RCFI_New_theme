@extends('layouts.admin')

@section('title', 'Family Aid Applications')

@section('content')

    <!-- Back Button Header -->
    <div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
        <a href="{{ route('applications.index') }}" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); padding: 0.5rem 1rem;">
            <i class="bx bx-left-arrow-alt"></i> Back to Dashboard
        </a>
        <h3 style="color: #ffffff; font-size: 1.25rem; font-weight: 600;">Family Aid Applications Registry</h3>
    </div>

    <!-- Success & Error Alert Panels -->
    @if (session('success'))
        <div class=\"alert alert-success\" style="background-color: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent-green); color: #8cf5c6; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500;">
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
            <h2 class="panel-title">Family Aid Applications List</h2>
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
                        <th>Name</th>
                        <th>Age</th>
                        <th>Place</th>
                        <th>Panchayath</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $appItem)
                        @php
                            $meta = $appItem->meta ?? [];
                            $appYear = !empty($appItem->created_at) ? date('y', strtotime($appItem->created_at)) : '24';
                            $appId = 'APLRCFI' . $appYear . 'FA' . str_pad($appItem->id, 5, '0', STR_PAD_LEFT);
                            
                            $searchTerms = [
                                $appId,
                                $appItem->applicant_name ?? '',
                                $appItem->place ?? '',
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

                            <!-- Age -->
                            <td>{{ $meta['age'] ?? 'N/A' }}</td>

                            <!-- Place -->
                            <td>{{ $appItem->place ?? 'N/A' }}</td>

                            <!-- Panchayath -->
                            <td>{{ $appItem->panchayat ?? $appItem->panchayath ?? 'N/A' }}</td>

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
                                    @if($appItem->status === 'Pending')
                                        <!-- Approve -->
                                        <button type="button" onclick="openApproveModal({{ $appItem->id }}, '{{ $appItem->cluster_id }}', '{{ $appItem->agency_number }}')" class="btn-custom" style="background: transparent; color: var(--accent-green); border: 1px solid var(--accent-green); padding: 0.4rem; font-size: 1rem; border-radius: 6px; cursor: pointer; transition: all 0.2s; margin-right: 0.5rem; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px;" title="Approve">
                                            <i class="bx bx-check"></i>
                                        </button>

                                        <!-- Reject -->
                                        <form action="{{ route('applications.reject', [$categorySlug, $appItem->id]) }}" method="POST" style="display: inline-block;" onsubmit="confirmApplicationRejection(event, this); return false;">
                                            @csrf
                                            <button type="submit" class="btn-danger-custom" style="padding: 0.4rem; font-size: 1rem; margin-right: 0.5rem; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px;" title="Reject">
                                                <i class="bx bx-x"></i>
                                            </button>
                                        </form>

                                    @endif
                                @endif

                                @if(Auth::user()->isSuperAdmin() || ($appItem->status === 'Pending' && Auth::user()->hasAdminAccess()))
                                    <form action="{{ route('applications.destroy', $appItem->id) }}" method="POST" style="display: inline-block;" onsubmit="confirmApplicationDeletion(event, this); return false;">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="redirect_category" value="{{ $categorySlug }}">
                                        <button type="submit" class="btn-danger-custom" style="padding: 0.4rem; font-size: 1rem; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px;" title="Delete">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem;">No family aid applications registered yet.</td>
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
                    <button onclick="editFromDetails()" class="btn-custom" style="background: transparent; color: var(--accent-cyan); border: 1px solid var(--accent-cyan); padding: 0.6rem 1.5rem;">
                        <i class="bx bx-pencil"></i> Edit
                    </button>
                    <button onclick="deleteFromDetails()" class="btn-danger-custom" style="padding: 0.6rem 1.5rem;">
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
                <h2 class="panel-title" style="font-size: 1.25rem;">Add Family Aid Application</h2>
            </div>

            <form action="{{ route('applications.store') }}" method="POST">
                @csrf
                
                <!-- Hidden Category and redirect tags -->
                <input type="hidden" name="category" value="{{ $categoryName }}">
                <input type="hidden" name="redirect_category" value="{{ $categorySlug }}">
                <input type="hidden" name="amount_requested" value="0">

                <!-- Form Section 1: Personal Details of Applicant -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">1. Personal Details of Applicant</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="applicant_name">Name *</label>
                            <input type="text" class="form-control-dark" id="applicant_name" name="applicant_name" value="{{ old('applicant_name') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="father_name">Father's Name *</label>
                            <input type="text" class="form-control-dark" id="father_name" name="meta[father_name]" value="{{ old('meta.father_name') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="mother_name">Mother's Name *</label>
                            <input type="text" class="form-control-dark" id="mother_name" name="meta[mother_name]" value="{{ old('meta.mother_name') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="fathers_father">Father's Father</label>
                            <input type="text" class="form-control-dark" id="fathers_father" name="meta[fathers_father]" value="{{ old('meta.fathers_father') }}">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 2fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="dob">Date of Birth *</label>
                            <input type="date" class="form-control-dark" id="dob" name="meta[dob]" value="{{ old('meta.dob') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="age">Age *</label>
                            <input type="number" class="form-control-dark" id="age" name="meta[age]" value="{{ old('meta.age') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="aadhar_number">Aadhaar Number *</label>
                            <input type="text" class="form-control-dark" id="aadhar_number" name="meta[aadhar_number]" value="{{ old('meta.aadhar_number') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="house_name">House Name *</label>
                            <input type="text" class="form-control-dark" id="house_name" name="meta[house_name]" value="{{ old('meta.house_name') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="place">Place *</label>
                            <input type="text" class="form-control-dark" id="place" name="meta[place]" value="{{ old('meta.place') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="village">Village *</label>
                            <input type="text" class="form-control-dark" id="village" name="meta[village]" value="{{ old('meta.village') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="post_office">P.O. *</label>
                            <input type="text" class="form-control-dark" id="post_office" name="meta[post_office]" value="{{ old('meta.post_office') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="panchayat">Panchayath *</label>
                            <input type="text" class="form-control-dark" id="panchayat" name="meta[panchayat]" value="{{ old('meta.panchayat') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="district">District *</label>
                            <input type="text" class="form-control-dark" id="district" name="meta[district]" value="{{ old('meta.district') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="state">State *</label>
                            <input type="text" class="form-control-dark" id="state" name="meta[state]" value="{{ old('meta.state') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="pin_code">Pin Code *</label>
                            <input type="text" class="form-control-dark" id="pin_code" name="meta[pin_code]" value="{{ old('meta.pin_code') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="mobile_1">Mobile 1 *</label>
                            <input type="text" class="form-control-dark" id="mobile_1" name="meta[mobile_1]" value="{{ old('meta.mobile_1') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="mobile_2">Mobile 2</label>
                            <input type="text" class="form-control-dark" id="mobile_2" name="meta[mobile_2]" value="{{ old('meta.mobile_2') }}">
                        </div>
                    </div>
                </div>

                <!-- Form Section 2: Family & Income Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">2. Family & Income Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1.8fr 1fr 1fr 1.2fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="children_total">Number of children in the family *</label>
                            <input type="number" class="form-control-dark" id="children_total" name="meta[children_total]" readonly value="{{ old('meta.children_total') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="children_male">Male *</label>
                            <input type="number" class="form-control-dark" id="children_male" name="meta[children_male]" value="{{ old('meta.children_male') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="children_female">Female *</label>
                            <input type="number" class="form-control-dark" id="children_female" name="meta[children_female]" value="{{ old('meta.children_female') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="nri_status">NRI *</label>
                            <select class="form-select-dark" id="nri_status" name="meta[nri_status]" required>
                                <option value="No" {{ old('meta.nri_status') === 'No' ? 'selected' : '' }}>No</option>
                                <option value="Yes" {{ old('meta.nri_status') === 'Yes' ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="occupation">Occupation *</label>
                            <input type="text" class="form-control-dark" id="occupation" name="meta[occupation]" value="{{ old('meta.occupation') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="monthly_income">Monthly Income (₹) *</label>
                            <input type="number" class="form-control-dark" id="monthly_income" name="meta[monthly_income]" value="{{ old('meta.monthly_income') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1.5fr 1.5fr 1fr; gap: 1rem; align-items: center;">
                        <div>
                            <label class="form-label" for="other_income_sources">Other sources of income</label>
                            <input type="text" class="form-control-dark" id="other_income_sources" name="meta[other_income_sources]" placeholder="None or list sources" value="{{ old('meta.other_income_sources') }}">
                        </div>
                        <div>
                            <label class="form-label" for="health_status">Health Status *</label>
                            <input type="text" class="form-control-dark" id="health_status" name="meta[health_status]" value="{{ old('meta.health_status') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="disability_status">Disability *</label>
                            <select class="form-select-dark" id="disability_status" name="meta[disability_status]" required>
                                <option value="No" {{ old('meta.disability_status') === 'No' ? 'selected' : '' }}>No</option>
                                <option value="Yes" {{ old('meta.disability_status') === 'Yes' ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Form Section 3: Health & Welfare Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">3. Health & Welfare Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="routine_treatment_explanation">Explanation if routine treatment is required</label>
                            <textarea class="form-control-dark" id="routine_treatment_explanation" name="meta[routine_treatment_explanation]" style="height: 50px;" placeholder="Details if Daily/Routine treatment is needed">{{ old('meta.routine_treatment_explanation') }}</textarea>
                        </div>
                        <div>
                            <label class="form-label" for="chronic_patients_description">Description of chronic patients in the house</label>
                            <textarea class="form-control-dark" id="chronic_patients_description" name="meta[chronic_patients_description]" style="height: 50px;" placeholder="Details of other chronic patients if any">{{ old('meta.chronic_patients_description') }}</textarea>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; align-items: center;">
                        <div>
                            <label class="form-label" for="residence_info">Residence Information *</label>
                            <select class="form-select-dark" id="residence_info" name="meta[residence_info]" required>
                                <option value="Own House" {{ old('meta.residence_info') === 'Own House' ? 'selected' : '' }}>Own House</option>
                                <option value="Homestead" {{ old('meta.residence_info') === 'Homestead' ? 'selected' : '' }}>Homestead</option>
                                <option value="Rented House" {{ old('meta.residence_info') === 'Rented House' ? 'selected' : '' }}>Rented House</option>
                                <option value="Others" {{ old('meta.residence_info') === 'Others' ? 'selected' : '' }}>Others</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="own_house_condition">If own house, describe present condition</label>
                            <input type="text" class="form-control-dark" id="own_house_condition" name="meta[own_house_condition]" placeholder="e.g. Good, Dilapidated, etc" value="{{ old('meta.own_house_condition') }}">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem; align-items: center;">
                        <div>
                            <label class="form-label" for="own_place_status">Own place *</label>
                            <select class="form-select-dark" id="own_place_status" name="meta[own_place_status]" required>
                                <option value="No" {{ old('meta.own_place_status') === 'No' ? 'selected' : '' }}>No</option>
                                <option value="Yes" {{ old('meta.own_place_status') === 'Yes' ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="own_place_size">If so, how much?</label>
                            <input type="text" class="form-control-dark" id="own_place_size" name="meta[own_place_size]" placeholder="e.g. Sqft, acres or N/A" value="{{ old('meta.own_place_size') }}">
                        </div>
                        <div>
                            <label class="form-label" for="sequel_status">Is there a sequel *</label>
                            <select class="form-select-dark" id="sequel_status" name="meta[sequel_status]" required>
                                <option value="No" {{ old('meta.sequel_status') === 'No' ? 'selected' : '' }}>No</option>
                                <option value="Yes" {{ old('meta.sequel_status') === 'Yes' ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="welfare_assistance_areas">Areas of welfare needing assistance</label>
                        <textarea class="form-control-dark" id="welfare_assistance_areas" name="meta[welfare_assistance_areas]" style="height: 50px;" placeholder="e.g. Housing aid, medical expense aid, etc">{{ old('meta.welfare_assistance_areas') }}</textarea>
                    </div>
                </div>

                <!-- Form Section 4: Submission Details -->
                <div style="margin-bottom: 2rem;">
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

                <!-- Submit Button -->
                <button type="submit" class="btn-custom" style="width: 100%; padding: 0.75rem;">
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
                <h2 class="panel-title" style="font-size: 1.25rem;">Edit Family Aid Application</h2>
            </div>

            <form id="editAppForm" action="" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Hidden Category and redirect tags -->
                <input type="hidden" name="category" value="{{ $categoryName }}">
                <input type="hidden" name="redirect_category" value="{{ $categorySlug }}">
                <input type="hidden" name="amount_requested" value="0">

                <!-- Form Section 1: Personal Details of Applicant -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">1. Personal Details of Applicant</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_applicant_name">Name *</label>
                            <input type="text" class="form-control-dark" id="edit_applicant_name" name="applicant_name" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_father_name">Father's Name *</label>
                            <input type="text" class="form-control-dark" id="edit_father_name" name="meta[father_name]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_mother_name">Mother's Name *</label>
                            <input type="text" class="form-control-dark" id="edit_mother_name" name="meta[mother_name]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_fathers_father">Father's Father</label>
                            <input type="text" class="form-control-dark" id="edit_fathers_father" name="meta[fathers_father]">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 2fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_dob">Date of Birth *</label>
                            <input type="date" class="form-control-dark" id="edit_dob" name="meta[dob]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_age">Age *</label>
                            <input type="number" class="form-control-dark" id="edit_age" name="meta[age]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_aadhar_number">Aadhaar Number *</label>
                            <input type="text" class="form-control-dark" id="edit_aadhar_number" name="meta[aadhar_number]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_house_name">House Name *</label>
                            <input type="text" class="form-control-dark" id="edit_house_name" name="meta[house_name]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_place">Place *</label>
                            <input type="text" class="form-control-dark" id="edit_place" name="meta[place]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1.2fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_post_office">P.O. *</label>
                            <input type="text" class="form-control-dark" id="edit_post_office" name="meta[post_office]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_panchayat">Panchayat *</label>
                            <input type="text" class="form-control-dark" id="edit_panchayat" name="meta[panchayat]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_district">District *</label>
                            <input type="text" class="form-control-dark" id="edit_district" name="meta[district]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1.2fr 2fr 2fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_pin_code">Pin Code *</label>
                            <input type="text" class="form-control-dark" id="edit_pin_code" name="meta[pin_code]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_mobile_1">Mobile 1 *</label>
                            <input type="text" class="form-control-dark" id="edit_mobile_1" name="meta[mobile_1]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_mobile_2">Mobile 2</label>
                            <input type="text" class="form-control-dark" id="edit_mobile_2" name="meta[mobile_2]">
                        </div>
                    </div>
                </div>

                <!-- Form Section 2: Family & Income Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">2. Family & Income Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1.8fr 1fr 1fr 1.2fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_children_total">Number of children in the family *</label>
                            <input type="number" class="form-control-dark" id="edit_children_total" name="meta[children_total]" readonly required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_children_male">Male *</label>
                            <input type="number" class="form-control-dark" id="edit_children_male" name="meta[children_male]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_children_female">Female *</label>
                            <input type="number" class="form-control-dark" id="edit_children_female" name="meta[children_female]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_nri_status">NRI *</label>
                            <select class="form-select-dark" id="edit_nri_status" name="meta[nri_status]" required>
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_occupation">Occupation *</label>
                            <input type="text" class="form-control-dark" id="edit_occupation" name="meta[occupation]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_monthly_income">Monthly Income (₹) *</label>
                            <input type="number" class="form-control-dark" id="edit_monthly_income" name="meta[monthly_income]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1.5fr 1.5fr 1fr; gap: 1rem; align-items: center;">
                        <div>
                            <label class="form-label" for="edit_other_income_sources">Other sources of income</label>
                            <input type="text" class="form-control-dark" id="edit_other_income_sources" name="meta[other_income_sources]">
                        </div>
                        <div>
                            <label class="form-label" for="edit_health_status">Health Status *</label>
                            <input type="text" class="form-control-dark" id="edit_health_status" name="meta[health_status]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_disability_status">Disability *</label>
                            <select class="form-select-dark" id="edit_disability_status" name="meta[disability_status]" required>
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Form Section 3: Health & Welfare Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">3. Health & Welfare Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_routine_treatment_explanation">Explanation if routine treatment is required</label>
                            <textarea class="form-control-dark" id="edit_routine_treatment_explanation" name="meta[routine_treatment_explanation]" style="height: 50px;"></textarea>
                        </div>
                        <div>
                            <label class="form-label" for="edit_chronic_patients_description">Description of chronic patients in the house</label>
                            <textarea class="form-control-dark" id="edit_chronic_patients_description" name="meta[chronic_patients_description]" style="height: 50px;"></textarea>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; align-items: center;">
                        <div>
                            <label class="form-label" for="edit_residence_info">Residence Information *</label>
                            <select class="form-select-dark" id="edit_residence_info" name="meta[residence_info]" required>
                                <option value="Own House">Own House</option>
                                <option value="Homestead">Homestead</option>
                                <option value="Rented House">Rented House</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="edit_own_house_condition">If own house, describe present condition</label>
                            <input type="text" class="form-control-dark" id="edit_own_house_condition" name="meta[own_house_condition]">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem; align-items: center;">
                        <div>
                            <label class="form-label" for="edit_own_place_status">Own place *</label>
                            <select class="form-select-dark" id="edit_own_place_status" name="meta[own_place_status]" required>
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="edit_own_place_size">If so, how much?</label>
                            <input type="text" class="form-control-dark" id="edit_own_place_size" name="meta[own_place_size]">
                        </div>
                        <div>
                            <label class="form-label" for="edit_sequel_status">Is there a sequel *</label>
                            <select class="form-select-dark" id="edit_sequel_status" name="meta[sequel_status]" required>
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="edit_welfare_assistance_areas">Areas of welfare needing assistance</label>
                        <textarea class="form-control-dark" id="edit_welfare_assistance_areas" name="meta[welfare_assistance_areas]" style="height: 50px;"></textarea>
                    </div>
                </div>

                <!-- Form Section 4: Submission Details -->
                <div style="margin-bottom: 2rem;">
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

                <!-- Submit Button -->
                <button type="submit" class="btn-custom" style="width: 100%; padding: 0.75rem;">
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
                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label" for="approve_agency_number">Agency Number *</label>
                    <input type="text" id="approve_agency_number" name="agency_number" class="form-control-dark" style="width: 100%; padding: 0.6rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: #111c2d; color: #ffffff;" required>
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
        // Add Application Modal Toggle
        function openModal() {
            document.getElementById('addAppModal').style.display = 'flex';
        }

        // Close Modal Toggle
        function closeModal() {
            document.getElementById('addAppModal').style.display = 'none';
        }

        // Edit Application Modal Toggle
        function openEditModal(appItem) {
            const form = document.getElementById('editAppForm');
            form.action = '/admin/applications/' + appItem.id;

            document.getElementById('edit_applicant_name').value = appItem.applicant_name;
            document.getElementById('edit_status').value = appItem.status;
            document.getElementById('edit_details').value = appItem.details || '';

            // Meta fields mapping
            const meta = appItem.meta || {};
            document.getElementById('edit_father_name').value = meta.father_name || '';
            document.getElementById('edit_fathers_father').value = meta.fathers_father || '';
            document.getElementById('edit_mother_name').value = meta.mother_name || '';
            document.getElementById('edit_dob').value = meta.dob || '';
            document.getElementById('edit_age').value = meta.age || '';
            document.getElementById('edit_aadhar_number').value = meta.aadhar_number || '';
            document.getElementById('edit_house_name').value = meta.house_name || '';
            document.getElementById('edit_place').value = meta.place || meta.location || appItem.place || '';
            document.getElementById('edit_post_office').value = meta.post_office || '';
            document.getElementById('edit_panchayat').value = meta.panchayat || '';
            document.getElementById('edit_district').value = meta.district || '';
            document.getElementById('edit_pin_code').value = meta.pin_code || '';
            document.getElementById('edit_mobile_1').value = meta.mobile_1 || '';
            document.getElementById('edit_mobile_2').value = meta.mobile_2 || '';

            document.getElementById('edit_children_total').value = meta.children_total || '';
            document.getElementById('edit_children_male').value = meta.children_male || '';
            document.getElementById('edit_children_female').value = meta.children_female || '';
            document.getElementById('edit_nri_status').value = meta.nri_status || 'No';
            document.getElementById('edit_occupation').value = meta.occupation || '';
            document.getElementById('edit_monthly_income').value = meta.monthly_income || '';
            document.getElementById('edit_other_income_sources').value = meta.other_income_sources || '';
            document.getElementById('edit_health_status').value = meta.health_status || '';
            document.getElementById('edit_disability_status').value = meta.disability_status || 'No';

            document.getElementById('edit_routine_treatment_explanation').value = meta.routine_treatment_explanation || '';
            document.getElementById('edit_chronic_patients_description').value = meta.chronic_patients_description || '';
            document.getElementById('edit_residence_info').value = meta.residence_info || 'Own House';
            document.getElementById('edit_own_house_condition').value = meta.own_house_condition || '';
            document.getElementById('edit_own_place_status').value = meta.own_place_status || 'No';
            document.getElementById('edit_own_place_size').value = meta.own_place_size || '';
            document.getElementById('edit_sequel_status').value = meta.sequel_status || 'No';
            document.getElementById('edit_welfare_assistance_areas').value = meta.welfare_assistance_areas || '';

            document.getElementById('edit_cluster_id').value = appItem.cluster_id || '';
            document.getElementById('edit_agency_number').value = appItem.agency_number || '';

            document.getElementById('editAppModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editAppModal').style.display = 'none';
        }

        // View Details Modal Toggle
        function openDetailsModal(appItem) {
            currentDetailsAppItem = appItem;
            
            // Populate status actions in the modal footer dynamically
            const statusActionsContainer = document.getElementById('modal_status_actions');
            if (statusActionsContainer) {
                let statusHtml = '';
                const rejectUrl = `/admin/applications/{{ $categorySlug }}/${appItem.id}/reject`;
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
                    statusHtml = `
                        <form action="${rejectUrl}" method="POST" style="display: inline-block;" onsubmit="confirmApplicationRejection(event, this); return false;">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <button type="submit" class="btn-danger-custom" style="padding: 0.6rem 1.5rem; display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                                <i class="bx bx-x"></i> Reject Application
                            </button>
                        </form>
                    `;
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
            const getV = (k) => appItem[k] !== undefined && appItem[k] !== null && appItem[k] !== '' ? appItem[k] : (meta[k] !== undefined && meta[k] !== null && meta[k] !== '' ? meta[k] : null);
            const formatVal = (val) => val ? val : '<span style="color: var(--text-muted); font-style: italic;">N/A</span>';
            
            let html = `
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <!-- Col 1 -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">1. Personal Details of Applicant</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px;">Applicant Name:</td><td style="font-weight: 600; color: #ffffff;">${formatVal(getV('applicant_name'))}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Date of Birth / Age:</td><td>${formatVal(getV('dob'))} / ${formatVal(getV('age'))} yrs</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Aadhaar Number:</td><td>${formatVal(getV('aadhar_number'))}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Father's Name:</td><td>${formatVal(getV('father_name'))}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Father's Father:</td><td>${formatVal(getV('fathers_father'))}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Mother's Name:</td><td>${formatVal(getV('mother_name'))}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">House / Place:</td><td>${formatVal(getV('house_name'))} / ${formatVal(getV('place') || getV('location'))}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">PO / Panchayat / Dist:</td><td>${formatVal(getV('post_office') || getV('post'))} / ${formatVal(getV('panchayat') || getV('panchayath'))} / ${formatVal(getV('district'))}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Pin Code / Contact:</td><td>Pin: ${formatVal(getV('pin_code') || getV('pin'))} / Mob: ${formatVal(getV('contact_number_1') || getV('mobile_1') || getV('mobile'))} ${getV('contact_number_2') || getV('mobile_2') ? ', ' + (getV('contact_number_2') || getV('mobile_2')) : ''}</td></tr>
                        </table>

                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">2. Family & Income Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px;">Children in Family:</td><td>Total: ${formatVal(meta.children_total)} (M: ${formatVal(meta.children_male)} / F: ${formatVal(meta.children_female)})</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">NRI Status:</td><td>${formatVal(meta.nri_status)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Occupation:</td><td>${formatVal(meta.occupation)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Monthly Income:</td><td>${meta.monthly_income ? '₹' + Number(meta.monthly_income).toLocaleString() : 'N/A'}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Other Income Sources:</td><td>${formatVal(meta.other_income_sources)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Health & Disability:</td><td>Health: ${formatVal(meta.health_status)} / Disability: ${formatVal(meta.disability_status)}</td></tr>
                        </table>
                    </div>

                    <!-- Col 2 -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">3. Health & Residence Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px;">Routine Treatment details:</td><td>${formatVal(meta.routine_treatment_explanation)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Chronic Patients in House:</td><td>${formatVal(meta.chronic_patients_description)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Residence Information:</td><td style="font-weight: 600; color: #ffffff;">${formatVal(meta.residence_info)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Own House Condition:</td><td>${formatVal(meta.own_house_condition)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Own Place / Size:</td><td>Place: ${formatVal(meta.own_place_status)} / Size: ${formatVal(meta.own_place_size)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Is there a sequel?</td><td>${formatVal(meta.sequel_status)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Welfare Areas:</td><td>${formatVal(meta.welfare_assistance_areas)}</td></tr>
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
                                    <td style="padding: 0.5rem 0; font-weight: 600; width: 150px; color: var(--text-muted);">Assigned Cluster:</td>
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
                                    <td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Sponsor Status:</td>
                                    <td id="modal-sponsor-display-status" style="font-weight: 600; color: #ffffff;">
                                        ${appItem.sponsor_status === 'Sponsored' 
                                            ? '<span style="background-color: rgba(16, 185, 129, 0.2); color: var(--accent-green); padding: 0.15rem 0.4rem; border-radius: 4px; font-weight: 600;">Sponsored</span>' 
                                            : '<span style="background-color: rgba(245, 158, 11, 0.2); color: #f59e0b; padding: 0.15rem 0.4rem; border-radius: 4px; font-weight: 600;">Not Sponsored</span>'}
                                    </td>
                                </tr>
                            </table>
                            <button type="button" onclick="toggleClusterEditForm()" class="btn-custom" style="background: transparent; border: 1px solid var(--accent-cyan); color: var(--accent-cyan); padding: 0.35rem 0.75rem; font-size: 0.8rem; border-radius: 6px; cursor: pointer; white-space: nowrap;">
                                <i class="bx bx-edit"></i> Edit Cluster
                            </button>
                        </div>
                    </div>

                    <div id="modal-cluster-edit-form" style="display: none; margin-top: 0.5rem;">
                        <form onsubmit="submitClusterForm(event, ${appItem.id})">
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
                            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                <button type="button" onclick="toggleClusterEditForm()" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); padding: 0.35rem 0.75rem; font-size: 0.8rem;">Cancel</button>
                                <button type="submit" class="btn-custom" style="background: var(--accent-blue); color: #ffffff; border: none; padding: 0.35rem 0.75rem; font-size: 0.8rem;">Save Cluster Info</button>
                            </div>
                        </form>
                    </div>
                </div>
                ` : ''}

                ${(appItem.status === 'Rejected' && (appItem.rejected_reason || meta.rejected_reason)) ? `
                <div style="margin-top: 1.5rem; border-top: 1px solid var(--panel-border); padding-top: 1rem;">
                    <h5 style="color: var(--accent-red); font-size: 0.85rem; margin-bottom: 0.5rem; text-transform: uppercase; font-weight: 700;">Rejected Reason:</h5>
                    <p style="color: #ffffff; line-height: 1.5; font-size: 0.85rem; margin: 0; background-color: rgba(239, 68, 68, 0.05); padding: 0.75rem; border-radius: 6px; border: 1px solid rgba(239, 68, 68, 0.2); min-height: 50px;">
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
            `;
            
            document.getElementById('details_content').innerHTML = html;
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
                showCustomConfirm('Are you sure you want to delete this application? This action cannot be undone.', function() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/admin/applications/' + currentDetailsAppItem.id;

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

        // Realtime calculation of children count and age from Date of Birth
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
            const maleInput = document.getElementById('children_male');
            const femaleInput = document.getElementById('children_female');
            const totalInput = document.getElementById('children_total');

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
            const editMaleInput = document.getElementById('edit_children_male');
            const editFemaleInput = document.getElementById('edit_children_female');
            const editTotalInput = document.getElementById('edit_children_total');

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

        function openApproveModal(appId, clusterId = '', agencyNumber = '') {
            const form = document.getElementById('approveAppForm');
            form.action = `/admin/applications/{{ $categorySlug }}/${appId}/approve`;
            
            document.getElementById('approve_cluster_id').value = clusterId || '';
            document.getElementById('approve_agency_number').value = agencyNumber || '';
            
            document.getElementById('approveAppModal').style.display = 'flex';
        }

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
                const response = await fetch(`/admin/applications/${appId}/update-cluster`, {
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
    </script>

@endsection
