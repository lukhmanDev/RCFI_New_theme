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
</style>

<div class="container-fluid" style="padding: 1.5rem;">

    <!-- Header Controls -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="color: var(--text-main); font-weight: 700; margin: 0; font-size: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                Single Project Detailed Report
            </h2>
            <p style="color: var(--text-muted); margin: 0.25rem 0 0 0; font-size: 0.88rem;">
                Detailed breakdown of project manager, engineer, documents, inspections, and financial summary
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
            <!-- Project Dropdown Selector -->
            <form method="GET" action="{{ route('admin.reports.single_project') }}" style="display: flex; gap: 0.5rem; align-items: center;">
                <select name="key" class="form-control-dark" style="height: 40px; border-radius: 8px; font-size: 0.88rem; min-width: 280px; font-weight: 600;" onchange="this.form.submit()">
                    @foreach($allProjectsList as $pItem)
                        <option value="{{ $pItem['key'] }}" {{ ($targetProjectData && $targetProjectData['key'] === $pItem['key']) ? 'selected' : '' }}>
                            [{{ $pItem['project_id_str'] }}] {{ $pItem['name'] }} ({{ $pItem['category'] }})
                        </option>
                    @endforeach
                </select>
            </form>

            <button onclick="window.print()" class="btn-custom" style="background: linear-gradient(135deg, #10b981, #059669); color: #ffffff; border: none; padding: 0.65rem 1.25rem; font-size: 0.88rem; border-radius: 10px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);">
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
            
            <!-- Card 1: Project Overview Info -->
            <div class="report-card">
                <div class="info-line">
                    <span class="info-label">Project Id :</span>
                    <span class="info-val" style="color: #10b981; font-size: 1.05rem; font-weight: 800;">{{ $targetProjectData['project_id_str'] }}</span>
                </div>
                <div class="info-line">
                    <span class="info-label">Application :</span>
                    <span class="info-val">{{ $application ? ($application->applicant_name ?? 'APL#' . $application->id) : ($projectObj->project_name ?? $projectObj->name ?? 'N/A') }}</span>
                </div>
                <div class="info-line">
                    <span class="info-label">Estimate Amount :</span>
                    <span class="info-val" style="color: #10b981; font-weight: 800;">₹{{ number_format($totalAllocated, 2) }}</span>
                </div>
                <div class="info-line">
                    <span class="info-label">Project Manager :</span>
                    <span class="info-val">{{ $projectManager ? $projectManager->name : 'N/A' }}</span>
                </div>
                <div class="info-line">
                    <span class="info-label">Engineer :</span>
                    <span class="info-val">{{ $engineer ? $engineer->name : 'N/A' }}</span>
                </div>
                <div class="info-line">
                    <span class="info-label">Category :</span>
                    <span class="info-val" style="background: #ecfdf5; color: #059669; padding: 0.2rem 0.6rem; border-radius: 6px; font-size: 0.78rem; font-weight: 700;">{{ $targetProjectData['category'] }}</span>
                </div>
                <div class="info-line">
                    <span class="info-label">Status :</span>
                    <span class="info-val" style="background: #ecfdf5; color: #10b981; padding: 0.2rem 0.6rem; border-radius: 6px; font-size: 0.78rem; font-weight: 700; text-transform: uppercase;">{{ $projectObj->status ?? 'Active' }}</span>
                </div>
            </div>

            <!-- Card 2: Team Staff (Project Manager & Engineer) -->
            <div class="report-card" style="display: flex; gap: 1rem;">
                <!-- Manager Half -->
                <div style="flex: 1; text-align: center; border-right: 1px solid #f1f5f9; padding-right: 0.75rem;">
                    <h4 style="font-size: 1rem; font-weight: 700; margin-top: 0; margin-bottom: 1rem; color: #0f172a;">Project Manager</h4>
                    <div class="avatar-circle">
                        <i class="bx bxs-user"></i>
                    </div>
                    <h5 style="margin: 0; font-size: 1rem; font-weight: 700; color: #0f172a;">{{ $projectManager ? $projectManager->name : 'Unassigned' }}</h5>
                    <p style="margin: 0.25rem 0 0 0; font-size: 0.8rem; color: #64748b;">
                        {{ $projectManager ? ($projectManager->email ?? 'Manager') : 'Project Manager' }}
                    </p>
                </div>

                <!-- Engineer Half -->
                <div style="flex: 1; text-align: center; padding-left: 0.75rem;">
                    <h4 style="font-size: 1rem; font-weight: 700; margin-top: 0; margin-bottom: 1rem; color: #0f172a;">Project Engineer</h4>
                    <div class="avatar-circle">
                        <i class="bx bxs-hard-hat"></i>
                    </div>
                    <h5 style="margin: 0; font-size: 1rem; font-weight: 700; color: #0f172a;">{{ $engineer ? $engineer->name : 'Unassigned' }}</h5>
                    <p style="margin: 0.25rem 0 0 0; font-size: 0.8rem; color: #64748b;">
                        {{ $engineer ? ($engineer->email ?? 'Engineer') : 'Project Engineer' }}
                    </p>
                </div>
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
                
                <hr style="border: none; border-top: 1px dashed #e2e8f0; margin: 0.75rem 0;">

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
            </div>

        </div>

        <!-- Bottom Grid: Documents & Inspection Report -->
        <div style="display: grid; grid-template-columns: minmax(300px, 450px) 1fr; gap: 1.5rem; flex-wrap: wrap;">
            
            <!-- Card 4: Documents Checklist -->
            <div class="report-card">
                <h3 class="report-card-title">Documents</h3>
                <div style="max-height: 480px; overflow-y: auto; padding-right: 0.35rem;">
                    @foreach($docFields as $docKey => $docLabel)
                        @php
                            $filePath = $projectDocument ? $projectDocument->{$docKey} : null;
                            $tickedAt = $projectDocument ? $projectDocument->{$docKey . '_ticked_at'} : null;
                            $isDone = !empty($filePath) || !empty($tickedAt);
                        @endphp
                        <div class="doc-item">
                            <span class="info-label" style="display: flex; align-items: center; gap: 0.4rem; color: #334155;">
                                <i class="bx {{ $isDone ? 'bxs-check-circle' : 'bx-circle' }}" style="color: {{ $isDone ? '#10b981' : '#cbd5e1' }}; font-size: 1.1rem;"></i>
                                {{ $docLabel }}-
                            </span>
                            <div>
                                @if(!empty($filePath))
                                    <a href="{{ asset($filePath) }}" target="_blank" style="color: #059669; text-decoration: none; font-size: 0.78rem; font-weight: 700; background: #ecfdf5; padding: 0.2rem 0.6rem; border-radius: 4px; border: 1px solid #a7f3d0;">
                                        <i class="bx bx-download"></i> View
                                    </a>
                                @elseif($isDone)
                                    <span style="color: #10b981; font-size: 0.78rem; font-weight: 700;">Verified</span>
                                @else
                                    <span style="color: #94a3b8; font-size: 0.78rem; font-weight: 600;">Pending</span>
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
                                    <td style="color: #475569;">{{ $insp->date ? date('d M, Y', strtotime($insp->date)) : 'N/A' }}</td>
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

    @endif
</div>

@if(request()->has('print') || request()->has('pdf'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            window.print();
        }, 500);
    });
</script>
@endif
@endsection
