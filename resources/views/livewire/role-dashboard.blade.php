<div class="role-dashboard-container">

    <style>
        .role-dashboard-container {
            display: flex;
            flex-direction: column;
            gap: 1.75rem;
            font-family: inherit;
        }

        /* Hero Welcome Header */
        .hero-welcome-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 1.75rem 2rem;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.03);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1.25rem;
            position: relative;
            overflow: hidden;
        }
        .hero-welcome-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
            background: linear-gradient(180deg, #10b981 0%, #0ea5e9 100%);
            border-top-left-radius: 20px;
            border-bottom-left-radius: 20px;
        }
        .hero-welcome-title {
            color: #0f172a;
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .hero-welcome-subtitle {
            color: #64748b;
            font-size: 0.9rem;
            margin-top: 0.35rem;
            margin-bottom: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .role-badge {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
            padding: 0.2rem 0.65rem;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 0.78rem;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        /* Top Grid - Hero Impact Cards (Gradient) */
        .hero-impact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.35rem;
        }
        .impact-card {
            border-radius: 20px;
            padding: 1.75rem;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 160px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: #ffffff;
        }
        .impact-card:hover {
            transform: translateY(-4px);
        }
        .impact-card-peoples {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 60%, #075985 100%);
            box-shadow: 0 10px 25px -5px rgba(2, 132, 199, 0.35), 0 8px 10px -6px rgba(2, 132, 199, 0.2);
        }
        .impact-card-families {
            background: linear-gradient(135deg, #059669 0%, #047857 60%, #065f46 100%);
            box-shadow: 0 10px 25px -5px rgba(5, 150, 105, 0.35), 0 8px 10px -6px rgba(5, 150, 105, 0.2);
        }
        .impact-card-projects {
            background: linear-gradient(135deg, #d97706 0%, #b45309 60%, #92400e 100%);
            box-shadow: 0 10px 25px -5px rgba(217, 119, 6, 0.35), 0 8px 10px -6px rgba(217, 119, 6, 0.2);
        }
        .impact-card .deco-circle-1 {
            position: absolute;
            right: -20px;
            top: -20px;
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            pointer-events: none;
        }
        .impact-card .deco-circle-2 {
            position: absolute;
            right: 40px;
            bottom: -30px;
            width: 90px;
            height: 90px;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 50%;
            pointer-events: none;
        }
        .impact-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            position: relative;
            z-index: 1;
        }
        .impact-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.25);
        }
        .impact-pill {
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(8px);
            padding: 0.25rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }
        .impact-body {
            position: relative;
            z-index: 1;
            margin-top: 1rem;
        }
        .impact-label {
            font-size: 0.82rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: rgba(255, 255, 255, 0.9);
            display: block;
        }
        .impact-value {
            font-size: 2.35rem;
            font-weight: 900;
            line-height: 1.1;
            margin: 0.25rem 0;
            color: #ffffff;
            letter-spacing: -0.02em;
        }
        .impact-footer {
            font-size: 0.78rem;
            color: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            gap: 0.35rem;
            font-weight: 500;
        }

        /* Pipeline Stats Grid */
        .pipeline-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.25rem;
        }
        .stat-card-modern {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 1.35rem;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.02);
            display: flex;
            align-items: center;
            gap: 1.15rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        .stat-card-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -4px rgba(15, 23, 42, 0.06);
            border-color: #cbd5e1;
        }
        .stat-icon-modern {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.45rem;
            flex-shrink: 0;
            transition: transform 0.25s ease;
        }
        .stat-card-modern:hover .stat-icon-modern {
            transform: scale(1.08);
        }
        .stat-icon-blue { background: #eff6ff; color: #2563eb; }
        .stat-icon-orange { background: #fff7ed; color: #ea580c; }
        .stat-icon-emerald { background: #ecfdf5; color: #059669; }
        .stat-icon-purple { background: #f5f3ff; color: #7c3aed; }

        .stat-info {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            min-width: 0;
        }
        .stat-label-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
        }
        .stat-label {
            color: #64748b;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .stat-badge-new {
            background: #ef4444;
            color: #ffffff;
            font-size: 0.7rem;
            font-weight: 800;
            padding: 0.12rem 0.5rem;
            border-radius: 9999px;
            box-shadow: 0 2px 6px rgba(239, 68, 68, 0.3);
            animation: pulse-soft 2s infinite;
        }
        @keyframes pulse-soft {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .stat-number {
            color: #0f172a;
            font-size: 1.75rem;
            font-weight: 900;
            line-height: 1.2;
            margin: 0.15rem 0;
            letter-spacing: -0.02em;
        }
        .stat-subtext {
            font-size: 0.75rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        .text-blue { color: #2563eb; }
        .text-orange { color: #ea580c; }
        .text-emerald { color: #059669; }
        .text-purple { color: #7c3aed; }

        /* Main Analytics Section (Chart & Quick Breakdown) */
        .analytics-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 1.75rem 2rem;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.02);
        }
        .card-header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .card-title {
            color: #0f172a;
            font-size: 1.15rem;
            font-weight: 800;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .card-subtitle {
            color: #64748b;
            font-size: 0.84rem;
            margin: 0.25rem 0 0;
        }
        .chart-legend-custom {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 0.45rem 0.9rem;
            border-radius: 12px;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.8rem;
            font-weight: 700;
        }
        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 3px;
        }
        .chart-container-wrap {
            position: relative;
            height: 310px;
            width: 100%;
        }

        /* Modern Table Styling */
        .modern-table-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 1.75rem 2rem;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.03);
        }
        .table-custom-modern {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.88rem;
        }
        .table-custom-modern th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.74rem;
            letter-spacing: 0.05em;
            padding: 0.95rem 1.1rem;
            border-top: 1px solid #e2e8f0;
            border-bottom: 2px solid #e2e8f0;
        }
        .table-custom-modern th:first-child { border-top-left-radius: 12px; }
        .table-custom-modern th:last-child { border-top-right-radius: 12px; }
        .table-custom-modern td {
            padding: 1rem 1.1rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            transition: background 0.15s ease;
        }
        .table-custom-modern tr:hover td {
            background: #f8fafc;
        }
        .table-custom-modern tr:last-child td {
            border-bottom: none;
        }
        .rank-badge {
            width: 26px;
            height: 26px;
            border-radius: 8px;
            background: #f1f5f9;
            color: #475569;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.78rem;
            font-weight: 800;
        }
        .badge-pill-soft {
            padding: 0.25rem 0.65rem;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 0.78rem;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        /* Responsiveness */
        @media (max-width: 768px) {
            .hero-welcome-card { padding: 1.25rem; }
            .hero-welcome-title { font-size: 1.4rem; }
            .analytics-card { padding: 1.25rem; }
            .modern-table-card { padding: 1.25rem; }
            .chart-container-wrap { height: 260px; }
        }
    </style>

    <!-- 1. Hero Welcome Card -->
    <div class="hero-welcome-card">
        <div>
            <h1 class="hero-welcome-title">
                Welcome back, {{ $user->name }}
            </h1>
            <div class="hero-welcome-subtitle">
                <span>Role assigned:</span>
                <span class="role-badge">
                    <i class="bx bxs-shield-alt-2"></i> {{ $user->role_name }}
                </span>
                <span style="color: #cbd5e1;">•</span>
                <span>Real-time impact & operational metrics overview</span>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 0.65rem;">
            @if(Auth::user()->canAddApplications())
                <a href="{{ route('applications.index') }}" style="display: inline-flex; align-items: center; gap: 0.4rem; background: #ffffff; color: #0f172a; border: 1px solid #cbd5e1; padding: 0.55rem 1rem; border-radius: 10px; font-size: 0.84rem; font-weight: 700; text-decoration: none; box-shadow: 0 2px 6px rgba(0,0,0,0.03); transition: all 0.2s ease;">
                    <i class="bx bx-file" style="color: #2563eb;"></i> Applications
                </a>
            @endif
            @if(Auth::user()->canAddEditProjects())
                <a href="{{ route('projects.index') }}" style="display: inline-flex; align-items: center; gap: 0.4rem; background: #10b981; color: #ffffff; border: 1px solid #10b981; padding: 0.55rem 1.15rem; border-radius: 10px; font-size: 0.84rem; font-weight: 700; text-decoration: none; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25); transition: all 0.2s ease;">
                    <i class="bx bx-briefcase-alt-2"></i> All Projects
                </a>
            @endif
        </div>
    </div>

    <!-- 2. Primary Impact Highlight Cards (Gradient Hero Stats) -->
    <div class="hero-impact-grid">

        <!-- Total Benefited Peoples -->
        <div class="impact-card impact-card-peoples">
            <div class="deco-circle-1"></div>
            <div class="deco-circle-2"></div>
            <div class="impact-header">
                <div class="impact-icon-wrapper">
                    <i class="bx bxs-group"></i>
                </div>
                <div class="impact-pill">Impact Metric</div>
            </div>
            <div class="impact-body">
                <span class="impact-label">Total Benefited Peoples</span>
                <div class="impact-value">{{ number_format($totalBeneficiaryPeoples) }}</div>
                <div class="impact-footer">
                    <i class="bx bx-check-double"></i> Verified beneficiaries across all project categories
                </div>
            </div>
        </div>

        <!-- Total Benefited Families -->
        <div class="impact-card impact-card-families">
            <div class="deco-circle-1"></div>
            <div class="deco-circle-2"></div>
            <div class="impact-header">
                <div class="impact-icon-wrapper">
                    <i class="bx bxs-home-heart"></i>
                </div>
                <div class="impact-pill">Impact Metric</div>
            </div>
            <div class="impact-body">
                <span class="impact-label">Total Benefited Families</span>
                <div class="impact-value">{{ number_format($totalBeneficiaryFamily) }}</div>
                <div class="impact-footer">
                    <i class="bx bx-check-double"></i> Supported families across active and completed schemes
                </div>
            </div>
        </div>

        <!-- Completed Projects -->
        <div class="impact-card impact-card-projects">
            <div class="deco-circle-1"></div>
            <div class="deco-circle-2"></div>
            <div class="impact-header">
                <div class="impact-icon-wrapper">
                    <i class="bx bxs-badge-check"></i>
                </div>
                <div class="impact-pill">Delivered</div>
            </div>
            <div class="impact-body">
                <span class="impact-label">Completed Projects</span>
                <div class="impact-value">{{ number_format($completedProjects) }}</div>
                <div class="impact-footer">
                    <i class="bx bx-trophy"></i> Successfully finished and delivered initiatives
                </div>
            </div>
        </div>

    </div>

    <!-- 3. Operational Pipeline Cards (Refined Modern Cards) -->
    <div class="pipeline-stats-grid">

        <!-- Applications Submissions -->
        <div class="stat-card-modern">
            <div class="stat-icon-modern stat-icon-blue">
                <i class="bx bx-file"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label-wrap">
                    <span class="stat-label">Applications</span>
                    @if($newApplicationsCount > 0)
                        <span class="stat-badge-new">+{{ $newApplicationsCount }} New</span>
                    @endif
                </div>
                <div class="stat-number">{{ number_format($totalApplications) }}</div>
                <div class="stat-subtext text-blue">
                    <i class="bx bx-layer"></i> Total Submissions
                </div>
            </div>
        </div>

        <!-- Pending Review -->
        <div class="stat-card-modern">
            <div class="stat-icon-modern stat-icon-orange">
                <i class="bx bx-time-five"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label-wrap">
                    <span class="stat-label">Pending Review</span>
                </div>
                <div class="stat-number">{{ number_format($pendingCount) }}</div>
                <div class="stat-subtext text-orange">
                    <i class="bx bx-loader-alt"></i> Awaiting Action
                </div>
            </div>
        </div>

        <!-- Approved Applications -->
        <div class="stat-card-modern">
            <div class="stat-icon-modern stat-icon-emerald">
                <i class="bx bx-check-circle"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label-wrap">
                    <span class="stat-label">Approved</span>
                </div>
                <div class="stat-number">{{ number_format($approvedCount) }}</div>
                <div class="stat-subtext text-emerald">
                    <i class="bx bx-badge-check"></i> Verified Success
                </div>
            </div>
        </div>

        <!-- Running Projects -->
        <div class="stat-card-modern">
            <div class="stat-icon-modern stat-icon-purple">
                <i class="bx bx-folder-open"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label-wrap">
                    <span class="stat-label">Running Projects</span>
                </div>
                <div class="stat-number">{{ number_format($runningProjects) }}</div>
                <div class="stat-subtext text-purple">
                    <i class="bx bx-pulse"></i> Active Pipeline
                </div>
            </div>
        </div>

    </div>

    <!-- 4. Year-wise Impact Overview (Visual Chart Card) -->
    <div class="analytics-card">
        <div class="card-header-flex">
            <div>
                <h3 class="card-title">
                    <i class="bx bx-bar-chart-alt-2" style="color: #0284c7; font-size: 1.35rem;"></i>
                    Year-wise Impact Overview
                </h3>
                <p class="card-subtitle">Comprehensive distribution of benefited individuals and families by year</p>
            </div>
            <div class="chart-legend-custom">
                <div class="legend-item" style="color: #0284c7;">
                    <span class="legend-dot" style="background: #0ea5e9;"></span>
                    Benefited Peoples
                </div>
                <div class="legend-item" style="color: #059669;">
                    <span class="legend-dot" style="background: #10b981;"></span>
                    Benefited Families
                </div>
            </div>
        </div>

        <div class="chart-container-wrap">
            <canvas id="beneficiaryYearChart"></canvas>
        </div>
    </div>

    <!-- 5. Theme-Wise Performance & Impact Breakdown -->
    <div class="modern-table-card">
        <div class="card-header-flex">
            <div>
                <h3 class="card-title">
                    <i class="bx bx-category-alt" style="color: #7c3aed; font-size: 1.35rem;"></i>
                    Theme-Wise Overview & Impact Breakdown
                </h3>
                <p class="card-subtitle">
                    Real-time project distribution, execution stages, and beneficiary reach categorized by development themes
                </p>
            </div>
            <a href="{{ route('themes.index') }}" style="display: inline-flex; align-items: center; gap: 0.4rem; background: #f8fafc; color: #334155; border: 1px solid #cbd5e1; padding: 0.5rem 0.95rem; border-radius: 10px; font-size: 0.82rem; font-weight: 700; text-decoration: none; transition: all 0.2s ease;">
                <i class="bx bx-slider-alt" style="color: #64748b;"></i> Manage Themes & Subthemes
            </a>
        </div>

        <div style="overflow-x: auto;">
            <table class="table-custom-modern">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">#</th>
                        <th style="text-align: left;">Theme Name</th>
                        <th style="text-align: center;">Subthemes</th>
                        <th style="text-align: center;">Total Projects</th>
                        <th style="text-align: center;">Running</th>
                        <th style="text-align: center;">Completed</th>
                        <th style="text-align: right;">Beneficiary Impact</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($themeSummaryData as $index => $item)
                        <tr>
                            <td style="text-align: center;">
                                <span class="rank-badge">{{ $index + 1 }}</span>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: #0f172a; font-size: 0.92rem;">
                                    {{ $item['name'] }}
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge-pill-soft" style="background: #f1f5f9; color: #475569;" title="{{ implode(', ', $item['subthemes_list']) }}">
                                    <i class="bx bx-list-ul"></i> {{ $item['subthemes_count'] }} Subthemes
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <span style="font-weight: 800; color: #0f172a; font-size: 0.95rem;">
                                    {{ number_format($item['total_projects']) }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge-pill-soft" style="background: #f5f3ff; color: #7c3aed;">
                                    <i class="bx bx-loader-circle"></i> {{ number_format($item['running_projects']) }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge-pill-soft" style="background: #ecfdf5; color: #059669;">
                                    <i class="bx bx-check"></i> {{ number_format($item['completed_projects']) }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="font-weight: 800; color: #0284c7; font-size: 0.92rem;">
                                    {{ number_format($item['benefited_peoples']) }} <span style="font-weight: 600; font-size: 0.74rem; color: #64748b;">peoples</span>
                                </div>
                                <div style="font-size: 0.76rem; color: #059669; font-weight: 700; margin-top: 0.1rem;">
                                    {{ number_format($item['benefited_families']) }} <span style="font-weight: 600; color: #64748b;">families</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding: 2.5rem; text-align: center; color: #94a3b8;">
                                <i class="bx bx-folder-open" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                                No theme data available at this time.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Request Leave Modal Dialog Component -->
    @include('partials.leave_request_modal')

    <!-- Chart.js Beneficiary Year Chart (Enhanced Rendering) -->
    <script>
    (function() {
        var labels   = @json($beneficiaryChartData['labels']);
        var peoples  = @json($beneficiaryChartData['peoples']);
        var families = @json($beneficiaryChartData['families']);

        function initBeneficiaryChart() {
            var canvas = document.getElementById('beneficiaryYearChart');
            if (!canvas) return;
            var ctx = canvas.getContext('2d');
            if (!ctx) return;

            if (window.beneficiaryYearChartInstance) {
                window.beneficiaryYearChartInstance.destroy();
            }

            // Create gradient fills
            var gradPeoples = ctx.createLinearGradient(0, 0, 0, 300);
            gradPeoples.addColorStop(0, 'rgba(14, 165, 233, 0.95)');
            gradPeoples.addColorStop(1, 'rgba(2, 132, 199, 0.65)');

            var gradFamilies = ctx.createLinearGradient(0, 0, 0, 300);
            gradFamilies.addColorStop(0, 'rgba(16, 185, 129, 0.95)');
            gradFamilies.addColorStop(1, 'rgba(5, 150, 105, 0.65)');

            window.beneficiaryYearChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Benefited Peoples',
                            data: peoples,
                            backgroundColor: gradPeoples,
                            borderColor: '#0284c7',
                            borderWidth: 1.5,
                            borderRadius: { topLeft: 10, topRight: 10, bottomLeft: 0, bottomRight: 0 },
                            borderSkipped: false,
                            maxBarThickness: 48,
                        },
                        {
                            label: 'Benefited Families',
                            data: families,
                            backgroundColor: gradFamilies,
                            borderColor: '#059669',
                            borderWidth: 1.5,
                            borderRadius: { topLeft: 10, topRight: 10, bottomLeft: 0, bottomRight: 0 },
                            borderSkipped: false,
                            maxBarThickness: 48,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 800,
                        easing: 'easeOutQuart'
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.92)',
                            titleColor: '#cbd5e1',
                            titleFont: { size: 12, weight: '700' },
                            bodyColor: '#ffffff',
                            bodyFont: { size: 13, weight: '600' },
                            padding: { top: 10, bottom: 10, left: 14, right: 14 },
                            cornerRadius: 10,
                            boxPadding: 6,
                            usePointStyle: true,
                            borderColor: 'rgba(255, 255, 255, 0.1)',
                            borderWidth: 1,
                            callbacks: {
                                label: function(ctx) {
                                    return ' ' + ctx.dataset.label + ': ' + ctx.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                color: '#64748b',
                                font: { size: 12, weight: '700' }
                            },
                            border: { color: '#e2e8f0' }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(226, 232, 240, 0.6)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#94a3b8',
                                font: { size: 11, weight: '600' },
                                padding: 8,
                                callback: function(val) {
                                    if (val >= 1000) return (val / 1000).toFixed(1) + 'k';
                                    return val;
                                }
                            },
                            border: { dash: [4, 4], color: 'transparent' }
                        }
                    }
                }
            });
        }

        // Initialize Chart
        if (typeof Chart !== 'undefined') {
            initBeneficiaryChart();
        } else {
            document.addEventListener('DOMContentLoaded', function() {
                var attempts = 0;
                var interval = setInterval(function() {
                    if (typeof Chart !== 'undefined') {
                        clearInterval(interval);
                        initBeneficiaryChart();
                    } else if (++attempts > 25) {
                        clearInterval(interval);
                    }
                }, 150);
            });
        }

        // Livewire re-render hooks
        document.addEventListener('livewire:navigated', initBeneficiaryChart);
        document.addEventListener('livewire:update', function() {
            setTimeout(initBeneficiaryChart, 100);
        });
    })();
    </script>
</div>
