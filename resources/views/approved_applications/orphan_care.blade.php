@extends('layouts.admin')

@section('title', 'Approved Orphan Care Applications')

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
                        <th>Orphan Name</th>
                        <th>Father Name</th>
                        <th>Mother Name</th>
                        <th>Gender</th>
                        <th>Age</th>
                        <th>Place</th>
                        <th>District</th>
                        <th>State</th>
                        <th>Agency No</th>
                        <th style="white-space: nowrap; min-width: 140px;">Sponsor Status</th>
                        <th style="text-align: center; white-space: nowrap; min-width: 130px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $appItem)
                        @php
                            $meta = $appItem->meta ?? [];
                            $appYear = !empty($appItem->created_at) ? date('y', strtotime($appItem->created_at)) : '24';
                            $appId = 'APLRCFI' . $appYear . 'OC' . str_pad($appItem->id, 5, '0', STR_PAD_LEFT);
                            $project = $projectsMap[$appItem->id] ?? null;
                            
                            $searchTerms = [
                                $appId,
                                $appItem->applicant_name ?? '',
                                $appItem->place ?? '',
                                $appItem->district ?? '',
                                $appItem->state ?? '',
                                $appItem->agency_number ?? '',
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
                        <tr class="app-row" id="app-row-{{ $appItem->id }}" data-search="{{ $searchStr }}" data-cluster="{{ $appItem->cluster_id ?? '' }}" data-sponsor="{{ strtolower($appItem->sponsor_status ?? 'not sponsored') }}" data-place="{{ $appItem->place ?? '' }}" onclick="openDetailsModal({{ $appItem->id }})">
                            <td style="font-weight: 600;">
                                <a href="javascript:void(0)" onclick="event.stopPropagation(); openDetailsModal({{ $appItem->id }})" style="color: var(--accent-cyan); font-weight: 600; text-decoration: none; cursor: pointer;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'" title="View Application Details">
                                    {{ $appId }}
                                </a>
                            </td>
                            <td title="{{ $appItem->applicant_name }}">{{ \Illuminate\Support\Str::limit($appItem->applicant_name, 15, '...') }}</td>
                            <td title="{{ $meta['father_name'] ?? 'N/A' }}">{{ \Illuminate\Support\Str::limit($meta['father_name'] ?? 'N/A', 15, '...') }}</td>
                            <td title="{{ $meta['mother_name'] ?? 'N/A' }}">{{ \Illuminate\Support\Str::limit($meta['mother_name'] ?? 'N/A', 15, '...') }}</td>
                            <td>{{ $meta['gender'] ?? 'N/A' }}</td>
                            <td>{{ $meta['age'] ?? 'N/A' }}</td>
                            <td title="{{ $appItem->place ?? $meta['place'] ?? 'N/A' }}">{{ \Illuminate\Support\Str::limit($appItem->place ?? $meta['place'] ?? 'N/A', 15, '...') }}</td>
                            <td title="{{ $appItem->district ?? $meta['district'] ?? 'N/A' }}">{{ \Illuminate\Support\Str::limit($appItem->district ?? $meta['district'] ?? 'N/A', 15, '...') }}</td>
                            <td title="{{ $appItem->state ?? $meta['state'] ?? 'N/A' }}">{{ \Illuminate\Support\Str::limit($appItem->state ?? $meta['state'] ?? 'N/A', 15, '...') }}</td>
                            <td title="{{ $appItem->agency_number ?? 'N/A' }}">{{ \Illuminate\Support\Str::limit($appItem->agency_number ?? 'N/A', 15, '...') }}</td>
                            <td id="sponsor-status-cell-{{ $appItem->id }}" style="white-space: nowrap;">
                                @if(($appItem->sponsor_status ?? 'Not Sponsored') === 'Sponsored')
                                    <span style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.3rem 0.75rem; background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; border-radius: 20px; font-size: 0.75rem; font-weight: 600; white-space: nowrap; line-height: 1;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #10b981; box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2); display: inline-block;"></span>
                                        Sponsored
                                    </span>
                                @else
                                    <span style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.3rem 0.75rem; background: #fffbeb; color: #92400e; border: 1px solid #fde68a; border-radius: 20px; font-size: 0.75rem; font-weight: 600; white-space: nowrap; line-height: 1;">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: #f59e0b; box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2); display: inline-block;"></span>
                                        Not Sponsored
                                    </span>
                                @endif
                            </td>
                            <td id="sponsor-action-cell-{{ $appItem->id }}" style="text-align: center; white-space: nowrap;" onclick="event.stopPropagation()">
                                @if(($appItem->sponsor_status ?? 'Not Sponsored') === 'Sponsored')
                                    @if(Auth::user()->isSuperAdmin())
                                        <a href="#" onclick="event.preventDefault(); event.stopPropagation(); handleToggleSponsor(event, {{ $appItem->id }}, 'Sponsored', 'orphan-care')" class="btn-action-animated" style="background: #ffffff; color: #dc2626 !important; border: 1px solid #fecaca; padding: 0.45rem 1rem; font-size: 0.8rem; font-weight: 700; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem; text-decoration: none; min-width: 115px; box-shadow: 0 1px 3px rgba(239, 68, 68, 0.08); transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);" onmouseover="this.style.background='#fef2f2'; this.style.borderColor='#fca5a5'; this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(239, 68, 68, 0.15)';" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#fecaca'; this.style.transform='none'; this.style.boxShadow='0 1px 3px rgba(239, 68, 68, 0.08)';" title="Mark as Not Sponsored (Super Admin Only)">
                                            <i class="bx bx-x-circle" style="font-size: 1rem;"></i> Un-sponsor
                                        </a>
                                    @else
                                        <span style="color: #10b981; font-size: 0.78rem; font-weight: 700; padding: 0.4rem 0.85rem; background: rgba(16, 185, 129, 0.1); border-radius: 8px; display: inline-flex; align-items: center; gap: 0.35rem; border: 1px solid rgba(16, 185, 129, 0.2);">
                                            <i class="bx bx-check-double"></i> Sponsored
                                        </span>
                                    @endif
                                @else
                                    @if(Auth::user()->canManageSponsorship())
                                        <a href="#" onclick="event.preventDefault(); event.stopPropagation(); handleToggleSponsor(event, {{ $appItem->id }}, 'Not Sponsored', 'orphan-care')" class="btn-action-animated" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff !important; border: 1px solid #059669; padding: 0.45rem 1rem; font-size: 0.8rem; font-weight: 700; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem; text-decoration: none; min-width: 115px; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.28); transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(16, 185, 129, 0.4)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 8px rgba(16, 185, 129, 0.28)';" title="Mark as Sponsored">
                                            <i class="bx bx-plus-circle" style="font-size: 1rem;"></i> Sponsor
                                        </a>
                                    @else
                                        <span style="color: var(--text-muted); font-size: 0.85rem; font-style: italic;">No Action</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" style="text-align: center; padding: 2rem; color: var(--text-muted);">No approved applications registered in this category yet.</td>
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
                            <button type="button" onclick="event.preventDefault(); event.stopPropagation(); handleToggleSponsor(event, ${appItem.id}, 'Sponsored', 'orphan-care')" class="btn-custom" style="background: transparent; color: #ef4444; border: 1px solid #ef4444; padding: 0.6rem 1.5rem; display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600; cursor: pointer;">
                                <i class="bx bx-x-circle"></i> Mark as Un-sponsored
                            </button>
                        `;
                    @endif
                } else {
                    @if(Auth::user()->canManageSponsorship())
                        statusHtml += `
                            <button type="button" onclick="event.preventDefault(); event.stopPropagation(); openSponsorDateModal(${appItem.id}, 'orphan-care')" class="btn-custom" style="background: linear-gradient(135deg, #2ecc71, #27ae60); border: none; color: #ffffff; padding: 0.6rem 1.5rem; display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600; cursor: pointer;">
                                <i class="bx bx-check-circle"></i> Mark as Sponsored
                            </button>
                        `;
                    @endif
                }
                statusActionsContainer.innerHTML = statusHtml;
            }

            const meta = appItem.meta || {};
            const addr = appItem.address || appItem.applicant_address || {};
            const photoVal = appItem.student_photo || appItem.photo || (meta && meta.student_photo ? meta.student_photo : null) || (addr && addr.student_photo ? addr.student_photo : null);
            let photoSrc = null;
            if (photoVal) {
                if (photoVal.startsWith('http://') || photoVal.startsWith('https://')) {
                    photoSrc = photoVal;
                } else {
                    photoSrc = window.location.origin + '/' + photoVal.replace(/^\/+/, '');
                }
            }
            const formatVal = (val) => val ? val : '<span style="color: var(--text-muted); font-style: italic;">N/A</span>';
            const formatDate = (val) => {
                if (!val) return '<span style="color: var(--text-muted); font-style: italic;">Not set</span>';
                const str = String(val).trim();
                const parts = str.split('T')[0].split('-');
                if (parts.length === 3 && parts[0].length === 4) {
                    return `${parts[2]}/${parts[1]}/${parts[0]}`;
                }
                return val;
            };
            
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
            const whatsappNum = meta.whatsapp_number || addr.whatsapp_number || appItem.whatsapp_number;
            
            let html = `
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <!-- 1. Orphan & Family Details -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">1. Orphan & Family Details</h4>
                        <div style="display: flex; gap: 1rem; align-items: flex-start; margin-bottom: 0.5rem;">
                            <div style="flex: 1; min-width: 0;">
                                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600; width: 170px;">Orphan Name:</td><td style="font-weight: 600; color: #ffffff;">${formatVal(appItem.applicant_name)}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Gender:</td><td>${formatVal(meta.gender)}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Date of Birth:</td><td>${formatDate(meta.dob)}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Age:</td><td>${formatVal(meta.age ? (meta.age + ' yrs') : null)}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Aadhar Number:</td><td>${formatVal(meta.aadhar_number)}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Father's Name:</td><td>${formatVal(meta.father_name)}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Grandfather's Name:</td><td>${formatVal(meta.grandfather_name)}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Mother's Name:</td><td>${formatVal(meta.mother_name)}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Mother's Father:</td><td>${formatVal(meta.mothers_father_name)}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Guardian Name:</td><td>${formatVal(meta.guardian_name)}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Guardian Relation:</td><td>${formatVal(meta.guardian_relation)}</td></tr>
                                </table>
                            </div>
                            <div style="width: 112px; flex-shrink: 0; align-self: flex-start; margin-top: 0px; border: 1px solid var(--panel-border); border-radius: 10px; padding: 0.4rem 0.25rem; background: rgba(255,255,255,0.03); text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.15); display: flex; flex-direction: column; align-items: center;">
                                <h5 style="color: #00a65a; font-weight: 700; font-size: 0.65rem; letter-spacing: 0.04em; margin: 0 0 0.3rem 0; text-transform: uppercase;">STUDENT PHOTO</h5>
                                <div style="width: 80px; height: 104px; border: 2px dashed #00a65a; border-radius: 8px; padding: 0.15rem; display: flex; flex-direction: column; align-items: center; justify-content: center; background: transparent; overflow: hidden; position: relative;">
                                    ${photoSrc ? `
                                        <img src="${photoSrc}" onerror="this.onerror=null; this.parentNode.innerHTML='<i class=\\'bx bx-image\\' style=\\'font-size: 1.5rem; color: #00a65a; margin-bottom: 0.1rem;\\'></i><span style=\\'color: var(--text-muted); font-size: 0.6rem; font-weight: 500; text-align: center; line-height: 1.1;\\'>No Photo<br>Uploaded</span>';" style="width: 100%; height: 100%; border-radius: 6px; object-fit: cover;">
                                    ` : `
                                        <i class="bx bx-image" style="font-size: 1.5rem; color: #00a65a; margin-bottom: 0.1rem;"></i>
                                        <span style="color: var(--text-muted); font-size: 0.6rem; font-weight: 500; text-align: center; line-height: 1.1;">No Photo<br>Uploaded</span>
                                    `}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Parental Death & Sibling Details -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">2. Parental Death & Sibling Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600; width: 170px;">Father's Death Date:</td><td>${formatDate(meta.father_death_date)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Father's Death Cause:</td><td>${formatVal(meta.father_death_cause)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Mother Alive Status:</td><td>${formatVal(meta.mother_alive_status)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Mother's Death Date:</td><td>${formatDate(meta.mother_death_date)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Mother's Death Cause:</td><td>${formatVal(meta.mother_death_cause)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Mother Re-Married?</td><td>${formatVal(meta.mother_remarried_status)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Brothers:</td><td>${formatVal(meta.siblings_male)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Sisters:</td><td>${formatVal(meta.siblings_female)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Total Siblings:</td><td>${formatVal(meta.siblings_total)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Current Beneficiaries:</td><td>${formatVal(meta.current_beneficiaries)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Monthly Income:</td><td>${meta.monthly_income ? '₹' + Number(meta.monthly_income).toLocaleString() : 'N/A'}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Monthly Expense:</td><td>${meta.monthly_expense ? '₹' + Number(meta.monthly_expense).toLocaleString() : 'N/A'}</td></tr>
                        </table>
                    </div>

                    <!-- 3. Education & House Details -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">3. Education & House Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600; width: 170px;">Type Of House:</td><td>${formatVal(meta.house_type)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">School Name:</td><td>${formatVal(meta.school_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">School Class:</td><td>${formatVal(meta.school_class)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Madrassa Name:</td><td>${formatVal(meta.madrassa_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Madrassa Class:</td><td>${formatVal(meta.madrassa_class)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">If Not Studying, Reason:</td><td>${formatVal(meta.not_studying_reason)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Health Status:</td><td>${formatVal(meta.health_status)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Sponsorship Details:</td><td>${formatVal(meta.sponsorship_details)}</td></tr>
                        </table>
                    </div>

                    <!-- 4. Address & Contact Details -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">4. Address & Contact Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600; width: 170px;">House Name:</td><td>${formatVal(houseName)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Place:</td><td>${formatVal(placeName)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Village:</td><td>${formatVal(villageName)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Post Office:</td><td>${formatVal(postOffice)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Panchayath:</td><td>${formatVal(panchayatName)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">District:</td><td>${formatVal(districtName)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">State:</td><td>${formatVal(stateName)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Pin Code:</td><td>${formatVal(pinCode)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Mobile 1:</td><td>${formatVal(mob1)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Mobile 2:</td><td>${formatVal(mob2)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">WhatsApp Number:</td><td>${formatVal(whatsappNum)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Review Status:</td><td style="font-weight: 600; color: #ffffff;">${appItem.status}</td></tr>
                        </table>
                    </div>

                    <!-- 5. Recommendation Details -->
                    <div>
                        <h4 style="color: var(--accent-cyan); border-bottom: 1px solid var(--panel-border); padding-bottom: 0.5rem; margin-bottom: 0.75rem; font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">5. Recommendation Details</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600; width: 170px;">Recommender Name:</td><td>${formatVal(meta.recommender_name)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Organization:</td><td>${formatVal(meta.recommender_org)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Phone Number:</td><td>${formatVal(meta.recommender_phone)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.45rem 0; font-weight: 600;">Position:</td><td>${formatVal(meta.recommender_position)}</td></tr>
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
                                        ${(meta.agency_name || appItem.agency_name) ? (meta.agency_name || appItem.agency_name) : '<span style="color: var(--text-muted); font-style: italic;">Not set</span>'}
                                    </td>
                                </tr>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);">
                                    <td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Application Date:</td>
                                    <td id="modal-agency-display-date" style="font-weight: 600; color: #ffffff;">
                                        ${formatDate(meta.application_date || appItem.application_date)}
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
                                            <option value="{{ $d->name }}" ${(meta.agency_name || appItem.agency_name) == '{{ addslashes($d->name) }}' ? 'selected' : ''}>{{ $d->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.3rem;">Application Date</label>
                                    <input type="date" id="assign_application_date" name="meta[application_date]" class="form-control-dark" style="width: 100%; padding: 0.5rem; border-radius: 6px; border: 1px solid var(--panel-border); background-color: var(--bg-color); color: #ffffff;" value="${meta.application_date || appItem.application_date || ''}">
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
                const response = await fetch("{{ url('admin/applications') }}/" + appId + "/update-cluster", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        cluster_id: clusterId || null,
                        agency_number: agencyNumber || null
                    })
                });

                const result = await response.json();
                if (result.success) {
                    // Update display
                    document.getElementById('modal-cluster-display-name').innerHTML = result.cluster 
                        ? `${result.cluster.name} (${result.cluster.code})` 
                        : '<span style="color: var(--text-muted); font-style: italic;">Not assigned</span>';
                    document.getElementById('modal-agency-display-number').innerHTML = result.agency_number 
                        ? result.agency_number 
                        : '<span style="color: var(--text-muted); font-style: italic;">Not set</span>';
                    
                    // Reload page to update the main list view
                    window.location.reload();
                } else {
                    alert(result.error || 'Failed to update Cluster and Agency Number.');
                }
            } catch (err) {
                console.error(err);
                alert('An error occurred while updating.');
            }
        }

        function onSponsorStatusUpdated(appId, newStatus, categorySlug = 'orphan-care', sponsoredDate = null) {
            if (!appId) return;
            const isSponsored = (String(newStatus).toLowerCase() === 'sponsored');
            const normalizedStatus = isSponsored ? 'Sponsored' : 'Not Sponsored';

            // 1. Update row attribute & highlight briefly
            const rowEl = document.getElementById(`app-row-${appId}`);
            if (rowEl) {
                rowEl.setAttribute('data-sponsor', normalizedStatus.toLowerCase());
                rowEl.style.transition = 'background-color 0.4s ease';
                rowEl.style.backgroundColor = isSponsored ? 'rgba(16, 185, 129, 0.12)' : 'rgba(245, 158, 11, 0.12)';
                setTimeout(() => {
                    rowEl.style.backgroundColor = '';
                }, 1200);
            }

            // 2. Update Status cell
            const statusCell = document.getElementById(`sponsor-status-cell-${appId}`);
            if (statusCell) {
                if (isSponsored) {
                    statusCell.innerHTML = `
                        <span style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.3rem 0.75rem; background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; border-radius: 20px; font-size: 0.75rem; font-weight: 600; white-space: nowrap; line-height: 1;">
                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #10b981; box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2); display: inline-block;"></span>
                            Sponsored
                        </span>
                    `;
                } else {
                    statusCell.innerHTML = `
                        <span style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.3rem 0.75rem; background: #fffbeb; color: #92400e; border: 1px solid #fde68a; border-radius: 20px; font-size: 0.75rem; font-weight: 600; white-space: nowrap; line-height: 1;">
                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #f59e0b; box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2); display: inline-block;"></span>
                            Not Sponsored
                        </span>
                    `;
                }
            }

            // 3. Update Action cell
            const actionCell = document.getElementById(`sponsor-action-cell-${appId}`);
            if (actionCell) {
                if (isSponsored) {
                    @if(Auth::user()->isSuperAdmin())
                        actionCell.innerHTML = `
                            <a href="#" onclick="event.preventDefault(); event.stopPropagation(); handleToggleSponsor(event, ${appId}, 'Sponsored', '${categorySlug}')" class="btn-action-animated" style="background: #ffffff; color: #dc2626 !important; border: 1px solid #fecaca; padding: 0.45rem 1rem; font-size: 0.8rem; font-weight: 700; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem; text-decoration: none; min-width: 115px; box-shadow: 0 1px 3px rgba(239, 68, 68, 0.08); transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);" onmouseover="this.style.background='#fef2f2'; this.style.borderColor='#fca5a5'; this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(239, 68, 68, 0.15)';" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#fecaca'; this.style.transform='none'; this.style.boxShadow='0 1px 3px rgba(239, 68, 68, 0.08)';" title="Mark as Not Sponsored (Super Admin Only)">
                                <i class="bx bx-x-circle" style="font-size: 1rem;"></i> Un-sponsor
                            </a>
                        `;
                    @else
                        actionCell.innerHTML = `
                            <span style="color: #10b981; font-size: 0.78rem; font-weight: 700; padding: 0.4rem 0.85rem; background: rgba(16, 185, 129, 0.1); border-radius: 8px; display: inline-flex; align-items: center; gap: 0.35rem; border: 1px solid rgba(16, 185, 129, 0.2);">
                                <i class="bx bx-check-double"></i> Sponsored
                            </span>
                        `;
                    @endif
                } else {
                    @if(Auth::user()->canManageSponsorship())
                        actionCell.innerHTML = `
                            <a href="#" onclick="event.preventDefault(); event.stopPropagation(); handleToggleSponsor(event, ${appId}, 'Not Sponsored', '${categorySlug}')" class="btn-action-animated" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff !important; border: 1px solid #059669; padding: 0.45rem 1rem; font-size: 0.8rem; font-weight: 700; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem; text-decoration: none; min-width: 115px; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.28); transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(16, 185, 129, 0.4)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 8px rgba(16, 185, 129, 0.28)';" title="Mark as Sponsored">
                                <i class="bx bx-plus-circle" style="font-size: 1rem;"></i> Sponsor
                            </a>
                        `;
                    @else
                        actionCell.innerHTML = `<span style="color: var(--text-muted); font-size: 0.85rem; font-style: italic;">No Action</span>`;
                    @endif
                }
            }

            // 4. Update cached applicationsMap
            if (typeof applicationsMap !== 'undefined' && applicationsMap && applicationsMap[appId]) {
                applicationsMap[appId].sponsor_status = normalizedStatus;
                if (!applicationsMap[appId].meta) applicationsMap[appId].meta = {};
                if (isSponsored) {
                    applicationsMap[appId].meta.sponsored_date = sponsoredDate || new Date().toISOString().split('T')[0];
                } else {
                    delete applicationsMap[appId].meta.sponsored_date;
                }
            }

            // 5. Update open details modal if applicable
            if (currentDetailsAppItem && currentDetailsAppItem.id == appId) {
                currentDetailsAppItem.sponsor_status = normalizedStatus;
                if (!currentDetailsAppItem.meta) currentDetailsAppItem.meta = {};
                if (isSponsored) {
                    currentDetailsAppItem.meta.sponsored_date = sponsoredDate || new Date().toISOString().split('T')[0];
                } else {
                    delete currentDetailsAppItem.meta.sponsored_date;
                }
                openDetailsModal(currentDetailsAppItem);
            }
        }

        async function handleToggleSponsor(event, appId, currentStatus = '', categorySlug = 'orphan-care') {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            const isSponsored = (currentStatus && String(currentStatus).toLowerCase() === 'sponsored');
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '{{ csrf_token() }}';

            if (isSponsored) {
                const doUnsponsor = async () => {
                    try {
                        const response = await fetch(`/admin/applications/${categorySlug}/${appId}/toggle-sponsor`, {
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
                            onSponsorStatusUpdated(appId, result.sponsor_status || 'Not Sponsored', categorySlug);
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
                const doSponsor = async (sponsoredDate) => {
                    try {
                        const response = await fetch(`/admin/applications/${categorySlug}/${appId}/toggle-sponsor`, {
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
                            onSponsorStatusUpdated(appId, result.sponsor_status || 'Sponsored', categorySlug, sponsoredDate);
                        } else {
                            alert(result.error || 'Failed to update sponsor status.');
                        }
                    } catch (err) {
                        console.error(err);
                        alert('An error occurred while updating sponsor status.');
                    }
                };

                if (typeof showCustomConfirm === 'function') {
                    showCustomConfirm('Please select the official sponsored date to sponsor this application:', doSponsor, false, true);
                } else {
                    const sponsoredDate = prompt("Enter Sponsored Date (YYYY-MM-DD):", new Date().toISOString().split('T')[0]);
                    if (!sponsoredDate) return;
                    doSponsor(sponsoredDate);
                }
            }
        }
    
        // Global Window Bindings
        window.openDetailsModal = openDetailsModal;
        window.closeDetailsModal = closeDetailsModal;
        window.onSponsorStatusUpdated = onSponsorStatusUpdated;
        window.handleToggleSponsor = handleToggleSponsor;
    </script>

@endsection