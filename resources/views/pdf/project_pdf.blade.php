<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Report - {{ $projectObj->project_id ?? ($project->project_id ?? 'PDF') }}</title>
    <style>
        @page {
            size: A4;
            margin: 12mm 15mm 12mm 15mm;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
            color: #18181b;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            font-size: 12.5px;
            line-height: 1.6;
        }

        /* ─── Top Action Bar (hidden on print) ─────────────────────────── */
        .pdf-actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #0f172a;
            color: #ffffff;
            padding: 10px 18px;
            border-radius: 6px;
            margin-bottom: 24px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.15);
        }
        .pdf-actions-bar h3 {
            margin: 0;
            font-size: 13px;
            font-weight: 600;
            color: #38bdf8;
        }
        .btn-print {
            background: #10b981;
            color: #ffffff;
            border: none;
            padding: 7px 16px;
            border-radius: 5px;
            font-weight: 700;
            font-size: 12px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-print:hover { background: #059669; }

        /* ─── Watermark ──────────────────────────────────────────────────── */
        .watermark-bg {
            position: fixed;
            top: 48%; left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.055;
            width: 520px;
            max-width: 88%;
            pointer-events: none;
            z-index: -1;
            text-align: center;
        }
        .watermark-bg img { width: 100%; height: auto; }

        /* ─── Document Header ────────────────────────────────────────────── */
        .doc-header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        .doc-title {
            font-size: 22px;
            font-weight: 900;
            color: #09090b;
            margin: 0;
            letter-spacing: -0.3px;
            text-transform: uppercase;
        }
        .doc-subtitle {
            font-size: 10.5px;
            color: #71717a;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-top: 3px;
        }
        .doc-meta-td {
            text-align: right;
            vertical-align: top;
        }
        .doc-logo {
            height: 42px;
            width: auto;
            object-fit: contain;
            margin-bottom: 6px;
        }
        .doc-meta-line {
            font-size: 11.5px;
            color: #3f3f46;
            line-height: 1.9;
            text-align: right;
        }
        .doc-meta-line strong { color: #09090b; font-weight: 700; }
        .header-rule {
            border: none;
            border-top: 1.5px solid #d4d4d8;
            margin: 12px 0 18px;
        }

        /* ─── Section heading with rule ─────────────────────────────────── */
        .section-heading-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 22px;
            margin-bottom: 10px;
            page-break-after: avoid;
        }
        .section-num-title {
            white-space: nowrap;
            font-size: 13px;
            font-weight: 800;
            color: #09090b;
            padding-right: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .section-rule-td { width: 100%; vertical-align: middle; }
        .section-rule-td div { border-bottom: 1.5px solid #52525b; }

        /* ─── Details grid ───────────────────────────────────────────────── */
        .details-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .details-grid td {
            padding: 5px 0;
            vertical-align: top;
            font-size: 12px;
            text-transform: uppercase;
        }
        .details-grid td.lbl {
            width: 200px;
            font-weight: 700;
            color: #3f3f46;
            white-space: nowrap;
        }
        .details-grid td.cln {
            width: 14px;
            text-align: center;
            font-weight: 700;
            color: #3f3f46;
        }
        .details-grid td.val {
            font-weight: 700;
            color: #09090b;
            word-break: break-word;
        }

        /* ─── Two-column details layout ──────────────────────────────────── */
        .two-col-table {
            width: 100%;
            border-collapse: collapse;
        }
        .two-col-table td { vertical-align: top; width: 50%; padding-right: 20px; }
        .two-col-table td:last-child { padding-right: 0; padding-left: 12px; }
        .col-divider { width: 1px; background: #e4e4e7; }

        /* ─── Data table (for funds / expenses / inspections) ───────────── */
        .pdf-data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            margin-bottom: 18px;
            font-size: 10.5px;
        }
        .pdf-data-table thead th {
            background-color: #10b981;
            color: #ffffff;
            padding: 8px 10px;
            text-align: left;
            font-weight: 700;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .pdf-data-table tbody tr:nth-child(even) td { background: #f9fafb; }
        .pdf-data-table tbody td {
            padding: 7.5px 10px;
            border-bottom: 1px solid #e4e4e7;
            color: #27272a;
            vertical-align: middle;
        }
        .pdf-data-table tfoot td {
            padding: 9px 10px;
            font-weight: 800;
            color: #059669;
            border-top: 2px solid #10b981;
            font-size: 11.5px;
        }

        /* ─── Status badge ───────────────────────────────────────────────── */
        .status-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .badge-running    { background: #dbeafe; color: #1d4ed8; }
        .badge-completed  { background: #dcfce7; color: #166534; }
        .badge-pending    { background: #fef9c3; color: #854d0e; }
        .badge-default    { background: #f1f5f9; color: #475569; }

        /* ─── Amount / Total highlight ───────────────────────────────────── */
        .amount-highlight {
            color: #059669;
            font-weight: 800;
        }

        /* ─── Signature block ─────────────────────────────────────────────── */
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 50px;
        }
        .sig-cell {
            width: 25%;
            text-align: center;
            padding: 0 8px;
            vertical-align: bottom;
        }
        .sig-line {
            border-top: 1px solid #a1a1aa;
            padding-top: 7px;
            margin-top: 48px;
            font-size: 9.5px;
            font-weight: 800;
            color: #3f3f46;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        /* ─── Footer disclaimer ──────────────────────────────────────────── */
        .footer-disclaimer {
            margin-top: 28px;
            font-size: 9px;
            color: #a1a1aa;
            line-height: 1.6;
            border-top: 1px solid #e4e4e7;
            padding-top: 10px;
        }
        .footer-disclaimer strong { color: #71717a; }

        /* ─── Print rules ─────────────────────────────────────────────────── */
        @media print {
            .pdf-actions-bar { display: none !important; }
            body { padding: 0; }
            .section-heading-table, .details-grid { page-break-inside: avoid; }
        }
    </style>
</head>
<body>

    {{-- Watermark --}}
    <div class="watermark-bg">
        <img src="{{ asset('images/logo_collapsed.png') }}" alt="Watermark">
    </div>

    {{-- TOP ACTION BAR (hidden on print) --}}
    <div class="pdf-actions-bar">
        <h3>Project Report &bull; {{ $projectObj->project_id ?? ($project->project_id ?? 'PDF Preview') }}</h3>
        <div>
            <button onclick="window.print()" class="btn-print">
                &#128438; &nbsp;Print / Download PDF
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- DOCUMENT HEADER                                                    --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <table class="doc-header-table">
        <tr>
            <td style="vertical-align: top;">
                <div class="doc-title">Project Report</div>
                <div class="doc-subtitle">{{ $targetProjectData['category_name'] ?? ($targetProjectData['name'] ?? 'Project Detailed Report') }} &bull; RCFI</div>
            </td>
            <td class="doc-meta-td">
                <img src="{{ asset('images/logo.png') }}" alt="RCFI Logo" class="doc-logo"><br>
                <div class="doc-meta-line">
                    <strong>Project ID:</strong> {{ $projectObj->project_id ?? ($project->project_id ?? '—') }}<br>
                    <strong>Generated:</strong> {{ date('d-M-Y H:i') }}<br>
                    <strong>Status:</strong>
                    @php
                        $st = $projectObj->status ?? ($project->status ?? 'Active');
                        $bClass = match(strtolower($st)) { 'running' => 'badge-running', 'completed' => 'badge-completed', 'pending' => 'badge-pending', default => 'badge-default' };
                    @endphp
                    <span class="status-badge {{ $bClass }}">{{ $st }}</span>
                </div>
            </td>
        </tr>
    </table>
    <hr class="header-rule">

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- SECTION 1: PROJECT DETAILS                                         --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <table class="section-heading-table">
        <tr>
            <td class="section-num-title">1. Project Details</td>
            <td class="section-rule-td"><div></div></td>
        </tr>
    </table>

    <table class="two-col-table">
        <tr>
            <td>
                <table class="details-grid">
                    <tr>
                        <td class="lbl">RCFI Project ID</td>
                        <td class="cln">:</td>
                        <td class="val">{{ $projectObj->project_id ?? ($project->project_id ?? '—') }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Project Name</td>
                        <td class="cln">:</td>
                        <td class="val">{{ $projectObj->project_name ?? ($project->project_name ?? '—') }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Agency</td>
                        <td class="cln">:</td>
                        <td class="val">{{ $projectObj->agency ?? ($project->agency ?? ($projectObj->sponsor ?? '—')) }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Agency Project No.</td>
                        <td class="cln">:</td>
                        <td class="val">{{ $projectObj->agency_project_no ?? ($project->agency_project_no ?? '—') }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Type of Project</td>
                        <td class="cln">:</td>
                        <td class="val">{{ $targetProjectData['category_name'] ?? ($targetProjectData['name'] ?? '—') }}</td>
                    </tr>
                </table>
            </td>
            <td>
                <table class="details-grid">
                    <tr>
                        <td class="lbl">Location / District</td>
                        <td class="cln">:</td>
                        <td class="val">{{ $projectObj->location ?? ($project->location ?? ($application->district ?? '—')) }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">State</td>
                        <td class="cln">:</td>
                        <td class="val">{{ $projectObj->state ?? ($project->state ?? ($application->state ?? 'Kerala')) }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Total Budget (INR)</td>
                        <td class="cln">:</td>
                        <td class="val amount-highlight">₹{{ number_format($totalAllocated ?? ($projectObj->budget ?? 0), 2) }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Total Project Cost</td>
                        <td class="cln">:</td>
                        <td class="val amount-highlight">₹{{ number_format($totalProjectCost ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Status</td>
                        <td class="cln">:</td>
                        <td class="val">{{ $projectObj->status ?? ($project->status ?? 'Active') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- SECTION 2: BENEFICIARY / APPLICATION DETAILS                       --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @if(isset($application) && $application)
    <table class="section-heading-table">
        <tr>
            <td class="section-num-title">2. Beneficiary / Application Details</td>
            <td class="section-rule-td"><div></div></td>
        </tr>
    </table>

    <table class="two-col-table">
        <tr>
            <td>
                <table class="details-grid">
                    <tr>
                        <td class="lbl">Applicant Name</td>
                        <td class="cln">:</td>
                        <td class="val">{{ $application->applicant_name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Application ID</td>
                        <td class="cln">:</td>
                        <td class="val">{{ $application->application_id ?? ('APLRCFI'.$application->id) }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Father / Guardian</td>
                        <td class="cln">:</td>
                        <td class="val">{{ $application->father_name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Mother Name</td>
                        <td class="cln">:</td>
                        <td class="val">{{ $application->mother_name ?? '—' }}</td>
                    </tr>
                </table>
            </td>
            <td>
                <table class="details-grid">
                    <tr>
                        <td class="lbl">Contact Mobile</td>
                        <td class="cln">:</td>
                        <td class="val">{{ $application->mobile_1 ?? ($application->mobile ?? '—') }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Place / Village</td>
                        <td class="cln">:</td>
                        <td class="val">{{ $application->place ?? ($application->village ?? '—') }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">District</td>
                        <td class="cln">:</td>
                        <td class="val">{{ $application->district ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">State</td>
                        <td class="cln">:</td>
                        <td class="val">{{ $application->state ?? '—' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Beneficiary Impact Row --}}
    @if(($projectObj->total_beneficiary_peoples ?? 0) || ($projectObj->total_family ?? 0))
    <table class="two-col-table" style="margin-top:4px;">
        <tr>
            <td>
                <table class="details-grid">
                    <tr>
                        <td class="lbl">Total Benefited Peoples</td>
                        <td class="cln">:</td>
                        <td class="val amount-highlight">{{ number_format($projectObj->total_beneficiary_peoples ?? 0) }}</td>
                    </tr>
                </table>
            </td>
            <td>
                <table class="details-grid">
                    <tr>
                        <td class="lbl">Total Benefited Families</td>
                        <td class="cln">:</td>
                        <td class="val amount-highlight">{{ number_format($projectObj->total_family ?? 0) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    @endif
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- SECTION 3: MANAGEMENT                                              --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    <table class="section-heading-table">
        <tr>
            <td class="section-num-title">3. Project Management</td>
            <td class="section-rule-td"><div></div></td>
        </tr>
    </table>

    <table class="two-col-table">
        <tr>
            <td>
                <table class="details-grid">
                    <tr>
                        <td class="lbl">Project Manager</td>
                        <td class="cln">:</td>
                        <td class="val">{{ $projectManager->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Engineer</td>
                        <td class="cln">:</td>
                        <td class="val">{{ $engineer->name ?? '—' }}</td>
                    </tr>
                </table>
            </td>
            <td>
                <table class="details-grid">
                    <tr>
                        <td class="lbl">Contractor</td>
                        <td class="cln">:</td>
                        <td class="val">{{ $contractor->name ?? ($contractor->company_name ?? '—') }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Start Date</td>
                        <td class="cln">:</td>
                        <td class="val">{{ isset($projectObj->start_date) ? date('d-M-Y', strtotime($projectObj->start_date)) : (isset($project->start_date) ? date('d-M-Y', strtotime($project->start_date)) : '—') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- SECTION 4: FUND ALLOCATIONS                                        --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @if(isset($funds) && $funds && (is_countable($funds) ? count($funds) : $funds->count()) > 0)
    <table class="section-heading-table">
        <tr>
            <td class="section-num-title">4. Fund Allocation Splits</td>
            <td class="section-rule-td"><div></div></td>
        </tr>
    </table>

    <table class="pdf-data-table">
        <thead>
            <tr>
                <th style="width:36px;">#</th>
                <th>Donor / Agency</th>
                <th>Reference / Cheque No.</th>
                <th>Bank Account</th>
                <th style="text-align:right;">Amount (INR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($funds as $i => $fund)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $fund->donor_name ?? ($fund->agency ?? '—') }}</td>
                <td>{{ $fund->cheque_no ?? ($fund->reference ?? '—') }}</td>
                <td>{{ $fund->bank_account ?? '—' }}</td>
                <td style="text-align:right; font-weight:700;">₹{{ number_format($fund->amount ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align:right; font-weight:800; text-transform:uppercase; letter-spacing:0.04em; font-size:10.5px;">Total Allocated Amount:</td>
                <td style="text-align:right;">₹{{ number_format($totalAllocated ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- SECTION 5: COMMUNITY CONTRIBUTIONS                                 --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @if(isset($communityContributions) && $communityContributions && (is_countable($communityContributions) ? count($communityContributions) : $communityContributions->count()) > 0)
    <table class="section-heading-table">
        <tr>
            <td class="section-num-title">5. Community Contributions</td>
            <td class="section-rule-td"><div></div></td>
        </tr>
    </table>

    <table class="pdf-data-table">
        <thead>
            <tr>
                <th style="width:36px;">#</th>
                <th>Contributor</th>
                <th>Type</th>
                <th style="text-align:right;">Value (INR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($communityContributions as $i => $contrib)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $contrib->contributor ?? ($contrib->description ?? '—') }}</td>
                <td>{{ $contrib->contribution_type ?? ($contrib->type ?? '—') }}</td>
                <td style="text-align:right; font-weight:700;">₹{{ number_format($contrib->amount ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align:right; font-weight:800; text-transform:uppercase; letter-spacing:0.04em; font-size:10.5px;">Total Community Contribution:</td>
                <td style="text-align:right;">₹{{ number_format($totalCommunityContrib ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- SECTION 6: INSPECTIONS                                             --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @if(isset($inspections) && $inspections && (is_countable($inspections) ? count($inspections) : $inspections->count()) > 0)
    <table class="section-heading-table">
        <tr>
            <td class="section-num-title">6. Site Inspections</td>
            <td class="section-rule-td"><div></div></td>
        </tr>
    </table>

    <table class="pdf-data-table">
        <thead>
            <tr>
                <th style="width:36px;">#</th>
                <th>Date</th>
                <th>Inspector</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inspections as $i => $insp)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ isset($insp->inspection_date) ? date('d-M-Y', strtotime($insp->inspection_date)) : '—' }}</td>
                <td>{{ $insp->inspector_name ?? ($insp->inspector ?? '—') }}</td>
                <td>{{ $insp->remarks ?? ($insp->notes ?? '—') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif



    {{-- Auto print --}}
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() { window.print(); }, 350);
        });
    </script>
</body>
</html>
