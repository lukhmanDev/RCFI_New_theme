<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Report - {{ $projectObj->project_id ?? ($project->project_id ?? 'PDF') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        @page {
            size: A4;
            margin: 10mm 12mm 10mm 12mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #0f172a;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            font-size: 11px;
            line-height: 1.4;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
            font-feature-settings: "cv02", "cv03", "cv04", "cv11";
        }

        /* Top Action Bar (Hidden in Print) */
        .pdf-actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #0f172a;
            color: #ffffff;
            padding: 10px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .pdf-actions-bar h3 {
            margin: 0;
            font-size: 13px;
            font-weight: 700;
        }

        .btn-print {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #ffffff;
            border: none;
            padding: 7px 16px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
            transition: opacity 0.2s;
        }

        .btn-print:hover {
            opacity: 0.9;
        }

        /* Watermark Background */
        .watermark-bg {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 320px;
            opacity: 0.035;
            z-index: -1;
            pointer-events: none;
        }

        .watermark-bg img {
            width: 100%;
            height: auto;
        }

        /* Document Header */
        .doc-header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #00b074;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }

        .doc-title {
            font-size: 19px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
            margin: 0;
            line-height: 1.15;
            text-transform: uppercase;
        }

        .doc-subtitle {
            font-size: 11px;
            font-weight: 600;
            color: #00b074;
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .doc-meta-box {
            font-size: 10px;
            color: #475569;
            margin-top: 4px;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 9.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .badge-running, .badge-active {
            background-color: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }

        .badge-completed {
            background-color: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .badge-pending {
            background-color: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .badge-default {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        /* Section Headings */
        .section-header {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 6px;
            page-break-after: avoid;
        }

        .section-badge-td {
            white-space: nowrap;
            padding-right: 8px;
        }

        .section-badge {
            display: inline-block;
            background: #f1f5f9;
            color: #0f172a;
            font-size: 10.5px;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            padding: 3px 8px;
            border-radius: 4px;
            border-left: 3.5px solid #00b074;
        }

        .section-line-td {
            width: 100%;
            vertical-align: middle;
        }

        .section-line-td div {
            border-bottom: 1px solid #e2e8f0;
            width: 100%;
        }

        /* Two Column Grid Sections */
        .grid-2col-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }

        .grid-2col-table > tbody > tr > td {
            width: 50%;
            vertical-align: top;
        }

        .grid-2col-table > tbody > tr > td:first-child {
            padding-right: 12px;
        }

        .grid-2col-table > tbody > tr > td:last-child {
            padding-left: 12px;
            border-left: 1px solid #f1f5f9;
        }

        /* Field Row Table */
        .field-list-table {
            width: 100%;
            border-collapse: collapse;
        }

        .field-list-table tr {
            border-bottom: 1px solid #f8fafc;
        }

        .field-list-table td {
            padding: 3px 0;
            vertical-align: top;
            font-size: 10.5px;
        }

        .field-list-table td.lbl {
            width: 125px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.03em;
        }

        .field-list-table td.cln {
            width: 10px;
            text-align: center;
            color: #cbd5e1;
            font-weight: 700;
        }

        .field-list-table td.val {
            font-weight: 600;
            color: #0f172a;
            word-break: break-word;
        }

        .val-highlight {
            color: #00b074 !important;
            font-weight: 700 !important;
        }

        /* Data Tables */
        .pdf-data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            margin-bottom: 12px;
            font-size: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
        }

        .pdf-data-table thead th {
            background-color: #00b074 !important;
            color: #ffffff !important;
            padding: 6px 8px;
            text-align: left;
            font-weight: 700;
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            border: none;
        }

        .pdf-data-table tbody tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .pdf-data-table tbody td {
            padding: 5.5px 8px;
            border-bottom: 1px solid #e2e8f0;
            color: #1e293b;
            vertical-align: middle;
        }

        .pdf-data-table tfoot td {
            background-color: #f1f5f9;
            padding: 6.5px 8px;
            border-top: 1.5px solid #cbd5e1;
        }

        /* Document Checklist Badges Grid */
        .doc-checklist-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            margin-bottom: 10px;
        }

        .doc-checklist-table td {
            width: 33.33%;
            padding: 4px 6px;
            vertical-align: middle;
            font-size: 9.5px;
        }

        .doc-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 600;
            line-height: 1.2;
            width: 100%;
        }

        .doc-badge.is-checked {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .doc-badge.is-checked .doc-icon {
            font-weight: 800;
            color: #059669;
            font-size: 10px;
        }

        .doc-badge.is-unchecked {
            background-color: #f8fafc;
            color: #94a3b8;
            border: 1px solid #e2e8f0;
        }

        .doc-badge.is-unchecked .doc-icon {
            color: #cbd5e1;
            font-size: 9px;
        }

        /* Photo Grid */
        .photos-grid-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            margin-bottom: 12px;
        }

        .photos-grid-table td {
            width: 33.33%;
            padding: 4px;
            vertical-align: top;
        }

        .photo-card {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
            background: #ffffff;
            text-align: center;
        }

        .photo-card img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            display: block;
        }

        .photo-card-caption {
            padding: 4px 6px;
            font-size: 8.5px;
            font-weight: 700;
            color: #475569;
            background: #f8fafc;
            border-top: 1px solid #f1f5f9;
            text-transform: uppercase;
        }

        .section-header, .grid-2col-table, .pdf-data-table, .doc-checklist-table {
            page-break-inside: avoid;
        }

        .page-break {
            page-break-before: always;
        }

        @media print {
            .pdf-actions-bar {
                display: none !important;
            }
            body {
                padding: 0;
            }
            .section-header, .grid-2col-table, .pdf-data-table, .doc-checklist-table {
                page-break-inside: avoid;
            }
            @page {
                margin: 8mm 10mm;
            }
        }
    </style>
</head>
<body>

    <!-- WATERMARK LOGO -->
    <div class="watermark-bg">
        <img src="{{ asset('images/pdf logo.png') }}" alt="Watermark">
    </div>

    <!-- TOP ACTION BAR -->
    <div class="pdf-actions-bar">
        <h3>Project & Application Report &bull; {{ $projectObj->project_id ?? ($project->project_id ?? 'Report') }}</h3>
        <div>
            <button onclick="window.print()" class="btn-print">
                &#128438; &nbsp;Print / Save PDF
            </button>
        </div>
    </div>

    @php
        $categoryDisplayName = $targetProjectData['category'] 
            ?? ($targetProjectData['category_name'] 
            ?? ($projectObj->type_of_project ?? ($project->type_of_project ?? 'Project Report')));
        
        $statusText = $projectObj->status ?? ($project->status ?? 'Active');
        $bClass = match(strtolower($statusText)) {
            'running', 'active' => 'badge-running',
            'completed' => 'badge-completed',
            'pending' => 'badge-pending',
            default => 'badge-default'
        };

        // Resolved Agency Name
        $resolvedAgency = $projectObj->donor?->name 
            ?? ($project->donor?->name 
            ?? ($projectObj->agency_name 
            ?? ($project->agency_name 
            ?? ($application->agency_name 
            ?? ($application->meta['agency_name'] ?? null)))));
        if ((!$resolvedAgency || is_numeric($resolvedAgency)) && !empty($projectObj->donor_id ?? $project->donor_id)) {
            $resolvedAgency = \App\Models\Donor::find($projectObj->donor_id ?? $project->donor_id)?->name ?? $resolvedAgency;
        }
        if (is_numeric($resolvedAgency)) {
            $resolvedAgency = \App\Models\Donor::find($resolvedAgency)?->name ?? '—';
        }
        $resolvedAgency = $resolvedAgency ?: '—';

        $resolvedSponsor = $projectObj->sponsor ?? ($project->sponsor ?? '—');

        $budgetAmount = $projectObj->available_budget ?? ($project->available_budget ?? ($totalAllocated ?? ($projectObj->budget ?? 0)));
        $allocatedAmount = $totalAllocated ?? 0;
        $spentAmount = $totalSpent ?? 0;
        $balanceAmount = $allocatedAmount - $spentAmount;
        $costAmount = $totalProjectCost ?? 0;

        $projectPhaseText = $projectObj->status === 'Completed' 
            ? 'Completed' 
            : ($projectObj->project_phase === 'Other' 
                ? ($projectObj->project_phase_custom ?: 'Other') 
                : ($projectObj->project_phase ?? '—'));
    @endphp

    <!-- DOCUMENT HEADER -->
    <table class="doc-header-table">
        <tr>
            <td style="vertical-align: middle;">
                <div class="doc-title">Project & Application Report</div>
                <div class="doc-subtitle">
                    {{ $categoryDisplayName }} &bull; RCFI Project Intelligence
                </div>
            </td>
            <td style="text-align: right; vertical-align: middle;">
                <img src="{{ asset('images/logo.png') }}" alt="RCFI Logo" style="height: 38px; width: auto; object-fit: contain; margin-bottom: 2px;"><br>
                <div class="doc-meta-box">
                    <strong>Project ID:</strong> {{ $projectObj->project_id ?? ($project->project_id ?? '—') }} &nbsp;|&nbsp; 
                    <strong>Generated:</strong> {{ date('d/m/Y H:i') }} &nbsp;|&nbsp;
                    <strong>Status:</strong> <span class="status-badge {{ $bClass }}">{{ $statusText }}</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- SECTION 1: PROJECT SPECIFICATIONS & METADATA -->
    <table class="section-header">
        <tr>
            <td class="section-badge-td"><span class="section-badge">1. Project Specifications & Overview</span></td>
            <td class="section-line-td"><div></div></td>
        </tr>
    </table>

    <table class="grid-2col-table">
        <tbody>
            <tr>
                <td>
                    <table class="field-list-table">
                        <tr>
                            <td class="lbl">RCFI Project ID</td>
                            <td class="cln">:</td>
                            <td class="val val-highlight">{{ $projectObj->project_id ?? ($project->project_id ?? '—') }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">Project Name</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $projectObj->project_name ?? ($project->project_name ?? '—') }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">Agency Name</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $resolvedAgency }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">Sponsor Name</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $resolvedSponsor }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">Agency Project No</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $projectObj->agency_project_no ?? ($project->agency_project_no ?? '—') }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">Type of Project</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $categoryDisplayName }}</td>
                        </tr>
                        @if(!empty($projectObj->theme ?? $project->theme))
                        <tr>
                            <td class="lbl">Theme & Subtheme</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $projectObj->theme ?? $project->theme }} @if(!empty($projectObj->subtheme ?? $project->subtheme)) / {{ $projectObj->subtheme ?? $project->subtheme }} @endif</td>
                        </tr>
                        @endif
                        @if(!empty($projectObj->activity ?? $project->activity))
                        <tr>
                            <td class="lbl">Activity</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $projectObj->activity ?? $project->activity }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="lbl">Operating Unit</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $projectObj->unit ?? ($project->unit ?? 'RCFI') }}</td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table class="field-list-table">
                        <tr>
                            <td class="lbl">Place / Location</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $projectObj->place ?? ($project->place ?? ($projectObj->location ?? ($project->location ?? ($application->place ?? ($application->village ?? '—'))))) }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">District & State</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $projectObj->district ?? ($project->district ?? ($application->district ?? '—')) }}, {{ $projectObj->state ?? ($project->state ?? ($application->state ?? 'Kerala')) }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">Available Budget</td>
                            <td class="cln">:</td>
                            <td class="val val-highlight">₹{{ number_format((float)$budgetAmount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">Total Allocated</td>
                            <td class="cln">:</td>
                            <td class="val">₹{{ number_format((float)$allocatedAmount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">Total Spent</td>
                            <td class="cln">:</td>
                            <td class="val">₹{{ number_format((float)$spentAmount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">Balance Available</td>
                            <td class="cln">:</td>
                            <td class="val val-highlight" style="color: {{ $balanceAmount >= 0 ? '#059669' : '#dc2626' }} !important;">₹{{ number_format((float)$balanceAmount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">Current Stage / Phase</td>
                            <td class="cln">:</td>
                            <td class="val">Stage {{ $projectObj->stage ?? ($project->stage ?? '1') }} &bull; {{ $projectPhaseText }}</td>
                        </tr>
                        @if(!empty($projectObj->remarks ?? $project->remarks))
                        <tr>
                            <td class="lbl">Project Remarks</td>
                            <td class="cln">:</td>
                            <td class="val" style="font-weight: 500; color: #475569;">{{ $projectObj->remarks ?? $project->remarks }}</td>
                        </tr>
                        @endif
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    @if(!empty($projectObj->project_spec ?? $project->project_spec))
    <table class="grid-2col-table" style="margin-top: 2px;">
        <tbody>
            <tr>
                <td colspan="2" style="width: 100%; padding: 0;">
                    <table class="field-list-table">
                        <tr>
                            <td class="lbl" style="width: 125px;">Scope / Specification</td>
                            <td class="cln">:</td>
                            <td class="val" style="font-weight: 500; color: #334155; white-space: pre-wrap;">{{ $projectObj->project_spec ?? $project->project_spec }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    @endif

    <!-- SECTION 2: APPLICATION & BENEFICIARY DETAILS -->
    @if(isset($application) && $application)
    @php
        $appMeta = (isset($application->meta) && is_array($application->meta)) ? $application->meta : [];
    @endphp
    <table class="section-header">
        <tr>
            <td class="section-badge-td"><span class="section-badge">2. Application & Beneficiary Profile</span></td>
            <td class="section-line-td"><div></div></td>
        </tr>
    </table>

    <table class="grid-2col-table">
        <tbody>
            <tr>
                <td>
                    <table class="field-list-table">
                        <tr>
                            <td class="lbl">Application ID</td>
                            <td class="cln">:</td>
                            <td class="val val-highlight">{{ $application->application_id ?? ('APLRCFI'.$application->id) }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">Applicant Name</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $application->applicant_name ?? ($appMeta['applicant_name'] ?? '—') }}</td>
                        </tr>
                        @if(!empty($application->committee_name ?? ($appMeta['committee_name'] ?? null)))
                        <tr>
                            <td class="lbl">Committee / Trust</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $application->committee_name ?? $appMeta['committee_name'] }}</td>
                        </tr>
                        @endif
                        @if(!empty($application->reg_number ?? ($appMeta['reg_number'] ?? null)))
                        <tr>
                            <td class="lbl">Reg Number & Year</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $application->reg_number ?? $appMeta['reg_number'] }} @if(!empty($application->year ?? ($appMeta['year'] ?? null))) (Year: {{ $application->year ?? $appMeta['year'] }}) @endif</td>
                        </tr>
                        @endif
                        @if(!empty($application->father_name ?? ($application->guardian_name ?? ($appMeta['father_name'] ?? ($appMeta['guardian_name'] ?? null)))))
                        <tr>
                            <td class="lbl">Father / Guardian</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $application->father_name ?? ($application->guardian_name ?? ($appMeta['father_name'] ?? ($appMeta['guardian_name'] ?? '—'))) }}</td>
                        </tr>
                        @endif
                        @if(!empty($application->mother_name ?? ($application->spouse_name ?? ($appMeta['mother_name'] ?? ($appMeta['spouse_name'] ?? null)))))
                        <tr>
                            <td class="lbl">Mother / Spouse</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $application->mother_name ?? ($application->spouse_name ?? ($appMeta['mother_name'] ?? ($appMeta['spouse_name'] ?? '—'))) }}</td>
                        </tr>
                        @endif
                        @if(!empty($application->gender ?? ($appMeta['gender'] ?? null)))
                        <tr>
                            <td class="lbl">Gender & Age</td>
                            <td class="cln">:</td>
                            <td class="val">{{ strtoupper($application->gender ?? $appMeta['gender']) }} @if(!empty($application->age ?? ($appMeta['age'] ?? null))) &bull; Age: {{ $application->age ?? $appMeta['age'] }} @endif</td>
                        </tr>
                        @endif
                        @if(!empty($application->amount_requested ?? ($appMeta['amount_requested'] ?? null)))
                        <tr>
                            <td class="lbl">Amount Requested</td>
                            <td class="cln">:</td>
                            <td class="val val-highlight">₹{{ number_format((float)($application->amount_requested ?? $appMeta['amount_requested']), 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="lbl">Application Status</td>
                            <td class="cln">:</td>
                            <td class="val"><span class="status-badge badge-completed">{{ $application->status ?? 'Approved' }}</span></td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table class="field-list-table">
                        <tr>
                            <td class="lbl">Primary Mobile</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $application->contact_number_1 ?? ($application->mobile_1 ?? ($application->mobile ?? ($appMeta['contact_number_1'] ?? ($appMeta['mobile_1'] ?? '—')))) }}</td>
                        </tr>
                        @if(!empty($application->contact_number_2 ?? ($application->mobile_2 ?? ($appMeta['contact_number_2'] ?? ($appMeta['mobile_2'] ?? null)))))
                        <tr>
                            <td class="lbl">Secondary Mobile</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $application->contact_number_2 ?? ($application->mobile_2 ?? ($appMeta['contact_number_2'] ?? $appMeta['mobile_2'])) }}</td>
                        </tr>
                        @endif
                        @if(!empty($application->whatsapp_number ?? ($appMeta['whatsapp_number'] ?? null)))
                        <tr>
                            <td class="lbl">WhatsApp Number</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $application->whatsapp_number ?? $appMeta['whatsapp_number'] }}</td>
                        </tr>
                        @endif
                        @if(!empty($application->contact_email ?? ($application->email ?? ($appMeta['contact_email'] ?? null))))
                        <tr>
                            <td class="lbl">Contact Email</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $application->contact_email ?? ($application->email ?? $appMeta['contact_email']) }}</td>
                        </tr>
                        @endif
                        @if(!empty($application->house_name ?? ($appMeta['house_name'] ?? ($application->address ?? null))))
                        <tr>
                            <td class="lbl">House / Address</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $application->house_name ?? ($appMeta['house_name'] ?? ($application->address ?? '—')) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="lbl">Place / Village</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $application->place ?? ($application->village ?? ($appMeta['place'] ?? ($appMeta['village'] ?? '—'))) }}</td>
                        </tr>
                        @if(!empty($application->post_office ?? ($appMeta['post_office'] ?? null)))
                        <tr>
                            <td class="lbl">Post Office</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $application->post_office ?? $appMeta['post_office'] }} P/O</td>
                        </tr>
                        @endif
                        @if(!empty($application->panchayat ?? ($application->panjayath ?? ($appMeta['panchayat'] ?? null))))
                        <tr>
                            <td class="lbl">Panchayat / Local Body</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $application->panchayat ?? ($application->panjayath ?? $appMeta['panchayat']) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="lbl">District & State</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $application->district ?? ($appMeta['district'] ?? '—') }}, {{ $application->state ?? ($appMeta['state'] ?? 'Kerala') }} @if(!empty($application->pin_code ?? ($appMeta['pin_code'] ?? null))) (PIN: {{ $application->pin_code ?? $appMeta['pin_code'] }}) @endif</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- SECTION 3: LOCALITY, SITE & SCOPE DETAILS -->
    @php
        $hasLocality = !empty($application->mahallu_name ?? ($appMeta['mahallu_name'] ?? null)) 
            || !empty($application->families_in_mahallu ?? ($appMeta['families_in_mahallu'] ?? null)) 
            || !empty($application->building_area_sq ?? ($appMeta['building_area_sq'] ?? null)) 
            || !empty($application->land_area_sq ?? ($appMeta['land_area_sq'] ?? null)) 
            || !empty($application->land_owner_name ?? ($appMeta['land_owner_name'] ?? null)) 
            || !empty($application->total_students ?? ($application->num_students ?? ($appMeta['total_students'] ?? null))) 
            || !empty($application->num_classrooms ?? ($appMeta['num_classrooms'] ?? null)) 
            || !empty($application->well_type ?? ($appMeta['well_type'] ?? null)) 
            || !empty($application->recommendation_name ?? ($appMeta['recommendation_name'] ?? null))
            || !empty($projectObj->total_beneficiary_peoples ?? ($application->total_beneficiary_peoples ?? null))
            || !empty($projectObj->total_family ?? ($application->total_family ?? null));
    @endphp

    @if($hasLocality)
    <table class="section-header">
        <tr>
            <td class="section-badge-td"><span class="section-badge">3. Locality, Site & Scope Parameters</span></td>
            <td class="section-line-td"><div></div></td>
        </tr>
    </table>

    <table class="grid-2col-table">
        <tbody>
            <tr>
                <td>
                    <table class="field-list-table">
                        @if(!empty($application->mahallu_name ?? ($appMeta['mahallu_name'] ?? null)))
                        <tr>
                            <td class="lbl">Mahallu Name</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $application->mahallu_name ?? $appMeta['mahallu_name'] }}</td>
                        </tr>
                        @endif
                        @if(!empty($application->families_in_mahallu ?? ($appMeta['families_in_mahallu'] ?? null)))
                        <tr>
                            <td class="lbl">Families in Mahallu</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $application->families_in_mahallu ?? $appMeta['families_in_mahallu'] }} Families</td>
                        </tr>
                        @endif
                        @if(!empty($projectObj->total_beneficiary_peoples ?? ($application->total_beneficiary_peoples ?? null)))
                        <tr>
                            <td class="lbl">Benefited Peoples</td>
                            <td class="cln">:</td>
                            <td class="val val-highlight">{{ number_format((float)($projectObj->total_beneficiary_peoples ?? $application->total_beneficiary_peoples)) }} Persons</td>
                        </tr>
                        @endif
                        @if(!empty($projectObj->total_family ?? ($application->total_family ?? null)))
                        <tr>
                            <td class="lbl">Benefited Families</td>
                            <td class="cln">:</td>
                            <td class="val val-highlight">{{ number_format((float)($projectObj->total_family ?? $application->total_family)) }} Families</td>
                        </tr>
                        @endif
                        @if(!empty($application->building_area_sq ?? ($appMeta['building_area_sq'] ?? null)))
                        <tr>
                            <td class="lbl">Building Area</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $application->building_area_sq ?? $appMeta['building_area_sq'] }} Sq.Ft</td>
                        </tr>
                        @endif
                        @if(!empty($application->land_area_sq ?? ($appMeta['land_area_sq'] ?? null)))
                        <tr>
                            <td class="lbl">Land Area</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $application->land_area_sq ?? $appMeta['land_area_sq'] }} Sq.Ft</td>
                        </tr>
                        @endif
                        @if(!empty($application->land_owner_name ?? ($appMeta['land_owner_name'] ?? null)))
                        <tr>
                            <td class="lbl">Land Owner</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $application->land_owner_name ?? $appMeta['land_owner_name'] }} @if(!empty($application->land_owner_mobile ?? ($appMeta['land_owner_mobile'] ?? null))) ({{ $application->land_owner_mobile ?? $appMeta['land_owner_mobile'] }}) @endif</td>
                        </tr>
                        @endif
                    </table>
                </td>
                <td>
                    <table class="field-list-table">
                        @if(!empty($application->total_students ?? ($application->num_students ?? ($appMeta['total_students'] ?? null))))
                        <tr>
                            <td class="lbl">Total Students</td>
                            <td class="cln">:</td>
                            <td class="val val-highlight">{{ $application->total_students ?? ($application->num_students ?? $appMeta['total_students']) }} Students</td>
                        </tr>
                        @endif
                        @if(!empty($application->num_classrooms ?? ($appMeta['num_classrooms'] ?? null)))
                        <tr>
                            <td class="lbl">Classrooms Count</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $application->num_classrooms ?? $appMeta['num_classrooms'] }} Classrooms</td>
                        </tr>
                        @endif
                        @if(!empty($application->well_type ?? ($appMeta['well_type'] ?? null)))
                        <tr>
                            <td class="lbl">Well Type & Depth</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $application->well_type ?? $appMeta['well_type'] }} @if(!empty($application->well_depth ?? ($appMeta['well_depth'] ?? null))) (Depth: {{ $application->well_depth ?? $appMeta['well_depth'] }}) @endif</td>
                        </tr>
                        @endif
                        @if(!empty($application->site_has_building ?? ($appMeta['site_has_building'] ?? null)))
                        <tr>
                            <td class="lbl">Site Status</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $application->site_has_building ?? $appMeta['site_has_building'] }} @if(!empty($application->status_of_current_building ?? ($appMeta['status_of_current_building'] ?? null))) ({{ $application->status_of_current_building ?? $appMeta['status_of_current_building'] }}) @endif</td>
                        </tr>
                        @endif
                        @if(!empty($application->legal_approvals_status ?? ($application->legal_permissions ?? ($appMeta['legal_approvals_status'] ?? null))))
                        <tr>
                            <td class="lbl">Legal Approvals</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $application->legal_approvals_status ?? ($application->legal_permissions ?? $appMeta['legal_approvals_status']) }}</td>
                        </tr>
                        @endif
                        @if(!empty($application->recommendation_name ?? ($appMeta['recommendation_name'] ?? null)))
                        <tr>
                            <td class="lbl">Recommended By</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $application->recommendation_name ?? $appMeta['recommendation_name'] }} @if(!empty($application->recommendation_position ?? ($appMeta['recommendation_position'] ?? null))) ({{ $application->recommendation_position ?? $appMeta['recommendation_position'] }}) @endif</td>
                        </tr>
                        @endif
                        @if(!empty($application->recommendation_organization ?? ($appMeta['recommendation_organization'] ?? null)))
                        <tr>
                            <td class="lbl">Recommender Org</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $application->recommendation_organization ?? $appMeta['recommendation_organization'] }} @if(!empty($application->recommendation_phone ?? ($appMeta['recommendation_phone'] ?? null))) | {{ $application->recommendation_phone ?? $appMeta['recommendation_phone'] }} @endif</td>
                        </tr>
                        @endif
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    @endif
    @endif

    <!-- SECTION 4: PROJECT MANAGEMENT & SUPERVISION -->
    <table class="section-header">
        <tr>
            <td class="section-badge-td"><span class="section-badge">4. Project Supervision & Execution Team</span></td>
            <td class="section-line-td"><div></div></td>
        </tr>
    </table>

    <table class="grid-2col-table">
        <tbody>
            <tr>
                <td>
                    <table class="field-list-table">
                        <tr>
                            <td class="lbl">Project Manager</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $projectManager->name ?? ($projectObj->projectManager->name ?? '—') }} @if(!empty($projectManager->mobile ?? null)) ({{ $projectManager->mobile }}) @endif</td>
                        </tr>
                        <tr>
                            <td class="lbl">Project Engineer</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $engineer->name ?? ($projectObj->engineer->name ?? '—') }} @if(!empty($engineer->mobile ?? null)) ({{ $engineer->mobile }}) @endif</td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table class="field-list-table">
                        <tr>
                            <td class="lbl">Contractor</td>
                            <td class="cln">:</td>
                            <td class="val">{{ $contractor->name ?? ($contractor->company_name ?? ($projectObj->contractor->name ?? '—')) }} @if(!empty($contractor->mobile ?? null)) ({{ $contractor->mobile }}) @endif</td>
                        </tr>
                        <tr>
                            <td class="lbl">Start Date</td>
                            <td class="cln">:</td>
                            <td class="val">{{ isset($projectObj->start_date) ? date('d/m/Y', strtotime($projectObj->start_date)) : (isset($project->start_date) ? date('d/m/Y', strtotime($project->start_date)) : '—') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- SECTION 5: DOCUMENT VERIFICATION & COMPLIANCE CHECKLIST -->
    @php
        $docMap = [
            'land_document' => 'Land Document',
            'possession_certificate' => 'Possession Certificate',
            'recommendation_letter' => 'Recommendation Letter',
            'committee_minutes' => 'Committee Minutes',
            'permit_copy' => 'Permit Copy',
            'plan' => 'Architectural Plan',
            'tender_schedule_sheet' => 'Tender Schedule',
            'site_study' => 'Site Study Report',
            'quotations' => 'Quotations',
            'quotations_approval_form' => 'Quotation Approval',
            'work_order_letter' => 'Work Order Letter',
            'meeting_minutes_copy' => 'Meeting Minutes',
            'agreement_with_contractor' => 'Contractor Agreement',
            'agreement_with_committee' => 'Committee Agreement',
            'project_summary_form' => 'Project Summary Form',
        ];
    @endphp

    <table class="section-header">
        <tr>
            <td class="section-badge-td"><span class="section-badge">5. Compliance & Document Verification</span></td>
            <td class="section-line-td"><div></div></td>
        </tr>
    </table>

    <table class="doc-checklist-table">
        <tbody>
            @php $docKeys = array_keys($docMap); $totalDocKeys = count($docKeys); @endphp
            @for($r = 0; $r < $totalDocKeys; $r += 3)
            <tr>
                @for($c = 0; $c < 3; $c++)
                    @php 
                        $currIdx = $r + $c; 
                        $key = $docKeys[$currIdx] ?? null;
                    @endphp
                    @if($key)
                        @php
                            $isUploaded = ($projectDocument && !empty($projectDocument->{$key}) && $projectDocument->{$key} !== '0');
                        @endphp
                        <td>
                            <div class="doc-badge {{ $isUploaded ? 'is-checked' : 'is-unchecked' }}">
                                <span class="doc-icon">{{ $isUploaded ? '✔' : '✖' }}</span>
                                <span>{{ $docMap[$key] }}</span>
                            </div>
                        </td>
                    @else
                        <td></td>
                    @endif
                @endfor
            </tr>
            @endfor
        </tbody>
    </table>

    <!-- SECTION 6: MATERIALS & ALLOCATED EXPENSES -->
    @if(isset($materials) && (is_countable($materials) ? count($materials) : 0) > 0)
    <table class="section-header">
        <tr>
            <td class="section-badge-td"><span class="section-badge">6. Material Allocations & Cost Breakdown</span></td>
            <td class="section-line-td"><div></div></td>
        </tr>
    </table>

    <table class="pdf-data-table">
        <thead>
            <tr>
                <th style="width: 36px; text-align: center;">SL NO</th>
                <th>MATERIAL / ITEM DESCRIPTION</th>
                <th style="text-align: right; width: 120px;">ALLOCATED (INR)</th>
                <th style="text-align: right; width: 120px;">SPENT (INR)</th>
                <th style="text-align: right; width: 120px;">BALANCE (INR)</th>
            </tr>
        </thead>
        <tbody>
            @php $totalMatAlloc = 0; $totalMatSpent = 0; @endphp
            @foreach($materials as $materialIdx => $mat)
            @php
                $matName = is_array($mat) 
                    ? ($mat['material'] ?? ($mat['name'] ?? ($mat['expense_name'] ?? '—'))) 
                    : ($mat->material ?? ($mat->item_name ?? ($mat->expense_name ?? '—')));
                
                $matAllocated = (float)(is_array($mat) ? ($mat['amount'] ?? 0) : ($mat->amount ?? 0));
                $totalMatAlloc += $matAllocated;

                // Calculate spent for this material index if available
                $matSpent = 0;
                if (isset($expenses) && is_iterable($expenses)) {
                    foreach($expenses as $exp) {
                        $eIdx = is_array($exp) ? ($exp['material_index'] ?? null) : ($exp->material_index ?? null);
                        $eAmt = (float)(is_array($exp) ? ($exp['amount'] ?? 0) : ($exp->amount ?? 0));
                        if ($eIdx !== null && (string)$eIdx === (string)$materialIdx) {
                            $matSpent += $eAmt;
                        }
                    }
                }
                $totalMatSpent += $matSpent;
                $matBalance = $matAllocated - $matSpent;
            @endphp
            <tr>
                <td style="text-align: center; font-weight: 700; color: #64748b;">{{ $loop->iteration }}</td>
                <td style="font-weight: 600;">{{ $matName }}</td>
                <td style="text-align: right; font-weight: 600;">₹{{ number_format($matAllocated, 2) }}</td>
                <td style="text-align: right; font-weight: 600; color: #dc2626;">₹{{ number_format($matSpent, 2) }}</td>
                <td style="text-align: right; font-weight: 700; color: {{ $matBalance >= 0 ? '#00b074' : '#dc2626' }};">₹{{ number_format($matBalance, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" style="text-align: right; font-weight: 800; text-transform: uppercase; color: #0f172a;">Total Material Allocation:</td>
                <td style="text-align: right; color: #00b074; font-weight: 800; font-size: 10.5px;">₹{{ number_format($totalMatAlloc, 2) }}</td>
                <td style="text-align: right; color: #dc2626; font-weight: 800; font-size: 10.5px;">₹{{ number_format($totalMatSpent, 2) }}</td>
                <td style="text-align: right; color: #00b074; font-weight: 800; font-size: 10.5px;">₹{{ number_format($totalMatAlloc - $totalMatSpent, 2) }}</td>
            </tr>
        </tfoot>
    </table>
    @endif

    <!-- SECTION 7: FUND ALLOCATION SPLITS -->
    @if(isset($funds) && $funds && (is_countable($funds) ? count($funds) : $funds->count()) > 0)
    <table class="section-header">
        <tr>
            <td class="section-badge-td"><span class="section-badge">7. Fund Allocation Splits</span></td>
            <td class="section-line-td"><div></div></td>
        </tr>
    </table>

    <table class="pdf-data-table">
        <thead>
            <tr>
                <th style="width: 36px; text-align: center;">SL NO</th>
                <th>DONOR / AGENCY</th>
                <th>REFERENCE / CHEQUE NO</th>
                <th>BANK ACCOUNT</th>
                <th style="text-align: right; width: 120px;">AMOUNT (INR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($funds as $i => $fund)
            <tr>
                <td style="text-align: center; font-weight: 700; color: #64748b;">{{ $i + 1 }}</td>
                <td style="font-weight: 600;">{{ $fund->donor_name ?? ($fund->agency ?? '—') }}</td>
                <td>{{ $fund->cheque_no ?? ($fund->reference ?? '—') }}</td>
                <td>{{ $fund->bank_account ?? '—' }}</td>
                <td style="text-align: right; font-weight: 700; color: #00b074;">₹{{ number_format($fund->amount ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align: right; font-weight: 800; text-transform: uppercase; color: #0f172a;">Total Fund Splits:</td>
                <td style="text-align: right; color: #00b074; font-weight: 800; font-size: 10.5px;">₹{{ number_format($funds->sum('amount') ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>
    @endif

    <!-- SECTION 8: COMMUNITY CONTRIBUTIONS -->
    @if(isset($communityContributions) && $communityContributions && (is_countable($communityContributions) ? count($communityContributions) : $communityContributions->count()) > 0)
    <table class="section-header">
        <tr>
            <td class="section-badge-td"><span class="section-badge">8. Community Contributions</span></td>
            <td class="section-line-td"><div></div></td>
        </tr>
    </table>

    <table class="pdf-data-table">
        <thead>
            <tr>
                <th style="width: 36px; text-align: center;">SL NO</th>
                <th>CONTRIBUTION ITEM / DESCRIPTION</th>
                <th style="text-align: right; width: 140px;">VALUE (INR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($communityContributions as $i => $contrib)
            <tr>
                <td style="text-align: center; font-weight: 700; color: #64748b;">{{ $i + 1 }}</td>
                <td style="font-weight: 600;">{{ $contrib->item ?? ($contrib->contributor ?? ($contrib->description ?? 'Community Contribution')) }}</td>
                <td style="text-align: right; font-weight: 700; color: #00b074;">₹{{ number_format((float)($contrib->amount ?? 0), 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" style="text-align: right; font-weight: 800; text-transform: uppercase; color: #0f172a;">Total Community Contribution:</td>
                <td style="text-align: right; color: #00b074; font-weight: 800; font-size: 10.5px;">₹{{ number_format((float)$totalCommunityContrib, 2) }}</td>
            </tr>
        </tfoot>
    </table>
    @endif

    <!-- SECTION 9: SITE INSPECTIONS & VISITS -->
    @if(isset($inspections) && $inspections && (is_countable($inspections) ? count($inspections) : $inspections->count()) > 0)
    <table class="section-header">
        <tr>
            <td class="section-badge-td"><span class="section-badge">9. Site Inspections & Field Visits</span></td>
            <td class="section-line-td"><div></div></td>
        </tr>
    </table>

    <table class="pdf-data-table">
        <thead>
            <tr>
                <th style="width: 36px; text-align: center;">SL NO</th>
                <th style="width: 110px;">DATE</th>
                <th style="width: 180px;">INSPECTOR</th>
                <th>REMARKS / FINDINGS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inspections as $i => $insp)
            @php
                $inspDate = !empty($insp->date) 
                    ? date('d/m/Y', strtotime($insp->date)) 
                    : (!empty($insp->inspection_date) 
                        ? date('d/m/Y', strtotime($insp->inspection_date)) 
                        : (!empty($insp->created_at) ? $insp->created_at->format('d/m/Y') : '—'));
                $inspName = $insp->name ?? ($insp->inspector_name ?? ($insp->inspector ?? '—'));
                $inspDesig = $insp->designation ?? '';
            @endphp
            <tr>
                <td style="text-align: center; font-weight: 700; color: #64748b;">{{ $i + 1 }}</td>
                <td style="font-weight: 600;">{{ $inspDate }}</td>
                <td style="font-weight: 600;">{{ $inspName }} @if($inspDesig) <span style="font-weight: 400; color: #64748b;">({{ $inspDesig }})</span> @endif</td>
                <td>{{ $insp->remarks ?? ($insp->notes ?? '—') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- SECTION 10: PROJECT COMPLETION & SETTLEMENT -->
    @php
        $finalCost = $costAmount > 0 
            ? $costAmount 
            : (($allocatedAmount ?: $budgetAmount) + $totalCommunityContrib + ($completionDetail->any_other ?? 0) - ($completionDetail->deductions ?? 0));
    @endphp
    @if(isset($completionDetail) && $completionDetail)
    <table class="section-header">
        <tr>
            <td class="section-badge-td"><span class="section-badge">10. Completion Settlement Details</span></td>
            <td class="section-line-td"><div></div></td>
        </tr>
    </table>

    <table class="grid-2col-table">
        <tbody>
            <tr>
                <td>
                    <table class="field-list-table">
                        <tr>
                            <td class="lbl">Amount Paid by Donor</td>
                            <td class="cln">:</td>
                            <td class="val val-highlight">₹{{ number_format((float)($completionDetail->amount_paid_by_donor ?? $allocatedAmount), 2) }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">Community Contribution</td>
                            <td class="cln">:</td>
                            <td class="val">₹{{ number_format((float)($totalCommunityContrib ?? ($completionDetail->community_contribution ?? 0)), 2) }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">Any Other Inflows</td>
                            <td class="cln">:</td>
                            <td class="val">₹{{ number_format((float)($completionDetail->any_other ?? 0), 2) }}</td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table class="field-list-table">
                        <tr>
                            <td class="lbl">Deductions</td>
                            <td class="cln">:</td>
                            <td class="val">₹{{ number_format((float)($completionDetail->deductions ?? 0), 2) }}</td>
                        </tr>
                        <tr>
                            <td class="lbl">Total Project Cost</td>
                            <td class="cln">:</td>
                            <td class="val val-highlight" style="font-size: 11.5px;">₹{{ number_format((float)$finalCost, 2) }}</td>
                        </tr>
                        @if(!empty($completionDetail->completion_certificate))
                        <tr>
                            <td class="lbl">Completion Certificate</td>
                            <td class="cln">:</td>
                            <td class="val"><span class="status-badge badge-completed">Verified</span></td>
                        </tr>
                        @endif
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    @endif

    <!-- SECTION 11: PROJECT PHOTOS GALLERY -->
    @if(isset($allProjectPhotos) && !empty($allProjectPhotos))
    <table class="section-header page-break">
        <tr>
            <td class="section-badge-td"><span class="section-badge">11. Project Photo Gallery</span></td>
            <td class="section-line-td"><div></div></td>
        </tr>
    </table>

    <table class="photos-grid-table">
        <tbody>
            @php 
                $photoChunks = array_chunk($allProjectPhotos, 3);
            @endphp
            @foreach($photoChunks as $chunk)
            <tr>
                @foreach($chunk as $p)
                <td>
                    <div class="photo-card">
                        <img src="{{ $p['url'] }}" alt="{{ $p['category'] }}">
                        <div class="photo-card-caption">{{ $p['category'] }}</div>
                    </div>
                </td>
                @endforeach
                @for($k = count($chunk); $k < 3; $k++)
                    <td></td>
                @endfor
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <script>
        window.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() { window.print(); }, 350);
        });
    </script>
</body>
</html>
