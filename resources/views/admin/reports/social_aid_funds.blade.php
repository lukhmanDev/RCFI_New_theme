@extends('layouts.admin')

@section('title', 'Social Aid Fund Report')

@section('content')

    @if(!Auth::user() || !(Auth::user()->isSuperAdmin() || Auth::user()->isCoo() || Auth::user()->isHod() || Auth::user()->isSocialAid()))
        <div class="alert alert-danger" style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid var(--accent-red); color: #ff9999; padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; font-size: 1rem; font-weight: 600;">
            <i class="bx bx-error-circle" style="vertical-align: middle; margin-right: 0.5rem; font-size: 1.25rem;"></i>
            Unauthorized Action: Only Super Admin, COO, HOD, and Social Aid Manager can access the Social Aid Fund Report page.
        </div>
    @else

    <style>
        .report-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.03);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.25s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(15, 23, 42, 0.08);
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .stat-info h5 {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #64748b;
            margin: 0 0 0.25rem 0;
            font-weight: 700;
        }
        .stat-info h3 {
            font-size: 1.45rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
        }
        .stat-info span {
            font-size: 0.78rem;
            color: #64748b;
            font-weight: 500;
        }

        .filter-header-panel {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .filter-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        .filter-input {
            padding: 0.6rem 1rem;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 0.85rem;
            color: #1e293b;
            background-color: #f8fafc;
            outline: none;
            transition: border-color 0.2s;
        }
        .filter-input:focus {
            border-color: #10b981;
            background-color: #ffffff;
        }

        .custom-tab-btn {
            padding: 0.6rem 1.2rem;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 600;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s;
        }
        .custom-tab-btn.active {
            background: #10b981;
            color: #ffffff;
            border-color: #10b981;
        }
    </style>

    <!-- Page Title & Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="color: #0f172a; font-size: 1.75rem; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 0.6rem;">
                <i class="bx bxs-report" style="color: #10b981;"></i> Social Aid Fund Report
            </h1>
            <p style="color: #64748b; font-size: 0.88rem; margin: 0.25rem 0 0 0;">Super Admin Consolidated Fund Transfer Analytics & Financial Registry</p>
        </div>
        <div>
            <a href="{{ route('admin.reports.social_aid_funds', ['export' => 'csv']) }}" class="btn-custom" style="background: linear-gradient(135deg, #10b981, #059669); color: #ffffff; border: none; padding: 0.65rem 1.25rem; font-size: 0.88rem; border-radius: 10px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);">
                <i class="bx bx-download" style="font-size: 1.1rem;"></i> Export CSV Report
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="report-stats-grid">
        <!-- Total Funds -->
        <div class="stat-card">
            <div class="stat-info">
                <h5>Total Social Aid Funds</h5>
                <h3>₹{{ number_format($totalAmount, 2) }}</h3>
                <span>{{ $totalCount }} total transfers</span>
            </div>
            <div class="stat-icon" style="background-color: rgba(16, 185, 129, 0.1); color: #10b981;">
                <i class="bx bx-wallet"></i>
            </div>
        </div>

        <!-- Orphan Care -->
        <div class="stat-card">
            <div class="stat-info">
                <h5>Orphan Care</h5>
                <h3 style="color: #10b981;">₹{{ number_format($orphanAmount, 2) }}</h3>
                <span>{{ $orphanCount }} transfers</span>
            </div>
            <div class="stat-icon" style="background-color: rgba(16, 185, 129, 0.1); color: #10b981;">
                <i class="bx bx-child"></i>
            </div>
        </div>

        <!-- Differently Abled -->
        <div class="stat-card">
            <div class="stat-info">
                <h5>Differently Abled</h5>
                <h3 style="color: #d97706;">₹{{ number_format($daAmount, 2) }}</h3>
                <span>{{ $daCount }} transfers</span>
            </div>
            <div class="stat-icon" style="background-color: rgba(217, 119, 6, 0.1); color: #d97706;">
                <i class="bx bx-accessibility"></i>
            </div>
        </div>

        <!-- Family Aid -->
        <div class="stat-card">
            <div class="stat-info">
                <h5>Family Aid</h5>
                <h3 style="color: #059669;">₹{{ number_format($faAmount, 2) }}</h3>
                <span>{{ $faCount }} transfers</span>
            </div>
            <div class="stat-icon" style="background-color: rgba(5, 150, 105, 0.1); color: #059669;">
                <i class="bx bx-home-heart"></i>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-header-panel" style="flex-wrap: wrap; gap: 1rem;">
        <div class="filter-group" style="flex-wrap: wrap; gap: 0.5rem; align-items: center;">
            <input type="text" id="reportSearch" placeholder="Search by Agency, Beneficiary, Project ID..." class="filter-input" style="min-width: 240px;" onkeyup="filterReportTable()">
            
            <select id="categoryFilter" class="filter-input" onchange="filterReportTable()">
                <option value="">All Categories</option>
                <option value="orphan care">Orphan Care</option>
                <option value="differently abled">Differently Abled</option>
                <option value="family aid">Family Aid</option>
            </select>

            <select id="agencyFilter" class="filter-input" onchange="filterReportTable()">
                <option value="">All Agencies / Sponsors</option>
                @foreach($agencies as $ag)
                    <option value="{{ strtolower($ag) }}">{{ $ag }}</option>
                @endforeach
            </select>

            <select id="clusterFilter" class="filter-input" onchange="filterReportTable()">
                <option value="">All Clusters</option>
                @foreach($clusters as $cl)
                    <option value="{{ strtolower($cl) }}">{{ $cl }}</option>
                @endforeach
            </select>

            <!-- Date Wise Filter -->
            <div style="display: flex; align-items: center; gap: 0.35rem; background: #ffffff; border: 1px solid #cbd5e1; padding: 0.25rem 0.5rem; border-radius: 8px;">
                <span style="font-size: 0.8rem; font-weight: 600; color: #475569; white-space: nowrap;"><i class="bx bx-calendar" style="color: #10b981;"></i> Date:</span>
                <select id="dateTypeFilter" class="filter-input" style="padding: 0.3rem 0.4rem; font-size: 0.8rem; border-color: #cbd5e1; font-weight: 700; color: #10b981;" onchange="filterReportTable()" title="Select Date Field to Filter By">
                    <option value="transfer">Transfer Date</option>
                    <option value="created">Created Date</option>
                </select>
                <select id="datePresetFilter" class="filter-input" style="padding: 0.3rem 0.4rem; font-size: 0.8rem; border-color: #cbd5e1;" onchange="applyDatePreset(this.value)">
                    <option value="all">All Dates</option>
                    <option value="today">Today</option>
                    <option value="this_week">This Week</option>
                    <option value="this_month">This Month</option>
                    <option value="this_year">This Year</option>
                    <option value="custom">Custom Range</option>
                </select>
                <input type="date" id="fromDateFilter" class="filter-input" style="padding: 0.3rem 0.4rem; font-size: 0.8rem; width: 130px; border-color: #cbd5e1;" onchange="onManualDateChange()" title="From Date">
                <span style="font-size: 0.8rem; color: #94a3b8;">to</span>
                <input type="date" id="toDateFilter" class="filter-input" style="padding: 0.3rem 0.4rem; font-size: 0.8rem; width: 130px; border-color: #cbd5e1;" onchange="onManualDateChange()" title="To Date">
                <button type="button" onclick="resetDateFilters()" style="background: transparent; border: none; color: #64748b; font-size: 1.1rem; cursor: pointer; display: flex; align-items: center; padding: 0.2rem;" title="Reset Date Filter">
                    <i class="bx bx-refresh"></i>
                </button>
            </div>
        </div>

        <div class="filter-group">
            <button onclick="switchViewTab('all')" id="btnTabAll" class="custom-tab-btn active">
                <i class="bx bx-list-ul"></i> All Transactions ({{ $totalCount }})
            </button>
            <button onclick="switchViewTab('agency')" id="btnTabAgency" class="custom-tab-btn">
                <i class="bx bx-buildings"></i> Agency Breakdown
            </button>
        </div>
    </div>

    <!-- All Transactions Panel -->
    <div id="panelTransactions" class="panel" style="width: 100%;">
        <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2 class="panel-title" style="font-size: 1.1rem; color: #1e293b; font-weight: 700;">Fund Transfer Records</h2>
            <span id="recordCounter" style="font-size: 0.82rem; color: #64748b; font-weight: 600;">Showing {{ $totalCount }} records</span>
        </div>

        <div style="overflow-x: auto;">
            <table class="table-custom" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Transfer Date</th>
                        <th>Created Date</th>
                        <th>Category</th>
                        <th>Cluster</th>
                        <th>Agency Project No</th>
                        <th>RCFI ID</th>
                        <th>Beneficiary / Applicant</th>
                        <th>Agency / Sponsor</th>
                        <th style="text-align: right;">Amount (₹)</th>
                    </tr>
                </thead>
                <tbody id="reportTableBody">
                    @forelse($allFunds as $index => $fund)
                        @php
                            $catBadgeColor = '#10b981';
                            $catBg = 'rgba(16, 185, 129, 0.1)';
                            if ($fund['category_slug'] === 'orphan-care') {
                                $catBadgeColor = '#10b981';
                                $catBg = 'rgba(16, 185, 129, 0.1)';
                            } elseif ($fund['category_slug'] === 'differently-abled') {
                                $catBadgeColor = '#d97706';
                                $catBg = 'rgba(217, 119, 6, 0.1)';
                            } elseif ($fund['category_slug'] === 'family-aid') {
                                $catBadgeColor = '#059669';
                                $catBg = 'rgba(5, 150, 105, 0.1)';
                            }
                            $searchStr = strtolower($fund['category'] . ' ' . ($fund['cluster'] ?? '') . ' ' . $fund['agency_project_no'] . ' ' . $fund['project_id'] . ' ' . $fund['applicant_name'] . ' ' . $fund['agency'] . ' ' . $fund['formatted_date'] . ' ' . $fund['formatted_created_date']);
                        @endphp
                        <tr class="fund-row" data-search="{{ $searchStr }}" data-category="{{ strtolower($fund['category']) }}" data-cluster="{{ strtolower($fund['cluster'] ?? '') }}" data-agency="{{ strtolower($fund['agency']) }}" data-date="{{ $fund['date'] }}" data-created-date="{{ $fund['created_date'] }}" data-amount="{{ $fund['amount'] }}">
                            <td style="color: #64748b; font-size: 0.82rem;">{{ $index + 1 }}</td>
                            <td style="font-weight: 600; color: #334155;">{{ $fund['formatted_date'] }}</td>
                            <td style="font-size: 0.82rem; color: #64748b; font-weight: 500;">{{ $fund['formatted_created_date'] }}</td>
                            <td>
                                <span style="background-color: {{ $catBg }}; color: {{ $catBadgeColor }}; padding: 0.25rem 0.6rem; border-radius: 6px; font-size: 0.75rem; font-weight: 700;">
                                    {{ $fund['category'] }}
                                </span>
                            </td>
                            <td>
                                <span style="background-color: #f8fafc; color: #475569; border: 1px solid #e2e8f0; padding: 0.2rem 0.55rem; border-radius: 4px; font-size: 0.78rem; font-weight: 600; white-space: nowrap;">
                                    <i class="bx bx-map-pin" style="vertical-align: middle; color: #8b5cf6; margin-right: 2px;"></i> {{ $fund['cluster'] ?? 'N/A' }}
                                </span>
                            </td>
                            <td style="font-weight: 700; color: #10b981;">
                                {{ $fund['agency_project_no'] ?? 'N/A' }}
                            </td>
                            <td>
                                 @php
                                     $fundDataJson = json_encode([
                                         'project_id' => $fund['project_id'],
                                         'agency_project_no' => $fund['agency_project_no'],
                                         'applicant_name' => $fund['applicant_name'],
                                         'applicant_id' => $fund['applicant_id'] ?? 'N/A',
                                         'category' => $fund['category'],
                                         'category_slug' => $fund['category_slug'],
                                         'cluster' => $fund['cluster'] ?? 'N/A',
                                         'agency' => $fund['agency'],
                                         'sponsor' => $fund['sponsor'] ?? $fund['agency'],
                                         'photo' => $fund['photo'] ?? null,
                                         'theme' => $fund['theme'] ?? 'N/A',
                                         'subtheme' => $fund['subtheme'] ?? 'N/A',
                                         'activity' => $fund['activity'] ?? 'N/A',
                                         'project_spec' => $fund['project_spec'] ?? 'N/A',
                                         'available_budget' => $fund['available_budget'] ?? 0,
                                         'location' => $fund['location'] ?? 'N/A',
                                         'amount' => $fund['amount'],
                                         'formatted_date' => $fund['formatted_date'],
                                         'project_db_id' => $fund['project_db_id'] ?? null,
                                         'project_url' => $fund['project_db_id'] ? route('projects.show', $fund['project_db_id']) . '?type=' . urlencode($fund['category']) : null,
                                     ]);
                                 @endphp
                                 <a href="{{ $fund['project_db_id'] ? route('projects.show', $fund['project_db_id']) . '?type=' . urlencode($fund['category']) : 'javascript:void(0)' }}"
                                    class="project-id-link"
                                    data-fund="{{ e($fundDataJson) }}"
                                    onmouseenter="showProjectHoverCard(event, this)"
                                    onmouseleave="hideProjectHoverCard()"
                                    onclick="if(event.ctrlKey || event.metaKey) return true; event.preventDefault(); openProjectDetailsModal(event, JSON.parse(this.getAttribute('data-fund')));"
                                    style="color: #10b981; font-weight: 700; text-decoration: none; cursor: pointer;">
                                     <i class="bx bx-folder-open" style="font-size: 0.85rem; vertical-align: middle; margin-right: 2px;"></i>
                                     {{ $fund['project_id'] }}
                                 </a>
                            </td>
                            <td style="font-weight: 600; color: #1e293b;">{{ $fund['applicant_name'] }}</td>
                            <td>
                                <span style="background-color: #f1f5f9; color: #334155; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">
                                    <i class="bx bx-building-house" style="vertical-align: middle; color: #64748b;"></i> {{ $fund['agency'] }}
                                </span>
                            </td>
                            <td style="text-align: right; font-weight: 800; color: #059669; font-size: 0.95rem;">
                                ₹{{ number_format($fund['amount'], 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 2.5rem; color: #64748b;">No fund transfer records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Agency Breakdown Panel -->
    <div id="panelAgency" class="panel" style="width: 100%; display: none;">
        <div class="panel-header">
            <h2 class="panel-title" style="font-size: 1.1rem; color: #1e293b; font-weight: 700;">Agency Funding Summary</h2>
        </div>

        <div style="overflow-x: auto;">
            <table class="table-custom" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Agency / Sponsor Name</th>
                        <th style="text-align: center;">Total Transfers</th>
                        <th>Supported Categories</th>
                        <th style="text-align: right;">Total Contribution Amount (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($agencySummary as $idx => $agItem)
                        <tr>
                            <td style="color: #64748b; font-size: 0.82rem;">{{ $idx + 1 }}</td>
                            <td style="font-weight: 700; color: #1e293b;">
                                <i class="bx bx-buildings" style="color: #4f46e5; margin-right: 0.4rem;"></i> {{ $agItem['agency'] }}
                            </td>
                            <td style="text-align: center;">
                                <span style="background-color: #f1f5f9; color: #1e293b; padding: 0.2rem 0.6rem; border-radius: 12px; font-weight: 700; font-size: 0.8rem;">
                                    {{ $agItem['count'] }}
                                </span>
                            </td>
                            <td style="font-size: 0.85rem; color: #475569;">{{ $agItem['categories'] }}</td>
                            <td style="text-align: right; font-weight: 800; color: #059669; font-size: 0.95rem;">
                                ₹{{ number_format($agItem['total_amount'], 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2.5rem; color: #64748b;">No agency summary records available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Client-side Interactive Filter Script -->
    <script>
        function applyDatePreset(preset) {
            const fromInput = document.getElementById('fromDateFilter');
            const toInput = document.getElementById('toDateFilter');
            const today = new Date();
            
            function formatDate(d) {
                const year = d.getFullYear();
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            if (preset === 'all') {
                fromInput.value = '';
                toInput.value = '';
            } else if (preset === 'today') {
                const formattedToday = formatDate(today);
                fromInput.value = formattedToday;
                toInput.value = formattedToday;
            } else if (preset === 'this_week') {
                const dayOfWeek = today.getDay();
                const distanceToMon = (dayOfWeek === 0 ? 6 : dayOfWeek - 1);
                const mon = new Date(today);
                mon.setDate(today.getDate() - distanceToMon);
                fromInput.value = formatDate(mon);
                toInput.value = formatDate(today);
            } else if (preset === 'this_month') {
                const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                fromInput.value = formatDate(firstDay);
                toInput.value = formatDate(lastDay);
            } else if (preset === 'this_year') {
                const firstDay = new Date(today.getFullYear(), 0, 1);
                const lastDay = new Date(today.getFullYear(), 11, 31);
                fromInput.value = formatDate(firstDay);
                toInput.value = formatDate(lastDay);
            }
            
            filterReportTable();
        }

        function onManualDateChange() {
            document.getElementById('datePresetFilter').value = 'custom';
            filterReportTable();
        }

        function resetDateFilters() {
            document.getElementById('datePresetFilter').value = 'all';
            document.getElementById('fromDateFilter').value = '';
            document.getElementById('toDateFilter').value = '';
            filterReportTable();
        }

        function filterReportTable() {
            const searchVal = document.getElementById('reportSearch').value.toLowerCase();
            const catVal = document.getElementById('categoryFilter').value.toLowerCase();
            const agencyVal = document.getElementById('agencyFilter').value.toLowerCase();
            const clusterVal = document.getElementById('clusterFilter') ? document.getElementById('clusterFilter').value.toLowerCase() : '';
            const dateType = document.getElementById('dateTypeFilter') ? document.getElementById('dateTypeFilter').value : 'transfer';
            const fromDate = document.getElementById('fromDateFilter').value;
            const toDate = document.getElementById('toDateFilter').value;

            const rows = document.querySelectorAll('#reportTableBody .fund-row');
            let visibleCount = 0;
            let visibleTotal = 0;

            rows.forEach(row => {
                const searchData = row.getAttribute('data-search') || '';
                const categoryData = row.getAttribute('data-category') || '';
                const agencyData = row.getAttribute('data-agency') || '';
                const clusterData = row.getAttribute('data-cluster') || '';
                const rowDate = (dateType === 'created')
                    ? (row.getAttribute('data-created-date') || row.getAttribute('data-date') || '')
                    : (row.getAttribute('data-date') || '');
                const rowAmount = parseFloat(row.getAttribute('data-amount')) || 0;

                const matchesSearch = !searchVal || searchData.includes(searchVal);
                const matchesCat = !catVal || categoryData.includes(catVal);
                const matchesAgency = !agencyVal || agencyData.includes(agencyVal);
                const matchesCluster = !clusterVal || clusterData.includes(clusterVal);

                let matchesDate = true;
                if (fromDate && rowDate) {
                    matchesDate = matchesDate && (rowDate >= fromDate);
                }
                if (toDate && rowDate) {
                    matchesDate = matchesDate && (rowDate <= toDate);
                }

                if (matchesSearch && matchesCat && matchesAgency && matchesCluster && matchesDate) {
                    row.style.display = '';
                    visibleCount++;
                    visibleTotal += rowAmount;
                } else {
                    row.style.display = 'none';
                }
            });

            const formattedTotal = '₹' + visibleTotal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('recordCounter').innerHTML = `Showing <strong>${visibleCount}</strong> records | Filtered Total: <strong style="color: #059669;">${formattedTotal}</strong>`;

            const table = document.querySelector('#panelTransactions table.table-custom');
            if (table && table.pagerUpdate) {
                table.pagerUpdate();
            }
        }

        function switchViewTab(tab) {
            const panelTx = document.getElementById('panelTransactions');
            const panelAg = document.getElementById('panelAgency');
            const btnAll = document.getElementById('btnTabAll');
            const btnAg = document.getElementById('btnTabAgency');

            if (tab === 'all') {
                panelTx.style.display = 'block';
                panelAg.style.display = 'none';
                btnAll.classList.add('active');
                btnAg.classList.remove('active');
            } else {
                panelTx.style.display = 'none';
                panelAg.style.display = 'block';
                btnAg.classList.add('active');
                btnAll.classList.remove('active');
            }
        }

        let hoverCardElement = null;

        function showProjectHoverCard(event, linkElem) {
            hideProjectHoverCard();
            try {
                const data = JSON.parse(linkElem.getAttribute('data-fund'));
                if (!data) return;

                const card = document.createElement('div');
                card.id = 'project-hover-popover';
                card.style.cssText = `
                    position: fixed;
                    z-index: 99999;
                    width: 320px;
                    background: #ffffff;
                    border: 1px solid #e2e8f0;
                    border-radius: 12px;
                    padding: 1.25rem;
                    box-shadow: 0 12px 28px -5px rgba(15, 23, 42, 0.18), 0 8px 10px -6px rgba(15, 23, 42, 0.1);
                    font-family: inherit;
                    pointer-events: none;
                    transition: opacity 0.15s ease, transform 0.15s ease;
                    opacity: 0;
                    transform: translateY(6px);
                `;

                const categoryColors = {
                    'orphan-care': { bg: 'rgba(2, 132, 199, 0.1)', text: '#0284c7' },
                    'differently-abled': { bg: 'rgba(217, 119, 6, 0.1)', text: '#d97706' },
                    'family-aid': { bg: 'rgba(5, 150, 105, 0.1)', text: '#059669' }
                };
                const catTheme = categoryColors[data.category_slug] || { bg: 'rgba(79, 70, 229, 0.1)', text: '#4f46e5' };

                const photoHtml = data.photo 
                    ? `<img src="${data.photo}" alt="${data.applicant_name}" style="width: 52px; height: 52px; border-radius: 50%; object-fit: cover; border: 2px solid ${catTheme.text}; flex-shrink: 0;" />`
                    : `<div style="width: 52px; height: 52px; border-radius: 50%; background: ${catTheme.bg}; color: ${catTheme.text}; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.1rem; border: 2px solid ${catTheme.text}; flex-shrink: 0;"><i class="bx bx-user"></i></div>`;

                const formattedBudget = data.available_budget ? '₹' + Number(data.available_budget).toLocaleString('en-IN', {minimumFractionDigits: 2}) : 'N/A';

                card.innerHTML = `
                    <div style="display: flex; gap: 0.85rem; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.85rem; margin-bottom: 0.85rem;">
                        ${photoHtml}
                        <div style="min-width: 0;">
                            <span style="background: ${catTheme.bg}; color: ${catTheme.text}; padding: 0.15rem 0.5rem; border-radius: 4px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">${data.category}</span>
                            <h4 style="margin: 0.25rem 0 0 0; font-size: 0.95rem; font-weight: 700; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${data.applicant_name}</h4>
                            <span style="font-size: 0.78rem; font-weight: 700; color: #4f46e5;">ID: ${data.project_id}</span>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem 0.75rem; font-size: 0.8rem; color: #475569;">
                        <div>
                            <div style="font-size: 0.7rem; color: #94a3b8; font-weight: 600; text-transform: uppercase;">Agency Project No</div>
                            <div style="font-weight: 700; color: #0284c7;">${data.agency_project_no || 'N/A'}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.7rem; color: #94a3b8; font-weight: 600; text-transform: uppercase;">Cluster</div>
                            <div style="font-weight: 700; color: #8b5cf6;">${data.cluster || 'N/A'}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.7rem; color: #94a3b8; font-weight: 600; text-transform: uppercase;">Sponsor / Agency</div>
                            <div style="font-weight: 600; color: #334155; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${data.agency || 'N/A'}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.7rem; color: #94a3b8; font-weight: 600; text-transform: uppercase;">Available Budget</div>
                            <div style="font-weight: 700; color: #059669;">${formattedBudget}</div>
                        </div>
                    </div>
                    <div style="margin-top: 0.75rem; padding-top: 0.6rem; border-top: 1px dashed #e2e8f0; font-size: 0.78rem; color: #64748b; display: flex; justify-content: space-between; align-items: center;">
                        <span><i class="bx bx-map" style="color: #94a3b8;"></i> ${data.location || 'N/A'}</span>
                        <span style="font-size: 0.7rem; color: #6366f1; font-weight: 600;">Click for details &rarr;</span>
                    </div>
                `;

                document.body.appendChild(card);
                hoverCardElement = card;

                const rect = linkElem.getBoundingClientRect();
                let top = rect.bottom + 8;
                let left = rect.left;

                if (left + 330 > window.innerWidth) {
                    left = window.innerWidth - 340;
                }
                if (top + card.offsetHeight > window.innerHeight) {
                    top = rect.top - card.offsetHeight - 8;
                }

                card.style.top = top + 'px';
                card.style.left = left + 'px';

                requestAnimationFrame(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                });
            } catch(e) {
                console.error(e);
            }
        }

        function hideProjectHoverCard() {
            if (hoverCardElement) {
                hoverCardElement.remove();
                hoverCardElement = null;
            }
        }

        function openProjectDetailsModal(event, data) {
            hideProjectHoverCard();
            
            let modal = document.getElementById('projectDetailsHoverModal');
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'projectDetailsHoverModal';
                modal.style.cssText = `
                    position: fixed; inset: 0; z-index: 999999;
                    background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);
                    display: flex; align-items: center; justify-content: center; padding: 1.5rem;
                `;
                modal.onclick = function(e) { if (e.target === modal) closeProjectDetailsModal(); };
                document.body.appendChild(modal);
            }

            const categoryColors = {
                'orphan-care': { bg: 'rgba(2, 132, 199, 0.15)', text: '#0284c7', border: '#0284c7' },
                'differently-abled': { bg: 'rgba(217, 119, 6, 0.15)', text: '#d97706', border: '#d97706' },
                'family-aid': { bg: 'rgba(5, 150, 105, 0.15)', text: '#059669', border: '#059669' }
            };
            const catTheme = categoryColors[data.category_slug] || { bg: 'rgba(79, 70, 229, 0.15)', text: '#4f46e5', border: '#4f46e5' };

            const photoHtml = data.photo 
                ? `<img src="${data.photo}" alt="${data.applicant_name}" style="width: 100px; height: 100px; border-radius: 12px; object-fit: cover; border: 3px solid ${catTheme.border}; box-shadow: 0 4px 10px rgba(0,0,0,0.1);" />`
                : `<div style="width: 100px; height: 100px; border-radius: 12px; background: ${catTheme.bg}; color: ${catTheme.text}; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 2.2rem; border: 3px solid ${catTheme.border};"><i class="bx bx-user"></i></div>`;

            const formattedBudget = data.available_budget ? '₹' + Number(data.available_budget).toLocaleString('en-IN', {minimumFractionDigits: 2}) : 'N/A';
            const formattedAmount = data.amount ? '₹' + Number(data.amount).toLocaleString('en-IN', {minimumFractionDigits: 2}) : 'N/A';

            modal.innerHTML = `
                <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; width: 100%; max-width: 520px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); overflow: hidden; animation: popIn 0.2s ease;">
                    <div style="background: linear-gradient(135deg, #1e293b, #0f172a); color: #ffffff; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 0.6rem;">
                            <span style="background: ${catTheme.bg}; color: ${catTheme.text}; padding: 0.2rem 0.6rem; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">${data.category}</span>
                            <span style="font-weight: 700; font-size: 1.05rem; color: #a5b4fc;">${data.project_id}</span>
                        </div>
                        <button onclick="closeProjectDetailsModal()" style="background: transparent; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer; display: flex; align-items: center; justify-content: center; border-radius: 6px; padding: 0.2rem;" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#94a3b8'">&times;</button>
                    </div>

                    <div style="padding: 1.5rem;">
                        <div style="display: flex; gap: 1.25rem; align-items: center; margin-bottom: 1.5rem; background: #f8fafc; border: 1px solid #f1f5f9; padding: 1rem; border-radius: 12px;">
                            ${photoHtml}
                            <div>
                                <h3 style="margin: 0 0 0.35rem 0; font-size: 1.15rem; font-weight: 800; color: #0f172a;">${data.applicant_name}</h3>
                                <div style="font-size: 0.85rem; color: #64748b; margin-bottom: 0.25rem;"><i class="bx bx-hash"></i> Agency Project No: <strong style="color: #0284c7;">${data.agency_project_no || 'N/A'}</strong></div>
                                <div style="font-size: 0.85rem; color: #64748b;"><i class="bx bx-id-card"></i> Applicant ID: <strong style="color: #334155;">${data.applicant_id || 'N/A'}</strong></div>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem; font-size: 0.85rem;">
                            <div style="background: #ffffff; border: 1px solid #e2e8f0; padding: 0.75rem; border-radius: 8px;">
                                <span style="color: #94a3b8; font-size: 0.75rem; display: block; font-weight: 600; text-transform: uppercase;">Agency / Sponsor</span>
                                <strong style="color: #1e293b; font-size: 0.9rem;">${data.agency || 'N/A'}</strong>
                            </div>
                            <div style="background: #ffffff; border: 1px solid #e2e8f0; padding: 0.75rem; border-radius: 8px;">
                                <span style="color: #94a3b8; font-size: 0.75rem; display: block; font-weight: 600; text-transform: uppercase;">Cluster</span>
                                <strong style="color: #8b5cf6; font-size: 0.9rem;">${data.cluster || 'N/A'}</strong>
                            </div>
                            <div style="background: #ffffff; border: 1px solid #e2e8f0; padding: 0.75rem; border-radius: 8px;">
                                <span style="color: #94a3b8; font-size: 0.75rem; display: block; font-weight: 600; text-transform: uppercase;">Available Budget</span>
                                <strong style="color: #059669; font-size: 0.9rem;">${formattedBudget}</strong>
                            </div>
                            <div style="background: #ffffff; border: 1px solid #e2e8f0; padding: 0.75rem; border-radius: 8px;">
                                <span style="color: #94a3b8; font-size: 0.75rem; display: block; font-weight: 600; text-transform: uppercase;">Location</span>
                                <strong style="color: #334155; font-size: 0.85rem;">${data.location || 'N/A'}</strong>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; background: #eef2ff; border: 1px solid #e0e7ff; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                            <div>
                                <span style="color: #6366f1; font-size: 0.75rem; font-weight: 600; display: block;">Transferred Amount (${data.formatted_date})</span>
                                <strong style="color: #4338ca; font-size: 1.1rem;">${formattedAmount}</strong>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                            <button onclick="closeProjectDetailsModal()" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.85rem; font-weight: 600; cursor: pointer;">Close</button>
                            ${data.project_url ? `<a href="${data.project_url}" style="background: #4f46e5; color: #ffffff; border: none; padding: 0.5rem 1.25rem; border-radius: 8px; font-size: 0.85rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">Go To Application / Project <i class="bx bx-right-arrow-alt"></i></a>` : ''}
                        </div>
                    </div>
                </div>
            `;
            modal.style.display = 'flex';
        }

        function closeProjectDetailsModal() {
            const modal = document.getElementById('projectDetailsHoverModal');
            if (modal) modal.style.display = 'none';
        }
    </script>

    @endif

@endsection
