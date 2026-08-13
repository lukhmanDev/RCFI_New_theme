@extends('layouts.admin')

@section('title', 'House Applications')

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
            <h2 class="panel-title">House Applications List</h2>
            <div style="display: flex; gap: 0.75rem;">
                <a href="{{ route('applications.export', $categorySlug) }}" class="btn-custom" style="background: linear-gradient(135deg, #2ecc71, #27ae60); text-decoration: none;">
                    <i class="bx bx-download"></i> Download Excel
                </a>
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
                        <th>Name of Applicant</th>
                        <th>Place</th>
                        <th>District</th>
                        <th>Accommodation</th>
                        <th>Desired Model</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $appItem)
                        @php
                            $meta = $appItem->meta ?? [];
                            $appYear = !empty($appItem->created_at) ? date('y', strtotime($appItem->created_at)) : '24';
                            $appId = 'APLRCFI' . $appYear . 'H' . str_pad($appItem->id, 5, '0', STR_PAD_LEFT);
                            
                            $searchTerms = [
                                $appId,
                                $appItem->applicant_name ?? '',
                                $appItem->place ?? '',
                                $appItem->district ?? $meta['district'] ?? '',
                                $meta['project_type'] ?? $meta['desired_model'] ?? $appItem->project_type ?? '',
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

                            <!-- Name of Applicant -->
                            <td style="font-weight: 600; color: #ffffff;">{{ $appItem->applicant_name }}</td>

                            <!-- Place -->
                            <td>{{ $appItem->place ?? 'N/A' }}</td>

                            <!-- District -->
                            <td>{{ $appItem->district ?? $meta['district'] ?? $meta['locality_district'] ?? 'N/A' }}</td>

                            <!-- Accommodation -->
                            <td>{{ $meta['accommodation_details'] ?? 'N/A' }}</td>

                            <!-- Desired Model -->
                            <td>{{ $meta['desired_model'] ?? 'N/A' }}</td>

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
                                <button onclick="openDetailsModal({{ $appItem->id }})" class="btn-custom" style="background: transparent; color: var(--accent-green); border: 1px solid var(--accent-green); padding: 0.4rem; font-size: 1rem; border-radius: 6px; cursor: pointer; transition: all 0.2s; margin-right: 0.5rem; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px;" title="Details"><i class="bx bx-show"></i></button>

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
                            <td colspan="8" style="text-align: center; padding: 2rem;">No house applications registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- View Full Details Modal Dialog -->
    <div id="detailsAppModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.75); display: none; align-items: center; justify-content: center; z-index: 1100; overflow-y: auto;" onclick="closeDetailsModal()">
        <div class="panel" style="width: 100%; max-width: 800px; margin: 2rem auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #2a3547; max-height: 90vh; overflow-y: auto;" onclick="event.stopPropagation()">
            
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
    <div id="addAppModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.75); display: none; align-items: flex-start; justify-content: center; z-index: 1000; overflow-y: auto; padding: 2rem 1rem;" onclick="closeModal()">
        <div class="panel" style="width: 100%; max-width: 700px; margin: 0 auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #2a3547;" onclick="event.stopPropagation()">
            
            <button onclick="closeModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; z-index: 10;"><i class="bx bx-x"></i></button>
            
            <div class="panel-header" style="margin-bottom: 1.5rem;">
                <h2 class="panel-title" style="font-size: 1.25rem;">Add House Application</h2>
            </div>

            <form action="{{ route('applications.store') }}" method="POST">
                @csrf
                
                <!-- Hidden Category and redirect tags -->
                <input type="hidden" name="category" value="{{ $categoryName }}">
                <input type="hidden" name="redirect_category" value="{{ $categorySlug }}">

                <!-- Form Section 1: Personal Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">1. Personal Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="applicant_name">Name *</label>
                            <input type="text" class="form-control-dark" id="applicant_name" name="applicant_name" value="{{ old('applicant_name') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="age">Age *</label>
                            <input type="number" class="form-control-dark" id="age" name="meta[age]" value="{{ old('meta.age') }}" placeholder="Enter age" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="father_name">Father's Name *</label>
                            <input type="text" class="form-control-dark" id="father_name" name="meta[father_name]" value="{{ old('meta.father_name') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="mother_name">Mother's Name *</label>
                            <input type="text" class="form-control-dark" id="mother_name" name="meta[mother_name]" value="{{ old('meta.mother_name') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="house_name">House Name *</label>
                            <input type="text" class="form-control-dark" id="house_name" name="meta[house_name]" value="{{ old('meta.house_name') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="pin_code">Pin Code *</label>
                            <input type="tel" class="form-control-dark" id="pin_code" name="meta[pin_code]" value="{{ old('meta.pin_code') }}" placeholder="Enter 6-digit pin code" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" required>
                        </div>
                        <div>
                            <label class="form-label" for="place">Place *</label>
                            <input type="text" class="form-control-dark" id="place" name="meta[place]" value="{{ old('meta.place') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="village">Village *</label>
                            <input type="text" class="form-control-dark" id="village" name="meta[village]" value="{{ old('meta.village') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="post">P.O. *</label>
                            <input type="text" class="form-control-dark" id="post" name="meta[post]" value="{{ old('meta.post') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="panchayath">Panchayath *</label>
                            <input type="text" class="form-control-dark" id="panchayath" name="meta[panchayath]" value="{{ old('meta.panchayath') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="district">District *</label>
                            <input type="text" class="form-control-dark" id="district" name="meta[district]" value="{{ old('meta.district') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="state">State *</label>
                            <input type="text" class="form-control-dark" id="state" name="meta[state]" value="{{ old('meta.state') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="gender">Applicant Gender *</label>
                            <select class="form-select-dark" id="gender" name="meta[gender]" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="contact_number_1">Mobile 1 *</label>
                            <input type="tel" class="form-control-dark" id="contact_number_1" name="meta[contact_number_1]" value="{{ old('meta.contact_number_1') }}" placeholder="Enter 10-digit number" maxlength="10" inputmode="numeric" pattern="[0-9]{10}" required>
                        </div>
                        <div>
                            <label class="form-label" for="contact_number_2">Mobile 2 *</label>
                            <input type="tel" class="form-control-dark" id="contact_number_2" name="meta[contact_number_2]" value="{{ old('meta.contact_number_2') }}" placeholder="Enter 10-digit number" maxlength="10" inputmode="numeric" pattern="[0-9]{10}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="education">Educational Qualification *</label>
                            <input type="text" class="form-control-dark" id="education" name="meta[education]" value="{{ old('meta.education') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="married">Married *</label>
                            <select class="form-select-dark" id="married" name="meta[married]" required>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="num_male_children">Male Children *</label>
                            <input type="number" class="form-control-dark" id="num_male_children" name="meta[num_male_children]" value="{{ old('meta.num_male_children', 0) }}" min="0" required>
                        </div>
                        <div>
                            <label class="form-label" for="num_female_children">Female Children *</label>
                            <input type="number" class="form-control-dark" id="num_female_children" name="meta[num_female_children]" value="{{ old('meta.num_female_children', 0) }}" min="0" required>
                        </div>
                        <div>
                            <label class="form-label" for="num_children">Number of Children *</label>
                            <input type="number" class="form-control-dark" id="num_children" name="meta[num_children]" value="{{ old('meta.num_children', 0) }}" readonly style="background-color: rgba(255, 255, 255, 0.05); cursor: not-allowed;">
                        </div>
                    </div>
                </div>

                <!-- Form Section 2: Income & Health Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">2. Income & Health Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="has_occupation">Occupation (Yes/No) *</label>
                            <select class="form-select-dark" id="has_occupation" name="meta[has_occupation]" required>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="monthly_income">Monthly Income (₹) *</label>
                            <input type="number" class="form-control-dark" id="monthly_income" name="meta[monthly_income]" value="{{ old('meta.monthly_income') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="other_income">Other Source of Income *</label>
                            <input type="text" class="form-control-dark" id="other_income" name="meta[other_income]" value="{{ old('meta.other_income') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="health_status">Health Status *</label>
                            <select class="form-select-dark" id="health_status" name="meta[health_status]" required>
                                <option value="Satisfactory">Satisfactory</option>
                                <option value="Chronically Ill">Chronically Ill</option>
                                <option value="Differently Abled">Differently Abled</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="daily_treatment_explanation">Explanation if daily treatment is required *</label>
                        <input type="text" class="form-control-dark" id="daily_treatment_explanation" name="meta[daily_treatment_explanation]" placeholder="Enter diagnosis/treatment or 'None'" value="{{ old('meta.daily_treatment_explanation') }}" required>
                    </div>
                </div>

                <!-- Form Section 3: Accommodation & Land Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">3. Accommodation & Land Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="accommodation_details">Accommodation Details *</label>
                            <select class="form-select-dark" id="accommodation_details" name="meta[accommodation_details]" required>
                                <option value="Own House">Own House</option>
                                <option value="Ancestral Home">Ancestral Home</option>
                                <option value="Rental Home">Rental Home</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="own_place">Have your own place? *</label>
                            <select class="form-select-dark" id="own_place" name="meta[own_place]" required>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="own_place_details">If So How Many *</label>
                            <input type="text" class="form-control-dark" id="own_place_details" name="meta[own_place_details]" placeholder="e.g. 5 cents, 1 acre, or 'None'" value="{{ old('meta.own_place_details') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="land_type">Type of Land *</label>
                            <input type="text" class="form-control-dark" id="land_type" name="meta[land_type]" placeholder="e.g. Wet land, Dry land" value="{{ old('meta.land_type') }}" required>
                        </div>
                    </div>
                </div>

                <!-- Form Section 4: Proposed Project Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">4. Proposed Project Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="desired_model">Desired Model *</label>
                            <select class="form-select-dark" id="desired_model" name="meta[desired_model]" required>
                                <option value="">Select Desired Model</option>
                                <option value="1 BHK">1 BHK</option>
                                <option value="2 BHK">2 BHK</option>
                                <option value="3 BHK">3 BHK</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="building_area_sq">Total Sqr ft *</label>
                            <input type="text" class="form-control-dark" id="building_area_sq" name="meta[building_area_sq]" value="{{ old('meta.building_area_sq') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="proposed_budget">Expected Amount (₹) *</label>
                            <input type="number" class="form-control-dark" id="proposed_budget" name="amount_requested" placeholder="Amount" value="{{ old('amount_requested') }}" required>
                        </div>
                        <div>
                            <label class="form-label" style="display: block; margin-bottom: 0.5rem;">Permission? *</label>
                            <div style="display: flex; gap: 1.5rem; align-items: center; min-height: 38px;">
                                <label style="display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer; color: var(--text-main); font-weight: 500;">
                                    <input type="radio" id="legal_approvals_status_yes" name="meta[legal_approvals_status]" value="Yes" checked required style="accent-color: var(--accent-cyan); width: 16px; height: 16px;"> Yes
                                </label>
                                <label style="display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer; color: var(--text-main); font-weight: 500;">
                                    <input type="radio" id="legal_approvals_status_no" name="meta[legal_approvals_status]" value="No" required style="accent-color: var(--accent-cyan); width: 16px; height: 16px;"> No
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="form-label" for="intended_house_form">Form of Intended House *</label>
                            <select class="form-select-dark" id="intended_house_form" name="meta[intended_house_form]" required>
                                <option value="Sheet">Sheet</option>
                                <option value="Concrete">Concrete</option>
                                <option value="Oat House">Oat House</option>
                                <option value="Flat">Flat</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Form Section 5: For Officec Use & Submit -->
                <div style="margin-bottom: 2rem;">
                    <div style="display: grid; grid-template-columns: 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="office_build_house">For Officec Use *</label>
                            <select class="form-select-dark" id="office_build_house" name="meta[office_build_house]" required>
                                <option value="Build house">Build house</option>
                                <option value="Rennovation">Rennovation</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="details">Additional Notes</label>
                        <textarea class="form-control-dark" id="details" name="details" style="height: 60px; resize: vertical;">{{ old('details') }}</textarea>
                    </div>

                    <input type="hidden" name="status" value="Pending">
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
    <div id="editAppModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.75); display: none; align-items: flex-start; justify-content: center; z-index: 1000; overflow-y: auto; padding: 2rem 1rem;" onclick="closeEditModal()">
        <div class="panel" style="width: 100%; max-width: 700px; margin: 0 auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #2a3547;" onclick="event.stopPropagation()">
            
            <button onclick="closeEditModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; z-index: 10;"><i class="bx bx-x"></i></button>
            
            <div class="panel-header" style="margin-bottom: 1.5rem;">
                <h2 class="panel-title" style="font-size: 1.25rem;">Edit House Application</h2>
            </div>

            <form id="editAppForm" action="" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Hidden Category and redirect tags -->
                <input type="hidden" name="category" value="{{ $categoryName }}">
                <input type="hidden" name="redirect_category" value="{{ $categorySlug }}">

                <!-- Form Section 1: Personal Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">1. Personal Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_applicant_name">Name *</label>
                            <input type="text" class="form-control-dark" id="edit_applicant_name" name="applicant_name" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_age">Age *</label>
                            <input type="number" class="form-control-dark" id="edit_age" name="meta[age]" placeholder="Enter age" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_father_name">Father's Name *</label>
                            <input type="text" class="form-control-dark" id="edit_father_name" name="meta[father_name]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_mother_name">Mother's Name *</label>
                            <input type="text" class="form-control-dark" id="edit_mother_name" name="meta[mother_name]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_house_name">House Name *</label>
                            <input type="text" class="form-control-dark" id="edit_house_name" name="meta[house_name]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_pin_code">Pin Code *</label>
                            <input type="tel" class="form-control-dark" id="edit_pin_code" name="meta[pin_code]" placeholder="Enter 6-digit pin code" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_place">Place *</label>
                            <input type="text" class="form-control-dark" id="edit_place" name="meta[place]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_village">Village *</label>
                            <input type="text" class="form-control-dark" id="edit_village" name="meta[village]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_post">P.O. *</label>
                            <input type="text" class="form-control-dark" id="edit_post" name="meta[post]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_panchayath">Panchayath *</label>
                            <input type="text" class="form-control-dark" id="edit_panchayath" name="meta[panchayath]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_district">District *</label>
                            <input type="text" class="form-control-dark" id="edit_district" name="meta[district]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_state">State *</label>
                            <input type="text" class="form-control-dark" id="edit_state" name="meta[state]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_gender">Applicant Gender *</label>
                            <select class="form-select-dark" id="edit_gender" name="meta[gender]" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_contact_number_1">Mobile 1 *</label>
                            <input type="tel" class="form-control-dark" id="edit_contact_number_1" name="meta[contact_number_1]" placeholder="Enter 10-digit number" maxlength="10" inputmode="numeric" pattern="[0-9]{10}" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_contact_number_2">Mobile 2 *</label>
                            <input type="tel" class="form-control-dark" id="edit_contact_number_2" name="meta[contact_number_2]" placeholder="Enter 10-digit number" maxlength="10" inputmode="numeric" pattern="[0-9]{10}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_education">Educational Qualification *</label>
                            <input type="text" class="form-control-dark" id="edit_education" name="meta[education]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_married">Married *</label>
                            <select class="form-select-dark" id="edit_married" name="meta[married]" required>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="edit_num_male_children">Male Children *</label>
                            <input type="number" class="form-control-dark" id="edit_num_male_children" name="meta[num_male_children]" min="0" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_num_female_children">Female Children *</label>
                            <input type="number" class="form-control-dark" id="edit_num_female_children" name="meta[num_female_children]" min="0" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_num_children">Number of Children *</label>
                            <input type="number" class="form-control-dark" id="edit_num_children" name="meta[num_children]" readonly style="background-color: rgba(255, 255, 255, 0.05); cursor: not-allowed;">
                        </div>
                    </div>
                </div>

                <!-- Form Section 2: Income & Health Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">2. Income & Health Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_has_occupation">Occupation (Yes/No) *</label>
                            <select class="form-select-dark" id="edit_has_occupation" name="meta[has_occupation]" required>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="edit_monthly_income">Monthly Income (₹) *</label>
                            <input type="number" class="form-control-dark" id="edit_monthly_income" name="meta[monthly_income]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_other_income">Other Source of Income *</label>
                            <input type="text" class="form-control-dark" id="edit_other_income" name="meta[other_income]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_health_status">Health Status *</label>
                            <select class="form-select-dark" id="edit_health_status" name="meta[health_status]" required>
                                <option value="Satisfactory">Satisfactory</option>
                                <option value="Chronically Ill">Chronically Ill</option>
                                <option value="Differently Abled">Differently Abled</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="edit_daily_treatment_explanation">Explanation if daily treatment is required *</label>
                        <input type="text" class="form-control-dark" id="edit_daily_treatment_explanation" name="meta[daily_treatment_explanation]" required>
                    </div>
                </div>

                <!-- Form Section 3: Accommodation & Land Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">3. Accommodation & Land Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_accommodation_details">Accommodation Details *</label>
                            <select class="form-select-dark" id="edit_accommodation_details" name="meta[accommodation_details]" required>
                                <option value="Own House">Own House</option>
                                <option value="Ancestral Home">Ancestral Home</option>
                                <option value="Rental Home">Rental Home</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="edit_own_place">Have your own place? *</label>
                            <select class="form-select-dark" id="edit_own_place" name="meta[own_place]" required>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="edit_own_place_details">If So How Many *</label>
                            <input type="text" class="form-control-dark" id="edit_own_place_details" name="meta[own_place_details]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_land_type">Type of Land *</label>
                            <input type="text" class="form-control-dark" id="edit_land_type" name="meta[land_type]" required>
                        </div>
                    </div>
                </div>

                <!-- Form Section 4: Proposed Project Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">4. Proposed Project Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_desired_model">Desired Model *</label>
                            <select class="form-select-dark" id="edit_desired_model" name="meta[desired_model]" required>
                                <option value="">Select Desired Model</option>
                                <option value="1 BHK">1 BHK</option>
                                <option value="2 BHK">2 BHK</option>
                                <option value="3 BHK">3 BHK</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="edit_building_area_sq">Total Sqr ft *</label>
                            <input type="text" class="form-control-dark" id="edit_building_area_sq" name="meta[building_area_sq]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="edit_proposed_budget">Expected Amount (₹) *</label>
                            <input type="number" class="form-control-dark" id="edit_proposed_budget" name="amount_requested" required>
                        </div>
                        <div>
                            <label class="form-label" style="display: block; margin-bottom: 0.5rem;">Permission? *</label>
                            <div style="display: flex; gap: 1.5rem; align-items: center; min-height: 38px;">
                                <label style="display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer; color: var(--text-main); font-weight: 500;">
                                    <input type="radio" id="edit_legal_approvals_status_yes" name="meta[legal_approvals_status]" value="Yes" required style="accent-color: var(--accent-cyan); width: 16px; height: 16px;"> Yes
                                </label>
                                <label style="display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer; color: var(--text-main); font-weight: 500;">
                                    <input type="radio" id="edit_legal_approvals_status_no" name="meta[legal_approvals_status]" value="No" required style="accent-color: var(--accent-cyan); width: 16px; height: 16px;"> No
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="form-label" for="edit_intended_house_form">Form of Intended House *</label>
                            <select class="form-select-dark" id="edit_intended_house_form" name="meta[intended_house_form]" required>
                                <option value="Sheet">Sheet</option>
                                <option value="Concrete">Concrete</option>
                                <option value="Oat House">Oat House</option>
                                <option value="Flat">Flat</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Form Section 5: For Officec Use -->
                <div style="margin-bottom: 2rem;">
                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="edit_office_build_house">For Officec Use *</label>
                        <select class="form-select-dark" id="edit_office_build_house" name="meta[office_build_house]" required>
                            <option value="Build house">Build house</option>
                            <option value="Rennovation">Rennovation</option>
                            <option value="Others">Others</option>
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

        // Add Application Modal Toggle
        function openModal() {
            const modal = document.getElementById('addAppModal') || document.getElementById('addModal') || document.getElementById('createModal');
            if (modal) modal.style.display = 'flex';
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
            document.getElementById('edit_proposed_budget').value = appItem.amount_requested || '';
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

            if (document.getElementById('edit_pin_code')) { document.getElementById('edit_pin_code').value = appItem.pin_code || ''; }
            if (document.getElementById('edit_age')) { document.getElementById('edit_age').value = meta.age || ''; }
            document.getElementById('edit_gender').value = meta.gender || 'Male';
            document.getElementById('edit_contact_number_1').value = meta.contact_number_1 || '';
            document.getElementById('edit_contact_number_2').value = meta.contact_number_2 || '';
            document.getElementById('edit_education').value = meta.education || '';
            document.getElementById('edit_married').value = meta.married || 'Yes';
            document.getElementById('edit_num_male_children').value = meta.num_male_children || 0;
            document.getElementById('edit_num_female_children').value = meta.num_female_children || 0;
            document.getElementById('edit_num_children').value = (parseInt(meta.num_male_children) || 0) + (parseInt(meta.num_female_children) || 0);

            document.getElementById('edit_has_occupation').value = meta.has_occupation || 'Yes';
            document.getElementById('edit_monthly_income').value = meta.monthly_income || 0;
            document.getElementById('edit_other_income').value = meta.other_income || '';
            document.getElementById('edit_health_status').value = meta.health_status || 'Satisfactory';
            document.getElementById('edit_daily_treatment_explanation').value = meta.daily_treatment_explanation || '';

            document.getElementById('edit_accommodation_details').value = meta.accommodation_details || 'Own House';
            document.getElementById('edit_own_place').value = meta.own_place || 'Yes';
            document.getElementById('edit_own_place_details').value = meta.own_place_details || '';
            document.getElementById('edit_land_type').value = meta.land_type || '';

            document.getElementById('edit_desired_model').value = meta.desired_model || '';
            document.getElementById('edit_building_area_sq').value = meta.building_area_sq || '';
            const legalVal = (meta.legal_approvals_status || 'Yes').toLowerCase();
            const legalYes = document.getElementById('edit_legal_approvals_status_yes');
            const legalNo = document.getElementById('edit_legal_approvals_status_no');
            if (legalVal === 'yes') { if (legalYes) legalYes.checked = true; }
            else { if (legalNo) legalNo.checked = true; }
            document.getElementById('edit_intended_house_form').value = meta.intended_house_form || 'Sheet';
            document.getElementById('edit_office_build_house').value = meta.office_build_house || 'Build house';

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

        var applicationsMap = @json($applications->keyBy('id'));

        // View Details Modal Toggle
        function openDetailsModal(appItem) {
            if (typeof appItem === 'number' || typeof appItem === 'string') {
                appItem = applicationsMap[appItem] || appItem;
            }
            if (!appItem || typeof appItem !== 'object') return;
            currentDetailsAppItem = appItem;
            
            // Populate status actions in the modal footer dynamically
            const statusActionsContainer = document.getElementById('modal_status_actions');
            if (statusActionsContainer) {
                let statusHtml = '';
                const approveUrl = `{{ url('admin/applications') }}/{{ $categorySlug }}/${appItem.id}/approve`;
                const rejectUrl = `{{ url('admin/applications') }}/{{ $categorySlug }}/${appItem.id}/reject`;
                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '{{ csrf_token() }}';

                if (appItem.status === 'Pending') {
                    statusHtml = `
                        <form action="${approveUrl}" method="POST" style="display: inline-block;">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <button type="submit" class="btn-custom" style="background: linear-gradient(135deg, #2ecc71, #27ae60); padding: 0.6rem 1.5rem; display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                                <i class="bx bx-check"></i> Approve
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
            const getV = (k) => appItem[k] !== undefined && appItem[k] !== null && appItem[k] !== '' ? appItem[k] : (meta[k] !== undefined && meta[k] !== null && meta[k] !== '' ? meta[k] : null);
            const formatVal = (val) => val ? val : '<span style="color: var(--text-muted); font-style: italic;">N/A</span>';
            
            let html = `
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <!-- Col 1 -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">1. Personal Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">Name:</td><td>${formatVal(getV('applicant_name'))}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Age:</td><td>${formatVal(getV('age'))}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Gender:</td><td>${formatVal(getV('gender'))}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Father's Name:</td><td>${formatVal(getV('father_name'))}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Mother's Name:</td><td>${formatVal(getV('mother_name'))}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">House Name:</td><td>${formatVal(getV('house_name'))}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Place:</td><td>${formatVal(getV('place') || getV('location'))}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Panchayath:</td><td>${formatVal(getV('panchayath') || getV('panchayat'))}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Post Office:</td><td>${formatVal(getV('post') || getV('post_office'))}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">District:</td><td>${formatVal(getV('district'))}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">State:</td><td>${formatVal(getV('state'))}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Pin Code:</td><td>${formatVal(getV('pin_code') || getV('pin'))}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Contact Number 1:</td><td>${formatVal(getV('contact_number_1') || getV('contact1') || getV('mobile'))}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Contact Number 2:</td><td>${formatVal(getV('contact_number_2') || getV('contact2'))}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Education:</td><td>${formatVal(getV('education'))}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Married?</td><td>${formatVal(getV('married'))}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Children Count:</td><td>Total: ${formatVal(getV('num_children') || getV('children_total'))} (M: ${formatVal(getV('num_male_children') || getV('children_male'))} / F: ${formatVal(getV('num_female_children') || getV('children_female'))})</td></tr>
                        </table>

                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">2. Income & Health Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">Occupation?</td><td>${formatVal(getV('has_occupation') || getV('occupation'))}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Monthly Income:</td><td>${getV('monthly_income') ? '₹' + Number(getV('monthly_income')).toLocaleString() : 'N/A'}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Other Income:</td><td>${formatVal(getV('other_income') || getV('other_income_sources'))}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Health Status:</td><td>${formatVal(getV('health_status'))}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Treatment Note:</td><td>${formatVal(getV('daily_treatment_explanation') || getV('routine_treatment_explanation'))}</td></tr>
                        </table>
                    </div>

                    <!-- Col 2 -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">3. Accommodation & Land</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">Accommodation:</td><td>${formatVal(getV('accommodation_details') || getV('residence_info'))}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Own Place?</td><td>${formatVal(getV('own_place') || getV('own_place_status'))}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">If So How Many:</td><td>${formatVal(getV('own_place_details') || getV('own_place_size'))}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Type of Land:</td><td>${formatVal(getV('land_type'))}</td></tr>
                        </table>

                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">4. Proposed Project Description</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">Desired Model:</td><td>${formatVal(meta.desired_model)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Total Sqr ft:</td><td>${formatVal(meta.building_area_sq)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Expected Amount:</td><td style="color: var(--accent-green); font-weight: 600;">${appItem.amount_requested ? '₹' + Number(appItem.amount_requested).toLocaleString() : 'N/A'}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Permission?</td><td>${formatVal(meta.legal_approvals_status)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Form of House:</td><td>${formatVal(meta.intended_house_form)}</td></tr>
                        </table>

                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; margin-top: 1.5rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">For Officec Use:</td><td style="font-weight: 600; color: #ffffff;">${formatVal(meta.office_build_house)}</td></tr>
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
    
        // Auto-calculate Total Children in House Application Add Modal
        document.addEventListener('DOMContentLoaded', function() {
            const maleAdd = document.getElementById('num_male_children');
            const femaleAdd = document.getElementById('num_female_children');
            const totalAdd = document.getElementById('num_children');

            function calcTotalAdd() {
                if (maleAdd && femaleAdd && totalAdd) {
                    const m = parseInt(maleAdd.value) || 0;
                    const f = parseInt(femaleAdd.value) || 0;
                    totalAdd.value = m + f;
                }
            }

            if (maleAdd && femaleAdd && totalAdd) {
                maleAdd.addEventListener('input', calcTotalAdd);
                femaleAdd.addEventListener('input', calcTotalAdd);
                calcTotalAdd();
            }

            const maleEdit = document.getElementById('edit_num_male_children');
            const femaleEdit = document.getElementById('edit_num_female_children');
            const totalEdit = document.getElementById('edit_num_children');

            function calcTotalEdit() {
                if (maleEdit && femaleEdit && totalEdit) {
                    const m = parseInt(maleEdit.value) || 0;
                    const f = parseInt(femaleEdit.value) || 0;
                    totalEdit.value = m + f;
                }
            }

            if (maleEdit && femaleEdit && totalEdit) {
                maleEdit.addEventListener('input', calcTotalEdit);
                femaleEdit.addEventListener('input', calcTotalEdit);
            }
        });

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
