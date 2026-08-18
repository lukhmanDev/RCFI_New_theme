@extends('layouts.admin')

@section('title', 'Shops and Others Applications')

@section('content')

    <!-- Back Button Header -->
    <div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
        <a href="{{ route('applications.index') }}" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); padding: 0.5rem 1rem;">
            <i class="bx bx-left-arrow-alt"></i> Back to Dashboard
        </a>
            </div>

    <!-- Success & Error Alert Panels -->
    @if (session('success'))
        <div class="alert alert-success" style="background-color: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent-green); color: #047857; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500;">
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
            <h2 class="panel-title">Shops and Others Applications List</h2>
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
                        <th>Room Count</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $appItem)
                        @php
                            $meta = $appItem->meta ?? [];
                            $appYear = !empty($appItem->created_at) ? date('y', strtotime($appItem->created_at)) : '24';
                            $appId = 'APLRCFI' . $appYear . 'SO' . str_pad($appItem->id, 5, '0', STR_PAD_LEFT);
                            
                            $searchTerms = [
                                $appId,
                                $appItem->applicant_name ?? '',
                                $appItem->place ?? '',
                                $appItem->district ?? $meta['district'] ?? '',
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

                            <!-- Room Count -->
                            <td>{{ $meta['num_rooms'] ?? 'N/A' }}</td>

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
                            <td colspan="8" style="text-align: center; padding: 2rem;">No shop or other applications registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- View Full Details Modal Dialog -->
    <div id="detailsAppModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.75); display: none; align-items: center; justify-content: center; z-index: 1100; overflow-y: auto;" onclick="closeDetailsModal()">
        <div class="panel" style="width: 95%; max-width: 750px; margin: 2rem auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #2a3547; max-height: 90vh; overflow-y: auto;" onclick="event.stopPropagation()">
            
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
        <div class="panel" style="width: 100%; max-width: 700px; margin: 2rem auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #2a3547; max-height: 90vh; overflow-y: auto;" onclick="event.stopPropagation()">
            
            <button onclick="closeModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; z-index: 10;"><i class="bx bx-x"></i></button>
            
            <div class="panel-header" style="margin-bottom: 1.5rem;">
                <h2 class="panel-title" style="font-size: 1.25rem;">Add Shop or Other Application</h2>
            </div>

            <form action="{{ route('applications.store') }}" method="POST">
                @csrf
                
                <!-- Hidden Category and redirect tags -->
                <input type="hidden" name="category" value="{{ $categoryName }}">
                <input type="hidden" name="redirect_category" value="{{ $categorySlug }}">

                <!-- Form Section 1: Applicant Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">1. Applicant & Committee Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="applicant_name">Name of Applicant *</label>
                            <input type="text" class="form-control-dark" id="applicant_name" name="applicant_name" value="{{ old('applicant_name') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="committee_name">Name of Committee *</label>
                            <input type="text" class="form-control-dark" id="committee_name" name="meta[committee_name]" value="{{ old('meta.committee_name') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="reg_number">Register Number *</label>
                            <input type="text" class="form-control-dark" id="reg_number" name="meta[reg_number]" value="{{ old('meta.reg_number') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="year">Registration Year *</label>
                            <input type="number" class="form-control-dark" id="year" name="meta[year]" value="{{ old('meta.year') }}" placeholder="YYYY" min="1900" max="2099" oninput="if(this.value.length > 4) this.value = this.value.slice(0, 4);" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="pin_code">Pin Code *</label>
                            <input type="tel" class="form-control-dark" id="pin_code" name="meta[pin_code]" value="{{ old('meta.pin_code') }}" placeholder="Enter 6-digit pin code" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" required>
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
                            <label class="form-label" for="post">Post *</label>
                            <input type="text" class="form-control-dark" id="post" name="meta[post]" value="{{ old('meta.post') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="panchayath">Panchayat *</label>
                            <input type="text" class="form-control-dark" id="panchayath" name="meta[panchayath]" value="{{ old('meta.panchayath') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="district">District *</label>
                            <input type="text" class="form-control-dark" id="district" name="meta[district]" value="{{ old('meta.district') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="state">State *</label>
                            <input type="text" class="form-control-dark" id="state" name="meta[state]" value="{{ old('meta.state') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="contact_number_1">Mobile Number 1 *</label>
                            <input type="tel" class="form-control-dark" id="contact_number_1" name="meta[contact_number_1]" value="{{ old('meta.contact_number_1') }}" placeholder="Enter 10-digit number" maxlength="10" inputmode="numeric" pattern="[0-9]{10}" required>
                        </div>
                        <div>
                            <label class="form-label" for="contact_number_2">Mobile Number 2 *</label>
                            <input type="tel" class="form-control-dark" id="contact_number_2" name="meta[contact_number_2]" value="{{ old('meta.contact_number_2') }}" placeholder="Enter 10-digit number" maxlength="10" inputmode="numeric" pattern="[0-9]{10}" required>
                        </div>
                    </div>
                </div>

                <!-- Form Section 2: Description of the project site -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                        <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin: 0; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">2. Description of the project site</h4>
                        <label style="display: inline-flex; align-items: center; gap: 0.4rem; color: var(--accent-cyan); font-size: 0.82rem; font-weight: 600; cursor: pointer; user-select: none;">
                            <input type="checkbox" onchange="copyApplicantAddressToLocality(this)" style="cursor: pointer; width: 16px; height: 16px; accent-color: var(--accent-cyan);">
                            Same as Applicant Address
                        </label>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="mahallu_name">Name of Mahal *</label>
                            <input type="text" class="form-control-dark" id="mahallu_name" name="meta[mahallu_name]" value="{{ old('meta.mahallu_name') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="locality_pin_code">Pin Code *</label>
                            <input type="tel" class="form-control-dark" id="locality_pin_code" name="meta[locality_pin_code]" value="{{ old('meta.locality_pin_code') }}" placeholder="Enter 6-digit pin code" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" required>
                        </div>
                        <div>
                            <label class="form-label" for="locality_place">Location *</label>
                            <input type="text" class="form-control-dark" id="locality_place" name="meta[locality_place]" value="{{ old('meta.locality_place') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="locality_village">Village *</label>
                            <input type="text" class="form-control-dark" id="locality_village" name="meta[locality_village]" value="{{ old('meta.locality_village') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="locality_post">Post *</label>
                            <input type="text" class="form-control-dark" id="locality_post" name="meta[locality_post]" value="{{ old('meta.locality_post') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="locality_panchayath">Panchayath *</label>
                            <input type="text" class="form-control-dark" id="locality_panchayath" name="meta[locality_panchayath]" value="{{ old('meta.locality_panchayath') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="locality_district">District *</label>
                            <input type="text" class="form-control-dark" id="locality_district" name="meta[locality_district]" value="{{ old('meta.locality_district') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="locality_state">State *</label>
                            <input type="text" class="form-control-dark" id="locality_state" name="meta[locality_state]" value="{{ old('meta.locality_state') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; align-items: start;">
                        <div>
                            <label class="form-label" style="display: block; margin-bottom: 0.5rem;">Proposed Site Has Building *</label>
                            <div style="display: flex; gap: 1.5rem; align-items: center; min-height: 38px;">
                                <label style="display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer; color: var(--text-main); font-weight: 500;">
                                    <input type="radio" id="site_has_building_yes" name="meta[site_has_building]" value="Yes" required style="accent-color: var(--accent-cyan); width: 16px; height: 16px;"> Yes
                                </label>
                                <label style="display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer; color: var(--text-main); font-weight: 500;">
                                    <input type="radio" id="site_has_building_no" name="meta[site_has_building]" value="No" checked required style="accent-color: var(--accent-cyan); width: 16px; height: 16px;"> No
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="form-label" for="status_of_current_building">Current Status *</label>
                            <select class="form-select-dark" id="status_of_current_building" name="meta[status_of_current_building]" onchange="toggleCurrentStatusOther(this)" required>
                                <option value="">Select Current Status</option>
                                <option value="Not Started">Not Started</option>
                                <option value="Under Construction">Under Construction</option>
                                <option value="Partially Completed">Partially Completed</option>
                                <option value="Under Renovation">Under Renovation</option>
                                <option value="Old Building Demolished">Old Building Demolished</option>
                                <option value="Not adequate facility">Not adequate facility</option>
                                <option value="Other">Other</option>
                            </select>
                            <div class="current-status-other-wrapper" style="margin-top: 0.5rem; display: none;">
                                <input type="text" class="form-control-dark current-status-other-input" id="status_of_current_building_other" name="meta[status_of_current_building_other]" placeholder="Specify other status details">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Section 3: Description of the proposed project -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">3. Description of the proposed project</h4>
                    
                    <div style="margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="building_area_sq">Matham Building Area (Sqft) *</label>
                            <input type="number" step="any" min="0" class="form-control-dark" id="building_area_sq" name="meta[building_area_sq]" value="{{ old('meta.building_area_sq') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="proposed_budget">Estimated amount (₹) *</label>
                            <input type="number" class="form-control-dark" id="proposed_budget" name="amount_requested" placeholder="Estimated Amount" value="{{ old('amount_requested') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="families_in_mahallu">Families benefited by the scheme *</label>
                            <input type="number" class="form-control-dark" id="families_in_mahallu" name="meta[families_in_mahallu]" value="{{ old('meta.families_in_mahallu') }}" required>
                        </div>
                        <div>
                            <label class="form-label" style="display: block; margin-bottom: 0.5rem;">Are legal permissions available: *</label>
                            <div style="display: flex; gap: 1.5rem; align-items: center; min-height: 38px;">
                                <label style="display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer; color: var(--text-main); font-weight: 500;">
                                    <input type="radio" id="legal_approvals_status_yes" name="meta[legal_approvals_status]" value="Yes" required style="accent-color: var(--accent-cyan); width: 16px; height: 16px;"> Yes
                                </label>
                                <label style="display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer; color: var(--text-main); font-weight: 500;">
                                    <input type="radio" id="legal_approvals_status_no" name="meta[legal_approvals_status]" value="No" checked required style="accent-color: var(--accent-cyan); width: 16px; height: 16px;"> No
                                </label>
                            </div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="permitted_type">Type of Permit Received *</label>
                            <input type="text" class="form-control-dark" id="permitted_type" name="meta[permitted_type]" value="{{ old('meta.permitted_type') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="project_area">Permitted Area *</label>
                            <input type="text" class="form-control-dark" id="project_area" name="meta[project_area]" value="{{ old('meta.project_area') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="num_rooms">How many rooms *</label>
                            <input type="number" class="form-control-dark" id="num_rooms" name="meta[num_rooms]" value="{{ old('meta.num_rooms') }}" required>
                        </div>
                    </div>
                </div>

                <!-- Form Section 4: For Office Use Only -->
                <div style="margin-bottom: 2rem;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="office_shop">For Office Use Only *</label>
                            <select class="form-select-dark" id="office_shop" name="meta[office_shop]" required>
                                <option value="Shop">Shop</option>
                                <option value="Auditorium">Auditorium</option>
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
                            <input type="text" class="form-control-dark" id="recommendation_name" name="meta[recommendation_name]" value="{{ old('meta.recommendation_name') }}" placeholder="Full name">
                        </div>
                        <div>
                            <label class="form-label" for="recommendation_organization">Organization</label>
                            <select class="form-select-dark" id="recommendation_organization" name="meta[recommendation_organization]" onchange="toggleOrgOther(this, 'recommendation_organization_other')">
                                <option value="">-- Select Organization --</option>
                                <option value="KMJ" {{ old('meta.recommendation_organization') == 'KMJ' ? 'selected' : '' }}>KMJ</option>
                                <option value="SYS" {{ old('meta.recommendation_organization') == 'SYS' ? 'selected' : '' }}>SYS</option>
                                <option value="SSF" {{ old('meta.recommendation_organization') == 'SSF' ? 'selected' : '' }}>SSF</option>
                                <option value="Others" {{ old('meta.recommendation_organization') == 'Others' ? 'selected' : '' }}>Others</option>
                            </select>
                            <input type="text" class="form-control-dark" id="recommendation_organization_other" name="meta[recommendation_organization_other]" value="{{ old('meta.recommendation_organization_other') }}" placeholder="Specify organization" style="margin-top: 0.5rem; display: {{ old('meta.recommendation_organization') == 'Others' ? 'block' : 'none' }};">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="recommendation_phone">Phone</label>
                            <input type="tel" class="form-control-dark" id="recommendation_phone" name="meta[recommendation_phone]" value="{{ old('meta.recommendation_phone') }}" placeholder="Phone number">
                        </div>
                        <div>
                            <label class="form-label" for="recommendation_position">Position / Designation</label>
                            <input type="text" class="form-control-dark" id="recommendation_position" name="meta[recommendation_position]" value="{{ old('meta.recommendation_position') }}" placeholder="Job title / Designation">
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
        <div class="panel" style="width: 100%; max-width: 700px; margin: 2rem auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #2a3547; max-height: 90vh; overflow-y: auto;" onclick="event.stopPropagation()">
            
            <button onclick="closeEditModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; z-index: 10;"><i class="bx bx-x"></i></button>
            
            <div class="panel-header" style="margin-bottom: 1.5rem;">
                <h2 class="panel-title" style="font-size: 1.25rem;">Edit Shop or Other Application</h2>
            </div>

            <form id="editAppForm" action="" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Hidden Category and redirect tags -->
                <input type="hidden" name="category" value="{{ $categoryName }}">
                <input type="hidden" name="redirect_category" value="{{ $categorySlug }}">

                <!-- Form Section 1: Applicant Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">1. Applicant & Committee Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_applicant_name">Name of Applicant *</label>
                            <input type="text" class="form-control-dark" id="edit_applicant_name" name="applicant_name" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_committee_name">Name of Committee *</label>
                            <input type="text" class="form-control-dark" id="edit_committee_name" name="meta[committee_name]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_reg_number">Register Number *</label>
                            <input type="text" class="form-control-dark" id="edit_reg_number" name="meta[reg_number]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_year">Registration Year *</label>
                            <input type="number" class="form-control-dark" id="edit_year" name="meta[year]" placeholder="YYYY" min="1900" max="2099" oninput="if(this.value.length > 4) this.value = this.value.slice(0, 4);" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_pin_code">Pin Code *</label>
                            <input type="tel" class="form-control-dark" id="edit_pin_code" name="meta[pin_code]" placeholder="Enter 6-digit pin code" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_place">Place *</label>
                            <input type="text" class="form-control-dark" id="edit_place" name="meta[place]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_village">Village *</label>
                            <input type="text" class="form-control-dark" id="edit_village" name="meta[village]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_post_office">Post *</label>
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

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="edit_state">State *</label>
                            <input type="text" class="form-control-dark" id="edit_state" name="meta[state]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_contact_number_1">Mobile Number 1 *</label>
                            <input type="tel" class="form-control-dark" id="edit_contact_number_1" name="meta[contact_number_1]" placeholder="Enter 10-digit number" maxlength="10" inputmode="numeric" pattern="[0-9]{10}" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_contact_number_2">Mobile Number 2 *</label>
                            <input type="tel" class="form-control-dark" id="edit_contact_number_2" name="meta[contact_number_2]" placeholder="Enter 10-digit number" maxlength="10" inputmode="numeric" pattern="[0-9]{10}" required>
                        </div>
                    </div>
                </div>

                <!-- Form Section 2: Description of the project site -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                        <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin: 0; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">2. Description of the project site</h4>
                        <label style="display: inline-flex; align-items: center; gap: 0.4rem; color: var(--accent-cyan); font-size: 0.82rem; font-weight: 600; cursor: pointer; user-select: none;">
                            <input type="checkbox" id="edit_same_as_applicant" onchange="copyApplicantAddressToLocality(this)" style="cursor: pointer; width: 16px; height: 16px; accent-color: var(--accent-cyan);">
                            Same as Applicant Address
                        </label>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_mahallu_name">Name of Mahal *</label>
                            <input type="text" class="form-control-dark" id="edit_mahallu_name" name="meta[mahallu_name]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_locality_pin_code">Pin Code *</label>
                            <input type="tel" class="form-control-dark" id="edit_locality_pin_code" name="meta[locality_pin_code]" placeholder="Enter 6-digit pin code" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_locality_place">Location *</label>
                            <input type="text" class="form-control-dark" id="edit_locality_place" name="meta[locality_place]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_locality_village">Village *</label>
                            <input type="text" class="form-control-dark" id="edit_locality_village" name="meta[locality_village]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_locality_post">Post *</label>
                            <input type="text" class="form-control-dark" id="edit_locality_post" name="meta[locality_post]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_locality_panchayath">Panchayath *</label>
                            <input type="text" class="form-control-dark" id="edit_locality_panchayath" name="meta[locality_panchayath]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_locality_district">District *</label>
                            <input type="text" class="form-control-dark" id="edit_locality_district" name="meta[locality_district]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_locality_state">State *</label>
                            <input type="text" class="form-control-dark" id="edit_locality_state" name="meta[locality_state]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; align-items: start;">
                        <div>
                            <label class="form-label" style="display: block; margin-bottom: 0.5rem;">Proposed Site Has Building *</label>
                            <div style="display: flex; gap: 1.5rem; align-items: center; min-height: 38px;">
                                <label style="display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer; color: var(--text-main); font-weight: 500;">
                                    <input type="radio" id="edit_site_has_building_yes" name="meta[site_has_building]" value="Yes" required style="accent-color: var(--accent-cyan); width: 16px; height: 16px;"> Yes
                                </label>
                                <label style="display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer; color: var(--text-main); font-weight: 500;">
                                    <input type="radio" id="edit_site_has_building_no" name="meta[site_has_building]" value="No" required style="accent-color: var(--accent-cyan); width: 16px; height: 16px;"> No
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="form-label" for="edit_status_of_current_building">Current Status *</label>
                            <select class="form-select-dark" id="edit_status_of_current_building" name="meta[status_of_current_building]" onchange="toggleCurrentStatusOther(this)" required>
                                <option value="">Select Current Status</option>
                                <option value="Not Started">Not Started</option>
                                <option value="Under Construction">Under Construction</option>
                                <option value="Partially Completed">Partially Completed</option>
                                <option value="Under Renovation">Under Renovation</option>
                                <option value="Old Building Demolished">Old Building Demolished</option>
                                <option value="Not adequate facility">Not adequate facility</option>
                                <option value="Other">Other</option>
                            </select>
                            <div class="current-status-other-wrapper" style="margin-top: 0.5rem; display: none;">
                                <input type="text" class="form-control-dark current-status-other-input" id="edit_status_of_current_building_other" name="meta[status_of_current_building_other]" placeholder="Specify other status details">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Section 3: Description of the proposed project -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">3. Description of the proposed project</h4>
                    
                    <div style="margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_building_area_sq">Matham Building Area (Sqft) *</label>
                            <input type="number" step="any" min="0" class="form-control-dark" id="edit_building_area_sq" name="meta[building_area_sq]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_proposed_budget">Estimated amount (₹) *</label>
                            <input type="number" class="form-control-dark" id="edit_proposed_budget" name="amount_requested" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_families_in_mahallu">Families benefited by the scheme *</label>
                            <input type="number" class="form-control-dark" id="edit_families_in_mahallu" name="meta[families_in_mahallu]" required>
                        </div>
                        <div>
                            <label class="form-label" style="display: block; margin-bottom: 0.5rem;">Are legal permissions available: *</label>
                            <div style="display: flex; gap: 1.5rem; align-items: center; min-height: 38px;">
                                <label style="display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer; color: var(--text-main); font-weight: 500;">
                                    <input type="radio" id="edit_legal_approvals_status_yes" name="meta[legal_approvals_status]" value="Yes" required style="accent-color: var(--accent-cyan); width: 16px; height: 16px;"> Yes
                                </label>
                                <label style="display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer; color: var(--text-main); font-weight: 500;">
                                    <input type="radio" id="edit_legal_approvals_status_no" name="meta[legal_approvals_status]" value="No" required style="accent-color: var(--accent-cyan); width: 16px; height: 16px;"> No
                                </label>
                            </div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="edit_permitted_type">Type of Permit Received *</label>
                            <input type="text" class="form-control-dark" id="edit_permitted_type" name="meta[permitted_type]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_project_area">Permitted Area *</label>
                            <input type="text" class="form-control-dark" id="edit_project_area" name="meta[project_area]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_num_rooms">How many rooms *</label>
                            <input type="number" class="form-control-dark" id="edit_num_rooms" name="meta[num_rooms]" required>
                        </div>
                    </div>
                </div>

                <!-- Form Section 4: For Office Use Only -->
                <div style="margin-bottom: 2rem;">
                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="edit_office_shop">For Office Use Only *</label>
                        <select class="form-select-dark" id="edit_office_shop" name="meta[office_shop]" required>
                            <option value="Shop">Shop</option>
                            <option value="Auditorium">Auditorium</option>
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
                            <input type="text" class="form-control-dark" id="edit_recommendation_name" name="meta[recommendation_name]" placeholder="Full name">
                        </div>
                        <div>
                            <label class="form-label" for="edit_recommendation_organization">Organization</label>
                            <select class="form-select-dark" id="edit_recommendation_organization" name="meta[recommendation_organization]" onchange="toggleOrgOther(this, 'edit_recommendation_organization_other')">
                                <option value="">-- Select Organization --</option>
                                <option value="KMJ">KMJ</option>
                                <option value="SYS">SYS</option>
                                <option value="SSF">SSF</option>
                                <option value="Others">Others</option>
                            </select>
                            <input type="text" class="form-control-dark" id="edit_recommendation_organization_other" name="meta[recommendation_organization_other]" placeholder="Specify organization" style="margin-top: 0.5rem; display: none;">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="edit_recommendation_phone">Phone</label>
                            <input type="tel" class="form-control-dark" id="edit_recommendation_phone" name="meta[recommendation_phone]" placeholder="Phone number">
                        </div>
                        <div>
                            <label class="form-label" for="edit_recommendation_position">Position / Designation</label>
                            <input type="text" class="form-control-dark" id="edit_recommendation_position" name="meta[recommendation_position]" placeholder="Job title / Designation">
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

        function toggleOrgOther(selectEl, otherId) {
            const otherInput = document.getElementById(otherId);
            if (otherInput) {
                otherInput.style.display = selectEl.value === 'Others' ? 'block' : 'none';
                if (selectEl.value !== 'Others') otherInput.value = '';
            }
        }
        window.toggleOrgOther = toggleOrgOther;

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
            document.getElementById('edit_committee_name').value = meta.committee_name || '';
            document.getElementById('edit_reg_number').value = meta.reg_number || '';
            document.getElementById('edit_year').value = meta.year || '';
            const locVal = meta.location || meta.place || appItem.location || appItem.place || '';
            if (document.getElementById('edit_location')) document.getElementById('edit_location').value = locVal;
            if (document.getElementById('edit_place')) document.getElementById('edit_place').value = locVal;
            const vilVal = meta.village || appItem.village || '';
            if (document.getElementById('edit_village')) document.getElementById('edit_village').value = vilVal;
            const postVal = meta.post || meta.post_office || appItem.post || appItem.post_office || '';
            if (document.getElementById('edit_post')) document.getElementById('edit_post').value = postVal;
            if (document.getElementById('edit_post_office')) document.getElementById('edit_post_office').value = postVal;
            const panVal = meta.panchayath || meta.panchayat || appItem.panchayath || appItem.panchayat || '';
            if (document.getElementById('edit_panchayath')) document.getElementById('edit_panchayath').value = panVal;
            if (document.getElementById('edit_panchayat')) document.getElementById('edit_panchayat').value = panVal;
            const distVal = meta.district || appItem.district || '';
            if (document.getElementById('edit_district')) document.getElementById('edit_district').value = distVal;
            const stVal = meta.state || appItem.state || '';
            if (document.getElementById('edit_state')) document.getElementById('edit_state').value = stVal;
            document.getElementById('edit_pin_code').value = meta.pin_code || meta.pin || appItem.pin_code || '';
            document.getElementById('edit_contact_number_1').value = meta.contact_number_1 || '';
            document.getElementById('edit_contact_number_2').value = meta.contact_number_2 || '';
            
            document.getElementById('edit_mahallu_name').value = meta.mahallu_name || '';
            if (document.getElementById('edit_locality_pin_code')) { document.getElementById('edit_locality_pin_code').value = meta.locality_pin_code || meta.locality_pin || ''; }
            document.getElementById('edit_locality_place').value = meta.locality_place || '';
            document.getElementById('edit_locality_village').value = meta.locality_village || '';
            if (document.getElementById('edit_locality_post')) { document.getElementById('edit_locality_post').value = meta.locality_post || meta.locality_post_office || ''; }
            if (document.getElementById('edit_locality_panchayath')) { document.getElementById('edit_locality_panchayath').value = meta.locality_panchayath || meta.locality_panchayat || ''; }
            document.getElementById('edit_locality_district').value = meta.locality_district || '';
            document.getElementById('edit_locality_state').value = meta.locality_state || '';
            const siteHasVal = (meta.site_has_building || 'No').toLowerCase();
            const siteHasYes = document.getElementById('edit_site_has_building_yes');
            const siteHasNo = document.getElementById('edit_site_has_building_no');
            if (siteHasVal === 'yes') { if (siteHasYes) siteHasYes.checked = true; }
            else { if (siteHasNo) siteHasNo.checked = true; }

            const statusSelect = document.getElementById('edit_status_of_current_building');
            const statusOtherInput = document.getElementById('edit_status_of_current_building_other');
            const statusVal = meta.status_of_current_building || '';
            const presets = ['Not Started', 'Under Construction', 'Partially Completed', 'Under Renovation', 'Old Building Demolished', 'Not adequate facility'];
            if (statusSelect) {
                if (presets.includes(statusVal)) {
                    statusSelect.value = statusVal;
                    toggleCurrentStatusOther(statusSelect);
                } else if (statusVal || meta.status_of_current_building_other) {
                    statusSelect.value = 'Other';
                    toggleCurrentStatusOther(statusSelect);
                    if (statusOtherInput) statusOtherInput.value = meta.status_of_current_building_other || statusVal;
                } else {
                    statusSelect.value = '';
                    toggleCurrentStatusOther(statusSelect);
                }
            }

            document.getElementById('edit_building_area_sq').value = meta.building_area_sq || '';
            document.getElementById('edit_families_in_mahallu').value = meta.families_in_mahallu || '';
            const legalVal = (meta.legal_approvals_status || 'No').toLowerCase();
            const legalYes = document.getElementById('edit_legal_approvals_status_yes');
            const legalNo = document.getElementById('edit_legal_approvals_status_no');
            if (legalVal === 'yes') { if (legalYes) legalYes.checked = true; }
            else { if (legalNo) legalNo.checked = true; }
            document.getElementById('edit_permitted_type').value = meta.permitted_type || '';
            document.getElementById('edit_project_area').value = meta.project_area || '';
            document.getElementById('edit_num_rooms').value = meta.num_rooms || '';
            document.getElementById('edit_office_shop').value = meta.office_shop || 'Shop';

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
            const formatVal = (val) => val ? val : '<span style="color: var(--text-muted); font-style: italic;">N/A</span>';
            
            let html = `
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <!-- 1. Applicant & Committee -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">1. Applicant & Committee</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600; width: 170px;">Applicant Name:</td><td style="font-weight: 600; color: #ffffff;">${formatVal(appItem.applicant_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Committee Name:</td><td>${formatVal(meta.committee_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Register Number:</td><td>${formatVal(meta.reg_number)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Year:</td><td>${formatVal(meta.year)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Place:</td><td>${formatVal(meta.place)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Village:</td><td>${formatVal(meta.village)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Post:</td><td>${formatVal(meta.post)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Panchayat:</td><td>${formatVal(meta.panchayath)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">District:</td><td>${formatVal(meta.district)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">State:</td><td>${formatVal(meta.state)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Pin Code:</td><td>${formatVal(meta.pin_code || meta.pin)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Contact Number 1:</td><td>${formatVal(meta.contact_number_1)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Contact Number 2:</td><td>${formatVal(meta.contact_number_2)}</td></tr>
                        </table>
                    </div>

                    <!-- 2. Description of the project site -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">2. Description of the project site</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600; width: 170px;">Name of Mahal:</td><td>${formatVal(meta.mahallu_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Place:</td><td>${formatVal(meta.locality_place)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Village:</td><td>${formatVal(meta.locality_village)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Post:</td><td>${formatVal(meta.locality_post)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Panchayath:</td><td>${formatVal(meta.locality_panchayath || meta.locality_panchayat)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">District:</td><td>${formatVal(meta.locality_district || meta.district)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">State:</td><td>${formatVal(meta.locality_state || meta.state)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Pin Code:</td><td>${formatVal(meta.locality_pin_code || meta.locality_pin)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Has Building?</td><td>${formatVal(meta.site_has_building)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Current Status:</td><td>${formatVal(meta.status_of_current_building_other || meta.status_of_current_building)}</td></tr>
                        </table>
                    </div>

                    <!-- 3. Description of the proposed project -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">3. Description of the proposed project</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600; width: 170px;">Matham Bldg Area:</td><td>${formatVal(meta.building_area_sq)} sq.ft</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Estimated Amount:</td><td style="color: var(--accent-green); font-weight: 600;">${appItem.amount_requested ? '₹' + Number(appItem.amount_requested).toLocaleString() : 'N/A'}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Families Benefited:</td><td>${formatVal(meta.families_in_mahallu)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Legal Permissions?</td><td>${formatVal(meta.legal_approvals_status)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Type of Permit:</td><td>${formatVal(meta.permitted_type)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Permitted Area:</td><td>${formatVal(meta.project_area)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Rooms Count:</td><td>${formatVal(meta.num_rooms)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">For Office Use:</td><td style="font-weight: 600; color: #ffffff;">${formatVal(meta.office_shop)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Review Status:</td><td style="font-weight: 600; color: #ffffff;">${appItem.status}</td></tr>
                        </table>
                    </div>

                    <!-- 4. Recommendation Details -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">4. Recommendation Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600; width: 170px;">Recommender Name:</td><td>${formatVal(meta.recommendation_name || meta.recommender_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Organization:</td><td>${formatVal((meta.recommendation_organization === 'Others' ? meta.recommendation_organization_other : meta.recommendation_organization) || meta.recommender_org)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Phone Number:</td><td>${formatVal(meta.recommendation_phone || meta.recommender_phone)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Position:</td><td>${formatVal(meta.recommendation_position || meta.recommender_position)}</td></tr>
                        </table>
                    </div>

                    ${appItem.details ? `
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">Additional Notes</h4>
                        <p style="color: var(--text-muted); line-height: 1.5; font-size: 0.85rem; margin: 0; background-color: #121824; padding: 0.75rem; border-radius: 6px; border: 1px solid var(--panel-border);">
                            ${appItem.details}
                        </p>
                    </div>
                    ` : ''}
                </div>

                ${(appItem.status === 'Rejected' && (appItem.rejected_reason || meta.rejected_reason)) ? `
                <div style="margin-top: 1.5rem; border-top: 1px solid var(--panel-border); padding-top: 1rem;">
                    <h5 style="color: var(--accent-red); font-size: 0.85rem; margin-bottom: 0.5rem; text-transform: uppercase; font-weight: 700;">Rejected Reason:</h5>
                    <p style="color: #ef4444; line-height: 1.5; font-size: 0.85rem; margin: 0; background-color: rgba(239, 68, 68, 0.08); padding: 0.75rem; border-radius: 6px; border: 1px solid rgba(239, 68, 68, 0.3); min-height: 50px; font-weight: 600;">
                        ${appItem.rejected_reason || meta.rejected_reason}
                    </p>
                </div>
                ` : ''}
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
