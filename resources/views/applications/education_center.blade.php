@extends('layouts.admin')

@section('title', 'Education Center Applications')

@section('content')

    <!-- Back Button Header -->
    <div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
        <a href="{{ route('applications.index') }}" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); padding: 0.5rem 1rem;">
            <i class="bx bx-left-arrow-alt"></i> Back to Dashboard
        </a>
        <h3 style="color: #ffffff; font-size: 1.25rem; font-weight: 600;">Education Center Registry</h3>
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
            <h2 class="panel-title">Education Center Applications List</h2>
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
                        <th>Name of Applicant</th>
                        <th>Place</th>
                        <th>Village</th>
                        <th>Panchayath</th>
                        <th>Requirement</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $appItem)
                        @php
                            $meta = $appItem->meta ?? [];
                            $appYear = !empty($appItem->created_at) ? date('y', strtotime($appItem->created_at)) : '24';
                            $appId = 'APLRCFI' . $appYear . 'EC' . str_pad($appItem->id, 5, '0', STR_PAD_LEFT);
                            
                            $searchTerms = [
                                $appId,
                                $appItem->applicant_name ?? '',
                                $appItem->place ?? '',
                                $appItem->village ?? $appItem->town ?? '',
                                $appItem->panchayat ?? $appItem->panchayath ?? '',
                                $appItem->status ?? '',
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

                            <!-- Village -->
                            <td>{{ $appItem->village ?? $appItem->town ?? 'N/A' }}</td>

                            <!-- Panchayath -->
                            <td>{{ $appItem->panchayat ?? $appItem->panchayath ?? 'N/A' }}</td>

                            <!-- Requirement -->
                            <td>{{ $meta['requirement'] ?? 'N/A' }}</td>

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

                                @if($appItem->status !== 'Approved' && Auth::user()->role == 2)
                                    @if($appItem->status === 'Pending')
                                        <!-- Approve -->
                                        <form action="{{ route('applications.approve', [$categorySlug, $appItem->id]) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            <button type="submit" class="btn-custom" style="background: transparent; color: var(--accent-green); border: 1px solid var(--accent-green); padding: 0.4rem; font-size: 1rem; border-radius: 6px; cursor: pointer; transition: all 0.2s; margin-right: 0.5rem; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px;" title="Approve">
                                                <i class="bx bx-check"></i>
                                            </button>
                                        </form>

                                        <!-- Reject -->
                                        <form action="{{ route('applications.reject', [$categorySlug, $appItem->id]) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to reject this application?');">
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
                            <td colspan="9" style="text-align: center; padding: 2rem;">No education center applications registered yet.</td>
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
                @if(Auth::user()->role == 2)
                    <span id="modal_status_actions" style="display: inline-flex; gap: 0.75rem;"></span>
                @endif
                @if(in_array(Auth::user()->role, [1, 2, 4]))
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
        <div class="panel" style="width: 100%; max-width: 700px; margin: 2rem auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #2a3547; max-height: 90vh; overflow-y: auto;" onclick="event.stopPropagation()">
            
            <button onclick="closeModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; z-index: 10;"><i class="bx bx-x"></i></button>
            
            <div class="panel-header" style="margin-bottom: 1.5rem;">
                <h2 class="panel-title" style="font-size: 1.25rem;">Add Education Center Application</h2>
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
                            <label class="form-label" for="reg_number">Reg. Number *</label>
                            <input type="text" class="form-control-dark" id="reg_number" name="meta[reg_number]" value="{{ old('meta.reg_number') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="year">Year *</label>
                            <input type="number" class="form-control-dark" id="year" name="meta[year]" value="{{ old('meta.year') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="location">Place *</label>
                            <input type="text" class="form-control-dark" id="location" name="meta[location]" value="{{ old('meta.location') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="village">Village *</label>
                            <input type="text" class="form-control-dark" id="village" name="meta[village]" value="{{ old('meta.village') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="post">Post *</label>
                            <input type="text" class="form-control-dark" id="post" name="meta[post]" value="{{ old('meta.post') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="panchayath">Panchayath *</label>
                            <input type="text" class="form-control-dark" id="panchayath" name="meta[panchayath]" value="{{ old('meta.panchayath') }}" required>
                        </div>
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
                            <label class="form-label" for="contact_number_1">Contact Number 1 *</label>
                            <input type="text" class="form-control-dark" id="contact_number_1" name="meta[contact_number_1]" value="{{ old('meta.contact_number_1') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="contact_number_2">Contact Number 2 *</label>
                            <input type="text" class="form-control-dark" id="contact_number_2" name="meta[contact_number_2]" value="{{ old('meta.contact_number_2') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="submitted_before">Submitted Application Before *</label>
                            <input type="text" class="form-control-dark" id="submitted_before" name="meta[submitted_before]" value="{{ old('meta.submitted_before') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="received_support_before">Received Financial Support from RCFI *</label>
                            <input type="text" class="form-control-dark" id="received_support_before" name="meta[received_support_before]" value="{{ old('meta.received_support_before') }}" required>
                        </div>
                    </div>
                </div>

                <!-- Form Section 2: Details of Locality -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">2. Details of Proposed Locality</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="mahallu_name">Name of the Mahallu *</label>
                            <input type="text" class="form-control-dark" id="mahallu_name" name="meta[mahallu_name]" value="{{ old('meta.mahallu_name') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="locality_location">Place *</label>
                            <input type="text" class="form-control-dark" id="locality_location" name="meta[locality_location]" value="{{ old('meta.locality_location') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="locality_village">Village *</label>
                            <input type="text" class="form-control-dark" id="locality_village" name="meta[locality_village]" value="{{ old('meta.locality_village') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="locality_district">District *</label>
                            <input type="text" class="form-control-dark" id="locality_district" name="meta[locality_district]" value="{{ old('meta.locality_district') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="locality_state">State *</label>
                            <input type="text" class="form-control-dark" id="locality_state" name="meta[locality_state]" value="{{ old('meta.locality_state') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="families_in_mahallu">No of Families in Mahallu *</label>
                            <input type="number" class="form-control-dark" id="families_in_mahallu" name="meta[families_in_mahallu]" value="{{ old('meta.families_in_mahallu') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="requirement">Requirement *</label>
                            <select class="form-select-dark" id="requirement" name="meta[requirement]" required>
                                <option value="New construction">New construction</option>
                                <option value="Repairing">Repairing</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Form Section 3: Current Status -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">3. Current Status & Students</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="site_has_building">Proposed Site Has Building *</label>
                            <select class="form-select-dark" id="site_has_building" name="meta[site_has_building]" required>
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="status_of_current_building">Status of Current Building *</label>
                            <input type="text" class="form-control-dark" id="status_of_current_building" name="meta[status_of_current_building]" placeholder="dilapidated, semi-completed, etc." value="{{ old('meta.status_of_current_building') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="students_boys">Number of Boys *</label>
                            <input type="number" class="form-control-dark" id="students_boys" name="meta[students_boys]" value="{{ old('meta.students_boys') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="students_girls">Number of Girls *</label>
                            <input type="number" class="form-control-dark" id="students_girls" name="meta[students_girls]" value="{{ old('meta.students_girls') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="education_center_nearby">Education Center Nearby *</label>
                            <input type="text" class="form-control-dark" id="education_center_nearby" name="meta[education_center_nearby]" value="{{ old('meta.education_center_nearby') ?? old('meta.cultural_center_nearby') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="distance_education_center">Distance to Education Center (KM) *</label>
                            <input type="number" step="0.1" class="form-control-dark" id="distance_education_center" name="meta[distance_education_center]" value="{{ old('meta.distance_education_center') ?? old('meta.distance_cultural_centre') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="syllabus">Syllabus *</label>
                            <input type="text" class="form-control-dark" id="syllabus" name="meta[syllabus]" value="{{ old('meta.syllabus') }}" required>
                        </div>
                        <input type="hidden" name="status" value="Pending">
                    </div>
                </div>

                <!-- Form Section 4: Proposed Project Details -->
                <div style="margin-bottom: 2rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">4. Proposed Project Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="project_type">Select Project Type *</label>
                            <select class="form-select-dark" id="project_type" name="meta[project_type]" required>
                                <option value="orphanage">Orphanage</option>
                                <option value="classroom">Classroom</option>
                                <option value="education acadamy">Education Academy</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="building_area_sq">Building Area (Sq. Ft) *</label>
                            <input type="text" class="form-control-dark" id="building_area_sq" name="meta[building_area_sq]" value="{{ old('meta.building_area_sq') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="land_area_sq">Land Area (Sq. Ft) *</label>
                            <input type="text" class="form-control-dark" id="land_area_sq" name="meta[land_area_sq]" value="{{ old('meta.land_area_sq') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="num_classrooms">Number of Classrooms *</label>
                            <input type="number" class="form-control-dark" id="num_classrooms" name="meta[num_classrooms]" value="{{ old('meta.num_classrooms') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="num_students">Number of Students *</label>
                            <input type="number" class="form-control-dark" id="num_students" name="meta[num_students]" value="{{ old('meta.num_students') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="proposed_budget">Proposed Budget (₹) *</label>
                            <input type="number" class="form-control-dark" id="proposed_budget" name="amount_requested" placeholder="Total Budget" value="{{ old('amount_requested') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="legal_approvals_status">Status of Legal Approvals *</label>
                            <input type="text" class="form-control-dark" id="legal_approvals_status" name="meta[legal_approvals_status]" value="{{ old('meta.legal_approvals_status') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="area">Area / Zone *</label>
                            <input type="text" class="form-control-dark" id="area" name="meta[area]" value="{{ old('meta.area') }}" required>
                        </div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="details">Additional Notes</label>
                        <textarea class="form-control-dark" id="details" name="details" style="height: 60px; resize: vertical;">{{ old('details') }}</textarea>
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
        <div class="panel" style="width: 100%; max-width: 700px; margin: 2rem auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #2a3547; max-height: 90vh; overflow-y: auto;" onclick="event.stopPropagation()">
            
            <button onclick="closeEditModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; z-index: 10;"><i class="bx bx-x"></i></button>
            
            <div class="panel-header" style="margin-bottom: 1.5rem;">
                <h2 class="panel-title" style="font-size: 1.25rem;">Edit Education Center Application Details</h2>
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
                            <label class="form-label" for="edit_reg_number">Reg. Number *</label>
                            <input type="text" class="form-control-dark" id="edit_reg_number" name="meta[reg_number]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_year">Year *</label>
                            <input type="number" class="form-control-dark" id="edit_year" name="meta[year]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_location">Place *</label>
                            <input type="text" class="form-control-dark" id="edit_location" name="meta[location]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_village">Village *</label>
                            <input type="text" class="form-control-dark" id="edit_village" name="meta[village]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_post">Post *</label>
                            <input type="text" class="form-control-dark" id="edit_post" name="meta[post]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_panchayath">Panchayath *</label>
                            <input type="text" class="form-control-dark" id="edit_panchayath" name="meta[panchayath]" required>
                        </div>
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
                            <label class="form-label" for="edit_contact_number_1">Contact Number 1 *</label>
                            <input type="text" class="form-control-dark" id="edit_contact_number_1" name="meta[contact_number_1]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_contact_number_2">Contact Number 2 *</label>
                            <input type="text" class="form-control-dark" id="edit_contact_number_2" name="meta[contact_number_2]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="edit_submitted_before">Submitted Application Before *</label>
                            <input type="text" class="form-control-dark" id="edit_submitted_before" name="meta[submitted_before]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_received_support_before">Received Financial Support from RCFI *</label>
                            <input type="text" class="form-control-dark" id="edit_received_support_before" name="meta[received_support_before]" required>
                        </div>
                    </div>
                </div>

                <!-- Form Section 2: Details of Locality -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">2. Details of Proposed Locality</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_mahallu_name">Name of the Mahallu *</label>
                            <input type="text" class="form-control-dark" id="edit_mahallu_name" name="meta[mahallu_name]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_locality_location">Place *</label>
                            <input type="text" class="form-control-dark" id="edit_locality_location" name="meta[locality_location]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_locality_village">Village *</label>
                            <input type="text" class="form-control-dark" id="edit_locality_village" name="meta[locality_village]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_locality_district">District *</label>
                            <input type="text" class="form-control-dark" id="edit_locality_district" name="meta[locality_district]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_locality_state">State *</label>
                            <input type="text" class="form-control-dark" id="edit_locality_state" name="meta[locality_state]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="edit_families_in_mahallu">No of Families in Mahallu *</label>
                            <input type="number" class="form-control-dark" id="edit_families_in_mahallu" name="meta[families_in_mahallu]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_requirement">Requirement *</label>
                            <select class="form-select-dark" id="edit_requirement" name="meta[requirement]" required>
                                <option value="New construction">New construction</option>
                                <option value="Repairing">Repairing</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Form Section 3: Current Status -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">3. Current Status & Students</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_site_has_building">Proposed Site Has Building *</label>
                            <select class="form-select-dark" id="edit_site_has_building" name="meta[site_has_building]" required>
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="edit_status_of_current_building">Status of Current Building *</label>
                            <input type="text" class="form-control-dark" id="edit_status_of_current_building" name="meta[status_of_current_building]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_students_boys">Number of Boys *</label>
                            <input type="number" class="form-control-dark" id="edit_students_boys" name="meta[students_boys]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_students_girls">Number of Girls *</label>
                            <input type="number" class="form-control-dark" id="edit_students_girls" name="meta[students_girls]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_education_center_nearby">Education Center Nearby *</label>
                            <input type="text" class="form-control-dark" id="edit_education_center_nearby" name="meta[education_center_nearby]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_distance_cultural_centre">Distance to Cultural Center (KM) *</label>
                            <input type="number" step="0.1" class="form-control-dark" id="edit_distance_cultural_centre" name="meta[distance_cultural_centre]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_syllabus">Syllabus *</label>
                            <input type="text" class="form-control-dark" id="edit_syllabus" name="meta[syllabus]" required>
                        </div>
                    </div>
                    <input type="hidden" name="status" id="edit_status">
                </div>

                <!-- Form Section 4: Proposed Project Details -->
                <div style="margin-bottom: 2rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">4. Proposed Project Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_project_type">Select Project Type *</label>
                            <select class="form-select-dark" id="edit_project_type" name="meta[project_type]" required>
                                <option value="orphanage">Orphanage</option>
                                <option value="classroom">Classroom</option>
                                <option value="education acadamy">Education Academy</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="edit_building_area_sq">Building Area (Sq. Ft) *</label>
                            <input type="text" class="form-control-dark" id="edit_building_area_sq" name="meta[building_area_sq]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_land_area_sq">Land Area (Sq. Ft) *</label>
                            <input type="text" class="form-control-dark" id="edit_land_area_sq" name="meta[land_area_sq]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_num_classrooms">Number of Classrooms *</label>
                            <input type="number" class="form-control-dark" id="edit_num_classrooms" name="meta[num_classrooms]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_num_students">Number of Students *</label>
                            <input type="number" class="form-control-dark" id="edit_num_students" name="meta[num_students]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_proposed_budget">Proposed Budget (₹) *</label>
                            <input type="number" class="form-control-dark" id="edit_proposed_budget" name="amount_requested" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_legal_approvals_status">Status of Legal Approvals *</label>
                            <input type="text" class="form-control-dark" id="edit_legal_approvals_status" name="meta[legal_approvals_status]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_area">Area / Zone *</label>
                            <input type="text" class="form-control-dark" id="edit_area" name="meta[area]" required>
                        </div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="edit_details">Additional Notes</label>
                        <textarea class="form-control-dark" id="edit_details" name="details" style="height: 60px; resize: vertical;"></textarea>
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

            // Base fields
            document.getElementById('edit_applicant_name').value = appItem.applicant_name;
            document.getElementById('edit_proposed_budget').value = appItem.amount_requested || '';
            document.getElementById('edit_status').value = appItem.status;
            document.getElementById('edit_details').value = appItem.details || '';

            // Meta fields mapping
            const meta = appItem.meta || {};
            document.getElementById('edit_committee_name').value = meta.committee_name || '';
            document.getElementById('edit_reg_number').value = meta.reg_number || '';
            document.getElementById('edit_year').value = meta.year || '';
                        if (document.getElementById('edit_house_name')) { document.getElementById('edit_house_name').value = appItem.house_name || ''; }
            if (document.getElementById('edit_place')) { document.getElementById('edit_place').value = appItem.place || ''; }
            if (document.getElementById('edit_post_office')) { document.getElementById('edit_post_office').value = appItem.post_office || ''; }
            if (document.getElementById('edit_village')) { document.getElementById('edit_village').value = appItem.village || ''; }
            if (document.getElementById('edit_panchayat')) { document.getElementById('edit_panchayat').value = appItem.panchayat || ''; }
            if (document.getElementById('edit_district')) { document.getElementById('edit_district').value = appItem.district || ''; }
            if (document.getElementById('edit_state')) { document.getElementById('edit_state').value = appItem.state || ''; }
            if (document.getElementById('edit_pin_code')) { document.getElementById('edit_pin_code').value = appItem.pin_code || ''; }
            document.getElementById('edit_contact_number_1').value = meta.contact_number_1 || '';
            document.getElementById('edit_contact_number_2').value = meta.contact_number_2 || '';
            document.getElementById('edit_submitted_before').value = meta.submitted_before || '';
            document.getElementById('edit_received_support_before').value = meta.received_support_before || '';
            
            document.getElementById('edit_mahallu_name').value = meta.mahallu_name || '';
            document.getElementById('edit_locality_location').value = meta.locality_location || '';
            document.getElementById('edit_locality_village').value = meta.locality_village || '';
            document.getElementById('edit_locality_district').value = meta.locality_district || '';
            document.getElementById('edit_locality_state').value = meta.locality_state || '';
            document.getElementById('edit_families_in_mahallu').value = meta.families_in_mahallu || '';
            document.getElementById('edit_requirement').value = meta.requirement || 'New construction';

            document.getElementById('edit_site_has_building').value = meta.site_has_building || 'No';
            document.getElementById('edit_status_of_current_building').value = meta.status_of_current_building || '';
            document.getElementById('edit_students_boys').value = meta.students_boys || '';
            document.getElementById('edit_students_girls').value = meta.students_girls || '';
            document.getElementById('edit_education_center_nearby').value = meta.education_center_nearby || '';
            document.getElementById('edit_distance_cultural_centre').value = meta.distance_cultural_centre || '';
            document.getElementById('edit_syllabus').value = meta.syllabus || '';

            document.getElementById('edit_project_type').value = meta.project_type || 'orphanage';
            document.getElementById('edit_building_area_sq').value = meta.building_area_sq || '';
            document.getElementById('edit_land_area_sq').value = meta.land_area_sq || '';
            document.getElementById('edit_num_classrooms').value = meta.num_classrooms || '';
            document.getElementById('edit_num_students').value = meta.num_students || '';
            document.getElementById('edit_legal_approvals_status').value = meta.legal_approvals_status || '';
            document.getElementById('edit_area').value = meta.area || '';

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
                const approveUrl = `/admin/applications/{{ $categorySlug }}/${appItem.id}/approve`;
                const rejectUrl = `/admin/applications/{{ $categorySlug }}/${appItem.id}/reject`;
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                if (appItem.status === 'Pending') {
                    statusHtml = `
                        <form action="${approveUrl}" method="POST" style="display: inline-block;">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <button type="submit" class="btn-custom" style="background: linear-gradient(135deg, #2ecc71, #27ae60); padding: 0.6rem 1.5rem; display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                                <i class="bx bx-check"></i> Approve
                            </button>
                        </form>
                        <form action="${rejectUrl}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to reject this application?');">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <button type="submit" class="btn-danger-custom" style="padding: 0.6rem 1.5rem; display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                                <i class="bx bx-x"></i> Reject
                            </button>
                        </form>
                    `;
                } else if (appItem.status === 'Approved') {
                    statusHtml = `
                        <form action="${rejectUrl}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to reject this approved application?');">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <button type="submit" class="btn-danger-custom" style="padding: 0.6rem 1.5rem; display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                                <i class="bx bx-x"></i> Reject Application
                            </button>
                        </form>
                    `;
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
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <!-- Col 1 -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">1. Applicant & Committee</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">Applicant Name:</td><td>${formatVal(appItem.applicant_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Committee Name:</td><td>${formatVal(meta.committee_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Reg. Number:</td><td>${formatVal(meta.reg_number)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Year:</td><td>${formatVal(meta.year)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Place:</td><td>${formatVal(meta.location)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Village:</td><td>${formatVal(meta.village)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Post:</td><td>${formatVal(meta.post)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Panchayath:</td><td>${formatVal(meta.panchayath)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">District / State:</td><td>${formatVal(meta.district)} / ${formatVal(meta.state)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Contact 1 / 2:</td><td>${formatVal(meta.contact_number_1)} / ${formatVal(meta.contact_number_2)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Submitted Before?</td><td>${formatVal(meta.submitted_before)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">RCFI Support?</td><td>${formatVal(meta.received_support_before)}</td></tr>
                        </table>

                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">2. Mahallu Locality Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">Mahallu Name:</td><td>${formatVal(meta.mahallu_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Place:</td><td>${formatVal(meta.locality_location)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Village:</td><td>${formatVal(meta.locality_village)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">District / State:</td><td>${formatVal(meta.locality_district)} / ${formatVal(meta.locality_state)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Families Count:</td><td>${formatVal(meta.families_in_mahallu)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Requirement:</td><td>${formatVal(meta.requirement)}</td></tr>
                        </table>
                    </div>

                    <!-- Col 2 -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">3. Current Status & Students</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">Has Building?</td><td>${formatVal(meta.site_has_building)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Building Status:</td><td>${formatVal(meta.status_of_current_building)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Boys Count:</td><td>${formatVal(meta.students_boys)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Girls Count:</td><td>${formatVal(meta.students_girls)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Center Nearby?</td><td>${formatVal(meta.education_center_nearby)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Distance to CC (KM):</td><td>${formatVal(meta.distance_cultural_centre)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Syllabus:</td><td>${formatVal(meta.syllabus)}</td></tr>
                        </table>

                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">4. Proposed Project Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">Project Type:</td><td style="text-transform: capitalize; font-weight: 600; color: #ffffff;">${formatVal(meta.project_type)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Building Area (Sq):</td><td>${formatVal(meta.building_area_sq)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Land Area (Sq):</td><td>${formatVal(meta.land_area_sq)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Classrooms Count:</td><td>${formatVal(meta.num_classrooms)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Proposed Students:</td><td>${formatVal(meta.num_students)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Proposed Budget:</td><td style="color: var(--accent-green); font-weight: 600;">${appItem.amount_requested ? '₹' + Number(appItem.amount_requested).toLocaleString() : 'N/A'}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Legal Approvals:</td><td>${formatVal(meta.legal_approvals_status)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Area / Zone:</td><td>${formatVal(meta.area)}</td></tr>
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

                let currentDetailsAppItem = null;

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
                    form.submit();
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
