@extends('layouts.admin')

@section('title', 'Themes & Subthemes')

@section('content')

    <!-- Top Header & Navigation Tabs -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <!-- Navigation Tabs -->
        <div style="display: flex; gap: 0.5rem; background-color: #ffffff; padding: 0.35rem; border-radius: 10px; border: 1px solid var(--panel-border, #e2e8f0); box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <button id="tab-btn-themes" onclick="switchTab('themes')" style="display: flex; align-items: center; gap: 0.6rem; padding: 0.6rem 1.25rem; font-size: 0.9rem; font-weight: 600; border: none; border-radius: 8px; cursor: pointer; transition: all 0.2s; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff;">
                <i class="bx bxs-category"></i>
                <span>Themes</span>
                <span id="badge-count-themes" style="background: rgba(255,255,255,0.25); color: #ffffff; font-size: 0.75rem; padding: 0.15rem 0.55rem; border-radius: 12px; font-weight: 700;">{{ count($themes) }}</span>
            </button>
            
            <button id="tab-btn-subthemes" onclick="switchTab('subthemes')" style="display: flex; align-items: center; gap: 0.6rem; padding: 0.6rem 1.25rem; font-size: 0.9rem; font-weight: 600; border: none; border-radius: 8px; cursor: pointer; transition: all 0.2s; background: transparent; color: #64748b;">
                <i class="bx bxs-layer"></i>
                <span>Subthemes</span>
                <span id="badge-count-subthemes" style="background: rgba(99,102,241,0.12); color: #4338ca; font-size: 0.75rem; padding: 0.15rem 0.55rem; border-radius: 12px; font-weight: 700;">{{ count($subthemes) }}</span>
            </button>
        </div>

        <!-- Add Actions -->
        @if($canManage)
        <div style="display: flex; gap: 0.75rem;">
            <button onclick="openAddThemeModal()" class="btn-custom" style="padding: 0.6rem 1.25rem; font-size: 0.85rem; display: flex; align-items: center; gap: 0.4rem;">
                <i class="bx bx-plus-circle"></i> Add Theme
            </button>
            <button onclick="openAddSubthemeModal()" class="btn-custom" style="padding: 0.6rem 1.25rem; font-size: 0.85rem; background: linear-gradient(135deg, #6366f1, #4f46e5); display: flex; align-items: center; gap: 0.4rem;">
                <i class="bx bx-plus-circle"></i> Add Subtheme
            </button>
        </div>
        @endif
    </div>

    <!-- Alert Notifications -->
    @if (session('success'))
        <div class="alert alert-success" style="background-color: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent-green, #10b981); color: #047857; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500;">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid var(--accent-red, #ef4444); color: #b91c1c; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500;">
            {{ session('error') }}
        </div>
    @endif

    @if (isset($errors) && $errors->any())
        <div style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid var(--accent-red, #ef4444); color: #b91c1c; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500;">
            <ul style="list-style-position: inside; margin: 0; padding: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- TAB 1: THEMES TAB PANEL -->
    <div id="tab-content-themes" class="panel" style="width: 100%;">
        <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--panel-border, #e2e8f0); padding-bottom: 1rem; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 class="panel-title" style="font-size: 1.15rem; font-weight: 700; color: #0f172a;">Themes List</h2>
                <p style="font-size: 0.8rem; color: #64748b; margin-top: 0.25rem;">Manage top-level project themes and categories</p>
            </div>
            
            <div>
                <input type="text" id="searchThemesInput" onkeyup="filterThemesTable()" placeholder="Search themes..." style="padding: 0.5rem 0.9rem; font-size: 0.85rem; border: 1px solid #cbd5e1; border-radius: 6px; width: 250px; outline: none; background-color: #ffffff; color: #0f172a;">
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="table-custom" id="themesTable">
                <thead>
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th>Theme Name</th>
                        <th style="text-align: center; width: 200px;">Subthemes Count</th>
                        <th style="text-align: center; width: 120px;">Status</th>
                        @if($canManage)
                        <th style="text-align: center; width: 140px;">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($themes as $theme)
                        <tr>
                            <td style="color: #64748b; font-size: 0.85rem; font-weight: 600;">#{{ $theme->id }}</td>
                            <td>
                                <div style="font-weight: 600; color: #0f172a; font-size: 0.95rem;">{{ $theme->name }}</div>
                            </td>
                            <td style="text-align: center;">
                                <button onclick="switchToSubthemesByTheme({{ $theme->id }})" style="background-color: rgba(99, 102, 241, 0.12); border: 1px solid rgba(99, 102, 241, 0.3); color: #4338ca; padding: 0.35rem 0.85rem; border-radius: 16px; font-size: 0.8rem; font-weight: 600; white-space: nowrap; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.35rem;" title="View subthemes for {{ $theme->name }}">
                                    <i class="bx bxs-layer"></i>
                                    <span>{{ $theme->subthemes_count }} Subthemes</span>
                                </button>
                            </td>
                            <td style="text-align: center;">
                                @if($theme->status == 1)
                                    <span style="background-color: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3); color: #059669; padding: 0.25rem 0.65rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600; white-space: nowrap; display: inline-flex; align-items: center; gap: 0.3rem;">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background-color: #059669;"></span> Active
                                    </span>
                                @else
                                    <span style="background-color: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.3); color: #dc2626; padding: 0.25rem 0.65rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600; white-space: nowrap; display: inline-flex; align-items: center; gap: 0.3rem;">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background-color: #dc2626;"></span> Inactive
                                    </span>
                                @endif
                            </td>
                            @if($canManage)
                            <td style="text-align: center; white-space: nowrap; width: 110px;">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 0.4rem;">
                                    <button type="button" onclick="openEditThemeModal({{ json_encode($theme) }})" class="btn-custom" style="background: transparent; color: #0284c7; border: 1px solid #0284c7; width: 32px; height: 32px; padding: 0; font-size: 1rem; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center;" title="Edit Theme">
                                        <i class="bx bx-pencil"></i>
                                    </button>
                                    
                                    <form action="{{ route('themes.destroy', $theme->id) }}" method="POST" style="display: inline-flex; margin: 0;" onsubmit="return confirm('Are you sure you want to delete this theme? All associated subthemes will also be removed.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger-custom" style="width: 32px; height: 32px; padding: 0; font-size: 1rem; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center;" title="Delete Theme">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $canManage ? 5 : 4 }}" style="text-align: center; padding: 2.5rem; color: #64748b;">No themes registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 2: SUBTHEMES TAB PANEL -->
    <div id="tab-content-subthemes" class="panel" style="width: 100%; display: none;">
        <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--panel-border, #e2e8f0); padding-bottom: 1rem; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 class="panel-title" style="font-size: 1.15rem; font-weight: 700; color: #0f172a;">Subthemes List</h2>
                <p style="font-size: 0.8rem; color: #64748b; margin-top: 0.25rem;">Manage detailed subthemes mapped to parent themes</p>
            </div>
            
            <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
                <!-- Parent Theme Filter Dropdown -->
                <div style="display: flex; align-items: center; gap: 0.4rem;">
                    <label style="font-size: 0.8rem; font-weight: 600; color: #64748b; white-space: nowrap;">Filter Theme:</label>
                    <select id="subthemeParentFilter" onchange="filterSubthemesTable()" style="padding: 0.5rem 0.85rem; font-size: 0.85rem; border: 1px solid #cbd5e1; border-radius: 6px; background-color: #ffffff; color: #0f172a; outline: none; max-width: 250px;">
                        <option value="">All Themes</option>
                        @foreach($themes as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Search Input -->
                <input type="text" id="searchSubthemesInput" onkeyup="filterSubthemesTable()" placeholder="Search subthemes..." style="padding: 0.5rem 0.9rem; font-size: 0.85rem; border: 1px solid #cbd5e1; border-radius: 6px; width: 230px; outline: none; background-color: #ffffff; color: #0f172a;">
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="table-custom" id="subthemesTable">
                <thead>
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th>Subtheme Name</th>
                        <th>Parent Theme</th>
                        <th style="text-align: center; width: 120px;">Status</th>
                        @if($canManage)
                        <th style="text-align: center; width: 140px;">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($subthemes as $sub)
                        <tr data-theme-id="{{ $sub->theme_id }}">
                            <td style="color: #64748b; font-size: 0.85rem; font-weight: 600;">#{{ $sub->id }}</td>
                            <td>
                                <div style="font-weight: 600; color: #0f172a; font-size: 0.95rem;">{{ $sub->name }}</div>
                            </td>
                            <td>
                                <span style="background-color: rgba(2, 132, 199, 0.1); border: 1px solid rgba(2, 132, 199, 0.25); color: #0284c7; padding: 0.25rem 0.75rem; border-radius: 12px; font-weight: 600; font-size: 0.8rem; display: inline-block;">
                                    {{ $sub->theme ? $sub->theme->name : 'Unassigned' }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                @if($sub->status == 1)
                                    <span style="background-color: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3); color: #059669; padding: 0.25rem 0.65rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600; white-space: nowrap; display: inline-flex; align-items: center; gap: 0.3rem;">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background-color: #059669;"></span> Active
                                    </span>
                                @else
                                    <span style="background-color: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.3); color: #dc2626; padding: 0.25rem 0.65rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600; white-space: nowrap; display: inline-flex; align-items: center; gap: 0.3rem;">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background-color: #dc2626;"></span> Inactive
                                    </span>
                                @endif
                            </td>
                            @if($canManage)
                            <td style="text-align: center; white-space: nowrap; width: 110px;">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 0.4rem;">
                                    <button type="button" onclick="openEditSubthemeModal({{ json_encode($sub) }})" class="btn-custom" style="background: transparent; color: #0284c7; border: 1px solid #0284c7; width: 32px; height: 32px; padding: 0; font-size: 1rem; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center;" title="Edit Subtheme">
                                        <i class="bx bx-pencil"></i>
                                    </button>
                                    
                                    <form action="{{ route('subthemes.destroy', $sub->id) }}" method="POST" style="display: inline-flex; margin: 0;" onsubmit="return confirm('Are you sure you want to delete this subtheme?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger-custom" style="width: 32px; height: 32px; padding: 0; font-size: 1rem; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center;" title="Delete Subtheme">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $canManage ? 5 : 4 }}" style="text-align: center; padding: 2.5rem; color: #64748b;">No subthemes registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= ADD THEME MODAL ================= -->
    <div id="addThemeModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.75); display: none; align-items: center; justify-content: center; z-index: 1000;" onclick="closeModal('addThemeModal')">
        <div class="panel" style="width: 100%; max-width: 480px; margin: 2rem auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #cbd5e1; background-color: #ffffff;" onclick="event.stopPropagation()">
            <button onclick="closeModal('addThemeModal')" style="position: absolute; top: 1.25rem; right: 1.25rem; background: none; border: none; color: #64748b; font-size: 1.5rem; cursor: pointer;"><i class="bx bx-x"></i></button>
            
            <div class="panel-header" style="margin-bottom: 1.25rem;">
                <h2 class="panel-title" style="font-size: 1.15rem; color: #0f172a;">Add New Theme</h2>
            </div>

            <form action="{{ route('themes.store') }}" method="POST">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="theme_name" style="color: #334155; font-weight: 600;">Theme Name</label>
                    <input type="text" class="form-control-dark" id="theme_name" name="name" placeholder="e.g. Social Welfare" required style="color: #0f172a; border-color: #cbd5e1;">
                </div>

                <div style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" id="theme_status" name="status" value="1" checked style="accent-color: #0284c7; width: 18px; height: 18px;">
                    <label for="theme_status" style="color: #334155; font-size: 0.9rem; cursor: pointer; font-weight: 500;">Active Status</label>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" onclick="closeModal('addThemeModal')" class="btn-custom" style="background: transparent; border: 1px solid #cbd5e1; color: #64748b;">Cancel</button>
                    <button type="submit" class="btn-custom">Save Theme</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= EDIT THEME MODAL ================= -->
    <div id="editThemeModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.75); display: none; align-items: center; justify-content: center; z-index: 1000;" onclick="closeModal('editThemeModal')">
        <div class="panel" style="width: 100%; max-width: 480px; margin: 2rem auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #cbd5e1; background-color: #ffffff;" onclick="event.stopPropagation()">
            <button onclick="closeModal('editThemeModal')" style="position: absolute; top: 1.25rem; right: 1.25rem; background: none; border: none; color: #64748b; font-size: 1.5rem; cursor: pointer;"><i class="bx bx-x"></i></button>
            
            <div class="panel-header" style="margin-bottom: 1.25rem;">
                <h2 class="panel-title" style="font-size: 1.15rem; color: #0f172a;">Edit Theme</h2>
            </div>

            <form id="editThemeForm" method="POST">
                @csrf
                @method('PUT')
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="edit_theme_name" style="color: #334155; font-weight: 600;">Theme Name</label>
                    <input type="text" class="form-control-dark" id="edit_theme_name" name="name" required style="color: #0f172a; border-color: #cbd5e1;">
                </div>

                <div style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" id="edit_theme_status" name="status" value="1" style="accent-color: #0284c7; width: 18px; height: 18px;">
                    <label for="edit_theme_status" style="color: #334155; font-size: 0.9rem; cursor: pointer; font-weight: 500;">Active Status</label>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" onclick="closeModal('editThemeModal')" class="btn-custom" style="background: transparent; border: 1px solid #cbd5e1; color: #64748b;">Cancel</button>
                    <button type="submit" class="btn-custom">Update Theme</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= ADD SUBTHEME MODAL ================= -->
    <div id="addSubthemeModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.75); display: none; align-items: center; justify-content: center; z-index: 1000;" onclick="closeModal('addSubthemeModal')">
        <div class="panel" style="width: 100%; max-width: 480px; margin: 2rem auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #cbd5e1; background-color: #ffffff;" onclick="event.stopPropagation()">
            <button onclick="closeModal('addSubthemeModal')" style="position: absolute; top: 1.25rem; right: 1.25rem; background: none; border: none; color: #64748b; font-size: 1.5rem; cursor: pointer;"><i class="bx bx-x"></i></button>
            
            <div class="panel-header" style="margin-bottom: 1.25rem;">
                <h2 class="panel-title" style="font-size: 1.15rem; color: #0f172a;">Add New Subtheme</h2>
            </div>

            <form action="{{ route('subthemes.store') }}" method="POST">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="subtheme_theme_id" style="color: #334155; font-weight: 600;">Parent Theme</label>
                    <select class="form-control-dark" id="subtheme_theme_id" name="theme_id" required style="color: #0f172a; border-color: #cbd5e1; background-color: #ffffff;">
                        <option value="">Select Parent Theme...</option>
                        @foreach($themes as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="subtheme_name" style="color: #334155; font-weight: 600;">Subtheme Name</label>
                    <input type="text" class="form-control-dark" id="subtheme_name" name="name" placeholder="e.g. Healthcare Assistance" required style="color: #0f172a; border-color: #cbd5e1;">
                </div>

                <div style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" id="subtheme_status" name="status" value="1" checked style="accent-color: #6366f1; width: 18px; height: 18px;">
                    <label for="subtheme_status" style="color: #334155; font-size: 0.9rem; cursor: pointer; font-weight: 500;">Active Status</label>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" onclick="closeModal('addSubthemeModal')" class="btn-custom" style="background: transparent; border: 1px solid #cbd5e1; color: #64748b;">Cancel</button>
                    <button type="submit" class="btn-custom" style="background: linear-gradient(135deg, #6366f1, #4f46e5);">Save Subtheme</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= EDIT SUBTHEME MODAL ================= -->
    <div id="editSubthemeModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.75); display: none; align-items: center; justify-content: center; z-index: 1000;" onclick="closeModal('editSubthemeModal')">
        <div class="panel" style="width: 100%; max-width: 480px; margin: 2rem auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #cbd5e1; background-color: #ffffff;" onclick="event.stopPropagation()">
            <button onclick="closeModal('editSubthemeModal')" style="position: absolute; top: 1.25rem; right: 1.25rem; background: none; border: none; color: #64748b; font-size: 1.5rem; cursor: pointer;"><i class="bx bx-x"></i></button>
            
            <div class="panel-header" style="margin-bottom: 1.25rem;">
                <h2 class="panel-title" style="font-size: 1.15rem; color: #0f172a;">Edit Subtheme</h2>
            </div>

            <form id="editSubthemeForm" method="POST">
                @csrf
                @method('PUT')
                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="edit_subtheme_theme_id" style="color: #334155; font-weight: 600;">Parent Theme</label>
                    <select class="form-control-dark" id="edit_subtheme_theme_id" name="theme_id" required style="color: #0f172a; border-color: #cbd5e1; background-color: #ffffff;">
                        <option value="">Select Parent Theme...</option>
                        @foreach($themes as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label class="form-label" for="edit_subtheme_name" style="color: #334155; font-weight: 600;">Subtheme Name</label>
                    <input type="text" class="form-control-dark" id="edit_subtheme_name" name="name" required style="color: #0f172a; border-color: #cbd5e1;">
                </div>

                <div style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" id="edit_subtheme_status" name="status" value="1" style="accent-color: #6366f1; width: 18px; height: 18px;">
                    <label for="edit_subtheme_status" style="color: #334155; font-size: 0.9rem; cursor: pointer; font-weight: 500;">Active Status</label>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" onclick="closeModal('editSubthemeModal')" class="btn-custom" style="background: transparent; border: 1px solid #cbd5e1; color: #64748b;">Cancel</button>
                    <button type="submit" class="btn-custom" style="background: linear-gradient(135deg, #6366f1, #4f46e5);">Update Subtheme</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript Handlers for Tabs, Modals & Realtime Filters -->
    <script>
        function switchTab(tabName) {
            const themesPanel = document.getElementById('tab-content-themes');
            const subthemesPanel = document.getElementById('tab-content-subthemes');
            const themesBtn = document.getElementById('tab-btn-themes');
            const subthemesBtn = document.getElementById('tab-btn-subthemes');

            if (tabName === 'themes') {
                themesPanel.style.display = 'block';
                subthemesPanel.style.display = 'none';

                themesBtn.style.background = 'linear-gradient(135deg, #0284c7, #0369a1)';
                themesBtn.style.color = '#ffffff';
                document.getElementById('badge-count-themes').style.background = 'rgba(255,255,255,0.25)';
                document.getElementById('badge-count-themes').style.color = '#ffffff';

                subthemesBtn.style.background = 'transparent';
                subthemesBtn.style.color = '#64748b';
                document.getElementById('badge-count-subthemes').style.background = 'rgba(99,102,241,0.12)';
                document.getElementById('badge-count-subthemes').style.color = '#4338ca';
                window.location.hash = 'themes';
            } else {
                themesPanel.style.display = 'none';
                subthemesPanel.style.display = 'block';

                subthemesBtn.style.background = 'linear-gradient(135deg, #6366f1, #4f46e5)';
                subthemesBtn.style.color = '#ffffff';
                document.getElementById('badge-count-subthemes').style.background = 'rgba(255,255,255,0.25)';
                document.getElementById('badge-count-subthemes').style.color = '#ffffff';

                themesBtn.style.background = 'transparent';
                themesBtn.style.color = '#64748b';
                document.getElementById('badge-count-themes').style.background = 'rgba(2,132,199,0.12)';
                document.getElementById('badge-count-themes').style.color = '#0284c7';
                window.location.hash = 'subthemes';
            }
        }

        function switchToSubthemesByTheme(themeId) {
            document.getElementById('subthemeParentFilter').value = themeId;
            switchTab('subthemes');
            filterSubthemesTable();
        }

        function filterThemesTable() {
            const query = document.getElementById('searchThemesInput').value.toLowerCase();
            const rows = document.querySelectorAll('#themesTable tbody tr');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        }

        function filterSubthemesTable() {
            const query = document.getElementById('searchSubthemesInput').value.toLowerCase();
            const selectedThemeId = document.getElementById('subthemeParentFilter').value;
            const rows = document.querySelectorAll('#subthemesTable tbody tr');

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                const rowThemeId = row.getAttribute('data-theme-id');

                const matchesQuery = text.includes(query);
                const matchesTheme = (!selectedThemeId || rowThemeId == selectedThemeId);

                row.style.display = (matchesQuery && matchesTheme) ? '' : 'none';
            });
        }

        function openAddThemeModal() {
            document.getElementById('addThemeModal').style.display = 'flex';
        }

        function openEditThemeModal(theme) {
            document.getElementById('edit_theme_name').value = theme.name || '';
            document.getElementById('edit_theme_status').checked = (theme.status == 1);
            document.getElementById('editThemeForm').action = '/admin/themes/' + theme.id;
            document.getElementById('editThemeModal').style.display = 'flex';
        }

        function openAddSubthemeModal() {
            document.getElementById('addSubthemeModal').style.display = 'flex';
        }

        function openEditSubthemeModal(subtheme) {
            document.getElementById('edit_subtheme_theme_id').value = subtheme.theme_id || '';
            document.getElementById('edit_subtheme_name').value = subtheme.name || '';
            document.getElementById('edit_subtheme_status').checked = (subtheme.status == 1);
            document.getElementById('editSubthemeForm').action = '/admin/subthemes/' + subtheme.id;
            document.getElementById('editSubthemeModal').style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Initialize tab based on URL hash if present
        document.addEventListener('DOMContentLoaded', function() {
            if (window.location.hash === '#subthemes') {
                switchTab('subthemes');
            }
        });
    </script>

@endsection
