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

        <!-- Suspended -->
        <div class="stat-widget-card" style="background: var(--panel-bg); border: 1px solid var(--panel-border); border-radius: 16px; padding: 1.25rem 1.4rem; box-shadow: 0 4px 18px rgba(0,0,0,0.03); transition: all 0.25s ease; position: relative; overflow: hidden;">
            <div style="position: absolute; right: -10px; top: -10px; width: 70px; height: 70px; background: rgba(239, 68, 68, 0.04); border-radius: 50%; pointer-events: none;"></div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem;">
                <span style="color: var(--text-muted); font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Suspended</span>
                <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(239, 68, 68, 0.1); color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                    <i class="bx bxs-error-circle"></i>
                </div>
            </div>
            <div style="display: flex; align-items: baseline; gap: 0.5rem;">
                <span style="color: #ef4444; font-size: 1.75rem; font-weight: 800; letter-spacing: -0.02em;">{{ number_format($suspendedProjects) }}</span>
                <span style="color: var(--text-muted); font-size: 0.78rem; font-weight: 600;">on hold</span>
            </div>
        </div>

        <!-- Total Transferred -->
        <div class="stat-widget-card" style="background: var(--panel-bg); border: 1px solid var(--panel-border); border-radius: 16px; padding: 1.25rem 1.4rem; box-shadow: 0 4px 18px rgba(0,0,0,0.03); transition: all 0.25s ease; position: relative; overflow: hidden;">
            <div style="position: absolute; right: -10px; top: -10px; width: 70px; height: 70px; background: rgba(16, 185, 129, 0.04); border-radius: 50%; pointer-events: none;"></div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem;">
                <span style="color: var(--text-muted); font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Total Transferred</span>
                <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(16, 185, 129, 0.1); color: #059669; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                    <i class="bx bx-rupee"></i>
                </div>
            </div>
            <div style="display: flex; align-items: baseline; gap: 0.35rem;">
                <span style="color: #059669; font-size: 1.65rem; font-weight: 800; letter-spacing: -0.02em;">₹{{ number_format($totalAmount, 2) }}</span>
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

                <!-- Date Preset -->
                <div style="width: 150px;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 0.45rem;">Date Preset</label>
                    <select name="date_preset" id="date_preset_select" class="form-control-dark" style="width: 100%; border-radius: 10px; height: 42px; font-size: 0.88rem; border: 1px solid var(--panel-border); background: rgba(148, 163, 184, 0.05); color: var(--text-main); font-weight: 500; cursor: pointer; transition: all 0.2s ease;" onchange="handlePresetChange(this)">
                        <option value="">All Time</option>
                        <option value="today" {{ request('date_preset') === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="this_week" {{ request('date_preset') === 'this_week' ? 'selected' : '' }}>This Week</option>
                        <option value="this_month" {{ request('date_preset') === 'this_month' ? 'selected' : '' }}>This Month</option>
                        <option value="this_year" {{ request('date_preset') === 'this_year' ? 'selected' : '' }}>This Year</option>
                        <option value="custom" {{ (request('date_preset') === 'custom' || (request('from_date') && request('to_date'))) ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>

                <!-- From Date -->
                <div id="from_date_container" style="width: 140px; display: {{ (request('date_preset') === 'custom' || (request('from_date') && request('to_date'))) ? 'block' : 'none' }};">
                    <label style="display: block; color: var(--text-muted); font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 0.45rem;">From Date</label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control-dark" style="width: 100%; border-radius: 10px; height: 42px; font-size: 0.88rem; border: 1px solid var(--panel-border); background: rgba(148, 163, 184, 0.05); color: var(--text-main);">
                </div>

                <!-- To Date -->
                <div id="to_date_container" style="width: 140px; display: {{ (request('date_preset') === 'custom' || (request('from_date') && request('to_date'))) ? 'block' : 'none' }};">
                    <label style="display: block; color: var(--text-muted); font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 0.45rem;">To Date</label>
                    <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control-dark" style="width: 100%; border-radius: 10px; height: 42px; font-size: 0.88rem; border: 1px solid var(--panel-border); background: rgba(148, 163, 184, 0.05); color: var(--text-main);">
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
                        <th style="padding: 1rem 1.1rem; text-align: center; border-bottom: 1px solid var(--panel-border);">Stage</th>
                        <th style="padding: 1rem 1.1rem; text-align: center; border-bottom: 1px solid var(--panel-border);">Status</th>
                        <th style="padding: 1rem 1.1rem; text-align: right; border-bottom: 1px solid var(--panel-border);">Total Amount</th>
                        <th style="padding: 1rem 1.1rem; text-align: center; border-bottom: 1px solid var(--panel-border);">Created Date</th>
                        <th style="padding: 1rem 1.1rem; text-align: center; width: 90px; border-bottom: 1px solid var(--panel-border);">Action</th>
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
                            <td style="padding: 1rem 1.1rem; text-align: center; border-bottom: 1px solid var(--panel-border);">
                                <span style="background: rgba(148, 163, 184, 0.12); color: #475569; border: 1px solid rgba(148, 163, 184, 0.25); padding: 0.2rem 0.55rem; border-radius: 6px; font-weight: 700; font-size: 0.78rem; display: inline-block;">
                                    Stage {{ $row['stage'] }}
                                </span>
                            </td>
                            <td style="padding: 1rem 1.1rem; text-align: center; border-bottom: 1px solid var(--panel-border);">
                                @if($row['status'] === 'Active')
                                    <span style="background: rgba(16, 185, 129, 0.12); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); padding: 0.25rem 0.75rem; border-radius: 20px; font-weight: 800; font-size: 0.74rem; text-transform: uppercase; letter-spacing: 0.03em; display: inline-block;">
                                        Active
                                    </span>
                                @elseif($row['status'] === 'Completed')
                                    <span style="background: rgba(2, 132, 199, 0.12); color: #0284c7; border: 1px solid rgba(2, 132, 199, 0.3); padding: 0.25rem 0.75rem; border-radius: 20px; font-weight: 800; font-size: 0.74rem; text-transform: uppercase; letter-spacing: 0.03em; display: inline-block;">
                                        Completed
                                    </span>
                                @else
                                    <span style="background: rgba(239, 68, 68, 0.12); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); padding: 0.25rem 0.75rem; border-radius: 20px; font-weight: 800; font-size: 0.74rem; text-transform: uppercase; letter-spacing: 0.03em; display: inline-block;">
                                        {{ $row['status'] }}
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 1rem 1.1rem; text-align: right; font-weight: 800; color: #059669; font-size: 0.95rem; border-bottom: 1px solid var(--panel-border);">
                                ₹{{ number_format($row['amount'], 2) }}
                            </td>
                            <td style="padding: 1rem 1.1rem; text-align: center; color: var(--text-muted); font-size: 0.84rem; font-weight: 500; border-bottom: 1px solid var(--panel-border);">
                                {{ $row['formatted_created_date'] }}
                            </td>
                            <td style="padding: 1rem 1.1rem; text-align: center; border-bottom: 1px solid var(--panel-border);">
                                <div style="display: inline-flex; gap: 0.4rem; align-items: center; justify-content: center;">
                                    <a href="{{ route('admin.reports.single_project', [$row['id'], 'category' => $row['category_slug']]) }}" style="width: 32px; height: 32px; border-radius: 8px; background: rgba(16, 185, 129, 0.1); color: #10b981; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; font-size: 1.1rem; transition: all 0.15s ease;" title="Single Project Report" onmouseover="this.style.background='#10b981'; this.style.color='#ffffff';" onmouseout="this.style.background='rgba(16, 185, 129, 0.1)'; this.style.color='#10b981';">
                                        <i class="bx bxs-file-doc"></i>
                                    </a>
                                    <a href="{{ $row['url'] }}" style="width: 32px; height: 32px; border-radius: 8px; background: rgba(99, 102, 241, 0.1); color: #6366f1; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; font-size: 1.1rem; transition: all 0.15s ease;" title="View Project Details" onmouseover="this.style.background='#6366f1'; this.style.color='#ffffff';" onmouseout="this.style.background='rgba(99, 102, 241, 0.1)'; this.style.color='#6366f1';">
                                        <i class="bx bx-right-arrow-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" style="padding: 3.5rem 1rem; text-align: center; color: var(--text-muted);">
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
    </div>
</div>

<style>
    .stat-widget-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.06) !important;
    }
</style>

<script>
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

    function updateProjectTableRows(limit) {
        const rows = document.querySelectorAll('.project-report-row');
        if (limit === 'all') {
            rows.forEach(r => r.style.display = '');
        } else {
            const count = parseInt(limit, 10);
            rows.forEach((r, idx) => {
                r.style.display = (idx < count) ? '' : 'none';
            });
        }
    }

    // Initialize default row limit
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('projectReportRowSelect');
        if (select) {
            updateProjectTableRows(select.value);
        }
    });
</script>
@endsection
