@extends('layouts.admin')

@section('title', 'User Management')

@section('content')

    @php
        $rolesMap = [
            1 => 'Super Admin',
            2 => 'COO',
            3 => 'Project Manager',
            4 => 'HOD',
            5 => 'Others',
            6 => 'Engineer'
        ];

        // Stats queries
        $totalStaffs = \App\Models\User::count();
        $activeStaffs = \App\Models\User::where('is_suspended', 0)->count();
        $departmentsCount = 7; // Mock matching mockup
        $newThisMonth = \App\Models\User::where('created_at', '>=', now()->startOfMonth())->count();
    @endphp

    <!-- Success & Error Alert Panels -->
    @if (session('success'))
        <div class="alert alert-success" style="background-color: rgba(16, 185, 129, 0.05); border: 1px solid var(--accent-green); color: var(--accent-green); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500;">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="background-color: rgba(239, 68, 68, 0.05); border: 1px solid var(--accent-red); color: var(--accent-red); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500;">
            <ul style="list-style-position: inside; margin: 0; padding: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Page Header Title and Actions -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="color: #1e293b; font-size: 1.75rem; font-weight: 700; margin: 0;">Staffs</h1>
            <p style="color: #64748b; font-size: 0.88rem; margin-top: 0.25rem; margin-bottom: 0;">Dashboard &nbsp;•&nbsp; Staffs</p>
        </div>
        <button onclick="openModal()" class="btn-custom" style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); color: #ffffff; border: none; border-radius: 10px; padding: 0.65rem 1.25rem; font-weight: 600; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2); transition: transform 0.1s ease;">
            <i class="bx bx-user-plus" style="font-size: 1.15rem;"></i> Add New Staff
        </button>
    </div>

    <!-- Stat Cards Row (4 Cards) -->
    <div class="coo-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
        <!-- Total Staffs -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem 1.5rem; display: flex; align-items: center; gap: 1.15rem; box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.01); min-height: 105px;">
            <div style="background: #eff6ff; color: #3b82f6; width: 46px; height: 46px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0;">
                <i class="bx bx-group"></i>
            </div>
            <div>
                <span style="color: #64748b; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; display: block;">Total Staffs</span>
                <h2 style="color: #1e293b; font-size: 1.65rem; font-weight: 700; margin: 0.15rem 0 0.15rem;">{{ $totalStaffs }}</h2>
                <span style="color: #10b981; font-size: 0.76rem; font-weight: 600; display: flex; align-items: center; gap: 0.15rem;">
                    <i class="bx bx-trending-up"></i> ↑ 18% <span style="color: #94a3b8; font-weight: 500;">from last month</span>
                </span>
            </div>
        </div>

        <!-- Active Staffs -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem 1.5rem; display: flex; align-items: center; gap: 1.15rem; box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.01); min-height: 105px;">
            <div style="background: #ecfdf5; color: #10b981; width: 46px; height: 46px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0;">
                <i class="bx bx-user-check"></i>
            </div>
            <div>
                <span style="color: #64748b; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; display: block;">Active Staffs</span>
                <h2 style="color: #1e293b; font-size: 1.65rem; font-weight: 700; margin: 0.15rem 0 0.15rem;">{{ $activeStaffs }}</h2>
                <span style="color: #10b981; font-size: 0.76rem; font-weight: 600; display: flex; align-items: center; gap: 0.15rem;">
                    <i class="bx bx-trending-up"></i> ↑ 12% <span style="color: #94a3b8; font-weight: 500;">from last month</span>
                </span>
            </div>
        </div>

        <!-- Departments -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem 1.5rem; display: flex; align-items: center; gap: 1.15rem; box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.01); min-height: 105px;">
            <div style="background: #fff7ed; color: #f97316; width: 46px; height: 46px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0;">
                <i class="bx bx-briefcase"></i>
            </div>
            <div>
                <span style="color: #64748b; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; display: block;">Departments</span>
                <h2 style="color: #1e293b; font-size: 1.65rem; font-weight: 700; margin: 0.15rem 0 0.15rem;">{{ $departmentsCount }}</h2>
                <span style="color: #64748b; font-size: 0.76rem; font-weight: 600; display: flex; align-items: center; gap: 0.15rem;">
                    — No change
                </span>
            </div>
        </div>

        <!-- New This Month -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem 1.5rem; display: flex; align-items: center; gap: 1.15rem; box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.01); min-height: 105px;">
            <div style="background: #f5f3ff; color: #8b5cf6; width: 46px; height: 46px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0;">
                <i class="bx bx-user-plus"></i>
            </div>
            <div>
                <span style="color: #64748b; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; display: block;">New This Month</span>
                <h2 style="color: #1e293b; font-size: 1.65rem; font-weight: 700; margin: 0.15rem 0 0.15rem;">{{ $newThisMonth }}</h2>
                <span style="color: #10b981; font-size: 0.76rem; font-weight: 600; display: flex; align-items: center; gap: 0.15rem;">
                    <i class="bx bx-trending-up"></i> ↑ 25% <span style="color: #94a3b8; font-weight: 500;">from last month</span>
                </span>
            </div>
        </div>
    </div>

    <!-- Filter and Search Bar -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.01); margin-bottom: 1.5rem;">
        <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
            <div style="position: relative; flex: 1; min-width: 250px;">
                <input type="text" id="staffSearchInput" onkeyup="filterStaffs()" placeholder="Search staff by name, email or role..." style="width: 100%; padding: 0.65rem 2.5rem 0.65rem 1rem; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 0.88rem; outline: none; font-family: inherit; color: #1e293b; background: #f8fafc; transition: border-color 0.15s ease;">
                <i class="bx bx-search" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 1.15rem;"></i>
            </div>
            
            <select id="deptFilter" onchange="filterStaffs()" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; color: #475569; padding: 0.65rem 1rem; font-size: 0.88rem; outline: none; font-family: inherit; font-weight: 500; cursor: pointer; min-width: 165px;">
                <option value="">All Departments</option>
                <option value="Operations">Operations</option>
                <option value="Admin">Admin</option>
                <option value="Finance">Finance</option>
                <option value="Projects">Projects</option>
                <option value="IT">IT</option>
            </select>

            <select id="roleFilter" onchange="filterStaffs()" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; color: #475569; padding: 0.65rem 1rem; font-size: 0.88rem; outline: none; font-family: inherit; font-weight: 500; cursor: pointer; min-width: 145px;">
                <option value="">All Roles</option>
                <option value="Super Admin">Super Admin</option>
                <option value="COO">COO</option>
                <option value="Project Manager">Project Manager</option>
                <option value="HOD">HOD</option>
                <option value="Others">Others</option>
                <option value="Engineer">Engineer</option>
            </select>

            <select id="statusFilter" onchange="filterStaffs()" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; color: #475569; padding: 0.65rem 1rem; font-size: 0.88rem; outline: none; font-family: inherit; font-weight: 500; cursor: pointer; min-width: 135px;">
                <option value="">All Status</option>
                <option value="Active">Active</option>
                <option value="Suspended">Suspended</option>
            </select>
            
            <button onclick="clearFilters()" style="background: transparent; border: 1px solid #e2e8f0; border-radius: 10px; color: #475569; padding: 0.65rem 1.2rem; font-size: 0.88rem; outline: none; font-family: inherit; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 0.45rem; transition: background 0.15s ease, border-color 0.15s ease;">
                <i class="bx bx-filter-alt"></i> Clear Filters
            </button>
        </div>
    </div>

    <!-- Users List Panel -->
    <div class="panel" style="width: 100%; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.01);">
        <div style="overflow-x: auto;">
            <table class="table-custom" data-page-size="5" style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #e2e8f0;">
                        <th style="padding: 1rem 0.75rem; text-align: left; font-weight: 700; color: #1e293b; width: 60px;">#</th>
                        <th style="padding: 1rem 0.75rem; text-align: left; font-weight: 700; color: #1e293b;">Name</th>
                        <th style="padding: 1rem 0.75rem; text-align: left; font-weight: 700; color: #1e293b;">Email</th>
                        <th style="padding: 1rem 0.75rem; text-align: left; font-weight: 700; color: #1e293b;">Mobile</th>
                        <th style="padding: 1rem 0.75rem; text-align: left; font-weight: 700; color: #1e293b;">Department</th>
                        <th style="padding: 1rem 0.75rem; text-align: left; font-weight: 700; color: #1e293b;">Designation</th>
                        <th style="padding: 1rem 0.75rem; text-align: center; font-weight: 700; color: #1e293b; width: 110px;">Role</th>
                        <th style="padding: 1rem 0.75rem; text-align: center; font-weight: 700; color: #1e293b; width: 110px;">Status</th>
                        <th style="padding: 1rem 0.75rem; text-align: center; font-weight: 700; color: #1e293b; width: 170px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        @php
                            // Extract initials
                            $words = explode(' ', $user->name);
                            $initials = '';
                            foreach ($words as $w) {
                                $initials .= strtoupper(substr($w, 0, 1));
                            }
                            $initials = substr($initials, 0, 2);
                            
                            // Color mapping for avatar circular badge
                            $avatarColors = [
                                0 => ['bg' => '#eff6ff', 'text' => '#3b82f6'],
                                1 => ['bg' => '#ecfdf5', 'text' => '#10b981'],
                                2 => ['bg' => '#fff7ed', 'text' => '#f97316'],
                                3 => ['bg' => '#f5f3ff', 'text' => '#8b5cf6'],
                                4 => ['bg' => '#fdf2f8', 'text' => '#ec4899'],
                                5 => ['bg' => '#f0fdf4', 'text' => '#15803d'],
                            ];
                            $colorIdx = $user->id % count($avatarColors);
                            $avColor = $avatarColors[$colorIdx];

                            // Department Mapping logic
                            $designationLower = strtolower($user->designation);
                            $dept = 'Admin';
                            if (strpos($designationLower, 'oper') !== false) {
                                $dept = 'Operations';
                            } elseif (strpos($designationLower, 'finan') !== false) {
                                $dept = 'Finance';
                            } elseif (strpos($designationLower, 'proj') !== false) {
                                $dept = 'Projects';
                            } elseif ($user->role == 1 || $designationLower == 'super admin') {
                                $dept = 'IT';
                            } elseif ($user->role == 2 || $designationLower == 'hod' || $designationLower == 'coo') {
                                $dept = 'Operations';
                            }

                            // Role tag configuration
                            $roleLabel = $rolesMap[$user->role] ?? 'User';
                            $roleBadgeBg = 'rgba(59, 130, 246, 0.1)';
                            $roleBadgeColor = '#3b82f6';
                            if ($user->role == 1) { // Super Admin
                                $roleBadgeBg = 'rgba(139, 92, 246, 0.1)';
                                $roleBadgeColor = '#8b5cf6';
                                $roleLabel = 'ADMIN';
                            } elseif ($user->role == 2) { // COO
                                $roleBadgeBg = 'rgba(59, 130, 246, 0.1)';
                                $roleBadgeColor = '#2563eb';
                                $roleLabel = 'COO';
                            } else {
                                $roleBadgeBg = 'rgba(14, 165, 233, 0.1)';
                                $roleBadgeColor = '#0284c7';
                                $roleLabel = 'USER';
                            }
                        @endphp
                        <tr class="staff-row" style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s ease;">
                            <!-- Serial Index -->
                            <td style="padding: 1rem 0.75rem; color: #64748b; font-weight: 600;">
                                {{ sprintf('%02d', $loop->iteration) }}
                            </td>
                            <!-- Name and avatar info -->
                            <td style="padding: 1rem 0.75rem;">
                                <div style="display: flex; align-items: center; gap: 0.85rem;">
                                    <div style="width: 38px; height: 38px; border-radius: 50%; background: {{ $avColor['bg'] }}; color: {{ $avColor['text'] }}; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; flex-shrink: 0;">
                                        {{ $initials }}
                                    </div>
                                    <div style="min-width: 0;">
                                        <h4 class="staff-name" style="color: #1e293b; font-size: 0.9rem; font-weight: 700; margin: 0;">{{ $user->name }}</h4>
                                        <p style="color: #94a3b8; font-size: 0.75rem; margin: 0.15rem 0 0; font-weight: 500;">Joined on {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <!-- Email -->
                            <td class="staff-email" style="padding: 1rem 0.75rem; color: #475569; font-weight: 500;">{{ $user->email }}</td>
                            <!-- Mobile -->
                            <td style="padding: 1rem 0.75rem; color: #475569; font-weight: 500;">{{ $user->mobile ?? 'N/A' }}</td>
                            <!-- Department -->
                            <td class="staff-dept" style="padding: 1rem 0.75rem; color: #475569; font-weight: 500;">{{ $dept }}</td>
                            <!-- Designation -->
                            <td class="staff-designation" style="padding: 1rem 0.75rem; color: #475569; font-weight: 500;">{{ $user->designation ?? 'N/A' }}</td>
                            <!-- Role badge -->
                            <td style="padding: 1rem 0.75rem; text-align: center;">
                                <span class="staff-role" style="background-color: {{ $roleBadgeBg }}; color: {{ $roleBadgeColor }}; padding: 0.25rem 0.65rem; border-radius: 6px; font-size: 0.74rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.02em;">
                                    {{ $roleLabel }}
                                </span>
                            </td>
                            <!-- Status badge -->
                            <td style="padding: 1rem 0.75rem; text-align: center; white-space: nowrap;">
                                @if($user->is_suspended)
                                    <div style="display: inline-flex; align-items: center; gap: 0.35rem; vertical-align: middle;">
                                        <span style="width: 8px; height: 8px; background: #ef4444; border-radius: 50%; display: inline-block;"></span>
                                        <span class="staff-status" style="color: #ef4444; font-weight: 700; font-size: 0.8rem;">Suspended</span>
                                    </div>
                                @else
                                    <div style="display: inline-flex; align-items: center; gap: 0.35rem; vertical-align: middle;">
                                        <span style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; display: inline-block;"></span>
                                        <span class="staff-status" style="color: #10b981; font-weight: 700; font-size: 0.8rem;">Active</span>
                                    </div>
                                @endif
                            </td>
                            <!-- Action button stack -->
                            <td style="padding: 1rem 0.75rem; text-align: center; white-space: nowrap;">
                                @if(in_array(Auth::user()->role, [1, 2, 4]))
                                    <div style="display: flex; gap: 0.4rem; justify-content: center; align-items: center;">
                                        <!-- View Details -->
                                        <button onclick="openViewModal({{ $user->id }})" style="background: transparent; border: 1px solid #e2e8f0; color: #475569; border-radius: 8px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s ease;" title="View Details"><i class="bx bx-show"></i></button>

                                        <!-- Edit -->
                                        <button onclick="openEditModal({{ json_encode($user) }})" style="background: transparent; border: 1px solid #e2e8f0; color: #3b82f6; border-radius: 8px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s ease;" title="Edit"><i class="bx bx-pencil"></i></button>
                                        
                                        <!-- Suspend/Toggle -->
                                        @if($user->id !== Auth::id())
                                        <form action="{{ route('users.toggle_suspend', $user->id) }}" method="POST" style="display: inline-block; margin: 0;">
                                            @csrf
                                            @if($user->is_suspended)
                                                <button type="submit" style="background: transparent; border: 1px solid #e2e8f0; color: #f59e0b; border-radius: 8px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s ease;" title="Unsuspend Account"><i class="bx bx-lock-open"></i></button>
                                            @else
                                                <button type="submit" style="background: transparent; border: 1px solid #e2e8f0; color: #e11d48; border-radius: 8px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s ease;" title="Suspend Account"><i class="bx bx-block"></i></button>
                                            @endif
                                        </form>
                                        @endif

                                        <!-- Delete -->
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');" style="display: inline-block; margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background: transparent; border: 1px solid rgba(239, 68, 68, 0.2); color: #ef4444; border-radius: 8px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s ease;" title="Delete"><i class="bx bx-trash"></i></button>
                                        </form>
                                    </div>
                                @else
                                    <span style="color: var(--text-muted); font-size: 0.85rem; font-style: italic;">View Only</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 2rem; color: #94a3b8; font-weight: 500;">No registered users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add User Modal Dialog -->
    <div id="addUserModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(15, 23, 42, 0.3); backdrop-filter: blur(6px); display: none; align-items: center; justify-content: center; z-index: 1000;" onclick="closeModal()">
        <div class="panel" style="width: 100%; max-width: 440px; margin: 1rem; position: relative; box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08); border-color: var(--panel-border); background: #ffffff; border-radius: 16px; padding: 2rem;" onclick="event.stopPropagation()">
            
            <button onclick="closeModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; transition: color 0.2s;"><i class="bx bx-x"></i></button>
            
            <div class="panel-header" style="margin-bottom: 2rem;">
                <h2 class="panel-title" style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0;">Add New Staff</h2>
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
                    <input type="text" class="form-control-dark" id="designation" name="designation" placeholder="e.g. Operations HOD" value="{{ old('designation') }}">
                </div>

                <!-- Role -->
                <div style="margin-bottom: 1.25rem;">
                    <label class="form-label" for="role">User Role</label>
                    <select class="form-select-dark" id="role" name="role" required @if(Auth::user()->role != 1) disabled @endif>
                        <option value="2" {{ old('role') == '2' ? 'selected' : '' }}>COO</option>
                        <option value="3" {{ old('role') == '3' ? 'selected' : '' }}>Project Manager</option>
                        <option value="4" {{ old('role') == '4' ? 'selected' : '' }}>HOD</option>
                        <option value="5" {{ (old('role') == '5' || Auth::user()->role != 1) ? 'selected' : '' }}>Others</option>
                        <option value="6" {{ old('role') == '6' ? 'selected' : '' }}>Engineer</option>
                    </select>
                    @if(Auth::user()->role != 1)
                        <input type="hidden" name="role" value="5">
                        <small style="color: var(--text-muted); font-size: 0.75rem; margin-top: 0.25rem; display: block;">Only Super Admins can assign user roles. Defaults to 'Others'.</small>
                    @endif
                </div>

                <!-- Password -->
                <div style="margin-bottom: 2rem;">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" class="form-control-dark" id="password" name="password" placeholder="Create password" required>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-custom" style="width: 100%; padding: 0.75rem; background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    <i class="bx bx-plus-circle"></i> Register Staff
                </button>
            </form>
        </div>
    </div>

    <!-- Edit User Modal Dialog -->
    <div id="editUserModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(15, 23, 42, 0.3); backdrop-filter: blur(6px); display: none; align-items: center; justify-content: center; z-index: 1000;" onclick="closeEditModal()">
        <div class="panel" style="width: 100%; max-width: 440px; margin: 1rem; position: relative; box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08); border-color: var(--panel-border); background: #ffffff; border-radius: 16px; padding: 2rem;" onclick="event.stopPropagation()">
            
            <button onclick="closeEditModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; transition: color 0.2s;"><i class="bx bx-x"></i></button>
            
            <div class="panel-header" style="margin-bottom: 2rem;">
                <h2 class="panel-title" style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0;">Edit Staff Details</h2>
            </div>

            <form id="editUserForm" action="" method="POST">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div style="margin-bottom: 1.25rem;">
                    <label class="form-label" for="edit_name">Full Name</label>
                    <input type="text" class="form-control-dark" id="edit_name" name="name" placeholder="Enter full name" required readonly>
                </div>

                <!-- Email -->
                <div style="margin-bottom: 1.25rem;">
                    <label class="form-label" for="edit_email">Email Address</label>
                    <input type="email" class="form-control-dark" id="edit_email" name="email" placeholder="Enter email address" required readonly>
                </div>

                <!-- Mobile -->
                <div style="margin-bottom: 1.25rem;">
                    <label class="form-label" for="edit_mobile">Mobile Number</label>
                    <input type="text" class="form-control-dark" id="edit_mobile" name="mobile" placeholder="Enter mobile number" readonly>
                </div>

                <!-- Designation -->
                <div style="margin-bottom: 1.25rem;">
                    <label class="form-label" for="edit_designation">Designation</label>
                    <input type="text" class="form-control-dark" id="edit_designation" name="designation" placeholder="e.g. Operations HOD" @if(Auth::user()->role != 1) readonly @endif>
                </div>

                <!-- Role -->
                <div style="margin-bottom: 1.25rem;">
                    <label class="form-label" for="edit_role">User Role</label>
                    <select class="form-select-dark" id="edit_role" name="role" required @if(Auth::user()->role != 1) disabled @endif>
                        <option value="2">COO</option>
                        <option value="3">Project Manager</option>
                        <option value="4">HOD</option>
                        <option value="5">Others</option>
                        <option value="6">Engineer</option>
                    </select>
                    @if(Auth::user()->role != 1)
                        <input type="hidden" name="role" id="edit_role_hidden">
                        <small style="color: var(--text-muted); font-size: 0.75rem; margin-top: 0.25rem; display: block;">Only Super Admins can change user roles.</small>
                    @endif
                </div>

                @if(Auth::user()->role != 1)
                <!-- Password (Optional) -->
                <div style="margin-bottom: 2rem;">
                    <label class="form-label" for="edit_password">Password (Leave blank to keep current)</label>
                    <input type="password" class="form-control-dark" id="edit_password" name="password" placeholder="Enter new password">
                </div>
                @endif

                <!-- Submit Button -->
                <button type="submit" class="btn-custom" style="width: 100%; padding: 0.75rem; background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    <i class="bx bx-save"></i> Save Changes
                </button>
            </form>
        </div>
    </div>

    <!-- View Staff Details Modal -->
    <div id="viewUserModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(15, 23, 42, 0.3); backdrop-filter: blur(6px); display: none; align-items: center; justify-content: center; z-index: 1000;" onclick="closeViewModal()">
        <div class="panel" style="width: 100%; max-width: 650px; margin: 1rem; position: relative; box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08); border-color: var(--panel-border); background: #ffffff; border-radius: 16px; padding: 2rem;" onclick="event.stopPropagation()">
            
            <button onclick="closeViewModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; transition: color 0.2s;"><i class="bx bx-x"></i></button>
            
            <div class="panel-header" style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--panel-border); padding-bottom: 1rem;">
                <h2 class="panel-title" style="font-size: 1.25rem; display: flex; align-items: center; gap: 0.5rem; font-weight: 700; color: #1e293b;">
                    <i class="bx bx-user" style="color: var(--accent-cyan);"></i> Staff Member Profile
                </h2>
            </div>

            <!-- Profile Fields Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
                <div>
                    <label class="form-label" style="margin-bottom: 0.25rem; font-size: 0.75rem;">Full Name</label>
                    <div id="view_name" style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);">-</div>
                </div>
                <div>
                    <label class="form-label" style="margin-bottom: 0.25rem; font-size: 0.75rem;">Email Address</label>
                    <div id="view_email" style="font-size: 0.95rem; color: var(--text-muted); font-weight: 500;">-</div>
                </div>
                <div>
                    <label class="form-label" style="margin-bottom: 0.25rem; font-size: 0.75rem;">Mobile Number</label>
                    <div id="view_mobile" style="font-size: 0.95rem; color: var(--text-main);">-</div>
                </div>
                <div>
                    <label class="form-label" style="margin-bottom: 0.25rem; font-size: 0.75rem;">Designation</label>
                    <div id="view_designation" style="font-size: 0.95rem; color: var(--text-main);">-</div>
                </div>
                <div>
                    <label class="form-label" style="margin-bottom: 0.25rem; font-size: 0.75rem;">System Role</label>
                    <div id="view_role" style="font-size: 0.95rem; color: var(--text-main);">-</div>
                </div>
                <div>
                    <label class="form-label" style="margin-bottom: 0.25rem; font-size: 0.75rem;">Account Status</label>
                    <div id="view_status" style="font-size: 0.95rem;">-</div>
                </div>
                <div style="grid-column: span 2;">
                    <label class="form-label" style="margin-bottom: 0.25rem; font-size: 0.75rem;">Residential Address</label>
                    <div id="view_address" style="font-size: 0.95rem; color: var(--text-muted); line-height: 1.4; white-space: pre-line;">-</div>
                </div>
            </div>

            <!-- Assigned Projects Section -->
            <div class="panel-header" style="margin-bottom: 1rem; border-top: 1px solid var(--panel-border); padding-top: 1.5rem;">
                <h3 class="panel-title" style="font-size: 1.05rem; display: flex; align-items: center; gap: 0.5rem; font-weight: 700; color: #1e293b;">
                    <i class="bx bx-briefcase" style="color: var(--accent-purple);"></i> Assigned Projects
                </h3>
            </div>

            <div style="max-height: 200px; overflow-y: auto; border: 1px solid var(--panel-border); border-radius: 8px; background-color: #f8fafc;">
                <table class="table-custom" style="margin: 0; font-size: 0.85rem; width: 100%;">
                    <thead>
                        <tr style="background-color: #ffffff; border-bottom: 1px solid #e2e8f0;">
                            <th style="padding: 0.5rem 0.75rem; color: #1e293b; font-weight: 700;">Project ID</th>
                            <th style="padding: 0.5rem 0.75rem; color: #1e293b; font-weight: 700;">Project Title</th>
                            <th style="padding: 0.5rem 0.75rem; text-align: center; color: #1e293b; font-weight: 700;">Assigned Role</th>
                            <th style="padding: 0.5rem 0.75rem; text-align: center; color: #1e293b; font-weight: 700;">Stage/Status</th>
                        </tr>
                    </thead>
                    <tbody id="projects_table_body">
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 1rem;">Loading assigned projects...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- Modal Scripts -->
    <script>
        // Client-side search and filters
        function filterStaffs() {
            const searchVal = document.getElementById('staffSearchInput').value.toLowerCase();
            const deptVal = document.getElementById('deptFilter').value.toLowerCase();
            const roleVal = document.getElementById('roleFilter').value.toLowerCase();
            const statusVal = document.getElementById('statusFilter').value.toLowerCase();

            const rows = document.querySelectorAll('.staff-row');
            
            rows.forEach(row => {
                const name = row.querySelector('.staff-name').innerText.toLowerCase();
                const email = row.querySelector('.staff-email').innerText.toLowerCase();
                const designation = row.querySelector('.staff-designation').innerText.toLowerCase();
                const dept = row.querySelector('.staff-dept').innerText.toLowerCase();
                const role = row.querySelector('.staff-role').innerText.toLowerCase();
                const status = row.querySelector('.staff-status').innerText.toLowerCase();

                const matchesSearch = name.includes(searchVal) || email.includes(searchVal) || designation.includes(searchVal);
                const matchesDept = !deptVal || dept === deptVal;
                const matchesRole = !roleVal || role === roleVal;
                const matchesStatus = !statusVal || status.includes(statusVal);

                if (matchesSearch && matchesDept && matchesRole && matchesStatus) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function clearFilters() {
            document.getElementById('staffSearchInput').value = '';
            document.getElementById('deptFilter').value = '';
            document.getElementById('roleFilter').value = '';
            document.getElementById('statusFilter').value = '';
            filterStaffs();
        }

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
            const hiddenRole = document.getElementById('edit_role_hidden');
            if (hiddenRole) {
                hiddenRole.value = user.role;
            }
            const passwordField = document.getElementById('edit_password');
            if (passwordField) {
                passwordField.value = '';
            }

            document.getElementById('editUserModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editUserModal').style.display = 'none';
        }

        // View Details Modal
        function openViewModal(userId) {
            // Reset placeholders
            document.getElementById('view_name').innerText = 'Loading...';
            document.getElementById('view_email').innerText = 'Loading...';
            document.getElementById('view_mobile').innerText = 'Loading...';
            document.getElementById('view_designation').innerText = 'Loading...';
            document.getElementById('view_role').innerText = 'Loading...';
            document.getElementById('view_status').innerText = 'Loading...';
            document.getElementById('view_address').innerText = 'Loading...';
            
            const tableBody = document.getElementById('projects_table_body');
            tableBody.innerHTML = `<tr><td colspan="4" style="text-align: center; color: var(--text-muted); padding: 1rem;">Loading assigned projects...</td></tr>`;
            
            // Show modal
            document.getElementById('viewUserModal').style.display = 'flex';
            
            // Fetch user detail JSON via AJAX
            fetch(`/admin/users/${userId}/details`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const u = data.user;
                        document.getElementById('view_name').innerText = u.name;
                        document.getElementById('view_email').innerText = u.email;
                        document.getElementById('view_mobile').innerText = u.mobile || 'N/A';
                        document.getElementById('view_designation').innerText = u.designation || 'N/A';
                        
                        let displayRole = 'User';
                        if (u.role == 1) displayRole = 'Super Admin';
                        else if (u.role == 2) displayRole = 'COO';
                        else if (u.role == 3) displayRole = 'Project Manager';
                        else if (u.role == 4) displayRole = 'HOD';
                        else if (u.role == 5) displayRole = 'Others';
                        else if (u.role == 6) displayRole = 'Engineer';
                        
                        document.getElementById('view_role').innerText = displayRole;
                        document.getElementById('view_address').innerText = u.address || 'N/A';
                        
                        // Set status badge
                        const statusContainer = document.getElementById('view_status');
                        if (u.is_suspended) {
                            statusContainer.innerHTML = `<span style="background-color: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">SUSPENDED</span>`;
                        } else {
                            statusContainer.innerHTML = `<span style="background-color: rgba(16, 185, 129, 0.1); color: #10b981; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">ACTIVE</span>`;
                        }
                        
                        // Populate projects table
                        if (data.projects && data.projects.length > 0) {
                            tableBody.innerHTML = data.projects.map(p => `
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="font-weight: 600; color: var(--text-main); padding: 0.5rem 0.75rem;">${p.project_id}</td>
                                    <td style="padding: 0.5rem 0.75rem;">
                                        <div style="font-weight: 500; color: var(--text-main);">${p.title}</div>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);">${p.type}</div>
                                    </td>
                                    <td style="text-align: center; font-weight: 600; color: var(--accent-cyan); padding: 0.5rem 0.75rem;">${p.role}</td>
                                    <td style="text-align: center; padding: 0.5rem 0.75rem;">
                                        <span style="background-color: rgba(15, 23, 42, 0.04); color: var(--text-main); padding: 0.15rem 0.4rem; border-radius: 4px; font-size: 0.75rem; font-weight: 500;">
                                            ${p.status}
                                        </span>
                                    </td>
                                </tr>
                            `).join('');
                        } else {
                            tableBody.innerHTML = `<tr><td colspan="4" style="text-align: center; color: var(--text-muted); padding: 1.5rem;">No projects assigned to this user.</td></tr>`;
                        }
                    } else {
                        alert('Failed to retrieve user details.');
                        closeViewModal();
                    }
                })
                .catch(error => {
                    console.error('Error fetching details:', error);
                    alert('Error loading details from server.');
                    closeViewModal();
                });
        }

        function closeViewModal() {
            document.getElementById('viewUserModal').style.display = 'none';
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
