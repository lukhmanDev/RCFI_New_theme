@extends('layouts.admin')

@section('title', 'Single Project Detailed Report')

@section('content')
<style>
    .report-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem;
        color: #1e293b;
        box-shadow: 0 4px 12px -2px rgba(15, 23, 42, 0.05);
        box-sizing: border-box;
    }
    .report-card-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-top: 0;
        margin-bottom: 1.25rem;
        text-align: center;
        color: #0f172a;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #ecfdf5;
        padding-bottom: 0.65rem;
    }
    .info-line {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.65rem 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
    }
    .info-line:last-child {
        border-bottom: none;
    }
    .info-label {
        font-weight: 600;
        color: #64748b;
    }
    .info-val {
        font-weight: 700;
        color: #0f172a;
        text-align: right;
    }
    .avatar-circle {
        width: 85px;
        height: 85px;
        border-radius: 50%;
        background: #ecfdf5;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.85rem auto;
        font-size: 2.2rem;
        color: #10b981;
        border: 3px solid #a7f3d0;
    }
    .doc-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.6rem 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.88rem;
    }
    .doc-item:last-child {
        border-bottom: none;
    }
    .inspection-table {
        width: 100%;
        border-collapse: collapse;
        color: #1e293b;
        font-size: 0.88rem;
    }
    .inspection-table th {
        padding: 0.75rem 1rem;
        text-align: left;
        color: #ffffff !important;
        background-color: #10b981 !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        font-size: 0.8rem;
        letter-spacing: 0.05em;
    }
    .inspection-table td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #e2e8f0;
    }

    /* Slideshow Styles */
    .slideshow-container {
        position: relative;
        width: 100%;
        height: 400px;
        background: #0f172a;
        border-radius: 14px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .slideshow-img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        transition: opacity 0.3s ease-in-out;
    }
    .slideshow-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(15, 23, 42, 0.65);
        color: #ffffff;
        border: none;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        font-size: 1.5rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        z-index: 10;
        backdrop-filter: blur(4px);
    }
    .slideshow-nav-btn:hover {
        background: #10b981;
        color: #ffffff;
        transform: translateY(-50%) scale(1.1);
    }
    .slideshow-nav-btn.prev { left: 1rem; }
    .slideshow-nav-btn.next { right: 1rem; }

    .slideshow-badge {
        position: absolute;
        top: 1rem;
        padding: 0.35rem 0.85rem;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 700;
        z-index: 10;
        backdrop-filter: blur(6px);
    }
    .slideshow-badge.category {
        left: 1rem;
        background: rgba(16, 185, 129, 0.85);
        color: #ffffff;
    }
    .slideshow-badge.counter {
        right: 1rem;
        background: rgba(15, 23, 42, 0.75);
        color: #ffffff;
    }

    .thumb-strip {
        display: flex;
        gap: 0.65rem;
        overflow-x: auto;
        padding: 0.75rem 0.25rem 0.25rem 0.25rem;
        scroll-behavior: smooth;
    }
    .thumb-item {
        width: 80px;
        height: 60px;
        border-radius: 8px;
        object-fit: cover;
        cursor: pointer;
        border: 2px solid transparent;
        opacity: 0.6;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }
    .thumb-item:hover, .thumb-item.active {
        opacity: 1;
        border-color: #10b981;
        transform: scale(1.05);
    }

    /* Print Styles */
    @media print {
        body { background: #ffffff !important; color: #000000 !important; }
        .btn-custom, select, .thumb-strip, .slideshow-nav-btn { display: none !important; }
        .report-card { border: 1px solid #cbd5e1 !important; box-shadow: none !important; break-inside: avoid; }
        .slideshow-container { height: 320px !important; background: #ffffff !important; }
        .slideshow-img { max-height: 300px !important; }
    }
</style>

<div class="container-fluid" style="padding: 1.5rem;">

    <!-- Header Controls -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="color: var(--text-main); font-weight: 700; margin: 0; font-size: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="bx bx-file" style="color: #10b981;"></i> Single Project Detailed Report
            </h2>
            <p style="color: var(--text-muted); margin: 0.25rem 0 0 0; font-size: 0.88rem;">
                Detailed breakdown of project manager, engineer, documents, inspections, and financial summary
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">

            <button onclick="window.print()" class="btn-custom" style="background: linear-gradient(135deg, #10b981, #059669); color: #ffffff; border: none; padding: 0.65rem 1.25rem; font-size: 0.88rem; border-radius: 10px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25); cursor: pointer;">
                <i class="bx bx-printer" style="font-size: 1.1rem;"></i> Print Report
            </button>
        </div>
    </div>

    @if(!$targetProjectData)
        <div class="report-card" style="text-align: center; padding: 3rem;">
            <i class="bx bx-info-circle" style="font-size: 3rem; margin-bottom: 1rem; color: #10b981;"></i>
            <h3 style="color: #0f172a;">No Projects Available</h3>
            <p style="color: #64748b;">There are currently no projects in the system to display.</p>
        </div>
    @else

        <!-- Top Grid: 3 Main Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
            
            <!-- Card 1: Project & Application Details -->
            <div class="report-card">
                <h3 class="report-card-title">Project & Application Details</h3>
                
                <div class="info-line">
                    <span class="info-label">Project Id :</span>
                    <span class="info-val" style="color: #10b981; font-size: 1.05rem; font-weight: 800;">{{ $targetProjectData['project_id_str'] }}</span>
                </div>
                
                <div class="info-line">
                    <span class="info-label">Project Name :</span>
                    <span class="info-val">{{ $projectObj->project_name ?? $projectObj->name ?? $projectObj->title ?? 'N/A' }}</span>
                </div>

                <div class="info-line">
                    <span class="info-label">Applicant Name :</span>
                    <span class="info-val">{{ $application ? ($application->applicant_name ?? $application->name ?? 'APL#' . $application->id) : ($projectObj->applicant_name ?? 'N/A') }}</span>
                </div>

                @php
                    $applicantMobile = $application->mobile ?? $application->phone ?? $projectObj->mobile ?? null;
                    $applicantLocation = implode(', ', array_filter([
                        $application->place ?? $projectObj->place ?? null,
                        $application->district ?? $projectObj->district ?? null,
                        $application->state ?? $projectObj->state ?? null
                    ]));
                @endphp

                @if($applicantMobile)
                <div class="info-line">
                    <span class="info-label">Contact Mobile :</span>
                    <span class="info-val" style="color: #059669; font-weight: 700;">{{ $applicantMobile }}</span>
                </div>
                @endif

                @if($applicantLocation)
                <div class="info-line">
                    <span class="info-label">Location / District :</span>
                    <span class="info-val">{{ $applicantLocation }}</span>
                </div>
                @endif

                <div class="info-line">
                    <span class="info-label">Category :</span>
                    <span class="info-val" style="background: #ecfdf5; color: #059669; padding: 0.2rem 0.6rem; border-radius: 6px; font-size: 0.78rem; font-weight: 700;">{{ $targetProjectData['category'] }}</span>
                </div>

                <div class="info-line">
                    <span class="info-label">Estimate Amount :</span>
                    <span class="info-val" style="color: #10b981; font-weight: 800;">₹{{ number_format($totalAllocated, 2) }}</span>
                </div>

                <div class="info-line">
                    <span class="info-label">Status :</span>
                    <span class="info-val" style="background: #ecfdf5; color: #10b981; padding: 0.2rem 0.6rem; border-radius: 6px; font-size: 0.78rem; font-weight: 700; text-transform: uppercase;">{{ $projectObj->status ?? 'COMPLETED' }}</span>
                </div>
            </div>

            <!-- Card 2: Team Staff (Project Manager & Engineer) -->
            @php
                $getUserPhoto = function($userObj) {
                    if (!$userObj) return null;
                    $path = $userObj->profile_photo_path ?? $userObj->avatar ?? $userObj->photo ?? $userObj->profile_photo_url ?? null;
                    if (!$path) return null;
                    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) return $path;
                    return asset($path);
                };
                $pmPhotoUrl = $getUserPhoto($projectManager);
                $engPhotoUrl = $getUserPhoto($engineer);
                $hasEngineer = $engineer && !empty($engineer->name);
            @endphp
            <div class="report-card" style="display: flex; gap: 1rem; align-items: center; justify-content: center;">
                <!-- Project Manager -->
                <div style="flex: 1; text-align: center; {{ $hasEngineer ? 'border-right: 1px solid #f1f5f9; padding-right: 0.75rem;' : '' }}">
                    <h4 style="font-size: 0.95rem; font-weight: 700; margin-top: 0; margin-bottom: 0.85rem; color: #0f172a;">Project Manager</h4>
                    @if($pmPhotoUrl)
                        <img src="{{ $pmPhotoUrl }}" alt="Project Manager" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid #a7f3d0; margin: 0 auto 0.75rem auto; display: block;">
                    @else
                        <div class="avatar-circle" style="width: 80px; height: 80px; font-size: 2rem; margin: 0 auto 0.75rem auto;">
                            <i class="bx bxs-user"></i>
                        </div>
                    @endif
                    <h5 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: #0f172a;">{{ $projectManager ? $projectManager->name : 'N/A' }}</h5>
                    <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.25rem; word-break: break-all;">
                        {{ $projectManager ? ($projectManager->email ?? 'N/A') : 'N/A' }}
                    </div>
                    @if($projectManager && ($projectManager->mobile || $projectManager->phone))
                        <div style="font-size: 0.78rem; color: #059669; font-weight: 600; margin-top: 0.15rem;">
                            <i class="bx bx-phone" style="color: #10b981;"></i> {{ $projectManager->mobile ?? $projectManager->phone }}
                        </div>
                    @endif

                    @if($hod)
                        <div style="margin-top: 0.65rem; padding-top: 0.45rem; border-top: 1px dashed #cbd5e1;">
                            <div style="font-size: 0.82rem; color: #6b21a8; font-weight: 700;">
                                <i class="bx bxs-shield-alt-2" style="color: #8b5cf6;"></i> HOD: <span style="color: #0f172a; font-weight: 600;">{{ $hod->name }}</span>
                            </div>
                            @if($hod->email)
                                <div style="font-size: 0.76rem; color: #64748b; margin-top: 0.15rem; word-break: break-all;">
                                    <i class="bx bx-envelope" style="color: #94a3b8;"></i> {{ $hod->email }}
                                </div>
                            @endif
                            @if($hod->mobile || $hod->phone)
                                <div style="font-size: 0.76rem; color: #7c3aed; font-weight: 600; margin-top: 0.15rem;">
                                    <i class="bx bx-phone" style="color: #8b5cf6;"></i> {{ $hod->mobile ?? $hod->phone }}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Project Engineer (Only shown if assigned) -->
                @if($hasEngineer)
                    <div style="flex: 1; text-align: center; padding-left: 0.75rem;">
                        <h4 style="font-size: 0.95rem; font-weight: 700; margin-top: 0; margin-bottom: 0.85rem; color: #0f172a;">Project Engineer</h4>
                        @if($engPhotoUrl)
                            <img src="{{ $engPhotoUrl }}" alt="Project Engineer" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid #bfdbfe; margin: 0 auto 0.75rem auto; display: block;">
                        @else
                            <div class="avatar-circle" style="width: 80px; height: 80px; font-size: 2rem; background: #eff6ff; color: #3b82f6; border-color: #bfdbfe; margin: 0 auto 0.75rem auto;">
                                <i class="bx bxs-hard-hat"></i>
                            </div>
                        @endif
                        <h5 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: #0f172a;">{{ $engineer->name }}</h5>
                        <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.25rem; word-break: break-all;">
                            {{ $engineer->email ?? 'N/A' }}
                        </div>
                        @if($engineer->mobile || $engineer->phone)
                            <div style="font-size: 0.78rem; color: #2563eb; font-weight: 600; margin-top: 0.15rem;">
                                <i class="bx bx-phone" style="color: #3b82f6;"></i> {{ $engineer->mobile ?? $engineer->phone }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Card 3: Financial Report -->
            <div class="report-card">
                <h3 class="report-card-title">Financial Report</h3>
                <div class="info-line">
                    <span class="info-label">Total Allocated :</span>
                    <span class="info-val">₹{{ number_format($totalAllocated, 2) }}</span>
                </div>
                <div class="info-line">
                    <span class="info-label">Community Contribution :</span>
                    <span class="info-val">₹{{ number_format($totalCommunityContrib, 2) }}</span>
                </div>
                
                <hr style="border: none; border-top: 1px dashed #e2e8f0; margin: 0.5rem 0;">

                <div class="info-line">
                    <span class="info-label">Total Grants :</span>
                    <span class="info-val">₹{{ number_format($totalGrants, 2) }}</span>
                </div>
                <div class="info-line">
                    <span class="info-label">Community Contribution :</span>
                    <span class="info-val">₹{{ number_format($totalCommunityContrib, 2) }}</span>
                </div>
                <div class="info-line">
                    <span class="info-label">Leverage :</span>
                    <span class="info-val">₹{{ number_format($leverage, 2) }}</span>
                </div>
                <div class="info-line">
                    <span class="info-label">Any Other :</span>
                    <span class="info-val">₹{{ number_format($anyOther, 2) }}</span>
                </div>
                <div class="info-line">
                    <span class="info-label">Deductions :</span>
                    <span class="info-val">₹{{ number_format($deductions, 2) }}</span>
                </div>
                <div class="info-line" style="border-top: 1px solid #e2e8f0; padding-top: 0.75rem; margin-top: 0.25rem;">
                    <span class="info-label" style="font-size: 0.95rem; font-weight: 700; color: #0f172a;">Total Project Cost :</span>
                    <span class="info-val" style="font-size: 1.1rem; font-weight: 800; color: #10b981;">₹{{ number_format($totalProjectCost, 2) }}</span>
                </div>

                @php
                    $benefitedPeopleCount = $completionDetails['total_beneficiary_peoples'] ?? ($projectObj->total_beneficiary_peoples ?? ($projectObj->num_benefited_people ?? 0));
                    $benefitedFamilyCount = $completionDetails['total_family'] ?? ($projectObj->total_family ?? 0);
                @endphp
                @if($benefitedPeopleCount > 0 || $benefitedFamilyCount > 0)
                <div class="info-line" style="border-top: 1px dashed #e2e8f0; padding-top: 0.5rem; margin-top: 0.5rem;">
                    <span class="info-label">Benefited People :</span>
                    <span class="info-val" style="font-weight: 700; color: #0284c7;">{{ number_format($benefitedPeopleCount) }} people</span>
                </div>
                <div class="info-line">
                    <span class="info-label">Benefited Families :</span>
                    <span class="info-val" style="font-weight: 700; color: #8b5cf6;">{{ number_format($benefitedFamilyCount) }} families</span>
                </div>
                @endif
            </div>

        </div>

        <!-- Middle Grid: Documents & Inspection Report -->
        <div style="display: grid; grid-template-columns: minmax(300px, 450px) 1fr; gap: 1.5rem; flex-wrap: wrap; margin-bottom: 1.5rem;">
            
            <!-- Card 4: Documents Checklist -->
            <div class="report-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">
                    <h3 class="report-card-title" style="margin-bottom: 0;">Documents</h3>
                    @php
                        $totalDocs = count($docFields);
                        $doneDocs = 0;
                        foreach($docFields as $dk => $dl) {
                            $fp = $projectDocument ? $projectDocument->{$dk} : null;
                            $ta = $projectDocument ? $projectDocument->{$dk . '_ticked_at'} : null;
                            if (!$ta && $project && isset($project->files['checklist'][$dk]['ticked_at'])) {
                                $ta = $project->files['checklist'][$dk]['ticked_at'];
                            }
                            if ($dk === 'site_study' && $siteStudyData && ($siteStudyData->report || $siteStudyData->report_text || $siteStudyData->file_path || $siteStudyData->ticked_at)) {
                                $doneDocs++;
                            } elseif (!empty($ta) || (!empty($fp) && $fp !== '0')) {
                                $doneDocs++;
                            }
                        }
                    @endphp
                    <span style="font-size: 0.78rem; font-weight: 700; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 0.2rem 0.6rem; border-radius: 12px;">
                        {{ $doneDocs }} / {{ $totalDocs }} Done
                    </span>
                </div>
                <div style="max-height: 480px; overflow-y: auto; padding-right: 0.35rem;">
                    @foreach($docFields as $docKey => $docLabel)
                        @php
                            $filePath = $projectDocument ? $projectDocument->{$docKey} : null;
                            $tickedAt = $projectDocument ? $projectDocument->{$docKey . '_ticked_at'} : null;
                            if (!$tickedAt && $project && isset($project->files['checklist'][$docKey]['ticked_at'])) {
                                $tickedAt = $project->files['checklist'][$docKey]['ticked_at'];
                            }
                            
                            $isSiteStudy = ($docKey === 'site_study');
                            $hasSiteStudyData = $isSiteStudy && $siteStudyData && ($siteStudyData->report || $siteStudyData->report_text || $siteStudyData->file_path || $siteStudyData->ticked_at);
                            if ($hasSiteStudyData && !$tickedAt) {
                                $tickedAt = $siteStudyData->ticked_at ?? $siteStudyData->updated_at;
                            }

                            $isDone = $hasSiteStudyData || !empty($tickedAt) || (!empty($filePath) && $filePath !== '0');
                        @endphp
                        <div class="doc-item" style="display: flex; justify-content: space-between; align-items: center; padding: 0.65rem 0; border-bottom: 1px solid #f1f5f9; gap: 0.75rem;">
                            <div style="display: flex; flex-direction: column; gap: 0.2rem; min-width: 0;">
                                <span class="info-label" style="display: flex; align-items: center; gap: 0.45rem; color: #1e293b; font-weight: 600; font-size: 0.85rem;">
                                    <i class="bx {{ $isDone ? 'bxs-check-circle' : 'bx-circle' }}" style="color: {{ $isDone ? '#10b981' : '#cbd5e1' }}; font-size: 1.15rem; flex-shrink: 0;"></i>
                                    <span>{{ $docLabel }}</span>
                                </span>
                                @if($tickedAt)
                                    <span style="font-size: 0.72rem; color: #64748b; margin-left: 1.6rem; display: inline-flex; align-items: center; gap: 0.3rem;">
                                        <i class="bx bx-time-five" style="font-size: 0.8rem; color: #059669;"></i>
                                        Ticked: <strong style="color: #047857; font-weight: 600;">{{ \Carbon\Carbon::parse($tickedAt)->timezone('Asia/Kolkata')->format('d/m/Y h:i A') }}</strong>
                                    </span>
                                @elseif($isDone)
                                    <span style="font-size: 0.72rem; color: #10b981; margin-left: 1.6rem; font-weight: 600;">
                                        Ticked
                                    </span>
                                @endif
                            </div>
                            <div style="flex-shrink: 0; display: flex; align-items: center; gap: 0.35rem;">
                                @if($isSiteStudy && $hasSiteStudyData)
                                    <button type="button" onclick="openSiteStudyReportModal()" style="color: #059669; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 0.25rem 0.65rem; border-radius: 6px; font-size: 0.78rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem; cursor: pointer; transition: all 0.15s;" onmouseover="this.style.background='#d1fae5'" onmouseout="this.style.background='#ecfdf5'">
                                        <i class="bx bx-file-find"></i> View
                                    </button>
                                @elseif($isDone)
                                    <span style="color: #059669; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 0.22rem 0.6rem; border-radius: 6px; font-size: 0.74rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.25rem;">
                                        <i class="bx bx-check"></i> Ticked
                                    </span>
                                @else
                                    <span style="color: #94a3b8; font-size: 0.78rem; font-weight: 600; padding: 0.2rem 0.55rem; background: #f8fafc; border-radius: 4px; border: 1px solid #e2e8f0;">
                                        Pending
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Card 5: Inspection Report -->
            <div class="report-card" style="display: flex; flex-direction: column; padding: 0; overflow: hidden;">
                <div style="padding: 1.25rem 1.5rem 0.5rem 1.5rem;">
                    <h3 class="report-card-title" style="margin-bottom: 0.5rem;">Inspection Report</h3>
                </div>
                <div style="flex: 1; overflow-x: auto;">
                    <table class="inspection-table">
                        <thead>
                            <tr>
                                <th>Inspector Name</th>
                                <th>Designation</th>
                                <th>Inspection Date</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inspections as $insp)
                                <tr>
                                    <td style="font-weight: 700; color: #0f172a;">{{ $insp->name }}</td>
                                    <td style="color: #475569;">{{ $insp->designation ?? 'Inspector' }}</td>
                                    <td style="color: #475569;">{{ $insp->date ? date('d/m/Y', strtotime($insp->date)) : 'N/A' }}</td>
                                    <td style="color: #475569;">{{ $insp->remarks ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 2.5rem; color: #64748b; font-style: italic;">
                                        No inspection records filed for this project yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- End of Page: Completed Project Photos Slideshow & Gallery -->
        <div class="report-card" style="margin-bottom: 1.5rem;">
            <h3 class="report-card-title" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <i class="bx bx-images" style="color: #10b981;"></i> Completed Project Photos & Slideshow Gallery
            </h3>

            @if(empty($allProjectPhotos))
                <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 12px; padding: 2.5rem; text-align: center; color: #64748b;">
                    <i class="bx bx-image-alt" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 0.5rem; display: block;"></i>
                    <h4 style="margin: 0 0 0.25rem 0; color: #334155; font-size: 1rem; font-weight: 700;">No Project Photos Uploaded</h4>
                    <p style="margin: 0; font-size: 0.85rem;">Upload project completion photos in the project details section to view them here.</p>
                </div>
            @else
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <!-- Slideshow Viewport -->
                    <div class="slideshow-container" id="projectSlideshowContainer">
                        <span class="slideshow-badge category" id="slideshowCatBadge">Completed Project</span>
                        <span class="slideshow-badge counter" id="slideshowCounterBadge">1 / {{ count($allProjectPhotos) }}</span>

                        <button type="button" class="slideshow-nav-btn prev" onclick="moveSlideshow(-1)" title="Previous Image">
                            <i class="bx bx-chevron-left"></i>
                        </button>

                        <img id="slideshowMainImg" src="{{ $allProjectPhotos[0]['url'] }}" alt="Project Photo" class="slideshow-img" onclick="openPhotoLightbox(this.src)" style="cursor: zoom-in;">

                        <button type="button" class="slideshow-nav-btn next" onclick="moveSlideshow(1)" title="Next Image">
                            <i class="bx bx-chevron-right"></i>
                        </button>
                    </div>

                    <!-- Thumbnails Carousel Strip -->
                    <div class="thumb-strip" id="slideshowThumbStrip">
                        @foreach($allProjectPhotos as $index => $photo)
                            <img src="{{ $photo['url'] }}" 
                                 alt="Thumbnail {{ $index + 1 }}" 
                                 class="thumb-item {{ $index === 0 ? 'active' : '' }}" 
                                 data-category="{{ $photo['category'] }}"
                                 onclick="selectSlideshowIndex({{ $index }})">
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

    @endif
</div>

<!-- Fullscreen Lightbox Modal -->
<div id="photoLightboxModal" onclick="closePhotoLightbox()" style="display: none; position: fixed; z-index: 9999; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(15, 23, 42, 0.9); backdrop-filter: blur(8px); align-items: center; justify-content: center; padding: 2rem;">
    <span style="position: absolute; top: 1.5rem; right: 2rem; color: #ffffff; font-size: 2.2rem; cursor: pointer; font-weight: 700;">&times;</span>
    <img id="lightboxModalImg" src="" alt="Enlarged Photo" style="max-width: 90vw; max-height: 90vh; border-radius: 12px; box-shadow: 0 25px 50px rgba(0,0,0,0.5); object-fit: contain;">
</div>

@if(!empty($allProjectPhotos))
<script>
    const photosData = @json($allProjectPhotos);
    let currentPhotoIdx = 0;

    function renderSlideshowPhoto(idx) {
        if (!photosData || photosData.length === 0) return;
        if (idx < 0) idx = photosData.length - 1;
        if (idx >= photosData.length) idx = 0;

        currentPhotoIdx = idx;
        const photo = photosData[idx];

        const mainImg = document.getElementById('slideshowMainImg');
        const catBadge = document.getElementById('slideshowCatBadge');
        const counterBadge = document.getElementById('slideshowCounterBadge');

        if (mainImg) {
            mainImg.style.opacity = '0.3';
            setTimeout(() => {
                mainImg.src = photo.url;
                mainImg.style.opacity = '1';
            }, 150);
        }

        if (catBadge) catBadge.textContent = photo.category || 'Completed Project';
        if (counterBadge) counterBadge.textContent = `${idx + 1} / ${photosData.length}`;

        // Update active thumbnail
        const thumbs = document.querySelectorAll('.thumb-item');
        thumbs.forEach((t, i) => {
            if (i === idx) {
                t.classList.add('active');
                t.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            } else {
                t.classList.remove('active');
            }
        });
    }

    function moveSlideshow(step) {
        renderSlideshowPhoto(currentPhotoIdx + step);
    }

    function selectSlideshowIndex(idx) {
        renderSlideshowPhoto(idx);
    }

    function openPhotoLightbox(src) {
        const modal = document.getElementById('photoLightboxModal');
        const modalImg = document.getElementById('lightboxModalImg');
        if (modal && modalImg) {
            modalImg.src = src;
            modal.style.display = 'flex';
        }
    }

    function closePhotoLightbox() {
        const modal = document.getElementById('photoLightboxModal');
        if (modal) modal.style.display = 'none';
    }
</script>
@endif

<!-- Site Study Report Modal -->
<div id="siteStudyReportModal" style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center; z-index: 99999; padding: 1.5rem;" onclick="closeSiteStudyReportModal()">
    <div style="background: #ffffff; border-radius: 16px; max-width: 680px; width: 100%; max-height: 85vh; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid #e2e8f0;" onclick="event.stopPropagation()">
        <div style="padding: 1.25rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 0.6rem;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: #ecfdf5; color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; border: 1px solid #a7f3d0;">
                    <i class="bx bx-file-find"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: #0f172a;">Site Study Report</h3>
                    <p style="margin: 0; font-size: 0.8rem; color: #64748b;">Comprehensive field study and evaluation</p>
                </div>
            </div>
            <button onclick="closeSiteStudyReportModal()" style="background: none; border: none; font-size: 1.4rem; color: #94a3b8; cursor: pointer; padding: 0.2rem; display: flex; align-items: center;" onmouseover="this.style.color='#0f172a'" onmouseout="this.style.color='#94a3b8'">
                <i class="bx bx-x"></i>
            </button>
        </div>
        <div style="padding: 1.5rem; overflow-y: auto; flex: 1; color: #334155; font-size: 0.92rem; line-height: 1.6;">
            @php
                $studyText = $siteStudyData->report ?? $siteStudyData->report_text ?? $siteStudyData->remarks ?? null;
            @endphp
            @if(!empty($studyText))
                <div style="white-space: pre-wrap; background: #f8fafc; padding: 1.25rem; border-radius: 10px; border: 1px solid #e2e8f0; font-family: inherit;">{{ $studyText }}</div>
            @else
                <p style="color: #94a3b8; font-style: italic; text-align: center; margin: 2rem 0;">No text report content available.</p>
            @endif

            @if($siteStudyData && $siteStudyData->file_path)
                <div style="margin-top: 1.25rem; padding-top: 1.25rem; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.85rem; color: #64748b; font-weight: 500;">Attached Document File:</span>
                    <a href="{{ asset($siteStudyData->file_path) }}" target="_blank" style="color: #059669; text-decoration: none; font-size: 0.82rem; font-weight: 700; background: #ecfdf5; padding: 0.35rem 0.8rem; border-radius: 6px; border: 1px solid #a7f3d0; display: inline-flex; align-items: center; gap: 0.35rem;">
                        <i class="bx bx-download"></i> Download / View Attached File
                    </a>
                </div>
            @endif
        </div>
        <div style="padding: 1rem 1.5rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end;">
            <button onclick="closeSiteStudyReportModal()" style="background: #e2e8f0; color: #475569; border: none; padding: 0.45rem 1.2rem; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer;">Close</button>
        </div>
    </div>
</div>

<script>
    function openSiteStudyReportModal() {
        const m = document.getElementById('siteStudyReportModal');
        if (m) m.style.display = 'flex';
    }
    function closeSiteStudyReportModal() {
        const m = document.getElementById('siteStudyReportModal');
        if (m) m.style.display = 'none';
    }
</script>

@if(request()->has('print') || request()->has('pdf'))
<script>
    window.addEventListener('load', function() {
        window.print();
    });
</script>
@endif

@endsection
