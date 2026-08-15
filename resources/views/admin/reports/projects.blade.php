@extends('layouts.admin')

@section('title', 'All Projects Detail Report')

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">
    <!-- Page Header -->
    <div style="margin-bottom: 1.75rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.35rem;">
            <div style="width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(5, 150, 105, 0.15)); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);">
                <i class="bx bxs-briefcase"></i>
            </div>
            <div>
                <h2 style="color: var(--text-main); font-weight: 800; margin: 0; font-size: 1.6rem; letter-spacing: -0.02em; display: flex; align-items: center; gap: 0.6rem;">
                    All Projects Detail Report
                    <span style="background: rgba(16, 185, 129, 0.1); color: #059669; font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.65rem; border-radius: 20px; border: 1px solid rgba(16, 185, 129, 0.25);">
                        Construction, Water & General
                    </span>
                </h2>
                <p style="color: var(--text-muted); margin: 0.2rem 0 0 0; font-size: 0.88rem; font-weight: 500;">
                    Comprehensive analytics, progress stages, and financial metrics across non-social aid projects.
                </p>
            </div>
        </div>
    </div>

    <!-- Analytics Stat Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1.25rem; margin-bottom: 1.75rem;">
        <!-- Total Projects -->
        <div class="stat-widget-card" style="background: var(--panel-bg); border: 1px solid var(--panel-border); border-radius: 16px; padding: 1.25rem 1.4rem; box-shadow: 0 4px 18px rgba(0,0,0,0.03); transition: all 0.25s ease; position: relative; overflow: hidden;">
            <div style="position: absolute; right: -10px; top: -10px; width: 70px; height: 70px; background: rgba(99, 102, 241, 0.04); border-radius: 50%; pointer-events: none;"></div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem;">
                <span style="color: var(--text-muted); font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Total Projects</span>
                <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(99, 102, 241, 0.1); color: #6366f1; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                    <i class="bx bxs-folder"></i>
                </div>
            </div>
            <div style="display: flex; align-items: baseline; gap: 0.5rem;">
                <span style="color: var(--text-main); font-size: 1.75rem; font-weight: 800; letter-spacing: -0.02em;">{{ number_format($totalProjects) }}</span>
                <span style="color: var(--text-muted); font-size: 0.78rem; font-weight: 600;">records</span>
            </div>
        </div>

        <!-- Active Projects -->
        <div class="stat-widget-card" style="background: var(--panel-bg); border: 1px solid var(--panel-border); border-radius: 16px; padding: 1.25rem 1.4rem; box-shadow: 0 4px 18px rgba(0,0,0,0.03); transition: all 0.25s ease; position: relative; overflow: hidden;">
            <div style="position: absolute; right: -10px; top: -10px; width: 70px; height: 70px; background: rgba(16, 185, 129, 0.04); border-radius: 50%; pointer-events: none;"></div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem;">
                <span style="color: var(--text-muted); font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Active Projects</span>
                <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(16, 185, 129, 0.1); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                    <i class="bx bx-play-circle"></i>
                </div>
            </div>
            <div style="display: flex; align-items: baseline; gap: 0.5rem;">
                <span style="color: #10b981; font-size: 1.75rem; font-weight: 800; letter-spacing: -0.02em;">{{ number_format($activeProjects) }}</span>
                <span style="color: var(--text-muted); font-size: 0.78rem; font-weight: 600;">in progress</span>
            </div>
        </div>

        <!-- Completed -->
        <div class="stat-widget-card" style="background: var(--panel-bg); border: 1px solid var(--panel-border); border-radius: 16px; padding: 1.25rem 1.4rem; box-shadow: 0 4px 18px rgba(0,0,0,0.03); transition: all 0.25s ease; position: relative; overflow: hidden;">
            <div style="position: absolute; right: -10px; top: -10px; width: 70px; height: 70px; background: rgba(2, 132, 199, 0.04); border-radius: 50%; pointer-events: none;"></div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem;">
                <span style="color: var(--text-muted); font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Completed</span>
                <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(2, 132, 199, 0.1); color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                    <i class="bx bxs-check-shield"></i>
                </div>
            </div>
            <div style="display: flex; align-items: baseline; gap: 0.5rem;">
                <span style="color: #0284c7; font-size: 1.75rem; font-weight: 800; letter-spacing: -0.02em;">{{ number_format($completedProjects) }}</span>
                <span style="color: var(--text-muted); font-size: 0.78rem; font-weight: 600;">finished</span>
            </div>
        </div>
    </div>

    <!-- Filter Toolbar -->
    <div style="background: var(--panel-bg); border: 1px solid var(--panel-border); border-radius: 16px; padding: 1.35rem; margin-bottom: 1.75rem; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
        <form method="GET" action="{{ route('admin.reports.projects') }}" id="projectReportFilterForm">
            <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
                <!-- Search Field -->
                <div style="flex: 1.5; min-width: 230px;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 0.45rem;">Search Query</label>
                    <div style="position: relative;">
                        <i class="bx bx-search" style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 1.15rem;"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search ID, Project Name, Agency No, Donor..." class="form-control-dark" style="padding-left: 2.5rem; width: 100%; border-radius: 10px; height: 42px; font-size: 0.88rem; border: 1px solid var(--panel-border); background: rgba(148, 163, 184, 0.05); color: var(--text-main); font-weight: 500; transition: all 0.2s ease;">
                    </div>
                </div>

                <!-- Category Filter -->
                <div style="flex: 1.2; min-width: 190px;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 0.45rem;">Project Category</label>
                    <select name="category" class="form-control-dark" style="width: 100%; border-radius: 10px; height: 42px; font-size: 0.88rem; border: 1px solid var(--panel-border); background: rgba(148, 163, 184, 0.05); color: var(--text-main); font-weight: 500; cursor: pointer; transition: all 0.2s ease;" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        @foreach($categoriesList as $slug => $cName)
                            <option value="{{ $slug }}" {{ request('category') === $slug ? 'selected' : '' }}>{{ $cName }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div style="width: 150px;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 0.45rem;">Status</label>
                    <select name="status" class="form-control-dark" style="width: 100%; border-radius: 10px; height: 42px; font-size: 0.88rem; border: 1px solid var(--panel-border); background: rgba(148, 163, 184, 0.05); color: var(--text-main); font-weight: 500; cursor: pointer; transition: all 0.2s ease;" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="Suspended" {{ request('status') === 'Suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>

                <!-- Actions -->
                <div style="display: flex; gap: 0.6rem;">
                    <button type="submit" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; border: none; padding: 0 1.35rem; height: 42px; font-size: 0.88rem; border-radius: 10px; font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.25); transition: all 0.2s ease;">
                        <i class="bx bx-filter-alt" style="font-size: 1.1rem;"></i> Filter
                    </button>
                    <a href="{{ route('admin.reports.projects') }}" style="background: rgba(148, 163, 184, 0.12); color: var(--text-muted); border: 1px solid var(--panel-border); width: 42px; height: 42px; border-radius: 10px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.2s ease;" title="Reset Filters">
                        <i class="bx bx-refresh" style="font-size: 1.3rem;"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Project Data Table Container -->
    <div style="background: var(--panel-bg); border: 1px solid var(--panel-border); border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.04);">
        <!-- Table Header Toolbar -->
        <div style="padding: 1.1rem 1.4rem; border-bottom: 1px solid var(--panel-border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; background: rgba(148, 163, 184, 0.02);">
            <div style="display: flex; align-items: center; gap: 0.6rem;">
                <h4 style="margin: 0; color: var(--text-main); font-size: 1.05rem; font-weight: 800; letter-spacing: -0.01em;">
                    Project Records
                </h4>
                <span style="background: rgba(16, 185, 129, 0.1); color: #059669; font-weight: 700; font-size: 0.78rem; padding: 0.2rem 0.65rem; border-radius: 20px;">
                    {{ $allProjects->count() }} found
                </span>
            </div>

            <div style="display: flex; align-items: center; gap: 0.6rem;">
                <label style="color: var(--text-muted); font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; margin: 0;">Rows per page:</label>
                <select id="projectReportRowSelect" class="form-control-dark" style="width: 85px; height: 36px; padding: 0.2rem 0.5rem; font-size: 0.85rem; border-radius: 8px; font-weight: 700; border: 1px solid var(--panel-border); background: var(--panel-bg); color: var(--text-main); cursor: pointer;" onchange="updateProjectTableRows(this.value)">
                    <option value="10">10</option>
                    <option value="25" selected>25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="all">All</option>
                </select>
            </div>
        </div>

        <!-- Table View -->
        <div class="table-responsive">
            <table class="table" id="projectReportTable" style="width: 100%; margin-bottom: 0; border-collapse: separate; border-spacing: 0;">
                <thead>
                    <tr style="background: rgba(148, 163, 184, 0.06); border-bottom: 1px solid var(--panel-border); font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted); font-weight: 800; letter-spacing: 0.05em;">
                        <th style="padding: 1rem 1.1rem; text-align: center; width: 55px; border-bottom: 1px solid var(--panel-border);"># SL</th>
                        <th style="padding: 1rem 1.1rem; text-align: left; border-bottom: 1px solid var(--panel-border);">RCFI ID</th>
                        <th style="padding: 1rem 1.1rem; text-align: left; border-bottom: 1px solid var(--panel-border);">Project Name</th>
                        <th style="padding: 1rem 1.1rem; text-align: left; border-bottom: 1px solid var(--panel-border);">Category</th>
                        <th style="padding: 1rem 1.1rem; text-align: left; border-bottom: 1px solid var(--panel-border);">Agency No</th>
                        <th style="padding: 1rem 1.1rem; text-align: left; border-bottom: 1px solid var(--panel-border);">Sponsor / Donor</th>
                        <th style="padding: 1rem 1.1rem; text-align: center; white-space: nowrap; min-width: 140px; border-bottom: 1px solid var(--panel-border);">Status</th>
                        <th style="padding: 1rem 1.1rem; text-align: center; white-space: nowrap; min-width: 110px; border-bottom: 1px solid var(--panel-border);">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allProjects as $index => $row)
                        @php
                            $catSlug = $row['category_slug'] ?? '';
                            $isWater = str_contains($catSlug, 'water');
                            $isGeneral = ($catSlug === 'general');
                            
                            if ($isWater) {
                                $catBadgeBg = 'rgba(14, 165, 233, 0.1)';
                                $catBadgeColor = '#0284c7';
                                $catBadgeBorder = 'rgba(14, 165, 233, 0.25)';
                            } elseif ($isGeneral) {
                                $catBadgeBg = 'rgba(99, 102, 241, 0.1)';
                                $catBadgeColor = '#4f46e5';
                                $catBadgeBorder = 'rgba(99, 102, 241, 0.25)';
                            } else {
                                // Construction categories
                                $catBadgeBg = 'rgba(16, 185, 129, 0.1)';
                                $catBadgeColor = '#059669';
                                $catBadgeBorder = 'rgba(16, 185, 129, 0.25)';
                            }

                            $rawStatus = strtoupper(trim($row['status'] ?? ''));
                            if ($rawStatus === 'ACTIVE' || $rawStatus === 'RUNNING' || $rawStatus === 'IN PROGRESS') {
                                $stClass = 'status-badge-active';
                                $stDotColor = '#3b82f6';
                                $stText = 'Active';
                            } elseif ($rawStatus === 'COMPLETED') {
                                $stClass = 'status-badge-completed';
                                $stDotColor = '#10b981';
                                $stText = 'Completed';
                            } elseif ($rawStatus === 'PENDING' || $rawStatus === 'UNDER REVIEW') {
                                $stClass = 'status-badge-pending';
                                $stDotColor = '#f59e0b';
                                $stText = 'Pending';
                            } else {
                                $stClass = 'status-badge-suspended';
                                $stDotColor = '#ef4444';
                                $stText = ucfirst(strtolower($row['status'] ?: 'Pending'));
                            }
                        @endphp
                        <tr class="project-report-row" style="border-bottom: 1px solid var(--panel-border); font-size: 0.88rem; transition: all 0.15s ease;" onmouseover="this.style.background='rgba(16, 185, 129, 0.03)'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 1rem 1.1rem; text-align: center; color: var(--text-muted); font-weight: 600; border-bottom: 1px solid var(--panel-border);">
                                {{ $loop->iteration }}
                            </td>
                            <td style="padding: 1rem 1.1rem; text-align: left; font-weight: 700; border-bottom: 1px solid var(--panel-border);">
                                <a href="{{ $row['url'] }}" style="color: #10b981; text-decoration: none; font-weight: 700; font-family: monospace; font-size: 0.92rem; background: rgba(16, 185, 129, 0.08); padding: 0.25rem 0.55rem; border-radius: 6px; border: 1px solid rgba(16, 185, 129, 0.2); transition: all 0.15s ease; display: inline-block;" onmouseover="this.style.background='rgba(16, 185, 129, 0.15)'" onmouseout="this.style.background='rgba(16, 185, 129, 0.08)'">
                                    {{ $row['project_id_str'] }}
                                </a>
                            </td>
                            <td style="padding: 1rem 1.1rem; text-align: left; font-weight: 700; border-bottom: 1px solid var(--panel-border);">
                                <a href="{{ $row['url'] }}" style="color: var(--text-main); text-decoration: none; transition: color 0.15s ease;" onmouseover="this.style.color='#10b981'" onmouseout="this.style.color='var(--text-main)'">
                                    {{ $row['name'] }}
                                </a>
                            </td>
                            <td style="padding: 1rem 1.1rem; text-align: left; border-bottom: 1px solid var(--panel-border);">
                                <span style="background: {{ $catBadgeBg }}; color: {{ $catBadgeColor }}; border: 1px solid {{ $catBadgeBorder }}; padding: 0.25rem 0.65rem; border-radius: 8px; font-weight: 700; font-size: 0.78rem; display: inline-block; white-space: nowrap;">
                                    {{ $row['category'] }}
                                </span>
                            </td>
                            <td style="padding: 1rem 1.1rem; text-align: left; color: var(--text-muted); font-weight: 500; border-bottom: 1px solid var(--panel-border);">
                                {{ $row['agency_project_no'] }}
                            </td>
                            <td style="padding: 1rem 1.1rem; text-align: left; font-weight: 600; color: var(--text-main); border-bottom: 1px solid var(--panel-border);">
                                {{ $row['sponsor'] }}
                            </td>
                            <td style="padding: 1rem 1.1rem; text-align: center; white-space: nowrap; border-bottom: 1px solid var(--panel-border);">
                                <span class="report-status-pill {{ $stClass }}">
                                    <span class="status-dot" style="background: {{ $stDotColor }};"></span>
                                    {{ $stText }}
                                </span>
                            </td>
                            <td style="padding: 1rem 1.1rem; text-align: center; white-space: nowrap; border-bottom: 1px solid var(--panel-border);">
                                <div style="display: inline-flex; gap: 0.5rem; align-items: center; justify-content: center;">
                                    <a href="{{ route('admin.reports.single_project', [$row['id'], 'category' => $row['category_slug']]) }}" class="action-btn-report" title="Single Project Report">
                                        <i class="bx bxs-report"></i>
                                    </a>
                                    <a href="{{ $row['url'] }}" class="action-btn-view" title="View Project Details">
                                        <i class="bx bx-right-arrow-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding: 3.5rem 1rem; text-align: center; color: var(--text-muted);">
                                <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(148, 163, 184, 0.1); color: #94a3b8; display: inline-flex; align-items: center; justify-content: center; font-size: 2rem; margin-bottom: 1rem;">
                                    <i class="bx bx-folder-open"></i>
                                </div>
                                <h5 style="margin: 0 0 0.35rem 0; color: var(--text-main); font-weight: 700; font-size: 1.05rem;">No matching project records found</h5>
                                <p style="margin: 0; font-size: 0.88rem; color: var(--text-muted);">Try adjusting your search criteria or resetting filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Pagination Footer -->
        <div id="projectReportPaginationContainer" style="padding: 1rem 1.4rem; border-top: 1px solid var(--panel-border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; background: rgba(148, 163, 184, 0.02);">
            <div id="projectReportPaginationInfo" style="color: var(--text-muted); font-size: 0.85rem; font-weight: 500;">
                Showing <strong style="color: var(--text-main);">1</strong> to <strong style="color: var(--text-main);">{{ min(25, $allProjects->count()) }}</strong> of <strong style="color: var(--text-main);">{{ $allProjects->count() }}</strong> entries
            </div>
            <div id="projectReportPaginationNav" style="display: flex; align-items: center; gap: 0.35rem;">
                <!-- Dynamically populated -->
            </div>
        </div>
    </div>
</div>

<style>
    .stat-widget-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.06) !important;
    }

    /* Status Badges */
    .report-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.32rem 0.85rem;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.76rem;
        letter-spacing: 0.02em;
        white-space: nowrap;
        box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        transition: all 0.15s ease;
    }
    .report-status-pill .status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }
    .status-badge-completed {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }
    .status-badge-completed .status-dot {
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25);
    }
    .status-badge-active {
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
    }
    .status-badge-active .status-dot {
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.25);
    }
    .status-badge-pending {
        background: #fffbeb;
        color: #d97706;
        border: 1px solid #fde68a;
    }
    .status-badge-pending .status-dot {
        box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.25);
    }
    .status-badge-suspended {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }
    .status-badge-suspended .status-dot {
        box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.25);
    }

    /* Action Buttons (Unified Green Theme) */
    .action-btn-report {
        width: 36px;
        height: 36px;
        border-radius: 9px;
        background: rgba(16, 185, 129, 0.1) !important;
        color: #059669 !important;
        border: 1px solid rgba(16, 185, 129, 0.3) !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none !important;
        font-size: 1.2rem;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 1px 2px rgba(16, 185, 129, 0.08);
    }
    .action-btn-report i {
        color: #059669 !important;
        font-size: 1.15rem;
        transition: color 0.2s ease;
    }
    .action-btn-report:hover {
        background: #10b981 !important;
        border-color: #10b981 !important;
        color: #ffffff !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
    }
    .action-btn-report:hover i {
        color: #ffffff !important;
    }

    .action-btn-view {
        width: 36px;
        height: 36px;
        border-radius: 9px;
        background: linear-gradient(135deg, #10b981, #059669) !important;
        color: #ffffff !important;
        border: 1px solid #10b981 !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none !important;
        font-size: 1.25rem;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25);
    }
    .action-btn-view i {
        color: #ffffff !important;
        font-size: 1.25rem;
        transition: transform 0.2s ease;
    }
    .action-btn-view:hover {
        background: linear-gradient(135deg, #059669, #047857) !important;
        border-color: #047857 !important;
        color: #ffffff !important;
        transform: translateY(-2px);
        box-shadow: 0 5px 14px rgba(16, 185, 129, 0.45);
    }
    .action-btn-view:hover i {
        color: #ffffff !important;
        transform: translateX(2px);
    }
    .pagination-btn {
        min-width: 34px;
        height: 34px;
        padding: 0 0.55rem;
        border-radius: 8px;
        border: 1px solid var(--panel-border, #e2e8f0);
        background: var(--panel-bg, #ffffff);
        color: var(--text-main, #334155);
        font-size: 0.84rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
        user-select: none;
    }
    .pagination-btn:hover:not(.disabled):not(.active) {
        background: rgba(16, 185, 129, 0.08);
        border-color: rgba(16, 185, 129, 0.4);
        color: #059669;
    }
    .pagination-btn.active {
        background: linear-gradient(135deg, #10b981, #059669) !important;
        border-color: #10b981 !important;
        color: #ffffff !important;
        box-shadow: 0 3px 10px rgba(16, 185, 129, 0.3);
    }
    .pagination-btn.disabled {
        opacity: 0.38;
        cursor: not-allowed;
        background: transparent;
        pointer-events: none;
    }
    .pagination-ellipsis {
        padding: 0 0.35rem;
        color: var(--text-muted, #94a3b8);
        font-weight: 700;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
    }
</style>

<script>
    let currentProjectPage = 1;
    let currentProjectPageSize = 25;

    function handlePresetChange(select) {
        const fromContainer = document.getElementById('from_date_container');
        const toContainer = document.getElementById('to_date_container');
        if (select.value === 'custom') {
            fromContainer.style.display = 'block';
            toContainer.style.display = 'block';
        } else {
            fromContainer.style.display = 'none';
            toContainer.style.display = 'none';
            if (select.value !== '') {
                document.getElementById('projectReportFilterForm').submit();
            }
        }
    }

    function renderProjectPagination() {
        const allRows = Array.from(document.querySelectorAll('.project-report-row'));
        const totalRecords = allRows.length;
        const infoEl = document.getElementById('projectReportPaginationInfo');
        const navEl = document.getElementById('projectReportPaginationNav');
        const container = document.getElementById('projectReportPaginationContainer');

        if (totalRecords === 0) {
            if (container) container.style.display = 'none';
            return;
        }
        if (container) container.style.display = 'flex';

        const pageSize = (currentProjectPageSize === 'all') ? totalRecords : parseInt(currentProjectPageSize, 10);
        const totalPages = Math.max(1, Math.ceil(totalRecords / pageSize));

        if (currentProjectPage > totalPages) currentProjectPage = totalPages;
        if (currentProjectPage < 1) currentProjectPage = 1;

        const startIdx = (currentProjectPage - 1) * pageSize;
        const endIdx = Math.min(startIdx + pageSize, totalRecords);

        allRows.forEach((row, idx) => {
            if (idx >= startIdx && idx < endIdx) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        // Update Info Text
        if (infoEl) {
            infoEl.innerHTML = `Showing <strong style="color: var(--text-main);">${totalRecords > 0 ? startIdx + 1 : 0}</strong> to <strong style="color: var(--text-main);">${endIdx}</strong> of <strong style="color: var(--text-main);">${totalRecords}</strong> entries`;
        }

        // Update Nav Buttons
        if (navEl) {
            if (totalPages <= 1) {
                navEl.innerHTML = '';
                return;
            }

            let html = '';

            // Previous Button
            const prevDisabled = (currentProjectPage === 1);
            html += `<button type="button" class="pagination-btn ${prevDisabled ? 'disabled' : ''}" ${prevDisabled ? 'disabled' : `onclick="goToProjectPage(${currentProjectPage - 1})"`} title="Previous Page">
                <i class="bx bx-chevron-left" style="font-size: 1.15rem;"></i>
            </button>`;

            // Numeric Buttons with Ellipsis
            let startPage = Math.max(1, currentProjectPage - 2);
            let endPage = Math.min(totalPages, currentProjectPage + 2);

            if (startPage > 1) {
                html += `<button type="button" class="pagination-btn" onclick="goToProjectPage(1)">1</button>`;
                if (startPage > 2) {
                    html += `<span class="pagination-ellipsis">&hellip;</span>`;
                }
            }

            for (let p = startPage; p <= endPage; p++) {
                const isActive = (p === currentProjectPage);
                html += `<button type="button" class="pagination-btn ${isActive ? 'active' : ''}" onclick="goToProjectPage(${p})">${p}</button>`;
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    html += `<span class="pagination-ellipsis">&hellip;</span>`;
                }
                html += `<button type="button" class="pagination-btn" onclick="goToProjectPage(${totalPages})">${totalPages}</button>`;
            }

            // Next Button
            const nextDisabled = (currentProjectPage === totalPages);
            html += `<button type="button" class="pagination-btn ${nextDisabled ? 'disabled' : ''}" ${nextDisabled ? 'disabled' : `onclick="goToProjectPage(${currentProjectPage + 1})"`} title="Next Page">
                <i class="bx bx-chevron-right" style="font-size: 1.15rem;"></i>
            </button>`;

            navEl.innerHTML = html;
        }
    }

    function goToProjectPage(page) {
        currentProjectPage = page;
        renderProjectPagination();
        const tableContainer = document.getElementById('projectReportTable');
        if (tableContainer) {
            tableContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    function updateProjectTableRows(limit) {
        currentProjectPageSize = (limit === 'all') ? 'all' : parseInt(limit, 10);
        currentProjectPage = 1;
        renderProjectPagination();
    }

    // Initialize pagination on load
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('projectReportRowSelect');
        if (select) {
            currentProjectPageSize = (select.value === 'all') ? 'all' : parseInt(select.value, 10);
        }
        renderProjectPagination();
    });
</script>
@endsection
