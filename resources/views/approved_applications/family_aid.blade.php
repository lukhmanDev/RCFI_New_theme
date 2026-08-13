@extends('layouts.admin')

@section('title', 'Approved Family Aid Applications')

@section('content')

    <!-- Back Button Header -->
    <div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
        <a href="{{ route('applications.approved.index') }}" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); padding: 0.5rem 1rem;">
            <i class="bx bx-left-arrow-alt"></i> Back to Approved Dashboard
        </a>
            </div>

    <!-- Success & Error Alert Panels -->
    @if (session('success'))
        <div class="alert alert-success" style="background-color: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent-green); color: #047857; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 500;">
            {{ session('success') }}
        </div>
    @endif

    <div class="panel" style="width: 100%;">
        <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2 class="panel-title">Approved Records</h2>
        </div>
        
        <!-- Filter & Export Toolbar -->
        @include('approved_applications.partials.filter_toolbar')

        <div style="overflow-x: auto;">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Application ID</th>
                        <th>Name</th>
                        <th>Father Name</th>
                        <th>Mother Name</th>
                        <th>Location</th>
                        <th>District</th>
                        <th>Sponsor Status</th>
                        <th style="text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $appItem)
                        @php
                            $meta = $appItem->meta ?? [];
                            $appYear = !empty($appItem->created_at) ? date('y', strtotime($appItem->created_at)) : '24';
                            $appId = 'APLRCFI' . $appYear . 'FA' . str_pad($appItem->id, 5, '0', STR_PAD_LEFT);
                            $project = $projectsMap[$appItem->id] ?? null;
                            
                            $searchTerms = [
                                $appId,
                                $appItem->applicant_name ?? '',
                                $appItem->place ?? '',
                                $appItem->village ?? $appItem->town ?? '',
                                $appItem->panchayat ?? $appItem->panchayath ?? '',
                                $appItem->status ?? '',
                                $appItem->details ?? '',
                            ];
                            if (is_array($meta)) {
                                foreach ($meta as $val) {
                                    if (is_scalar($val)) {
                                        $searchTerms[] = (string)$val;
                                    }
                                }
                            }
                            if ($project) {
                                $searchTerms[] = $project->project_id ?? '';
                                $searchTerms[] = $project->status ?? '';
                                if ($project->donor) {
                                    $searchTerms[] = $project->donor->name ?? '';
                                }
                                if ($project->projectManager) {
                                    $searchTerms[] = $project->projectManager->name ?? '';
                                }
                            }
                            $searchStr = strtolower(implode(' ', array_filter($searchTerms)));
                        @endphp
                        <tr class="app-row" data-search="{{ $searchStr }}" data-cluster="{{ $appItem->cluster_id ?? '' }}" data-sponsor="{{ strtolower($appItem->sponsor_status ?? 'not sponsored') }}" data-place="{{ $appItem->place ?? '' }}" onclick="openDetailsModal({{ $appItem->id }})">
                            <td style="font-weight: 600;">
                                <a href="javascript:void(0)" onclick="event.stopPropagation(); openDetailsModal({{ $appItem->id }})" style="color: var(--accent-cyan); font-weight: 600; text-decoration: none; cursor: pointer;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'" title="View Application Details">
                                    {{ $appId }}
                                </a>
                            </td>
                            <td title="{{ $appItem->applicant_name }}">{{ \Illuminate\Support\Str::limit($appItem->applicant_name, 15, '...') }}</td>
                            <td title="{{ $meta['father_name'] ?? '-' }}">{{ \Illuminate\Support\Str::limit($meta['father_name'] ?? '-', 15, '...') }}</td>
                            <td title="{{ $meta['mother_name'] ?? '-' }}">{{ \Illuminate\Support\Str::limit($meta['mother_name'] ?? '-', 15, '...') }}</td>
                            <td title="{{ $meta['location'] ?? '-' }}">{{ \Illuminate\Support\Str::limit($meta['location'] ?? '-', 15, '...') }}</td>
                            <td title="{{ $appItem->district ?? $meta['district'] ?? $meta['locality_district'] ?? '-' }}">{{ \Illuminate\Support\Str::limit($appItem->district ?? $meta['district'] ?? $meta['locality_district'] ?? '-', 15, '...') }}</td>
                            <td>
                                @if(($appItem->sponsor_status ?? 'Not Sponsored') === 'Sponsored')
                                    <span style="background-color: #d1fae5; color: #065f46; border: 1px solid rgba(16, 185, 129, 0.3); padding: 0.3rem 0.65rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem; white-space: nowrap;">
                                        <i class="bx bx-check-circle" style="font-size: 0.85rem;"></i> Sponsored
                                    </span>
                                @else
                                    <span style="background-color: #fef3c7; color: #92400e; border: 1px solid rgba(245, 158, 11, 0.3); padding: 0.3rem 0.65rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem; white-space: nowrap;">
                                        <i class="bx bx-time-five" style="font-size: 0.85rem;"></i> Not Sponsored
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: center; white-space: nowrap;" onclick="event.stopPropagation()">
                                @if(($appItem->sponsor_status ?? 'Not Sponsored') === 'Sponsored')
                                    @if(Auth::user()->isSuperAdmin())
                                        <a href="#" onclick="event.preventDefault(); event.stopPropagation(); handleToggleSponsor(event, {{ $appItem->id }}, 'Sponsored', 'family-aid')" style="background: #ffffff; color: #dc2626 !important; border: 1px solid #fca5a5; padding: 0.35rem 0.75rem; font-size: 0.78rem; font-weight: 600; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 0.35rem; text-decoration: none; min-width: 110px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: all 0.2s;" title="Mark as Not Sponsored (Super Admin Only)">
                                            <i class="bx bx-x" style="font-size: 0.95rem;"></i> Un-sponsor
                                        </a>
                                    @else
                                        <span style="color: #10b981; font-size: 0.78rem; font-weight: 600; padding: 0.35rem 0.75rem; background: rgba(16, 185, 129, 0.1); border-radius: 6px; display: inline-flex; align-items: center; gap: 0.35rem;">
                                            <i class="bx bx-check-double"></i> Sponsored
                                        </span>
                                    @endif
                                @else
                                    @if(Auth::user()->canManageSponsorship())
                                        <a href="#" onclick="event.preventDefault(); event.stopPropagation(); handleToggleSponsor(event, {{ $appItem->id }}, 'Not Sponsored', 'family-aid')" style="background: #10b981; color: #ffffff !important; border: 1px solid #059669; padding: 0.35rem 0.75rem; font-size: 0.78rem; font-weight: 600; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 0.35rem; text-decoration: none; min-width: 110px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: all 0.2s;" title="Mark as Sponsored">
                                            <i class="bx bx-check" style="font-size: 0.95rem;"></i> Sponsor
                                        </a>
                                    @else
                                        <span style="color: var(--text-muted); font-size: 0.85rem; font-style: italic;">No Action</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 2rem; color: var(--text-muted);">No approved applications registered in this category yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Details Modal Dialog -->
    <div id="detailsAppModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.75); display: none; align-items: center; justify-content: center; z-index: 1000; overflow-y: auto;" onclick="closeDetailsModal()">
        <div class="panel" style="width: 100%; max-width: 800px; margin: 2rem auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #2a3547; max-height: 90vh; overflow-y: auto;" onclick="event.stopPropagation()">
            
            <button onclick="closeDetailsModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; z-index: 10;"><i class="bx bx-x"></i></button>
            
            <div class="panel-header" style="margin-bottom: 1.5rem;">
                <h2 class="panel-title" style="font-size: 1.25rem;"><i class="bx bx-detail" style="vertical-align: middle; margin-right: 0.5rem; color: var(--accent-green);"></i> Application Details</h2>
            </div>

            <!-- Details content dynamically populated by JS -->
            <div id="details_content" style="color: var(--text-main); font-size: 0.9rem;">
                <!-- Tables populated by script -->
            </div>
            
            <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--panel-border); padding-top: 1.5rem; flex-wrap: wrap;">
                @if(Auth::user()->hasAdminAccess())
                    <span id="modal_status_actions" style="display: inline-flex; gap: 0.75rem;"></span>
                @endif
                <button onclick="closeDetailsModal()" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); padding: 0.6rem 1.5rem;">Close Details</button>
            </div>
        </div>
    </div>

    <!-- Script Block -->
    <script>
        var applicationsMap = @json($applications->keyBy('id'));
        var currentDetailsAppItem = null;

        function openDetailsModal(appItem, isProjectApproved = false) {
            if (typeof appItem === 'number' || typeof appItem === 'string') {
                appItem = applicationsMap[appItem] || appItem;
            }
            if (!appItem || typeof appItem !== 'object') return;
            currentDetailsAppItem = appItem;
            
            // Populate status actions in the modal footer dynamically
            const statusActionsContainer = document.getElementById('modal_status_actions');
            if (statusActionsContainer) {
                let statusHtml = '';

                if (appItem.sponsor_status === 'Sponsored') {
                    @if(Auth::user()->isSuperAdmin())
                        statusHtml += `
                            <button type="button" onclick="event.preventDefault(); event.stopPropagation(); handleToggleSponsor(event, ${appItem.id}, 'Sponsored', 'family-aid')" class="btn-custom" style="background: transparent; color: #ef4444; border: 1px solid #ef4444; padding: 0.6rem 1.5rem; display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600; cursor: pointer;">
                                <i class="bx bx-x-circle"></i> Mark as Un-sponsored
                            </button>
                        `;
                    @endif
                } else {
                    @if(Auth::user()->canManageSponsorship())
                        statusHtml += `
                            <button type="button" onclick="event.preventDefault(); event.stopPropagation(); openSponsorDateModal(${appItem.id}, 'family-aid')" class="btn-custom" style="background: linear-gradient(135deg, #2ecc71, #27ae60); border: none; color: #ffffff; padding: 0.6rem 1.5rem; display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600; cursor: pointer;">
                                <i class="bx bx-check-circle"></i> Mark as Sponsored
                            </button>
                        `;
                    @endif
                }
                statusActionsContainer.innerHTML = statusHtml;
            }

            const meta = appItem.meta || {};
            const formatVal = (val) => val ? val : '<span style="color: var(--text-muted); font-style: italic;">N/A</span>';
            
            let html = `
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">1. Applicant Profile</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px;">Applicant Name:</td><td>${formatVal(appItem.applicant_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Father Name:</td><td>${formatVal(meta.father_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Mother Name:</td><td>${formatVal(meta.mother_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Place:</td><td>${formatVal(meta.location)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Sponsor Status:</td><td>
                                ${appItem.sponsor_status === 'Sponsored'
                                    ? '<span style="background-color: rgba(16, 185, 129, 0.2); color: var(--accent-green); padding: 0.15rem 0.4rem; border-radius: 4px; font-weight: 600;">Sponsored</span>'
                                    : '<span style="background-color: rgba(245, 158, 11, 0.2); color: #f59e0b; padding: 0.15rem 0.4rem; border-radius: 4px; font-weight: 600;">Not Sponsored</span>'}
                            </td></tr>
                        </table>
                    </div>
                </div>

                <!-- Cluster & Agency Details -->
                <div style="margin-top: 1.5rem; background: rgba(255,255,255,0.02); border: 1px solid var(--panel-border); padding: 1.25rem; border-radius: 8px;">
                    <h4 style="color: var(--accent-green); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">
                        Cluster &amp; Agency Number Details
                    </h4>
                    
                    <div id="modal-cluster-container">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 2rem;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);">
                                    <td style="padding: 0.5rem 0; font-weight: 600; width: 160px; color: var(--text-muted);">Assigned Cluster:</td>
                                    <td id="modal-cluster-display-name" style="font-weight: 600; color: #ffffff;">
                                        ${appItem.cluster ? `${appItem.cluster.name} (${appItem.cluster.code})` : '<span style="color: var(--text-muted); font-style: italic;">Not assigned</span>'}
                                    </td>
                                </tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);">
                                    <td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Agency Number:</td>
                                    <td id="modal-agency-display-number" style="font-weight: 600; color: #ffffff;">
                                        ${appItem.agency_number ? appItem.agency_number : '<span style="color: var(--text-muted); font-style: italic;">Not set</span>'}
                                    </td>
                                </tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);">
                                    <td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Agency Name (Donor):</td>
                                    <td id="modal-agency-display-name" style="font-weight: 600; color: #ffffff;">
                                        ${meta.agency_name ? meta.agency_name : '<span style="color: var(--text-muted); font-style: italic;">Not set</span>'}
                                    </td>
                                </tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);">
                                    <td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Application Date:</td>
                                    <td id="modal-agency-display-date" style="font-weight: 600; color: #ffffff;">
                                        ${meta.application_date ? meta.application_date : '<span style="color: var(--text-muted); font-style: italic;">Not set</span>'}
                                    </td>
                                </tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);">
                                    <td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Sponsor Status:</td>
                                    <td style="font-weight: 600; color: #ffffff;">
                                        ${appItem.sponsor_status === 'Sponsored'
                                            ? '<span style="background-color: rgba(16, 185, 129, 0.2); color: var(--accent-green); padding: 0.15rem 0.4rem; border-radius: 4px; font-weight: 600;">Sponsored</span>'
                                            : '<span style="background-color: rgba(245, 158, 11, 0.2); color: #f59e0b; padding: 0.15rem 0.4rem; border-radius: 4px; font-weight: 600;">Not Sponsored</span>'}
                                    </td>
                                </tr>
                            </table>
                            
                            @if(Auth::user() && Auth::user()->isSuperAdmin())
                            <button onclick="toggleClusterEditForm()" class="btn-custom" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.25rem; cursor: pointer;">
                                <i class="bx bx-edit"></i> Edit
                            </button>
                            @endif
                        </div>
                    </div>

                    @if(Auth::user() && Auth::user()->isSuperAdmin())
                    <div id="modal-cluster-edit-form" style="display: none;">
                        <form id="save-cluster-form" action="{{ url('admin/applications') }}/${appItem.id}/update-cluster" method="POST" onsubmit="submitClusterForm(event, ${appItem.id})">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                                <div>
                                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.3rem;">Select Cluster *</label>
                                    <select id="assign_cluster_id" name="cluster_id" class="form-control-dark" style="width: 100%; padding: 0.5rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" required>
                                        <option value="">-- Choose Cluster --</option>
                                        @foreach($clusters as $cl)
                                            <option value="{{ $cl->id }}" ${appItem.cluster_id == {{ $cl->id }} ? 'selected' : ''}>{{ $cl->name }} ({{ $cl->code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.3rem;">Agency Number *</label>
                                    <input type="text" id="assign_agency_number" name="agency_number" class="form-control-dark" style="width: 100%; padding: 0.5rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" required value="${appItem.agency_number || ''}">
                                </div>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                                <div>
                                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.3rem;">Agency Name (Donor)</label>
                                    <select id="assign_agency_name" name="meta[agency_name]" class="form-select-dark" style="width: 100%; padding: 0.5rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;">
                                        <option value="">-- Select Agency --</option>
                                        @foreach(($donors ?? []) as $d)
                                            <option value="{{ $d->name }}" ${meta.agency_name == '{{ addslashes($d->name) }}' ? 'selected' : ''}>{{ $d->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.3rem;">Application Date</label>
                                    <input type="date" id="assign_application_date" name="meta[application_date]" class="form-control-dark" style="width: 100%; padding: 0.5rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" value="${meta.application_date || ''}">
                                </div>
                            </div>
                            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                <button type="button" onclick="toggleClusterEditForm()" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); padding: 0.4rem 1rem; font-size: 0.8rem; cursor: pointer;">Cancel</button>
                                <button type="submit" class="btn-custom" style="padding: 0.4rem 1.2rem; font-size: 0.8rem; background: linear-gradient(135deg, #2ecc71, #27ae60); border: none; cursor: pointer;">Save Changes</button>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
            `;
            document.getElementById('details_content').innerHTML = html;
            
            document.getElementById('detailsAppModal').style.display = 'flex';
        }

        function closeDetailsModal() {
            document.getElementById('detailsAppModal').style.display = 'none';
        }

        function filterTable() {
            const input = document.getElementById('tableSearchInput');
            const filter = input ? input.value.toLowerCase().trim() : '';
            
            const clusterSelect = document.getElementById('clusterFilterSelect');
            const clusterVal = clusterSelect ? clusterSelect.value : 'all';

            const sponsorSelect = document.getElementById('sponsorFilterSelect');
            const sponsorVal = sponsorSelect ? sponsorSelect.value.toLowerCase() : 'all';

            const rows = document.querySelectorAll('.app-row');
            
            rows.forEach(row => {
                const searchText = (row.getAttribute('data-search') || '').toLowerCase();
                const rowCluster = row.getAttribute('data-cluster') || '';
                const rowSponsor = (row.getAttribute('data-sponsor') || 'not sponsored').toLowerCase();

                let matchesSearch = !filter || searchText.includes(filter);
                let matchesCluster = (clusterVal === 'all') || (rowCluster == clusterVal);
                let matchesSponsor = (sponsorVal === 'all') ||
                    (sponsorVal === 'sponsored' && rowSponsor === 'sponsored') ||
                    (sponsorVal.includes('not') && rowSponsor !== 'sponsored');

                if (matchesSearch && matchesCluster && matchesSponsor) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            // Update Export Excel button href dynamically
            const exportBtn = document.getElementById('excelExportBtn');
            if (exportBtn) {
                let baseUrl = "{{ route('applications.approved.export', ['category' => $categorySlug]) }}";
                let params = new URLSearchParams();
                if (clusterVal && clusterVal !== 'all') params.set('cluster_id', clusterVal);
                if (sponsorVal && sponsorVal !== 'all') params.set('sponsor_status', sponsorVal);
                if (filter) params.set('search', filter);
                
                let qStr = params.toString();
                exportBtn.href = baseUrl + (qStr ? '?' + qStr : '');
            }
        }

        function toggleClusterEditForm() {
            const displayDiv = document.getElementById('modal-cluster-container');
            const editDiv = document.getElementById('modal-cluster-edit-form');
            if (displayDiv.style.display === 'none') {
                displayDiv.style.display = 'block';
                editDiv.style.display = 'none';
            } else {
                displayDiv.style.display = 'none';
                editDiv.style.display = 'block';
            }
        }

        async function submitClusterForm(event, appId) {
            event.preventDefault();
            const form = event.target;
            const clusterId = form.querySelector('[name="cluster_id"]').value;
            const agencyNumber = form.querySelector('[name="agency_number"]').value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const response = await fetch(`/admin/applications/${appId}/update-cluster`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        category: 'family-aid',
                        cluster_id: clusterId || null,
                        agency_number: agencyNumber || null
                    })
                });

                const result = await response.json();
                if (result.success) {
                    document.getElementById('modal-cluster-display-name').innerHTML = result.cluster 
                        ? `${result.cluster.name} (${result.cluster.code})` 
                        : '<span style="color: var(--text-muted); font-style: italic;">Not assigned</span>';
                    document.getElementById('modal-agency-display-number').innerHTML = result.agency_number 
                        ? result.agency_number 
                        : '<span style="color: var(--text-muted); font-style: italic;">Not set</span>';
                    
                    window.location.reload();
                } else {
                    alert(result.error || 'Failed to update Cluster and Agency Number.');
                }
            } catch (err) {
                console.error(err);
                alert('An error occurred while updating.');
            }
        }

        async function handleToggleSponsor(event, appId, currentStatus = '', categorySlug = 'family-aid') {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            const isSponsored = (currentStatus && String(currentStatus).toLowerCase() === 'sponsored');

            if (isSponsored) {
                const doUnsponsor = async () => {
                    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '{{ csrf_token() }}';
                    try {
                        const response = await fetch(`/admin/applications/family-aid/${appId}/toggle-sponsor`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({ category: categorySlug })
                        });

                        const result = await response.json();
                        if (response.ok && result.success) {
                            window.location.reload();
                        } else {
                            alert(result.error || 'Failed to update sponsor status.');
                        }
                    } catch (err) {
                        console.error(err);
                        alert('An error occurred while updating sponsor status.');
                    }
                };

                if (typeof showCustomConfirm === 'function') {
                    showCustomConfirm('Are you sure you want to un-sponsor this application?', doUnsponsor);
                } else if (confirm('Are you sure you want to un-sponsor this application?')) {
                    doUnsponsor();
                }
            } else {
                if (typeof openSponsorDateModal === 'function') {
                    openSponsorDateModal(appId, categorySlug);
                } else {
                    const sponsoredDate = prompt("Enter Sponsored Date (YYYY-MM-DD):", new Date().toISOString().split('T')[0]);
                    if (!sponsoredDate) return;
                    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '{{ csrf_token() }}';
                    try {
                        const response = await fetch(`/admin/applications/family-aid/${appId}/toggle-sponsor`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({ category: categorySlug, sponsored_date: sponsoredDate })
                        });
                        const result = await response.json();
                        if (response.ok && result.success) {
                            window.location.reload();
                        } else {
                            alert(result.error || 'Failed to update sponsor status.');
                        }
                    } catch (err) {
                        console.error(err);
                        alert('An error occurred while updating sponsor status.');
                    }
                }
            }
        }
    
        // Global Window Bindings
        window.openDetailsModal = openDetailsModal;
        window.closeDetailsModal = closeDetailsModal;
    </script>

@endsection
