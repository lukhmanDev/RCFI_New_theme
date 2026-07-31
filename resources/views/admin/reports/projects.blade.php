@extends('layouts.admin')

@section('title', 'All Projects Detail Report')

@section('content')
<div class="container-fluid" style="padding: 1.5rem;">
    <!-- Page Title & Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="color: var(--text-main); font-weight: 700; margin: 0; font-size: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="bx bxs-briefcase" style="color: #10b981;"></i> All Projects Detail Report
            </h2>
            <p style="color: var(--text-muted); margin: 0.25rem 0 0 0; font-size: 0.88rem;">
                Detailed report and summary metrics for all projects across all categories
            </p>
        </div>
        <div>
            <a href="{{ route('admin.reports.projects', array_merge(request()->query(), ['export' => 'csv'])) }}" class="btn-custom" style="background: linear-gradient(135deg, #10b981, #059669); color: #ffffff; border: none; padding: 0.65rem 1.25rem; font-size: 0.88rem; border-radius: 10px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);">
                <i class="bx bx-download" style="font-size: 1.1rem;"></i> Export Report (CSV)
            </a>
        </div>
    </div>

    <!-- Metrics Cards Row -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
        <div style="background: var(--panel-bg); border: 1px solid var(--panel-border); border-radius: 12px; padding: 1.25rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 1rem;">
            <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(16, 185, 129, 0.1); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; font-weight: 700;">
                <i class="bx bxs-folder-open"></i>
            </div>
            <div>
                <span style="display: block; color: var(--text-muted); font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Total Projects</span>
                <span style="display: block; color: var(--text-main); font-size: 1.5rem; font-weight: 700; margin-top: 0.15rem;">{{ number_format($totalProjects) }}</span>
            </div>
        </div>

        <div style="background: var(--panel-bg); border: 1px solid var(--panel-border); border-radius: 12px; padding: 1.25rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 1rem;">
            <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(16, 185, 129, 0.1); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; font-weight: 700;">
                <i class="bx bx-play-circle"></i>
            </div>
            <div>
                <span style="display: block; color: var(--text-muted); font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Active Projects</span>
                <span style="display: block; color: #10b981; font-size: 1.5rem; font-weight: 700; margin-top: 0.15rem;">{{ number_format($activeProjects) }}</span>
            </div>
        </div>

        <div style="background: var(--panel-bg); border: 1px solid var(--panel-border); border-radius: 12px; padding: 1.25rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 1rem;">
            <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(16, 185, 129, 0.1); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; font-weight: 700;">
                <i class="bx bxs-check-circle"></i>
            </div>
            <div>
                <span style="display: block; color: var(--text-muted); font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Completed</span>
                <span style="display: block; color: #10b981; font-size: 1.5rem; font-weight: 700; margin-top: 0.15rem;">{{ number_format($completedProjects) }}</span>
            </div>
        </div>

        <div style="background: var(--panel-bg); border: 1px solid var(--panel-border); border-radius: 12px; padding: 1.25rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 1rem;">
            <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(239, 68, 68, 0.1); color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; font-weight: 700;">
                <i class="bx bxs-error-circle"></i>
            </div>
            <div>
                <span style="display: block; color: var(--text-muted); font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Suspended</span>
                <span style="display: block; color: #ef4444; font-size: 1.5rem; font-weight: 700; margin-top: 0.15rem;">{{ number_format($suspendedProjects) }}</span>
            </div>
        </div>

        <div style="background: var(--panel-bg); border: 1px solid var(--panel-border); border-radius: 12px; padding: 1.25rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 1rem;">
            <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(16, 185, 129, 0.1); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; font-weight: 700;">
                <i class="bx bx-rupee"></i>
            </div>
            <div>
                <span style="display: block; color: var(--text-muted); font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">Total Transferred</span>
                <span style="display: block; color: #10b981; font-size: 1.5rem; font-weight: 700; margin-top: 0.15rem;">₹{{ number_format($totalAmount, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div style="background: var(--panel-bg); border: 1px solid var(--panel-border); border-radius: 12px; padding: 1.25rem; margin-bottom: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <form method="GET" action="{{ route('admin.reports.projects') }}" id="projectReportFilterForm">
            <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
                <!-- Search Box -->
                <div style="flex: 1; min-width: 220px;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem;">Search</label>
                    <div style="position: relative;">
                        <i class="bx bx-search" style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search ID, Name, Agency No, Sponsor..." class="form-control-dark" style="padding-left: 2.2rem; width: 100%; border-radius: 8px; height: 38px; font-size: 0.85rem;">
                    </div>
                </div>

                <!-- Category Filter -->
                <div style="flex: 1; min-width: 180px;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem;">Project Category</label>
                    <select name="category" class="form-control-dark" style="width: 100%; border-radius: 8px; height: 38px; font-size: 0.85rem;" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        @foreach($categoriesList as $slug => $cName)
                            <option value="{{ $slug }}" {{ request('category') === $slug ? 'selected' : '' }}>{{ $cName }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div style="width: 150px;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem;">Status</label>
                    <select name="status" class="form-control-dark" style="width: 100%; border-radius: 8px; height: 38px; font-size: 0.85rem;" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="Suspended" {{ request('status') === 'Suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>

                <!-- Date Filter Presets -->
                <div style="width: 150px;">
                    <label style="display: block; color: var(--text-muted); font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem;">Date Preset</label>
                    <select name="date_preset" id="date_preset_select" class="form-control-dark" style="width: 100%; border-radius: 8px; height: 38px; font-size: 0.85rem;" onchange="handlePresetChange(this)">
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
                    <label style="display: block; color: var(--text-muted); font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem;">From Date</label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control-dark" style="width: 100%; border-radius: 8px; height: 38px; font-size: 0.85rem;">
                </div>

                <!-- To Date -->
                <div id="to_date_container" style="width: 140px; display: {{ (request('date_preset') === 'custom' || (request('from_date') && request('to_date'))) ? 'block' : 'none' }};">
                    <label style="display: block; color: var(--text-muted); font-size: 0.8rem; font-weight: 600; margin-bottom: 0.4rem;">To Date</label>
                    <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control-dark" style="width: 100%; border-radius: 8px; height: 38px; font-size: 0.85rem;">
                </div>

                <!-- Submit & Reset Buttons -->
                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn-custom" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff; border: none; padding: 0 1.25rem; height: 38px; font-size: 0.85rem; border-radius: 8px; font-weight: 600;">
                        Filter
                    </button>
                    <a href="{{ route('admin.reports.projects') }}" class="btn-custom" style="background: rgba(148, 163, 184, 0.15); color: var(--text-muted); border: 1px solid var(--panel-border); padding: 0 0.85rem; height: 38px; font-size: 0.85rem; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;" title="Reset Filters">
                        <i class="bx bx-reset" style="font-size: 1.1rem;"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Data Table Container -->
    <div style="background: var(--panel-bg); border: 1px solid var(--panel-border); border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--panel-border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <h4 style="margin: 0; color: var(--text-main); font-size: 1rem; font-weight: 700;">
                Project Records <span style="color: var(--text-muted); font-weight: 500; font-size: 0.85rem;">({{ $allProjects->count() }} found)</span>
            </h4>

            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <label style="color: var(--text-muted); font-size: 0.8rem; font-weight: 600;">Rows per page:</label>
                <select id="projectReportRowSelect" class="form-control-dark" style="width: 85px; height: 36px; padding: 0.2rem 0.5rem; font-size: 0.85rem; border-radius: 6px; font-weight: 600; cursor: pointer;" onchange="updateProjectTableRows(this.value)">
                    <option value="10">10</option>
                    <option value="25" selected>25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="all">All</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table" id="projectReportTable" style="width: 100%; margin-bottom: 0; border-collapse: collapse;">
                <thead>
                    <tr style="background: rgba(148, 163, 184, 0.05); border-bottom: 1px solid var(--panel-border); font-size: 0.82rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700;">
                        <th style="padding: 0.85rem 1rem; text-align: center; width: 50px;"># SL</th>
                        <th style="padding: 0.85rem 1rem; text-align: left;">Project ID</th>
                        <th style="padding: 0.85rem 1rem; text-align: left;">Project Name</th>
                        <th style="padding: 0.85rem 1rem; text-align: left;">Category</th>
                        <th style="padding: 0.85rem 1rem; text-align: left;">Agency No</th>
                        <th style="padding: 0.85rem 1rem; text-align: left;">Sponsor / Donor</th>
                        <th style="padding: 0.85rem 1rem; text-align: center;">Stage</th>
                        <th style="padding: 0.85rem 1rem; text-align: center;">Status</th>
                        <th style="padding: 0.85rem 1rem; text-align: right;">Total Amount</th>
                        <th style="padding: 0.85rem 1rem; text-align: center;">Created Date</th>
                        <th style="padding: 0.85rem 1rem; text-align: center; width: 80px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allProjects as $index => $row)
                        <tr class="project-report-row" style="border-bottom: 1px solid var(--panel-border); font-size: 0.88rem; transition: background 0.15s ease;" onmouseover="this.style.background='rgba(148, 163, 184, 0.04)'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 0.85rem 1rem; text-align: center; color: var(--text-muted); font-weight: 600;">{{ $loop->iteration }}</td>
                            <td style="padding: 0.85rem 1rem; text-align: left; font-weight: 700;">
                                <a href="{{ $row['url'] }}" style="color: #10b981; text-decoration: none; transition: opacity 0.15s ease;" onmouseover="this.style.opacity='0.75'" onmouseout="this.style.opacity='1'">
                                    {{ $row['project_id_str'] }}
                                </a>
                            </td>
                            <td style="padding: 0.85rem 1rem; text-align: left; font-weight: 600;">
                                <a href="{{ $row['url'] }}" style="color: var(--text-main); text-decoration: none; transition: color 0.15s ease;" onmouseover="this.style.color='#10b981'" onmouseout="this.style.color='var(--text-main)'">
                                    {{ $row['name'] }}
                                </a>
                            </td>
                            <td style="padding: 0.85rem 1rem; text-align: left;">
                                <span style="background: rgba(16, 185, 129, 0.1); color: #059669; padding: 0.2rem 0.6rem; border-radius: 6px; font-weight: 600; font-size: 0.78rem;">
                                    {{ $row['category'] }}
                                </span>
                            </td>
                            <td style="padding: 0.85rem 1rem; text-align: left; color: var(--text-muted);">
                                {{ $row['agency_project_no'] }}
                            </td>
                            <td style="padding: 0.85rem 1rem; text-align: left; font-weight: 500; color: var(--text-main);">
                                {{ $row['sponsor'] }}
                            </td>
                            <td style="padding: 0.85rem 1rem; text-align: center;">
                                <span style="background: rgba(16, 185, 129, 0.1); color: #059669; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 700; font-size: 0.78rem;">
                                    Stage {{ $row['stage'] }}
                                </span>
                            </td>
                            <td style="padding: 0.85rem 1rem; text-align: center;">
                                @if($row['status'] === 'Active')
                                    <span style="background: rgba(16, 185, 129, 0.12); color: #10b981; padding: 0.25rem 0.65rem; border-radius: 20px; font-weight: 700; font-size: 0.75rem; text-transform: uppercase;">
                                        Active
                                    </span>
                                @elseif($row['status'] === 'Completed')
                                    <span style="background: rgba(2, 132, 199, 0.12); color: #0284c7; padding: 0.25rem 0.65rem; border-radius: 20px; font-weight: 700; font-size: 0.75rem; text-transform: uppercase;">
                                        Completed
                                    </span>
                                @else
                                    <span style="background: rgba(239, 68, 68, 0.12); color: #ef4444; padding: 0.25rem 0.65rem; border-radius: 20px; font-weight: 700; font-size: 0.75rem; text-transform: uppercase;">
                                        {{ $row['status'] }}
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 0.85rem 1rem; text-align: right; font-weight: 700; color: #10b981;">
                                ₹{{ number_format($row['amount'], 2) }}
                            </td>
                            <td style="padding: 0.85rem 1rem; text-align: center; color: var(--text-muted); font-size: 0.82rem;">
                                {{ $row['formatted_created_date'] }}
                            </td>
                            <td style="padding: 0.85rem 1rem; text-align: center;">
                                <div style="display: inline-flex; gap: 0.5rem; align-items: center; justify-content: center;">
                                    <a href="{{ route('admin.reports.single_project', [$row['id'], 'category' => $row['category_slug']]) }}" style="color: #10b981; text-decoration: none; font-size: 1.15rem;" title="Single Project Report">
                                        <i class="bx bxs-file-doc"></i>
                                    </a>
                                    <a href="{{ $row['url'] }}" style="color: #10b981; text-decoration: none; font-size: 1.15rem;" title="View Project Details">
                                        <i class="bx bx-right-arrow-circle"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" style="padding: 2.5rem; text-align: center; color: var(--text-muted); font-style: italic;">
                                <i class="bx bx-folder-open" style="font-size: 2rem; display: block; margin-bottom: 0.5rem; color: #94a3b8;"></i>
                                No matching project records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

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
