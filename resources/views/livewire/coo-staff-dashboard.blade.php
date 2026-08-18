<div style="display: flex; flex-direction: column; gap: 1.75rem;">

    <style>
        [x-cloak] { display: none !important; }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* FIX #1: added missing keyframes — this was referenced by .modal-dialog-box
           but never defined, so the modal's slide-up entrance animation silently
           never ran. */
        @keyframes modalSlideUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulseLoading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .global-loading-bar {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            height: 3px !important;
            background: linear-gradient(90deg, #10b981 0%, #3b82f6 50%, #10b981 100%) !important;
            background-size: 200% 100% !important;
            animation: pulseLoading 1.2s linear infinite !important;
            z-index: 999999 !important;
        }

        .modal-overlay-container {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            display: none;
            align-items: center !important;
            justify-content: center !important;
            z-index: 99999 !important;
            background-color: rgba(15, 23, 42, 0.6) !important;
            backdrop-filter: blur(6px) !important;
            -webkit-backdrop-filter: blur(6px) !important;
            padding: 1.5rem !important;
            overflow-y: auto !important;
            box-sizing: border-box !important;
            animation: modalFadeIn 0.16s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .modal-overlay-container.is-active {
            display: flex !important;
        }

        .modal-dialog-box {
            margin: auto !important;
            width: 100% !important;
            box-sizing: border-box !important;
            background: #ffffff !important;
            border-radius: 20px !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
            display: flex !important;
            flex-direction: column !important;
            position: relative !important;
            max-height: 90vh !important;
            animation: modalSlideUp 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .btn-action-animated {
            transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-action-animated:hover {
            transform: translateY(-1px);
        }
        .btn-action-animated:active {
            transform: scale(0.97);
        }
        .btn-action-animated:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none !important;
        }

        .modal-dialog-box input:focus, 
        .modal-dialog-box select:focus, 
        .modal-dialog-box textarea:focus {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15) !important;
            outline: none;
        }
    </style>

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

    <!-- Sleek Top Loading Bar for Background Requests -->
    <div wire:loading.delay.long wire:target="setTab,search,roleFilter,statusFilter,setViewMode" class="global-loading-bar"></div>

    <!-- Header & Navigation Tabs -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);">
        <div style="max-width: 600px;">
            <h1 style="color: #0f172a; font-size: 1.75rem; font-weight: 800; margin: 0;">Staffs</h1>
            <p style="color: #64748b; font-size: 0.88rem; margin-top: 0.35rem; margin-bottom: 0;">Manage organizational staff members, department team structure, and leave approvals.</p>
        </div>

        <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
            @if(Auth::user()->isSuperAdmin())
                <button type="button" onclick="openAddStaffModal()" class="btn-action-animated" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; border: none; border-radius: 10px; padding: 0.65rem 1.25rem; font-weight: 700; font-size: 0.88rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.45rem; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.25);">
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
                    <i class="bx bx-calendar-event" style="vertical-align: middle; font-size: 1rem;"></i> Leave Queue ({{ $pendingLeaveCount ?? count($pendingLeaveRequests) }})
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
                <h2 style="color: #d97706; font-size: 1.6rem; font-weight: 800; margin: 0.1rem 0;">{{ $onLeaveTodayCount ?? count($staffOnLeaveToday) }}</h2>
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
                <h2 style="color: #ef4444; font-size: 1.6rem; font-weight: 800; margin: 0.1rem 0;">{{ $pendingLeaveCount ?? count($pendingLeaveRequests) }}</h2>
                <span style="color: #dc2626; font-size: 0.75rem; font-weight: 600;">Awaiting Approval</span>
            </div>
        </div>
    </div>

    <!-- TAB: TEAM HIERARCHY TREE -->
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
                                    <div wire:key="tree-hod-{{ $child['id'] }}" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
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
                                                <button type="button" onclick="openStaffDetailsModal({{ $child['id'] }})" style="background: #ffffff; border: 1px solid #cbd5e1; color: #475569; padding: 0.35rem 0.65rem; border-radius: 8px; font-size: 0.78rem; font-weight: 700; cursor: pointer;">
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
                                                        <div wire:key="tree-sub-staff-{{ $staff['id'] }}" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 10px; padding: 0.85rem; display: flex; justify-content: space-between; align-items: center;">
                                                            <div style="display: flex; align-items: center; gap: 0.65rem; overflow: hidden;">
                                                                <div style="width: 32px; height: 32px; border-radius: 50%; background: #f1f5f9; color: #475569; font-weight: 800; font-size: 0.9rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                                    {{ strtoupper(substr($staff['name'], 0, 1)) }}
                                                                </div>
                                                                <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                                    <div style="font-size: 0.85rem; font-weight: 800; color: #0f172a; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $staff['name'] }}</div>
                                                                    <span style="font-size: 0.73rem; color: #64748b; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $staff['designation'] ?? $staff['role'] }}</span>
                                                                </div>
                                                            </div>

                                                            <!-- FIX #3: was wire:click="viewStaff(...)" which used a different
                                                                 (server round-trip) modal mechanism than every other "Details"
                                                                 button on this page. Switched to the same AJAX modal used
                                                                 everywhere else for consistent behavior. -->
                                                            <button type="button" onclick="openStaffDetailsModal({{ $staff['id'] }})" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #2563eb; padding: 0.25rem 0.45rem; border-radius: 6px; font-size: 0.75rem; font-weight: 700; cursor: pointer; flex-shrink: 0;" title="View Details">
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

    <!-- TAB: STAFF DIRECTORY & WORKLOAD MATRIX -->
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
                            <option value="social_aid">Social Aid Manager</option>
                            <option value="reception">Reception</option>
                            <option value="employee">Employee</option>
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
                            <div wire:key="staff-card-{{ $s->id }}" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03); display: flex; flex-direction: column; justify-content: space-between; gap: 1rem; transition: transform 0.2s ease, box-shadow 0.2s ease;">
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

                                    <!-- Leave Without Pay Total Count -->
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
                                    <button type="button" onclick="openStaffDetailsModal({{ $s->id }})" class="btn-action-animated" style="flex: 1; background: #eff6ff; border: 1px solid #bfdbfe; color: #2563eb; padding: 0.45rem 0.5rem; border-radius: 8px; font-weight: 700; font-size: 0.78rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.25rem;">
                                        <i class="bx bx-user-detail"></i> Details
                                    </button>
                                    <button type="button" onclick="openEditStaffModal({{ $s->id }})" class="btn-action-animated" style="flex: 1; background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; padding: 0.45rem 0.5rem; border-radius: 8px; font-weight: 700; font-size: 0.78rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.25rem;">
                                        <i class="bx bx-edit"></i> Edit
                                    </button>
                                    @if(Auth::user()->id !== $s->id)
                                        <button type="button" 
                                                id="card-suspend-btn-{{ $s->id }}"
                                                onclick="toggleUserSuspendJs({{ $s->id }}, this)" 
                                                class="btn-action-animated" 
                                                style="flex: 1; background: {{ $s->is_suspended ? '#ecfdf5' : '#fef2f2' }}; border: 1px solid {{ $s->is_suspended ? '#a7f3d0' : '#fee2e2' }}; color: {{ $s->is_suspended ? '#059669' : '#ef4444' }}; padding: 0.45rem 0.5rem; border-radius: 8px; font-weight: 700; font-size: 0.78rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.25rem;">
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
                                    <button type="button" onclick="openAddStaffModal()" class="btn-action-animated" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; border: none; border-radius: 10px; padding: 0.65rem 1.35rem; font-weight: 700; font-size: 0.88rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.45rem; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);">
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
                                <tr wire:key="staff-row-{{ $s->id }}" style="border-bottom: 1px solid #f1f5f9;">
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
                                            <button type="button" onclick="openStaffDetailsModal({{ $s->id }})" class="btn-action-animated" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #2563eb; padding: 0.35rem 0.75rem; border-radius: 8px; font-weight: 700; font-size: 0.78rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem;">
                                                <i class="bx bx-user-detail"></i> Details
                                            </button>
                                            <button type="button" onclick="openEditStaffModal({{ $s->id }})" class="btn-action-animated" style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; padding: 0.35rem 0.75rem; border-radius: 8px; font-weight: 700; font-size: 0.78rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem;">
                                                <i class="bx bx-edit"></i> Edit
                                            </button>
                                            @if(Auth::user()->id !== $s->id)
                                                <button type="button" 
                                                        id="table-suspend-btn-{{ $s->id }}"
                                                        onclick="toggleUserSuspendJs({{ $s->id }}, this)" 
                                                        class="btn-action-animated" 
                                                        style="background: {{ $s->is_suspended ? '#ecfdf5' : '#fef2f2' }}; border: 1px solid {{ $s->is_suspended ? '#a7f3d0' : '#fee2e2' }}; color: {{ $s->is_suspended ? '#059669' : '#ef4444' }}; padding: 0.35rem 0.75rem; border-radius: 8px; font-weight: 700; font-size: 0.78rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem;">
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

    <!-- TAB: LEAVE QUEUE APPROVALS -->
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
                        <tr wire:key="leave-req-{{ $req->id }}" style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 0.85rem;">
                                <div style="font-weight: 700; color: #0f172a;">{{ $req->user->name ?? 'User' }}</div>
                                <div style="font-size: 0.78rem; color: #64748b;">{{ $req->user->designation ?? $req->user->role }}</div>
                            </td>
                            <td style="padding: 0.85rem;"><x-leave-type-badge :type="$req->leaveType" /></td>
                            <td style="padding: 0.85rem; font-weight: 600; color: #0f172a;">
                                {{ $req->start_date->format('d/m/Y') }} &mdash; {{ $req->end_date->format('d/m/Y') }}
                                <div style="font-size: 0.75rem; color: #2563eb; font-weight: 700;">({{ $req->total_days }} day(s))</div>
                            </td>
                            <td style="padding: 0.85rem; max-width: 250px; color: #475569;">{{ $req->reason }}</td>
                            <td style="padding: 0.85rem; text-align: center;">
                                <div style="display: flex; gap: 0.4rem; justify-content: center;">
                                    <button type="button" wire:click="approveLeave({{ $req->id }})" style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #059669; padding: 0.4rem 0.85rem; border-radius: 8px; font-size: 0.78rem; font-weight: 700; cursor: pointer;">
                                        <i class="bx bx-check"></i> Approve
                                    </button>
                                    <button type="button" onclick="openRejectLeaveModal({{ $req->id }})" style="background: #fef2f2; border: 1px solid #fee2e2; color: #ef4444; padding: 0.4rem 0.85rem; border-radius: 8px; font-size: 0.78rem; font-weight: 700; cursor: pointer;">
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

    <!-- TAB: ROLES & ANALYTICS -->
    @if($activeTab === 'analytics')
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);">
            <h2 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 1.25rem;">Staff Distribution by Role</h2>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem;">
                @foreach($roleCounts as $roleName => $count)
                    <div wire:key="analytics-role-{{ $roleName }}" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; display: flex; justify-content: space-between; align-items: center;">
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

    <!-- ==========================================
         STAFF DETAIL MODAL (PURE JS / AJAX)
         ========================================== -->
    <div id="staffDetailsModal" class="modal-overlay-container" style="display: none;" onclick="if(event.target === this) closeStaffDetailsModal()">
        <div class="modal-dialog-box" style="max-width: 720px; padding: 1.75rem; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.85rem;">
                <div style="display: flex; align-items: center; gap: 0.85rem;">
                    <div id="sd_avatar" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; font-weight: 800; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.25);">
                        S
                    </div>
                    <div>
                        <h3 id="sd_name" style="font-size: 1.2rem; font-weight: 800; color: #0f172a; margin: 0;">Loading...</h3>
                        <span style="font-size: 0.8rem; color: #64748b;"><span id="sd_designation">Staff Member</span> &bull; <strong id="sd_role" style="color: #10b981;">USER</strong></span>
                    </div>
                </div>
                <button type="button" onclick="closeStaffDetailsModal()" style="background: #f1f5f9; border: none; font-size: 1.4rem; color: #64748b; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer;">&times;</button>
            </div>

            <!-- Modal Loading State -->
            <div id="sd_loading" style="display: flex; justify-content: center; align-items: center; padding: 3rem; color: #10b981;">
                <i class="bx bx-loader-alt bx-spin" style="font-size: 2.5rem;"></i>
            </div>

            <!-- Modal Content Body -->
            <div id="sd_content" style="display: none; flex-direction: column; gap: 1.25rem; font-size: 0.85rem;">
                <!-- 1. Personal & Family Details -->
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem;">
                    <h4 style="font-size: 0.9rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 0.75rem;"><i class="bx bx-user-pin" style="color: #10b981;"></i> Personal & Family Details</h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.85rem;">
                        <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">Email Address</strong> <span id="sd_email">—</span></div>
                        <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">Mobile Number</strong> <span id="sd_mobile">—</span></div>
                        <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">Father's Name</strong> <span id="sd_father_name">—</span></div>
                        <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">Mother's Name</strong> <span id="sd_mother_name">—</span></div>
                        <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">Date of Birth</strong> <span id="sd_dob">—</span></div>
                        <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">Date of Joining</strong> <span id="sd_doj">—</span></div>
                        <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">Gender / Marital Status</strong> <span id="sd_gender_marital">—</span></div>
                        <div id="sd_hod_wrapper" style="display: none;"><strong style="color: #64748b; display: block; font-size: 0.75rem;">Reporting HOD</strong> <span id="sd_assigned_hod" style="color: #2563eb; font-weight: 700;">—</span></div>
                    </div>
                </div>

                <!-- 2. Permanent Address -->
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem;">
                    <h4 style="font-size: 0.9rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 0.75rem;"><i class="bx bx-home-alt" style="color: #3b82f6;"></i> Permanent Address</h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.85rem;">
                        <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">House Name / No.</strong> <span id="sd_house_name">—</span></div>
                        <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">Place / Post Office</strong> <span id="sd_place_po">—</span></div>
                        <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">District / State</strong> <span id="sd_district_state">—</span></div>
                        <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">PIN Code</strong> <span id="sd_pincode">—</span></div>
                    </div>
                </div>

                <!-- 3. Identity Verification & Banking Info -->
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem;">
                    <h4 style="font-size: 0.9rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 0.75rem;"><i class="bx bx-credit-card-front" style="color: #8b5cf6;"></i> Identity & Banking Credentials</h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.85rem;">
                        <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">Aadhaar Number</strong> <span id="sd_aadhar">—</span></div>
                        <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">PAN Card Number</strong> <span id="sd_pan">—</span></div>
                        <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">Account Number</strong> <span id="sd_account_no">—</span></div>
                        <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">Bank Name & Branch</strong> <span id="sd_bank_branch">—</span></div>
                        <div><strong style="color: #64748b; display: block; font-size: 0.75rem;">IFSC Code</strong> <span id="sd_ifsc">—</span></div>
                    </div>
                </div>

                <!-- 4. Leave Balances -->
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem;">
                    <h4 style="font-size: 0.9rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 0.75rem;"><i class="bx bx-wallet" style="color: #3b82f6;"></i> Leave Balances</h4>
                    <div id="sd_leave_balances_container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 0.75rem;">
                        <div style="color: #94a3b8;">No leave balance records.</div>
                    </div>
                </div>

                <!-- 5. Assigned Projects (if PM/Engineer) -->
                <div id="sd_projects_section" style="display: none; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem;">
                    <h4 style="font-size: 0.9rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 0.75rem;"><i class="bx bx-briefcase-alt-2" style="color: #10b981;"></i> Assigned Projects</h4>
                    <div id="sd_projects_container" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.25rem; padding-top: 0.85rem; border-top: 1px solid #e2e8f0;">
                <button type="button" id="sd_edit_btn" onclick="" class="btn-action-animated" style="background: #10b981; color: #ffffff; border: none; padding: 0.55rem 1.25rem; border-radius: 10px; font-weight: 700; font-size: 0.88rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);">
                    <i class="bx bx-edit"></i> Edit Staff Details
                </button>
                <button type="button" onclick="closeStaffDetailsModal()" class="btn-action-animated" style="background: #ffffff; border: 1px solid #cbd5e1; color: #475569; padding: 0.55rem 1.25rem; border-radius: 10px; font-weight: 600; font-size: 0.88rem; cursor: pointer;">Close</button>
            </div>
        </div>
    </div>

    <!-- ==========================================
         ADD STAFF MODAL (PURE JS / AJAX)
         ========================================== -->
    <div id="addStaffModal" class="modal-overlay-container" style="display: none;" onclick="if(event.target === this) closeAddStaffModal()">
        <div class="modal-dialog-box" style="max-width: 820px; overflow: hidden;">
            <!-- Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem 1rem 2rem; border-bottom: 1px solid #e2e8f0; background: #ffffff;">
                <div>
                    <h3 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0;"><i class="bx bx-user-plus" style="color:#10b981; margin-right:0.4rem;"></i> Register New Staff Member</h3>
                    <span style="font-size: 0.8rem; color: #64748b;">All fields marked with <span style="color:#ef4444;">*</span> are mandatory</span>
                </div>
                <button type="button" onclick="closeAddStaffModal()" style="background: #f1f5f9; border: none; font-size: 1.4rem; color: #64748b; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer;">&times;</button>
            </div>

            <form id="addStaffForm" onsubmit="submitAddStaffForm(event)" style="display: flex; flex-direction: column; flex: 1; overflow: hidden; margin: 0;">
                @csrf
                <div style="padding: 1.5rem 2rem; overflow-y: auto; display: flex; flex-direction: column; gap: 1.5rem; flex: 1; max-height: 65vh;">
                    <!-- Section 1: Personal Information -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem;">
                        <h4 style="font-size: 0.88rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 0.85rem; text-transform: uppercase; letter-spacing: 0.04em;"><i class="bx bx-user" style="color: #10b981; margin-right: 0.35rem;"></i> Personal Information</h4>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Full Name <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="name" id="add_name" required placeholder="Enter full name" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Email Address <span style="color:#ef4444;">*</span></label>
                                <input type="email" name="email" id="add_email" required placeholder="Enter email address" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Mobile Number <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="mobile" id="add_mobile" required placeholder="10-digit mobile number" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Father's Name <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="father_name" id="add_father_name" required placeholder="Enter father's name" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Mother's Name <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="mother_name" id="add_mother_name" required placeholder="Enter mother's name" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Date of Birth <span style="color:#ef4444;">*</span></label>
                                <input type="date" name="date_of_birth" id="add_date_of_birth" required style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Date of Joining <span style="color:#ef4444;">*</span></label>
                                <input type="date" name="date_of_joining" id="add_date_of_joining" required style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Gender <span style="color:#ef4444;">*</span></label>
                                <select name="gender" id="add_gender" required style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem; background: white;">
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Marital Status <span style="color:#ef4444;">*</span></label>
                                <select name="marital_status" id="add_marital_status" required style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem; background: white;">
                                    <option value="Single">Single</option>
                                    <option value="Married">Married</option>
                                    <option value="Divorced">Divorced</option>
                                    <option value="Widowed">Widowed</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Address Information -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem;">
                        <h4 style="font-size: 0.88rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 0.85rem; text-transform: uppercase; letter-spacing: 0.04em;"><i class="bx bx-home-alt" style="color: #3b82f6; margin-right: 0.35rem;"></i> Permanent Address</h4>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">House Name/Number <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="house_name" id="add_house_name" required placeholder="House name or number" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Place <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="place" id="add_place" required placeholder="Enter place" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Post Office (P.O.) <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="po" id="add_po" required placeholder="Post office name" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">District <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="district" id="add_district" required placeholder="Enter district" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">State <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="state" id="add_state" value="Kerala" required placeholder="Enter state" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">PIN Code <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="pin_code" id="add_pin_code" required placeholder="6-digit PIN code" maxlength="6" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6)" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: ID & Banking Details -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem;">
                        <h4 style="font-size: 0.88rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 0.85rem; text-transform: uppercase; letter-spacing: 0.04em;"><i class="bx bx-id-card" style="color: #8b5cf6; margin-right: 0.35rem;"></i> Identity Verification & Banking</h4>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Aadhaar Number <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="aadhar_number" id="add_aadhar_number" required placeholder="12-digit Aadhaar Number" maxlength="14" oninput="this.value = this.value.replace(/\D/g, '').replace(/(\d{4})(?=\d)/g, '$1 ').trim()" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">PAN Card Number <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="pan_card_number" id="add_pan_card_number" required placeholder="10-character PAN" maxlength="10" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem; text-transform: uppercase;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Bank Account Number <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="account_number" id="add_account_number" required placeholder="Account number" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Bank Name <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="bank_name" id="add_bank_name" required placeholder="Bank name" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Bank Branch <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="bank_branch" id="add_bank_branch" required placeholder="Branch name" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">IFSC Code <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="ifsc_code" id="add_ifsc_code" required placeholder="IFSC Code" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem; text-transform: uppercase;">
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Account Credentials & Role -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem;">
                        <h4 style="font-size: 0.88rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 0.85rem; text-transform: uppercase; letter-spacing: 0.04em;"><i class="bx bx-lock-alt" style="color: #f59e0b; margin-right: 0.35rem;"></i> Account & Role Settings</h4>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Official Designation <span style="color:#ef4444;">*</span></label>
                                <input type="text" name="designation" id="add_designation" required placeholder="e.g. Senior Project Manager" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">User Role <span style="color:#ef4444;">*</span></label>
                                <select name="role" id="add_role" onchange="handleAddRoleChange(this.value)" required style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem; background: white;">
                                    <option value="engineer">Engineer</option>
                                    <option value="project_manager">Project Manager</option>
                                    <option value="hod">HOD</option>
                                    <option value="coo">COO</option>
                                    <option value="social_aid">Social Aid Manager</option>
                                    <option value="reception">Reception</option>
                                    <option value="employee">Employee</option>
                                    <option value="others">Others</option>
                                </select>
                            </div>
                            <div id="add_hr_checkbox_wrapper" style="display: none;">
                                <div style="display: flex; align-items: center; background: rgba(168, 85, 247, 0.05); border: 1px solid rgba(168, 85, 247, 0.2); padding: 0.75rem; border-radius: 8px; margin-top: 1.2rem;">
                                    <label style="display: flex; align-items: center; gap: 0.5rem; color: #6b21a8; font-weight: 700; font-size: 0.85rem; cursor: pointer; margin: 0;">
                                        <input type="checkbox" name="is_hr" id="add_is_hr" value="1" style="width: 18px; height: 18px; cursor: pointer;">
                                        <i class="bx bx-shield-quarter" style="font-size: 1.1rem;"></i> Designate as Central HR HOD
                                    </label>
                                </div>
                            </div>
                            <div id="add_hod_select_wrapper">
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Assigned HOD <span style="color:#ef4444;">*</span></label>
                                <select name="assigned_hod_id" id="add_hod_id" required style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem; background: white;">
                                    <option value="">-- Select Assigned HOD --</option>
                                    @foreach($hods as $h)
                                        <option value="{{ $h->id }}">{{ $h->name }} (HOD)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Password <span style="color:#ef4444;">*</span></label>
                                <input type="password" name="password" id="add_password" required placeholder="Minimum 8 characters" minlength="8" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; padding: 1rem 2rem; border-top: 1px solid #e2e8f0; background: #ffffff;">
                    <button type="button" onclick="closeAddStaffModal()" class="btn-action-animated" style="background: #ffffff; border: 1px solid #cbd5e1; color: #475569; padding: 0.65rem 1.25rem; border-radius: 10px; font-weight: 600; font-size: 0.88rem; cursor: pointer;">Cancel</button>
                    <button type="submit" id="add_staff_submit_btn" class="btn-action-animated" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; border: none; border-radius: 10px; padding: 0.65rem 1.5rem; font-weight: 700; font-size: 0.88rem; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25); display: inline-flex; align-items: center; gap: 0.35rem;">
                        <span>Save Staff Member</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==========================================
         EDIT STAFF MODAL (PURE JS / AJAX)
         ========================================== -->
    <div id="editStaffModal" class="modal-overlay-container" style="display: none;" onclick="if(event.target === this) closeEditStaffModal()">
        <div class="modal-dialog-box" style="max-width: 820px; overflow: hidden;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1.5rem 2rem 1rem 2rem; border-bottom: 1px solid #e2e8f0; background: #ffffff;">
                <h3 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 0.4rem;">
                    <i class="bx bx-edit-alt" style="color: #10b981;"></i> Edit Staff Details
                </h3>
                <button type="button" onclick="closeEditStaffModal()" style="background: #f1f5f9; border: none; font-size: 1.4rem; color: #64748b; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer;">&times;</button>
            </div>

            <form id="editStaffForm" onsubmit="submitEditStaffForm(event)" style="display: flex; flex-direction: column; flex: 1; overflow: hidden; margin: 0;">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_user_id" name="user_id" value="">

                <div style="padding: 1.5rem 2rem; overflow-y: auto; display: flex; flex-direction: column; gap: 1.5rem; flex: 1; max-height: 65vh;">
                    <!-- Section 1: Account & Role -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem;">
                        <h4 style="font-size: 0.88rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 0.85rem; text-transform: uppercase; letter-spacing: 0.04em;"><i class="bx bx-user" style="color: #10b981; margin-right: 0.35rem;"></i> Account & Role Information</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Full Name *</label>
                                <input type="text" name="name" id="edit_name" required style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Email Address *</label>
                                <input type="email" name="email" id="edit_email" required style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Mobile Number *</label>
                                <input type="text" name="mobile" id="edit_mobile" required placeholder="10-digit mobile number" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Designation *</label>
                                <input type="text" name="designation" id="edit_designation" required placeholder="e.g. Senior Project Manager" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">System Role *</label>
                                <select name="role" id="edit_role" onchange="handleEditRoleChange(this.value)" required style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem; background: white;">
                                    <option value="super_admin">Super Admin</option>
                                    <option value="coo">COO</option>
                                    <option value="hod">HOD</option>
                                    <option value="project_manager">Project Manager</option>
                                    <option value="engineer">Engineer</option>
                                    <option value="social_aid">Social Aid Manager</option>
                                    <option value="reception">Reception</option>
                                    <option value="employee">Employee</option>
                                    <option value="others">Others</option>
                                </select>
                            </div>
                            <div id="edit_hr_checkbox_wrapper" style="display: none;">
                                <div style="display: flex; align-items: center; background: rgba(168, 85, 247, 0.05); border: 1px solid rgba(168, 85, 247, 0.2); padding: 0.75rem; border-radius: 8px; margin-top: 1.2rem;">
                                    <label style="display: flex; align-items: center; gap: 0.5rem; color: #6b21a8; font-weight: 700; font-size: 0.85rem; cursor: pointer; margin: 0;">
                                        <input type="checkbox" name="is_hr" id="edit_is_hr" value="1" style="width: 18px; height: 18px; cursor: pointer;">
                                        <i class="bx bx-shield-quarter" style="font-size: 1.1rem;"></i> Designate as Central HR HOD
                                    </label>
                                </div>
                            </div>
                            <div id="edit_hod_select_wrapper">
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Assigned HOD</label>
                                <select name="assigned_hod_id" id="edit_hod_id" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem; background: white;">
                                    <option value="">-- Select Assigned HOD --</option>
                                    @foreach($hods as $h)
                                        <option value="{{ $h->id }}">{{ $h->name }} (HOD)</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Personal Info -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem;">
                        <h4 style="font-size: 0.88rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 0.85rem; text-transform: uppercase; letter-spacing: 0.04em;"><i class="bx bx-id-card" style="color: #3b82f6; margin-right: 0.35rem;"></i> Personal Information</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Father's Name</label>
                                <input type="text" name="father_name" id="edit_father_name" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Mother's Name</label>
                                <input type="text" name="mother_name" id="edit_mother_name" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Date of Birth</label>
                                <input type="date" name="date_of_birth" id="edit_date_of_birth" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem; background: white;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Date of Joining</label>
                                <input type="date" name="date_of_joining" id="edit_date_of_joining" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem; background: white;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Gender</label>
                                <select name="gender" id="edit_gender" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem; background: white;">
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Marital Status</label>
                                <select name="marital_status" id="edit_marital_status" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem; background: white;">
                                    <option value="Single">Single</option>
                                    <option value="Married">Married</option>
                                    <option value="Divorced">Divorced</option>
                                    <option value="Widowed">Widowed</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Address & Bank Info -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem;">
                        <h4 style="font-size: 0.88rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 0.85rem; text-transform: uppercase; letter-spacing: 0.04em;"><i class="bx bx-home-alt" style="color: #8b5cf6; margin-right: 0.35rem;"></i> Address & Bank Details</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">House Name</label>
                                <input type="text" name="house_name" id="edit_house_name" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Place</label>
                                <input type="text" name="place" id="edit_place" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">District</label>
                                <input type="text" name="district" id="edit_district" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">State</label>
                                <input type="text" name="state" id="edit_state" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">PIN Code</label>
                                <input type="text" name="pin_code" id="edit_pin_code" placeholder="6-digit PIN code" maxlength="6" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6)" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Aadhaar Number</label>
                                <input type="text" name="aadhar_number" id="edit_aadhar_number" placeholder="12-digit Aadhaar Number" maxlength="14" oninput="this.value = this.value.replace(/\D/g, '').replace(/(\d{4})(?=\d)/g, '$1 ').trim()" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">PAN Card Number</label>
                                <input type="text" name="pan_card_number" id="edit_pan_card_number" maxlength="10" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem; text-transform: uppercase;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Bank Account No</label>
                                <input type="text" name="account_number" id="edit_account_number" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">Bank Name</label>
                                <input type="text" name="bank_name" id="edit_bank_name" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.3rem;">IFSC Code</label>
                                <input type="text" name="ifsc_code" id="edit_ifsc_code" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem; text-transform: uppercase;">
                            </div>
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; padding: 1rem 2rem; border-top: 1px solid #e2e8f0; background: #ffffff;">
                    <button type="button" onclick="closeEditStaffModal()" class="btn-action-animated" style="background: #ffffff; border: 1px solid #cbd5e1; color: #475569; padding: 0.6rem 1.15rem; border-radius: 8px; font-weight: 600; font-size: 0.88rem; cursor: pointer;">Cancel</button>
                    <button type="submit" id="edit_staff_submit_btn" class="btn-action-animated" style="background: #10b981; color: #ffffff; border: none; border-radius: 8px; padding: 0.6rem 1.25rem; font-weight: 700; font-size: 0.88rem; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25); display: inline-flex; align-items: center; gap: 0.35rem;">
                        <span>Save Staff Details</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==========================================
         REJECT LEAVE MODAL (PURE JS / AJAX)
         ========================================== -->
    <div id="rejectLeaveModal" class="modal-overlay-container" style="display: none;" onclick="if(event.target === this) closeRejectLeaveModal()">
        <div class="modal-dialog-box" style="max-width: 480px; padding: 1.75rem;">
            <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-top: 0; margin-bottom: 0.5rem;">Reject Staff Leave Request</h3>
            <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 1rem;">State reason for rejecting this leave request:</p>

            <input type="hidden" id="reject_leave_id" value="">
            <textarea id="reject_leave_remarks" rows="3" placeholder="Provide reason for rejection..." style="width: 100%; padding: 0.65rem 1rem; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 0.9rem; outline: none; margin-bottom: 1rem; font-family: inherit;"></textarea>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" onclick="closeRejectLeaveModal()" class="btn-action-animated" style="background: #ffffff; border: 1px solid #cbd5e1; color: #475569; padding: 0.6rem 1.15rem; border-radius: 8px; font-weight: 600; font-size: 0.88rem; cursor: pointer;">Cancel</button>
                <button type="button" onclick="confirmRejectLeaveJs()" id="confirm_reject_leave_btn" class="btn-action-animated" style="background: #ef4444; color: #ffffff; border: none; border-radius: 8px; padding: 0.6rem 1.25rem; font-weight: 700; font-size: 0.88rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem;">
                    <span>Confirm Rejection</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ==========================================
         PURE JAVASCRIPT & AJAX LOGIC
         ========================================== -->
    <script>
        var CSRF_TOKEN = '{{ csrf_token() }}';
        window.CSRF_TOKEN = CSRF_TOKEN;

        function closeAllModals() {
            const modalIds = ['staffDetailsModal', 'addStaffModal', 'editStaffModal', 'rejectLeaveModal'];
            modalIds.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.classList.remove('is-active');
                    el.style.display = 'none';
                }
            });
        }

        // FIX #4: guard against duplicate keydown listeners if this Livewire
        // component re-renders/re-mounts this script block more than once
        // over the page's lifetime.
        if (!window.__staffModalsEscBound) {
            window.__staffModalsEscBound = true;
            window.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeAllModals();
                }
            });
        }

        // 1. Staff Details Modal
        function openStaffDetailsModal(userId) {
            closeAllModals();
            const modal = document.getElementById('staffDetailsModal');
            const loading = document.getElementById('sd_loading');
            const content = document.getElementById('sd_content');
            
            modal.classList.add('is-active');
            modal.style.display = 'flex';
            loading.style.display = 'flex';
            content.style.display = 'none';

            fetch(`/admin/users/${userId}/details`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success || !data.user) {
                    if (typeof showToast === 'function') {
                        showToast('Unable to load staff details.', 'error');
                    }
                    closeStaffDetailsModal();
                    return;
                }

                const u = data.user;
                document.getElementById('sd_avatar').textContent = (u.name || 'S').charAt(0).toUpperCase();
                document.getElementById('sd_name').textContent = u.name || 'N/A';
                document.getElementById('sd_designation').textContent = u.designation || 'Staff Member';
                document.getElementById('sd_role').textContent = (u.role || 'User').toUpperCase();

                document.getElementById('sd_email').textContent = u.email || '—';
                document.getElementById('sd_mobile').textContent = u.mobile || '—';
                document.getElementById('sd_father_name').textContent = u.father_name || '—';
                document.getElementById('sd_mother_name').textContent = u.mother_name || '—';
                document.getElementById('sd_dob').textContent = u.date_of_birth || '—';
                document.getElementById('sd_doj').textContent = u.date_of_joining || '—';
                document.getElementById('sd_gender_marital').textContent = (u.gender || 'N/A') + ' / ' + (u.marital_status || 'N/A');

                if (u.assigned_hod_name) {
                    document.getElementById('sd_hod_wrapper').style.display = 'block';
                    document.getElementById('sd_assigned_hod').textContent = u.assigned_hod_name + ' (HOD)';
                } else {
                    document.getElementById('sd_hod_wrapper').style.display = 'none';
                }

                document.getElementById('sd_house_name').textContent = u.house_name || '—';
                document.getElementById('sd_place_po').textContent = (u.place || 'N/A') + (u.po ? ' (P.O: ' + u.po + ')' : '');
                document.getElementById('sd_district_state').textContent = (u.district || 'N/A') + ', ' + (u.state || 'Kerala');
                document.getElementById('sd_pincode').textContent = u.pin_code || '—';

                document.getElementById('sd_aadhar').textContent = u.formatted_aadhar_number || u.aadhar_number || '—';
                document.getElementById('sd_pan').textContent = (u.pan_card_number || '—').toUpperCase();
                document.getElementById('sd_account_no').textContent = u.account_number || '—';
                document.getElementById('sd_bank_branch').textContent = (u.bank_name || 'N/A') + (u.bank_branch ? ' (' + u.bank_branch + ')' : '');
                document.getElementById('sd_ifsc').textContent = (u.ifsc_code || '—').toUpperCase();

                // Leave Balances
                const balancesContainer = document.getElementById('sd_leave_balances_container');
                if (data.leave_balances && data.leave_balances.length > 0) {
                    balancesContainer.innerHTML = data.leave_balances.map(b => `
                        <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0.6rem 0.85rem;">
                            <span style="font-size: 0.72rem; font-weight: 800; color: #64748b; display: block;">${b.leave_code || 'Type'}</span>
                            <strong style="font-size: 1.1rem; color: #10b981;">${b.balance_days} <span style="font-size: 0.7rem; color: #64748b;">days</span></strong>
                        </div>
                    `).join('');
                } else {
                    balancesContainer.innerHTML = `<div style="color: #94a3b8;">No leave balance records.</div>`;
                }

                // Assigned Projects
                const projectsSec = document.getElementById('sd_projects_section');
                const projectsContainer = document.getElementById('sd_projects_container');
                if (data.projects && data.projects.length > 0) {
                    projectsSec.style.display = 'block';
                    projectsContainer.innerHTML = data.projects.map(p => `
                        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.6rem 0.85rem; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <strong style="font-size: 0.85rem; color: #0f172a;">${p.title}</strong>
                                <div style="font-size: 0.75rem; color: #64748b;">${p.type || ''} &bull; ${p.role || ''}</div>
                            </div>
                            <span style="font-size: 0.72rem; font-weight: 800; background: #ecfdf5; color: #059669; padding: 0.2rem 0.5rem; border-radius: 6px;">${p.status || 'Active'}</span>
                        </div>
                    `).join('');
                } else {
                    projectsSec.style.display = 'none';
                }

                document.getElementById('sd_edit_btn').setAttribute('onclick', `closeStaffDetailsModal(); openEditStaffModal(${u.id || userId});`);

                loading.style.display = 'none';
                content.style.display = 'flex';
            })
            .catch(err => {
                if (typeof showToast === 'function') {
                    showToast('Error loading staff details: ' + err.message, 'error');
                }
                closeStaffDetailsModal();
            });
        }

        function closeStaffDetailsModal() {
            const modal = document.getElementById('staffDetailsModal');
            if (modal) {
                modal.classList.remove('is-active');
                modal.style.display = 'none';
            }
        }

        // 2. Add Staff Modal
        function openAddStaffModal() {
            closeAllModals();
            const modal = document.getElementById('addStaffModal');
            document.getElementById('addStaffForm').reset();
            // FIX #2: was hardcoded handleAddRoleChange('others'), which desynced
            // the HOD/HR wrapper visibility from whatever the <select> actually
            // reset to (its first option, "engineer"). Now reads the real value.
            handleAddRoleChange(document.getElementById('add_role').value);
            modal.classList.add('is-active');
            modal.style.display = 'flex';
        }

        function closeAddStaffModal() {
            const modal = document.getElementById('addStaffModal');
            if (modal) {
                modal.classList.remove('is-active');
                modal.style.display = 'none';
            }
        }

        function handleAddRoleChange(role) {
            const hrWrapper = document.getElementById('add_hr_checkbox_wrapper');
            const hodWrapper = document.getElementById('add_hod_select_wrapper');
            const hodSelect = document.getElementById('add_hod_id');

            if (role === 'hod') {
                hrWrapper.style.display = 'block';
                hodWrapper.style.display = 'none';
                hodSelect.value = '';
            } else if (role === 'super_admin' || role === 'coo') {
                hrWrapper.style.display = 'none';
                hodWrapper.style.display = 'none';
                hodSelect.value = '';
            } else {
                hrWrapper.style.display = 'none';
                hodWrapper.style.display = 'block';
            }
        }

        function submitAddStaffForm(e) {
            e.preventDefault();
            const form = document.getElementById('addStaffForm');
            const btn = document.getElementById('add_staff_submit_btn');
            const formData = new FormData(form);

            btn.disabled = true;
            btn.innerHTML = `<i class="bx bx-loader-alt bx-spin"></i> <span>Saving...</span>`;

            fetch('/doAddUser', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(async res => {
                const data = await res.json().catch(() => ({}));
                if (!res.ok) {
                    const errorMsg = data.errors ? Object.values(data.errors).flat().join('\n') : (data.message || 'Failed to create staff member.');
                    throw new Error(errorMsg);
                }
                return data;
            })
            .then(data => {
                closeAddStaffModal();
                if (typeof showToast === 'function') {
                    showToast(data.message || 'Staff member created successfully!', 'success');
                }
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            })
            .catch(err => {
                if (typeof showToast === 'function') {
                    showToast(err.message || 'Validation error occurred.', 'error');
                } else {
                    alert('Validation Error:\n' + err.message);
                }
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = `<span>Save Staff Member</span>`;
            });
        }

        // 3. Edit Staff Modal
        function openEditStaffModal(userId) {
            closeAllModals();
            const modal = document.getElementById('editStaffModal');
            modal.classList.add('is-active');
            modal.style.display = 'flex';

            fetch(`/admin/users/${userId}/details`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success || !data.user) {
                    if (typeof showToast === 'function') {
                        showToast('Unable to load staff details for editing.', 'error');
                    }
                    closeEditStaffModal();
                    return;
                }

                const u = data.user;
                document.getElementById('edit_user_id').value = u.id || userId;
                document.getElementById('edit_name').value = u.name || '';
                document.getElementById('edit_email').value = u.email || '';
                document.getElementById('edit_mobile').value = u.mobile || '';
                document.getElementById('edit_designation').value = u.designation || '';
                document.getElementById('edit_role').value = u.raw_role || 'others';
                
                handleEditRoleChange(u.raw_role || 'others');

                document.getElementById('edit_is_hr').checked = !!u.is_hr;
                document.getElementById('edit_hod_id').value = u.assigned_hod_id || u.hod_id || '';

                document.getElementById('edit_father_name').value = u.father_name || '';
                document.getElementById('edit_mother_name').value = u.mother_name || '';
                document.getElementById('edit_date_of_birth').value = u.date_of_birth || '';
                document.getElementById('edit_date_of_joining').value = u.date_of_joining || '';
                document.getElementById('edit_gender').value = u.gender || 'Male';
                document.getElementById('edit_marital_status').value = u.marital_status || 'Single';

                document.getElementById('edit_house_name').value = u.house_name || '';
                document.getElementById('edit_place').value = u.place || '';
                document.getElementById('edit_district').value = u.district || '';
                document.getElementById('edit_state').value = u.state || 'Kerala';
                document.getElementById('edit_pin_code').value = u.pin_code || '';

                document.getElementById('edit_aadhar_number').value = u.aadhar_number || '';
                document.getElementById('edit_pan_card_number').value = u.pan_card_number || '';
                document.getElementById('edit_account_number').value = u.account_number || '';
                document.getElementById('edit_bank_name').value = u.bank_name || '';
                document.getElementById('edit_ifsc_code').value = u.ifsc_code || '';
            })
            .catch(err => {
                if (typeof showToast === 'function') {
                    showToast('Error loading staff details: ' + err.message, 'error');
                }
                closeEditStaffModal();
            });
        }

        function closeEditStaffModal() {
            const modal = document.getElementById('editStaffModal');
            if (modal) {
                modal.classList.remove('is-active');
                modal.style.display = 'none';
            }
        }

        function handleEditRoleChange(role) {
            const hrWrapper = document.getElementById('edit_hr_checkbox_wrapper');
            const hodWrapper = document.getElementById('edit_hod_select_wrapper');
            const hodSelect = document.getElementById('edit_hod_id');

            if (role === 'hod') {
                hrWrapper.style.display = 'block';
                hodWrapper.style.display = 'none';
                hodSelect.value = '';
            } else if (role === 'super_admin' || role === 'coo') {
                hrWrapper.style.display = 'none';
                hodWrapper.style.display = 'none';
                hodSelect.value = '';
            } else {
                hrWrapper.style.display = 'none';
                hodWrapper.style.display = 'block';
            }
        }

        function submitEditStaffForm(e) {
            e.preventDefault();
            const form = document.getElementById('editStaffForm');
            const btn = document.getElementById('edit_staff_submit_btn');
            const userId = document.getElementById('edit_user_id').value;
            const formData = new FormData(form);

            btn.disabled = true;
            btn.innerHTML = `<i class="bx bx-loader-alt bx-spin"></i> <span>Saving...</span>`;

            fetch(`/admin/users/${userId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(async res => {
                const data = await res.json().catch(() => ({}));
                if (!res.ok) {
                    const errorMsg = data.errors ? Object.values(data.errors).flat().join('\n') : (data.message || 'Failed to update staff details.');
                    throw new Error(errorMsg);
                }
                return data;
            })
            .then(data => {
                closeEditStaffModal();
                if (typeof showToast === 'function') {
                    showToast(data.message || 'Staff details updated successfully!', 'success');
                }
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            })
            .catch(err => {
                if (typeof showToast === 'function') {
                    showToast(err.message || 'Update error occurred.', 'error');
                } else {
                    alert('Update Error:\n' + err.message);
                }
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = `<span>Save Staff Details</span>`;
            });
        }

        // 4. Toggle User Suspend via AJAX
        function toggleUserSuspendJs(userId, btnEl) {
            if (!confirm('Are you sure you want to change this staff member account status?')) {
                return;
            }

            const originalHtml = btnEl.innerHTML;
            btnEl.disabled = true;
            btnEl.innerHTML = `<i class="bx bx-loader-alt bx-spin"></i>`;

            fetch(`/admin/users/${userId}/toggle-suspend`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Update card & table buttons instantly
                    const isSuspended = data.is_suspended;
                    const newLabel = isSuspended ? 'Activate' : 'Suspend';
                    const newBg = isSuspended ? '#ecfdf5' : '#fef2f2';
                    const newColor = isSuspended ? '#059669' : '#ef4444';
                    const newBorder = isSuspended ? '#a7f3d0' : '#fee2e2';

                    btnEl.textContent = newLabel;
                    btnEl.style.background = newBg;
                    btnEl.style.color = newColor;
                    btnEl.style.borderColor = newBorder;

                    const altBtn = document.getElementById(`card-suspend-btn-${userId}`) || document.getElementById(`table-suspend-btn-${userId}`);
                    if (altBtn && altBtn !== btnEl) {
                        altBtn.textContent = newLabel;
                        altBtn.style.background = newBg;
                        altBtn.style.color = newColor;
                        altBtn.style.borderColor = newBorder;
                    }

                    if (typeof showToast === 'function') {
                        showToast(data.message || 'Status updated successfully!', 'success');
                    }
                } else {
                    if (typeof showToast === 'function') {
                        showToast(data.message || 'Failed to update status.', 'error');
                    }
                    btnEl.innerHTML = originalHtml;
                }
            })
            .catch(err => {
                if (typeof showToast === 'function') {
                    showToast('Error: ' + err.message, 'error');
                }
                btnEl.innerHTML = originalHtml;
            })
            .finally(() => {
                btnEl.disabled = false;
            });
        }

        // 5. Reject Leave via AJAX
        function openRejectLeaveModal(leaveId) {
            closeAllModals();
            document.getElementById('reject_leave_id').value = leaveId;
            document.getElementById('reject_leave_remarks').value = '';
            const modal = document.getElementById('rejectLeaveModal');
            modal.classList.add('is-active');
            modal.style.display = 'flex';
        }

        function closeRejectLeaveModal() {
            const modal = document.getElementById('rejectLeaveModal');
            if (modal) {
                modal.classList.remove('is-active');
                modal.style.display = 'none';
            }
        }

        function confirmRejectLeaveJs() {
            const leaveId = document.getElementById('reject_leave_id').value;
            const remarks = document.getElementById('reject_leave_remarks').value.trim();

            if (!remarks || remarks.length < 3) {
                if (typeof showToast === 'function') {
                    showToast('Please provide a reason for rejecting this leave request.', 'warning');
                }
                return;
            }

            const btn = document.getElementById('confirm_reject_leave_btn');
            btn.disabled = true;
            btn.innerHTML = `<i class="bx bx-loader-alt bx-spin"></i> <span>Rejecting...</span>`;

            fetch(`/admin/leave-requests/${leaveId}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ rejection_reason: remarks, remarks: remarks })
            })
            .then(res => res.json().catch(() => ({})))
            .then(data => {
                closeRejectLeaveModal();
                if (typeof showToast === 'function') {
                    showToast('Leave request rejected successfully.', 'success');
                }
                setTimeout(() => {
                    window.location.reload();
                }, 800);
            })
            .catch(err => {
                if (typeof showToast === 'function') {
                    showToast('Error rejecting leave request.', 'error');
                }
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = `<span>Confirm Rejection</span>`;
            });
        }
    </script>
</div>