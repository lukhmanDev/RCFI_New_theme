@extends('layouts.admin')

@section('title', 'Approved General Applications')

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
                        <th colspan="3" style="text-align: center; border-right: 2px solid #2a3547; font-weight: 700; color: var(--accent-cyan); letter-spacing: 0.05em; background-color: rgba(0,0,0,0.15);">APPLICATION DETAILS</th>
                        <th colspan="4" style="text-align: center; font-weight: 700; color: var(--accent-cyan); letter-spacing: 0.05em; background-color: rgba(0,0,0,0.15);">PROJECT DETAILS</th>
                    </tr>
                    <tr>
                        <th>Application ID</th>
                        <th>Name of Applicant</th>
                        <th style="border-right: 2px solid #2a3547;">District</th>
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
                            $appId = 'APLRCFI' . $appYear . 'GN' . str_pad($appItem->id, 5, '0', STR_PAD_LEFT);
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
                            <td style="border-right: 2px solid #2a3547;" title="{{ $appItem->district ?? $meta['district'] ?? $meta['locality_district'] ?? '-' }}">{{ \Illuminate\Support\Str::limit($appItem->district ?? $meta['district'] ?? $meta['locality_district'] ?? '-', 15, '...') }}</td>
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
                            <td style="vertical-align: middle;">
                                @if($project && ($project->status === 'Completed' || !empty($project->project_phase)))
                                    @php
                                        $phaseVal = $project->status === 'Completed' ? 'Completed' : $project->project_phase;
                                        $phaseLabel = $phaseVal === 'Other'
                                            ? ($project->project_phase_custom ?: 'Other')
                                            : $phaseVal;
                                        $phaseColors = [
                                            'Project Assigned'                      => ['bg' => 'rgba(99,102,241,0.18)',  'text' => '#a5b4fc'],
                                            'Site identified'                       => ['bg' => 'rgba(59,130,246,0.18)',  'text' => '#60a5fa'],
                                            'Documents verified'                    => ['bg' => 'rgba(14,165,233,0.18)',  'text' => '#38bdf8'],
                                            'Drawing'                               => ['bg' => 'rgba(168,85,247,0.18)', 'text' => '#c084fc'],
                                            'Tender'                                => ['bg' => 'rgba(245,158,11,0.18)', 'text' => '#fcd34d'],
                                            'Agreement'                             => ['bg' => 'rgba(249,115,22,0.18)', 'text' => '#fb923c'],
                                            'Foundation'                            => ['bg' => 'rgba(234,88,12,0.2)',   'text' => '#fdba74'],
                                            'Column'                                => ['bg' => 'rgba(202,138,4,0.2)',   'text' => '#fde047'],
                                            'Slab'                                  => ['bg' => 'rgba(132,204,22,0.18)', 'text' => '#bef264'],
                                            'Mason work'                            => ['bg' => 'rgba(20,184,166,0.18)', 'text' => '#5eead4'],
                                            'Plastering'                            => ['bg' => 'rgba(6,182,212,0.18)',  'text' => '#22d3ee'],
                                            'Flooring, Painting, Joinery and MEP'  => ['bg' => 'rgba(16,185,129,0.18)', 'text' => '#6ee7b7'],
                                            'Completed'                             => ['bg' => 'rgba(16,185,129,0.25)', 'text' => '#4ade80'],
                                            'Inaugurated'                           => ['bg' => 'rgba(52,211,153,0.25)', 'text' => '#34d399'],
                                            'Finance settled and Project phase off' => ['bg' => 'rgba(156,163,175,0.2)', 'text' => '#d1d5db'],
                                        ];
                                        $pColor = $phaseColors[$phaseVal] ?? ['bg' => 'rgba(6,182,212,0.15)', 'text' => 'var(--accent-cyan)'];
                                    @endphp
                                    <span title="{{ $phaseLabel }}" style="display:inline-flex;align-items:center;gap:0.3rem;background-color:{{ $pColor['bg'] }};color:{{ $pColor['text'] }};padding:0.25rem 0.75rem;border-radius:20px;font-size:0.72rem;font-weight:700;white-space:nowrap;margin:0 auto;">
                                        <i class="bx bx-radio-circle-marked" style="font-size:0.9rem;flex-shrink:0;"></i>
                                        {{ Str::limit($phaseLabel, 20) }}
                                    </span>
                                @elseif($project)
                                    <span style="background-color:rgba(156,163,175,0.15);color:var(--text-muted);padding:0.25rem 0.6rem;border-radius:20px;font-size:0.7rem;font-weight:600;display:inline-flex;align-items:center;gap:0.25rem;border:1px solid rgba(255,255,255,0.05);">
                                        <i class="bx bx-time-five" style="font-size:0.85rem;"></i> Not set
                                    </span>
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
            
            const addr = appItem.address || {};
            const houseName = meta.house_name || addr.house_name || appItem.house_name;
            const placeName = meta.place || addr.place || appItem.place;
            const villageName = meta.village || addr.village || appItem.village;
            const postOffice = meta.post_office || addr.post_office || appItem.post_office;
            const panchayatName = meta.panchayat || addr.panchayat || appItem.panchayat;
            const districtName = meta.district || addr.district || appItem.district;
            const stateName = meta.state || addr.state || appItem.state;
            const pinCode = meta.pin_code || addr.pin_code || appItem.pin_code;
            const mob1 = meta.mobile_1 || meta.mobile || addr.contact_number_1 || addr.mobile_1 || appItem.mobile_1;
            const mob2 = meta.mobile_2 || addr.contact_number_2 || addr.mobile_2 || appItem.mobile_2;
            const appTypeVal = meta.application_type || appItem.application_type || 'Individual';
            const orgNameVal = meta.organization_name || appItem.organization_name || '';
            const unitVal = meta.unit || appItem.unit || '';
            
            let html = `
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <!-- 1. Personal Details of Applicant -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">1. Personal Details of Applicant</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600; width: 170px;">Application Type:</td><td><span style="display: inline-block; padding: 0.15rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 700; ${appTypeVal === 'Group' ? 'background: rgba(168, 85, 247, 0.15); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.3);' : 'background: rgba(255,255,255,0.05); color: var(--text-muted); border: 1px solid var(--panel-border);'}">${appTypeVal}</span></td></tr>
                            ${appTypeVal === 'Group' ? `
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Institute/Org:</td><td style="font-weight: 600; color: #ffffff;">${formatVal(orgNameVal)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Unit:</td><td>${formatVal(unitVal)}</td></tr>
                            ` : ''}
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Applicant Name:</td><td style="font-weight: 600; color: #ffffff;">${formatVal(appItem.applicant_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Age:</td><td>${formatVal(meta.age ? (meta.age + ' yrs') : null)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Sex / Gender:</td><td>${formatVal(meta.sex || meta.gender)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">House Name:</td><td>${formatVal(houseName)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Place:</td><td>${formatVal(placeName)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Village:</td><td>${formatVal(villageName)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Post Office:</td><td>${formatVal(postOffice)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Panchayath:</td><td>${formatVal(panchayatName)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">District:</td><td>${formatVal(districtName)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">State:</td><td>${formatVal(stateName)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Pin Code:</td><td>${formatVal(pinCode)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Mobile 1:</td><td>${formatVal(mob1)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Mobile 2:</td><td>${formatVal(mob2)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Status of Applicant:</td><td>${formatVal(meta.status_of_applicant)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Education Level:</td><td>${formatVal(meta.education)}</td></tr>
                        </table>
                    </div>

                    <!-- 2. Family & Economic Details -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">2. Family & Economic Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600; width: 170px;">Male Members:</td><td>${formatVal(meta.num_male_family)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Female Members:</td><td>${formatVal(meta.num_female_family)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Total Members:</td><td style="font-weight: 600; color: #ffffff;">${formatVal(meta.num_total_family)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Earning Members:</td><td>${formatVal(meta.num_earning_members)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Avg Monthly Income:</td><td>${meta.average_monthly_income ? '₹' + Number(meta.average_monthly_income).toLocaleString() : 'N/A'}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Applying for:</td><td style="font-weight: 600; color: #ffffff;">${formatVal(meta.applying_for)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Amount Requested:</td><td style="color: var(--accent-green); font-weight: 600;">${appItem.amount_requested ? '₹' + Number(appItem.amount_requested).toLocaleString() : 'N/A'}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Office App Type:</td><td style="font-weight: 600; color: var(--accent-cyan);">${formatVal(meta.office_application_type)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Review Status:</td><td style="font-weight: 600; color: #ffffff;">${appItem.status}</td></tr>
                        </table>
                    </div>

                    <!-- 3. Recommendation Details -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">3. Recommendation Details</h4>
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
