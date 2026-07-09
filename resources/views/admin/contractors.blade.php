@extends('layouts.admin')

@section('title', 'Contractors')

@section('content')

    <!-- Success & Error Alert Panels -->
    @if (session('success'))
        <div class="alert alert-success" style="background-color: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent-green); color: #8cf5c6; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500;">
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

    <!-- Contractors List Panel -->
    <div class="panel" style="width: 100%;">
        <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2 class="panel-title">Contractors</h2>
            @if($canManage)
            <button onclick="openModal()" class="btn-custom">
                <i class="bx bx-plus-circle"></i> Add Contractor
            </button>
            @endif
        </div>
        
        <div style="overflow-x: auto;">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Contractor Details</th>
                        <th>Company Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Address</th>
                        @if($canManage)
                        <th style="text-align: center; width: 100px;">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($contractors as $contractor)
                        <tr>
                            <!-- Contractor Name -->
                            <td>
                                <div style="font-weight: 600; color: #ffffff;">{{ $contractor->name }}</div>
                            </td>

                            <!-- Company Name -->
                            <td>{{ $contractor->company_name }}</td>

                            <!-- Phone -->
                            <td>{{ $contractor->phone }}</td>

                            <!-- Email -->
                            <td>
                                @if($contractor->email)
                                    <a href="mailto:{{ $contractor->email }}" style="color: var(--accent-cyan); text-decoration: none;">{{ $contractor->email }}</a>
                                @else
                                    <span style="color: var(--text-muted); font-style: italic;">No email</span>
                                @endif
                            </td>

                            <!-- Address -->
                            <td style="max-width: 250px; white-space: normal; word-wrap: break-word;">{{ $contractor->address }}</td>

                            <!-- Action buttons -->
                            @if($canManage)
                            <td style="text-align: center; white-space: nowrap;">
                                <button onclick="openEditModal({{ json_encode($contractor) }})" class="btn-custom" style="background: transparent; color: var(--accent-cyan); border: 1px solid var(--accent-cyan); padding: 0.4rem; font-size: 1rem; border-radius: 6px; cursor: pointer; transition: all 0.2s; margin-right: 0.5rem; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px;" title="Edit">
                                    <i class="bx bx-pencil"></i>
                                </button>
                                
                                <form action="{{ route('contractors.destroy', $contractor->id) }}" method="POST" style="display: inline-flex;" onsubmit="return confirm('Are you sure you want to delete this contractor? All historic linkages to projects will lose this reference.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger-custom" style="padding: 0.4rem; font-size: 1rem; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px;" title="Delete">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $canManage ? 6 : 5 }}" style="text-align: center; padding: 2rem;">No registered contractors found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Contractor Modal Dialog -->
    <div id="addContractorModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.75); display: none; align-items: center; justify-content: center; z-index: 1000; overflow-y: auto;" onclick="closeModal()">
        <div class="panel" style="width: 100%; max-width: 500px; margin: 2rem auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #2a3547;" onclick="event.stopPropagation()">
            
            <button onclick="closeModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; z-index: 10;"><i class="bx bx-x"></i></button>
            
            <div class="panel-header" style="margin-bottom: 1.5rem;">
                <h2 class="panel-title" style="font-size: 1.25rem;">Add Contractor</h2>
            </div>

            <form action="{{ route('contractors.store') }}" method="POST">
                @csrf

                <!-- Contractor Name -->
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="name">Contractor Name</label>
                    <input type="text" class="form-control-dark" id="name" name="name" placeholder="Enter contractor name" value="{{ old('name') }}" required>
                </div>

                <!-- Company Name -->
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="company_name">Company Name</label>
                    <input type="text" class="form-control-dark" id="company_name" name="company_name" placeholder="Enter company name" value="{{ old('company_name') }}" required>
                </div>

                <!-- Phone -->
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="phone">Phone Number</label>
                    <input type="text" class="form-control-dark" id="phone" name="phone" placeholder="Enter phone number" value="{{ old('phone') }}" required>
                </div>

                <!-- Email -->
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" class="form-control-dark" id="email" name="email" placeholder="Enter email address" value="{{ old('email') }}">
                </div>

                <!-- Address -->
                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label" for="address">Address</label>
                    <textarea class="form-control-dark" id="address" name="address" placeholder="Enter address" style="min-height: 100px;" required>{{ old('address') }}</textarea>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-custom" style="width: 100%; padding: 0.75rem;">
                    Add Contractor
                </button>
            </form>
        </div>
    </div>

    <!-- Edit Contractor Modal Dialog -->
    <div id="editContractorModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.75); display: none; align-items: center; justify-content: center; z-index: 1000; overflow-y: auto;" onclick="closeEditModal()">
        <div class="panel" style="width: 100%; max-width: 500px; margin: 2rem auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #2a3547;" onclick="event.stopPropagation()">
            
            <button onclick="closeEditModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; z-index: 10;"><i class="bx bx-x"></i></button>
            
            <div class="panel-header" style="margin-bottom: 1.5rem;">
                <h2 class="panel-title" style="font-size: 1.25rem;">Edit Contractor Details</h2>
            </div>

            <form id="editContractorForm" action="" method="POST">
                @csrf
                @method('PUT')

                <!-- Contractor Name -->
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="edit_name">Contractor Name</label>
                    <input type="text" class="form-control-dark" id="edit_name" name="name" required>
                </div>

                <!-- Company Name -->
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="edit_company_name">Company Name</label>
                    <input type="text" class="form-control-dark" id="edit_company_name" name="company_name" required>
                </div>

                <!-- Phone -->
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="edit_phone">Phone Number</label>
                    <input type="text" class="form-control-dark" id="edit_phone" name="phone" required>
                </div>

                <!-- Email -->
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="edit_email">Email Address</label>
                    <input type="email" class="form-control-dark" id="edit_email" name="email">
                </div>

                <!-- Address -->
                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label" for="edit_address">Address</label>
                    <textarea class="form-control-dark" id="edit_address" name="address" style="min-height: 100px;" required></textarea>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-custom" style="width: 100%; padding: 0.75rem;">
                    Save Changes
                </button>
            </form>
        </div>
    </div>

    <!-- Modal Controls Scripts -->
    <script>
        function openModal() {
            document.getElementById('addContractorModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('addContractorModal').style.display = 'none';
        }

        function openEditModal(contractor) {
            const form = document.getElementById('editContractorForm');
            form.action = `/admin/contractors/${contractor.id}`;

            document.getElementById('edit_name').value = contractor.name || '';
            document.getElementById('edit_company_name').value = contractor.company_name || '';
            document.getElementById('edit_phone').value = contractor.phone || '';
            document.getElementById('edit_email').value = contractor.email || '';
            document.getElementById('edit_address').value = contractor.address || '';

            document.getElementById('editContractorModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editContractorModal').style.display = 'none';
        }
    </script>

@endsection
