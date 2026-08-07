@extends('layouts.admin')

@section('title', 'Drinking Water - Individual Level Applications')

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
            <h2 class="panel-title">Individual Water Applications List</h2>
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
                        <th>Project Type</th>
                        <th>Well Type</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $appItem)
                        @php
                            $meta = $appItem->meta ?? [];
                            $appYear = !empty($appItem->created_at) ? date('y', strtotime($appItem->created_at)) : '24';
                            $appId = 'APLRCFI' . $appYear . 'DWI' . str_pad($appItem->id, 5, '0', STR_PAD_LEFT);
                            
                            $searchTerms = [
                                $appId,
                                $appItem->applicant_name ?? '',
                                $appItem->place ?? '',
                                $appItem->district ?? $meta['district'] ?? '',
                                $meta['project_type'] ?? $meta['well_type'] ?? $appItem->project_type ?? '',
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

                            <!-- Project Type -->
                            <td>{{ !empty($meta['project_type']) ? ucwords($meta['project_type']) : (!empty($meta['well_type']) ? ucwords($meta['well_type']) : (!empty($appItem->project_type) ? ucwords($appItem->project_type) : 'N/A')) }}</td>

                            <!-- Well Type -->
                            <td>{{ $meta['well_type'] ?? 'N/A' }}</td>

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
                            <td colspan="8" style="text-align: center; padding: 2rem;">No individual drinking water applications registered yet.</td>
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
                <h2 class="panel-title" style="font-size: 1.25rem;">Add Drinking Water Application</h2>
            </div>

            <form action="{{ route('applications.store') }}" method="POST">
                @csrf
                
                <!-- Hidden Category and redirect tags -->
                <input type="hidden" name="category" value="{{ $categoryName }}">
                <input type="hidden" name="redirect_category" value="{{ $categorySlug }}">

                <!-- Form Section 1: Applicant Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">1. Personal Details of Applicant</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="applicant_name">Name of Applicant *</label>
                            <input type="text" class="form-control-dark" id="applicant_name" name="applicant_name" value="{{ old('applicant_name') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="pin">Pin Code *</label>
                            <input type="tel" class="form-control-dark" id="pin" name="meta[pin]" value="{{ old('meta.pin') }}" placeholder="Enter 6-digit pin code" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" required>
                        </div>
                        <div>
                            <label class="form-label" for="location">Place *</label>
                            <input type="text" class="form-control-dark" id="location" name="meta[location]" value="{{ old('meta.location') }}" required>
                        </div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="address">Address *</label>
                        <textarea class="form-control-dark" id="address" name="meta[address]" style="height: 50px;" required>{{ old('meta.address') }}</textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="village">Village *</label>
                            <input type="text" class="form-control-dark" id="village" name="meta[village]" value="{{ old('meta.village') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="post">Post *</label>
                            <input type="text" class="form-control-dark" id="post" name="meta[post]" value="{{ old('meta.post') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="panchayath">Panchayath *</label>
                            <input type="text" class="form-control-dark" id="panchayath" name="meta[panchayath]" value="{{ old('meta.panchayath') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="district">District *</label>
                            <input type="text" class="form-control-dark" id="district" name="meta[district]" value="{{ old('meta.district') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="state">State *</label>
                            <input type="text" class="form-control-dark" id="state" name="meta[state]" value="{{ old('meta.state') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="contact_number_1">Contact 1 *</label>
                            <input type="tel" class="form-control-dark" id="contact_number_1" name="meta[contact_number_1]" value="{{ old('meta.contact_number_1') }}" placeholder="Enter 10-digit number" maxlength="10" inputmode="numeric" pattern="[0-9]{10}" required>
                        </div>
                        <div>
                            <label class="form-label" for="contact_number_2">Contact 2 *</label>
                            <input type="tel" class="form-control-dark" id="contact_number_2" name="meta[contact_number_2]" value="{{ old('meta.contact_number_2') }}" placeholder="Enter 10-digit number" maxlength="10" inputmode="numeric" pattern="[0-9]{10}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="job">Job of Applicant *</label>
                            <input type="text" class="form-control-dark" id="job" name="meta[job]" value="{{ old('meta.job') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="monthly_income">Average Monthly Income *</label>
                            <input type="number" class="form-control-dark" id="monthly_income" name="meta[monthly_income]" value="{{ old('meta.monthly_income') }}" required>
                        </div>
                    </div>
                </div>

                <!-- Form Section 2: Details of Beneficiaries -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">2. Details of Beneficiaries</h4>
                    
                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" style="margin-bottom: 0.5rem; display: block;">List of Beneficiaries *</label>
                        <div id="add_beneficiaries_list">
                            <div class="beneficiary-row" style="display: flex; gap: 0.75rem; margin-bottom: 0.5rem; align-items: center;">
                                <input type="text" class="form-control-dark" name="meta[beneficiaries][0][name]" placeholder="Name" required style="flex: 1;">
                                <input type="text" class="form-control-dark" name="meta[beneficiaries][0][phone]" placeholder="Phone Number" required style="flex: 1;">
                                <button type="button" class="btn-danger-custom" onclick="removeRow(this)" style="padding: 0.5rem 0.75rem; height: 38px; border-radius: 6px;">-</button>
                            </div>
                        </div>
                        <button type="button" class="btn-custom" onclick="addBeneficiaryRow('add_beneficiaries_list')" style="padding: 0.4rem 1rem; font-size: 0.8rem; background-color: var(--accent-green);">+ Add Beneficiary</button>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="num_male_benefited">Total Male *</label>
                            <input type="number" class="form-control-dark" id="num_male_benefited" name="meta[num_male_benefited]" value="{{ old('meta.num_male_benefited') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="num_female_benefited">Total Female *</label>
                            <input type="number" class="form-control-dark" id="num_female_benefited" name="meta[num_female_benefited]" value="{{ old('meta.num_female_benefited') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="num_benefited_people">No. Of Benefited People *</label>
                            <input type="number" class="form-control-dark" id="num_benefited_people" name="meta[num_benefited_people]" value="{{ old('meta.num_benefited_people') }}" readonly required>
                        </div>
                    </div>
                </div>

                <!-- Form Section 3: Owner of the Proposed Land -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                        <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin: 0; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">3. Owner of the Proposed Land</h4>
                        <label style="display: inline-flex; align-items: center; gap: 0.4rem; color: var(--accent-cyan); font-size: 0.82rem; font-weight: 600; cursor: pointer; user-select: none;">
                            <input type="checkbox" onchange="copyApplicantAddressToLocality(this)" style="cursor: pointer; width: 16px; height: 16px; accent-color: var(--accent-cyan);">
                            Same as Applicant Address
                        </label>
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="land_owner_name">Owner of the Proposed Land *</label>
                        <input type="text" maxlength="50" class="form-control-dark" id="land_owner_name" name="meta[land_owner_name]" value="{{ old('meta.land_owner_name') }}" placeholder="Enter land owner name (max 50 chars)" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="land_owner_place">Place *</label>
                            <input type="text" class="form-control-dark" id="land_owner_place" name="meta[land_owner_place]" value="{{ old('meta.land_owner_place') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="land_owner_post">Post *</label>
                            <input type="text" class="form-control-dark" id="land_owner_post" name="meta[land_owner_post]" value="{{ old('meta.land_owner_post') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="land_owner_panchayath">Panchayath *</label>
                            <input type="text" class="form-control-dark" id="land_owner_panchayath" name="meta[land_owner_panchayath]" value="{{ old('meta.land_owner_panchayath') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="land_owner_district">District *</label>
                            <input type="text" class="form-control-dark" id="land_owner_district" name="meta[land_owner_district]" value="{{ old('meta.land_owner_district') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="land_owner_mobile">Mobile *</label>
                            <input type="text" class="form-control-dark" id="land_owner_mobile" name="meta[land_owner_mobile]" value="{{ old('meta.land_owner_mobile') }}" required>
                        </div>
                    </div>
                </div>

                <!-- Form Section 4: Project & Well Details -->
                <div style="margin-bottom: 2rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">4. Project & Well Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="well_type">Type of Well *</label>
                            <select class="form-select-dark" id="well_type" name="meta[well_type]" required>
                                <option value="Bore Well">Bore Well</option>
                                <option value="Open Well">Open Well</option>
                                <option value="India Mark 2 Hand Pump">India Mark 2 Hand Pump</option>
                                <option value="Mazra Well">Mazra Well</option>
                                <option value="Personal Hygiene Corner">Personal Hygiene Corner</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="well_depth">Expected Depth of Well (Feet) *</label>
                            <input type="text" class="form-control-dark" id="well_depth" name="meta[well_depth]" value="{{ old('meta.well_depth') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="well_diameter">Diameter *</label>
                            <input type="text" class="form-control-dark" id="well_diameter" name="meta[well_diameter]" value="{{ old('meta.well_diameter') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="proposed_budget">Budget Estimated (₹) *</label>
                            <input type="number" class="form-control-dark" id="proposed_budget" name="amount_requested" placeholder="Amount" value="{{ old('amount_requested') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="land_nature">Nature of Land *</label>
                            <input type="text" class="form-control-dark" id="land_nature" name="meta[land_nature]" value="{{ old('meta.land_nature') }}" required>
                        </div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="current_water_source">How you are getting drinking water now? *</label>
                        <textarea class="form-control-dark" id="current_water_source" name="meta[current_water_source]" style="height: 50px;" required>{{ old('meta.current_water_source') }}</textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                        <div>
                            <label class="form-label" for="need_pump">Need of Electric Pump *</label>
                            <select class="form-select-dark" id="need_pump" name="meta[need_pump]" required>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="well_for_agriculture">Whether Well Used for Agriculture? *</label>
                            <select class="form-select-dark" id="well_for_agriculture" name="meta[well_for_agriculture]" required>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
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
    <div id="editAppModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.75); display: none; align-items: center; justify-content: center; z-index: 1000; overflow-y: auto;" onclick="closeEditModal()">
        <div class="panel" style="width: 100%; max-width: 750px; margin: 2rem auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #2a3547; max-height: 90vh; overflow-y: auto;" onclick="event.stopPropagation()">
            
            <button onclick="closeEditModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; z-index: 10;"><i class="bx bx-x"></i></button>
            
            <div class="panel-header" style="margin-bottom: 1.5rem;">
                <h2 class="panel-title" style="font-size: 1.25rem;">Edit Drinking Water Application</h2>
            </div>

            <form id="editAppForm" action="" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Hidden Category and redirect tags -->
                <input type="hidden" name="category" value="{{ $categoryName }}">
                <input type="hidden" name="redirect_category" value="{{ $categorySlug }}">

                <!-- Form Section 1: Applicant Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">1. Personal Details of Applicant</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_applicant_name">Name of Applicant *</label>
                            <input type="text" class="form-control-dark" id="edit_applicant_name" name="applicant_name" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_pin">Pin Code *</label>
                            <input type="tel" class="form-control-dark" id="edit_pin" name="meta[pin]" placeholder="Enter 6-digit pin code" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_location">Place *</label>
                            <input type="text" class="form-control-dark" id="edit_location" name="meta[location]" required>
                        </div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="edit_address">Address *</label>
                        <textarea class="form-control-dark" id="edit_address" name="meta[address]" style="height: 50px;" required></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_village">Village *</label>
                            <input type="text" class="form-control-dark" id="edit_village" name="meta[village]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_post">Post *</label>
                            <input type="text" class="form-control-dark" id="edit_post" name="meta[post]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_panchayath">Panchayath *</label>
                            <input type="text" class="form-control-dark" id="edit_panchayath" name="meta[panchayath]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_district">District *</label>
                            <input type="text" class="form-control-dark" id="edit_district" name="meta[district]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_state">State *</label>
                            <input type="text" class="form-control-dark" id="edit_state" name="meta[state]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_contact_number_1">Contact 1 *</label>
                            <input type="tel" class="form-control-dark" id="edit_contact_number_1" name="meta[contact_number_1]" placeholder="Enter 10-digit number" maxlength="10" inputmode="numeric" pattern="[0-9]{10}" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_contact_number_2">Contact 2 *</label>
                            <input type="text" class="form-control-dark" id="edit_contact_number_2" name="meta[contact_number_2]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="edit_job">Job of Applicant *</label>
                            <input type="text" class="form-control-dark" id="edit_job" name="meta[job]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_monthly_income">Average Monthly Income *</label>
                            <input type="number" class="form-control-dark" id="edit_monthly_income" name="meta[monthly_income]" required>
                        </div>
                    </div>
                </div>

                <!-- Form Section 2: Details of Beneficiaries -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">2. Details of Beneficiaries</h4>
                    
                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" style="margin-bottom: 0.5rem; display: block;">List of Beneficiaries *</label>
                        <div id="edit_beneficiaries_list">
                            <!-- Populated dynamically by JS -->
                        </div>
                        <button type="button" class="btn-custom" onclick="addBeneficiaryRow('edit_beneficiaries_list')" style="padding: 0.4rem 1rem; font-size: 0.8rem; background-color: var(--accent-green);">+ Add Beneficiary</button>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="edit_num_male_benefited">Total Male *</label>
                            <input type="number" class="form-control-dark" id="edit_num_male_benefited" name="meta[num_male_benefited]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_num_female_benefited">Total Female *</label>
                            <input type="number" class="form-control-dark" id="edit_num_female_benefited" name="meta[num_female_benefited]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_num_benefited_people">No. Of Benefited People *</label>
                            <input type="number" class="form-control-dark" id="edit_num_benefited_people" name="meta[num_benefited_people]" readonly required>
                        </div>
                    </div>
                </div>

                <!-- Form Section 3: Owner of the Proposed Land -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                        <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin: 0; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">3. Owner of the Proposed Land</h4>
                        <label style="display: inline-flex; align-items: center; gap: 0.4rem; color: var(--accent-cyan); font-size: 0.82rem; font-weight: 600; cursor: pointer; user-select: none;">
                            <input type="checkbox" id="edit_same_as_applicant" onchange="copyApplicantAddressToLocality(this)" style="cursor: pointer; width: 16px; height: 16px; accent-color: var(--accent-cyan);">
                            Same as Applicant Address
                        </label>
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="edit_land_owner_name">Owner of the Proposed Land *</label>
                        <input type="text" maxlength="50" class="form-control-dark" id="edit_land_owner_name" name="meta[land_owner_name]" placeholder="Enter land owner name (max 50 chars)" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_land_owner_place">Place *</label>
                            <input type="text" class="form-control-dark" id="edit_land_owner_place" name="meta[land_owner_place]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_land_owner_post">Post *</label>
                            <input type="text" class="form-control-dark" id="edit_land_owner_post" name="meta[land_owner_post]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="edit_land_owner_panchayath">Panchayath *</label>
                            <input type="text" class="form-control-dark" id="edit_land_owner_panchayath" name="meta[land_owner_panchayath]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_land_owner_district">District *</label>
                            <input type="text" class="form-control-dark" id="edit_land_owner_district" name="meta[land_owner_district]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_land_owner_mobile">Mobile *</label>
                            <input type="text" class="form-control-dark" id="edit_land_owner_mobile" name="meta[land_owner_mobile]" required>
                        </div>
                    </div>
                </div>

                <!-- Form Section 4: Project & Well Details -->
                <div style="margin-bottom: 2rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">4. Project & Well Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_well_type">Type of Well *</label>
                            <select class="form-select-dark" id="edit_well_type" name="meta[well_type]" required>
                                <option value="Bore Well">Bore Well</option>
                                <option value="Open Well">Open Well</option>
                                <option value="India Mark 2 Hand Pump">India Mark 2 Hand Pump</option>
                                <option value="Mazra Well">Mazra Well</option>
                                <option value="Personal Hygiene Corner">Personal Hygiene Corner</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="edit_well_depth">Expected Depth of Well (Feet) *</label>
                            <input type="text" class="form-control-dark" id="edit_well_depth" name="meta[well_depth]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_well_diameter">Diameter *</label>
                            <input type="text" class="form-control-dark" id="edit_well_diameter" name="meta[well_diameter]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_proposed_budget">Budget Estimated (₹) *</label>
                            <input type="number" class="form-control-dark" id="edit_proposed_budget" name="amount_requested" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_land_nature">Nature of Land *</label>
                            <input type="text" class="form-control-dark" id="edit_land_nature" name="meta[land_nature]" required>
                        </div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="edit_current_water_source">How you are getting drinking water now? *</label>
                        <textarea class="form-control-dark" id="edit_current_water_source" name="meta[current_water_source]" style="height: 50px;" required></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                        <div>
                            <label class="form-label" for="edit_need_pump">Need of Electric Pump *</label>
                            <select class="form-select-dark" id="edit_need_pump" name="meta[need_pump]" required>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="edit_well_for_agriculture">Whether Well Used for Agriculture? *</label>
                            <select class="form-select-dark" id="edit_well_for_agriculture" name="meta[well_for_agriculture]" required>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
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

        // Add Beneficiary row helper
        function addBeneficiaryRow(containerId, initialName = '', initialPhone = '') {
            const container = document.getElementById(containerId);
            const index = container.getElementsByClassName('beneficiary-row').length;
            const prefixName = 'meta[beneficiaries]';
            
            const div = document.createElement('div');
            div.className = 'beneficiary-row';
            div.style.display = 'flex';
            div.style.gap = '0.75rem';
            div.style.marginBottom = '0.5rem';
            div.style.alignItems = 'center';
            
            div.innerHTML = `
                <input type="text" class="form-control-dark" name="${prefixName}[${index}][name]" value="${initialName}" placeholder="Name" required style="flex: 1;">
                <input type="text" class="form-control-dark" name="${prefixName}[${index}][phone]" value="${initialPhone}" placeholder="Phone Number" required style="flex: 1;">
                <button type="button" class="btn-danger-custom" onclick="removeRow(this)" style="padding: 0.5rem 0.75rem; height: 38px; border-radius: 6px;">-</button>
            `;
            
            container.appendChild(div);
        }

        function removeRow(btn) {
            const row = btn.parentNode;
            const container = row.parentNode;
            row.remove();
            
            // Re-index names to maintain array continuity
            const rows = container.getElementsByClassName('beneficiary-row');
            const prefixName = 'meta[beneficiaries]';
            for (let i = 0; i < rows.length; i++) {
                const inputs = rows[i].getElementsByTagName('input');
                inputs[0].name = `${prefixName}[${i}][name]`;
                inputs[1].name = `${prefixName}[${i}][phone]`;
            }
        }

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

            if (document.getElementById('edit_pin')) { document.getElementById('edit_pin').value = meta.pin || meta.pin_code || appItem.pin_code || ''; }
            document.getElementById('edit_contact_number_1').value = meta.contact_number_1 || '';
            document.getElementById('edit_contact_number_2').value = meta.contact_number_2 || '';
            document.getElementById('edit_job').value = meta.job || '';
            document.getElementById('edit_monthly_income').value = meta.monthly_income || 0;

            document.getElementById('edit_land_owner_name').value = meta.land_owner_name || '';
            document.getElementById('edit_land_owner_place').value = meta.land_owner_place || '';
            document.getElementById('edit_land_owner_post').value = meta.land_owner_post || '';
            document.getElementById('edit_land_owner_panchayath').value = meta.land_owner_panchayath || '';
            document.getElementById('edit_land_owner_district').value = meta.land_owner_district || '';
            document.getElementById('edit_land_owner_mobile').value = meta.land_owner_mobile || '';

            document.getElementById('edit_well_type').value = meta.well_type || 'Bore Well';
            document.getElementById('edit_well_depth').value = meta.well_depth || '';
            document.getElementById('edit_well_diameter').value = meta.well_diameter || '';
            document.getElementById('edit_land_nature').value = meta.land_nature || '';
            document.getElementById('edit_current_water_source').value = meta.current_water_source || '';
            document.getElementById('edit_need_pump').value = meta.need_pump || 'Yes';
            document.getElementById('edit_well_for_agriculture').value = meta.well_for_agriculture || 'Yes';

            document.getElementById('edit_num_male_benefited').value = meta.num_male_benefited || '';
            document.getElementById('edit_num_female_benefited').value = meta.num_female_benefited || '';
            document.getElementById('edit_num_benefited_people').value = meta.num_benefited_people || '';

            // Populate beneficiaries list rows
            const editBeneficiariesList = document.getElementById('edit_beneficiaries_list');
            editBeneficiariesList.innerHTML = '';
            const beneficiaries = meta.beneficiaries || [];
            if (beneficiaries.length > 0) {
                beneficiaries.forEach(b => {
                    addBeneficiaryRow('edit_beneficiaries_list', b.name, b.phone);
                });
            } else {
                addBeneficiaryRow('edit_beneficiaries_list');
            }

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
            
            // Build Beneficiary details HTML list
            const beneficiaries = meta.beneficiaries || [];
            let beneficiariesHtml = '';
            if (beneficiaries.length > 0) {
                beneficiariesHtml = `<ol style="margin: 0; padding-left: 1.25rem;">`;
                beneficiaries.forEach(b => {
                    beneficiariesHtml += `<li style="margin-bottom: 0.25rem; font-weight: 500; color: #ffffff;">${b.name} <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 400;">(${b.phone})</span></li>`;
                });
                beneficiariesHtml += `</ol>`;
            } else {
                beneficiariesHtml = '<span style="color: var(--text-muted); font-style: italic;">No beneficiaries listed.</span>';
            }

            let html = `
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <!-- Col 1 -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">1. Personal Details of Applicant</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">Applicant Name:</td><td>${formatVal(appItem.applicant_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Place:</td><td>${formatVal(meta.location)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Address:</td><td>${formatVal(meta.address)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Village:</td><td>${formatVal(meta.village)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Post:</td><td>${formatVal(meta.post)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Panchayath:</td><td>${formatVal(meta.panchayath)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">District:</td><td>${formatVal(meta.district)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">State:</td><td>${formatVal(meta.state)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Pin Code:</td><td>${formatVal(meta.pin_code || meta.pin)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Contact Number 1:</td><td>${formatVal(meta.contact_number_1)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Contact Number 2:</td><td>${formatVal(meta.contact_number_2)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Job / Income:</td><td>${formatVal(meta.job)} / ${meta.monthly_income ? '₹' + Number(meta.monthly_income).toLocaleString() : 'N/A'}</td></tr>
                        </table>

                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">2. Details of Beneficiaries</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">No. of Benefited:</td><td>${formatVal(meta.num_benefited_people)} (M: ${formatVal(meta.num_male_benefited)} / F: ${formatVal(meta.num_female_benefited)})</td></tr>
                            <tr><td colspan="15" style="padding-top: 0.5rem; font-weight: 600;">Beneficiaries List:</td></tr>
                            <tr><td colspan="15" style="padding-top: 0.25rem;">${beneficiariesHtml}</td></tr>
                        </table>
                    </div>

                    <!-- Col 2 -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">3. Owner of Proposed Land</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">Land Owner Name:</td><td>${formatVal(meta.land_owner_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Place:</td><td>${formatVal(meta.land_owner_place)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Post:</td><td>${formatVal(meta.locality_post)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Panchayath:</td><td>${formatVal(meta.locality_panchayath || meta.locality_panchayat)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">District / Mobile:</td><td>${formatVal(meta.land_owner_district)} / ${formatVal(meta.land_owner_mobile)}</td></tr>
                        </table>

                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">4. Project & Well Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">Type of Well:</td><td style="font-weight: 600; color: #ffffff;">${formatVal(meta.well_type)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Expected Depth:</td><td>${formatVal(meta.well_depth)} feet</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Diameter:</td><td>${formatVal(meta.well_diameter)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Nature of Land:</td><td>${formatVal(meta.land_nature)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Budget Estimated:</td><td style="color: var(--accent-green); font-weight: 600;">${appItem.amount_requested ? '₹' + Number(appItem.amount_requested).toLocaleString() : 'N/A'}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Current Water Source:</td><td>${formatVal(meta.current_water_source)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Electric Pump?</td><td>${formatVal(meta.need_pump)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">For Agriculture?</td><td>${formatVal(meta.well_for_agriculture)}</td></tr>
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

        // Realtime calculation of beneficiaries count
        document.addEventListener("DOMContentLoaded", function() {
            // Add Modal
            const maleInput = document.getElementById('num_male_benefited');
            const femaleInput = document.getElementById('num_female_benefited');
            const totalInput = document.getElementById('num_benefited_people');

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
            const editMaleInput = document.getElementById('edit_num_male_benefited');
            const editFemaleInput = document.getElementById('edit_num_female_benefited');
            const editTotalInput = document.getElementById('edit_num_benefited_people');

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
