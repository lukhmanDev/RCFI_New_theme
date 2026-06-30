@extends('layouts.admin')

@section('title', 'Orphan Care Applications')

@section('content')

    <!-- Back Button Header -->
    <div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
        <a href="{{ route('applications.index') }}" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); padding: 0.5rem 1rem;">
            <i class="bx bx-left-arrow-alt"></i> Back to Dashboard
        </a>
        <h3 style="color: #ffffff; font-size: 1.25rem; font-weight: 600;">Orphan Care Applications Registry</h3>
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
            <h2 class="panel-title">Orphan Care Applications List</h2>
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
                        <th class="col-committee">Committee Name</th>
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
                            $appId = 'APLRCFI' . $appYear . 'OC' . str_pad($appItem->id, 5, '0', STR_PAD_LEFT);
                        @endphp
                        <tr>
                            <!-- Application ID -->
                            <td style="font-weight: 600; color: var(--accent-cyan);">
                                {{ $appId }}
                            </td>

                            <!-- Name of Applicant -->
                            <td style="font-weight: 600; color: #ffffff;">{{ $appItem->applicant_name }}</td>

                            <!-- Committee Name -->
                            <td class="col-committee">N/A</td>

                            <!-- Reg. Number -->
                            <td class="col-reg">N/A</td>

                            <!-- Year -->
                            <td class="col-year">N/A</td>

                            <!-- Location -->
                            <td class="col-location">{{ $meta['place'] ?? 'N/A' }}</td>

                            <!-- Village -->
                            <td class="col-village">{{ $meta['town'] ?? 'N/A' }}</td>

                            <!-- Post -->
                            <td class="col-post">{{ $meta['post_office'] ?? 'N/A' }}</td>

                            <!-- Panchayath -->
                            <td class="col-panchayath">N/A</td>

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
                            <td colspan="14" style="text-align: center; padding: 2rem;">No orphan care applications registered yet.</td>
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
                <h2 class="panel-title" style="font-size: 1.25rem;">Add Orphan Care Application</h2>
            </div>

            <form action="{{ route('applications.store') }}" method="POST">
                @csrf
                
                <!-- Hidden Category and redirect tags -->
                <input type="hidden" name="category" value="{{ $categoryName }}">
                <input type="hidden" name="redirect_category" value="{{ $categorySlug }}">
                <input type="hidden" name="amount_requested" value="0">

                <!-- Form Section 1: Orphan & Family Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">1. Orphan & Family Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="applicant_name">Name Of Orphan *</label>
                            <input type="text" class="form-control-dark" id="applicant_name" name="applicant_name" value="{{ old('applicant_name') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="father_name">Name Of Father *</label>
                            <input type="text" class="form-control-dark" id="father_name" name="meta[father_name]" value="{{ old('meta.father_name') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="grandfather_name">Name Of GrandFather</label>
                            <input type="text" class="form-control-dark" id="grandfather_name" name="meta[grandfather_name]" value="{{ old('meta.grandfather_name') }}">
                        </div>
                        <div>
                            <label class="form-label" for="mother_name">Name Of Mother *</label>
                            <input type="text" class="form-control-dark" id="mother_name" name="meta[mother_name]" value="{{ old('meta.mother_name') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="mothers_father_name">Name Of MothersFather</label>
                            <input type="text" class="form-control-dark" id="mothers_father_name" name="meta[mothers_father_name]" value="{{ old('meta.mothers_father_name') }}">
                        </div>
                        <div>
                            <label class="form-label" for="gender">Male/Female *</label>
                            <select class="form-select-dark" id="gender" name="meta[gender]" required>
                                <option value="Male" {{ old('meta.gender') === 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('meta.gender') === 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1.5fr 1fr 2fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="dob">Date of Birth *</label>
                            <input type="date" class="form-control-dark" id="dob" name="meta[dob]" value="{{ old('meta.dob') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="age">Age *</label>
                            <input type="number" class="form-control-dark" id="age" name="meta[age]" value="{{ old('meta.age') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="aadhar_number">Aadhar Number *</label>
                            <input type="text" class="form-control-dark" id="aadhar_number" name="meta[aadhar_number]" value="{{ old('meta.aadhar_number') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="guardian_name">Name of Present Guardian *</label>
                            <input type="text" class="form-control-dark" id="guardian_name" name="meta[guardian_name]" value="{{ old('meta.guardian_name') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="guardian_relation">Relation with Orphan *</label>
                            <input type="text" class="form-control-dark" id="guardian_relation" name="meta[guardian_relation]" value="{{ old('meta.guardian_relation') }}" required>
                        </div>
                    </div>
                </div>

                <!-- Form Section 2: Parental Death & Sibling Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">2. Parental Death & Sibling Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="father_death_date">Date of Death(Father) *</label>
                            <input type="text" class="form-control-dark" id="father_death_date" name="meta[father_death_date]" placeholder="e.g. DD-MM-YYYY or Cause" value="{{ old('meta.father_death_date') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="father_death_cause">Cause Of Death *</label>
                            <input type="text" class="form-control-dark" id="father_death_cause" name="meta[father_death_cause]" value="{{ old('meta.father_death_cause') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="mother_alive_status">Mother Alive/Not *</label>
                            <input type="text" class="form-control-dark" id="mother_alive_status" name="meta[mother_alive_status]" placeholder="e.g. Yes / No" value="{{ old('meta.mother_alive_status') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="mother_death_date">If Not/Date of Death</label>
                            <input type="text" class="form-control-dark" id="mother_death_date" name="meta[mother_death_date]" placeholder="e.g. Alive or DD-MM-YYYY" value="{{ old('meta.mother_death_date') }}">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="mother_death_cause">Cause Of Death (Mother)</label>
                            <input type="text" class="form-control-dark" id="mother_death_cause" name="meta[mother_death_cause]" placeholder="e.g. N/A or Cause" value="{{ old('meta.mother_death_cause') }}">
                        </div>
                        <div>
                            <label class="form-label" for="mother_remarried_status">Mother Re-Married/not *</label>
                            <input type="text" class="form-control-dark" id="mother_remarried_status" name="meta[mother_remarried_status]" placeholder="e.g. Yes / No" value="{{ old('meta.mother_remarried_status') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="siblings_total">No Of Brothers And Sisters *</label>
                            <input type="number" class="form-control-dark" id="siblings_total" name="meta[siblings_total]" value="{{ old('meta.siblings_total') }}" readonly required>
                        </div>
                        <div>
                            <label class="form-label" for="siblings_male">Male *</label>
                            <input type="number" class="form-control-dark" id="siblings_male" name="meta[siblings_male]" value="{{ old('meta.siblings_male') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="siblings_female">Female *</label>
                            <input type="number" class="form-control-dark" id="siblings_female" name="meta[siblings_female]" value="{{ old('meta.siblings_female') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="monthly_income">Monthly Income ($) *</label>
                            <input type="number" class="form-control-dark" id="monthly_income" name="meta[monthly_income]" value="{{ old('meta.monthly_income') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="monthly_expense">Monthly Expense ($) *</label>
                            <input type="number" class="form-control-dark" id="monthly_expense" name="meta[monthly_expense]" value="{{ old('meta.monthly_expense') }}" required>
                        </div>
                    </div>
                </div>

                <!-- Form Section 3: Education & Health Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">3. Education & Health Details</h4>
                    
                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" style="margin-bottom: 0.5rem; display: block;">Type Of House *</label>
                        <div style="display: flex; gap: 1.5rem; align-items: center; margin-top: 0.5rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; color: #ffffff; cursor: pointer;">
                                <input type="radio" name="meta[house_type]" value="Own House" required {{ old('meta.house_type') === 'Own House' ? 'checked' : '' }}> Own House
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; color: #ffffff; cursor: pointer;">
                                <input type="radio" name="meta[house_type]" value="Rental" required {{ old('meta.house_type') === 'Rental' ? 'checked' : '' }}> Rental
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; color: #ffffff; cursor: pointer;">
                                <input type="radio" name="meta[house_type]" value="Flat" required {{ old('meta.house_type') === 'Flat' ? 'checked' : '' }}> Flat
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; color: #ffffff; cursor: pointer;">
                                <input type="radio" name="meta[house_type]" value="Others" required {{ old('meta.house_type') === 'Others' ? 'checked' : '' }}> Others
                            </label>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="school_name">Name Of School *</label>
                            <input type="text" class="form-control-dark" id="school_name" name="meta[school_name]" value="{{ old('meta.school_name') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="school_class">Class *</label>
                            <input type="text" class="form-control-dark" id="school_class" name="meta[school_class]" value="{{ old('meta.school_class') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="madrassa_name">Name Of Madrassa</label>
                            <input type="text" class="form-control-dark" id="madrassa_name" name="meta[madrassa_name]" value="{{ old('meta.madrassa_name') }}">
                        </div>
                        <div>
                            <label class="form-label" for="madrassa_class">Class</label>
                            <input type="text" class="form-control-dark" id="madrassa_class" name="meta[madrassa_class]" value="{{ old('meta.madrassa_class') }}">
                        </div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="not_studying_reason">If Not Studying, Reason</label>
                        <input type="text" class="form-control-dark" id="not_studying_reason" name="meta[not_studying_reason]" placeholder="Enter reason or 'N/A'" value="{{ old('meta.not_studying_reason') }}">
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="health_status">Health Status *</label>
                        <input type="text" class="form-control-dark" id="health_status" name="meta[health_status]" value="{{ old('meta.health_status') }}" required>
                    </div>

                    <div>
                        <label class="form-label" for="sponsorship_details">Sponsorship Details If Any</label>
                        <input type="text" class="form-control-dark" id="sponsorship_details" name="meta[sponsorship_details]" placeholder="Enter sponsorship info or 'None'" value="{{ old('meta.sponsorship_details') }}">
                    </div>
                </div>

                <!-- Form Section 4: Address & Contact Details -->
                <div style="margin-bottom: 2rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">4. Address & Contact Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="house_name">House Name *</label>
                            <input type="text" class="form-control-dark" id="house_name" name="meta[house_name]" value="{{ old('meta.house_name') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="place">Place *</label>
                            <input type="text" class="form-control-dark" id="place" name="meta[place]" value="{{ old('meta.place') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="town">Town *</label>
                            <input type="text" class="form-control-dark" id="town" name="meta[town]" value="{{ old('meta.town') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="post_office">Post Office *</label>
                            <input type="text" class="form-control-dark" id="post_office" name="meta[post_office]" value="{{ old('meta.post_office') }}" required>
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
                            <label class="form-label" for="pin_code">Pin Code *</label>
                            <input type="text" class="form-control-dark" id="pin_code" name="meta[pin_code]" value="{{ old('meta.pin_code') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="mobile_1">Mobile 1 *</label>
                            <input type="text" class="form-control-dark" id="mobile_1" name="meta[mobile_1]" value="{{ old('meta.mobile_1') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="mobile_2">Mobile 2</label>
                            <input type="text" class="form-control-dark" id="mobile_2" name="meta[mobile_2]" value="{{ old('meta.mobile_2') }}">
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
        <div class="panel" style="width: 100%; max-width: 750px; margin: 2rem auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #2a3547; max-height: 90vh; overflow-y: auto;" onclick="event.stopPropagation()">
            
            <button onclick="closeEditModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; z-index: 10;"><i class="bx bx-x"></i></button>
            
            <div class="panel-header" style="margin-bottom: 1.5rem;">
                <h2 class="panel-title" style="font-size: 1.25rem;">Edit Orphan Care Application</h2>
            </div>

            <form id="editAppForm" action="" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Hidden Category and redirect tags -->
                <input type="hidden" name="category" value="{{ $categoryName }}">
                <input type="hidden" name="redirect_category" value="{{ $categorySlug }}">
                <input type="hidden" name="amount_requested" value="0">

                <!-- Form Section 1: Orphan & Family Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">1. Orphan & Family Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_applicant_name">Name Of Orphan *</label>
                            <input type="text" class="form-control-dark" id="edit_applicant_name" name="applicant_name" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_father_name">Name Of Father *</label>
                            <input type="text" class="form-control-dark" id="edit_father_name" name="meta[father_name]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_grandfather_name">Name Of GrandFather</label>
                            <input type="text" class="form-control-dark" id="edit_grandfather_name" name="meta[grandfather_name]">
                        </div>
                        <div>
                            <label class="form-label" for="edit_mother_name">Name Of Mother *</label>
                            <input type="text" class="form-control-dark" id="edit_mother_name" name="meta[mother_name]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_mothers_father_name">Name Of MothersFather</label>
                            <input type="text" class="form-control-dark" id="edit_mothers_father_name" name="meta[mothers_father_name]">
                        </div>
                        <div>
                            <label class="form-label" for="edit_gender">Male/Female *</label>
                            <select class="form-select-dark" id="edit_gender" name="meta[gender]" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1.5fr 1fr 2fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_dob">Date of Birth *</label>
                            <input type="date" class="form-control-dark" id="edit_dob" name="meta[dob]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_age">Age *</label>
                            <input type="number" class="form-control-dark" id="edit_age" name="meta[age]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_aadhar_number">Aadhar Number *</label>
                            <input type="text" class="form-control-dark" id="edit_aadhar_number" name="meta[aadhar_number]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="edit_guardian_name">Name of Present Guardian *</label>
                            <input type="text" class="form-control-dark" id="edit_guardian_name" name="meta[guardian_name]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_guardian_relation">Relation with Orphan *</label>
                            <input type="text" class="form-control-dark" id="edit_guardian_relation" name="meta[guardian_relation]" required>
                        </div>
                    </div>
                </div>

                <!-- Form Section 2: Parental Death & Sibling Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">2. Parental Death & Sibling Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_father_death_date">Date of Death(Father) *</label>
                            <input type="text" class="form-control-dark" id="edit_father_death_date" name="meta[father_death_date]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_father_death_cause">Cause Of Death *</label>
                            <input type="text" class="form-control-dark" id="edit_father_death_cause" name="meta[father_death_cause]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_mother_alive_status">Mother Alive/Not *</label>
                            <input type="text" class="form-control-dark" id="edit_mother_alive_status" name="meta[mother_alive_status]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_mother_death_date">If Not/Date of Death</label>
                            <input type="text" class="form-control-dark" id="edit_mother_death_date" name="meta[mother_death_date]">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_mother_death_cause">Cause Of Death (Mother)</label>
                            <input type="text" class="form-control-dark" id="edit_mother_death_cause" name="meta[mother_death_cause]">
                        </div>
                        <div>
                            <label class="form-label" for="edit_mother_remarried_status">Mother Re-Married/not *</label>
                            <input type="text" class="form-control-dark" id="edit_mother_remarried_status" name="meta[mother_remarried_status]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_siblings_total">No Of Brothers And Sisters *</label>
                            <input type="number" class="form-control-dark" id="edit_siblings_total" name="meta[siblings_total]" readonly required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_siblings_male">Male *</label>
                            <input type="number" class="form-control-dark" id="edit_siblings_male" name="meta[siblings_male]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_siblings_female">Female *</label>
                            <input type="number" class="form-control-dark" id="edit_siblings_female" name="meta[siblings_female]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="edit_monthly_income">Monthly Income ($) *</label>
                            <input type="number" class="form-control-dark" id="edit_monthly_income" name="meta[monthly_income]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_monthly_expense">Monthly Expense ($) *</label>
                            <input type="number" class="form-control-dark" id="edit_monthly_expense" name="meta[monthly_expense]" required>
                        </div>
                    </div>
                </div>

                <!-- Form Section 3: Education & Health Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">3. Education & Health Details</h4>
                    
                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" style="margin-bottom: 0.5rem; display: block;">Type Of House *</label>
                        <div style="display: flex; gap: 1.5rem; align-items: center; margin-top: 0.5rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; color: #ffffff; cursor: pointer;">
                                <input type="radio" id="edit_house_own" name="meta[house_type]" value="Own House" required> Own House
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; color: #ffffff; cursor: pointer;">
                                <input type="radio" id="edit_house_rental" name="meta[house_type]" value="Rental" required> Rental
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; color: #ffffff; cursor: pointer;">
                                <input type="radio" id="edit_house_flat" name="meta[house_type]" value="Flat" required> Flat
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; color: #ffffff; cursor: pointer;">
                                <input type="radio" id="edit_house_others" name="meta[house_type]" value="Others" required> Others
                            </label>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_school_name">Name Of School *</label>
                            <input type="text" class="form-control-dark" id="edit_school_name" name="meta[school_name]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_school_class">Class *</label>
                            <input type="text" class="form-control-dark" id="edit_school_class" name="meta[school_class]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_madrassa_name">Name Of Madrassa</label>
                            <input type="text" class="form-control-dark" id="edit_madrassa_name" name="meta[madrassa_name]">
                        </div>
                        <div>
                            <label class="form-label" for="edit_madrassa_class">Class</label>
                            <input type="text" class="form-control-dark" id="edit_madrassa_class" name="meta[madrassa_class]">
                        </div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="edit_not_studying_reason">If Not Studying, Reason</label>
                        <input type="text" class="form-control-dark" id="edit_not_studying_reason" name="meta[not_studying_reason]">
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="edit_health_status">Health Status *</label>
                        <input type="text" class="form-control-dark" id="edit_health_status" name="meta[health_status]" required>
                    </div>

                    <div>
                        <label class="form-label" for="edit_sponsorship_details">Sponsorship Details If Any</label>
                        <input type="text" class="form-control-dark" id="edit_sponsorship_details" name="meta[sponsorship_details]">
                    </div>
                </div>

                <!-- Form Section 4: Address & Contact Details -->
                <div style="margin-bottom: 2rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">4. Address & Contact Details</h4>
                    
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

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_town">Town *</label>
                            <input type="text" class="form-control-dark" id="edit_town" name="meta[town]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_post_office">Post Office *</label>
                            <input type="text" class="form-control-dark" id="edit_post_office" name="meta[post_office]" required>
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
                            <label class="form-label" for="edit_pin_code">Pin Code *</label>
                            <input type="text" class="form-control-dark" id="edit_pin_code" name="meta[pin_code]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_mobile_1">Mobile 1 *</label>
                            <input type="text" class="form-control-dark" id="edit_mobile_1" name="meta[mobile_1]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_mobile_2">Mobile 2</label>
                            <input type="text" class="form-control-dark" id="edit_mobile_2" name="meta[mobile_2]">
                        </div>
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
            document.getElementById('edit_grandfather_name').value = meta.grandfather_name || '';
            document.getElementById('edit_mother_name').value = meta.mother_name || '';
            document.getElementById('edit_mothers_father_name').value = meta.mothers_father_name || '';
            document.getElementById('edit_gender').value = meta.gender || 'Male';
            document.getElementById('edit_dob').value = meta.dob || '';
            document.getElementById('edit_age').value = meta.age || '';
            document.getElementById('edit_aadhar_number').value = meta.aadhar_number || '';
            document.getElementById('edit_guardian_name').value = meta.guardian_name || '';
            document.getElementById('edit_guardian_relation').value = meta.guardian_relation || '';

            document.getElementById('edit_father_death_date').value = meta.father_death_date || '';
            document.getElementById('edit_father_death_cause').value = meta.father_death_cause || '';
            document.getElementById('edit_mother_alive_status').value = meta.mother_alive_status || '';
            document.getElementById('edit_mother_death_date').value = meta.mother_death_date || '';
            document.getElementById('edit_mother_death_cause').value = meta.mother_death_cause || '';
            document.getElementById('edit_mother_remarried_status').value = meta.mother_remarried_status || '';
            document.getElementById('edit_siblings_male').value = meta.siblings_male || '';
            document.getElementById('edit_siblings_female').value = meta.siblings_female || '';
            document.getElementById('edit_siblings_total').value = meta.siblings_total || '';
            document.getElementById('edit_monthly_income').value = meta.monthly_income || '';
            document.getElementById('edit_monthly_expense').value = meta.monthly_expense || '';

            // Radio buttons mapping
            if (meta.house_type === 'Own House') {
                document.getElementById('edit_house_own').checked = true;
            } else if (meta.house_type === 'Rental') {
                document.getElementById('edit_house_rental').checked = true;
            } else if (meta.house_type === 'Flat') {
                document.getElementById('edit_house_flat').checked = true;
            } else if (meta.house_type === 'Others') {
                document.getElementById('edit_house_others').checked = true;
            }

            document.getElementById('edit_school_name').value = meta.school_name || '';
            document.getElementById('edit_school_class').value = meta.school_class || '';
            document.getElementById('edit_madrassa_name').value = meta.madrassa_name || '';
            document.getElementById('edit_madrassa_class').value = meta.madrassa_class || '';
            document.getElementById('edit_not_studying_reason').value = meta.not_studying_reason || '';
            document.getElementById('edit_health_status').value = meta.health_status || '';
            document.getElementById('edit_sponsorship_details').value = meta.sponsorship_details || '';

            document.getElementById('edit_house_name').value = meta.house_name || '';
            document.getElementById('edit_place').value = meta.place || '';
            document.getElementById('edit_town').value = meta.town || '';
            document.getElementById('edit_post_office').value = meta.post_office || '';
            document.getElementById('edit_district').value = meta.district || '';
            document.getElementById('edit_state').value = meta.state || '';
            document.getElementById('edit_pin_code').value = meta.pin_code || '';
            document.getElementById('edit_mobile_1').value = meta.mobile_1 || '';
            document.getElementById('edit_mobile_2').value = meta.mobile_2 || '';

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
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">1. Orphan & Family Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px;">Orphan Name:</td><td style="font-weight: 600; color: #ffffff;">${formatVal(appItem.applicant_name)} (${formatVal(meta.gender)})</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Date of Birth / Age:</td><td>${formatVal(meta.dob)} / ${formatVal(meta.age)} yrs</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Aadhar Number:</td><td>${formatVal(meta.aadhar_number)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Father's Name:</td><td>${formatVal(meta.father_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Grandfather's Name:</td><td>${formatVal(meta.grandfather_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Mother's Name:</td><td>${formatVal(meta.mother_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Mother's Father Name:</td><td>${formatVal(meta.mothers_father_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Guardian / Relation:</td><td>${formatVal(meta.guardian_name)} (${formatVal(meta.guardian_relation)})</td></tr>
                        </table>

                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">2. Parental Death & Sibling Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px;">Father's Death Date:</td><td>${formatVal(meta.father_death_date)} <span style="font-size: 0.8rem; color: var(--text-muted);">(${formatVal(meta.father_death_cause)})</span></td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Mother Alive Status:</td><td>${formatVal(meta.mother_alive_status)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Mother's Death Date:</td><td>${formatVal(meta.mother_death_date)} <span style="font-size: 0.8rem; color: var(--text-muted);">(${formatVal(meta.mother_death_cause)})</span></td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Mother Re-Married?</td><td>${formatVal(meta.mother_remarried_status)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Brothers & Sisters:</td><td>Total: ${formatVal(meta.siblings_total)} (M: ${formatVal(meta.siblings_male)} / F: ${formatVal(meta.siblings_female)})</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Monthly Income:</td><td>${meta.monthly_income ? '$' + Number(meta.monthly_income).toLocaleString() : 'N/A'}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Monthly Expense:</td><td>${meta.monthly_expense ? '$' + Number(meta.monthly_expense).toLocaleString() : 'N/A'}</td></tr>
                        </table>
                    </div>

                    <!-- Col 2 -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">3. Education & House Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px;">Type Of House:</td><td>${formatVal(meta.house_type)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">School Name:</td><td>${formatVal(meta.school_name)} (Class: ${formatVal(meta.school_class)})</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Madrassa Name:</td><td>${formatVal(meta.madrassa_name)} (Class: ${formatVal(meta.madrassa_class)})</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">If Not Studying, Reason:</td><td>${formatVal(meta.not_studying_reason)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Health Status:</td><td>${formatVal(meta.health_status)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Sponsorship Details:</td><td>${formatVal(meta.sponsorship_details)}</td></tr>
                        </table>

                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">4. Address & Contact Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 150px;">House Name / Place:</td><td>${formatVal(meta.house_name)} / ${formatVal(meta.place)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Town / Post Office:</td><td>${formatVal(meta.town)} / ${formatVal(meta.post_office)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">District / State / Pin:</td><td>${formatVal(meta.district)} / ${formatVal(meta.state)} / ${formatVal(meta.pin_code)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Mobile 1 / 2:</td><td>${formatVal(meta.mobile_1)} / ${formatVal(meta.mobile_2)}</td></tr>
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

        // Realtime calculation of siblings count
        document.addEventListener("DOMContentLoaded", function() {
            // Add Modal
            const maleInput = document.getElementById('siblings_male');
            const femaleInput = document.getElementById('siblings_female');
            const totalInput = document.getElementById('siblings_total');

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
            const editMaleInput = document.getElementById('edit_siblings_male');
            const editFemaleInput = document.getElementById('edit_siblings_female');
            const editTotalInput = document.getElementById('edit_siblings_total');

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
