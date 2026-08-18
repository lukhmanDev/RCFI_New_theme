<div class="role-dashboard-container" wire:poll.10s>

    <style>
        .role-dashboard-container {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            font-family: inherit;
        }

        /* 1. Executive Header */
        .dash-header-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.5rem 1.75rem;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04), 0 6px 16px rgba(15, 23, 42, 0.02);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1.25rem;
            position: relative;
            overflow: hidden;
        }
        .dash-header-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #10b981 0%, #059669 100%);
        }
        .dash-title-wrap {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }
        .dash-title {
            color: #0f172a;
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: -0.025em;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .dash-meta-row {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            flex-wrap: wrap;
            font-size: 0.85rem;
            color: #64748b;
        }
        .role-badge-pill {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
            padding: 0.2rem 0.65rem;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 0.76rem;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }
        .live-status-dot {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-weight: 600;
            font-size: 0.8rem;
            color: #059669;
        }
        .live-pulse-dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: pulse-ring 2s infinite;
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
        .dash-actions-wrap {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        .btn-dash-outline {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background: #ffffff;
            color: #334155;
            border: 1px solid #cbd5e1;
            padding: 0.55rem 1.05rem;
            border-radius: 10px;
            font-size: 0.84rem;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
            transition: all 0.2s ease;
        }
        .btn-dash-outline:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #0f172a;
            transform: translateY(-1px);
        }
        .btn-dash-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            border: 1px solid #059669;
            padding: 0.55rem 1.2rem;
            border-radius: 10px;
            font-size: 0.84rem;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
            transition: all 0.2s ease;
        }
        .btn-dash-primary:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.32);
        }

        /* 2. Standard KPI Grid */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.25rem;
        }
        .kpi-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.35rem 1.5rem;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 0.85rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px -4px rgba(15, 23, 42, 0.08);
            border-color: #cbd5e1;
        }
        .kpi-card-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .kpi-icon-box {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }
        .icon-emerald { background: #ecfdf5; color: #059669; border: 1px solid #d1fae5; }
        .icon-teal    { background: #f0fdf4; color: #16a34a; border: 1px solid #dcfce7; }
        .icon-amber   { background: #fffbeb; color: #d97706; border: 1px solid #fef3c7; }
        .icon-blue    { background: #eff6ff; color: #2563eb; border: 1px solid #dbeafe; }

        .kpi-badge {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.2rem 0.55rem;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .badge-emerald-soft { background: #ecfdf5; color: #059669; }
        .badge-teal-soft    { background: #f0fdf4; color: #16a34a; }
        .badge-amber-soft   { background: #fffbeb; color: #d97706; }
        .badge-blue-soft    { background: #eff6ff; color: #2563eb; }

        .kpi-main-info {
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
        }
        .kpi-label {
            color: #64748b;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .kpi-value {
            color: #0f172a;
            font-size: 1.85rem;
            font-weight: 900;
            line-height: 1.15;
            letter-spacing: -0.03em;
        }
        .kpi-footer {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.76rem;
            font-weight: 600;
            color: #64748b;
            padding-top: 0.65rem;
            border-top: 1px dashed #f1f5f9;
        }

        /* 3. Operational Quick Summary Bar */
        .status-strip-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        .status-strip-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.95rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            transition: all 0.2s ease;
        }
        .status-strip-card:hover {
            border-color: #cbd5e1;
            background: #fbfcfe;
        }
        .status-strip-title {
            font-size: 0.8rem;
            font-weight: 700;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 0.45rem;
        }
        .status-strip-num {
            font-size: 1.15rem;
            font-weight: 800;
            color: #0f172a;
        }

        /* 4. Full-Width Analytics Chart Card */
        .chart-main-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.5rem 1.75rem;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }
        .card-header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .card-heading {
            margin: 0;
            color: #0f172a;
            font-size: 1.1rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            letter-spacing: -0.01em;
        }
        .card-subtext {
            color: #64748b;
            font-size: 0.82rem;
            margin: 0.2rem 0 0;
            font-weight: 500;
        }
        .chart-legend-box {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 0.4rem 0.85rem;
            border-radius: 10px;
        }
        .legend-tag {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.78rem;
            font-weight: 700;
        }
        .legend-color-dot {
            width: 10px;
            height: 10px;
            border-radius: 3px;
        }
        .chart-canvas-wrap {
            position: relative;
            height: 320px;
            width: 100%;
        }

        /* 4.5. National Geographic Reach & State-wise Impact Map */
        .map-main-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.5rem 1.75rem;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04), 0 6px 16px rgba(15, 23, 42, 0.02);
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }
        .map-grid-layout {
            display: grid;
            grid-template-columns: 1.35fr 1fr;
            gap: 1.5rem;
            align-items: stretch;
        }
        @media (max-width: 1024px) {
            .map-grid-layout {
                grid-template-columns: 1fr;
            }
        }
        .map-visualization-area {
            position: relative;
            background: radial-gradient(circle at center, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            overflow: hidden;
            min-height: 520px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #indiaMapContainer {
            width: 100%;
            height: 100%;
            min-height: 520px;
        }
        .map-controls-floating {
            position: absolute;
            top: 14px;
            right: 14px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            z-index: 10;
        }
        .btn-map-ctrl {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #334155;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(15, 23, 42, 0.06);
            transition: all 0.2s ease;
            font-size: 1.1rem;
        }
        .btn-map-ctrl:hover {
            background: #ecfdf5;
            color: #059669;
            border-color: #10b981;
            transform: scale(1.05);
        }
        .map-legend-bar {
            position: absolute;
            bottom: 14px;
            left: 14px;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(8px);
            border: 1px solid #e2e8f0;
            padding: 0.45rem 0.75rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.72rem;
            font-weight: 700;
            color: #475569;
            z-index: 10;
        }
        .map-legend-gradient {
            width: 90px;
            height: 8px;
            border-radius: 4px;
            background: linear-gradient(90deg, #e2e8f0 0%, #6ee7b7 40%, #10b981 70%, #047857 100%);
        }
        .india-map-tooltip {
            position: absolute;
            display: none;
            pointer-events: none;
            background: rgba(15, 23, 42, 0.94);
            backdrop-filter: blur(10px);
            color: #ffffff;
            padding: 0.75rem 0.95rem;
            border-radius: 10px;
            border: 1px solid rgba(16, 185, 129, 0.4);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.2);
            font-size: 0.8rem;
            z-index: 50;
            min-width: 190px;
            transition: transform 0.08s ease-out;
        }

        /* State Insights Side Panel */
        .state-insights-panel {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .state-detail-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 1.25rem 1.4rem;
            position: relative;
            overflow: hidden;
        }
        .state-detail-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #10b981 0%, #059669 100%);
        }
        .state-detail-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }
        .state-detail-name {
            font-size: 1.15rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.01em;
            margin: 0;
        }
        .state-project-pill {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
            padding: 0.25rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.76rem;
            font-weight: 700;
        }
        .state-stats-mini-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            margin-top: 0.85rem;
        }
        .state-mini-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.75rem;
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }
        .state-mini-box-label {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #64748b;
        }
        .state-mini-box-val {
            font-size: 1.15rem;
            font-weight: 800;
            color: #0f172a;
        }

        /* Ranked State Leaderboard */
        .state-leaderboard-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 1.15rem 1.35rem;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
            flex: 1;
        }
        .leaderboard-title-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            font-weight: 700;
            color: #334155;
        }
        .leaderboard-scroll-list {
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
            max-height: 260px;
            overflow-y: auto;
            padding-right: 4px;
        }
        .leaderboard-scroll-list::-webkit-scrollbar {
            width: 4px;
        }
        .leaderboard-scroll-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .leaderboard-item-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.55rem 0.75rem;
            border-radius: 8px;
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .leaderboard-item-row:hover, .leaderboard-item-row.active {
            background: #ecfdf5;
            border-color: #a7f3d0;
            transform: translateX(2px);
        }
        .leaderboard-rank-badge {
            width: 22px;
            height: 22px;
            border-radius: 6px;
            background: #e2e8f0;
            color: #475569;
            font-size: 0.7rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.6rem;
        }
        .leaderboard-item-row.active .leaderboard-rank-badge,
        .leaderboard-item-row:hover .leaderboard-rank-badge {
            background: #059669;
            color: #ffffff;
        }
        .leaderboard-st-name {
            font-size: 0.82rem;
            font-weight: 700;
            color: #1e293b;
            flex: 1;
        }
        .leaderboard-st-count {
            font-size: 0.78rem;
            font-weight: 800;
            color: #059669;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 0.15rem 0.5rem;
            border-radius: 6px;
        }

        /* 5. Theme Breakdown Modern Table (Clean Standard Enterprise Theme) */
        .table-card-container {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.5rem 1.75rem;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }
        .table-custom-wrapper {
            overflow-x: auto;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
        }
        .table-custom {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }
        .table-custom thead tr {
            background-color: #10b981 !important;
            color: #ffffff !important;
        }
        .table-custom thead th {
            background-color: #10b981 !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            font-size: 0.74rem !important;
            letter-spacing: 0.05em !important;
            padding: 0.9rem 1.15rem !important;
            border: none !important;
            white-space: nowrap;
        }
        .table-custom tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.15s ease;
        }
        .table-custom tbody tr:hover {
            background: #f8fafc;
        }
        .table-custom tbody tr:last-child {
            border-bottom: none;
        }
        .table-custom td {
            padding: 0.95rem 1.15rem;
            vertical-align: middle;
            background: transparent;
        }
        .col-rank-num {
            font-size: 0.85rem;
            font-weight: 700;
            color: #64748b;
        }
        .theme-name-text {
            font-weight: 700;
            color: #0f172a;
            font-size: 0.92rem;
            line-height: 1.35;
        }
        .subtheme-pill-link {
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #4338ca;
            padding: 0.35rem 0.8rem;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 700;
            white-space: nowrap;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            transition: all 0.2s ease;
        }
        .subtheme-pill-link:hover {
            background-color: #e0e7ff;
            border-color: #c7d2fe;
            color: #3730a3;
            transform: translateY(-1px);
        }
        .projects-total-count {
            font-weight: 800;
            color: #0f172a;
            font-size: 0.95rem;
        }
        .status-pill-running {
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            color: #b45309;
            padding: 0.25rem 0.65rem;
            border-radius: 14px;
            font-size: 0.76rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            white-space: nowrap;
        }
        .status-pill-completed {
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #047857;
            padding: 0.25rem 0.65rem;
            border-radius: 14px;
            font-size: 0.76rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            white-space: nowrap;
        }
        .status-pill-zero {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 28px;
            height: 24px;
            border-radius: 6px;
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            color: #94a3b8;
            font-size: 0.78rem;
            font-weight: 600;
        }
        .impact-none-badge {
            color: #94a3b8;
            font-size: 0.82rem;
            font-weight: 500;
        }

        /* Responsive Breakpoints */
        @media (max-width: 768px) {
            .dash-header-card { padding: 1.25rem; }
            .dash-title { font-size: 1.35rem; }
            .chart-main-card { padding: 1.25rem; }
            .table-card-container { padding: 1.25rem; }
            .chart-canvas-wrap { height: 260px; }
        }
    </style>

    <!-- 1. Executive Welcome Header -->
    <div class="dash-header-card">
        <div class="dash-title-wrap">
            <h1 class="dash-title">
                Welcome back, {{ $user->name }}
            </h1>
            <div class="dash-meta-row">
                <span class="role-badge-pill">
                    <i class="bx bxs-shield-alt-2"></i> {{ $user->role_name }}
                </span>
                <span>•</span>
                <span class="live-status-dot">
                    <span class="live-pulse-dot"></span> System Live & Operational
                </span>
                <span>•</span>
                <span>{{ now()->format('l, d/m/Y') }}</span>
            </div>
        </div>
        <div class="dash-actions-wrap">
            @if(Auth::user()->canAddApplications())
                <a href="{{ route('applications.index') }}" class="btn-dash-outline">
                    <i class="bx bx-file" style="color: #059669; font-size: 1.05rem;"></i> Applications
                </a>
            @endif
            @if(Auth::user()->canAddEditProjects())
                <a href="{{ route('projects.index') }}" class="btn-dash-primary">
                    <i class="bx bx-briefcase-alt-2" style="font-size: 1.05rem;"></i> All Projects
                </a>
            @endif
        </div>
    </div>

    <!-- 2. Standardized Executive KPI Grid -->
    <div class="kpi-grid">

        <!-- KPI 1: Benefited Peoples -->
        <div class="kpi-card">
            <div class="kpi-card-top">
                <div class="kpi-icon-box icon-emerald">
                    <i class="bx bxs-group"></i>
                </div>
                <span class="kpi-badge badge-emerald-soft">
                    <i class="bx bx-check-double"></i> Direct Impact
                </span>
            </div>
            <div class="kpi-main-info">
                <span class="kpi-label">Total Benefited Peoples</span>
                <div class="kpi-value">{{ number_format($totalBeneficiaryPeoples) }}</div>
            </div>
            <div class="kpi-footer">
                <i class="bx bx-badge-check" style="color: #059669;"></i>
                Verified individuals across completed projects
            </div>
        </div>

        <!-- KPI 2: Benefited Families -->
        <div class="kpi-card">
            <div class="kpi-card-top">
                <div class="kpi-icon-box icon-teal">
                    <i class="bx bxs-home-heart"></i>
                </div>
                <span class="kpi-badge badge-teal-soft">
                    <i class="bx bx-heart"></i> Household
                </span>
            </div>
            <div class="kpi-main-info">
                <span class="kpi-label">Total Benefited Families</span>
                <div class="kpi-value">{{ number_format($totalBeneficiaryFamily) }}</div>
            </div>
            <div class="kpi-footer">
                <i class="bx bx-check-circle" style="color: #16a34a;"></i>
                Supported families in completed projects
            </div>
        </div>

        <!-- KPI 3: Projects Execution -->
        <div class="kpi-card">
            <div class="kpi-card-top">
                <div class="kpi-icon-box icon-amber">
                    <i class="bx bxs-briefcase-alt-2"></i>
                </div>
                <span class="kpi-badge badge-amber-soft">
                    <i class="bx bx-loader-circle"></i> {{ number_format($runningProjects) }} Active
                </span>
            </div>
            <div class="kpi-main-info">
                <span class="kpi-label">Projects Portfolio</span>
                <div class="kpi-value">{{ number_format($totalProjects) }}</div>
            </div>
            <div class="kpi-footer">
                <i class="bx bx-trophy" style="color: #d97706;"></i>
                {{ number_format($completedProjects) }} Completed · {{ number_format($runningProjects) }} In Delivery
            </div>
        </div>

        <!-- KPI 4: Applications Intake -->
        <div class="kpi-card">
            <div class="kpi-card-top">
                <div class="kpi-icon-box icon-blue">
                    <i class="bx bxs-file-blank"></i>
                </div>
                <span class="kpi-badge badge-blue-soft">
                    @if(($newApplicationsCount ?? 0) > 0)
                        +{{ $newApplicationsCount }} New
                    @else
                        Intake
                    @endif
                </span>
            </div>
            <div class="kpi-main-info">
                <span class="kpi-label">Total Applications</span>
                <div class="kpi-value">{{ number_format($totalApplications) }}</div>
            </div>
            <div class="kpi-footer">
                <i class="bx bx-time-five" style="color: #2563eb;"></i>
                {{ number_format($approvedCount) }} Approved · {{ number_format($pendingCount) }} Under Review
            </div>
        </div>

    </div>

    <!-- 3. Operational Quick Status Strip -->
    <div class="status-strip-grid">
        <div class="status-strip-card">
            <div class="status-strip-title">
                <i class="bx bx-file text-blue" style="font-size: 1.15rem; color: #2563eb;"></i>
                <span>Applications Total</span>
            </div>
            <span class="status-strip-num">{{ number_format($totalApplications) }}</span>
        </div>
        <div class="status-strip-card">
            <div class="status-strip-title">
                <i class="bx bx-time-five text-orange" style="font-size: 1.15rem; color: #ea580c;"></i>
                <span>Pending Review</span>
            </div>
            <span class="status-strip-num" style="color: #ea580c;">{{ number_format($pendingCount) }}</span>
        </div>
        <div class="status-strip-card">
            <div class="status-strip-title">
                <i class="bx bx-check-circle text-emerald" style="font-size: 1.15rem; color: #059669;"></i>
                <span>Approved</span>
            </div>
            <span class="status-strip-num" style="color: #059669;">{{ number_format($approvedCount) }}</span>
        </div>
        <div class="status-strip-card">
            <div class="status-strip-title">
                <i class="bx bx-loader-circle" style="font-size: 1.15rem; color: #7c3aed;"></i>
                <span>Active Pipeline</span>
            </div>
            <span class="status-strip-num" style="color: #7c3aed;">{{ number_format($runningProjects) }}</span>
        </div>
    </div>

    <!-- 4. Year-wise Impact Overview (Visual Chart Card) -->
    <div class="chart-main-card">
        <div class="card-header-flex">
            <div>
                <h3 class="card-heading">
                    <i class="bx bx-bar-chart-alt-2" style="color: #059669; font-size: 1.25rem;"></i>
                    Year-wise Impact Overview
                </h3>
                <p class="card-subtext">Annual verified reach of benefited individuals and families</p>
            </div>
            <div class="chart-legend-box">
                <div class="legend-tag" style="color: #047857;">
                    <span class="legend-color-dot" style="background: #10b981;"></span>
                    Benefited Peoples
                </div>
                <div class="legend-tag" style="color: #065f46;">
                    <span class="legend-color-dot" style="background: #34d399;"></span>
                    Benefited Families
                </div>
            </div>
        </div>

        <div class="chart-canvas-wrap" wire:ignore>
            <canvas id="beneficiaryYearChart"></canvas>
        </div>
    </div>

    <!-- 4.5. National Geographic Reach & State-wise Impact Map -->
    @php
        $activeStatesList = collect($stateSummaryData ?? [])->filter(fn($st) => ($st['total_projects'] ?? 0) > 0)->sortByDesc('total_projects')->values();
        $coveredStatesCount = $activeStatesList->count();
        $topState = $activeStatesList->first() ?? [
            'name' => 'Kerala',
            'total_projects' => 0,
            'running_projects' => 0,
            'completed_projects' => 0,
            'benefited_peoples' => 0,
            'benefited_families' => 0,
        ];
    @endphp
    <div class="map-main-card">
        <div class="card-header-flex">
            <div>
                <h3 class="card-heading">
                    <i class="bx bx-map-pin" style="color: #059669; font-size: 1.25rem;"></i>
                    National Reach & State-wise Impact Map
                </h3>
                <p class="card-subtext">
                    Interactive state-wise project density and verified community reach across India
                </p>
            </div>
            <div class="chart-legend-box">
                <div class="legend-tag" style="color: #047857;">
                    <span class="legend-color-dot" style="background: #10b981;"></span>
                    <span>States Covered: <strong>{{ $coveredStatesCount }}</strong> / 36</span>
                </div>
                <div class="legend-tag" style="color: #065f46;">
                    <span class="legend-color-dot" style="background: #047857;"></span>
                    <span>Live Tracking</span>
                </div>
            </div>
        </div>

        <div class="map-grid-layout">
            <!-- Left: Interactive D3.js SVG Map -->
            <div class="map-visualization-area">
                <div id="indiaMapContainer" wire:ignore></div>

                <!-- Floating Controls -->
                <div class="map-controls-floating">
                    <button type="button" class="btn-map-ctrl" id="mapZoomIn" title="Zoom In">
                        <i class="bx bx-plus"></i>
                    </button>
                    <button type="button" class="btn-map-ctrl" id="mapZoomOut" title="Zoom Out">
                        <i class="bx bx-minus"></i>
                    </button>
                    <button type="button" class="btn-map-ctrl" id="mapZoomReset" title="Reset View">
                        <i class="bx bx-reset"></i>
                    </button>
                </div>

                <!-- Legend Bar -->
                <div class="map-legend-bar">
                    <span>Density:</span>
                    <div class="map-legend-gradient"></div>
                    <span>High Reach</span>
                </div>

                <!-- Floating D3 Tooltip -->
                <div id="indiaMapTooltip" class="india-map-tooltip"></div>
            </div>

            <!-- Right: State Insights & Ranked Impact Leaderboard -->
            <div class="state-insights-panel">
                <!-- Selected State Details Card -->
                <div class="state-detail-card" id="stateDetailCard">
                    <div class="state-detail-header">
                        <div>
                            <span style="font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Selected State</span>
                            <h4 class="state-detail-name" id="selectedStateTitle">{{ $selectedStateInfo['name'] }}</h4>
                        </div>
                        <span class="state-project-pill" id="selectedStateBadge">
                            <i class="bx bx-briefcase-alt-2"></i> <span id="selectedStateProjects">{{ $selectedStateInfo['total_projects'] }}</span> Projects
                        </span>
                    </div>

                    <div class="state-stats-mini-grid">
                        <div class="state-mini-box">
                            <span class="state-mini-box-label">Running</span>
                            <span class="state-mini-box-val" id="selectedStateRunning" style="color: #059669;">{{ number_format($selectedStateInfo['running_projects']) }}</span>
                        </div>
                        <div class="state-mini-box">
                            <span class="state-mini-box-label">Completed</span>
                            <span class="state-mini-box-val" id="selectedStateCompleted" style="color: #10b981;">{{ number_format($selectedStateInfo['completed_projects']) }}</span>
                        </div>
                        <div class="state-mini-box">
                            <span class="state-mini-box-label">Benefited Peoples</span>
                            <span class="state-mini-box-val" id="selectedStatePeoples" style="color: #047857;">{{ number_format($selectedStateInfo['benefited_peoples']) }}</span>
                        </div>
                        <div class="state-mini-box">
                            <span class="state-mini-box-label">Benefited Families</span>
                            <span class="state-mini-box-val" id="selectedStateFamilies" style="color: #059669;">{{ number_format($selectedStateInfo['benefited_families']) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Ranked State Leaderboard -->
                <div class="state-leaderboard-card">
                    <div class="leaderboard-title-row">
                        <span><i class="bx bx-trophy" style="color: #d97706; margin-right: 4px;"></i> Active States & Reach</span>
                        <span style="font-size: 0.75rem; color: #64748b;">Click to view</span>
                    </div>
                    <div class="leaderboard-scroll-list">
                        @forelse($activeStatesList as $sIdx => $sItem)
                            <div class="leaderboard-item-row {{ ($selectedState ?? '') === $sItem['name'] ? 'active' : '' }}" 
                                 wire:click="selectState('{{ $sItem['name'] }}')"
                                 onclick="if(window.selectStateDetails) window.selectStateDetails('{{ $sItem['name'] }}');"
                                 data-state-row="{{ $sItem['name'] }}">
                                <div style="display: flex; align-items: center; flex: 1;">
                                    <span class="leaderboard-rank-badge">{{ $sIdx + 1 }}</span>
                                    <span class="leaderboard-st-name">{{ $sItem['name'] }}</span>
                                </div>
                                <span class="leaderboard-st-count">
                                    {{ $sItem['total_projects'] }} {{ Str::plural('Project', $sItem['total_projects']) }}
                                </span>
                            </div>
                        @empty
                            <div style="padding: 1.5rem; text-align: center; color: #94a3b8; font-size: 0.82rem;">
                                No active states recorded yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. Theme-Wise Performance & Impact Breakdown -->
    <div class="table-card-container">
        <div class="card-header-flex">
            <div>
                <h3 class="card-heading">
                    <i class="bx bx-category-alt" style="color: #059669; font-size: 1.25rem;"></i>
                    Theme-Wise Overview & Impact Breakdown
                </h3>
                <p class="card-subtext">
                    Real-time project distribution, execution stages, and beneficiary reach categorized by development themes
                </p>
            </div>
        </div>

        <div class="table-custom-wrapper">
            <table class="table-custom no-paginate">
                <thead>
                    <tr>
                        <th style="width: 55px; text-align: center;">#</th>
                        <th style="text-align: left;">Theme Name</th>
                        <th style="text-align: center; width: 130px;">Total Projects</th>
                        <th style="text-align: center; width: 120px;">Running</th>
                        <th style="text-align: center; width: 120px;">Completed</th>
                        <th style="text-align: right; width: 160px;">Benefited Peoples</th>
                        <th style="text-align: right; width: 160px;">Benefited Families</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($themeSummaryData as $index => $item)
                        <tr>
                            <td style="text-align: center;">
                                <span class="col-rank-num">{{ $index + 1 }}</span>
                            </td>
                            <td>
                                <div class="theme-name-text">
                                    {{ $item['name'] }}
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <span class="projects-total-count">
                                    {{ number_format($item['total_projects']) }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                @if($item['running_projects'] > 0)
                                    <span class="status-pill-running">
                                        <i class="bx bx-loader-circle bx-spin"></i> {{ number_format($item['running_projects']) }}
                                    </span>
                                @else
                                    <span class="status-pill-zero">0</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if($item['completed_projects'] > 0)
                                    <span class="status-pill-completed">
                                        <i class="bx bx-check-circle"></i> {{ number_format($item['completed_projects']) }}
                                    </span>
                                @else
                                    <span class="status-pill-zero">0</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                @if($item['benefited_peoples'] > 0)
                                    <span style="font-weight: 800; color: #059669; font-size: 0.92rem;">
                                        {{ number_format($item['benefited_peoples']) }}
                                    </span>
                                @else
                                    <span style="color: #94a3b8; font-size: 0.85rem; font-weight: 500;">0</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                @if($item['benefited_families'] > 0)
                                    <span style="font-weight: 800; color: #10b981; font-size: 0.92rem;">
                                        {{ number_format($item['benefited_families']) }}
                                    </span>
                                @else
                                    <span style="color: #94a3b8; font-size: 0.85rem; font-weight: 500;">0</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding: 3rem; text-align: center; color: #94a3b8;">
                                <i class="bx bx-folder-open" style="font-size: 2.25rem; display: block; margin-bottom: 0.5rem; color: #cbd5e1;"></i>
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

    <!-- Chart.js & D3 India Map Scripts (Rock-Solid Persistent Rendering) -->
    <script>
    window.__dashChartData = @json($beneficiaryChartData);
    window.__dashStateData = @json($stateSummaryData ?? []);

    // 1. Chart Initializer
    window.initBeneficiaryChart = function() {
        var canvas = document.getElementById('beneficiaryYearChart');
        if (!canvas) return;
        var ctx = canvas.getContext('2d');
        if (!ctx) return;

        if (typeof Chart === 'undefined') {
            setTimeout(window.initBeneficiaryChart, 100);
            return;
        }

        var chartData = window.__dashChartData || { labels: [], peoples: [], families: [] };
        var labels = chartData.labels || [];
        var peoples = chartData.peoples || [];
        var families = chartData.families || [];

        // If chart already exists on this canvas, update datasets smoothly
        if (window.beneficiaryYearChartInstance && window.beneficiaryYearChartInstance.canvas === canvas) {
            window.beneficiaryYearChartInstance.data.labels = labels;
            window.beneficiaryYearChartInstance.data.datasets[0].data = peoples;
            window.beneficiaryYearChartInstance.data.datasets[1].data = families;
            window.beneficiaryYearChartInstance.update();
            return;
        }

        if (window.beneficiaryYearChartInstance) {
            window.beneficiaryYearChartInstance.destroy();
        }

        var gradPeoples = ctx.createLinearGradient(0, 0, 0, 300);
        gradPeoples.addColorStop(0, 'rgba(16, 185, 129, 0.95)');
        gradPeoples.addColorStop(1, 'rgba(5, 150, 105, 0.70)');

        var gradFamilies = ctx.createLinearGradient(0, 0, 0, 300);
        gradFamilies.addColorStop(0, 'rgba(52, 211, 153, 0.95)');
        gradFamilies.addColorStop(1, 'rgba(16, 185, 129, 0.55)');

        window.beneficiaryYearChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Benefited Peoples',
                        data: peoples,
                        yAxisID: 'y',
                        backgroundColor: gradPeoples,
                        borderColor: '#059669',
                        borderWidth: 1.5,
                        borderRadius: { topLeft: 8, topRight: 8, bottomLeft: 0, bottomRight: 0 },
                        borderSkipped: false,
                        hoverBackgroundColor: 'rgba(16, 185, 129, 1)',
                        hoverBorderColor: '#047857',
                        maxBarThickness: 38,
                        minBarLength: 12,
                        categoryPercentage: 0.65,
                        barPercentage: 0.85
                    },
                    {
                        label: 'Benefited Families',
                        data: families,
                        yAxisID: 'y1',
                        backgroundColor: gradFamilies,
                        borderColor: '#10b981',
                        borderWidth: 1.5,
                        borderRadius: { topLeft: 8, topRight: 8, bottomLeft: 0, bottomRight: 0 },
                        borderSkipped: false,
                        hoverBackgroundColor: 'rgba(52, 211, 153, 1)',
                        hoverBorderColor: '#059669',
                        maxBarThickness: 38,
                        minBarLength: 12,
                        categoryPercentage: 0.65,
                        barPercentage: 0.85
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 700,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.94)',
                        titleColor: '#cbd5e1',
                        titleFont: { size: 12, weight: '700' },
                        bodyColor: '#ffffff',
                        bodyFont: { size: 13, weight: '600' },
                        padding: { top: 10, bottom: 10, left: 14, right: 14 },
                        cornerRadius: 10,
                        boxPadding: 6,
                        usePointStyle: true,
                        borderColor: 'rgba(16, 185, 129, 0.3)',
                        borderWidth: 1,
                        callbacks: {
                            label: function(ctx) {
                                return ' ' + ctx.dataset.label + ': ' + Number(ctx.parsed.y).toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#475569',
                            font: { size: 12, weight: '700' }
                        },
                        border: { color: '#e2e8f0' }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(226, 232, 240, 0.6)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#059669',
                            font: { size: 11, weight: '700' },
                            padding: 8,
                            callback: function(val) {
                                if (val >= 1000000) return (val / 1000000).toFixed(1) + 'M';
                                if (val >= 1000) return (val / 1000).toFixed(1) + 'k';
                                return val;
                            }
                        },
                        title: {
                            display: true,
                            text: 'Benefited Peoples',
                            color: '#059669',
                            font: { size: 11, weight: '700' }
                        },
                        border: { dash: [4, 4], color: 'transparent' }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        grid: {
                            drawOnChartArea: false,
                            drawBorder: false
                        },
                        ticks: {
                            color: '#10b981',
                            font: { size: 11, weight: '700' },
                            padding: 8,
                            callback: function(val) {
                                if (val >= 1000000) return (val / 1000000).toFixed(1) + 'M';
                                if (val >= 1000) return (val / 1000).toFixed(1) + 'k';
                                return val;
                            }
                        },
                        title: {
                            display: true,
                            text: 'Benefited Families',
                            color: '#10b981',
                            font: { size: 11, weight: '700' }
                        },
                        border: { dash: [4, 4], color: 'transparent' }
                    }
                }
            }
        });
    };

    // 2. D3 India Map Initializer
    window.initIndiaD3Map = function() {
        var container = document.getElementById('indiaMapContainer');
        if (!container) return;

        if (typeof d3 === 'undefined' || !window.INDIA_GEO_JSON || !window.INDIA_GEO_JSON.features) {
            setTimeout(window.initIndiaD3Map, 100);
            return;
        }

        var geoData = window.INDIA_GEO_JSON;
        var stateData = window.__dashStateData || {};

        // If SVG already exists, just update colors/data without tearing down DOM
        if (container.querySelector('svg')) {
            var svgExisting = d3.select(container).select('svg');
            var maxProjectsEx = d3.max(Object.values(stateData), function(d) { return d.total_projects; }) || 1;
            var colorScaleEx = d3.scaleSequential()
                .domain([0, Math.max(maxProjectsEx, 3)])
                .interpolator(d3.interpolateRgbBasis(['#f1f5f9', '#a7f3d0', '#34d399', '#10b981', '#059669', '#047857']));

            svgExisting.selectAll('path.state-feature')
                .attr('fill', function(d) {
                    var name = d.properties.ST_NM;
                    var info = stateData[name];
                    if (info && info.total_projects > 0) {
                        return colorScaleEx(info.total_projects);
                    }
                    return '#f8fafc';
                });
            return;
        }

        container.innerHTML = '';
        var width = container.clientWidth || 580;
        var height = container.clientHeight || 520;
        if (height < 450) height = 520;

        var svg = d3.select(container)
            .append('svg')
            .attr('width', '100%')
            .attr('height', height)
            .attr('viewBox', '0 0 ' + width + ' ' + height)
            .style('display', 'block')
            .style('user-select', 'none');

        var g = svg.append('g');
        window.__indiaMapG = g;

        var projection = d3.geoMercator().fitSize([width - 30, height - 30], geoData);
        var pathGenerator = d3.geoPath().projection(projection);

        var zoom = d3.zoom()
            .scaleExtent([0.7, 7])
            .on('zoom', function(event) {
                g.attr('transform', event.transform);
            });
            
        svg.call(zoom)
           .on('touchstart.zoom', null)
           .on('touchmove.zoom', null)
           .on('touchend.zoom', null);

        var btnIn = document.getElementById('mapZoomIn');
        var btnOut = document.getElementById('mapZoomOut');
        var btnReset = document.getElementById('mapZoomReset');

        if (btnIn) btnIn.onclick = function() { svg.transition().duration(250).call(zoom.scaleBy, 1.35); };
        if (btnOut) btnOut.onclick = function() { svg.transition().duration(250).call(zoom.scaleBy, 0.75); };
        if (btnReset) btnReset.onclick = function() {
            window.selectedMapState = null;
            svg.transition().duration(400).call(zoom.transform, d3.zoomIdentity);
            g.selectAll('path.state-feature')
                .attr('stroke', '#cbd5e1')
                .attr('stroke-width', 1);
        };

        var maxProjects = d3.max(Object.values(stateData), function(d) { return d.total_projects; }) || 1;
        var colorScale = d3.scaleSequential()
            .domain([0, Math.max(maxProjects, 3)])
            .interpolator(d3.interpolateRgbBasis(['#f1f5f9', '#a7f3d0', '#34d399', '#10b981', '#059669', '#047857']));

        var tooltip = d3.select('#indiaMapTooltip');

        g.selectAll('path.state-feature')
            .data(geoData.features)
            .enter()
            .append('path')
            .attr('class', 'state-feature')
            .attr('d', pathGenerator)
            .attr('data-state', function(d) { return d.properties.ST_NM; })
            .attr('fill', function(d) {
                var name = d.properties.ST_NM;
                var info = stateData[name];
                if (info && info.total_projects > 0) {
                    return colorScale(info.total_projects);
                }
                return '#f8fafc';
            })
            .attr('stroke', '#cbd5e1')
            .attr('stroke-width', 1)
            .style('cursor', 'pointer')
            .style('transition', 'fill 0.15s ease, stroke 0.15s ease')
            .on('mouseenter', function(event, d) {
                var name = d.properties.ST_NM;
                var info = (window.__dashStateData && window.__dashStateData[name]) || {
                    name: name,
                    total_projects: 0,
                    running_projects: 0,
                    completed_projects: 0,
                    benefited_peoples: 0,
                    benefited_families: 0
                };

                d3.select(this)
                    .attr('stroke', '#065f46')
                    .attr('stroke-width', 2.5)
                    .raise();

                var projBadge = info.total_projects > 0 
                    ? '<span style="background: rgba(16, 185, 129, 0.35); color: #6ee7b7; padding: 2px 8px; border-radius: 9999px; font-size: 0.72rem; border: 1px solid rgba(110, 231, 183, 0.4); font-weight: 700;">' + info.total_projects + ' Projects</span>'
                    : '<span style="background: rgba(148, 163, 184, 0.25); color: #cbd5e1; padding: 2px 7px; border-radius: 9999px; font-size: 0.7rem;">0 Projects</span>';

                tooltip.style('display', 'block')
                    .html(
                        '<div style="font-weight: 800; font-size: 0.95rem; color: #ffffff; margin-bottom: 4px; display: flex; align-items: center; justify-content: space-between; gap: 8px;">' +
                            '<span>' + info.name + '</span>' + projBadge +
                        '</div>' +
                        '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px; font-size: 0.76rem; margin-top: 6px; color: #cbd5e1; border-top: 1px solid rgba(255,255,255,0.12); padding-top: 6px;">' +
                            '<div>Running: <strong style="color:#6ee7b7;">' + info.running_projects.toLocaleString() + '</strong></div>' +
                            '<div>Completed: <strong style="color:#a7f3d0;">' + info.completed_projects.toLocaleString() + '</strong></div>' +
                            '<div>Peoples: <strong style="color:#ffffff;">' + info.benefited_peoples.toLocaleString() + '</strong></div>' +
                            '<div>Families: <strong style="color:#ffffff;">' + info.benefited_families.toLocaleString() + '</strong></div>' +
                        '</div>'
                    );
            })
            .on('mousemove', function(event) {
                var rect = container.getBoundingClientRect();
                var x = event.clientX - rect.left + 14;
                var y = event.clientY - rect.top - 18;
                tooltip.style('left', x + 'px').style('top', y + 'px');
            })
            .on('mouseleave', function(event, d) {
                var name = d.properties.ST_NM;
                if (window.selectedMapState !== name) {
                    d3.select(this)
                        .attr('stroke', '#cbd5e1')
                        .attr('stroke-width', 1);
                }
                tooltip.style('display', 'none');
            })
            .on('click', function(event, d) {
                window.selectStateDetails(d.properties.ST_NM);
            });
    };

    window.selectStateDetails = function(stName) {
        window.selectedMapState = stName;

        if (window.__indiaMapG) {
            window.__indiaMapG.selectAll('path.state-feature')
                .attr('stroke', function(d) {
                    return d.properties.ST_NM === stName ? '#047857' : '#cbd5e1';
                })
                .attr('stroke-width', function(d) {
                    return d.properties.ST_NM === stName ? 3 : 1;
                });
        }

        document.querySelectorAll('.leaderboard-item-row').forEach(function(row) {
            if (row.getAttribute('data-state-row') === stName) {
                row.classList.add('active');
            } else {
                row.classList.remove('active');
            }
        });

        var info = (window.__dashStateData && window.__dashStateData[stName]) || {
            name: stName,
            total_projects: 0,
            running_projects: 0,
            completed_projects: 0,
            benefited_peoples: 0,
            benefited_families: 0
        };

        var elTitle = document.getElementById('selectedStateTitle');
        var elBadge = document.getElementById('selectedStateProjects');
        var elRunning = document.getElementById('selectedStateRunning');
        var elCompleted = document.getElementById('selectedStateCompleted');
        var elPeoples = document.getElementById('selectedStatePeoples');
        var elFamilies = document.getElementById('selectedStateFamilies');

        if (elTitle) elTitle.textContent = info.name;
        if (elBadge) elBadge.textContent = info.total_projects;
        if (elRunning) elRunning.textContent = Number(info.running_projects).toLocaleString();
        if (elCompleted) elCompleted.textContent = Number(info.completed_projects).toLocaleString();
        if (elPeoples) elPeoples.textContent = Number(info.benefited_peoples).toLocaleString();
        if (elFamilies) elFamilies.textContent = Number(info.benefited_families).toLocaleString();

        try {
            let rootEl = document.querySelector('.role-dashboard-container');
            if (rootEl && window.Livewire) {
                let comp = window.Livewire.find(rootEl.getAttribute('wire:id'));
                if (comp) {
                    comp.set('selectedState', stName);
                }
            }
        } catch(e) {}
    };

    // Multi-event robust trigger system
    function triggerDashboardVisuals() {
        window.__dashChartData = @json($beneficiaryChartData);
        window.__dashStateData = @json($stateSummaryData ?? []);
        window.initBeneficiaryChart();
        window.initIndiaD3Map();
    }

    // Immediate & event-driven executions
    triggerDashboardVisuals();
    document.addEventListener('DOMContentLoaded', triggerDashboardVisuals);
    document.addEventListener('livewire:navigated', triggerDashboardVisuals);
    document.addEventListener('livewire:init', triggerDashboardVisuals);
    document.addEventListener('livewire:initialized', triggerDashboardVisuals);
    document.addEventListener('livewire:update', triggerDashboardVisuals);
    document.addEventListener('pageshow', triggerDashboardVisuals);
    document.addEventListener('turbo:load', triggerDashboardVisuals);
    document.addEventListener('pjax:end', triggerDashboardVisuals);
    window.addEventListener('resize', function() {
        if (window.initIndiaD3Map) window.initIndiaD3Map();
    });
    </script>
</div>
