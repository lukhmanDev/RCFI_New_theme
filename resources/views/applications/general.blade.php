@extends('layouts.admin')

@section('title', 'General Applications')

@section('content')

    <!-- Back Button Header -->
    <div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
        <a href="{{ route('applications.index') }}" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); padding: 0.5rem 1rem;">
            <i class="bx bx-left-arrow-alt"></i> Back to Dashboard
        </a>
        <h3 style="color: #ffffff; font-size: 1.25rem; font-weight: 600;">General Applications Registry</h3>
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
                            $appId = 'APLRCFI' . $appYear . 'GN' . str_pad($appItem->id, 5, '0', STR_PAD_LEFT);
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
                            <td colspan="14" style="text-align: center; padding: 2rem;">No general applications registered yet.</td>
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
            
            <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
                <button onclick="closeDetailsModal()" class="btn-custom" style="padding: 0.6rem 1.5rem;">Close Details</button>
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
                    
                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="applicant_name">Name *</label>
                            <input type="text" class="form-control-dark" id="applicant_name" name="applicant_name" value="{{ old('applicant_name') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="age">Age *</label>
                            <input type="number" class="form-control-dark" id="age" name="meta[age]" value="{{ old('meta.age') }}" required>
                        </div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="address">Current Mailing Address *</label>
                        <textarea class="form-control-dark" id="address" name="meta[address]" style="height: 50px;" required>{{ old('meta.address') }}</textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="ward">Ward *</label>
                            <input type="text" class="form-control-dark" id="ward" name="meta[ward]" value="{{ old('meta.ward') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="post">POST *</label>
                            <input type="text" class="form-control-dark" id="post" name="meta[post]" value="{{ old('meta.post') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="village">Village *</label>
                            <input type="text" class="form-control-dark" id="village" name="meta[village]" value="{{ old('meta.village') }}" required>
                        </div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="panchayat_municipality_corporation">Panchayat/Municipality/Corporation *</label>
                        <input type="text" class="form-control-dark" id="panchayat_municipality_corporation" name="meta[panchayat_municipality_corporation]" value="{{ old('meta.panchayat_municipality_corporation') }}" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="block">Block *</label>
                            <input type="text" class="form-control-dark" id="block" name="meta[block]" value="{{ old('meta.block') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="district">District *</label>
                            <input type="text" class="form-control-dark" id="district" name="meta[district]" value="{{ old('meta.district') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="state">State *</label>
                            <input type="text" class="form-control-dark" id="state" name="meta[state]" value="{{ old('meta.state') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="pin">Pin Code *</label>
                            <input type="text" class="form-control-dark" id="pin" name="meta[pin]" value="{{ old('meta.pin') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="contact_number_1">Contact Number 1 *</label>
                            <input type="text" class="form-control-dark" id="contact_number_1" name="meta[contact_number_1]" value="{{ old('meta.contact_number_1') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="contact_number_2">Contact Number 2 *</label>
                            <input type="text" class="form-control-dark" id="contact_number_2" name="meta[contact_number_2]" value="{{ old('meta.contact_number_2') }}" required>
                        </div>
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
                            <label class="form-label" for="average_monthly_income">Average Monthly Income ($) *</label>
                            <input type="number" class="form-control-dark" id="average_monthly_income" name="meta[average_monthly_income]" value="{{ old('meta.average_monthly_income') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="applying_for">Applying for *</label>
                            <input type="text" class="form-control-dark" id="applying_for" name="meta[applying_for]" value="{{ old('meta.applying_for') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="monthly_income_detail">Monthly Income ($) *</label>
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
                        <label class="form-label" for="details">Additional Notes *</label>
                        <textarea class="form-control-dark" id="details" name="details" style="height: 60px; resize: vertical;" required>{{ old('details') }}</textarea>
                    </div>

                    <input type="hidden" name="status" value="Pending">
                    <input type="hidden" name="amount_requested" value="0">
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
                    
                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_applicant_name">Name *</label>
                            <input type="text" class="form-control-dark" id="edit_applicant_name" name="applicant_name" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_age">Age *</label>
                            <input type="number" class="form-control-dark" id="edit_age" name="meta[age]" required>
                        </div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="edit_address">Current Mailing Address *</label>
                        <textarea class="form-control-dark" id="edit_address" name="meta[address]" style="height: 50px;" required></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_ward">Ward *</label>
                            <input type="text" class="form-control-dark" id="edit_ward" name="meta[ward]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_post">POST *</label>
                            <input type="text" class="form-control-dark" id="edit_post" name="meta[post]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_village">Village *</label>
                            <input type="text" class="form-control-dark" id="edit_village" name="meta[village]" required>
                        </div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="edit_panchayat_municipality_corporation">Panchayat/Municipality/Corporation *</label>
                        <input type="text" class="form-control-dark" id="edit_panchayat_municipality_corporation" name="meta[panchayat_municipality_corporation]" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_block">Block *</label>
                            <input type="text" class="form-control-dark" id="edit_block" name="meta[block]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_district">District *</label>
                            <input type="text" class="form-control-dark" id="edit_district" name="meta[district]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_state">State *</label>
                            <input type="text" class="form-control-dark" id="edit_state" name="meta[state]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_pin">Pin Code *</label>
                            <input type="text" class="form-control-dark" id="edit_pin" name="meta[pin]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_contact_number_1">Contact Number 1 *</label>
                            <input type="text" class="form-control-dark" id="edit_contact_number_1" name="meta[contact_number_1]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_contact_number_2">Contact Number 2 *</label>
                            <input type="text" class="form-control-dark" id="edit_contact_number_2" name="meta[contact_number_2]" required>
                        </div>
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
                            <label class="form-label" for="edit_average_monthly_income">Average Monthly Income ($) *</label>
                            <input type="number" class="form-control-dark" id="edit_average_monthly_income" name="meta[average_monthly_income]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_applying_for">Applying for *</label>
                            <input type="text" class="form-control-dark" id="edit_applying_for" name="meta[applying_for]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_monthly_income_detail">Monthly Income ($) *</label>
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
            document.getElementById('edit_status').value = appItem.status;
            document.getElementById('edit_details').value = appItem.details || '';

            // Meta fields mapping
            const meta = appItem.meta || {};
            document.getElementById('edit_age').value = meta.age || '';
            document.getElementById('edit_address').value = meta.address || '';
            document.getElementById('edit_ward').value = meta.ward || '';
            document.getElementById('edit_post').value = meta.post || '';
            document.getElementById('edit_village').value = meta.village || '';
            document.getElementById('edit_panchayat_municipality_corporation').value = meta.panchayat_municipality_corporation || '';
            document.getElementById('edit_block').value = meta.block || '';
            document.getElementById('edit_district').value = meta.district || '';
            document.getElementById('edit_state').value = meta.state || '';
            document.getElementById('edit_pin').value = meta.pin || '';
            document.getElementById('edit_contact_number_1').value = meta.contact_number_1 || '';
            document.getElementById('edit_contact_number_2').value = meta.contact_number_2 || '';
            
            // Radio mapping
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
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">1. Personal Details of Applicant</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px;">Applicant Name:</td><td>${formatVal(appItem.applicant_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Age / Sex:</td><td>${formatVal(meta.age)} yrs / ${formatVal(meta.sex)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Mailing Address:</td><td>${formatVal(meta.address)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Ward / Village / Post:</td><td>${formatVal(meta.ward)} / ${formatVal(meta.village)} / ${formatVal(meta.post)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Panchayat/Mun/Corp:</td><td>${formatVal(meta.panchayat_municipality_corporation)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Block / District / State:</td><td>${formatVal(meta.block)} / ${formatVal(meta.district)} / ${formatVal(meta.state)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Pin Code:</td><td>${formatVal(meta.pin)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Contact 1 / 2:</td><td>${formatVal(meta.contact_number_1)} / ${formatVal(meta.contact_number_2)}</td></tr>
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
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Average Monthly Income:</td><td>${meta.average_monthly_income ? '$' + Number(meta.average_monthly_income).toLocaleString() : 'N/A'}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Applying for:</td><td style="font-weight: 600; color: #ffffff;">${formatVal(meta.applying_for)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Monthly Income:</td><td>${meta.monthly_income_detail ? '$' + Number(meta.monthly_income_detail).toLocaleString() : 'N/A'}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Recommended by:</td><td>${formatVal(meta.recommended_by)} <span style="font-size: 0.8rem; color: var(--text-muted);">(${formatVal(meta.recommended_phone)})</span></td></tr>
                        </table>

                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">3. For Office Use Only</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px;">Office Application Type:</td><td style="font-weight: 600; color: var(--accent-cyan);">${formatVal(meta.office_application_type)}</td></tr>
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
    </script>

@endsection
