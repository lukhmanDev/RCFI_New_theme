@extends('layouts.admin')

@section('title', 'House Applications')

@section('content')

    <!-- Back Button Header -->
    <div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
        <a href="{{ route('applications.index') }}" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); padding: 0.5rem 1rem;">
            <i class="bx bx-left-arrow-alt"></i> Back to Dashboard
        </a>
        <h3 style="color: #ffffff; font-size: 1.25rem; font-weight: 600;">House Registry</h3>
    </div>

    <!-- Success & Error Alert Panels -->
    @if (session('success'))
        <div style="background-color: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent-green); color: #8cf5c6; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500;">
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
                <button onclick="openModal()" class="btn-custom">
                    <i class="bx bx-plus-circle"></i> Add Application
                </button>
            </div>
        </div>
        
        <div style="overflow-x: auto;">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Application ID</th>
                        <th>Name of Applicant</th>
                        <th class="col-committee">House Name</th>
                        <th class="col-reg">Reg. Number</th>
                        <th class="col-year">Year</th>
                        <th class="col-location">Location</th>
                        <th class="col-village">Village</th>
                        <th class="col-post">Post</th>
                        <th class="col-panchayath">Panchayath</th>
                        <th class="col-district">District</th>
                        <th class="col-state">State</th>
                        <th class="col-contact1">Contact 1</th>
                        <th class="col-contact2">Contact 2</th>
                        <th style="text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $appItem)
                        @php
                            $meta = $appItem->meta ?? [];
                            $appYear = !empty($appItem->created_at) ? date('y', strtotime($appItem->created_at)) : '24';
                            $appId = 'APLRCFI' . $appYear . 'HS' . str_pad($appItem->id, 5, '0', STR_PAD_LEFT);
                        @endphp
                        <tr>
                            <!-- Application ID -->
                            <td style="font-weight: 600; color: var(--accent-cyan);">
                                {{ $appId }}
                            </td>

                            <!-- Name of Applicant -->
                            <td style="font-weight: 600; color: #ffffff;">{{ $appItem->applicant_name }}</td>

                            <!-- House Name -->
                            <td class="col-committee">{{ $meta['house_name'] ?? 'N/A' }}</td>

                            <!-- Reg. Number -->
                            <td class="col-reg">N/A</td>

                            <!-- Year -->
                            <td class="col-year">N/A</td>

                            <!-- Location -->
                            <td class="col-location">{{ $meta['location'] ?? 'N/A' }}</td>

                            <!-- Village -->
                            <td class="col-village">N/A</td>

                            <!-- Post -->
                            <td class="col-post">{{ $meta['post_office'] ?? 'N/A' }}</td>

                            <!-- Panchayath -->
                            <td class="col-panchayath">{{ $meta['panchayat'] ?? 'N/A' }}</td>

                            <!-- District -->
                            <td class="col-district">{{ $meta['district'] ?? 'N/A' }}</td>

                            <!-- State -->
                            <td class="col-state">{{ $meta['state'] ?? 'N/A' }}</td>

                            <!-- Contact 1 -->
                            <td class="col-contact1">{{ $meta['mobile_1'] ?? 'N/A' }}</td>

                            <!-- Contact 2 -->
                            <td class="col-contact2">{{ $meta['mobile_2'] ?? 'N/A' }}</td>

                            <!-- Actions -->
                            <td style="text-align: center; white-space: nowrap;">
                                <button onclick="openDetailsModal({{ json_encode($appItem) }})" class="btn-custom" style="background: transparent; color: var(--accent-green); border: 1px solid var(--accent-green); padding: 0.4rem 0.8rem; font-size: 0.8rem; border-radius: 6px; cursor: pointer; transition: all 0.2s; margin-right: 0.5rem;">
                                    Details
                                </button>

                                <button onclick="openEditModal({{ json_encode($appItem) }})" class="btn-custom" style="background: transparent; color: var(--accent-cyan); border: 1px solid var(--accent-cyan); padding: 0.4rem 0.8rem; font-size: 0.8rem; border-radius: 6px; cursor: pointer; transition: all 0.2s; margin-right: 0.5rem;">
                                    Edit
                                </button>
                                
                                <form action="{{ route('applications.destroy', $appItem->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this application?');" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="redirect_category" value="{{ $categorySlug }}">
                                    <button type="submit" class="btn-danger-custom">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" style="text-align: center; padding: 2rem;">No house applications registered yet.</td>
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
            
            <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
                <button onclick="closeDetailsModal()" class="btn-custom" style="padding: 0.6rem 1.5rem;">Close Details</button>
            </div>
        </div>
    </div>

    <!-- Add Application Modal Dialog -->
    <div id="addAppModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.75); display: none; align-items: center; justify-content: center; z-index: 1000; overflow-y: auto;" onclick="closeModal()">
        <div class="panel" style="width: 100%; max-width: 700px; margin: 2rem auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #2a3547; max-height: 90vh; overflow-y: auto;" onclick="event.stopPropagation()">
            
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
                            <input type="number" class="form-control-dark" id="age" name="meta[age]" value="{{ old('meta.age') }}" required>
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

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="house_name">House Name *</label>
                            <input type="text" class="form-control-dark" id="house_name" name="meta[house_name]" value="{{ old('meta.house_name') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="place">Location *</label>
                            <input type="text" class="form-control-dark" id="place" name="meta[place]" value="{{ old('meta.place') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="panchayath">Panchayat *</label>
                            <input type="text" class="form-control-dark" id="panchayath" name="meta[panchayath]" value="{{ old('meta.panchayath') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="post">P.O. *</label>
                            <input type="text" class="form-control-dark" id="post" name="meta[post]" value="{{ old('meta.post') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="pin_code">Pin Code *</label>
                            <input type="text" class="form-control-dark" id="pin_code" name="meta[pin_code]" value="{{ old('meta.pin_code') }}" required>
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
                            <input type="text" class="form-control-dark" id="contact_number_1" name="meta[contact_number_1]" value="{{ old('meta.contact_number_1') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="contact_number_2">Mobile 2 *</label>
                            <input type="text" class="form-control-dark" id="contact_number_2" name="meta[contact_number_2]" value="{{ old('meta.contact_number_2') }}" required>
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
                            <label class="form-label" for="num_children">Number of Children *</label>
                            <input type="number" class="form-control-dark" id="num_children" name="meta[num_children]" value="{{ old('meta.num_children') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="num_male_children">Male Children *</label>
                            <input type="number" class="form-control-dark" id="num_male_children" name="meta[num_male_children]" value="{{ old('meta.num_male_children') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="num_female_children">Female Children *</label>
                            <input type="number" class="form-control-dark" id="num_female_children" name="meta[num_female_children]" value="{{ old('meta.num_female_children') }}" required>
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
                            <label class="form-label" for="monthly_income">Monthly Income ($) *</label>
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
                            <label class="form-label" for="proposed_budget">Expected Amount ($) *</label>
                            <input type="number" class="form-control-dark" id="proposed_budget" name="amount_requested" placeholder="Amount" value="{{ old('amount_requested') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="legal_approvals_status">Permission? (Yes/No) *</label>
                            <select class="form-select-dark" id="legal_approvals_status" name="meta[legal_approvals_status]" required>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
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
                        <label class="form-label" for="details">Additional Notes *</label>
                        <textarea class="form-control-dark" id="details" name="details" style="height: 60px; resize: vertical;" required>{{ old('details') }}</textarea>
                    </div>

                    <input type="hidden" name="status" value="Pending">
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
        <div class="panel" style="width: 100%; max-width: 700px; margin: 2rem auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #2a3547; max-height: 90vh; overflow-y: auto;" onclick="event.stopPropagation()">
            
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
                            <input type="number" class="form-control-dark" id="edit_age" name="meta[age]" required>
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

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_house_name">House Name *</label>
                            <input type="text" class="form-control-dark" id="edit_house_name" name="meta[house_name]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_place">Location *</label>
                            <input type="text" class="form-control-dark" id="edit_place" name="meta[place]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_panchayath">Panchayat *</label>
                            <input type="text" class="form-control-dark" id="edit_panchayath" name="meta[panchayath]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_post">P.O. *</label>
                            <input type="text" class="form-control-dark" id="edit_post" name="meta[post]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_pin_code">Pin Code *</label>
                            <input type="text" class="form-control-dark" id="edit_pin_code" name="meta[pin_code]" required>
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
                            <input type="text" class="form-control-dark" id="edit_contact_number_1" name="meta[contact_number_1]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_contact_number_2">Mobile 2 *</label>
                            <input type="text" class="form-control-dark" id="edit_contact_number_2" name="meta[contact_number_2]" required>
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
                            <label class="form-label" for="edit_num_children">Number of Children *</label>
                            <input type="number" class="form-control-dark" id="edit_num_children" name="meta[num_children]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_num_male_children">Male Children *</label>
                            <input type="number" class="form-control-dark" id="edit_num_male_children" name="meta[num_male_children]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_num_female_children">Female Children *</label>
                            <input type="number" class="form-control-dark" id="edit_num_female_children" name="meta[num_female_children]" required>
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
                            <label class="form-label" for="edit_monthly_income">Monthly Income ($) *</label>
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
                            <label class="form-label" for="edit_proposed_budget">Expected Amount ($) *</label>
                            <input type="number" class="form-control-dark" id="edit_proposed_budget" name="amount_requested" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_legal_approvals_status">Permission? (Yes/No) *</label>
                            <select class="form-select-dark" id="edit_legal_approvals_status" name="meta[legal_approvals_status]" required>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
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
                        <label class="form-label" for="edit_details">Additional Notes *</label>
                        <textarea class="form-control-dark" id="edit_details" name="details" style="height: 60px; resize: vertical;" required></textarea>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-custom" style="width: 100%; padding: 0.75rem;">
                    Save Changes
                </button>
            </form>
        </div>
    </div>

    <!-- Modal Scripts -->
    <script>
        // Add Application Modal Toggle
        function openModal() {
            document.getElementById('addAppModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('addAppModal').style.display = 'none';
        }

        // Edit Application Modal Toggle
        function openEditModal(appItem) {
            const form = document.getElementById('editAppForm');
            form.action = '/admin/applications/' + appItem.id;

            document.getElementById('edit_applicant_name').value = appItem.applicant_name;
            document.getElementById('edit_proposed_budget').value = appItem.amount_requested || '';
            document.getElementById('edit_status').value = appItem.status;
            document.getElementById('edit_details').value = appItem.details || '';

            // Meta fields mapping
            const meta = appItem.meta || {};
            document.getElementById('edit_age').value = meta.age || '';
            document.getElementById('edit_father_name').value = meta.father_name || '';
            document.getElementById('edit_mother_name').value = meta.mother_name || '';
            document.getElementById('edit_house_name').value = meta.house_name || '';
            document.getElementById('edit_place').value = meta.place || '';
            document.getElementById('edit_panchayath').value = meta.panchayath || '';
            document.getElementById('edit_post').value = meta.post || '';
            document.getElementById('edit_pin_code').value = meta.pin_code || '';
            document.getElementById('edit_district').value = meta.district || '';
            document.getElementById('edit_state').value = meta.state || '';
            document.getElementById('edit_gender').value = meta.gender || 'Male';
            document.getElementById('edit_contact_number_1').value = meta.contact_number_1 || '';
            document.getElementById('edit_contact_number_2').value = meta.contact_number_2 || '';
            document.getElementById('edit_education').value = meta.education || '';
            document.getElementById('edit_married').value = meta.married || 'Yes';
            document.getElementById('edit_num_children').value = meta.num_children || 0;
            document.getElementById('edit_num_male_children').value = meta.num_male_children || 0;
            document.getElementById('edit_num_female_children').value = meta.num_female_children || 0;

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
            document.getElementById('edit_legal_approvals_status').value = meta.legal_approvals_status || 'Yes';
            document.getElementById('edit_intended_house_form').value = meta.intended_house_form || 'Sheet';
            document.getElementById('edit_office_build_house').value = meta.office_build_house || 'Build house';

            document.getElementById('editAppModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editAppModal').style.display = 'none';
        }

        // View Details Modal Toggle
        function openDetailsModal(appItem) {
            const meta = appItem.meta || {};
            const formatVal = (val) => val ? val : '<span style="color: var(--text-muted); font-style: italic;">N/A</span>';
            
            let html = `
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <!-- Col 1 -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">1. Personal Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">Name:</td><td>${formatVal(appItem.applicant_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Age / Gender:</td><td>${formatVal(meta.age)} / ${formatVal(meta.gender)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Father's Name:</td><td>${formatVal(meta.father_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Mother's Name:</td><td>${formatVal(meta.mother_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">House Name:</td><td>${formatVal(meta.house_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Location:</td><td>${formatVal(meta.place)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Panchayat / P.O.:</td><td>${formatVal(meta.panchayath)} / ${formatVal(meta.post)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">District / State:</td><td>${formatVal(meta.district)} / ${formatVal(meta.state)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Pin Code:</td><td>${formatVal(meta.pin_code)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Mobile 1 / 2:</td><td>${formatVal(meta.contact_number_1)} / ${formatVal(meta.contact_number_2)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Education:</td><td>${formatVal(meta.education)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Married?</td><td>${formatVal(meta.married)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Children Count:</td><td>Total: ${formatVal(meta.num_children)} (M: ${formatVal(meta.num_male_children)} / F: ${formatVal(meta.num_female_children)})</td></tr>
                        </table>

                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">2. Income & Health Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">Occupation?</td><td>${formatVal(meta.has_occupation)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Monthly Income:</td><td>${meta.monthly_income ? '$' + Number(meta.monthly_income).toLocaleString() : 'N/A'}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Other Income:</td><td>${formatVal(meta.other_income)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Health Status:</td><td>${formatVal(meta.health_status)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Treatment Note:</td><td>${formatVal(meta.daily_treatment_explanation)}</td></tr>
                        </table>
                    </div>

                    <!-- Col 2 -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">3. Accommodation & Land</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">Accommodation:</td><td>${formatVal(meta.accommodation_details)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Own Place?</td><td>${formatVal(meta.own_place)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">If So How Many:</td><td>${formatVal(meta.own_place_details)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Type of Land:</td><td>${formatVal(meta.land_type)}</td></tr>
                        </table>

                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">4. Proposed Project Description</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">Desired Model:</td><td>${formatVal(meta.desired_model)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Total Sqr ft:</td><td>${formatVal(meta.building_area_sq)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Expected Amount:</td><td style="color: var(--accent-green); font-weight: 600;">${appItem.amount_requested ? '$' + Number(appItem.amount_requested).toLocaleString() : 'N/A'}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Permission?</td><td>${formatVal(meta.legal_approvals_status)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Form of House:</td><td>${formatVal(meta.intended_house_form)}</td></tr>
                        </table>

                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem; margin-top: 1.5rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">For Officec Use:</td><td style="font-weight: 600; color: #ffffff;">${formatVal(meta.office_build_house)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Review Status:</td><td style="font-weight: 600; color: #ffffff;">${appItem.status}</td></tr>
                        </table>
                    </div>
                </div>
                
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

        function closeDetailsModal() {
            document.getElementById('detailsAppModal').style.display = 'none';
        }

        // Automatically open add modal if validation error occurs on creation
        @if ($errors->any())
            document.addEventListener("DOMContentLoaded", function() {
                openModal();
            });
        @endif
    </script>

@endsection
