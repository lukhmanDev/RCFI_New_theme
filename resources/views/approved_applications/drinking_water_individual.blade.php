@extends('layouts.admin')

@section('title', 'Approved Drinking Water - Individual Level Applications')

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
                        <th colspan="4" style="text-align: center; border-right: 2px solid #2a3547; font-weight: 700; color: var(--accent-cyan); letter-spacing: 0.05em; background-color: rgba(0,0,0,0.15);">APPLICATION DETAILS</th>
                        <th colspan="4" style="text-align: center; font-weight: 700; color: var(--accent-cyan); letter-spacing: 0.05em; background-color: rgba(0,0,0,0.15);">PROJECT DETAILS</th>
                    </tr>
                    <tr>
                        <th>Application ID</th>
                        <th>Name of Applicant</th>
                        <th>District</th>
                        <th style="border-right: 2px solid #2a3547;">Well Type</th>
                        <th>RCFI ID</th>
                        <th>Project Manager</th>
                        <th>Donor</th>
                        <th>Status</th>
                        </tr>
                </thead>
                <tbody>
                    @forelse($applications as $appItem)
                        @php
                            $meta = $appItem->meta ?? [];
                            $appYear = !empty($appItem->created_at) ? date('y', strtotime($appItem->created_at)) : '24';
                            $appId = 'APLRCFI' . $appYear . 'DWI' . str_pad($appItem->id, 5, '0', STR_PAD_LEFT);
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
                        <tr class="app-row" data-search="{{ $searchStr }}" data-place="{{ $appItem->place ?? '' }}">
                            <td style="font-weight: 600;">
                                <a href="javascript:void(0)" onclick="openDetailsModal({{ json_encode($appItem) }})" style="color: var(--accent-cyan); font-weight: 600; text-decoration: none; cursor: pointer;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'" title="View Application Details">
                                    {{ $appId }}
                                </a>
                            </td>
                            <td title="{{ $appItem->applicant_name }}">{{ \Illuminate\Support\Str::limit($appItem->applicant_name, 15, '...') }}</td>
                            <td title="{{ $appItem->district ?? $meta['district'] ?? $meta['locality_district'] ?? '-' }}">{{ \Illuminate\Support\Str::limit($appItem->district ?? $meta['district'] ?? $meta['locality_district'] ?? '-', 15, '...') }}</td>
                            <td style="border-right: 2px solid #2a3547;">{{ $meta['well_type'] ?? '-' }}</td>
                            <!-- Project ID & Status -->
                            <td>
                                @if($project)
                                    <a href="{{ route('projects.show', $project->id) }}?type={{ urlencode($project->type_of_project) }}" style="color: var(--accent-cyan); font-weight: 600; text-decoration: none; cursor: pointer;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'" title="View Project Details">
                                        {{ $project->project_id ?? 'Assigned' }}
                                    </a>
                                @else
                                    —
                                @endif
                            </td>
                            @php
                                $pmName = $project && $project->projectManager ? $project->projectManager->name : '—';
                                $donorName = $project && $project->donor ? $project->donor->name : '—';
                            @endphp
                            <td title="{{ $pmName }}">{{ \Illuminate\Support\Str::limit($pmName, 15, '...') }}</td>
                            <td title="{{ $donorName }}">{{ \Illuminate\Support\Str::limit($donorName, 15, '...') }}</td>
                            <td style=" vertical-align: middle;">
                                @if($project)
                                    @php
                                        $displayStatus = ($project->status === 'Completed') ? 'Completed' : ($project->project_phase ?: 'Running');
                                    @endphp
                                    @if($displayStatus === 'Completed')
                                        <span style="display:inline-flex;align-items:center;gap:0.3rem;background-color:rgba(16,185,129,0.25);color:#4ade80;padding:0.25rem 0.75rem;border-radius:20px;font-size:0.72rem;font-weight:700;white-space:nowrap;margin:0 auto;">
                                            <i class="bx bx-check-circle" style="font-size:0.9rem;flex-shrink:0;"></i>
                                            Completed
                                        </span>
                                    @else
                                        <span style="display:inline-flex;align-items:center;gap:0.3rem;background-color:rgba(6,182,212,0.18);color:var(--accent-cyan);padding:0.25rem 0.75rem;border-radius:20px;font-size:0.72rem;font-weight:700;white-space:nowrap;margin:0 auto;">
                                            <i class="bx bx-loader-circle" style="font-size:0.9rem;flex-shrink:0;"></i>
                                            {{ $displayStatus }}
                                        </span>
                                    @endif
                                @else
                                    <span style="background-color:rgba(156,163,175,0.15);color:var(--text-muted);padding:0.25rem 0.6rem;border-radius:20px;font-size:0.7rem;font-weight:600;display:inline-flex;align-items:center;gap:0.25rem;border:1px solid rgba(255,255,255,0.05);">
                                        <i class="bx bx-minus-circle" style="font-size:0.85rem;"></i> Not Started
                                    </span>
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
        <div class="panel" style="width: 95%; max-width: 750px; margin: 2rem auto; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border-color: #2a3547; max-height: 90vh; overflow-y: auto;" onclick="event.stopPropagation()">
            
            <button onclick="closeDetailsModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; z-index: 10;"><i class="bx bx-x"></i></button>
            
            <div class="panel-header" style="margin-bottom: 1.5rem;">
                <h2 class="panel-title" style="font-size: 1.25rem;"><i class="bx bx-detail" style="vertical-align: middle; margin-right: 0.5rem; color: var(--accent-green);"></i> Application Details</h2>
            </div>

            <!-- Details content dynamically populated by JS -->
            <div id="details_content" style="color: var(--text-main); font-size: 0.9rem;">
                <!-- Tables populated by script -->
            </div>
            
            <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--panel-border); padding-top: 1.5rem; flex-wrap: wrap;">
                @if(Auth::user()->canApproveApplications())
                    <span id="modal_status_actions" style="display: inline-flex; gap: 0.75rem;"></span>
                @endif
                <button onclick="closeDetailsModal()" class="btn-custom" style="background: transparent; border: 1px solid var(--panel-border); color: var(--text-muted); padding: 0.6rem 1.5rem;">Close Details</button>
            </div>
        </div>
    </div>

    <!-- Script Block -->
    <script>
        var currentDetailsAppItem = null;

        function openDetailsModal(appItem, isProjectApproved = false) {
            currentDetailsAppItem = appItem;
            
            // Populate status actions in the modal footer dynamically
            const statusActionsContainer = document.getElementById('modal_status_actions');
            if (statusActionsContainer) {
                statusActionsContainer.innerHTML = '';
            }

            const meta = appItem.meta || {};
            const formatVal = (val) => val ? val : '<span style="color: var(--text-muted); font-style: italic;">N/A</span>';
            
            // Build Beneficiary details HTML list
            const beneficiaries = meta.beneficiaries || [];
            let beneficiariesHtml = '';
            if (beneficiaries.length > 0) {
                beneficiariesHtml = `<ol style="margin: 0; padding-left: 1.25rem;">`;
                beneficiaries.forEach(b => {
                    beneficiariesHtml += `<li style="margin-bottom: 0.25rem; font-weight: 500; color: #ffffff;">${b.name} <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 400;">(${b.phone})</span></li>`;
                });
                beneficiariesHtml += `</ol>`;
            } else {
                beneficiariesHtml = '<span style="color: var(--text-muted); font-style: italic;">No beneficiaries listed.</span>';
            }

            let html = `
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <!-- 1. Personal Details of Applicant -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">1. Personal Details of Applicant</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600; width: 170px;">Applicant Name:</td><td style="font-weight: 600; color: #ffffff;">${formatVal(appItem.applicant_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Place:</td><td>${formatVal(meta.location)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Village:</td><td>${formatVal(meta.village)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Post:</td><td>${formatVal(meta.post)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Panchayath:</td><td>${formatVal(meta.panchayath)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">District:</td><td>${formatVal(meta.district)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">State:</td><td>${formatVal(meta.state)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Pin Code:</td><td>${formatVal(meta.pin_code || meta.pin)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Contact Number 1:</td><td>${formatVal(meta.contact_number_1)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Contact Number 2:</td><td>${formatVal(meta.contact_number_2)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Job:</td><td>${formatVal(meta.job)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Monthly Income:</td><td>${meta.monthly_income ? '₹' + Number(meta.monthly_income).toLocaleString() : 'N/A'}</td></tr>
                        </table>
                    </div>

                    <!-- 2. Details of Beneficiaries -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">2. Details of Beneficiaries</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600; width: 170px;">Total Benefited:</td><td>${formatVal(meta.num_benefited_people)} people</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Male Beneficiaries:</td><td>${formatVal(meta.num_male_benefited)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Female Beneficiaries:</td><td>${formatVal(meta.num_female_benefited)}</td></tr>
                            <tr><td colspan="15" style="padding-top: 0.5rem; font-weight: 600;">Beneficiaries List:</td></tr>
                            <tr><td colspan="15" style="padding-top: 0.25rem;">${beneficiariesHtml}</td></tr>
                        </table>
                    </div>

                    <!-- 3. Owner of Proposed Land -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">3. Owner of Proposed Land</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600; width: 170px;">Land Owner Name:</td><td style="font-weight: 600; color: #ffffff;">${formatVal(meta.land_owner_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Place:</td><td>${formatVal(meta.land_owner_place)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Post:</td><td>${formatVal(meta.locality_post)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Panchayath:</td><td>${formatVal(meta.locality_panchayath || meta.locality_panchayat)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">District:</td><td>${formatVal(meta.land_owner_district)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Mobile:</td><td>${formatVal(meta.land_owner_mobile)}</td></tr>
                        </table>
                    </div>

                    <!-- 4. Project & Well Details -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">4. Project & Well Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600; width: 170px;">Type of Well:</td><td style="font-weight: 600; color: #ffffff;">${formatVal(meta.well_type)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Expected Depth:</td><td>${formatVal(meta.well_depth)} feet</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Diameter:</td><td>${formatVal(meta.well_diameter)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Nature of Land:</td><td>${formatVal(meta.land_nature)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Budget Estimated:</td><td style="color: var(--accent-green); font-weight: 600;">${appItem.amount_requested ? '₹' + Number(appItem.amount_requested).toLocaleString() : 'N/A'}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Current Source:</td><td>${formatVal(meta.current_water_source)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Electric Pump?</td><td>${formatVal(meta.need_pump)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">For Agriculture?</td><td>${formatVal(meta.well_for_agriculture)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Review Status:</td><td style="font-weight: 600; color: #ffffff;">${appItem.status}</td></tr>
                        </table>
                    </div>

                    <!-- 5. Recommendation Details -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">5. Recommendation Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600; width: 170px;">Recommender Name:</td><td>${formatVal(meta.recommendation_name || meta.recommender_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Organization:</td><td>${formatVal((meta.recommendation_organization === 'Others' ? meta.recommendation_organization_other : meta.recommendation_organization) || meta.recommender_org)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Phone Number:</td><td>${formatVal(meta.recommendation_phone || meta.recommender_phone)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Position:</td><td>${formatVal(meta.recommendation_position || meta.recommender_position)}</td></tr>
                        </table>
                    </div>

                    ${appItem.details ? `
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">Additional Notes</h4>
                        <p style="color: var(--text-muted); line-height: 1.5; font-size: 0.85rem; margin: 0; background-color: #121824; padding: 0.75rem; border-radius: 6px; border: 1px solid var(--panel-border);">
                            ${appItem.details}
                        </p>
                    </div>
                    ` : ''}
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
            const filter = input.value.toLowerCase().trim();
            const rows = document.querySelectorAll('.app-row');
            
            rows.forEach(row => {
                const searchText = row.getAttribute('data-search') || '';
                if (searchText.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    
        // Global Window Bindings
        window.openDetailsModal = openDetailsModal;
        window.closeDetailsModal = closeDetailsModal;
    </script>

@endsection
