<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Report - {{ $projectObj->project_id ?? ($project->project_id ?? 'PDF') }}</title>
    <style>
        /* PDF & Print Styles */
        @page {
            size: A4;
            margin: 15mm;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            background-color: #ffffff;
            margin: 0;
            padding: 20px;
            font-size: 13px;
            line-height: 1.5;
        }

        /* Top Action Bar (Hidden when printing/generating PDF) */
        .pdf-actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #0f172a;
            color: #ffffff;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .pdf-actions-bar h3 {
            margin: 0;
            font-size: 15px;
            font-weight: 600;
            color: #38bdf8;
        }

        .btn-print {
            background: #10b981;
            color: #ffffff;
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s;
        }

        .btn-print:hover {
            background: #059669;
        }

        /* Document Header */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-bottom: 2px solid #10b981;
            padding-bottom: 15px;
        }

        .organization-title {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .report-subtitle {
            font-size: 13px;
            color: #64748b;
            margin-top: 4px;
            font-weight: 500;
        }

        .meta-box {
            text-align: right;
            font-size: 12px;
            color: #475569;
        }

        .meta-box strong {
            color: #0f172a;
        }

        /* Section Cards */
        .section-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .section-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 10px 15px;
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
        }

        .section-body {
            padding: 15px;
        }

        /* Data Grid Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th, 
        .data-table td {
            padding: 8px 12px;
            text-align: left;
            vertical-align: top;
            border-bottom: 1px solid #f1f5f9;
        }

        .data-table th {
            width: 25%;
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .data-table td {
            color: #0f172a;
            font-weight: 500;
        }

        /* Signatures Section */
        .signatures-table {
            width: 100%;
            margin-top: 40px;
            border-collapse: collapse;
        }

        .signature-cell {
            width: 33.33%;
            text-align: center;
            padding: 10px;
        }

        .signature-line {
            border-top: 1px dashed #cbd5e1;
            margin-top: 50px;
            padding-top: 8px;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
        }

        /* Print Media Query */
        @media print {
            .pdf-actions-bar {
                display: none !important;
            }
            body {
                padding: 0;
            }
            .section-card {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <!-- WATERMARK LOGO -->
    <div class="watermark-bg" style="position: fixed; top: 45%; left: 50%; transform: translate(-50%, -50%); opacity: 0.07; width: 580px; max-width: 90%; pointer-events: none; z-index: -1; text-align: center;">
        <img src="{{ asset('images/logo_collapsed.png') }}" alt="Watermark" style="width: 100%; height: auto;">
    </div>

    <!-- TOP ACTION BAR (Hidden when printing/saving to PDF) -->
    <div class="pdf-actions-bar">
        <h3>PDF Report Preview &bull; {{ $projectObj->project_id ?? ($project->project_id ?? 'Project Report') }}</h3>
        <div>
            <button onclick="window.print()" class="btn-print">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M2.5 8a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
                    <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm1 4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-1z"/>
                </svg>
                Print / Download PDF
            </button>
        </div>
    </div>

    <!-- DOCUMENT HEADER -->
    <table class="header-table">
        <tr>
            <td>
                <h1 class="organization-title">RCFI - Relief & Charitable Foundation of India</h1>
                <div class="report-subtitle">
                    Project Detailed Report &bull; {{ $targetProjectData['category_name'] ?? ($targetProjectData['name'] ?? 'Project Report') }}
                </div>
            </td>
            <td class="meta-box">
                <div><strong>RCFI ID:</strong> {{ $projectObj->project_id ?? ($project->project_id ?? 'N/A') }}</div>
                <div><strong>Generated Date:</strong> {{ date('d M Y') }}</div>
                <div><strong>Status:</strong> {{ $projectObj->status ?? ($project->status ?? 'Active') }}</div>
            </td>
        </tr>
    </table>

    <!-- ========================================================================== -->
    <!-- EDITABLE PDF TEMPLATE BLADE FILE                                           -->
    <!-- Location: resources/views/pdf/project_pdf.blade.php                        -->
    <!-- Add or modify your custom HTML & Blade code below                         -->
    <!-- ========================================================================== -->

    <!-- SECTION 1: PROJECT OVERVIEW -->
    <div class="section-card">
        <div class="section-header">
            1. Project Overview
        </div>
        <div class="section-body">
            <table class="data-table">
                <tr>
                    <th>RCFI ID</th>
                    <td>{{ $projectObj->project_id ?? ($project->project_id ?? 'N/A') }}</td>
                    <th>Project Name</th>
                    <td>{{ $projectObj->project_name ?? ($project->project_name ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <th>Agency</th>
                    <td>{{ $projectObj->agency ?? ($project->agency ?? ($projectObj->sponsor ?? 'N/A')) }}</td>
                    <th>Agency Project No</th>
                    <td>{{ $projectObj->agency_project_no ?? ($project->agency_project_no ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <th>Location / District</th>
                    <td>{{ $projectObj->location ?? ($project->location ?? ($application->district ?? 'N/A')) }}</td>
                    <th>State</th>
                    <td>{{ $projectObj->state ?? ($project->state ?? ($application->state ?? 'Kerala')) }}</td>
                </tr>
                <tr>
                    <th>Project Status</th>
                    <td>{{ $projectObj->status ?? ($project->status ?? 'Active') }}</td>
                    <th>Total Budget</th>
                    <td>₹{{ number_format($totalAllocated ?? ($projectObj->budget ?? 0), 2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- SECTION 2: BENEFICIARY DETAILS (IF APPLICATION ATTACHED) -->
    @if(isset($application) && $application)
    <div class="section-card">
        <div class="section-header">
            2. Beneficiary / Application Details
        </div>
        <div class="section-body">
            <table class="data-table">
                <tr>
                    <th>Applicant Name</th>
                    <td>{{ $application->applicant_name ?? 'N/A' }}</td>
                    <th>Application ID</th>
                    <td>{{ $application->application_id ?? ('APLRCFI' . $application->id) }}</td>
                </tr>
                <tr>
                    <th>Father / Guardian Name</th>
                    <td>{{ $application->father_name ?? 'N/A' }}</td>
                    <th>Mother Name</th>
                    <td>{{ $application->mother_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Contact Mobile</th>
                    <td>{{ $application->mobile_1 ?? ($application->mobile ?? 'N/A') }}</td>
                    <th>Place / Village</th>
                    <td>{{ $application->place ?? ($application->village ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <th>District</th>
                    <td>{{ $application->district ?? 'N/A' }}</td>
                    <th>State</th>
                    <td>{{ $application->state ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>
    </div>
    @endif

    <!-- SECTION 3: MANAGEMENT & FINANCIAL SUMMARY -->
    <div class="section-card">
        <div class="section-header">
            3. Project Management & Financial Summary
        </div>
        <div class="section-body">
            <table class="data-table">
                <tr>
                    <th>Project Manager</th>
                    <td>{{ $projectManager->name ?? 'N/A' }}</td>
                    <th>Engineer</th>
                    <td>{{ $engineer->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Contractor</th>
                    <td>{{ $contractor->name ?? ($contractor->company_name ?? 'N/A') }}</td>
                    <th>Total Project Cost</th>
                    <td>₹{{ number_format($totalProjectCost ?? 0, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- CUSTOM CONTENT PLACEHOLDER -->
    <!-- You can add more HTML sections, tables, images, or formatting here -->


    <!-- SIGNATURES SECTION -->
    <table class="signatures-table">
        <tr>
            <td class="signature-cell">
                <div class="signature-line">Prepared By</div>
            </td>
            <td class="signature-cell">
                <div class="signature-line">Project Manager</div>
            </td>
            <td class="signature-cell">
                <div class="signature-line">Authorized Signatory</div>
            </td>
        </tr>
    </table>

    <!-- Auto Print / Save PDF Script -->
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 300);
        });
    </script>
</body>
</html>
