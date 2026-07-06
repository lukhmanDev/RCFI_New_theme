@extends('layouts.admin')

@section('title', 'All Applications')

@section('content')

    <style>
        .controls-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            margin-top: 1rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .controls-row .btn-custom {
            height: 40px !important;
            box-sizing: border-box;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .search-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-muted);
            font-size: 0.9rem;
            height: 40px;
        }

        .search-container input {
            height: 40px !important;
            padding: 0.65rem 1rem !important;
            box-sizing: border-box;
        }

        .table-custom th, .table-custom td {
            vertical-align: middle !important;
        }

        .btn-action-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            color: #ffffff;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 1rem;
            text-decoration: none;
        }

        .btn-action-icon:hover {
            filter: brightness(1.2);
            transform: translateY(-1px);
        }

        .btn-action-icon.btn-view {
            background-color: #2bcbba;
        }
        .btn-action-icon.btn-approve {
            background-color: #2ed573;
        }
        .btn-action-icon.btn-reject {
            background-color: #ff4757;
        }
        .btn-action-icon.btn-edit {
            background-color: #fa8231;
        }
        .btn-action-icon.btn-delete {
            background-color: #eb3b5a;
        }

        /* Modal styling */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 1rem;
        }

        .modal-content-custom {
            background-color: var(--panel-bg);
            border: 1px solid var(--panel-border);
            width: 100%;
            max-width: 550px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header-custom {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #ffffff;
            padding: 1.25rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header-custom h3 {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .modal-close-btn {
            background: transparent;
            border: none;
            color: #ffffff;
            font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.2s;
        }

        .modal-close-btn:hover {
            color: #ff9999;
        }

        .modal-body-custom {
            padding: 1.5rem;
            max-height: 75vh;
            overflow-y: auto;
        }

        .form-group-custom {
            margin-bottom: 1.25rem;
        }

        .form-group-custom label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        .form-group-custom input, 
        .form-group-custom select, 
        .form-group-custom textarea {
            width: 100%;
            background-color: var(--bg-color);
            border: 1px solid var(--panel-border);
            color: #ffffff;
            padding: 0.65rem 1rem;
            border-radius: 6px;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-group-custom input:focus, 
        .form-group-custom select:focus, 
        .form-group-custom textarea:focus {
            border-color: var(--accent-cyan);
        }

        /* Responsive Column Hiding */
        @media (max-width: 1300px) {
            .col-details { display: none !important; }
        }
        @media (max-width: 1100px) {
            .col-date { display: none !important; }
        }
        @media (max-width: 900px) {
            .col-amount { display: none !important; }
        }
        @media (max-width: 700px) {
            .col-category { display: none !important; }
        }
    </style>

    <!-- Success & Error Alert Panels -->
    @if (session('success'))
        <div class="alert alert-success" style="background-color: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent-green); color: #8cf5c6; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500;">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid var(--accent-red); color: #ff9999; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500;">
            {{ session('error') }}
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

    <!-- Controls Row -->
    <div class="controls-row">
        <button onclick="openModal()" class="btn-custom">
            <i class="bx bx-plus-circle"></i> Add Application
        </button>

        <div class="search-container">
            <span>Search:</span>
            <input type="text" id="tableSearch" onkeyup="filterTable()" class="form-control-dark" style="width: 220px; padding: 0.4rem 0.8rem; font-size: 0.85rem;" placeholder="Search applications...">
        </div>
    </div>

    <!-- Applications Table Panel -->
    <div class="panel" style="width: 100%; margin-bottom: 3rem;">
        <div style="overflow-x: auto;">
            <table class="table-custom" id="appsTable">
                <thead>
                    <tr>
                        <th style="width: 60px; text-align: center;">S.No</th>
                        <th>Application ID</th>
                        <th class="col-category">Category</th>
                        <th>Applicant</th>
                        <th class="col-amount" style="text-align: right;">Amount Requested</th>
                        <th style="text-align: center;">Status</th>
                        <th class="col-details">Details</th>
                        <th class="col-date" style="text-align: center;">Created At</th>
                        <th style="text-align: center; width: 220px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allApplications as $index => $appItem)
                        @php
                            $prefixes = [
                                'education-center' => 'EC',
                                'cultural-center' => 'CC',
                                'hospital-or-clinics' => 'HC',
                                'shops-and-others' => 'SO',
                                'house' => 'HS',
                                'drinking-water-group-level' => 'DWG',
                                'drinking-water-individual-level' => 'DWI',
                                'orphan-care' => 'OC',
                                'differently-abled' => 'DA',
                                'family-aid' => 'FA',
                                'general' => 'GN'
                            ];
                            $prefix = $prefixes[$appItem->category_slug] ?? 'APP';
                            $appYear = !empty($appItem->created_at) ? date('y', strtotime($appItem->created_at)) : '24';
                            $appId = 'APLRCFI' . $appYear . $prefix . str_pad($appItem->id, 5, '0', STR_PAD_LEFT);
                        @endphp
                        <tr>
                            <td style="text-align: center;">{{ $index + 1 }}</td>
                            <td style="font-weight: 600; color: var(--accent-cyan);">
                                {{ $appId }}
                            </td>
                            <td class="col-category">
                                <span style="background-color: rgba(6, 182, 212, 0.15); color: var(--accent-cyan); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">
                                    {{ $appItem->category_name }}
                                </span>
                            </td>
                            <td style="font-weight: 600; color: #ffffff;">{{ $appItem->applicant_name }}</td>
                            <td class="col-amount" style="text-align: right; font-weight: 600; color: #ffffff;">
                                {{ $appItem->amount_requested ? '₹' . number_format($appItem->amount_requested) : 'N/A' }}
                            </td>
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
                            <td class="col-details" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $appItem->details ?? '-' }}
                            </td>
                            <td class="col-date" style="text-align: center; font-size: 0.85rem;">
                                {{ $appItem->created_at ? date('Y-m-d H:i', strtotime($appItem->created_at)) : 'N/A' }}
                            </td>
                            <td style="text-align: center; white-space: nowrap;">
                                <!-- Details -->
                                <button onclick="alert('Applicant details:\nID: {{ $appId }}\nCategory: {{ $appItem->category_name }}\nName: {{ $appItem->applicant_name }}\nAmount: {{ $appItem->amount_requested ? '₹' . number_format($appItem->amount_requested) : 'N/A' }}\nStatus: {{ $appItem->status }}\nEmail: {{ $appItem->contact_email ?? 'N/A' }}\nDetails: {{ $appItem->details ?? '-' }}\n\nAddress Details:\nHouse Name: {{ $appItem->house_name ?? 'N/A' }}\nPlace: {{ $appItem->place ?? 'N/A' }}\nPost Office: {{ $appItem->post_office ?? 'N/A' }}\nVillage: {{ $appItem->village ?? 'N/A' }}\nPanchayath: {{ $appItem->panchayat ?? 'N/A' }}\nDistrict: {{ $appItem->district ?? 'N/A' }}\nState: {{ $appItem->state ?? 'N/A' }}\nPin Code: {{ $appItem->pin_code ?? 'N/A' }}')" class="btn-action-icon btn-view" title="Details">
                                    <i class="bx bx-show-alt"></i>
                                </button>

                                @if(in_array(Auth::user()->role, [1, 2, 4]))
                                    @if($appItem->status === 'Pending')
                                        <!-- Approve -->
                                        <form action="{{ route('applications.approve', [$appItem->category_slug, $appItem->id]) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            <button type="submit" class="btn-action-icon btn-approve" title="Approve">
                                                <i class="bx bx-check"></i>
                                            </button>
                                        </form>

                                        <!-- Reject -->
                                        <form action="{{ route('applications.reject', [$appItem->category_slug, $appItem->id]) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            <button type="submit" class="btn-action-icon btn-reject" title="Reject" onsubmit="return confirm('Are you sure you want to reject this application?');">
                                                <i class="bx bx-x"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Edit -->
                                    <button onclick="openEditModal({{ json_encode($appItem) }}, '{{ $appId }}')" class="btn-action-icon btn-edit" title="Edit">
                                        <i class="bx bx-pencil"></i>
                                    </button>
                                    
                                    <!-- Delete -->
                                    <form action="{{ route('applications.destroy', $appItem->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this application?');" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="category" value="{{ $appItem->category_name }}">
                                        <input type="hidden" name="redirect_category" value="{{ $appItem->category_slug }}">
                                        <input type="hidden" name="redirect_all" value="1">
                                        <button type="submit" class="btn-action-icon btn-delete" title="Delete">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 2rem; color: var(--text-muted);">No applications registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ADD APPLICATION MODAL -->
    <div class="modal-overlay" id="addAppModal">
        <div class="modal-content-custom">
            <div class="modal-header-custom">
                <h3>Register Application</h3>
                <button onclick="closeModal()" class="modal-close-btn">&times;</button>
            </div>
            <form action="{{ route('applications.store') }}" method="POST">
                @csrf
                <input type="hidden" name="redirect_all" value="1">
                <input type="hidden" name="redirect_category" id="redirect_category_add" value="">

                <div class="modal-body-custom">
                    <!-- Category Selection -->
                    <div class="form-group-custom">
                        <label for="category">Category</label>
                        <select id="category" name="category" onchange="updateRedirectCategory(this, 'redirect_category_add'); handleCategoryChange(this.value, 'add')" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $slug => $config)
                                <option value="{{ $config['name'] }}" data-slug="{{ $slug }}">{{ $config['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Applicant Name -->
                    <div class="form-group-custom">
                        <label for="applicant_name">Applicant / Organization Name</label>
                        <input type="text" id="applicant_name" name="applicant_name" placeholder="Enter applicant name" required>
                    </div>

                    <!-- Amount Requested -->
                    <div class="form-group-custom">
                        <label for="amount_requested">Requested Funding Amount (₹)</label>
                        <input type="number" id="amount_requested" name="amount_requested" placeholder="Enter amount">
                    </div>

                    <!-- Contact Email -->
                    <div class="form-group-custom">
                        <label for="contact_email">Contact Email</label>
                        <input type="email" id="contact_email" name="contact_email" placeholder="applicant@example.com">
                    </div>

                    <!-- Status -->
                    <div class="form-group-custom">
                        <label for="status">Initial Status</label>
                        <select id="status" name="status" required>
                            <option value="Pending">Pending Review</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>

                    <!-- Details -->
                    <div class="form-group-custom">
                        <label for="details">Application Details / Notes</label>
                        <textarea id="details" name="details" placeholder="Brief project summary..." style="height: 60px; resize: vertical;"></textarea>
                    </div>

                    <!-- Address Section -->
                    <div style="margin-top: 1.5rem; margin-bottom: 0.75rem; border-top: 1px solid var(--panel-border); padding-top: 1rem;">
                        <span style="font-size: 0.85rem; font-weight: 700; color: var(--accent-cyan); text-transform: uppercase; letter-spacing: 0.05em;">Address Details</span>
                    </div>

                    <!-- House Name (Toggled dynamically) -->
                    <div class="form-group-custom" id="add_house_name_container">
                        <label for="house_name">House Name</label>
                        <input type="text" id="house_name" name="house_name" placeholder="Enter house name">
                    </div>

                    <!-- Row 1: Place & Post Office -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group-custom">
                            <label for="place">Place</label>
                            <input type="text" id="place" name="place" placeholder="Enter place">
                        </div>
                        <div class="form-group-custom">
                            <label for="post_office">Post Office</label>
                            <input type="text" id="post_office" name="post_office" placeholder="Enter post office">
                        </div>
                    </div>

                    <!-- Row 2: Village & Panchayat -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group-custom">
                            <label for="village">Village</label>
                            <input type="text" id="village" name="village" placeholder="Enter village">
                        </div>
                        <div class="form-group-custom">
                            <label for="panchayat">Panchayath</label>
                            <input type="text" id="panchayat" name="panchayat" placeholder="Enter panchayath">
                        </div>
                    </div>

                    <!-- Row 3: District & State -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group-custom">
                            <label for="district">District</label>
                            <input type="text" id="district" name="district" placeholder="Enter district">
                        </div>
                        <div class="form-group-custom">
                            <label for="state">State</label>
                            <input type="text" id="state" name="state" placeholder="Enter state">
                        </div>
                    </div>

                    <!-- Pin Code -->
                    <div class="form-group-custom">
                        <label for="pin_code">Pin Code</label>
                        <input type="text" id="pin_code" name="pin_code" placeholder="Enter pin code">
                    </div>

                    <!-- Submit -->
                    <div style="text-align: center; margin-top: 1.5rem;">
                        <button type="submit" class="btn-custom" style="width: 100%; padding: 0.75rem;">Save Application</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT APPLICATION MODAL -->
    <div class="modal-overlay" id="editAppModal">
        <div class="modal-content-custom">
            <div class="modal-header-custom">
                <h3 id="editModalTitle">Edit Application</h3>
                <button onclick="closeEditModal()" class="modal-close-btn">&times;</button>
            </div>
            <form id="editAppForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="redirect_all" value="1">
                <input type="hidden" name="redirect_category" id="redirect_category_edit" value="">

                <div class="modal-body-custom">
                    <!-- Locked Category -->
                    <div class="form-group-custom">
                        <label>Category</label>
                        <input type="text" id="edit_category_display" disabled style="background-color: var(--bg-color); border: 1px solid var(--panel-border); color: var(--text-muted); font-weight: 600;">
                        <input type="hidden" id="edit_category" name="category">
                    </div>

                    <!-- Applicant Name -->
                    <div class="form-group-custom">
                        <label for="edit_applicant_name">Applicant / Organization Name</label>
                        <input type="text" id="edit_applicant_name" name="applicant_name" required>
                    </div>

                    <!-- Amount Requested -->
                    <div class="form-group-custom">
                        <label for="edit_amount_requested">Requested Funding Amount (₹)</label>
                        <input type="number" id="edit_amount_requested" name="amount_requested">
                    </div>

                    <!-- Contact Email -->
                    <div class="form-group-custom">
                        <label for="edit_contact_email">Contact Email</label>
                        <input type="email" id="edit_contact_email" name="contact_email">
                    </div>

                    <!-- Status -->
                    <div class="form-group-custom">
                        <label for="edit_status">Status</label>
                        <select id="edit_status" name="status" required>
                            <option value="Pending">Pending Review</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                    </div>

                    <!-- Details -->
                    <div class="form-group-custom">
                        <label for="edit_details">Application Details / Notes</label>
                        <textarea id="edit_details" name="details" style="height: 60px; resize: vertical;"></textarea>
                    </div>

                    <!-- Address Section -->
                    <div style="margin-top: 1.5rem; margin-bottom: 0.75rem; border-top: 1px solid var(--panel-border); padding-top: 1rem;">
                        <span style="font-size: 0.85rem; font-weight: 700; color: var(--accent-cyan); text-transform: uppercase; letter-spacing: 0.05em;">Address Details</span>
                    </div>

                    <!-- House Name (Toggled dynamically) -->
                    <div class="form-group-custom" id="edit_house_name_container">
                        <label for="edit_house_name">House Name</label>
                        <input type="text" id="edit_house_name" name="house_name" placeholder="Enter house name">
                    </div>

                    <!-- Row 1: Place & Post Office -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group-custom">
                            <label for="edit_place">Place</label>
                            <input type="text" id="edit_place" name="place" placeholder="Enter place">
                        </div>
                        <div class="form-group-custom">
                            <label for="edit_post_office">Post Office</label>
                            <input type="text" id="edit_post_office" name="post_office" placeholder="Enter post office">
                        </div>
                    </div>

                    <!-- Row 2: Village & Panchayat -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group-custom">
                            <label for="edit_village">Village</label>
                            <input type="text" id="edit_village" name="village" placeholder="Enter village">
                        </div>
                        <div class="form-group-custom">
                            <label for="edit_panchayat">Panchayath</label>
                            <input type="text" id="edit_panchayat" name="panchayat" placeholder="Enter panchayath">
                        </div>
                    </div>

                    <!-- Row 3: District & State -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group-custom">
                            <label for="edit_district">District</label>
                            <input type="text" id="edit_district" name="district" placeholder="Enter district">
                        </div>
                        <div class="form-group-custom">
                            <label for="edit_state">State</label>
                            <input type="text" id="edit_state" name="state" placeholder="Enter state">
                        </div>
                    </div>

                    <!-- Pin Code -->
                    <div class="form-group-custom">
                        <label for="edit_pin_code">Pin Code</label>
                        <input type="text" id="edit_pin_code" name="pin_code" placeholder="Enter pin code">
                    </div>

                    <!-- Submit -->
                    <div style="text-align: center; margin-top: 1.5rem;">
                        <button type="submit" class="btn-custom" style="width: 100%; padding: 0.75rem;">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Client-side Scripts -->
    <script>
        const groupCategories = [
            'Education Center',
            'Cultural Center',
            'Hospital or Clinics',
            'Shops and Others',
            'Drinking Water - Group Level'
        ];

        // Modal functions
        function openModal() {
            document.getElementById('addAppModal').style.display = 'flex';
            // Default select is empty, so house name display
            handleCategoryChange('', 'add');
        }

        function closeModal() {
            document.getElementById('addAppModal').style.display = 'none';
        }

        function openEditModal(appItem, appId) {
            const form = document.getElementById('editAppForm');
            form.action = `/admin/applications/${appItem.id}`;

            const categoryName = appItem.category_name;

            document.getElementById('editModalTitle').textContent = `Edit Application - ${appId}`;
            document.getElementById('edit_category_display').value = categoryName;
            document.getElementById('edit_category').value = categoryName;
            document.getElementById('redirect_category_edit').value = appItem.category_slug;

            document.getElementById('edit_applicant_name').value = appItem.applicant_name;
            document.getElementById('edit_amount_requested').value = appItem.amount_requested || '';
            document.getElementById('edit_contact_email').value = appItem.contact_email || '';
            document.getElementById('edit_status').value = appItem.status;
            document.getElementById('edit_details').value = appItem.details || '';

            // Populate address fields
            document.getElementById('edit_house_name').value = appItem.house_name || '';
            document.getElementById('edit_place').value = appItem.place || '';
            document.getElementById('edit_post_office').value = appItem.post_office || '';
            document.getElementById('edit_village').value = appItem.village || '';
            document.getElementById('edit_panchayat').value = appItem.panchayat || '';
            document.getElementById('edit_district').value = appItem.district || '';
            document.getElementById('edit_state').value = appItem.state || '';
            document.getElementById('edit_pin_code').value = appItem.pin_code || '';

            handleCategoryChange(categoryName, 'edit');

            document.getElementById('editAppModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editAppModal').style.display = 'none';
        }

        function updateRedirectCategory(selectEl, hiddenInputId) {
            const selectedOption = selectEl.options[selectEl.selectedIndex];
            const slug = selectedOption.getAttribute('data-slug') || '';
            document.getElementById(hiddenInputId).value = slug;
        }

        function handleCategoryChange(categoryName, modalType) {
            const houseContainer = document.getElementById(modalType + '_house_name_container');
            if (!houseContainer) return;

            if (groupCategories.includes(categoryName)) {
                houseContainer.style.display = 'none';
                const input = houseContainer.querySelector('input');
                if (input) input.value = '';
            } else {
                houseContainer.style.display = 'block';
            }
        }

        // Live table search filtering
        function filterTable() {
            const input = document.getElementById('tableSearch');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('appsTable');
            const trs = table.getElementsByTagName('tr');

            for (let i = 1; i < trs.length; i++) {
                let match = false;
                const tds = trs[i].getElementsByTagName('td');
                // Loop through columns except S.No and Actions
                for (let j = 1; j < tds.length - 1; j++) {
                    if (tds[j]) {
                        const txtValue = tds[j].textContent || tds[j].innerText;
                        if (txtValue.toLowerCase().indexOf(filter) > -1) {
                            match = true;
                            break;
                        }
                    }
                }
                trs[i].style.display = match ? '' : 'none';
            }
        }
    </script>

@endsection
