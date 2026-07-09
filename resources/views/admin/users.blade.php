@extends('layouts.admin')

@section('title', 'User Management')

@section('content')

    @php
        $rolesMap = [
            1 => 'Admin',
            2 => 'COO',
            3 => 'Project Manager',
            4 => 'HOD',
            5 => 'Others',
            6 => 'Engineer'
        ];
    @endphp

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

    <!-- Users List Panel -->
    <div class="panel" style="width: 100%;">
        <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2 class="panel-title">Registered Users</h2>
            <button onclick="openModal()" class="btn-custom">
                <i class="bx bx-user-plus"></i> Add New User
            </button>
        </div>
        
        <div style="overflow-x: auto;">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th style="text-align: center;">Mobile</th>
                        <th>Designation</th>
                        <th style="text-align: center;">Role</th>
                        <th style="text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td style="font-weight: 600; color: #ffffff;">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td style="text-align: center;">{{ $user->mobile ?? 'N/A' }}</td>
                            <td>{{ $user->designation ?? 'N/A' }}</td>
                            <td style="text-align: center;">
                                @php
                                    $roleColors = [
                                        1 => ['bg' => 'rgba(99, 102, 241, 0.2)', 'text' => 'var(--accent-purple)'],
                                        2 => ['bg' => 'rgba(6, 182, 212, 0.2)', 'text' => 'var(--accent-cyan)'],
                                        3 => ['bg' => 'rgba(16, 185, 129, 0.2)', 'text' => 'var(--accent-green)'],
                                        4 => ['bg' => 'rgba(245, 158, 11, 0.2)', 'text' => '#f59e0b'],
                                        5 => ['bg' => 'rgba(156, 163, 175, 0.2)', 'text' => 'var(--text-muted)'],
                                        6 => ['bg' => 'rgba(236, 72, 153, 0.2)', 'text' => '#ec4899'],
                                    ];
                                    $color = $roleColors[$user->role] ?? ['bg' => 'rgba(156, 163, 175, 0.2)', 'text' => 'var(--text-muted)'];
                                    $roleLabel = $rolesMap[$user->role] ?? 'User';
                                @endphp
                                <span style="background-color: {{ $color['bg'] }}; color: {{ $color['text'] }}; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">
                                    {{ $roleLabel }}
                                </span>
                            </td>
                            <td style="text-align: center; white-space: nowrap;">
                                @if(in_array(Auth::user()->role, [1, 2, 4]))
                                <!-- Edit Button -->
                                <button onclick="openEditModal({{ json_encode($user) }})" class="btn-custom" style="background: transparent; color: var(--accent-cyan); border: 1px solid var(--accent-cyan); padding: 0.4rem; font-size: 1rem; border-radius: 6px; cursor: pointer; transition: all 0.2s; margin-right: 0.5rem; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px;" title="Edit"><i class="bx bx-pencil"></i></button>
                                
                                <!-- Delete Button -->
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger-custom" style="padding: 0.4rem; font-size: 1rem; display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px;" title="Delete"><i class="bx bx-trash"></i></button>
                                </form>
                                @else
                                <span style="color: var(--text-muted); font-size: 0.85rem; font-style: italic;">View Only</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem;">No registered users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add User Modal Dialog -->
    <div id="addUserModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.75); display: none; align-items: center; justify-content: center; z-index: 1000;" onclick="closeModal()">
        <div class="panel" style="width: 100%; max-width: 440px; margin: 1rem; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #2a3547;" onclick="event.stopPropagation()">
            
            <button onclick="closeModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; transition: color 0.2s;"><i class="bx bx-x"></i></button>
            
            <div class="panel-header" style="margin-bottom: 2rem;">
                <h2 class="panel-title" style="font-size: 1.25rem;">Add New User</h2>
            </div>

            <form action="{{ route('do.add_user') }}" method="POST">
                @csrf

                <!-- Name -->
                <div style="margin-bottom: 1.25rem;">
                    <label class="form-label" for="name">Full Name</label>
                    <input type="text" class="form-control-dark" id="name" name="name" placeholder="Enter full name" value="{{ old('name') }}" required>
                </div>

                <!-- Email -->
                <div style="margin-bottom: 1.25rem;">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" class="form-control-dark" id="email" name="email" placeholder="Enter email address" value="{{ old('email') }}" required>
                </div>

                <!-- Mobile -->
                <div style="margin-bottom: 1.25rem;">
                    <label class="form-label" for="mobile">Mobile Number</label>
                    <input type="text" class="form-control-dark" id="mobile" name="mobile" placeholder="Enter mobile number" value="{{ old('mobile') }}">
                </div>

                <!-- Designation -->
                <div style="margin-bottom: 1.25rem;">
                    <label class="form-label" for="designation">Designation</label>
                    <input type="text" class="form-control-dark" id="designation" name="designation" placeholder="e.g. Project Manager" value="{{ old('designation') }}">
                </div>

                <!-- Role -->
                <div style="margin-bottom: 1.25rem;">
                    <label class="form-label" for="role">User Role</label>
                    <select class="form-select-dark" id="role" name="role" required>
                        <option value="2" {{ old('role') == '2' ? 'selected' : '' }}>COO</option>
                        <option value="3" {{ old('role') == '3' ? 'selected' : '' }}>Project Manager</option>
                        <option value="4" {{ old('role') == '4' ? 'selected' : '' }}>HOD</option>
                        <option value="5" {{ old('role') == '5' ? 'selected' : '' }}>Others</option>
                        <option value="6" {{ old('role') == '6' ? 'selected' : '' }}>Engineer</option>
                        <option value="1" {{ old('role') == '1' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>

                <!-- Password -->
                <div style="margin-bottom: 2rem;">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" class="form-control-dark" id="password" name="password" placeholder="Create password" required>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-custom" style="width: 100%; padding: 0.75rem;">
                    <i class="bx bx-plus-circle"></i> Register User
                </button>
            </form>
        </div>
    </div>

    <!-- Edit User Modal Dialog -->
    <div id="editUserModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.75); display: none; align-items: center; justify-content: center; z-index: 1000;" onclick="closeEditModal()">
        <div class="panel" style="width: 100%; max-width: 440px; margin: 1rem; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #2a3547;" onclick="event.stopPropagation()">
            
            <button onclick="closeEditModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; transition: color 0.2s;"><i class="bx bx-x"></i></button>
            
            <div class="panel-header" style="margin-bottom: 2rem;">
                <h2 class="panel-title" style="font-size: 1.25rem;">Edit User Details</h2>
            </div>

            <form id="editUserForm" action="" method="POST">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div style="margin-bottom: 1.25rem;">
                    <label class="form-label" for="edit_name">Full Name</label>
                    <input type="text" class="form-control-dark" id="edit_name" name="name" placeholder="Enter full name" required>
                </div>

                <!-- Email -->
                <div style="margin-bottom: 1.25rem;">
                    <label class="form-label" for="edit_email">Email Address</label>
                    <input type="email" class="form-control-dark" id="edit_email" name="email" placeholder="Enter email address" required>
                </div>

                <!-- Mobile -->
                <div style="margin-bottom: 1.25rem;">
                    <label class="form-label" for="edit_mobile">Mobile Number</label>
                    <input type="text" class="form-control-dark" id="edit_mobile" name="mobile" placeholder="Enter mobile number">
                </div>

                <!-- Designation -->
                <div style="margin-bottom: 1.25rem;">
                    <label class="form-label" for="edit_designation">Designation</label>
                    <input type="text" class="form-control-dark" id="edit_designation" name="designation" placeholder="e.g. Project Manager">
                </div>

                <!-- Role -->
                <div style="margin-bottom: 1.25rem;">
                    <label class="form-label" for="edit_role">User Role</label>
                    <select class="form-select-dark" id="edit_role" name="role" required>
                        <option value="2">COO</option>
                        <option value="3">Project Manager</option>
                        <option value="4">HOD</option>
                        <option value="5">Others</option>
                        <option value="6">Engineer</option>
                        <option value="1">Admin</option>
                    </select>
                </div>

                <!-- Password (Optional) -->
                <div style="margin-bottom: 2rem;">
                    <label class="form-label" for="edit_password">Password (Leave blank to keep current)</label>
                    <input type="password" class="form-control-dark" id="edit_password" name="password" placeholder="Enter new password">
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-custom" style="width: 100%; padding: 0.75rem;">
                    <i class="bx bx-save"></i> Save Changes
                </button>
            </form>
        </div>
    </div>

    <!-- Modal Scripts -->
    <script>
        // Add User Modal
        function openModal() {
            document.getElementById('addUserModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('addUserModal').style.display = 'none';
        }

        // Edit User Modal
        function openEditModal(user) {
            const form = document.getElementById('editUserForm');
            form.action = '/admin/users/' + user.id;

            document.getElementById('edit_name').value = user.name;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_mobile').value = user.mobile || '';
            document.getElementById('edit_designation').value = user.designation || '';
            document.getElementById('edit_role').value = user.role;
            document.getElementById('edit_password').value = '';

            document.getElementById('editUserModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editUserModal').style.display = 'none';
        }
    </script>

    <!-- Automatically open add modal if validation error occurs on new user creation -->
    @if ($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                openModal();
            });
        </script>
    @endif

@endsection
