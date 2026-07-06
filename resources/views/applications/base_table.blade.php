<!-- Back Button Header -->
<div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
    <a href="{{ route('applications.index') }}" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); padding: 0.5rem 1rem;">
        <i class="bx bx-left-arrow-alt"></i> Back to Dashboard
    </a>
    <h3 style="color: #ffffff; font-size: 1.25rem; font-weight: 600;">{{ $categoryName }} Registry</h3>
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

<!-- Applications Table Panel -->
<div class="panel" style="width: 100%;">
    <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h2 class="panel-title">{{ $categoryName }} Applications List</h2>
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
                    <th>Applicant</th>
                    <th style="text-align: right;">Amount Requested</th>
                    <th style="text-align: center;">Status</th>
                    <th>Contact Email</th>
                    <th>Details</th>
                    <th style="text-align: center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $appItem)
                    <tr>
                        <td style="font-weight: 600; color: #ffffff;">{{ $appItem->applicant_name }}</td>
                        <td style="text-align: right; font-weight: 600; color: #ffffff;">
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
                        <td>{{ $appItem->contact_email ?? 'N/A' }}</td>
                        <td style="max-width: 240px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ $appItem->details ?? '-' }}
                        </td>
                        <td style="text-align: center; white-space: nowrap;">
                            <button onclick="alert('Applicant details:\nName: {{ $appItem->applicant_name }}\nAmount: ₹{{ number_format($appItem->amount_requested) }}\nStatus: {{ $appItem->status }}\nEmail: {{ $appItem->contact_email ?? \'N/A\' }}\nDetails: {{ $appItem->details ?? \'-\' }}')" class="btn-custom" style="background: transparent; color: var(--accent-green); border: 1px solid var(--accent-green); padding: 0.4rem; font-size: 1rem; border-radius: 6px; cursor: pointer; transition: all 0.2s; margin-right: 0.5rem; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px;" title="Details"><i class="bx bx-show"></i></button>

                            @if(in_array(Auth::user()->role, [1, 2, 4]))
                            <button onclick="openEditModal({{ json_encode($appItem) }})" class="btn-custom" style="background: transparent; color: var(--accent-cyan); border: 1px solid var(--accent-cyan); padding: 0.4rem; font-size: 1rem; border-radius: 6px; cursor: pointer; transition: all 0.2s; margin-right: 0.5rem; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px;" title="Edit"><i class="bx bx-pencil"></i></button>
                            
                            <form action="{{ route('applications.destroy', $appItem->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this application?');" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="redirect_category" value="{{ $categorySlug }}">
                                <button type="submit" class="btn-danger-custom" style="padding: 0.4rem; font-size: 1rem; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px;" title="Delete"><i class="bx bx-trash"></i></button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem;">No applications registered in this category yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add Application Modal Dialog -->
<div id="addAppModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.75); display: none; align-items: center; justify-content: center; z-index: 1000;" onclick="closeModal()">
    <div class="panel" style="width: 100%; max-width: 460px; margin: 1rem; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #2a3547;" onclick="event.stopPropagation()">
        
        <button onclick="closeModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; z-index: 10;"><i class="bx bx-x"></i></button>
        
        <div class="panel-header" style="margin-bottom: 2rem;">
            <h2 class="panel-title" style="font-size: 1.25rem;">Register Application</h2>
        </div>

        <form action="{{ route('applications.store') }}" method="POST">
            @csrf
            
            <!-- Hidden Category -->
            <input type="hidden" name="category" value="{{ $categoryName }}">
            <input type="hidden" name="redirect_category" value="{{ $categorySlug }}">

            <!-- Applicant Name -->
            <div style="margin-bottom: 1rem;">
                <label class="form-label" for="applicant_name">Applicant / Organization Name</label>
                <input type="text" class="form-control-dark" id="applicant_name" name="applicant_name" placeholder="Enter applicant name" value="{{ old('applicant_name') }}" required>
            </div>

            <!-- Amount Requested -->
            <div style="margin-bottom: 1rem;">
                <label class="form-label" for="amount_requested">Requested Funding Amount (₹)</label>
                <input type="number" class="form-control-dark" id="amount_requested" name="amount_requested" placeholder="Enter amount" value="{{ old('amount_requested') }}">
            </div>

            <!-- Contact Email -->
            <div style="margin-bottom: 1rem;">
                <label class="form-label" for="contact_email">Contact Email</label>
                <input type="email" class="form-control-dark" id="contact_email" name="contact_email" placeholder="applicant@example.com" value="{{ old('contact_email') }}">
            </div>

            <!-- Status -->
            <div style="margin-bottom: 1rem;">
                <label class="form-label" for="status">Initial Status</label>
                <select class="form-select-dark" id="status" name="status" required>
                    <option value="Pending" {{ old('status') == 'Pending' ? 'selected' : '' }}>Pending Review</option>
                    <option value="Approved" {{ old('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                    <option value="Rejected" {{ old('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <!-- Details -->
            <div style="margin-bottom: 1.5rem;">
                <label class="form-label" for="details">Application details / Notes</label>
                <textarea class="form-control-dark" id="details" name="details" placeholder="Brief project summary..." style="height: 80px; resize: vertical;">{{ old('details') }}</textarea>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-custom" style="width: 100%; padding: 0.75rem;">
                Save Application
            </button>
        </form>
    </div>
</div>

<!-- Edit Application Modal Dialog -->
<div id="editAppModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.75); display: none; align-items: center; justify-content: center; z-index: 1000;" onclick="closeEditModal()">
    <div class="panel" style="width: 100%; max-width: 460px; margin: 1rem; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #2a3547;" onclick="event.stopPropagation()">
        
        <button onclick="closeEditModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; z-index: 10;"><i class="bx bx-x"></i></button>
        
        <div class="panel-header" style="margin-bottom: 2rem;">
            <h2 class="panel-title" style="font-size: 1.25rem;">Edit Application Details</h2>
        </div>

        <form id="editAppForm" action="" method="POST">
            @csrf
            @method('PUT')

            <!-- Hidden Redirect Category -->
            <input type="hidden" name="category" value="{{ $categoryName }}">
            <input type="hidden" name="redirect_category" value="{{ $categorySlug }}">

            <!-- Applicant Name -->
            <div style="margin-bottom: 1rem;">
                <label class="form-label" for="edit_applicant_name">Applicant / Organization Name</label>
                <input type="text" class="form-control-dark" id="edit_applicant_name" name="applicant_name" required>
            </div>

            <!-- Amount Requested -->
            <div style="margin-bottom: 1rem;">
                <label class="form-label" for="edit_amount_requested">Requested Funding Amount (₹)</label>
                <input type="number" class="form-control-dark" id="edit_amount_requested" name="amount_requested">
            </div>

            <!-- Contact Email -->
            <div style="margin-bottom: 1rem;">
                <label class="form-label" for="edit_contact_email">Contact Email</label>
                <input type="email" class="form-control-dark" id="edit_contact_email" name="contact_email">
            </div>

            <input type="hidden" name="status" id="edit_status">

            <!-- Details -->
            <div style="margin-bottom: 1.5rem;">
                <label class="form-label" for="edit_details">Application details / Notes</label>
                <textarea class="form-control-dark" id="edit_details" name="details" style="height: 80px; resize: vertical;"></textarea>
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
        document.getElementById('edit_amount_requested').value = appItem.amount_requested || '';
        document.getElementById('edit_contact_email').value = appItem.contact_email || '';
        document.getElementById('edit_status').value = appItem.status;
        document.getElementById('edit_details').value = appItem.details || '';

        document.getElementById('editAppModal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editAppModal').style.display = 'none';
    }
</script>

<!-- Automatically open add modal if validation error occurs on creation -->
@if ($errors->any())
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            openModal();
        });
    </script>
@endif
