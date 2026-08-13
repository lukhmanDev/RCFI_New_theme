<div style="display: flex; flex-direction: column; gap: 1.75rem;">

    <!-- Flash Notifications -->
    @if($successMessage)
        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #059669; padding: 1rem 1.25rem; border-radius: 12px; font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; justify-content: space-between;">
            <div><i class="bx bx-check-circle" style="font-size: 1.2rem; vertical-align: middle; margin-right: 0.35rem;"></i> {{ $successMessage }}</div>
            <button type="button" wire:click="$set('successMessage', '')" style="background: none; border: none; cursor: pointer; color: #059669;"><i class="bx bx-x" style="font-size: 1.2rem;"></i></button>
        </div>
    @endif

    @if($errorMessage)
        <div style="background: #fef2f2; border: 1px solid #fee2e2; color: #ef4444; padding: 1rem 1.25rem; border-radius: 12px; font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; justify-content: space-between;">
            <div><i class="bx bx-error-circle" style="font-size: 1.2rem; vertical-align: middle; margin-right: 0.35rem;"></i> {{ $errorMessage }}</div>
            <button type="button" wire:click="$set('errorMessage', '')" style="background: none; border: none; cursor: pointer; color: #ef4444;"><i class="bx bx-x" style="font-size: 1.2rem;"></i></button>
        </div>
    @endif

    <!-- Header & Navigation Tabs -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);">
        <div style="max-width: 600px;">
            <h1 style="color: #0f172a; font-size: 1.75rem; font-weight: 800; margin: 0;">COO Staff Operations Dashboard</h1>
            <p style="color: #64748b; font-size: 0.88rem; margin-top: 0.35rem; margin-bottom: 0;">Executive oversight of organizational staff members, daily attendance, leave approvals, and workload distribution.</p>
        </div>

        <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
            @if(Auth::user()->isSuperAdmin())
                <button type="button" wire:click="openAddStaffModal" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; border: none; border-radius: 10px; padding: 0.65rem 1.25rem; font-weight: 700; font-size: 0.88rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.45rem; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.25);">
                    <i class="bx bx-user-plus" style="font-size: 1.15rem;"></i> Add Staff
                </button>
            @endif

            <div style="display: flex; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 12px; padding: 0.25rem; gap: 0.25rem;">
                <button type="button" wire:click="setTab('directory')" style="padding: 0.5rem 0.95rem; border-radius: 8px; font-size: 0.82rem; font-weight: 700; border: none; cursor: pointer; transition: all 0.2s ease; {{ $activeTab === 'directory' ? 'background: #ffffff; color: #0f172a; box-shadow: 0 2px 6px rgba(0,0,0,0.06);' : 'background: transparent; color: #64748b;' }}">
                    <i class="bx bx-group" style="vertical-align: middle; font-size: 1rem;"></i> Staff Directory
                </button>
                <button type="button" wire:click="setTab('tree')" style="padding: 0.5rem 0.95rem; border-radius: 8px; font-size: 0.82rem; font-weight: 700; border: none; cursor: pointer; transition: all 0.2s ease; {{ $activeTab === 'tree' ? 'background: #ffffff; color: #0f172a; box-shadow: 0 2px 6px rgba(0,0,0,0.06);' : 'background: transparent; color: #64748b;' }}">
                    <i class="bx bx-git-repo-forked" style="vertical-align: middle; font-size: 1rem;"></i> Team Hierarchy Tree
                </button>
                <button type="button" wire:click="setTab('leave')" style="padding: 0.5rem 0.95rem; border-radius: 8px; font-size: 0.82rem; font-weight: 700; border: none; cursor: pointer; transition: all 0.2s ease; {{ $activeTab === 'leave' ? 'background: #ffffff; color: #0f172a; box-shadow: 0 2px 6px rgba(0,0,0,0.06);' : 'background: transparent; color: #64748b;' }}">
                    <i class="bx bx-calendar-event" style="vertical-align: middle; font-size: 1rem;"></i> Leave Queue ({{ count($pendingLeaveRequests) }})
                </button>
                <button type="button" wire:click="setTab('analytics')" style="padding: 0.5rem 0.95rem; border-radius: 8px; font-size: 0.82rem; font-weight: 700; border: none; cursor: pointer; transition: all 0.2s ease; {{ $activeTab === 'analytics' ? 'background: #ffffff; color: #0f172a; box-shadow: 0 2px 6px rgba(0,0,0,0.06);' : 'background: transparent; color: #64748b;' }}">
                    <i class="bx bx-pie-chart-alt-2" style="vertical-align: middle; font-size: 1rem;"></i> Roles & Analytics
                </button>
            </div>
        </div>
    </div>

    <!-- Executive Stat Cards -->
    <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1.25rem; width: 100%;">
        <!-- Total Staff -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02); display: flex; align-items: center; gap: 1rem;">
            <div style="background: #eff6ff; color: #3b82f6; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="bx bx-user-voice"></i>
            </div>
            <div>
                <span style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em;">Total Staff</span>
                <h2 style="color: #0f172a; font-size: 1.6rem; font-weight: 800; margin: 0.1rem 0;">{{ $totalStaffCount }}</h2>
                <span style="color: #3b82f6; font-size: 0.75rem; font-weight: 600;">{{ $activeStaffCount }} Active / {{ $suspendedStaffCount }} Suspended</span>
            </div>
        </div>

        <!-- On Leave Today -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02); display: flex; align-items: center; gap: 1rem;">
            <div style="background: #fffbeb; color: #d97706; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="bx bx-calendar-minus"></i>
            </div>
            <div>
                <span style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em;">Staff On Leave</span>
                <h2 style="color: #d97706; font-size: 1.6rem; font-weight: 800; margin: 0.1rem 0;">{{ count($staffOnLeaveToday) }}</h2>
                <span style="color: #b45309; font-size: 0.75rem; font-weight: 600;">Approved Absences</span>
            </div>
        </div>

        <!-- Pending Leave Queue -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02); display: flex; align-items: center; gap: 1rem;">
            <div style="background: #fef2f2; color: #ef4444; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;">
                <i class="bx bx-time-five"></i>
            </div>
            <div>
                <span style="color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em;">Leave Requests</span>
                <h2 style="color: #ef4444; font-size: 1.6rem; font-weight: 800; margin: 0.1rem 0;">{{ count($pendingLeaveRequests) }}</h2>
                <span style="color: #dc2626; font-size: 0.75rem; font-weight: 600;">Awaiting Approval</span>
            </div>
        </div>
    </div>

    <!-- TAB 0: TEAM HIERARCHY TREE -->
    @if($activeTab === 'tree')
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 2rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 1rem;">
                <div>
                    <h2 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="bx bx-git-repo-forked" style="color: #10b981; font-size: 1.4rem;"></i> Organization Team Structure & Staff Tree
                    </h2>
                    <p style="color: #64748b; font-size: 0.82rem; margin-top: 0.25rem; margin-bottom: 0;">Interactive departmental hierarchy showing HODs and their assigned staff members.</p>
                </div>
                <span style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; padding: 0.3rem 0.75rem; border-radius: 20px; font-size: 0.78rem; font-weight: 700;">
                    Live Hierarchy Tree
                </span>
            </div>

            <div style="max-width: 900px; margin: 0 auto; padding: 1rem 0;">
                @if(isset($hierarchyTree) && !empty($hierarchyTree['name']))
                    <div style="display: flex; flex-direction: column; align-items: center;">
                        <!-- Root Executive Node (COO or HOD) -->
                        <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff; border-radius: 16px; padding: 1.25rem 1.75rem; width: 100%; max-width: 420px; box-shadow: 0 10px 25px rgba(15, 23, 42, 0.15); text-align: center; position: relative;">
                            <div style="width: 52px; height: 52px; border-radius: 50%; background: #10b981; color: #ffffff; font-weight: 800; font-size: 1.3rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
                                {{ strtoupper(substr($hierarchyTree['name'], 0, 1)) }}
                            </div>
                            <h3 style="font-size: 1.1rem; font-weight: 800; color: #ffffff; margin: 0;">{{ $hierarchyTree['name'] }}</h3>
                            <span style="display: inline-block; background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(52, 211, 153, 0.3); padding: 0.2rem 0.65rem; border-radius: 12px; font-size: 0.75rem; font-weight: 700; margin-top: 0.4rem;">
                                {{ $hierarchyTree['role'] }}
                            </span>
                            @if(!empty($hierarchyTree['email']))
                                <div style="font-size: 0.78rem; color: #94a3b8; margin-top: 0.4rem;"><i class="bx bx-envelope"></i> {{ $hierarchyTree['email'] }}</div>
                            @endif
                        </div>

                        @if(!empty($hierarchyTree['children']))
                            <!-- Vertical Trunk Line -->
                            <div style="width: 3px; height: 35px; background: #cbd5e1; margin: 0 auto;"></div>

                            <!-- Children Nodes Level (HODs or Staff) -->
                            <div style="width: 100%; display: flex; flex-direction: column; gap: 2rem;">
                                @foreach($hierarchyTree['children'] as $child)
                                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
                                        <!-- HOD / Department Header -->
                                        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.85rem; margin-bottom: 1rem;">
                                            <div style="display: flex; align-items: center; gap: 0.85rem;">
                                                <div style="width: 42px; height: 42px; border-radius: 50%; background: #3b82f6; color: #ffffff; font-weight: 800; font-size: 1.1rem; display: flex; align-items: center; justify-content: center;">
                                                    {{ strtoupper(substr($child['name'], 0, 1)) }}
                                                </div>
                                                <div>
                                                    <h4 style="font-size: 1rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 0.4rem;">
                                                        {{ $child['name'] }}
                                                        @if($child['is_hr'])
                                                            <span style="background: #f3e8ff; color: #9333ea; border: 1px solid #d8b4fe; padding: 0.1rem 0.45rem; border-radius: 8px; font-size: 0.7rem; font-weight: 800;">HR HOD</span>
                                                        @endif
                                                    </h4>
                                                    <span style="font-size: 0.78rem; font-weight: 700; color: #2563eb;">{{ $child['role'] }} {{ !empty($child['designation']) ? '&bull; ' . $child['designation'] : '' }}</span>
                                                </div>
                                            </div>
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <span style="background: #eff6ff; color: #2563eb; padding: 0.25rem 0.65rem; border-radius: 10px; font-size: 0.78rem; font-weight: 800;">
                                                    {{ count($child['children']) }} Direct Staff Member(s)
                                                </span>
                                                <button type="button" wire:click="viewStaff({{ $child['id'] }})" style="background: #ffffff; border: 1px solid #cbd5e1; color: #475569; padding: 0.35rem 0.65rem; border-radius: 8px; font-size: 0.78rem; font-weight: 700; cursor: pointer;">
                                                    Details
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Direct Subordinate Staff Members List -->
                                        <div style="padding-left: 1.25rem; border-left: 3px solid #3b82f6;">
                                            <div style="font-size: 0.75rem; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.75rem;">
                                                Assigned Department Staff:
                                            </div>
                                            
                                            @if(!empty($child['children']))
                                                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 0.85rem;">
                                                    @foreach($child['children'] as $staff)
                                                        <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 10px; padding: 0.85rem; display: flex; justify-content: space-between; align-items: center;">
                                                            <div style="display: flex; align-items: center; gap: 0.65rem; overflow: hidden;">
                                                                <div style="width: 32px; height: 32px; border-radius: 50%; background: #f1f5f9; color: #475569; font-weight: 800; font-size: 0.9rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                                    {{ strtoupper(substr($staff['name'], 0, 1)) }}
                                                                </div>
                                                                <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                                    <div style="font-size: 0.85rem; font-weight: 800; color: #0f172a; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $staff['name'] }}</div>
                                                                    <span style="font-size: 0.73rem; color: #64748b; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $staff['designation'] ?? $staff['role'] }}</span>
                                                                </div>
                                                            </div>

                                                            <button type="button" wire:click="viewStaff({{ $staff['id'] }})" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #2563eb; padding: 0.25rem 0.45rem; border-radius: 6px; font-size: 0.75rem; font-weight: 700; cursor: pointer; flex-shrink: 0;" title="View Details">
                                                                <i class="bx bx-user-detail"></i>
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div style="color: #94a3b8; font-size: 0.82rem; font-style: italic; background: #ffffff; padding: 0.75rem; border-radius: 8px; border: 1px dashed #cbd5e1;">
                                                    No staff members currently assigned to this HOD.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @else
                    <div style="text-align: center; padding: 3rem; color: #94a3b8;">
                        <i class="bx bx-git-repo-forked" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 0.5rem; display: block;"></i>
                        No organizational hierarchy tree data found.
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- TAB 1: STAFF DIRECTORY & WORKLOAD MATRIX -->
    @if($activeTab === 'directory')
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02); overflow: hidden;">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; background: #f8fafc;">
                <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; width: 100%; max-width: 650px;">
                    <div style="flex: 2; min-width: 200px;">
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search staff by name, email, designation..." style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.85rem; background: white;">
                    </div>
                    <div style="flex: 1; min-width: 130px;">
                        <select wire:model.live="roleFilter" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.85rem; background: white;">
                            <option value="">All Roles</option>
                            <option value="super_admin">Super Admin</option>
                            <option value="coo">COO</option>
                            <option value="hod">HOD</option>
                            <option value="project_manager">Project Manager</option>
                            <option value="engineer">Engineer</option>
                            <option value="social_aid">Social Aid</option>
                            <option value="reception">Reception</option>
                            <option value="others">Others</option>
                        </select>
                    </div>
                    <div style="flex: 1; min-width: 130px;">
                        <select wire:model.live="statusFilter" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.85rem; background: white;">
                            <option value="">All Statuses</option>
                            <option value="active">Active Only</option>
                            <option value="suspended">Suspended Only</option>
                        </select>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="display: flex; background: #e2e8f0; border-radius: 8px; padding: 0.15rem;">
                        <button type="button" wire:click="setViewMode('grid')" style="background: {{ $viewMode === 'grid' ? '#ffffff' : 'transparent' }}; color: {{ $viewMode === 'grid' ? '#0f172a' : '#64748b' }}; border: none; border-radius: 6px; padding: 0.35rem 0.65rem; font-weight: 700; font-size: 0.8rem; cursor: pointer; display: flex; align-items: center; gap: 0.25rem; box-shadow: {{ $viewMode === 'grid' ? '0 1px 3px rgba(0,0,0,0.1)' : 'none' }};">
                            <i class="bx bx-grid-alt"></i> Cards
                        </button>
                        <button type="button" wire:click="setViewMode('table')" style="background: {{ $viewMode === 'table' ? '#ffffff' : 'transparent' }}; color: {{ $viewMode === 'table' ? '#0f172a' : '#64748b' }}; border: none; border-radius: 6px; padding: 0.35rem 0.65rem; font-weight: 700; font-size: 0.8rem; cursor: pointer; display: flex; align-items: center; gap: 0.25rem; box-shadow: {{ $viewMode === 'table' ? '0 1px 3px rgba(0,0,0,0.1)' : 'none' }};">
                            <i class="bx bx-list-ul"></i> Table
                        </button>
                    </div>
                    <div style="font-size: 0.82rem; font-weight: 700; color: #64748b;">
                        Showing {{ $staffList->total() }} staff members
                    </div>
                </div>
            </div>

            @if($viewMode === 'grid')
                <!-- STAFF CARDS GRID VIEW -->
                <div style="padding: 1.5rem;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.25rem;">
                        @forelse($staffList as $s)
                            @php
                                $att = $todayAttendances->get($s->id);
                            @endphp
                            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03); display: flex; flex-direction: column; justify-content: space-between; gap: 1rem; transition: transform 0.2s ease, box-shadow 0.2s ease;">
                                <!-- Card Header: Avatar & Status Badges -->
                                <div>
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.85rem;">
                                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                                            <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; font-weight: 800; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.15rem; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.25);">
                                                {{ strtoupper(substr($s->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <h3 style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin: 0; line-height: 1.2;">{{ $s->name }}</h3>
                                                <span style="font-size: 0.78rem; font-weight: 700; color: #10b981; display: block; margin-top: 0.2rem;">{{ $s->designation ?? 'Staff Member' }}</span>
                                            </div>
                                        </div>

                                        <span style="background: {{ $s->is_suspended ? '#fef2f2' : '#ecfdf5' }}; color: {{ $s->is_suspended ? '#ef4444' : '#059669' }}; border: 1px solid {{ $s->is_suspended ? '#fee2e2' : '#a7f3d0' }}; padding: 0.2rem 0.55rem; border-radius: 12px; font-size: 0.7rem; font-weight: 800;">
                                            {{ $s->is_suspended ? 'Suspended' : 'Active' }}
                                        </span>
                                    </div>

                                    <!-- Role & Contact Info -->
                                    <div style="display: flex; flex-direction: column; gap: 0.35rem; background: #f8fafc; padding: 0.75rem 0.85rem; border-radius: 10px; border: 1px solid #e2e8f0; margin-bottom: 0.85rem;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.15rem;">
                                            <span style="font-size: 0.7rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em;">System Role</span>
                                            <span style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; padding: 0.15rem 0.5rem; border-radius: 8px; font-size: 0.72rem; font-weight: 800;">
                                                {{ strtoupper($s->role) }}
                                            </span>
                                        </div>
                                        <div style="font-size: 0.78rem; color: #475569; display: flex; align-items: center; gap: 0.35rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <i class="bx bx-envelope" style="color: #64748b; flex-shrink: 0;"></i> {{ $s->email }}
                                        </div>
                                        @if($s->mobile)
                                            <div style="font-size: 0.78rem; color: #475569; display: flex; align-items: center; gap: 0.35rem;">
                                                <i class="bx bx-phone" style="color: #64748b; flex-shrink: 0;"></i> {{ $s->mobile }}
                                            </div>
                                        @endif
                                    </div>



                                    <!-- Leave Without Pay Total Count (Red Mark - Only shown when staff has taken LWP) -->
                                    @php
                                        $lwpDays = isset($lwpBalances) && $lwpBalances->has($s->id) ? (float)$lwpBalances->get($s->id)->used_days : 0;
                                    @endphp
                                    @if($lwpDays > 0)
                                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0.75rem; border-radius: 10px; background: #fef2f2; border: 1px solid #fee2e2; margin-top: 0.5rem;">
                                            <span style="font-size: 0.75rem; font-weight: 700; color: #991b1b; display: flex; align-items: center; gap: 0.35rem;">
                                                <i class="bx bx-calendar-minus" style="color: #ef4444; font-size: 0.95rem;"></i> Leave Without Pay:
                                            </span>
                                            <span style="background: #ef4444; color: #ffffff; padding: 0.2rem 0.6rem; border-radius: 12px; font-size: 0.75rem; font-weight: 800; box-shadow: 0 2px 6px rgba(239, 68, 68, 0.25);">
                                                {{ $lwpDays }} Day(s)
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Card Action Footer Buttons -->
                                <div style="display: flex; gap: 0.4rem; padding-top: 0.75rem; border-top: 1px solid #f1f5f9;">
                                    <button type="button" wire:click="viewStaff({{ $s->id }})" style="flex: 1; background: #eff6ff; border: 1px solid #bfdbfe; color: #2563eb; padding: 0.45rem 0.5rem; border-radius: 8px; font-weight: 700; font-size: 0.78rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.25rem;">
                                        <i class="bx bx-user-detail"></i> Details
                                    </button>
                                    <button type="button" wire:click="openEditStaffModal({{ $s->id }})" style="flex: 1; background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; padding: 0.45rem 0.5rem; border-radius: 8px; font-weight: 700; font-size: 0.78rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.25rem;">
                                        <i class="bx bx-edit"></i> Edit
                                    </button>
                                    @if(Auth::user()->id !== $s->id)
                                        <button type="button" wire:click="toggleUserSuspend({{ $s->id }})" style="flex: 1; background: {{ $s->is_suspended ? '#ecfdf5' : '#fef2f2' }}; border: 1px solid {{ $s->is_suspended ? '#a7f3d0' : '#fee2e2' }}; color: {{ $s->is_suspended ? '#059669' : '#ef4444' }}; padding: 0.45rem 0.5rem; border-radius: 8px; font-weight: 700; font-size: 0.78rem; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                            {{ $s->is_suspended ? 'Activate' : 'Suspend' }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 2rem; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);">
                                <div style="width: 64px; height: 64px; background: #ecfdf5; color: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem; font-size: 2rem; border: 2px solid #a7f3d0;">
                                    <i class="bx bx-group"></i>
                                </div>
                                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0 0 0.5rem;">No Staff Members Found</h3>
                                <p style="color: #64748b; font-size: 0.88rem; max-width: 420px; margin: 0 auto 1.5rem;">There are currently no staff members matching your search or role filters.</p>
                                @if(Auth::user()->isSuperAdmin())
                                    <button type="button" wire:click="openAddStaffModal" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; border: none; border-radius: 10px; padding: 0.65rem 1.35rem; font-weight: 700; font-size: 0.88rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.45rem; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);">
                                        <i class="bx bx-user-plus" style="font-size: 1.15rem;"></i> Add First Staff Member
                                    </button>
                                @endif
                            </div>
                        @endforelse
                    </div>
                </div>
            @else
                <!-- TABLE VIEW -->
                <div style="overflow-x: auto;">
                    <table class="table-custom" style="width: 100%; border-collapse: collapse; font-size: 0.88rem;">
                        <thead>
                            <tr style="background: #10b981; color: #ffffff;">
                                <th style="padding: 0.85rem 1.25rem; text-align: left;">Staff Member</th>
                                <th style="padding: 0.85rem 1.25rem; text-align: center;">Role & Designation</th>
                                <th style="padding: 0.85rem 1.25rem; text-align: center;">Leave Without Pay</th>
                                <th style="padding: 0.85rem 1.25rem; text-align: center;">Account Status</th>
                                <th style="padding: 0.85rem 1.25rem; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($staffList as $s)
                                @php
                                    $att = $todayAttendances->get($s->id);
                                @endphp
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 1rem 1.25rem;">
                                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                                            <div style="background: #10b981; color: #ffffff; font-weight: 800; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1rem;">
                                                {{ strtoupper(substr($s->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div style="font-weight: 700; color: #0f172a;">{{ $s->name }}</div>
                                                <div style="font-size: 0.78rem; color: #64748b;">{{ $s->email }} &bull; {{ $s->mobile }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td style="padding: 1rem 1.25rem; text-align: center;">
                                        <span style="background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; padding: 0.25rem 0.65rem; border-radius: 12px; font-size: 0.75rem; font-weight: 800; display: inline-block;">
                                            {{ strtoupper($s->role) }}
                                        </span>
                                        <div style="font-size: 0.78rem; color: #475569; margin-top: 0.25rem; font-weight: 600;">
                                            {{ $s->designation ?? 'Staff Member' }}
                                        </div>
                                    </td>



                                    <td style="padding: 1rem 1.25rem; text-align: center;">
                                        @php
                                            $lwpDaysTbl = isset($lwpBalances) && $lwpBalances->has($s->id) ? (float)$lwpBalances->get($s->id)->used_days : 0;
                                        @endphp
                                        @if($lwpDaysTbl > 0)
                                            <span style="background: #fef2f2; color: #ef4444; border: 1px solid #fee2e2; padding: 0.25rem 0.65rem; border-radius: 12px; font-size: 0.75rem; font-weight: 800; display: inline-flex; align-items: center; gap: 0.25rem;">
                                                <i class="bx bx-calendar-minus"></i> {{ $lwpDaysTbl }} Day(s)
                                            </span>
                                        @else
                                            <span style="font-size: 0.78rem; color: #94a3b8; font-weight: 500;">-</span>
                                        @endif
                                    </td>

                                    <td style="padding: 1rem 1.25rem; text-align: center;">
                                        <span style="background: {{ $s->is_suspended ? '#fef2f2' : '#ecfdf5' }}; color: {{ $s->is_suspended ? '#ef4444' : '#059669' }}; border: 1px solid {{ $s->is_suspended ? '#fee2e2' : '#a7f3d0' }}; padding: 0.25rem 0.65rem; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">
                                            {{ $s->is_suspended ? 'Suspended' : 'Active' }}
                                        </span>
                                    </td>

                                    <td style="padding: 1rem 1.25rem; text-align: center;">
                                        <div style="display: flex; gap: 0.35rem; justify-content: center;">
                                            <button type="button" wire:click="viewStaff({{ $s->id }})" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #2563eb; padding: 0.35rem 0.75rem; border-radius: 8px; font-weight: 700; font-size: 0.78rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem;">
                                                <i class="bx bx-user-detail"></i> Details
                                            </button>
                                            <button type="button" wire:click="openEditStaffModal({{ $s->id }})" style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; padding: 0.35rem 0.75rem; border-radius: 8px; font-weight: 700; font-size: 0.78rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem;">
                                                <i class="bx bx-edit"></i> Edit
                                            </button>
                                            @if(Auth::user()->id !== $s->id)
                                                <button type="button" wire:click="toggleUserSuspend({{ $s->id }})" style="background: {{ $s->is_suspended ? '#ecfdf5' : '#fef2f2' }}; border: 1px solid {{ $s->is_suspended ? '#a7f3d0' : '#fee2e2' }}; color: {{ $s->is_suspended ? '#059669' : '#ef4444' }}; padding: 0.35rem 0.75rem; border-radius: 8px; font-weight: 700; font-size: 0.78rem; cursor: pointer;">
                                                    {{ $s->is_suspended ? 'Activate' : 'Suspend' }}
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 3rem 1.5rem; color: #94a3b8;">No staff members matching criteria.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

            @if(method_exists($staffList, 'links'))
                <div style="padding: 1rem 1.5rem; border-top: 1px solid #e2e8f0;">
                    {{ $staffList->links() }}
                </div>
            @endif
        </div>
    @endif


    <!-- TAB 3: LEAVE QUEUE APPROVALS -->
    @if($activeTab === 'leave')
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);">
            <h2 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 1.25rem;">Pending Staff Leave Requests ({{ count($pendingLeaveRequests) }})</h2>

            <table class="table-custom" style="width: 100%; border-collapse: collapse; font-size: 0.88rem;">
                <thead>
                    <tr style="background: #10b981; color: #ffffff;">
                        <th style="padding: 0.85rem; text-align: left;">Staff Member</th>
                        <th style="padding: 0.85rem; text-align: left;">Leave Type</th>
                        <th style="padding: 0.85rem; text-align: left;">Dates & Duration</th>
                        <th style="padding: 0.85rem; text-align: left;">Reason</th>
                        <th style="padding: 0.85rem; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingLeaveRequests as $req)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 0.85rem;">
                                <div style="font-weight: 700; color: #0f172a;">{{ $req->user->name ?? 'User' }}</div>
                                <div style="font-size: 0.78rem; color: #64748b;">{{ $req->user->designation ?? $req->user->role }}</div>
                            </td>
                            <td style="padding: 0.85rem;"><x-leave-type-badge :type="$req->leaveType" /></td>
                            <td style="padding: 0.85rem; font-weight: 600; color: #0f172a;">
                                {{ $req->start_date->format('M d') }} &mdash; {{ $req->end_date->format('M d, Y') }}
                                <div style="font-size: 0.75rem; color: #2563eb; font-weight: 700;">({{ $req->total_days }} day(s))</div>
                            </td>
                            <td style="padding: 0.85rem; max-width: 250px; color: #475569;">{{ $req->reason }}</td>
                            <td style="padding: 0.85rem; text-align: center;">
                                <div style="display: flex; gap: 0.4rem; justify-content: center;">
                                    <button type="button" wire:click="approveLeave({{ $req->id }})" style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #059669; padding: 0.4rem 0.85rem; border-radius: 8px; font-size: 0.78rem; font-weight: 700; cursor: pointer;">
                                        <i class="bx bx-check"></i> Approve
                                    </button>
                                    <button type="button" wire:click="openRejectLeaveModal({{ $req->id }})" style="background: #fef2f2; border: 1px solid #fee2e2; color: #ef4444; padding: 0.4rem 0.85rem; border-radius: 8px; font-size: 0.78rem; font-weight: 700; cursor: pointer;">
                                        <i class="bx bx-x"></i> Reject
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2.5rem; color: #94a3b8;">No pending staff leave requests.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    <!-- TAB 4: ROLES & ANALYTICS -->
    @if($activeTab === 'analytics')
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);">
            <h2 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 1.25rem;">Staff Distribution by Role</h2>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem;">
                @foreach($roleCounts as $roleName => $count)
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <span style="font-size: 0.78rem; font-weight: 800; color: #64748b; text-transform: uppercase;">{{ strtoupper($roleName) }}</span>
                            <h2 style="font-size: 1.5rem; font-weight: 800; color: #0f172a; margin: 0.2rem 0 0 0;">{{ $count }}</h2>
                        </div>
                        <div style="background: #ecfdf5; color: #059669; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                            <i class="bx bx-user"></i>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- STAFF DETAIL DRAWER / MODAL -->
    @if($showStaffModal && $selectedUser)
        <div style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(15, 23, 42, 0.5); backdrop-filter: blur(6px); display: flex; align-items: center; justify-content: center; z-index: 1100; padding: 1.5rem; overflow-y: auto;">
            <div style="background: #ffffff; border-radius: 16px; width: 100%; max-width: 720px; padding: 1.75rem; box-shadow: 0 20px 40px rgba(0,0,0,0.15); max-height: 90vh; overflow-y: auto;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.85rem;">
                    <div style="display: flex; align-items: center; gap: 0.85rem;">
                        <div style="background: #10b981; color: #ffffff; font-weight: 800; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                            {{ strtoupper(substr($selectedUser->name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin: 0;">{{ $selectedUser->name }}</h3>
                            <span style="font-size: 0.8rem; color: #64748b;">{{ $selectedUser->designation ?? 'Staff Member' }} &bull; <strong style="color: #10b981;">{{ strtoupper($selectedUser->role) }}</strong></span>
                        </div>
                    </div>
                    <button type="button" wire:click="closeStaffModal" style="background: none; border: none; cursor: pointer; color: #64748b; font-size: 1.5rem;"><i class="bx bx-x"></i></button>
                </div>

                <div style="display: flex; flex-direction: column; gap: 1.25rem; font-size: 0.85rem;">
                    <!-- 1. Personal & Family Details -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem;">
                        <h4 style="font-size: 0.9rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 0.75rem;"><i class="bx bx-user-pin" style="color: #10b981;"></i> Personal & Family Details</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.85rem;">
                            <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">Email Address</strong> {{ $selectedUser->email }}</div>
                            <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">Mobile Number</strong> {{ $selectedUser->mobile }}</div>
                            <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">Father's Name</strong> {{ $selectedUser->father_name ?? '&mdash;' }}</div>
                            <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">Mother's Name</strong> {{ $selectedUser->mother_name ?? '&mdash;' }}</div>
                            <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">Date of Birth</strong> {{ $selectedUser->date_of_birth ? \Carbon\Carbon::parse($selectedUser->date_of_birth)->format('M d, Y') : '&mdash;' }}</div>
                            <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">Date of Joining</strong> {{ $selectedUser->date_of_joining ? \Carbon\Carbon::parse($selectedUser->date_of_joining)->format('M d, Y') : '&mdash;' }}</div>
                            <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">Gender / Marital Status</strong> {{ $selectedUser->gender ?? 'N/A' }} / {{ $selectedUser->marital_status ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <!-- 2. Permanent Address -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem;">
                        <h4 style="font-size: 0.9rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 0.75rem;"><i class="bx bx-home-alt" style="color: #3b82f6;"></i> Permanent Address</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.85rem;">
                            <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">House Name / No.</strong> {{ $selectedUser->house_name ?? '&mdash;' }}</div>
                            <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">Place / Post Office</strong> {{ $selectedUser->place ?? 'N/A' }} {{ $selectedUser->po ? '(P.O: ' . $selectedUser->po . ')' : '' }}</div>
                            <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">District / State</strong> {{ $selectedUser->district ?? 'N/A' }}, {{ $selectedUser->state ?? 'Kerala' }}</div>
                            <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">PIN Code</strong> {{ $selectedUser->pin_code ?? '&mdash;' }}</div>
                        </div>
                    </div>

                    <!-- 3. Identity Verification & Banking Info -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem;">
                        <h4 style="font-size: 0.9rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 0.75rem;"><i class="bx bx-credit-card-front" style="color: #8b5cf6;"></i> Identity & Banking Credentials</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.85rem;">
                            <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">Aadhar Number</strong> {{ $selectedUser->formatted_aadhar_number ?? 'N/A' }}</div>
                            <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">PAN Card Number</strong> {{ $selectedUser->pan_card_number ? strtoupper($selectedUser->pan_card_number) : '&mdash;' }}</div>
                            <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">Account Number</strong> {{ $selectedUser->account_number ?? '&mdash;' }}</div>
                            <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">Bank Name & Branch</strong> {{ $selectedUser->bank_name ?? 'N/A' }} {{ $selectedUser->bank_branch ? '(' . $selectedUser->bank_branch . ' Branch)' : '' }}</div>
                            <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">IFSC Code</strong> {{ $selectedUser->ifsc_code ? strtoupper($selectedUser->ifsc_code) : '&mdash;' }}</div>
                        </div>
                    </div>

                    <!-- Leave Balances -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem;">
                        <h4 style="font-size: 0.9rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 0.75rem;"><i class="bx bx-wallet" style="color: #3b82f6;"></i> Leave Balances</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 0.75rem;">
                            @forelse($selectedUserBalances as $b)
                                <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0.6rem 0.85rem;">
                                    <span style="font-size: 0.72rem; font-weight: 800; color: #64748b; display: block;">{{ $b->leaveType->leave_code ?? 'Type' }}</span>
                                    <strong style="font-size: 1.1rem; color: #10b981;">{{ $b->balance_days }} <span style="font-size: 0.7rem; color: #64748b;">days</span></strong>
                                </div>
                            @empty
                                <div style="color: #94a3b8;">No leave balance records.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.25rem;">
                    <button type="button" wire:click="openEditStaffModal({{ $selectedUser->id }})" style="background: #10b981; color: #ffffff; border: none; padding: 0.5rem 1.15rem; border-radius: 8px; font-weight: 700; font-size: 0.88rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);">
                        <i class="bx bx-edit"></i> Edit Staff Details
                    </button>
                    <button type="button" wire:click="closeStaffModal" style="background: #ffffff; border: 1px solid #cbd5e1; color: #475569; padding: 0.5rem 1.15rem; border-radius: 8px; font-weight: 600; font-size: 0.88rem; cursor: pointer;">Close</button>
                </div>
            </div>
        </div>
    @endif

    <!-- EDIT STAFF DETAILS MODAL -->
    @if($showEditStaffModal)
        <div style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(15, 23, 42, 0.5); backdrop-filter: blur(6px); display: flex; align-items: center; justify-content: center; z-index: 1100; padding: 1.5rem; overflow-y: auto;">
            <div style="background: #ffffff; border-radius: 16px; width: 100%; max-width: 780px; padding: 1.75rem; box-shadow: 0 20px 40px rgba(0,0,0,0.15); max-height: 90vh; overflow-y: auto;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.85rem;">
                    <h3 style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 0.4rem;">
                        <i class="bx bx-edit-alt" style="color: #10b981;"></i> Edit Staff Details
                    </h3>
                    <button type="button" wire:click="closeEditStaffModal" style="background: none; border: none; cursor: pointer; color: #64748b; font-size: 1.5rem;"><i class="bx bx-x"></i></button>
                </div>

                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <!-- Section 1: Basic Account & Role -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem;">
                        <h4 style="font-size: 0.9rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 0.85rem;"><i class="bx bx-user" style="color: #10b981;"></i> Account & Role Information</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Full Name *</label>
                                <input type="text" wire:model="editName" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                                @error('editName') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Email Address *</label>
                                <input type="email" wire:model="editEmail" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                                @error('editEmail') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Mobile Number *</label>
                                <input type="text" wire:model="editMobile" placeholder="10-digit mobile number" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                                @error('editMobile') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Designation *</label>
                                <input type="text" wire:model="editDesignation" placeholder="e.g. Senior Project Manager" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                                @error('editDesignation') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">System Role *</label>
                                <select wire:model.live="editRole" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem; background: white;">
                                    <option value="super_admin">Super Admin</option>
                                    <option value="coo">COO</option>
                                    <option value="hod">HOD</option>
                                    <option value="project_manager">Project Manager</option>
                                    <option value="engineer">Engineer</option>
                                    <option value="social_aid">Social Aid</option>
                                    <option value="reception">Reception</option>
                                    <option value="others">Others</option>
                                </select>
                                @error('editRole') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                            </div>
                            <div>
                             @if($editRole === 'hod')
                                 <div style="display: flex; align-items: center; background: rgba(168, 85, 247, 0.05); border: 1px solid rgba(168, 85, 247, 0.2); padding: 0.75rem; border-radius: 8px; margin-top: 0.5rem;">
                                     <label style="display: flex; align-items: center; gap: 0.5rem; color: #6b21a8; font-weight: 700; font-size: 0.85rem; cursor: pointer; margin: 0;">
                                         <input type="checkbox" wire:model="editIsHr" value="1" style="width: 18px; height: 18px; cursor: pointer;">
                                         <i class="bx bx-shield-quarter" style="font-size: 1.1rem;"></i> Designate as Central HR HOD
                                     </label>
                                 </div>
                             @elseif(!in_array($editRole, ['super_admin', 'coo']))
                                 <div>
                                     <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Assigned HOD</label>
                                     <select wire:model="editHodId" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem; background: white;">
                                         <option value="">-- Select Assigned HOD --</option>
                                         @foreach($hods as $h)
                                             <option value="{{ $h->id }}">{{ $h->name }} (HOD)</option>
                                         @endforeach
                                     </select>
                                     @error('editHodId') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                 </div>
                             @endif</div>
                        </div>
                    </div>

                    <!-- Section 2: Personal Info -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem;">
                        <h4 style="font-size: 0.9rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 0.85rem;"><i class="bx bx-id-card" style="color: #3b82f6;"></i> Personal Information</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Father's Name</label>
                                <input type="text" wire:model="editFatherName" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Mother's Name</label>
                                <input type="text" wire:model="editMotherName" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Date of Birth</label>
                                <input type="date" wire:model="editDateOfBirth" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem; background: white;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Date of Joining</label>
                                <input type="date" wire:model="editDateOfJoining" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem; background: white;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Gender</label>
                                <select wire:model="editGender" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem; background: white;">
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Marital Status</label>
                                <select wire:model="editMaritalStatus" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem; background: white;">
                                    <option value="Single">Single</option>
                                    <option value="Married">Married</option>
                                    <option value="Divorced">Divorced</option>
                                    <option value="Widowed">Widowed</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Address & Bank Info -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem;">
                        <h4 style="font-size: 0.9rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 0.85rem;"><i class="bx bx-home-alt" style="color: #8b5cf6;"></i> Address & Bank Details</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">House Name</label>
                                <input type="text" wire:model="editHouseName" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Place</label>
                                <input type="text" wire:model="editPlace" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">District</label>
                                <input type="text" wire:model="editDistrict" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">State</label>
                                <input type="text" wire:model="editState" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">PIN Code</label>
                                <input type="text" wire:model="editPinCode" placeholder="6-digit PIN code" maxlength="6" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6)" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                                @error('editPinCode') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Aadhar Number</label>
                                <input type="text" wire:model.blur="editAadharNumber" placeholder="1234 5678 9012" maxlength="14" oninput="this.value = this.value.replace(/\D/g, '').replace(/(\d{4})(?=\d)/g, '$1 ').trim()" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">PAN Card Number</label>
                                <input type="text" wire:model="editPanCardNumber" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem; text-transform: uppercase;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Bank Account No</label>
                                <input type="text" wire:model="editAccountNumber" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Bank Name</label>
                                <input type="text" wire:model="editBankName" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">IFSC Code</label>
                                <input type="text" wire:model="editIfscCode" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem; text-transform: uppercase;">
                            </div>
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
                    <button type="button" wire:click="closeEditStaffModal" style="background: #ffffff; border: 1px solid #cbd5e1; color: #475569; padding: 0.6rem 1.15rem; border-radius: 8px; font-weight: 600; font-size: 0.88rem; cursor: pointer;">Cancel</button>
                    <button type="button" wire:click="saveStaffDetails" style="background: #10b981; color: #ffffff; border: none; border-radius: 8px; padding: 0.6rem 1.25rem; font-weight: 700; font-size: 0.88rem; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);">Save Staff Details</button>
                </div>
            </div>
        </div>
    @endif

    <!-- REJECT LEAVE MODAL -->
    @if($showRejectLeaveModal)
        <div style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(15, 23, 42, 0.5); backdrop-filter: blur(6px); display: flex; align-items: center; justify-content: center; z-index: 1100; padding: 1.5rem;">
            <div style="background: #ffffff; border-radius: 16px; width: 100%; max-width: 480px; padding: 1.75rem; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 0.5rem;">Reject Staff Leave Request</h3>
                <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 1rem;">State reason for rejecting this leave request:</p>

                <textarea wire:model="rejectRemarks" rows="3" placeholder="Provide reason for rejection..." style="width: 100%; padding: 0.65rem 1rem; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 0.9rem; outline: none; margin-bottom: 1rem; font-family: inherit;"></textarea>
                @error('rejectRemarks') <span style="color: #ef4444; font-size: 0.75rem; display: block; margin-bottom: 0.75rem;">{{ $message }}</span> @enderror

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" wire:click="closeRejectLeaveModal" style="background: #ffffff; border: 1px solid #cbd5e1; color: #475569; padding: 0.6rem 1.15rem; border-radius: 8px; font-weight: 600; font-size: 0.88rem; cursor: pointer;">Cancel</button>
                    <button type="button" wire:click="confirmRejectLeave" style="background: #ef4444; color: #ffffff; border: none; border-radius: 8px; padding: 0.6rem 1.25rem; font-weight: 700; font-size: 0.88rem; cursor: pointer;">Confirm Rejection</button>
                </div>
            </div>
        </div>
    @endif

    <!-- ADD NEW STAFF MODAL -->
    @if($showAddStaffModal)
        <div style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(6px); display: flex; align-items: center; justify-content: center; z-index: 1200; padding: 1.5rem;">
            <div style="background: #ffffff; border-radius: 20px; width: 100%; max-width: 820px; max-height: 90vh; display: flex; flex-direction: column; box-shadow: 0 25px 50px rgba(0,0,0,0.25); overflow: hidden;">
                <!-- Header -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem 1rem 2rem; border-bottom: 1px solid #e2e8f0; background: #ffffff;">
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0;"><i class="bx bx-user-plus" style="color:#10b981; margin-right:0.4rem;"></i> Register New Staff Member</h3>
                        <span style="font-size: 0.8rem; color: #64748b;">All fields marked with <span style="color:#ef4444;">*</span> are mandatory</span>
                    </div>
                    <button type="button" wire:click="closeAddStaffModal" style="background: #f1f5f9; border: none; font-size: 1.4rem; color: #64748b; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer;">&times;</button>
                </div>

                <form wire:submit.prevent="createStaff" style="display: flex; flex-direction: column; flex: 1; overflow: hidden; margin: 0;">
                    <div style="padding: 1.5rem 2rem; overflow-y: auto; display: flex; flex-direction: column; gap: 1.5rem; flex: 1;">
                        <!-- Section 1: Personal Information -->
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem;">
                            <h4 style="font-size: 0.88rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 0.85rem; text-transform: uppercase; letter-spacing: 0.04em;"><i class="bx bx-user" style="color: #10b981; margin-right: 0.35rem;"></i> Personal Information</h4>
                            
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                                <div>
                                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Full Name <span style="color:#ef4444;">*</span></label>
                                    <input type="text" wire:model="addName" placeholder="Enter full name" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                                    @error('addName') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Email Address <span style="color:#ef4444;">*</span></label>
                                    <input type="email" wire:model="addEmail" placeholder="Enter email address" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                                    @error('addEmail') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Mobile Number <span style="color:#ef4444;">*</span></label>
                                    <input type="text" wire:model="addMobile" placeholder="10-digit mobile number" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                                    @error('addMobile') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Father's Name <span style="color:#ef4444;">*</span></label>
                                    <input type="text" wire:model="addFatherName" placeholder="Enter father's name" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                                    @error('addFatherName') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Mother's Name <span style="color:#ef4444;">*</span></label>
                                    <input type="text" wire:model="addMotherName" placeholder="Enter mother's name" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                                    @error('addMotherName') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Date of Birth <span style="color:#ef4444;">*</span></label>
                                    <input type="date" wire:model="addDateOfBirth" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                                    @error('addDateOfBirth') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Date of Joining <span style="color:#ef4444;">*</span></label>
                                    <input type="date" wire:model="addDateOfJoining" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                                    @error('addDateOfJoining') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Gender <span style="color:#ef4444;">*</span></label>
                                    <select wire:model="addGender" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem; background: white;">
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    @error('addGender') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Marital Status <span style="color:#ef4444;">*</span></label>
                                    <select wire:model="addMaritalStatus" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem; background: white;">
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Divorced">Divorced</option>
                                        <option value="Widowed">Widowed</option>
                                    </select>
                                    @error('addMaritalStatus') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Address Information -->
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem;">
                            <h4 style="font-size: 0.88rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 0.85rem; text-transform: uppercase; letter-spacing: 0.04em;"><i class="bx bx-home-alt" style="color: #3b82f6; margin-right: 0.35rem;"></i> Permanent Address</h4>
                            
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                                <div>
                                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">House Name/Number <span style="color:#ef4444;">*</span></label>
                                    <input type="text" wire:model="addHouseName" placeholder="House name or number" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                                    @error('addHouseName') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Place <span style="color:#ef4444;">*</span></label>
                                    <input type="text" wire:model="addPlace" placeholder="Enter place" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                                    @error('addPlace') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Post Office (P.O.) <span style="color:#ef4444;">*</span></label>
                                    <input type="text" wire:model="addPo" placeholder="Post office name" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                                    @error('addPo') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">District <span style="color:#ef4444;">*</span></label>
                                    <input type="text" wire:model="addDistrict" placeholder="Enter district" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                                    @error('addDistrict') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">State <span style="color:#ef4444;">*</span></label>
                                    <input type="text" wire:model="addState" placeholder="Enter state" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                                    @error('addState') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">PIN Code <span style="color:#ef4444;">*</span></label>
                                    <input type="text" wire:model="addPinCode" placeholder="6-digit PIN code" maxlength="6" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6)" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                                    @error('addPinCode') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: ID & Banking Details -->
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem;">
                            <h4 style="font-size: 0.88rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 0.85rem; text-transform: uppercase; letter-spacing: 0.04em;"><i class="bx bx-id-card" style="color: #8b5cf6; margin-right: 0.35rem;"></i> Identity Verification & Banking</h4>
                            
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                                <div>
                                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Aadhar Number <span style="color:#ef4444;">*</span></label>
                                    <input type="text" wire:model.blur="addAadharNumber" placeholder="1234 5678 9012" maxlength="14" oninput="this.value = this.value.replace(/\D/g, '').replace(/(\d{4})(?=\d)/g, '$1 ').trim()" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                                    @error('addAadharNumber') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">PAN Card Number <span style="color:#ef4444;">*</span></label>
                                    <input type="text" wire:model="addPanCardNumber" placeholder="10-character PAN" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem; text-transform: uppercase;">
                                    @error('addPanCardNumber') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Bank Account Number <span style="color:#ef4444;">*</span></label>
                                    <input type="text" wire:model="addAccountNumber" placeholder="Account number" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                                    @error('addAccountNumber') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Bank Name <span style="color:#ef4444;">*</span></label>
                                    <input type="text" wire:model="addBankName" placeholder="Bank name" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                                    @error('addBankName') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Bank Branch <span style="color:#ef4444;">*</span></label>
                                    <input type="text" wire:model="addBankBranch" placeholder="Branch name" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                                    @error('addBankBranch') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">IFSC Code <span style="color:#ef4444;">*</span></label>
                                    <input type="text" wire:model="addIfscCode" placeholder="IFSC Code" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem; text-transform: uppercase;">
                                    @error('addIfscCode') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Section 4: Account Credentials & Role -->
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem;">
                            <h4 style="font-size: 0.88rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 0.85rem; text-transform: uppercase; letter-spacing: 0.04em;"><i class="bx bx-lock-alt" style="color: #f59e0b; margin-right: 0.35rem;"></i> Account & Role Settings</h4>
                            
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                                <div>
                                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Official Designation <span style="color:#ef4444;">*</span></label>
                                    <input type="text" wire:model="addDesignation" placeholder="e.g. Senior Project Manager" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                                    @error('addDesignation') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">User Role <span style="color:#ef4444;">*</span></label>
                                    <select wire:model.live="addRole" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem; background: white;">
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
                                <div>
                                    @if($addRole === 'hod')
                                        <div style="display: flex; align-items: center; background: rgba(168, 85, 247, 0.05); border: 1px solid rgba(168, 85, 247, 0.2); padding: 0.75rem; border-radius: 8px; margin-top: 0.5rem;">
                                            <label style="display: flex; align-items: center; gap: 0.5rem; color: #6b21a8; font-weight: 700; font-size: 0.85rem; cursor: pointer; margin: 0;">
                                                <input type="checkbox" wire:model="addIsHr" value="1" style="width: 18px; height: 18px; cursor: pointer;">
                                                <i class="bx bx-shield-quarter" style="font-size: 1.1rem;"></i> Designate as Central HR HOD
                                            </label>
                                        </div>
                                    @elseif(!in_array($addRole, ['super_admin', 'coo']))
                                        <div>
                                            <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Assigned HOD <span style="color:#ef4444;">*</span></label>
                                            <select wire:model="addHodId" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem; background: white;">
                                                <option value="">-- Select Assigned HOD --</option>
                                                @foreach($hods as $h)
                                                    <option value="{{ $h->id }}">{{ $h->name }} (HOD)</option>
                                                @endforeach
                                            </select>
                                            @error('addHodId') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Password <span style="color:#ef4444;">*</span></label>
                                    <input type="password" wire:model="addPassword" placeholder="Minimum 8 characters" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                                    @error('addPassword') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem; padding: 1rem 2rem; border-top: 1px solid #e2e8f0; background: #ffffff;">
                        <button type="button" wire:click="closeAddStaffModal" style="background: #ffffff; border: 1px solid #cbd5e1; color: #475569; padding: 0.65rem 1.25rem; border-radius: 10px; font-weight: 600; font-size: 0.88rem; cursor: pointer;">Cancel</button>
                        <button type="submit" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; border: none; border-radius: 10px; padding: 0.65rem 1.5rem; font-weight: 700; font-size: 0.88rem; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);">Save Staff</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
