@extends('layouts.admin')

@section('title', 'Orphan Care Applications')

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
            <h2 class="panel-title">Orphan Care Applications List</h2>
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
                        <th>Orphan Name</th>
                        <th>Father Name</th>
                        <th>Mother Name</th>
                        <th>Gender</th>
                        <th>Age</th>
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
                            $appId = 'APLRCFI' . $appYear . 'OC' . str_pad($appItem->id, 5, '0', STR_PAD_LEFT);
                            
                            $searchTerms = [
                                $appId,
                                $appItem->applicant_name ?? '',
                                $appItem->place ?? '',
                                $appItem->district ?? '',
                                $appItem->state ?? '',
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

                            <!-- Orphan Name -->
                            <td style="font-weight: 600; color: #ffffff;">{{ $appItem->applicant_name }}</td>

                            <!-- Father Name -->
                            <td>{{ $meta['father_name'] ?? 'N/A' }}</td>

                            <!-- Mother Name -->
                            <td>{{ $meta['mother_name'] ?? 'N/A' }}</td>

                            <!-- Gender -->
                            <td>{{ $meta['gender'] ?? 'N/A' }}</td>

                            <!-- Age -->
                            <td>{{ $meta['age'] ?? 'N/A' }}</td>

                            <!-- Place -->
                            <td>{{ $appItem->place ?? $meta['place'] ?? 'N/A' }}</td>

                            <!-- District -->
                            <td>{{ $appItem->district ?? $meta['district'] ?? 'N/A' }}</td>

                            <!-- Project Type -->
                            <td>{{ !empty($meta['project_type']) ? ucwords($meta['project_type']) : (!empty($appItem->project_type) ? ucwords($appItem->project_type) : 'Orphan Care') }}</td>

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
                            <td colspan="11" style="text-align: center; padding: 2rem;">No orphan care applications registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- View Full Details Modal Dialog -->
    <div id="detailsAppModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.75); display: none; align-items: center; justify-content: center; z-index: 1100; overflow-y: auto;" onclick="closeDetailsModal()">
        <div class="panel" style="width: 100%; max-width: 920px; margin: 2rem auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #2a3547; max-height: 90vh; overflow-y: auto;" onclick="event.stopPropagation()">
            
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
                <h2 class="panel-title" style="font-size: 1.25rem;">Add Orphan Care Application</h2>
            </div>

            <form action="{{ route('applications.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Hidden Category and redirect tags -->
                <input type="hidden" name="category" value="{{ $categoryName }}">
                <input type="hidden" name="redirect_category" value="{{ $categorySlug }}">
                <input type="hidden" name="amount_requested" value="0">

                <!-- Form Section 1: Orphan & Family Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1.25rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">1. Orphan & Family Details</h4>
                    
                    <!-- Top Split Layout: Left (Inputs 2x2 Grid) | Right (Fixed 175px Student Photo Card) -->
                    <div style="display: flex; gap: 1.25rem; margin-bottom: 1rem; align-items: flex-start; flex-wrap: wrap;">
                        <!-- Left Column: 2x2 Inputs Grid -->
                        <div style="flex: 1; min-width: 280px; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div>
                                <label class="form-label" for="applicant_name">Name Of Orphan *</label>
                                <input type="text" class="form-control-dark" id="applicant_name" name="applicant_name" value="{{ old('applicant_name') }}" required>
                            </div>
                            <div>
                                <label class="form-label" style="display: block; margin-bottom: 0.5rem;">Male/Female *</label>
                                <div style="display: flex; gap: 1.5rem; align-items: center; padding: 0.45rem 0;">
                                    <label style="display: flex; align-items: center; gap: 0.4rem; cursor: pointer; color: var(--text-main); font-size: 0.88rem; font-weight: 500;">
                                        <input type="radio" id="gender_male" name="meta[gender]" value="Male" {{ old('meta.gender', 'Male') === 'Male' ? 'checked' : '' }}>
                                        Male
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 0.4rem; cursor: pointer; color: var(--text-main); font-size: 0.88rem; font-weight: 500;">
                                        <input type="radio" id="gender_female" name="meta[gender]" value="Female" {{ old('meta.gender') === 'Female' ? 'checked' : '' }}>
                                        Female
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label class="form-label" for="father_name">Name Of Father *</label>
                                <input type="text" class="form-control-dark" id="father_name" name="meta[father_name]" value="{{ old('meta.father_name') }}" required>
                            </div>
                            <div>
                                <label class="form-label" for="grandfather_name">Name Of GrandFather</label>
                                <input type="text" class="form-control-dark" id="grandfather_name" name="meta[grandfather_name]" value="{{ old('meta.grandfather_name') }}">
                            </div>
                        </div>

                        <!-- Right Column: Fixed-Width Top-Aligned Student Photo Card (175px Wide, Passport 3:4 Frame) -->
                        <div style="width: 175px; flex-shrink: 0; align-self: flex-start; margin-top: -6px; border: 1px solid var(--panel-border); border-radius: 12px; padding: 0.55rem 0.65rem; background: rgba(255,255,255,0.03); text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: flex; flex-direction: column; align-items: center;">
                            <h5 style="color: #00a65a; font-weight: 700; font-size: 0.8rem; letter-spacing: 0.05em; margin: 0 0 0.5rem 0; text-transform: uppercase;">STUDENT PHOTO</h5>
                            
                            <div id="add_photo_preview_box" style="width: 105px; height: 135px; border: 2px dashed #00a65a; border-radius: 10px; padding: 0.2rem; display: flex; flex-direction: column; align-items: center; justify-content: center; background: transparent; overflow: hidden; position: relative; margin: 0 auto 0.6rem auto;">
                                <i id="add_photo_icon" class="bx bx-image-add" style="font-size: 2.2rem; color: #00a65a; margin-bottom: 0.2rem;"></i>
                                <span id="add_photo_text" style="color: var(--text-muted); font-size: 0.7rem; font-weight: 500; text-align: center; line-height: 1.2;">No Photo<br>Uploaded</span>
                                <img id="add_photo_img" src="" style="display: none; width: 100%; height: 100%; border-radius: 8px; object-fit: cover;">
                                <button type="button" id="add_photo_trash_overlay" onclick="removeStudentPhoto('add')" style="display: none; position: absolute; top: 4px; right: 4px; background: #dc3545; color: #ffffff; border: none; border-radius: 50%; width: 24px; height: 24px; cursor: pointer; align-items: center; justify-content: center; z-index: 10; box-shadow: 0 2px 4px rgba(0,0,0,0.3);" title="Remove Photo">
                                    <i class="bx bx-trash" style="font-size: 0.82rem; color: #ffffff;"></i>
                                </button>
                            </div>

                            <div style="display: flex; gap: 0.5rem; width: 100%;">
                                <label for="add_student_photo" class="btn-custom" style="display: flex; align-items: center; justify-content: center; gap: 0.3rem; flex: 1; padding: 0.45rem 0.25rem; background: #00a65a !important; color: #ffffff !important; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 0.78rem; border: none; margin: 0; transition: all 0.2s; box-shadow: 0 2px 6px rgba(0,166,90,0.3);">
                                    <i class="bx bx-upload" style="font-size: 0.9rem; color: #ffffff !important;"></i> <span style="color: #ffffff !important;">Upload</span>
                                </label>
                                <button type="button" id="add_photo_remove_btn" onclick="removeStudentPhoto('add')" style="display: none; align-items: center; justify-content: center; gap: 0.3rem; flex: 1; padding: 0.45rem 0.25rem; background: #dc3545 !important; color: #ffffff !important; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 0.78rem; border: none; margin: 0; transition: all 0.2s; box-shadow: 0 2px 6px rgba(220,53,69,0.3);" title="Delete Photo">
                                    <i class="bx bx-trash" style="font-size: 0.9rem; color: #ffffff !important;"></i> <span style="color: #ffffff !important;">Delete</span>
                                </button>
                            </div>
                            <input type="file" id="add_student_photo" name="student_photo" accept="image/*" style="display: none;" onchange="previewStudentPhoto(this, 'add')">
                            <input type="hidden" id="add_photo_hidden" name="meta[student_photo]" value="{{ old('meta.student_photo') }}">
                        </div>
                    </div>

                    <!-- Flowing Form Fields below top split section -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="mother_name">Name Of Mother *</label>
                            <input type="text" class="form-control-dark" id="mother_name" name="meta[mother_name]" value="{{ old('meta.mother_name') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="mothers_father_name">Name Of MothersFather</label>
                            <input type="text" class="form-control-dark" id="mothers_father_name" name="meta[mothers_father_name]" value="{{ old('meta.mothers_father_name') }}">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="dob">Date of Birth *</label>
                            <input type="date" class="form-control-dark" id="dob" name="meta[dob]" value="{{ old('meta.dob') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="age">Age *</label>
                            <input type="number" class="form-control-dark" id="age" name="meta[age]" value="{{ old('meta.age') }}" readonly style="background-color: rgba(255, 255, 255, 0.05); cursor: not-allowed;" required>
                        </div>
                        <div>
                            <label class="form-label" for="aadhar_number">Aadhaar Number *</label>
                            <input type="text" class="form-control-dark" id="aadhar_number" name="meta[aadhar_number]" value="{{ old('meta.aadhar_number') }}" placeholder="XXXX XXXX XXXX" maxlength="14" required>
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
                            <input type="date" class="form-control-dark" id="father_death_date" name="meta[father_death_date]" value="{{ old('meta.father_death_date') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="father_death_cause">Cause Of Death *</label>
                            <input type="text" class="form-control-dark" id="father_death_cause" name="meta[father_death_cause]" value="{{ old('meta.father_death_cause') }}" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" style="display: block; margin-bottom: 0.5rem;">Mother Alive/Not *</label>
                            <div style="display: flex; gap: 1.5rem; align-items: center; padding: 0.45rem 0;">
                                <label style="display: flex; align-items: center; gap: 0.4rem; cursor: pointer; color: var(--text-main); font-size: 0.88rem; font-weight: 500;">
                                    <input type="radio" id="mother_alive_yes" name="meta[mother_alive_status]" value="Yes" {{ old('meta.mother_alive_status', 'Yes') == 'Yes' ? 'checked' : '' }} onchange="toggleMotherDeathFieldsAdd()">
                                    Yes
                                </label>
                                <label style="display: flex; align-items: center; gap: 0.4rem; cursor: pointer; color: var(--text-main); font-size: 0.88rem; font-weight: 500;">
                                    <input type="radio" id="mother_alive_no" name="meta[mother_alive_status]" value="No" {{ old('meta.mother_alive_status') == 'No' ? 'checked' : '' }} onchange="toggleMotherDeathFieldsAdd()">
                                    No
                                </label>
                            </div>
                        </div>
                        <div id="add_mother_remarried_wrapper" style="display: {{ old('meta.mother_alive_status', 'Yes') == 'Yes' ? 'block' : 'none' }};">
                            <label class="form-label" for="mother_remarried_status">Mother Re-Married/not *</label>
                            <input type="text" class="form-control-dark" id="mother_remarried_status" name="meta[mother_remarried_status]" placeholder="e.g. Yes / No" value="{{ old('meta.mother_remarried_status') }}" required>
                        </div>
                    </div>

                    <!-- Conditional Mother Death Details -->
                    <div id="add_mother_death_fields" style="display: {{ old('meta.mother_alive_status') == 'No' ? 'grid' : 'none' }}; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="mother_death_date">If Not/Date of Death</label>
                            <input type="date" class="form-control-dark" id="mother_death_date" name="meta[mother_death_date]" value="{{ old('meta.mother_death_date') }}">
                        </div>
                        <div>
                            <label class="form-label" for="mother_death_cause">Cause Of Death (Mother)</label>
                            <input type="text" class="form-control-dark" id="mother_death_cause" name="meta[mother_death_cause]" placeholder="e.g. Cause" value="{{ old('meta.mother_death_cause') }}">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1.5fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="siblings_male">Brothers *</label>
                            <input type="number" class="form-control-dark" id="siblings_male" name="meta[siblings_male]" value="{{ old('meta.siblings_male') }}" min="0" placeholder="0" required>
                        </div>
                        <div>
                            <label class="form-label" for="siblings_female">Sisters *</label>
                            <input type="number" class="form-control-dark" id="siblings_female" name="meta[siblings_female]" value="{{ old('meta.siblings_female') }}" min="0" placeholder="0" required>
                        </div>
                        <div>
                            <label class="form-label" for="siblings_total">No Of Brothers And Sisters *</label>
                            <input type="number" class="form-control-dark" id="siblings_total" name="meta[siblings_total]" value="{{ old('meta.siblings_total', 0) }}" readonly style="background-color: rgba(255, 255, 255, 0.05); cursor: not-allowed;" required>
                        </div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="current_beneficiaries">Current Beneficiaries *</label>
                        <input type="number" class="form-control-dark" id="current_beneficiaries" name="meta[current_beneficiaries]" value="{{ old('meta.current_beneficiaries') }}" min="0" placeholder="Enter number of current beneficiaries" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="monthly_income">Monthly Income (₹) *</label>
                            <input type="number" class="form-control-dark" id="monthly_income" name="meta[monthly_income]" value="{{ old('meta.monthly_income') }}" required>
                        </div>
                        <div>
                            <label class="form-label" for="monthly_expense">Monthly Expense (₹) *</label>
                            <input type="number" class="form-control-dark" id="monthly_expense" name="meta[monthly_expense]" value="{{ old('meta.monthly_expense') }}" required>
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="sponsorship_details">Sponsorship Details If Any</label>
                        <input type="text" class="form-control-dark" id="sponsorship_details" name="meta[sponsorship_details]" placeholder="Enter sponsorship info or 'None'" value="{{ old('meta.sponsorship_details') }}">
                    </div>
                </div>

                <!-- Form Section 3: Education & Health Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">3. Education & Health Details</h4>
                    
                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" style="margin-bottom: 0.5rem; display: block;">Type Of House *</label>
                        <div style="display: flex; gap: 1.5rem; align-items: center; margin-top: 0.5rem; flex-wrap: wrap;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; color: #ffffff; cursor: pointer;">
                                <input type="radio" name="meta[house_type]" value="Own House" required {{ old('meta.house_type') === 'Own House' ? 'checked' : '' }}> Own House
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; color: #ffffff; cursor: pointer;">
                                <input type="radio" name="meta[house_type]" value="Family House" required {{ old('meta.house_type') === 'Family House' ? 'checked' : '' }}> Family House
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
                            <label class="form-label" for="school_name">Name Of School</label>
                            <input type="text" class="form-control-dark" id="school_name" name="meta[school_name]" value="{{ old('meta.school_name') }}">
                        </div>
                        <div>
                            <label class="form-label" for="school_class">Class</label>
                            <input type="number" min="1" max="12" class="form-control-dark" id="school_class" name="meta[school_class]" value="{{ old('meta.school_class') }}" placeholder="e.g. 5">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="madrassa_name">Name Of Madrassa</label>
                            <input type="text" class="form-control-dark" id="madrassa_name" name="meta[madrassa_name]" value="{{ old('meta.madrassa_name') }}">
                        </div>
                        <div>
                            <label class="form-label" for="madrassa_class">Class</label>
                            <input type="number" min="1" max="12" class="form-control-dark" id="madrassa_class" name="meta[madrassa_class]" value="{{ old('meta.madrassa_class') }}" placeholder="e.g. 5">
                        </div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="not_studying_reason">If Not Studying, Reason</label>
                        <input type="text" class="form-control-dark" id="not_studying_reason" name="meta[not_studying_reason]" placeholder="Enter reason or 'N/A'" value="{{ old('meta.not_studying_reason') }}">
                    </div>

                    <div>
                        <label class="form-label" for="health_status">Health Status *</label>
                        <input type="text" class="form-control-dark" id="health_status" name="meta[health_status]" value="{{ old('meta.health_status') }}" required>
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

                <!-- Form Section 5: Recommendation Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">5. Recommendation Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="recommender_name">Name</label>
                            <input type="text" class="form-control-dark" id="recommender_name" name="meta[recommender_name]" value="{{ old('meta.recommender_name') }}" placeholder="Enter recommender's name">
                        </div>
                        <div>
                            <label class="form-label" for="recommender_org_select">Organization</label>
                            <select class="form-select-dark" id="recommender_org_select" name="meta[recommender_org]" onchange="toggleRecommenderOrgAdd()" style="width: 100%; padding: 0.6rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: #111c2d; color: #ffffff;">
                                <option value="">-- Select Organization --</option>
                                <option value="KMJ" {{ old('meta.recommender_org') == 'KMJ' ? 'selected' : '' }}>KMJ</option>
                                <option value="SYS" {{ old('meta.recommender_org') == 'SYS' ? 'selected' : '' }}>SYS</option>
                                <option value="SSF" {{ old('meta.recommender_org') == 'SSF' ? 'selected' : '' }}>SSF</option>
                                <option value="Others" {{ old('meta.recommender_org') && !in_array(old('meta.recommender_org'), ['KMJ', 'SYS', 'SSF']) ? 'selected' : '' }}>Others</option>
                            </select>
                            <input type="text" class="form-control-dark" id="recommender_org_text" placeholder="Enter Organization Name" value="{{ old('meta.recommender_org') && !in_array(old('meta.recommender_org'), ['KMJ', 'SYS', 'SSF']) ? old('meta.recommender_org') : '' }}" style="display: {{ old('meta.recommender_org') && !in_array(old('meta.recommender_org'), ['KMJ', 'SYS', 'SSF']) ? 'block' : 'none' }}; margin-top: 0.5rem;" {{ old('meta.recommender_org') && !in_array(old('meta.recommender_org'), ['KMJ', 'SYS', 'SSF']) ? 'name=meta[recommender_org]' : 'disabled' }}>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="recommender_phone">Phone Number</label>
                            <input type="tel" class="form-control-dark" id="recommender_phone" name="meta[recommender_phone]" value="{{ old('meta.recommender_phone') }}" placeholder="Enter 10-digit phone number" maxlength="10" inputmode="numeric" pattern="[0-9]{10}">
                        </div>
                        <div>
                            <label class="form-label" for="recommender_position">Position</label>
                            <input type="text" class="form-control-dark" id="recommender_position" name="meta[recommender_position]" value="{{ old('meta.recommender_position') }}" placeholder="e.g. President, Secretary">
                        </div>
                    </div>
                </div>

                <!-- Form Section 6: Cluster & Agency Details -->
                <div style="margin-bottom: 2rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">6. Cluster & Agency Details (Optional)</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="cluster_search_input">Cluster</label>
                            <input type="text" class="form-control-dark" id="cluster_search_input" list="cluster_options_list" placeholder="Search or select cluster..." onchange="onClusterSelect(this, 'cluster_id')" oninput="onClusterSelect(this, 'cluster_id')" autocomplete="off">
                            <datalist id="cluster_options_list">
                                @foreach($clusters as $cl)
                                    <option value="{{ $cl->name }} ({{ $cl->code }})" data-id="{{ $cl->id }}">
                                @endforeach
                            </datalist>
                            <input type="hidden" id="cluster_id" name="cluster_id" value="{{ old('cluster_id') }}">
                        </div>
                        <div>
                            <label class="form-label" for="agency_number">Agency Number</label>
                            <input type="text" class="form-control-dark" id="agency_number" name="agency_number" value="{{ old('agency_number') }}" placeholder="Enter Agency Number">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="agency_name">Agency Name (Donor)</label>
                            <select class="form-select-dark" id="agency_name" name="meta[agency_name]" style="width: 100%; padding: 0.6rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: #111c2d; color: #ffffff;">
                                <option value="">-- Select Agency --</option>
                                @php
                                    $donorsList = $donors ?? \App\Models\Donor::orderBy('name', 'asc')->get();
                                @endphp
                                @foreach($donorsList as $dn)
                                    <option value="{{ $dn->name }}" {{ old('meta.agency_name') == $dn->name ? 'selected' : '' }}>
                                        {{ $dn->name }} {{ $dn->short_name ? '('.$dn->short_name.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="application_date">Date</label>
                            <input type="date" class="form-control-dark" id="application_date" name="meta[application_date]" value="{{ old('meta.application_date') }}">
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
                <h2 class="panel-title" style="font-size: 1.25rem;">Edit Orphan Care Application</h2>
            </div>

            <form id="editAppForm" action="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Hidden Category and redirect tags -->
                <input type="hidden" name="category" value="{{ $categoryName }}">
                <input type="hidden" name="redirect_category" value="{{ $categorySlug }}">
                <input type="hidden" name="amount_requested" value="0">

                <!-- Form Section 1: Orphan & Family Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1.25rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">1. Orphan & Family Details</h4>
                    
                    <!-- Top Split Layout: Left (Inputs 2x2 Grid) | Right (Fixed 175px Student Photo Card) -->
                    <div style="display: flex; gap: 1.25rem; margin-bottom: 1rem; align-items: flex-start; flex-wrap: wrap;">
                        <!-- Left Column: 2x2 Inputs Grid -->
                        <div style="flex: 1; min-width: 280px; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div>
                                <label class="form-label" for="edit_applicant_name">Name Of Orphan *</label>
                                <input type="text" class="form-control-dark" id="edit_applicant_name" name="applicant_name" required>
                            </div>
                            <div>
                                <label class="form-label" style="display: block; margin-bottom: 0.5rem;">Male/Female *</label>
                                <div style="display: flex; gap: 1.5rem; align-items: center; padding: 0.45rem 0;">
                                    <label style="display: flex; align-items: center; gap: 0.4rem; cursor: pointer; color: var(--text-main); font-size: 0.88rem; font-weight: 500;">
                                        <input type="radio" id="edit_gender_male" name="meta[gender]" value="Male">
                                        Male
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 0.4rem; cursor: pointer; color: var(--text-main); font-size: 0.88rem; font-weight: 500;">
                                        <input type="radio" id="edit_gender_female" name="meta[gender]" value="Female">
                                        Female
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label class="form-label" for="edit_father_name">Name Of Father *</label>
                                <input type="text" class="form-control-dark" id="edit_father_name" name="meta[father_name]" required>
                            </div>
                            <div>
                                <label class="form-label" for="edit_grandfather_name">Name Of GrandFather</label>
                                <input type="text" class="form-control-dark" id="edit_grandfather_name" name="meta[grandfather_name]">
                            </div>
                        </div>

                        <!-- Right Column: Fixed-Width Top-Aligned Student Photo Card (175px Wide, Passport 3:4 Frame) -->
                        <div style="width: 175px; flex-shrink: 0; align-self: flex-start; margin-top: -6px; border: 1px solid var(--panel-border); border-radius: 12px; padding: 0.55rem 0.65rem; background: rgba(255,255,255,0.03); text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: flex; flex-direction: column; align-items: center;">
                            <h5 style="color: #00a65a; font-weight: 700; font-size: 0.8rem; letter-spacing: 0.05em; margin: 0 0 0.5rem 0; text-transform: uppercase;">STUDENT PHOTO</h5>
                            
                            <div id="edit_photo_preview_box" style="width: 105px; height: 135px; border: 2px dashed #00a65a; border-radius: 10px; padding: 0.2rem; display: flex; flex-direction: column; align-items: center; justify-content: center; background: transparent; overflow: hidden; position: relative; margin: 0 auto 0.6rem auto;">
                                <i id="edit_photo_icon" class="bx bx-image-add" style="font-size: 2.2rem; color: #00a65a; margin-bottom: 0.2rem;"></i>
                                <span id="edit_photo_text" style="color: var(--text-muted); font-size: 0.7rem; font-weight: 500; text-align: center; line-height: 1.2;">No Photo<br>Uploaded</span>
                                <img id="edit_photo_img" src="" style="display: none; width: 100%; height: 100%; border-radius: 8px; object-fit: cover;">
                                <button type="button" id="edit_photo_trash_overlay" onclick="removeStudentPhoto('edit')" style="display: none; position: absolute; top: 4px; right: 4px; background: #dc3545; color: #ffffff; border: none; border-radius: 50%; width: 24px; height: 24px; cursor: pointer; align-items: center; justify-content: center; z-index: 10; box-shadow: 0 2px 4px rgba(0,0,0,0.3);" title="Remove Photo">
                                    <i class="bx bx-trash" style="font-size: 0.82rem; color: #ffffff;"></i>
                                </button>
                            </div>

                            <div style="display: flex; gap: 0.5rem; width: 100%;">
                                <label for="edit_student_photo" class="btn-custom" style="display: flex; align-items: center; justify-content: center; gap: 0.3rem; flex: 1; padding: 0.45rem 0.25rem; background: #00a65a !important; color: #ffffff !important; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 0.78rem; border: none; margin: 0; transition: all 0.2s; box-shadow: 0 2px 6px rgba(0,166,90,0.3);">
                                    <i class="bx bx-upload" style="font-size: 0.9rem; color: #ffffff !important;"></i> <span style="color: #ffffff !important;">Upload</span>
                                </label>
                                <button type="button" id="edit_photo_remove_btn" onclick="removeStudentPhoto('edit')" style="display: none; align-items: center; justify-content: center; gap: 0.3rem; flex: 1; padding: 0.45rem 0.25rem; background: #dc3545 !important; color: #ffffff !important; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 0.78rem; border: none; margin: 0; transition: all 0.2s; box-shadow: 0 2px 6px rgba(220,53,69,0.3);" title="Delete Photo">
                                    <i class="bx bx-trash" style="font-size: 0.9rem; color: #ffffff !important;"></i> <span style="color: #ffffff !important;">Delete</span>
                                </button>
                            </div>
                            <input type="file" id="edit_student_photo" name="student_photo" accept="image/*" style="display: none;" onchange="previewStudentPhoto(this, 'edit')">
                            <input type="hidden" id="edit_photo_hidden" name="meta[student_photo]">
                        </div>
                    </div>

                    <!-- Flowing Form Fields below top split section -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_mother_name">Name Of Mother *</label>
                            <input type="text" class="form-control-dark" id="edit_mother_name" name="meta[mother_name]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_mothers_father_name">Name Of MothersFather</label>
                            <input type="text" class="form-control-dark" id="edit_mothers_father_name" name="meta[mothers_father_name]">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_dob">Date of Birth *</label>
                            <input type="date" class="form-control-dark" id="edit_dob" name="meta[dob]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_age">Age *</label>
                            <input type="number" class="form-control-dark" id="edit_age" name="meta[age]" readonly style="background-color: rgba(255, 255, 255, 0.05); cursor: not-allowed;" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_aadhar_number">Aadhaar Number *</label>
                            <input type="text" class="form-control-dark" id="edit_aadhar_number" name="meta[aadhar_number]" placeholder="XXXX XXXX XXXX" maxlength="14" required>
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
                            <input type="date" class="form-control-dark" id="edit_father_death_date" name="meta[father_death_date]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_father_death_cause">Cause Of Death *</label>
                            <input type="text" class="form-control-dark" id="edit_father_death_cause" name="meta[father_death_cause]" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" style="display: block; margin-bottom: 0.5rem;">Mother Alive/Not *</label>
                            <div style="display: flex; gap: 1.5rem; align-items: center; padding: 0.45rem 0;">
                                <label style="display: flex; align-items: center; gap: 0.4rem; cursor: pointer; color: var(--text-main); font-size: 0.88rem; font-weight: 500;">
                                    <input type="radio" id="edit_mother_alive_yes" name="meta[mother_alive_status]" value="Yes" onchange="toggleMotherDeathFieldsEdit()">
                                    Yes
                                </label>
                                <label style="display: flex; align-items: center; gap: 0.4rem; cursor: pointer; color: var(--text-main); font-size: 0.88rem; font-weight: 500;">
                                    <input type="radio" id="edit_mother_alive_no" name="meta[mother_alive_status]" value="No" onchange="toggleMotherDeathFieldsEdit()">
                                    No
                                </label>
                            </div>
                        </div>
                        <div id="edit_mother_remarried_wrapper" style="display: block;">
                            <label class="form-label" for="edit_mother_remarried_status">Mother Re-Married/not *</label>
                            <input type="text" class="form-control-dark" id="edit_mother_remarried_status" name="meta[mother_remarried_status]" required>
                        </div>
                    </div>

                    <!-- Conditional Mother Death Details in Edit Modal -->
                    <div id="edit_mother_death_fields" style="display: none; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_mother_death_date">If Not/Date of Death</label>
                            <input type="date" class="form-control-dark" id="edit_mother_death_date" name="meta[mother_death_date]">
                        </div>
                        <div>
                            <label class="form-label" for="edit_mother_death_cause">Cause Of Death (Mother)</label>
                            <input type="text" class="form-control-dark" id="edit_mother_death_cause" name="meta[mother_death_cause]">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1.5fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_siblings_male">Brothers *</label>
                            <input type="number" class="form-control-dark" id="edit_siblings_male" name="meta[siblings_male]" min="0" placeholder="0" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_siblings_female">Sisters *</label>
                            <input type="number" class="form-control-dark" id="edit_siblings_female" name="meta[siblings_female]" min="0" placeholder="0" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_siblings_total">No Of Brothers And Sisters *</label>
                            <input type="number" class="form-control-dark" id="edit_siblings_total" name="meta[siblings_total]" readonly style="background-color: rgba(255, 255, 255, 0.05); cursor: not-allowed;" required>
                        </div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="edit_current_beneficiaries">Current Beneficiaries *</label>
                        <input type="number" class="form-control-dark" id="edit_current_beneficiaries" name="meta[current_beneficiaries]" min="0" placeholder="Enter number of current beneficiaries" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_monthly_income">Monthly Income (₹) *</label>
                            <input type="number" class="form-control-dark" id="edit_monthly_income" name="meta[monthly_income]" required>
                        </div>
                        <div>
                            <label class="form-label" for="edit_monthly_expense">Monthly Expense (₹) *</label>
                            <input type="number" class="form-control-dark" id="edit_monthly_expense" name="meta[monthly_expense]" required>
                        </div>
                    </div>

                    <div>
                        <label class="form-label" for="edit_sponsorship_details">Sponsorship Details If Any</label>
                        <input type="text" class="form-control-dark" id="edit_sponsorship_details" name="meta[sponsorship_details]">
                    </div>
                </div>

                <!-- Form Section 3: Education & Health Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">3. Education & Health Details</h4>
                    
                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" style="margin-bottom: 0.5rem; display: block;">Type Of House *</label>
                        <div style="display: flex; gap: 1.5rem; align-items: center; margin-top: 0.5rem; flex-wrap: wrap;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; color: #ffffff; cursor: pointer;">
                                <input type="radio" id="edit_house_own" name="meta[house_type]" value="Own House" required> Own House
                            </label>
                            <label style="display: flex; align-items: center; gap: 0.5rem; color: #ffffff; cursor: pointer;">
                                <input type="radio" id="edit_house_family" name="meta[house_type]" value="Family House" required> Family House
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
                            <label class="form-label" for="edit_school_name">Name Of School</label>
                            <input type="text" class="form-control-dark" id="edit_school_name" name="meta[school_name]">
                        </div>
                        <div>
                            <label class="form-label" for="edit_school_class">Class</label>
                            <input type="number" min="1" max="12" class="form-control-dark" id="edit_school_class" name="meta[school_class]" placeholder="e.g. 5">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_madrassa_name">Name Of Madrassa</label>
                            <input type="text" class="form-control-dark" id="edit_madrassa_name" name="meta[madrassa_name]">
                        </div>
                        <div>
                            <label class="form-label" for="edit_madrassa_class">Class</label>
                            <input type="number" min="1" max="12" class="form-control-dark" id="edit_madrassa_class" name="meta[madrassa_class]" placeholder="e.g. 5">
                        </div>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label class="form-label" for="edit_not_studying_reason">If Not Studying, Reason</label>
                        <input type="text" class="form-control-dark" id="edit_not_studying_reason" name="meta[not_studying_reason]">
                    </div>

                    <div>
                        <label class="form-label" for="edit_health_status">Health Status *</label>
                        <input type="text" class="form-control-dark" id="edit_health_status" name="meta[health_status]" required>
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

                <!-- Form Section 5: Recommendation Details -->
                <div style="border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">5. Recommendation Details</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_recommender_name">Name</label>
                            <input type="text" class="form-control-dark" id="edit_recommender_name" name="meta[recommender_name]" placeholder="Enter recommender's name">
                        </div>
                        <div>
                            <label class="form-label" for="edit_recommender_org_select">Organization</label>
                            <select class="form-select-dark" id="edit_recommender_org_select" name="meta[recommender_org]" onchange="toggleRecommenderOrgEdit()" style="width: 100%; padding: 0.6rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: #111c2d; color: #ffffff;">
                                <option value="">-- Select Organization --</option>
                                <option value="KMJ">KMJ</option>
                                <option value="SYS">SYS</option>
                                <option value="SSF">SSF</option>
                                <option value="Others">Others</option>
                            </select>
                            <input type="text" class="form-control-dark" id="edit_recommender_org_text" placeholder="Enter Organization Name" style="display: none; margin-top: 0.5rem;" disabled>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label" for="edit_recommender_phone">Phone Number</label>
                            <input type="tel" class="form-control-dark" id="edit_recommender_phone" name="meta[recommender_phone]" placeholder="Enter 10-digit phone number" maxlength="10" inputmode="numeric" pattern="[0-9]{10}">
                        </div>
                        <div>
                            <label class="form-label" for="edit_recommender_position">Position</label>
                            <input type="text" class="form-control-dark" id="edit_recommender_position" name="meta[recommender_position]" placeholder="e.g. President, Secretary">
                        </div>
                    </div>
                </div>

                <!-- Form Section 6: Cluster & Agency Details -->
                <div style="margin-bottom: 2rem;">
                    <h4 style="color: var(--accent-cyan); font-size: 0.95rem; margin-bottom: 1rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">6. Cluster & Agency Details (Optional)</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_cluster_search_input">Cluster</label>
                            <input type="text" class="form-control-dark" id="edit_cluster_search_input" list="edit_cluster_options_list" placeholder="Search or select cluster..." onchange="onClusterSelect(this, 'edit_cluster_id')" oninput="onClusterSelect(this, 'edit_cluster_id')" autocomplete="off">
                            <datalist id="edit_cluster_options_list">
                                @foreach($clusters as $cl)
                                    <option value="{{ $cl->name }} ({{ $cl->code }})" data-id="{{ $cl->id }}">
                                @endforeach
                            </datalist>
                            <input type="hidden" id="edit_cluster_id" name="cluster_id">
                        </div>
                        <div>
                            <label class="form-label" for="edit_agency_number">Agency Number</label>
                            <input type="text" class="form-control-dark" id="edit_agency_number" name="agency_number" placeholder="Enter Agency Number">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label" for="edit_agency_name">Agency Name (Donor)</label>
                            <select class="form-select-dark" id="edit_agency_name" name="meta[agency_name]" style="width: 100%; padding: 0.6rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: #111c2d; color: #ffffff;">
                                <option value="">-- Select Agency --</option>
                                @php
                                    $donorsList = $donors ?? \App\Models\Donor::orderBy('name', 'asc')->get();
                                @endphp
                                @foreach($donorsList as $dn)
                                    <option value="{{ $dn->name }}">
                                        {{ $dn->name }} {{ $dn->short_name ? '('.$dn->short_name.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="edit_application_date">Date</label>
                            <input type="date" class="form-control-dark" id="edit_application_date" name="meta[application_date]">
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
                <h2 class="panel-title" style="font-size: 1.25rem;"><i class="bx bx-check-circle" style="vertical-align: middle; margin-right: 0.5rem; color: var(--accent-green);"></i> Approve Orphan Care Application</h2>
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
                            @foreach(($donors ?? []) as $d)
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
            if (document.getElementById('edit_whatsapp_number')) { document.getElementById('edit_whatsapp_number').value = whatsappNum; }
            document.getElementById('edit_recommender_name').value = meta.recommender_name || '';
            const recommenderOrgVal = meta.recommender_org || '';
            const editOrgSelect = document.getElementById('edit_recommender_org_select');
            const editOrgText = document.getElementById('edit_recommender_org_text');
            if (editOrgSelect && editOrgText) {
                if (['KMJ', 'SYS', 'SSF', ''].includes(recommenderOrgVal)) {
                    editOrgSelect.value = recommenderOrgVal;
                    editOrgText.value = '';
                } else {
                    editOrgSelect.value = 'Others';
                    editOrgText.value = recommenderOrgVal;
                }
            }
            toggleRecommenderOrgEdit();
            document.getElementById('edit_recommender_phone').value = meta.recommender_phone || '';
            document.getElementById('edit_recommender_position').value = meta.recommender_position || '';
            const clusterId = appItem.cluster_id || '';
            document.getElementById('edit_cluster_id').value = clusterId;
            const editClusterDatalist = document.getElementById('edit_cluster_options_list');
            if (editClusterDatalist) {
                let foundText = '';
                const options = editClusterDatalist.options;
                for (let i = 0; i < options.length; i++) {
                    if (options[i].getAttribute('data-id') == clusterId) {
                        foundText = options[i].value;
                        break;
                    }
                }
                document.getElementById('edit_cluster_search_input').value = foundText;
            }
            document.getElementById('edit_agency_number').value = appItem.agency_number || '';
            if (document.getElementById('edit_agency_name')) { document.getElementById('edit_agency_name').value = meta.agency_name || ''; }
            if (document.getElementById('edit_application_date')) { document.getElementById('edit_application_date').value = meta.application_date || ''; }

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
                        <button type="button" onclick="closeDetailsModal(); openApproveModal(${appItem.id}, '${appItem.cluster_id || ''}', '${appItem.agency_number || ''}', '${meta.agency_name || ''}', '${meta.application_date || '{{ date("Y-m-d") }}'}')" class="btn-custom" style="background: linear-gradient(135deg, #2ecc71, #27ae60); padding: 0.6rem 1.5rem; display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600; cursor: pointer; border: none;">
                            <i class="bx bx-check"></i> Approve
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
                    } else if (appItem.sponsor_status === 'Sponsored') {
                        statusHtml = `
                            <div style="background-color: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3); color: #f59e0b; padding: 0.6rem 1rem; border-radius: 6px; font-size: 0.85rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem;">
                                <i class="bx bx-info-circle" style="font-size: 1.1rem;"></i> Sponsored application cannot be rejected.
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
            const houseName = meta.house_name || addr.house_name || appItem.house_name;
            const placeName = meta.place || addr.place || appItem.place;
            const villageName = meta.village || addr.village || appItem.village;
            const postOffice = meta.post_office || addr.post_office || appItem.post_office;
            const panchayatName = meta.panchayat || addr.panchayat || appItem.panchayat;
            const districtName = meta.district || addr.district || appItem.district;
            const stateName = meta.state || addr.state || appItem.state;
            const pinCode = meta.pin_code || addr.pin_code || appItem.pin_code;
            const mob1 = meta.mobile_1 || meta.mobile || addr.contact_number_1 || addr.mobile_1 || appItem.mobile_1;
            const mob2 = meta.mobile_2 || addr.contact_number_2 || addr.mobile_2 || appItem.mobile_2;
            const whatsappNum = meta.whatsapp_number || addr.whatsapp_number || appItem.whatsapp_number;
            const photoVal = appItem.student_photo || appItem.photo || (meta && meta.student_photo ? meta.student_photo : null) || (addr && addr.student_photo ? addr.student_photo : null);
            let photoSrc = null;
            if (photoVal) {
                if (photoVal.startsWith('http://') || photoVal.startsWith('https://')) {
                    photoSrc = photoVal;
                } else {
                    photoSrc = window.location.origin + '/' + photoVal.replace(/^\/+/, '');
                }
            }

            const formatVal = (val) => val ? val : '<span style="color: var(--text-muted); font-style: italic;">N/A</span>';
            
            let html = `
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <!-- Col 1 -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">1. Orphan & Family Details</h4>
                        <div style="display: flex; gap: 0.75rem; align-items: flex-start; margin-bottom: 0.5rem;">
                            <div style="flex: 1; min-width: 0;">
                                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600; width: 135px;">Orphan Name:</td><td style="font-weight: 600; color: #ffffff;">${formatVal(appItem.applicant_name)}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Gender:</td><td>${formatVal(meta.gender)}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Date of Birth:</td><td>${formatVal(meta.dob)}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Age:</td><td>${formatVal(meta.age ? (meta.age + ' yrs') : null)}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Aadhar Number:</td><td>${formatVal(meta.aadhar_number)}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Father's Name:</td><td>${formatVal(meta.father_name)}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Grandfather's Name:</td><td>${formatVal(meta.grandfather_name)}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Mother's Name:</td><td>${formatVal(meta.mother_name)}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Mother's Father Name:</td><td>${formatVal(meta.mothers_father_name)}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Guardian Name:</td><td>${formatVal(meta.guardian_name)}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Guardian Relation:</td><td>${formatVal(meta.guardian_relation)}</td></tr>
                                </table>
                            </div>
                            <div style="width: 112px; flex-shrink: 0; align-self: flex-start; margin-top: 0px; border: 1px solid var(--panel-border); border-radius: 10px; padding: 0.4rem 0.25rem; background: rgba(255,255,255,0.03); text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.15); display: flex; flex-direction: column; align-items: center;">
                                <h5 style="color: #00a65a; font-weight: 700; font-size: 0.65rem; letter-spacing: 0.04em; margin: 0 0 0.3rem 0; text-transform: uppercase;">STUDENT PHOTO</h5>
                                <div style="width: 80px; height: 104px; border: 2px dashed #00a65a; border-radius: 8px; padding: 0.15rem; display: flex; flex-direction: column; align-items: center; justify-content: center; background: transparent; overflow: hidden; position: relative;">
                                    ${photoSrc ? `
                                        <img src="${photoSrc}" onerror="this.onerror=null; this.parentNode.innerHTML='<i class=\\'bx bx-image\\' style=\\'font-size: 1.5rem; color: #00a65a; margin-bottom: 0.1rem;\\'></i><span style=\\'color: var(--text-muted); font-size: 0.6rem; font-weight: 500; text-align: center; line-height: 1.1;\\'>No Photo<br>Uploaded</span>';" style="width: 100%; height: 100%; border-radius: 6px; object-fit: cover;">
                                    ` : `
                                        <i class="bx bx-image" style="font-size: 1.5rem; color: #00a65a; margin-bottom: 0.1rem;"></i>
                                        <span style="color: var(--text-muted); font-size: 0.6rem; font-weight: 500; text-align: center; line-height: 1.1;">No Photo<br>Uploaded</span>
                                    `}
                                </div>
                            </div>
                        </div>

                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">2. Parental Death & Sibling Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600; width: 140px;">Father's Death Date:</td><td>${formatVal(meta.father_death_date)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Father's Death Cause:</td><td>${formatVal(meta.father_death_cause)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Mother Alive Status:</td><td>${formatVal(meta.mother_alive_status)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Mother's Death Date:</td><td>${formatVal(meta.mother_death_date)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Mother's Death Cause:</td><td>${formatVal(meta.mother_death_cause)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Mother Re-Married?</td><td>${formatVal(meta.mother_remarried_status)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Brothers:</td><td>${formatVal(meta.siblings_male)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Sisters:</td><td>${formatVal(meta.siblings_female)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Total Siblings:</td><td>${formatVal(meta.siblings_total)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Current Beneficiaries:</td><td>${formatVal(meta.current_beneficiaries)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Monthly Income:</td><td>${meta.monthly_income ? '₹' + Number(meta.monthly_income).toLocaleString() : 'N/A'}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Monthly Expense:</td><td>${meta.monthly_expense ? '₹' + Number(meta.monthly_expense).toLocaleString() : 'N/A'}</td></tr>
                        </table>
                    </div>

                    <!-- Col 2 -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">3. Education & House Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600; width: 140px;">Type Of House:</td><td>${formatVal(meta.house_type)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">School Name:</td><td>${formatVal(meta.school_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">School Class:</td><td>${formatVal(meta.school_class)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Madrassa Name:</td><td>${formatVal(meta.madrassa_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Madrassa Class:</td><td>${formatVal(meta.madrassa_class)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">If Not Studying, Reason:</td><td>${formatVal(meta.not_studying_reason)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Health Status:</td><td>${formatVal(meta.health_status)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Sponsorship Details:</td><td>${formatVal(meta.sponsorship_details)}</td></tr>
                        </table>

                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">4. Address & Contact Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600; width: 140px;">House Name:</td><td>${formatVal(houseName)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Place:</td><td>${formatVal(placeName)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Village:</td><td>${formatVal(villageName)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Post Office:</td><td>${formatVal(postOffice)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Panchayath:</td><td>${formatVal(panchayatName)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">District:</td><td>${formatVal(districtName)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">State:</td><td>${formatVal(stateName)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Pin Code:</td><td>${formatVal(pinCode)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Mobile 1:</td><td>${formatVal(mob1)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Mobile 2:</td><td>${formatVal(mob2)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">WhatsApp Number:</td><td>${formatVal(whatsappNum)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Review Status:</td><td style="font-weight: 600; color: #ffffff;">${appItem.status}</td></tr>
                        </table>

                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">5. Recommendation Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600; width: 140px;">Recommender Name:</td><td>${formatVal(meta.recommender_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Organization:</td><td>${formatVal(meta.recommender_org)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Phone Number:</td><td>${formatVal(meta.recommender_phone)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Position:</td><td>${formatVal(meta.recommender_position)}</td></tr>
                        </table>
                    </div>
                </div>
      </div>

                <!-- Cluster & Agency Details (Only visible if Approved) -->
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
                                    <td style="font-weight: 600; color: #ffffff;">
                                        ${appItem.sponsor_status === 'Sponsored'
                                            ? '<span style="background-color: rgba(16, 185, 129, 0.2); color: var(--accent-green); padding: 0.15rem 0.4rem; border-radius: 4px; font-weight: 600;">Sponsored</span>'
                                            : '<span style="background-color: rgba(245, 158, 11, 0.2); color: #f59e0b; padding: 0.15rem 0.4rem; border-radius: 4px; font-weight: 600;">Not Sponsored</span>'}
                                    </td>
                                </tr>
                            </table>
                            
                            @if(Auth::user() && Auth::user()->isSuperAdmin())
                            <button onclick="toggleClusterEditForm()" class="btn-custom" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.25rem; cursor: pointer;">
                                <i class="bx bx-edit"></i> Edit
                            </button>
                            @endif
                        </div>
                    </div>

                    @if(Auth::user() && Auth::user()->isSuperAdmin())
                    <div id="modal-cluster-edit-form" style="display: none;">
                        <form id="save-cluster-form" action="{{ url('admin/applications') }}/${appItem.id}/update-cluster" method="POST" onsubmit="submitClusterForm(event, ${appItem.id})">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                                <div>
                                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.3rem;">Select Cluster *</label>
                                    <select id="assign_cluster_id" name="cluster_id" class="form-control-dark" style="width: 100%; padding: 0.5rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" required>
                                        <option value="">-- Choose Cluster --</option>
                                        @foreach($clusters as $cl)
                                            <option value="{{ $cl->id }}" ${appItem.cluster_id == {{ $cl->id }} ? 'selected' : ''}>{{ $cl->name }} ({{ $cl->code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.3rem;">Agency Number *</label>
                                    <input type="text" id="assign_agency_number" name="agency_number" class="form-control-dark" style="width: 100%; padding: 0.5rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" required value="${appItem.agency_number || ''}">
                                </div>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                                <div>
                                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.3rem;">Agency Name (Donor)</label>
                                    <select id="assign_agency_name" name="meta[agency_name]" class="form-select-dark" style="width: 100%; padding: 0.5rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;">
                                        <option value="">-- Select Agency --</option>
                                        @foreach(($donors ?? []) as $d)
                                            <option value="{{ $d->name }}" ${meta.agency_name == '{{ addslashes($d->name) }}' ? 'selected' : ''}>{{ $d->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.3rem;">Application Date</label>
                                    <input type="date" id="assign_application_date" name="meta[application_date]" class="form-control-dark" style="width: 100%; padding: 0.5rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" value="${meta.application_date || ''}">
                                </div>
                            </div>
                            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                <button type="button" onclick="toggleClusterEditForm()" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); padding: 0.4rem 1rem; font-size: 0.8rem; cursor: pointer;">Cancel</button>
                                <button type="submit" class="btn-custom" style="padding: 0.4rem 1.2rem; font-size: 0.8rem; background: linear-gradient(135deg, #2ecc71, #27ae60); border: none; cursor: pointer;">Save Changes</button>
                            </div>
                        </form>
                    </div>
                    @endif
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

        function toggleMotherDeathFieldsAdd() {
            const noRadio = document.getElementById('mother_alive_no');
            const deathFields = document.getElementById('add_mother_death_fields');
            const remarriedWrapper = document.getElementById('add_mother_remarried_wrapper');
            if (noRadio && noRadio.checked) {
                if (deathFields) deathFields.style.display = 'grid';
                if (remarriedWrapper) remarriedWrapper.style.display = 'none';
            } else {
                if (deathFields) deathFields.style.display = 'none';
                if (remarriedWrapper) remarriedWrapper.style.display = 'block';
            }
        }

        function toggleMotherDeathFieldsEdit() {
            const noRadio = document.getElementById('edit_mother_alive_no');
            const deathFields = document.getElementById('edit_mother_death_fields');
            const remarriedWrapper = document.getElementById('edit_mother_remarried_wrapper');
            if (noRadio && noRadio.checked) {
                if (deathFields) deathFields.style.display = 'grid';
                if (remarriedWrapper) remarriedWrapper.style.display = 'none';
            } else {
                if (deathFields) deathFields.style.display = 'none';
                if (remarriedWrapper) remarriedWrapper.style.display = 'block';
            }
        }

        function toggleRecommenderOrgAdd() {
            const orgSelect = document.getElementById('recommender_org_select');
            const orgText = document.getElementById('recommender_org_text');
            if (!orgSelect || !orgText) return;

            if (orgSelect.value === 'Others') {
                orgText.style.display = 'block';
                orgText.disabled = false;
                orgText.setAttribute('name', 'meta[recommender_org]');
                orgSelect.removeAttribute('name');
                orgText.focus();
            } else {
                orgText.style.display = 'none';
                orgText.disabled = true;
                orgText.removeAttribute('name');
                orgSelect.setAttribute('name', 'meta[recommender_org]');
            }
        }

        function toggleRecommenderOrgEdit() {
            const orgSelect = document.getElementById('edit_recommender_org_select');
            const orgText = document.getElementById('edit_recommender_org_text');
            if (!orgSelect || !orgText) return;

            if (orgSelect.value === 'Others') {
                orgText.style.display = 'block';
                orgText.disabled = false;
                orgText.setAttribute('name', 'meta[recommender_org]');
                orgSelect.removeAttribute('name');
            } else {
                orgText.style.display = 'none';
                orgText.disabled = true;
                orgText.removeAttribute('name');
                orgSelect.setAttribute('name', 'meta[recommender_org]');
            }
        }

        function previewStudentPhoto(input, type) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewImg = document.getElementById(type + '_photo_img');
                    const icon = document.getElementById(type + '_photo_icon');
                    const text = document.getElementById(type + '_photo_text');
                    const hidden = document.getElementById(type + '_photo_hidden');
                    const removeBtn = document.getElementById(type + '_photo_remove_btn');
                    const trashOverlay = document.getElementById(type + '_photo_trash_overlay');

                    if (previewImg) {
                        previewImg.src = e.target.result;
                        previewImg.style.display = 'block';
                    }
                    if (icon) icon.style.display = 'none';
                    if (text) text.style.display = 'none';
                    if (hidden) hidden.value = e.target.result;
                    if (removeBtn) removeBtn.style.display = 'inline-flex';
                    if (trashOverlay) trashOverlay.style.display = 'flex';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removeStudentPhoto(type) {
            const fileInput = document.getElementById(type + '_student_photo');
            const previewImg = document.getElementById(type + '_photo_img');
            const icon = document.getElementById(type + '_photo_icon');
            const text = document.getElementById(type + '_photo_text');
            const hidden = document.getElementById(type + '_photo_hidden');
            const removeBtn = document.getElementById(type + '_photo_remove_btn');
            const trashOverlay = document.getElementById(type + '_photo_trash_overlay');

            if (fileInput) fileInput.value = '';
            if (previewImg) { previewImg.src = ''; previewImg.style.display = 'none'; }
            if (icon) icon.style.display = 'block';
            if (text) text.style.display = 'block';
            if (hidden) hidden.value = '';
            if (removeBtn) removeBtn.style.display = 'none';
            if (trashOverlay) trashOverlay.style.display = 'none';
        }

        function onClusterSelect(input, hiddenId) {
            const hiddenInput = document.getElementById(hiddenId);
            if (!hiddenInput) return;
            const val = input.value;
            const datalist = document.getElementById(input.getAttribute('list'));
            if (!datalist) return;
            
            let matchedId = '';
            const options = datalist.options;
            for (let i = 0; i < options.length; i++) {
                if (options[i].value === val) {
                    matchedId = options[i].getAttribute('data-id');
                    break;
                }
            }
            hiddenInput.value = matchedId;
        }

        // Realtime calculation of siblings count and age from Date of Birth
        document.addEventListener("DOMContentLoaded", function() {
            toggleMotherDeathFieldsAdd();
            toggleMotherDeathFieldsEdit();
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

            // Add Modal Sibling Calculation
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

            // Edit Modal Sibling Calculation
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
        function openApproveModal(appId, clusterId = '', agencyNumber = '', agencyName = '', appDate = '') {
            const form = document.getElementById('approveAppForm');
            form.action = "{{ url('admin/applications/orphan-care') }}/" + appId + "/approve";
            
            if (document.getElementById('approve_cluster_id')) document.getElementById('approve_cluster_id').value = clusterId || '';
            if (document.getElementById('approve_agency_number')) document.getElementById('approve_agency_number').value = agencyNumber || '';
            if (document.getElementById('approve_agency_name')) document.getElementById('approve_agency_name').value = agencyName || '';
            if (document.getElementById('approve_application_date')) document.getElementById('approve_application_date').value = appDate || '{{ date("Y-m-d") }}';
            
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
                    // Update display
                    document.getElementById('modal-cluster-display-name').innerHTML = result.cluster 
                        ? `${result.cluster.name} (${result.cluster.code})` 
                        : '<span style="color: var(--text-muted); font-style: italic;">Not assigned</span>';
                    document.getElementById('modal-agency-display-number').innerHTML = result.agency_number 
                        ? result.agency_number 
                        : '<span style="color: var(--text-muted); font-style: italic;">Not set</span>';
                    
                    // Reload page to update the main list view
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
