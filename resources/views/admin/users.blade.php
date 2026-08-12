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
            6 => 'Engineer',
            7 => 'Reception',
            8 => 'Social Aid Manager'
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

    @if (isset($errors) && $errors->any())
        <div style="background-color: rgba(239, 68, 68, 0.05); border: 1px solid var(--accent-red); color: var(--accent-red); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500;">
            <ul style="list-style-position: inside; margin: 0; padding: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Page Header Title and Actions -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.75rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="color: #0f172a; font-size: 1.75rem; font-weight: 800; margin: 0;">Staff Management</h1>
            <p style="color: #64748b; font-size: 0.88rem; margin-top: 0.25rem; margin-bottom: 0;">Dashboard &nbsp;•&nbsp; Staff Management</p>
        </div>
        @if(auth()->user()->isSuperAdmin())
            <button onclick="openAddStaffModal()" class="btn-custom" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; border: none; border-radius: 10px; padding: 0.65rem 1.25rem; font-weight: 600; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2); transition: transform 0.1s ease;">
                <i class="bx bx-user-plus" style="font-size: 1.15rem;"></i> Add New Staff
            </button>
        @endif
    </div>

    <!-- Stat Cards Row (5 Cards) -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 1.75rem;">
        <!-- Total Staffs -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);">
            <div style="background: #ecfdf5; color: #10b981; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="bx bx-group"></i>
            </div>
            <div>
                <span style="color: #64748b; font-size: 0.78rem; font-weight: 600; display: block;">Total Staffs</span>
                <h2 style="color: #0f172a; font-size: 1.65rem; font-weight: 800; margin: 0.1rem 0;">{{ $totalStaffs }}</h2>
                <span style="color: #10b981; font-size: 0.75rem; font-weight: 600; display: flex; align-items: center; gap: 0.2rem;">
                    <i class="bx bx-trending-up"></i> ↑ 12% <span style="color: #94a3b8; font-weight: 500;">from last month</span>
                </span>
            </div>
        </div>

        <!-- Active Staffs -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);">
            <div style="background: #eff6ff; color: #3b82f6; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="bx bx-user-check"></i>
            </div>
            <div>
                <span style="color: #64748b; font-size: 0.78rem; font-weight: 600; display: block;">Active Staffs</span>
                <h2 style="color: #0f172a; font-size: 1.65rem; font-weight: 800; margin: 0.1rem 0;">{{ $activeStaffs }}</h2>
                <span style="color: #10b981; font-size: 0.75rem; font-weight: 600; display: flex; align-items: center; gap: 0.2rem;">
                    <i class="bx bx-trending-up"></i> ↑ 8% <span style="color: #94a3b8; font-weight: 500;">from last month</span>
                </span>
            </div>
        </div>

        <!-- On Leave -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);">
            <div style="background: #fff7ed; color: #f97316; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="bx bx-calendar-event"></i>
            </div>
            <div>
                <span style="color: #64748b; font-size: 0.78rem; font-weight: 600; display: block;">On Leave</span>
                <h2 style="color: #0f172a; font-size: 1.65rem; font-weight: 800; margin: 0.1rem 0;">{{ $onLeaveCount ?? 0 }}</h2>
                <span style="color: #f97316; font-size: 0.75rem; font-weight: 600; display: flex; align-items: center; gap: 0.2rem;">
                    <i class="bx bx-time-five"></i> Currently Active
                </span>
            </div>
        </div>

        <!-- Departments -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);">
            <div style="background: #f3e8ff; color: #8b5cf6; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="bx bx-briefcase"></i>
            </div>
            <div>
                <span style="color: #64748b; font-size: 0.78rem; font-weight: 600; display: block;">Departments</span>
                <h2 style="color: #0f172a; font-size: 1.65rem; font-weight: 800; margin: 0.1rem 0;">{{ $departmentsCount }}</h2>
                <span style="color: #64748b; font-size: 0.75rem; font-weight: 600; display: flex; align-items: center; gap: 0.2rem;">
                    — No change
                </span>
            </div>
        </div>

        <!-- Avg. Attendance -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);">
            <div style="background: #fce7f3; color: #ec4899; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="bx bx-bar-chart-alt-2"></i>
            </div>
            <div>
                <span style="color: #64748b; font-size: 0.78rem; font-weight: 600; display: block;">Avg. Attendance</span>
                <h2 style="color: #0f172a; font-size: 1.65rem; font-weight: 800; margin: 0.1rem 0;">94%</h2>
                <span style="color: #10b981; font-size: 0.75rem; font-weight: 600; display: flex; align-items: center; gap: 0.2rem;">
                    <i class="bx bx-trending-up"></i> ↑ 5% <span style="color: #94a3b8; font-weight: 500;">from last month</span>
                </span>
            </div>
        </div>
    </div>

    <!-- Filter and Search Bar -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1rem 1.25rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02); margin-bottom: 1.5rem;">
        <div style="display: flex; gap: 0.85rem; align-items: center; flex-wrap: wrap;">
            <div style="position: relative; flex: 1; min-width: 260px;">
                <input type="text" id="staffSearchInput" onkeyup="filterStaffs()" placeholder="Search staff by name, email or role..." style="width: 100%; padding: 0.65rem 2.5rem 0.65rem 1rem; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 0.88rem; outline: none; font-family: inherit; color: #0f172a; background: #f8fafc; transition: all 0.15s ease;">
                <i class="bx bx-search" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 1.15rem;"></i>
            </div>
            
            <select id="deptFilter" onchange="filterStaffs()" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; color: #475569; padding: 0.65rem 1rem; font-size: 0.88rem; outline: none; font-family: inherit; font-weight: 500; cursor: pointer; min-width: 145px;">
                <option value="">All Departments</option>
                <option value="management">Management</option>
                <option value="operations">Operations</option>
                <option value="engineering">Engineering</option>
                <option value="accounts">Accounts</option>
                <option value="hr">HR</option>
            </select>

            <select id="roleFilter" onchange="filterStaffs()" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; color: #475569; padding: 0.65rem 1rem; font-size: 0.88rem; outline: none; font-family: inherit; font-weight: 500; cursor: pointer; min-width: 135px;">
                <option value="">All Roles</option>
                <option value="super_admin">Super Admin</option>
                <option value="coo">COO</option>
                <option value="project_manager">Project Manager</option>
                <option value="hod">HOD</option>
                <option value="social_aid">Social Aid Manager</option>
                <option value="engineer">Engineer</option>
                <option value="reception">Reception</option>
                <option value="employee">Employee</option>
                <option value="others">Others</option>
            </select>

            <select id="statusFilter" onchange="filterStaffs()" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; color: #475569; padding: 0.65rem 1rem; font-size: 0.88rem; outline: none; font-family: inherit; font-weight: 500; cursor: pointer; min-width: 125px;">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="suspended">Suspended</option>
            </select>

            <select id="leaveFilter" onchange="filterStaffs()" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; color: #475569; padding: 0.65rem 1rem; font-size: 0.88rem; outline: none; font-family: inherit; font-weight: 500; cursor: pointer; min-width: 145px;">
                <option value="">All Leave Status</option>
                <option value="no_leave">Active / No Leave</option>
                <option value="sick_leave">Sick Leave</option>
                <option value="casual_leave">Casual Leave</option>
                <option value="earned_leave">Earned Leave</option>
            </select>
            
            <button onclick="clearFilters()" style="background: transparent; border: 1px solid #e2e8f0; border-radius: 10px; color: #475569; padding: 0.65rem 1.15rem; font-size: 0.88rem; outline: none; font-family: inherit; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 0.45rem; transition: all 0.15s ease;">
                <i class="bx bx-filter-alt"></i> Filters
            </button>
        </div>
    </div>

    <!-- Staff Profile Cards Grid (Strict 4 Cards Per Row) -->
    <div id="staffGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
        <style>
            @media (min-width: 1024px) {
                #staffGrid {
                    grid-template-columns: repeat(4, 1fr) !important;
                }
            }
        </style>
        @forelse($users as $user)
            @php
                // Role tag styling configuration matching mockup
                $roleLabel = $user->role_name;
                $roleBadgeBg = '#f1f5f9';
                $roleBadgeColor = '#334155';
                if ($user->isSuperAdmin()) {
                    $roleBadgeBg = '#f3e8ff';
                    $roleBadgeColor = '#7c3aed';
                } elseif ($user->isCoo()) {
                    $roleBadgeBg = '#eff6ff';
                    $roleBadgeColor = '#2563eb';
                } elseif ($user->isPm()) {
                    $roleBadgeBg = '#ecfdf5';
                    $roleBadgeColor = '#059669';
                } elseif ($user->isHod()) {
                    $roleBadgeBg = '#f0fdf4';
                    $roleBadgeColor = '#16a34a';
                } elseif ($user->isSocialAid()) {
                    $roleBadgeBg = '#ecfdf5';
                    $roleBadgeColor = '#047857';
                } elseif ($user->isReception()) {
                    $roleBadgeBg = '#ecfeff';
                    $roleBadgeColor = '#0891b2';
                } elseif ($user->isEngineer()) {
                    $roleBadgeBg = '#fdf2f8';
                    $roleBadgeColor = '#db2777';
                } elseif ($user->isEmployee()) {
                    $roleBadgeBg = '#f1f5f9';
                    $roleBadgeColor = '#475569';
                } else {
                    $roleBadgeBg = '#fff7ed';
                    $roleBadgeColor = '#d97706';
                }

                // Initial avatar text
                $initial = strtoupper(substr($user->name, 0, 1));
            @endphp

            <div class="staff-row" 
                 data-role="{{ $user->role }}" 
                 data-status="{{ $user->is_suspended ? 'suspended' : 'active' }}"
                 data-leave-status="{{ strtolower($user->current_leave['code'] ?? 'no_leave') }}"
                 data-name="{{ strtolower($user->name) }}" 
                 data-email="{{ strtolower($user->email) }}" 
                 data-designation="{{ strtolower($user->designation ?? '') }}"
                 style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; position: relative; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02); transition: transform 0.15s ease, box-shadow 0.15s ease; display: flex; flex-direction: column; justify-content: space-between;">
                
                <!-- Online/Active Dot Top Right -->
                <span style="position: absolute; top: 1.25rem; right: 1.25rem; width: 9px; height: 9px; border-radius: 50%; background: {{ $user->is_suspended ? '#ef4444' : '#10b981' }}; border: 2px solid #ffffff;" title="{{ $user->is_suspended ? 'Suspended' : 'Active' }}"></span>

                <div>
                    <!-- Top Card Info: Avatar + Name + Role Badge -->
                    <div style="display: flex; gap: 1rem; align-items: flex-start; margin-bottom: 1rem;">
                        @if($user->profile && $user->profile->photo)
                            <img src="{{ asset($user->profile->photo) }}" alt="{{ $user->name }}" style="width: 52px; height: 52px; border-radius: 50%; object-fit: cover; border: 2px solid #e2e8f0; flex-shrink: 0;">
                        @else
                            <div style="width: 52px; height: 52px; border-radius: 50%; background: #ecfdf5; color: #059669; font-weight: 800; font-size: 1.25rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 2px solid #d1fae5;">
                                {{ $initial }}
                            </div>
                        @endif

                        <div style="flex: 1; min-width: 0; padding-right: 1rem;">
                            <h4 class="staff-name" style="font-size: 0.95rem; font-weight: 700; color: #0f172a; margin: 0 0 0.3rem 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $user->name }}">
                                {{ $user->name }}
                            </h4>
                            <span class="staff-role" style="background-color: {{ $roleBadgeBg }}; color: {{ $roleBadgeColor }}; padding: 0.2rem 0.65rem; border-radius: 20px; font-size: 0.72rem; font-weight: 700; display: inline-block;">
                                {{ $user->designation ? $user->designation : $roleLabel }}
                            </span>
                        </div>
                    </div>

                    <!-- Contact Details -->
                    <div style="margin-bottom: 1.15rem; display: flex; flex-direction: column; gap: 0.35rem;">
                        <div style="display: flex; align-items: center; gap: 0.45rem; font-size: 0.8rem; color: #64748b;">
                            <i class="bx bx-envelope" style="font-size: 0.95rem; color: #94a3b8;"></i>
                            <span class="staff-email" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $user->email }}</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.45rem; font-size: 0.8rem; color: #64748b;">
                            <i class="bx bx-phone" style="font-size: 0.95rem; color: #94a3b8;"></i>
                            <span>{{ $user->mobile ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <!-- Leave Status Section Box -->
                    @php
                        $leaveData = $user->current_leave;
                    @endphp
                    <div style="background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 12px; padding: 0.75rem 0.85rem; margin-bottom: 1.15rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.75rem; font-weight: 700; color: #475569;">Leave Status</span>
                            <span style="{{ $leaveData['badge_style'] }} padding: 0.15rem 0.6rem; border-radius: 20px; font-size: 0.7rem; font-weight: 700;">
                                {{ $leaveData['status'] }}
                            </span>
                        </div>
                        <p style="font-size: 0.73rem; color: {{ $leaveData['is_on_leave'] ? '#d97706' : '#94a3b8' }}; margin: 0.3rem 0 0 0; font-weight: 600;">
                            {{ $leaveData['type'] }} {{ $leaveData['dates'] ? '('.$leaveData['dates'].')' : '' }}
                        </p>
                    </div>
                </div>

                <!-- Footer Action Buttons -->
                @if(Auth::user()->hasAdminAccess())
                    <div style="display: flex; gap: 0.45rem; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 0.85rem;">
                        <button onclick="openViewModal({{ $user->id }})" style="flex: 1; background: #ffffff; border: 1px solid #e2e8f0; color: #475569; padding: 0.45rem 0.25rem; border-radius: 8px; font-size: 0.78rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.25rem; transition: background 0.15s ease;">
                            <i class="bx bx-show" style="font-size: 0.9rem;"></i> View
                        </button>

                        <button onclick="openEditModal({{ $user->id }})" style="flex: 1; background: #ffffff; border: 1px solid #e2e8f0; color: #3b82f6; padding: 0.45rem 0.25rem; border-radius: 8px; font-size: 0.78rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.25rem; transition: background 0.15s ease;">
                            <i class="bx bx-pencil" style="font-size: 0.9rem;"></i> Edit
                        </button>
                        
                        @if($user->id !== Auth::id())
                            <form action="{{ route('users.toggle_suspend', $user->id) }}" method="POST" style="flex: 1; margin: 0;">
                                @csrf
                                @if($user->is_suspended)
                                    <button type="submit" style="width: 100%; background: #fffbeb; border: 1px solid #fef3c7; color: #d97706; padding: 0.45rem 0.25rem; border-radius: 8px; font-size: 0.78rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.25rem;">
                                        <i class="bx bx-lock-open" style="font-size: 0.9rem;"></i> Activate
                                    </button>
                                @else
                                    <button type="submit" style="width: 100%; background: #fef2f2; border: 1px solid #fee2e2; color: #ef4444; padding: 0.45rem 0.25rem; border-radius: 8px; font-size: 0.78rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.25rem;">
                                        <i class="bx bx-block" style="font-size: 0.9rem;"></i> Suspend
                                    </button>
                                @endif
                            </form>
                        @endif
                    </div>
                @else
                    <div style="border-top: 1px solid #f1f5f9; padding-top: 0.75rem; text-align: center;">
                        <span style="color: #94a3b8; font-size: 0.78rem; font-style: italic;">View Only Mode</span>
                    </div>
                @endif

            </div>
        @empty
            <div style="grid-column: 1 / -1; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 3rem; text-align: center; color: #94a3b8; font-weight: 500;">
                No registered users found.
            </div>
        @endforelse
    </div>

    <!-- Add User Modal Dialog -->
    <div id="addUserModal" onclick="if(event.target === this) closeAddStaffModal()" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(15, 23, 42, 0.5); backdrop-filter: blur(6px); display: none; align-items: flex-start; justify-content: center; z-index: 1000; overflow-y: auto; padding: 2rem 1rem;">
        <div class="panel" style="width: 100%; max-width: 720px; margin: auto; position: relative; box-shadow: 0 20px 40px rgba(15, 23, 42, 0.15); border-color: var(--panel-border); background: #ffffff; border-radius: 16px; padding: 2rem 2.5rem 2.5rem;">

            <button type="button" onclick="closeAddStaffModal()" style="position: absolute; top: 1.25rem; right: 1.25rem; background: #f1f5f9; border: none; color: #64748b; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; cursor: pointer; transition: all 0.2s ease; z-index: 10;"><i class="bx bx-x"></i></button>

            <div class="panel-header" style="margin-bottom: 2rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem;">
                <h2 class="panel-title" style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0;"><i class="bx bx-user-plus" style="color:#10b981; margin-right:0.4rem;"></i> Add New Staff</h2>
                <p style="color:#64748b; font-size:0.85rem; margin: 0.25rem 0 0;">All fields marked <span style="color:#ef4444;">*</span> are mandatory.</p>
            </div>

            <form action="{{ route('do.add_user') }}" method="POST">
                @csrf

                {{-- ── Personal Information ── --}}
                <p style="font-size:0.8rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:1rem;">Personal Information</p>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div>
                        <label class="form-label" for="name">Full Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="name" name="name" placeholder="Enter full name" value="{{ old('name') }}" required>
                    </div>
                    <div>
                        <label class="form-label" for="email">Email Address <span style="color:#ef4444;">*</span></label>
                        <input type="email" class="form-control-dark" id="email" name="email" placeholder="Enter email address" value="{{ old('email') }}" required>
                    </div>
                    <div>
                        <label class="form-label" for="mobile">Mobile Number <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="mobile" name="mobile" placeholder="10-digit mobile number" value="{{ old('mobile') }}" required maxlength="10" minlength="10" pattern="[0-9]{10}" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                    </div>
                    <div>
                        <label class="form-label" for="father_name">Father's Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="father_name" name="father_name" placeholder="Enter father's name" value="{{ old('father_name') }}" required>
                    </div>
                    <div>
                        <label class="form-label" for="mother_name">Mother's Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="mother_name" name="mother_name" placeholder="Enter mother's name" value="{{ old('mother_name') }}" required>
                    </div>
                    <div>
                        <label class="form-label" for="date_of_birth">Date of Birth <span style="color:#ef4444;">*</span></label>
                        <input type="date" class="form-control-dark" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                    </div>
                    <div>
                        <label class="form-label" for="date_of_joining">Date of Joining <span style="color:#ef4444;">*</span></label>
                        <input type="date" class="form-control-dark" id="date_of_joining" name="date_of_joining" value="{{ old('date_of_joining') }}" required>
                    </div>
                    <div>
                        <label class="form-label" for="gender">Gender <span style="color:#ef4444;">*</span></label>
                        <select class="form-select-dark" id="gender" name="gender" required>
                            <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select gender</option>
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" for="marital_status">Marital Status <span style="color:#ef4444;">*</span></label>
                        <select class="form-select-dark" id="marital_status" name="marital_status" required>
                            <option value="" disabled {{ old('marital_status') ? '' : 'selected' }}>Select status</option>
                            <option value="Single" {{ old('marital_status') == 'Single' ? 'selected' : '' }}>Single</option>
                            <option value="Married" {{ old('marital_status') == 'Married' ? 'selected' : '' }}>Married</option>
                            <option value="Divorced" {{ old('marital_status') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                            <option value="Widowed" {{ old('marital_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                        </select>
                    </div>
                </div>

                {{-- ── Address ── --}}
                <p style="font-size:0.8rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:1rem; margin-top:0.5rem; border-top:1px solid #e2e8f0; padding-top:1rem;">Address</p>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div>
                        <label class="form-label" for="house_name">House Name/Number <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="house_name" name="house_name" placeholder="House name or number" value="{{ old('house_name') }}" required>
                    </div>
                    <div>
                        <label class="form-label" for="place">Place <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="place" name="place" placeholder="Enter place" value="{{ old('place') }}" required>
                    </div>
                    <div>
                        <label class="form-label" for="po">PO <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="po" name="po" placeholder="Post Office" value="{{ old('po') }}" required>
                    </div>
                    <div>
                        <label class="form-label" for="district">District <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="district" name="district" placeholder="Enter district" value="{{ old('district') }}" required>
                    </div>
                    <div>
                        <label class="form-label" for="state">State <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="state" name="state" placeholder="Enter state" value="{{ old('state') }}" required>
                    </div>
                    <div>
                        <label class="form-label" for="pin_code">PIN Code <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="pin_code" name="pin_code" placeholder="6-digit PIN code" value="{{ old('pin_code') }}" required maxlength="6" minlength="6" pattern="[0-9]{6}" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6)">
                    </div>
                </div>

                {{-- ── ID & Bank Details ── --}}
                <p style="font-size:0.8rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:1rem; margin-top:0.5rem; border-top:1px solid #e2e8f0; padding-top:1rem;">ID & Bank Details</p>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div>
                        <label class="form-label" for="aadhar_number">Aadhar Number <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="aadhar_number" name="aadhar_number" placeholder="1234 5678 9012" maxlength="14" oninput="this.value = this.value.replace(/\D/g, '').replace(/(\d{4})(?=\d)/g, '$1 ').trim()" value="{{ old('aadhar_number') }}" required>
                    </div>
                    <div>
                        <label class="form-label" for="pan_card_number">PAN Card Number <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="pan_card_number" name="pan_card_number" placeholder="10-character PAN Card Number" value="{{ old('pan_card_number') }}" required>
                    </div>
                    <div>
                        <label class="form-label" for="account_number">Bank Account Number <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="account_number" name="account_number" placeholder="Account Number" value="{{ old('account_number') }}" required>
                    </div>
                    <div>
                        <label class="form-label" for="bank_name">Bank Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="bank_name" name="bank_name" placeholder="Bank Name" value="{{ old('bank_name') }}" required>
                    </div>
                    <div>
                        <label class="form-label" for="bank_branch">Bank Branch <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="bank_branch" name="bank_branch" placeholder="Branch Name" value="{{ old('bank_branch') }}" required>
                    </div>
                    <div>
                        <label class="form-label" for="ifsc_code">IFSC Code <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="ifsc_code" name="ifsc_code" placeholder="IFSC Code" value="{{ old('ifsc_code') }}" required>
                    </div>
                </div>

                {{-- ── Account & Role Settings ── --}}
                <p style="font-size:0.8rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:1rem; margin-top:0.5rem; border-top:1px solid #e2e8f0; padding-top:1rem;">Account & Role Settings</p>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div>
                        <label class="form-label" for="designation">Designation <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="designation" name="designation" placeholder="e.g. Senior Accountant" value="{{ old('designation') }}" required>
                    </div>
                    @if(Auth::user()->hasAdminAccess())
                        <div>
                            <label class="form-label" for="role">User Role <span style="color:#ef4444;">*</span></label>
                            <select class="form-select-dark" id="role" name="role" required onchange="toggleRoleFields('add')">
                                <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select user role</option>
                                <option value="coo" {{ old('role') == 'coo' ? 'selected' : '' }}>COO</option>
                                <option value="project_manager" {{ old('role') == 'project_manager' ? 'selected' : '' }}>Project Manager</option>
                                <option value="hod" {{ old('role') == 'hod' ? 'selected' : '' }}>HOD</option>
                                <option value="social_aid" {{ old('role') == 'social_aid' ? 'selected' : '' }}>Social Aid Manager</option>
                                <option value="engineer" {{ old('role') == 'engineer' ? 'selected' : '' }}>Engineer</option>
                                <option value="reception" {{ old('role') == 'reception' ? 'selected' : '' }}>Reception</option>
                                <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>Employee</option>
                                <option value="others" {{ old('role') == 'others' ? 'selected' : '' }}>Others</option>
                            </select>
                        </div>
                    @endif
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1.5rem; align-items: end;">
                    <div id="add_assigned_hod_wrapper" style="display: block;">
                        <label class="form-label" for="assigned_hod_id">Assigned HOD <span style="color:#ef4444;" id="assigned_hod_star">*</span></label>
                        <select class="form-select-dark" id="assigned_hod_id" name="assigned_hod_id">
                            <option value="">-- Select Assigned HOD --</option>
                            @foreach($hods as $h)
                                <option value="{{ $h->id }}" {{ old('assigned_hod_id') == $h->id ? 'selected' : '' }}>{{ $h->name }} (HOD)</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="add_is_hr_wrapper" style="display: none; align-items: center; background: rgba(168, 85, 247, 0.05); border: 1px solid rgba(168, 85, 247, 0.2); padding: 0.75rem; border-radius: 8px; height: 42px;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; color: #6b21a8; font-weight: 700; font-size: 0.85rem; cursor: pointer; margin: 0;">
                            <input type="checkbox" id="is_hr" name="is_hr" value="1" {{ old('is_hr') ? 'checked' : '' }} style="width: 18px; height: 18px; cursor: pointer;">
                            <i class="bx bx-shield-quarter" style="font-size: 1.1rem;"></i> Designate as Central HR HOD
                        </label>
                    </div>

                    <div style="{{ Auth::user()->hasAdminAccess() ? '' : 'grid-column: 1 / -1;' }}">
                        <label class="form-label" for="password">Password <span style="color:#ef4444;">*</span></label>
                        <input type="password" class="form-control-dark" id="password" name="password" placeholder="Minimum 8 characters" autocomplete="new-password" required>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid #e2e8f0; padding-top: 1.25rem; margin-top: 1rem;">
                    <button type="button" onclick="closeAddStaffModal()" style="background: #ffffff; border: 1px solid #cbd5e1; color: #475569; padding: 0.65rem 1.25rem; border-radius: 10px; font-weight: 600; font-size: 0.88rem; cursor: pointer;">
                        Cancel
                    </button>
                    <button type="submit" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; border: none; border-radius: 10px; padding: 0.65rem 1.5rem; font-weight: 600; font-size: 0.88rem; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);">
                        <i class="bx bx-check-circle" style="font-size: 1.1rem; vertical-align: middle; margin-right: 0.25rem;"></i> Save Staff
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Staff Details Modal -->
    <div id="editUserModal" onclick="if(event.target === this) closeEditModal()" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(15, 23, 42, 0.5); backdrop-filter: blur(6px); display: none; align-items: flex-start; justify-content: center; z-index: 1000; overflow-y: auto; padding: 2rem 1rem;">
        <div class="panel" style="width: 100%; max-width: 720px; margin: auto; position: relative; box-shadow: 0 20px 40px rgba(15, 23, 42, 0.15); border-color: var(--panel-border); background: #ffffff; border-radius: 16px; padding: 0 2.5rem 2rem; overflow: visible;">

            <!-- Sticky Header -->
            <div style="position: sticky; top: 0; background: #ffffff; z-index: 30; padding: 1.75rem 0 1rem 0; border-bottom: 1px solid #e2e8f0; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h2 class="panel-title" style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0;"><i class="bx bx-pencil" style="color:#3b82f6; margin-right:0.4rem;"></i> Edit Staff Details</h2>
                    <p style="color:#64748b; font-size:0.85rem; margin: 0.25rem 0 0;">Update staff personal, address, and account information.</p>
                </div>
                <button type="button" onclick="closeEditModal()" style="background: #f1f5f9; border: none; color: #64748b; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; cursor: pointer; transition: all 0.2s ease; flex-shrink: 0;">
                    <i class="bx bx-x"></i>
                </button>
            </div>

            <form id="editUserForm" action="" method="POST">
                @csrf
                @method('PUT')

                {{-- Personal Information --}}
                <p style="font-size:0.8rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:1rem;">Personal Information</p>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div>
                        <label class="form-label" for="edit_name">Full Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="edit_name" name="name" required>
                    </div>
                    <div>
                        <label class="form-label" for="edit_email">Email Address <span style="color:#ef4444;">*</span></label>
                        <input type="email" class="form-control-dark" id="edit_email" name="email" required>
                    </div>
                    <div>
                        <label class="form-label" for="edit_mobile">Mobile Number <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="edit_mobile" name="mobile" placeholder="10-digit mobile number" required maxlength="10" minlength="10" pattern="[0-9]{10}" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                    </div>
                    <div>
                        <label class="form-label" for="edit_father_name">Father's Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="edit_father_name" name="father_name" required>
                    </div>
                    <div>
                        <label class="form-label" for="edit_mother_name">Mother's Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="edit_mother_name" name="mother_name" required>
                    </div>
                    <div>
                        <label class="form-label" for="edit_date_of_birth">Date of Birth <span style="color:#ef4444;">*</span></label>
                        <input type="date" class="form-control-dark" id="edit_date_of_birth" name="date_of_birth" required>
                    </div>
                    <div>
                        <label class="form-label" for="edit_date_of_joining">Date of Joining <span style="color:#ef4444;">*</span></label>
                        <input type="date" class="form-control-dark" id="edit_date_of_joining" name="date_of_joining" required>
                    </div>
                    <div>
                        <label class="form-label" for="edit_gender">Gender <span style="color:#ef4444;">*</span></label>
                        <select class="form-select-dark" id="edit_gender" name="gender" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" for="edit_marital_status">Marital Status <span style="color:#ef4444;">*</span></label>
                        <select class="form-select-dark" id="edit_marital_status" name="marital_status" required>
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                            <option value="Divorced">Divorced</option>
                            <option value="Widowed">Widowed</option>
                        </select>
                    </div>
                </div>

                {{-- Address --}}
                <p style="font-size:0.8rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:1rem; margin-top:0.5rem; border-top:1px solid #e2e8f0; padding-top:1rem;">Address</p>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div>
                        <label class="form-label" for="edit_house_name">House Name/Number <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="edit_house_name" name="house_name" required>
                    </div>
                    <div>
                        <label class="form-label" for="edit_place">Place <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="edit_place" name="place" required>
                    </div>
                    <div>
                        <label class="form-label" for="edit_po">PO <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="edit_po" name="po" required>
                    </div>
                    <div>
                        <label class="form-label" for="edit_district">District <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="edit_district" name="district" required>
                    </div>
                    <div>
                        <label class="form-label" for="edit_state">State <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="edit_state" name="state" required>
                    </div>
                    <div>
                        <label class="form-label" for="edit_pin_code">PIN Code <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="edit_pin_code" name="pin_code" placeholder="6-digit PIN code" required maxlength="6" minlength="6" pattern="[0-9]{6}" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6)">
                    </div>
                </div>

                {{-- ID & Bank Details --}}
                <p style="font-size:0.8rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:1rem; margin-top:0.5rem; border-top:1px solid #e2e8f0; padding-top:1rem;">ID & Bank Details</p>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div>
                        <label class="form-label" for="edit_aadhar_number">Aadhar Number <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="edit_aadhar_number" name="aadhar_number" placeholder="1234 5678 9012" maxlength="14" oninput="this.value = this.value.replace(/\D/g, '').replace(/(\d{4})(?=\d)/g, '$1 ').trim()" required>
                    </div>
                    <div>
                        <label class="form-label" for="edit_pan_card_number">PAN Card Number <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="edit_pan_card_number" name="pan_card_number" required>
                    </div>
                    <div>
                        <label class="form-label" for="edit_account_number">Bank Account Number <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="edit_account_number" name="account_number" required>
                    </div>
                    <div>
                        <label class="form-label" for="edit_bank_name">Bank Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="edit_bank_name" name="bank_name" required>
                    </div>
                    <div>
                        <label class="form-label" for="edit_bank_branch">Bank Branch <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="edit_bank_branch" name="bank_branch" required>
                    </div>
                    <div>
                        <label class="form-label" for="edit_ifsc_code">IFSC Code <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="edit_ifsc_code" name="ifsc_code" required>
                    </div>
                </div>

                {{-- Account & Role Settings --}}
                <p style="font-size:0.8rem; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.06em; margin-bottom:1rem; margin-top:0.5rem; border-top:1px solid #e2e8f0; padding-top:1rem;">Account & Role Settings</p>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                    <div>
                        <label class="form-label" for="edit_designation">Designation <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control-dark" id="edit_designation" name="designation" required>
                    </div>
                    @if(Auth::user()->hasAdminAccess())
                        <div>
                            <label class="form-label" for="edit_role">User Role <span style="color:#ef4444;">*</span></label>
                            <select class="form-select-dark" id="edit_role" name="role" required onchange="toggleRoleFields('edit')">
                                <option value="super_admin">Super Admin</option>
                                <option value="coo">COO</option>
                                <option value="project_manager">Project Manager</option>
                                <option value="hod">HOD</option>
                                <option value="social_aid">Social Aid Manager</option>
                                <option value="engineer">Engineer</option>
                                <option value="reception">Reception</option>
                                <option value="employee">Employee</option>
                                <option value="others">Others</option>
                            </select>
                        </div>
                    @endif
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1.5rem; align-items: end;">
                    <div id="edit_assigned_hod_wrapper" style="display: block;">
                        <label class="form-label" for="edit_assigned_hod_id">Assigned HOD <span style="color:#ef4444;" id="edit_assigned_hod_star">*</span></label>
                        <select class="form-select-dark" id="edit_assigned_hod_id" name="assigned_hod_id">
                            <option value="">-- Select Assigned HOD --</option>
                            @foreach($hods as $h)
                                <option value="{{ $h->id }}">{{ $h->name }} (HOD)</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="edit_is_hr_wrapper" style="display: none; align-items: center; background: rgba(168, 85, 247, 0.05); border: 1px solid rgba(168, 85, 247, 0.2); padding: 0.75rem; border-radius: 8px; height: 42px;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; color: #6b21a8; font-weight: 700; font-size: 0.85rem; cursor: pointer; margin: 0;">
                            <input type="checkbox" id="edit_is_hr" name="is_hr" value="1" style="width: 18px; height: 18px; cursor: pointer;">
                            <i class="bx bx-shield-quarter" style="font-size: 1.1rem;"></i> Designate as Central HR HOD
                        </label>
                    </div>

                    <div style="{{ Auth::user()->hasAdminAccess() ? '' : 'grid-column: 1 / -1;' }}">
                        <label class="form-label" for="edit_password">New Password <span style="color:#94a3b8; font-weight:normal; font-size:0.75rem;">(Leave blank to keep current)</span></label>
                        <input type="password" class="form-control-dark" id="edit_password" name="password" placeholder="Minimum 8 characters" autocomplete="new-password">
                    </div>
                </div>

                <!-- Sticky Footer -->
                <div style="position: sticky; bottom: 0; background: #ffffff; z-index: 30; padding: 1rem 0 0.5rem 0; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" onclick="closeEditModal()" style="background: #ffffff; border: 1px solid #cbd5e1; color: #475569; padding: 0.65rem 1.25rem; border-radius: 10px; font-weight: 600; font-size: 0.88rem; cursor: pointer;">
                        Cancel
                    </button>
                    <button type="submit" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: #ffffff; border: none; border-radius: 10px; padding: 0.65rem 1.5rem; font-weight: 600; font-size: 0.88rem; cursor: pointer; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);">
                        <i class="bx bx-save" style="font-size: 1.1rem; vertical-align: middle; margin-right: 0.25rem;"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Staff Details Modal -->
    <div id="viewUserModal" onclick="if(event.target === this) closeViewModal()" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(15, 23, 42, 0.55); backdrop-filter: blur(10px); display: none; align-items: flex-start; justify-content: center; z-index: 1000; overflow-y: auto; padding: 2.5rem 1rem;">
        <div class="panel" style="width: 100%; max-width: 840px; margin: auto; position: relative; box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.3); border: 1px solid rgba(226, 232, 240, 0.8); background: #ffffff; border-radius: 24px; padding: 0; overflow: hidden;">
            
            <!-- Hero Header Banner -->
            <div style="height: 140px; background: linear-gradient(135deg, #047857 0%, #10b981 60%, #059669 100%); position: relative; overflow: hidden;">
                <!-- Decorative background elements -->
                <div style="position: absolute; top: -30px; right: -30px; width: 160px; height: 160px; border-radius: 50%; background: rgba(255, 255, 255, 0.08);"></div>
                <div style="position: absolute; bottom: -40px; left: 20%; width: 200px; height: 200px; border-radius: 50%; background: rgba(255, 255, 255, 0.05);"></div>
                
                <!-- Close Button -->
                <button type="button" onclick="closeViewModal()" style="position: absolute; top: 1.25rem; right: 1.25rem; background: rgba(0, 0, 0, 0.25); color: #ffffff; width: 36px; height: 36px; border-radius: 50%; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; transition: all 0.2s ease; backdrop-filter: blur(4px); z-index: 10;" onmouseover="this.style.background='rgba(0,0,0,0.5)'; this.style.transform='scale(1.08)';" onmouseout="this.style.background='rgba(0,0,0,0.25)'; this.style.transform='scale(1)';">
                    <i class="bx bx-x"></i>
                </button>
            </div>

            <!-- Profile Summary Hero Header Content -->
            <div style="padding: 0 2.25rem 1.5rem; display: flex; align-items: flex-end; gap: 1.5rem; margin-top: -55px; position: relative; z-index: 2; border-bottom: 1px solid #f1f5f9; flex-wrap: wrap;">
                <!-- Avatar / Photo Circle -->
                <div style="width: 100px; height: 100px; border-radius: 50%; border: 4px solid #ffffff; background: linear-gradient(135deg, #ecfdf5 0%, #a7f3d0 100%); color: #047857; font-size: 2.35rem; font-weight: 800; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(15, 23, 42, 0.12); flex-shrink: 0; overflow: hidden; position: relative;" id="view_avatar_container">
                    <span id="view_avatar_initial">U</span>
                    <img id="view_avatar_img" src="" alt="Staff Photo" style="width: 100%; height: 100%; object-fit: cover; display: none; position: absolute; top: 0; left: 0;">
                </div>
                
                <div style="flex: 1; min-width: 250px; padding-top: 55px;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 0.25rem;">
                        <h2 id="view_name" style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin: 0; line-height: 1.25;">-</h2>
                        <div id="view_status" style="display: inline-block;">-</div>
                    </div>
                    <p id="view_designation" style="font-size: 0.95rem; font-weight: 700; color: #059669; margin: 0 0 0.65rem 0;">-</p>
                    
                    <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
                        <span id="view_role" style="background: #f1f5f9; color: #334155; padding: 0.28rem 0.85rem; border-radius: 20px; font-size: 0.78rem; font-weight: 700; border: 1px solid #e2e8f0; display: inline-flex; align-items: center; gap: 0.3rem;">-</span>
                        <span style="color: #64748b; font-size: 0.825rem; font-weight: 500; display: inline-flex; align-items: center; gap: 0.35rem; background: #f8fafc; padding: 0.28rem 0.85rem; border-radius: 20px; border: 1px solid #e2e8f0;">
                            <i class="bx bx-envelope" style="color: #059669; font-size: 0.95rem;"></i> <span id="view_email_text">-</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Modal Content Body -->
            <div style="padding: 2rem 2.25rem 2.25rem;">

                <!-- Section 1: Personal Details -->
                <div style="margin-bottom: 2rem;">
                    <div style="border-left: 4px solid #10b981; padding-left: 0.65rem; font-size: 0.825rem; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 1rem;">
                        Personal Details
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.15rem;">
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 0.9rem 1.1rem; transition: transform 0.15s ease;">
                            <span style="display: block; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; margin-bottom: 0.35rem;">Mobile Number</span>
                            <div id="view_mobile" style="font-size: 0.95rem; font-weight: 700; color: #0f172a;">-</div>
                        </div>

                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 0.9rem 1.1rem; transition: transform 0.15s ease;">
                            <span style="display: block; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; margin-bottom: 0.35rem;">Father's Name</span>
                            <div id="view_father_name" style="font-size: 0.95rem; font-weight: 700; color: #0f172a;">-</div>
                        </div>

                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 0.9rem 1.1rem; transition: transform 0.15s ease;">
                            <span style="display: block; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; margin-bottom: 0.35rem;">Mother's Name</span>
                            <div id="view_mother_name" style="font-size: 0.95rem; font-weight: 700; color: #0f172a;">-</div>
                        </div>

                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 0.9rem 1.1rem; transition: transform 0.15s ease;">
                            <span style="display: block; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; margin-bottom: 0.35rem;">Date of Birth</span>
                            <div id="view_dob" style="font-size: 0.95rem; font-weight: 700; color: #0f172a;">-</div>
                        </div>

                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 0.9rem 1.1rem; transition: transform 0.15s ease;">
                            <span style="display: block; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; margin-bottom: 0.35rem;">Date of Joining</span>
                            <div id="view_doj" style="font-size: 0.95rem; font-weight: 700; color: #0f172a;">-</div>
                        </div>

                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 0.9rem 1.1rem; transition: transform 0.15s ease;">
                            <span style="display: block; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; margin-bottom: 0.35rem;">Gender</span>
                            <div id="view_gender" style="font-size: 0.95rem; font-weight: 700; color: #0f172a;">-</div>
                        </div>

                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 0.9rem 1.1rem; transition: transform 0.15s ease;">
                            <span style="display: block; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; margin-bottom: 0.35rem;">Marital Status</span>
                            <div id="view_marital_status" style="font-size: 0.95rem; font-weight: 700; color: #0f172a;">-</div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Residential Address -->
                <div style="margin-bottom: 2rem;">
                    <div style="border-left: 4px solid #10b981; padding-left: 0.65rem; font-size: 0.825rem; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 1rem;">
                        Residential Address
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.15rem;">
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 0.9rem 1.1rem;">
                            <span style="display: block; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; margin-bottom: 0.35rem;">House Name/No</span>
                            <div id="view_house_name" style="font-size: 0.95rem; font-weight: 700; color: #0f172a;">-</div>
                        </div>

                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 0.9rem 1.1rem;">
                            <span style="display: block; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; margin-bottom: 0.35rem;">Place</span>
                            <div id="view_place" style="font-size: 0.95rem; font-weight: 700; color: #0f172a;">-</div>
                        </div>

                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 0.9rem 1.1rem;">
                            <span style="display: block; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; margin-bottom: 0.35rem;">Post Office (PO)</span>
                            <div id="view_po" style="font-size: 0.95rem; font-weight: 700; color: #0f172a;">-</div>
                        </div>

                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 0.9rem 1.1rem;">
                            <span style="display: block; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; margin-bottom: 0.35rem;">District</span>
                            <div id="view_district" style="font-size: 0.95rem; font-weight: 700; color: #0f172a;">-</div>
                        </div>

                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 0.9rem 1.1rem;">
                            <span style="display: block; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; margin-bottom: 0.35rem;">State</span>
                            <div id="view_state" style="font-size: 0.95rem; font-weight: 700; color: #0f172a;">-</div>
                        </div>

                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 0.9rem 1.1rem;">
                            <span style="display: block; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; margin-bottom: 0.35rem;">PIN Code</span>
                            <div id="view_pin_code" style="font-size: 0.95rem; font-weight: 700; color: #0f172a;">-</div>
                        </div>

                        <div style="grid-column: 1 / -1; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 0.9rem 1.1rem;">
                            <span style="display: flex; align-items: center; gap: 0.35rem; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; margin-bottom: 0.35rem;">
                                <i class="bx bx-map" style="color: #10b981; font-size: 0.95rem;"></i> Complete Address
                            </span>
                            <div id="view_address" style="font-size: 0.92rem; font-weight: 600; color: #334155; line-height: 1.5;">-</div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: ID & Bank Details -->
                <div style="margin-bottom: 1.5rem;">
                    <div style="border-left: 4px solid #10b981; padding-left: 0.65rem; font-size: 0.825rem; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 1rem;">
                        ID & Bank Details
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.15rem;">
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 0.9rem 1.1rem;">
                            <span style="display: block; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; margin-bottom: 0.35rem;">Aadhar Number</span>
                            <div id="view_aadhar" style="font-size: 0.95rem; font-weight: 700; color: #0f172a; letter-spacing: 0.02em;">-</div>
                        </div>

                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 0.9rem 1.1rem;">
                            <span style="display: block; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; margin-bottom: 0.35rem;">PAN Card Number</span>
                            <div id="view_pan" style="font-size: 0.95rem; font-weight: 800; color: #059669; text-transform: uppercase; letter-spacing: 0.05em;">-</div>
                        </div>

                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 0.9rem 1.1rem;">
                            <span style="display: block; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; margin-bottom: 0.35rem;">Account Number</span>
                            <div id="view_account" style="font-size: 0.95rem; font-weight: 700; color: #0f172a;">-</div>
                        </div>

                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 0.9rem 1.1rem;">
                            <span style="display: block; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; margin-bottom: 0.35rem;">Bank Name</span>
                            <div id="view_bank_name" style="font-size: 0.95rem; font-weight: 700; color: #0f172a;">-</div>
                        </div>

                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 0.9rem 1.1rem;">
                            <span style="display: block; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; margin-bottom: 0.35rem;">Bank Branch</span>
                            <div id="view_branch" style="font-size: 0.95rem; font-weight: 700; color: #0f172a;">-</div>
                        </div>

                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 0.9rem 1.1rem;">
                            <span style="display: block; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; margin-bottom: 0.35rem;">IFSC Code</span>
                            <div id="view_ifsc" style="font-size: 0.95rem; font-weight: 800; color: #059669; text-transform: uppercase; letter-spacing: 0.05em;">-</div>
                        </div>
                    </div>
                </div>

                <!-- Assigned Projects Section (Visible only for Project Managers & Engineers) -->
                <div id="assigned_projects_wrapper" style="display: none; margin-top: 2rem; border-top: 1px solid #e2e8f0; padding-top: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1rem;">
                        <div style="border-left: 4px solid #10b981; padding-left: 0.65rem; font-size: 0.825rem; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 0.06em;">
                            Assigned Projects
                        </div>

                        <!-- Running & Completed Project Counters -->
                        <div style="display: flex; align-items: center; gap: 0.6rem;">
                            <span style="background: #eff6ff; border: 1px solid #bfdbfe; color: #2563eb; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.78rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;">
                                <i class="bx bx-loader-circle" style="font-size: 0.95rem;"></i> Running: <strong id="running_projects_count">0</strong>
                            </span>
                            <span style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #059669; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.78rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;">
                                <i class="bx bx-check-circle" style="font-size: 0.95rem;"></i> Completed: <strong id="completed_projects_count">0</strong>
                            </span>
                        </div>
                    </div>

                    <div style="max-height: 250px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 14px; background-color: #ffffff;">
                        <table class="table-custom" style="margin: 0; font-size: 0.88rem; width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #10b981; color: #ffffff;">
                                    <th style="padding: 0.75rem 1.25rem; font-weight: 700; text-align: left; font-size: 0.825rem; text-transform: uppercase; letter-spacing: 0.04em;">RCFI ID</th>
                                    <th style="padding: 0.75rem 1.25rem; font-weight: 700; text-align: center; font-size: 0.825rem; text-transform: uppercase; letter-spacing: 0.04em;">Status</th>
                                </tr>
                            </thead>
                            <tbody id="projects_table_body">
                                <tr>
                                    <td colspan="2" style="text-align: center; color: #64748b; padding: 1.5rem;">Loading assigned projects...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Modal Scripts -->
    <script>
        // Client-side search and filters
        function filterStaffs() {
            const searchVal = document.getElementById('staffSearchInput').value.toLowerCase();
            const roleVal = document.getElementById('roleFilter').value.toLowerCase();
            const statusVal = document.getElementById('statusFilter').value.toLowerCase();
            const leaveVal = document.getElementById('leaveFilter').value.toLowerCase();

            const rows = document.querySelectorAll('.staff-row');
            
            rows.forEach(row => {
                const nameEl = row.querySelector('.staff-name');
                const emailEl = row.querySelector('.staff-email');
                const desigEl = row.querySelector('.staff-designation');

                const name = nameEl ? nameEl.innerText.toLowerCase() : '';
                const email = emailEl ? emailEl.innerText.toLowerCase() : '';
                const designation = desigEl ? desigEl.innerText.toLowerCase() : '';
                const role = (row.dataset.role || '').toLowerCase();
                const status = (row.dataset.status || '').toLowerCase();
                const leaveStatus = (row.dataset.leaveStatus || '').toLowerCase();

                const matchesSearch = name.includes(searchVal) || email.includes(searchVal) || designation.includes(searchVal);
                const matchesRole = !roleVal || role === roleVal;
                const matchesStatus = !statusVal || status === statusVal;
                const matchesLeave = !leaveVal || leaveStatus.includes(leaveVal) || (leaveVal === 'no_leave' && leaveStatus === 'no_leave');

                if (matchesSearch && matchesRole && matchesStatus && matchesLeave) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }



        function clearFilters() {
            if (document.getElementById('staffSearchInput')) document.getElementById('staffSearchInput').value = '';
            if (document.getElementById('roleFilter')) document.getElementById('roleFilter').value = '';
            if (document.getElementById('statusFilter')) document.getElementById('statusFilter').value = '';
            if (document.getElementById('deptFilter')) document.getElementById('deptFilter').value = '';
            if (document.getElementById('leaveFilter')) document.getElementById('leaveFilter').value = '';
            filterStaffs();
        }

        // Dynamic toggle function for Assigned HOD dropdown vs Is HR checkbox
        function toggleRoleFields(mode) {
            const prefix = mode === 'edit' ? 'edit_' : '';
            const roleEl = document.getElementById(prefix + 'role');
            if (!roleEl) return;

            const role = (roleEl.value || '').toLowerCase();
            const hodWrapper = document.getElementById(mode === 'edit' ? 'edit_assigned_hod_wrapper' : 'add_assigned_hod_wrapper');
            const hrWrapper = document.getElementById(mode === 'edit' ? 'edit_is_hr_wrapper' : 'add_is_hr_wrapper');
            const hodSelect = document.getElementById(prefix + 'assigned_hod_id');

            const excludedRoles = ['coo', 'super_admin', 'hod', '1', '2', '4'];
            const isExcluded = excludedRoles.includes(role);

            if (role === 'hod' || role === '4') {
                if (hrWrapper) hrWrapper.style.display = 'flex';
                if (hodWrapper) hodWrapper.style.display = 'none';
                if (hodSelect) { hodSelect.required = false; hodSelect.value = ''; }
            } else if (!isExcluded && role !== '') {
                if (hodWrapper) hodWrapper.style.display = 'block';
                if (hrWrapper) hrWrapper.style.display = 'none';
                if (hodSelect) hodSelect.required = true;
            } else {
                if (hodWrapper) hodWrapper.style.display = 'none';
                if (hrWrapper) hrWrapper.style.display = 'none';
                if (hodSelect) { hodSelect.required = false; hodSelect.value = ''; }
            }
        }

        // Add Staff Modal
        function openAddStaffModal() {
            const modal = document.getElementById('addUserModal');
            if (modal) {
                modal.style.display = "flex";
                toggleRoleFields('add');
            } else {
                console.error('addUserModal not found in DOM');
            }
        }

        function closeAddStaffModal() {
            const modal = document.getElementById('addUserModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        function openModal() {
            openAddStaffModal();
        }

        function closeModal() {
            closeAddStaffModal();
        }

        // Edit User Modal
        function openEditModal(userId) {
            const editModal = document.getElementById('editUserModal');
            if (editModal) editModal.style.display = 'flex';

            const form = document.getElementById('editUserForm');
            if (form) form.action = '/admin/users/' + userId;

            // Fetch user details via AJAX to populate edit modal
            fetch(`/admin/users/${userId}/details`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const u = data.user;
                        const setVal = (id, val) => {
                            const el = document.getElementById(id);
                            if (el) el.value = (val && val !== 'N/A') ? val : '';
                        };

                        setVal('edit_name', u.name);
                        setVal('edit_email', u.email);
                        setVal('edit_mobile', u.mobile);
                        setVal('edit_father_name', u.father_name);
                        setVal('edit_mother_name', u.mother_name);
                        setVal('edit_date_of_birth', u.date_of_birth);
                        setVal('edit_date_of_joining', u.date_of_joining);
                        setVal('edit_gender', u.gender);
                        setVal('edit_marital_status', u.marital_status);
                        setVal('edit_house_name', u.house_name);
                        setVal('edit_place', u.place);
                        setVal('edit_po', u.po);
                        setVal('edit_district', u.district);
                        setVal('edit_state', u.state);
                        setVal('edit_pin_code', u.pin_code);
                        setVal('edit_aadhar_number', u.aadhar_number);
                        setVal('edit_pan_card_number', u.pan_card_number);
                        setVal('edit_account_number', u.account_number);
                        setVal('edit_bank_name', u.bank_name);
                        setVal('edit_bank_branch', u.bank_branch);
                        setVal('edit_ifsc_code', u.ifsc_code);
                        setVal('edit_designation', u.designation);
                        setVal('edit_assigned_hod_id', u.assigned_hod_id || u.hod_id);

                        const editIsHrCheckbox = document.getElementById('edit_is_hr');
                        if (editIsHrCheckbox) {
                            editIsHrCheckbox.checked = !!u.is_hr;
                        }

                        const roleSelect = document.getElementById('edit_role');
                        if (roleSelect && u.raw_role) {
                            roleSelect.value = u.raw_role;
                        }

                        toggleRoleFields('edit');

                        const passwordInput = document.getElementById('edit_password');
                        if (passwordInput) passwordInput.value = '';
                    } else {
                        alert('Failed to load user details for editing.');
                    }
                })
                .catch(err => {
                    console.error('Error fetching details:', err);
                    alert('Error loading staff details.');
                });
        }

        function closeEditModal() {
            const modal = document.getElementById('editUserModal');
            if (modal) modal.style.display = 'none';
        }

        // View Details Modal
        function openViewModal(userId) {
            // Reset placeholders
            const fields = ['view_name', 'view_email', 'view_mobile', 'view_designation', 'view_role', 'view_status', 'view_father_name', 'view_mother_name', 'view_dob', 'view_doj', 'view_gender', 'view_marital_status', 'view_house_name', 'view_place', 'view_po', 'view_district', 'view_state', 'view_pin_code', 'view_address', 'view_aadhar', 'view_pan', 'view_account', 'view_bank_name', 'view_branch', 'view_ifsc'];
            fields.forEach(f => {
                const el = document.getElementById(f);
                if (el) el.innerText = 'Loading...';
            });
            
            const projectsWrapper = document.getElementById('assigned_projects_wrapper');
            if (projectsWrapper) projectsWrapper.style.display = 'none';

            const tableBody = document.getElementById('projects_table_body');
            if (tableBody) tableBody.innerHTML = `<tr><td colspan="4" style="text-align: center; color: var(--text-muted); padding: 1rem;">Loading assigned projects...</td></tr>`;
            
            // Show modal
            const viewModal = document.getElementById('viewUserModal');
            if (viewModal) viewModal.style.display = 'flex';
            
            // Fetch user detail JSON via AJAX
            fetch(`/admin/users/${userId}/details`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const u = data.user;
                        
                        const setTxt = (id, val) => {
                            const el = document.getElementById(id);
                            if (el) el.innerText = (val && String(val).trim() && val !== 'N/A') ? val : '-';
                        };

                        setTxt('view_name', u.name);
                        
                        // Avatar Photo or Initial
                        const avatarImg = document.getElementById('view_avatar_img');
                        const avatarInitial = document.getElementById('view_avatar_initial');
                        const photoUrl = u.photo_url || (u.profile && u.profile.photo ? '/' + u.profile.photo : null);

                        if (photoUrl && avatarImg) {
                            avatarImg.src = photoUrl;
                            avatarImg.style.display = 'block';
                            if (avatarInitial) avatarInitial.style.display = 'none';
                        } else {
                            if (avatarImg) avatarImg.style.display = 'none';
                            if (avatarInitial) {
                                avatarInitial.innerText = (u.name || 'U').charAt(0).toUpperCase();
                                avatarInitial.style.display = 'inline';
                            }
                        }

                        const emailTextEl = document.getElementById('view_email_text');
                        if (emailTextEl) {
                            emailTextEl.innerText = (u.email && u.email !== 'N/A') ? u.email : '-';
                        } else if (document.getElementById('view_email')) {
                            document.getElementById('view_email').innerText = (u.email && u.email !== 'N/A') ? u.email : '-';
                        }

                        setTxt('view_mobile', u.mobile);
                        setTxt('view_designation', u.designation);
                        
                        let displayRole = data.role_name || u.role_name || u.role;
                        setTxt('view_role', displayRole);

                        // Personal details
                        setTxt('view_father_name', u.father_name);
                        setTxt('view_mother_name', u.mother_name);
                        setTxt('view_dob', u.date_of_birth);
                        setTxt('view_doj', u.date_of_joining);
                        setTxt('view_gender', u.gender);
                        setTxt('view_marital_status', u.marital_status);

                        // Address details
                        setTxt('view_house_name', u.house_name);
                        setTxt('view_place', u.place);
                        setTxt('view_po', u.po);
                        setTxt('view_district', u.district);
                        setTxt('view_state', u.state);
                        setTxt('view_pin_code', u.pin_code);

                        const addrParts = [
                            u.house_name,
                            u.place,
                            u.po ? 'PO: ' + u.po : null,
                            u.district,
                            u.state ? u.state + (u.pin_code ? ' - ' + u.pin_code : '') : u.pin_code
                        ].filter(p => p && String(p).trim() && p !== 'N/A');

                        const fullAddress = addrParts.length ? addrParts.join(', ') : (u.address && u.address !== 'N/A' ? u.address : '-');
                        setTxt('view_address', fullAddress);

                        // ID & Bank details
                        setTxt('view_aadhar', u.aadhar_number);
                        setTxt('view_pan', u.pan_card_number ? u.pan_card_number.toUpperCase() : null);
                        setTxt('view_account', u.account_number);
                        setTxt('view_bank_name', u.bank_name);
                        setTxt('view_branch', u.bank_branch);
                        setTxt('view_ifsc', u.ifsc_code ? u.ifsc_code.toUpperCase() : null);
                        
                        // Set status badge
                        const statusContainer = document.getElementById('view_status');
                        if (statusContainer) {
                            if (u.is_suspended) {
                                statusContainer.innerHTML = `<span style="background-color: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">SUSPENDED</span>`;
                            } else {
                                statusContainer.innerHTML = `<span style="background-color: rgba(16, 185, 129, 0.1); color: #10b981; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700;">ACTIVE</span>`;
                            }
                        }

                        // Populate project counters
                        const runningCountEl = document.getElementById('running_projects_count');
                        const completedCountEl = document.getElementById('completed_projects_count');
                        if (runningCountEl) runningCountEl.innerText = data.running_projects_count ?? 0;
                        if (completedCountEl) completedCountEl.innerText = data.completed_projects_count ?? 0;

                        // Show Assigned Projects section ONLY for Project Managers and Engineers
                        if (u.is_pm_or_engineer && projectsWrapper) {
                            projectsWrapper.style.display = 'block';
                            if (tableBody) {
                                if (data.projects && data.projects.length > 0) {
                                    tableBody.innerHTML = data.projects.map(p => {
                                        let isCompleted = ['completed', 'done', 'handover', 'finished'].includes((p.status || '').toLowerCase());
                                        let badgeStyle = isCompleted 
                                            ? 'background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;' 
                                            : 'background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe;';
                                        return `
                                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                                <td style="font-weight: 700; color: #0f172a; padding: 0.75rem 1.25rem; font-size: 0.88rem;">${p.project_id}</td>
                                                <td style="text-align: center; padding: 0.75rem 1.25rem;">
                                                    <span style="${badgeStyle} padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.78rem; font-weight: 700; display: inline-block;">
                                                        ${p.status || 'Pending'}
                                                    </span>
                                                </td>
                                            </tr>
                                        `;
                                    }).join('');
                                } else {
                                    tableBody.innerHTML = `<tr><td colspan="2" style="text-align: center; color: #94a3b8; padding: 2rem; font-weight: 500;">No projects assigned to this user.</td></tr>`;
                                }
                            }
                        } else if (projectsWrapper) {
                            projectsWrapper.style.display = 'none';
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
            const modal = document.getElementById('viewUserModal');
            if (modal) modal.style.display = 'none';
        }

        // Global Window Bindings for inline onclick handlers
        window.openAddStaffModal = openAddStaffModal;
        window.closeAddStaffModal = closeAddStaffModal;
        window.openModal = openModal;
        window.closeModal = closeModal;
        window.openEditModal = openEditModal;
        window.closeEditModal = closeEditModal;
        window.openViewModal = openViewModal;
        window.closeViewModal = closeViewModal;

        // Close open modals when pressing Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddStaffModal();
                closeEditModal();
                closeViewModal();
            }
        });
    </script>

    <!-- Automatically open add modal if validation error occurs on new user creation -->
    @if (isset($errors) && $errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                if (typeof openModal === 'function') openModal();
            });
        </script>
    @endif

@endsection
