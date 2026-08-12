<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Aid Report - {{ $projectObj->project_id ?? ($project->project_id ?? 'PDF') }}</title>
    <style>
        @page {
            size: A4;
            margin: 12mm 15mm 12mm 15mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
            color: #18181b;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            font-size: 12.5px;
            line-height: 1.6;
        }

        /* Top Action Bar (Hidden in Print) */
        .pdf-actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #0f172a;
            color: #ffffff;
            padding: 10px 18px;
            border-radius: 6px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .pdf-actions-bar h3 {
            margin: 0;
            font-size: 13px;
            font-weight: 600;
            color: #38bdf8;
        }

        .btn-print {
            background: #00b074;
            color: #ffffff;
            border: none;
            padding: 7px 16px;
            border-radius: 5px;
            font-weight: 600;
            font-size: 12px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        /* Watermark Background */
        .watermark-bg {
            position: fixed;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.07;
            width: 580px;
            max-width: 90%;
            pointer-events: none;
            z-index: -1;
            text-align: center;
        }

        .watermark-bg img {
            width: 100%;
            height: auto;
        }

        /* Header Layout Grid */
        .header-layout-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .photo-td {
            width: 140px;
            vertical-align: top;
        }

        .photo-card-box {
            width: 130px;
            height: 155px;
            border-radius: 18px;
            border: 1px solid #d4d4d8;
            padding: 4px;
            background: #ffffff;
            overflow: hidden;
        }

        .photo-card-box img {
            width: 100%;
            height: 100%;
            border-radius: 14px;
            object-fit: cover;
        }

        .photo-placeholder {
            width: 100%;
            height: 100%;
            border-radius: 14px;
            background: #f4f4f5;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #a1a1aa;
        }

        .info-middle-td {
            vertical-align: top;
            padding-left: 20px;
            padding-right: 18px;
        }

        .divider-td {
            width: 1px;
            background-color: #27272a;
            vertical-align: top;
        }

        .info-right-td {
            width: 38%;
            vertical-align: top;
            padding-left: 20px;
        }

        /* Mini Info Table for Header */
        .mini-info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .mini-info-table td {
            padding: 4px 0;
            vertical-align: top;
            font-size: 12px;
            text-transform: uppercase;
        }

        .mini-info-table td.lbl {
            font-weight: 700;
            color: #27272a;
            white-space: nowrap;
        }

        .mini-info-table td.cln {
            width: 14px;
            text-align: center;
            font-weight: 700;
            color: #27272a;
        }

        .mini-info-table td.val {
            font-weight: 700;
            color: #09090b;
            word-break: break-word;
        }

        /* Section Headings with Full Horizontal Rule */
        .section-header-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 24px;
            margin-bottom: 12px;
            page-break-after: avoid;
        }

        .section-title-td {
            white-space: nowrap;
            font-size: 13.5px;
            font-weight: 700;
            color: #18181b;
            padding-right: 12px;
        }

        .section-line-td {
            width: 100%;
            vertical-align: middle;
        }

        .section-line-td div {
            border-bottom: 1px solid #52525b;
            width: 100%;
        }

        /* Details Grid Table (Aligned Labels & Colons) */
        .details-grid-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .details-grid-table td {
            padding: 5.5px 0;
            vertical-align: top;
            font-size: 12px;
            text-transform: uppercase;
        }

        .details-grid-table td.lbl {
            width: 205px;
            font-weight: 700;
            color: #27272a;
            white-space: nowrap;
        }

        .details-grid-table td.cln {
            width: 16px;
            text-align: center;
            font-weight: 700;
            color: #27272a;
        }

        .details-grid-table td.val {
            font-weight: 700;
            color: #09090b;
            word-break: break-word;
        }

        /* Tables Styling for Page 2 */
        .pdf-data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 20px;
            font-size: 10.5px;
        }

        .pdf-data-table thead th {
            background-color: #00b074 !important;
            color: #ffffff !important;
            padding: 8.5px 10px;
            text-align: left;
            font-weight: 700;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            border: none;
        }

        .pdf-data-table tbody td {
            padding: 8.5px 10px;
            border-bottom: 1px solid #e4e4e7;
            color: #27272a;
            vertical-align: middle;
        }

        .pdf-data-table tfoot td {
            padding: 9px 10px;
            font-weight: 700;
            color: #00b074;
            border-top: 1.5px solid #00b074;
            font-size: 11px;
        }

        /* Document & Checklist Badge Grid UI */
        .doc-checklist-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 4px 6px;
            align-items: center;
        }

        .doc-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 9.5px;
            font-weight: 600;
            line-height: 1.2;
            white-space: nowrap;
        }

        .doc-badge.is-checked {
            background-color: #ecfdf5;
            color: #047857;
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
            @page {
                margin: 10mm 12mm;
            }
        }
    </style>
</head>
<body>

    <!-- WATERMARK LOGO -->
    <div class="watermark-bg">
        <img src="{{ asset('images/logo_collapsed.png') }}" alt="Watermark">
    </div>

    <!-- TOP ACTION BAR -->
    <div class="pdf-actions-bar">
        <h3>Social Aid Project Report &bull; {{ $projectObj->project_id ?? ($project->project_id ?? 'Report') }}</h3>
        <button onclick="window.print()" class="btn-print">
            &#128438; &nbsp;Print / Save PDF
        </button>
    </div>

    <!-- DOCUMENT HEADER (Voucher-style) -->
    <table style="width:100%; border-collapse:collapse; margin-bottom:4px;">
        <tr>
            <td style="vertical-align:top;">
                <div style="font-size:22px; font-weight:900; color:#09090b; margin:0; text-transform:uppercase; letter-spacing:-0.3px;">Social Aid Report</div>
                <div style="font-size:10.5px; color:#71717a; font-weight:600; letter-spacing:0.06em; text-transform:uppercase; margin-top:3px;">
                    @php
                        $docCategoryName = '';
                        if(isset($projectObj->type_of_project)) $docCategoryName = $projectObj->type_of_project;
                        elseif(isset($project->type_of_project)) $docCategoryName = $project->type_of_project;
                    @endphp
                    {{ $docCategoryName ?: 'Orphan Care / Differently Abled / Family Aid' }} &bull; RCFI
                </div>
            </td>
            <td style="text-align:right; vertical-align:top;">
                <img src="{{ asset('images/logo.png') }}" alt="RCFI Logo" style="height:42px; width:auto; object-fit:contain; margin-bottom:5px;"><br>
                <div style="font-size:11.5px; color:#3f3f46; line-height:1.9; text-align:right;">
                    <strong style="color:#09090b;">Project ID:</strong> {{ $projectObj->project_id ?? ($project->project_id ?? '—') }}<br>
                    <strong style="color:#09090b;">Generated:</strong> {{ date('d-M-Y H:i') }}
                </div>
            </td>
        </tr>
    </table>
    <hr style="border:none; border-top:1.5px solid #d4d4d8; margin:12px 0 18px;">


    @php
        $app = $application ?? ($projectObj->application ?? null);
        $meta = $app->meta ?? [];
        
        $beneficiaryName = strtoupper($projectObj->project_name 
            ?? ($app?->applicant_name 
            ?? ($meta['applicant_name'] ?? ($meta['name_of_orphan'] ?? 'N/A'))));

        $photoSrc = null;
        if ($app) {
            if ($app->student_photo) {
                $photoSrc = asset('storage/' . $app->student_photo);
            } elseif (isset($meta['student_photo']) && $meta['student_photo']) {
                $photoSrc = asset('storage/' . $meta['student_photo']);
            } elseif ($app->photo) {
                $photoSrc = asset('storage/' . $app->photo);
            } elseif (isset($meta['photo']) && $meta['photo']) {
                $photoSrc = asset('storage/' . $meta['photo']);
            }
        }
        if (!$photoSrc && isset($projectObj->photo) && $projectObj->photo) {
            $photoSrc = asset('storage/' . $projectObj->photo);
        }

        $dob = $app?->dob ?? ($meta['dob'] ?? ($meta['date_of_birth'] ?? 'N/A'));
        $age = $app?->age ?? ($meta['age'] ?? 'N/A');
        $place = strtoupper($app?->place ?? ($meta['place'] ?? 'N/A'));
        $contactNo = $app?->mobile_1 ?? ($meta['mobile_1'] ?? ($meta['contact_number_1'] ?? ($app?->mobile_2 ?? ($meta['mobile_2'] ?? 'N/A'))));

        $projectId = $projectObj->project_id ?? ($project->project_id ?? 'N/A');
        $agencyName = strtoupper($app?->agency_name ?? ($meta['agency_name'] ?? ($projectObj->agency ?? ($projectObj->sponsor ?? 'N/A'))));
        $agencyId = $projectObj->agency_project_no ?? ($app?->agency_number ?? ($meta['agency_number'] ?? 'N/A'));
        $clusterCode = strtoupper($app?->cluster?->code ?? ($app?->cluster?->name ?? ($meta['cluster'] ?? 'N/A')));
        
        $sponsoredDate = 'N/A';
        if (isset($projectObj->created_at)) {
            $sponsoredDate = $projectObj->created_at->format('d/m/Y');
        } elseif ($app && isset($app->created_at)) {
            $sponsoredDate = $app->created_at->format('d/m/Y');
        }
    @endphp

    <!-- HEADER BLOCK GRID -->
    <table class="header-layout-table">
        <tr>
            <!-- PHOTO BOX -->
            <td class="photo-td">
                <div class="photo-card-box">
                    @if($photoSrc)
                        <img src="{{ $photoSrc }}" alt="Photo" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="photo-placeholder" style="display: none;">
                            <svg width="42" height="42" fill="#a1a1aa" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        </div>
                    @else
                        <div class="photo-placeholder">
                            <svg width="42" height="42" fill="#a1a1aa" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        </div>
                    @endif
                </div>
            </td>

            <!-- BENEFICIARY INFO MIDDLE -->
            <td class="info-middle-td">
                <table class="mini-info-table">
                    <tr><td class="lbl" style="width: 105px;">NAME</td><td class="cln">:</td><td class="val">{{ $beneficiaryName }}</td></tr>
                    <tr><td class="lbl">DOB</td><td class="cln">:</td><td class="val">{{ $dob }}</td></tr>
                    <tr><td class="lbl">AGE</td><td class="cln">:</td><td class="val">{{ $age }}</td></tr>
                    <tr><td class="lbl">PLACE</td><td class="cln">:</td><td class="val">{{ $place }}</td></tr>
                    <tr><td class="lbl">CONTACT NO</td><td class="cln">:</td><td class="val">{{ $contactNo }}</td></tr>
                </table>
            </td>

            <!-- VERTICAL DIVIDER -->
            <td class="divider-td"></td>

            <!-- PROJECT INFO RIGHT -->
            <td class="info-right-td">
                <table class="mini-info-table">
                    <tr><td class="lbl" style="width: 125px;">PROJECT ID</td><td class="cln">:</td><td class="val">{{ $projectId }}</td></tr>
                    <tr><td class="lbl">AGENCY</td><td class="cln">:</td><td class="val">{{ $agencyName }}</td></tr>
                    <tr><td class="lbl">AGENCY ID</td><td class="cln">:</td><td class="val">{{ $agencyId }}</td></tr>
                    <tr><td class="lbl">CLUSTER</td><td class="cln">:</td><td class="val">{{ $clusterCode }}</td></tr>
                    <tr><td class="lbl">SPONSERED DATE</td><td class="cln">:</td><td class="val">{{ $sponsoredDate }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- SECTION 1: FAMILY DETAILS -->
    <table class="section-header-table">
        <tr>
            <td class="section-title-td">Family Detals</td>
            <td class="section-line-td"><div></div></td>
        </tr>
    </table>
    <table class="details-grid-table">
        <tr>
            <td class="lbl">FATHER NAME</td>
            <td class="cln">:</td>
            <td class="val">{{ strtoupper($app?->father_name ?? ($meta['father_name'] ?? 'N/A')) }}</td>
        </tr>
        <tr>
            <td class="lbl">GRAND FATHER</td>
            <td class="cln">:</td>
            <td class="val">{{ strtoupper($app?->grand_father_name ?? ($meta['grand_father_name'] ?? ($meta['grandfather_name'] ?? 'N/A'))) }}</td>
        </tr>
        <tr>
            <td class="lbl">MOTHER</td>
            <td class="cln">:</td>
            <td class="val">{{ strtoupper($app?->mother_name ?? ($meta['mother_name'] ?? 'N/A')) }}</td>
        </tr>
        <tr>
            <td class="lbl">MOTHER'S FATHER</td>
            <td class="cln">:</td>
            <td class="val">{{ strtoupper($app?->mother_father_name ?? ($meta['mother_father_name'] ?? ($meta['mothers_father'] ?? 'N/A'))) }}</td>
        </tr>
        @php
            $guardianName = strtoupper($app?->guardian_name ?? ($meta['guardian_name'] ?? 'N/A'));
            $guardianRelation = strtoupper($app?->guardian_relation ?? ($meta['guardian_relation'] ?? 'FATHER'));
        @endphp
        <tr>
            <td class="lbl">GUARDIAN</td>
            <td class="cln">:</td>
            <td class="val">{{ $guardianName }} @if($guardianRelation) ( {{ $guardianRelation }} ) @endif</td>
        </tr>
        @php
            $brothers = $meta['brothers_count'] ?? ($meta['brothers'] ?? 0);
            $sisters = $meta['sisters_count'] ?? ($meta['sisters'] ?? 0);
            $totalSiblings = $app?->family_members ?? ($meta['family_members'] ?? ($brothers + $sisters));
        @endphp
        <tr>
            <td class="lbl">SIBILINGS</td>
            <td class="cln">:</td>
            <td class="val">{{ $totalSiblings }} ( BROTHOR :{{ $brothers }} , SISTERS :{{ $sisters }} )</td>
        </tr>
        @php
            $phone1 = $app?->mobile_1 ?? ($meta['mobile_1'] ?? ($meta['contact_number_1'] ?? 'N/A'));
            $phone2 = $app?->mobile_2 ?? ($meta['mobile_2'] ?? ($meta['contact_number_2'] ?? $phone1));
            $whatsapp = $meta['whatsapp'] ?? ($meta['whatsapp_number'] ?? $phone1);
        @endphp
        <tr>
            <td class="lbl">PHONE 1</td>
            <td class="cln">:</td>
            <td class="val">{{ $phone1 }} , PHONE 2 : {{ $phone2 }}</td>
        </tr>
        <tr>
            <td class="lbl">WHATSAPP</td>
            <td class="cln">:</td>
            <td class="val">{{ $whatsapp }}</td>
        </tr>
    </table>

    <!-- SECTION 2: ADDRESS DETAILS -->
    <table class="section-header-table">
        <tr>
            <td class="section-title-td">Address Detals</td>
            <td class="section-line-td"><div></div></td>
        </tr>
    </table>
    <table class="details-grid-table">
        <tr>
            <td class="lbl">HOUSE NAME</td>
            <td class="cln">:</td>
            <td class="val">{{ strtoupper($app?->house_name ?? ($meta['house_name'] ?? 'N/A')) }}</td>
        </tr>
        <tr>
            <td class="lbl">PLACE</td>
            <td class="cln">:</td>
            <td class="val">{{ strtoupper($app?->place ?? ($meta['place'] ?? 'N/A')) }}</td>
        </tr>
        <tr>
            <td class="lbl">POST</td>
            <td class="cln">:</td>
            <td class="val">{{ strtoupper($app?->post_office ?? ($meta['post_office'] ?? ($meta['po'] ?? 'N/A'))) }} P/O</td>
        </tr>
        <tr>
            <td class="lbl">PANJAYATH</td>
            <td class="cln">:</td>
            <td class="val">{{ strtoupper($app?->panchayat ?? ($meta['panchayat'] ?? ($meta['panjayath'] ?? 'N/A'))) }}</td>
        </tr>
        <tr>
            <td class="lbl">VILLAGE</td>
            <td class="cln">:</td>
            <td class="val">{{ strtoupper($app?->village ?? ($meta['village'] ?? 'N/A')) }}</td>
        </tr>
        <tr>
            <td class="lbl">DISTRICT</td>
            <td class="cln">:</td>
            <td class="val">{{ strtoupper($app?->district ?? ($meta['district'] ?? ($meta['dist'] ?? 'MALAPPURAM'))) }}</td>
        </tr>
        <tr>
            <td class="lbl">STATE</td>
            <td class="cln">:</td>
            <td class="val">{{ strtoupper($app?->state ?? ($meta['state'] ?? 'KERALA')) }}</td>
        </tr>
        <tr>
            <td class="lbl">PINCODE</td>
            <td class="cln">:</td>
            <td class="val">{{ $app?->pin_code ?? ($meta['pin_code'] ?? ($meta['pincode'] ?? '673641')) }}</td>
        </tr>
    </table>

    <!-- SECTION 3: EDUCATION DETAILS -->
    <table class="section-header-table">
        <tr>
            <td class="section-title-td">Education Detals</td>
            <td class="section-line-td"><div></div></td>
        </tr>
    </table>
    <table class="details-grid-table">
        <tr>
            <td class="lbl">SCHOOL</td>
            <td class="cln">:</td>
            <td class="val">{{ strtoupper($app?->school_name ?? ($meta['school_name'] ?? ($meta['school'] ?? ($app?->school_class ?? ($meta['school_class'] ?? 'N/A'))))) }}</td>
        </tr>
        <tr>
            <td class="lbl">MADRASSA</td>
            <td class="cln">:</td>
            <td class="val">{{ strtoupper($app?->madrassa_name ?? ($meta['madrassa_name'] ?? ($meta['madrassa'] ?? ($app?->madrassa_class ?? ($meta['madrassa_class'] ?? 'N/A'))))) }}</td>
        </tr>
        <tr>
            <td class="lbl">iF NOT STUDYING</td>
            <td class="cln">:</td>
            <td class="val">{{ strtoupper($app?->not_studying_reason ?? ($meta['not_studying_reason'] ?? '')) }}</td>
        </tr>
        <tr>
            <td class="lbl">HEALTH STATUS</td>
            <td class="cln">:</td>
            <td class="val">{{ strtoupper($app?->health_status ?? ($meta['health_status'] ?? 'OK')) }}</td>
        </tr>
        @php
            $income = $meta['monthly_income'] ?? ($meta['income'] ?? 1000);
            $expense = $meta['monthly_expense'] ?? ($meta['expense'] ?? 1000);
        @endphp
        <tr>
            <td class="lbl">MONTHLY INCOME</td>
            <td class="cln">:</td>
            <td class="val">{{ $income }} ( EXPENSE : {{ $expense }} )</td>
        </tr>
        <tr>
            <td class="lbl">HOUSE TYPE</td>
            <td class="cln">:</td>
            <td class="val">{{ strtoupper($app?->house_type ?? ($meta['house_type'] ?? 'OWN HOUSE')) }}</td>
        </tr>
    </table>


    <!-- PAGE BREAK FOR PAGE 2 -->
    <div class="page-break"></div>

    <!-- SECTION 4: FINANCIAL DETAILS (PAGE 2) -->
    <table class="section-header-table" style="margin-top: 10px;">
        <tr>
            <td class="section-title-td">Financial Detals</td>
            <td class="section-line-td"><div></div></td>
        </tr>
    </table>

    <table class="pdf-data-table">
        <thead>
            <tr>
                <th style="width: 8%;">SERIAL NO</th>
                <th style="width: 22%;">DATE OF FUND TRANSFERRED</th>
                <th style="width: 14%;">AMOUNT</th>
                <th style="width: 18%;">AGENCY</th>
                <th style="width: 14%;">ACCOUNT NAME</th>
                <th style="width: 14%;">ACCOUNT NUMBER</th>
                <th style="width: 10%;">IFSC NUMBER</th>
            </tr>
        </thead>
        <tbody>
            @php $totalFundAmount = 0; @endphp
            @forelse($funds as $idx => $fund)
                @php
                    $fAmt = (float)($fund->amount ?? 0);
                    $totalFundAmount += $fAmt;
                    $fDate = '—';
                    if (isset($fund->date)) {
                        $fDate = \Carbon\Carbon::parse($fund->date)->format('d-M-Y');
                    } elseif (isset($fund->created_at)) {
                        $fDate = $fund->created_at->format('d-M-Y');
                    }
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td>{{ $fDate }}</td>
                    <td style="color: #00b074; font-weight: 700;">₹{{ number_format($fAmt, 2) }}</td>
                    <td>{{ $fund->agency ?? ($fund->donor ?? ($agencyName !== 'N/A' ? $agencyName : '—')) }}</td>
                    <td>{{ $fund->account_name ?? '—' }}</td>
                    <td>{{ $fund->account_number ?? '—' }}</td>
                    <td>{{ $fund->ifsc_code ?? ($fund->ifsc ?? '—') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #71717a; padding: 12px;">No financial records transferred yet.</td>
                </tr>
            @endforelse
        </tbody>
        @if($funds->count() > 0)
        <tfoot>
            <tr>
                <td colspan="2" style="font-weight: 700; color: #18181b;">Total</td>
                <td style="color: #00b074; font-weight: 700; font-size: 10px;">₹{{ number_format($totalFundAmount, 2) }}</td>
                <td colspan="4"></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- SECTION 5: REPORT DETAILS (PAGE 2) -->
    <table class="section-header-table" style="margin-top: 25px;">
        <tr>
            <td class="section-title-td">Report Detals</td>
            <td class="section-line-td"><div></div></td>
        </tr>
    </table>

    <table class="pdf-data-table">
        <thead>
            <tr>
                <th style="width: 6%;">SERIAL NO</th>
                <th style="width: 20%;">PROGRAMME NAME</th>
                <th style="width: 11%;">DATE</th>
                <th style="width: 10%;">PLACE</th>
                <th style="width: 13%;">REMARKS</th>
                <th style="width: 40%;">CHECKLIST & DOCUMENTS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($programmes as $pidx => $prog)
                @php
                    $pDate = '—';
                    if (isset($prog->date)) {
                        $pDate = \Carbon\Carbon::parse($prog->date)->format('d-M-Y');
                    } elseif (isset($prog->created_at)) {
                        $pDate = $prog->created_at->format('d-M-Y');
                    }
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $pidx + 1 }}</td>
                    <td style="font-weight: 700; color: #18181b;">{{ $prog->name ?? ($prog->programme_name ?? 'Report Collection Programme') }}</td>
                    <td>{{ $pDate }}</td>
                    <td>{{ $prog->place ?: '-' }}</td>
                    <td>{{ $prog->remarks ?: '-' }}</td>
                    <td>
                        <div class="doc-checklist-grid">
                            <span class="doc-badge {{ $prog->present_ticked ? 'is-checked' : 'is-unchecked' }}">
                                <span class="doc-icon">{{ $prog->present_ticked ? '✓' : '✕' }}</span> Present
                            </span>
                            <span class="doc-badge {{ $prog->photo_ticked ? 'is-checked' : 'is-unchecked' }}">
                                <span class="doc-icon">{{ $prog->photo_ticked ? '✓' : '✕' }}</span> Photo
                            </span>
                            <span class="doc-badge {{ $prog->marklist_ticked ? 'is-checked' : 'is-unchecked' }}">
                                <span class="doc-icon">{{ $prog->marklist_ticked ? '✓' : '✕' }}</span> Marklist
                            </span>
                            <span class="doc-badge {{ $prog->thanks_letter_ticked ? 'is-checked' : 'is-unchecked' }}">
                                <span class="doc-icon">{{ $prog->thanks_letter_ticked ? '✓' : '✕' }}</span> Thanks Letter
                            </span>
                            <span class="doc-badge {{ $prog->report_form_ticked ? 'is-checked' : 'is-unchecked' }}">
                                <span class="doc-icon">{{ $prog->report_form_ticked ? '✓' : '✕' }}</span> Report Form
                            </span>
                            <span class="doc-badge {{ ($prog->medical_certificate_ticked || $prog->other_document_ticked) ? 'is-checked' : 'is-unchecked' }}">
                                <span class="doc-icon">{{ ($prog->medical_certificate_ticked || $prog->other_document_ticked) ? '✓' : '✕' }}</span> Other Doc
                            </span>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #71717a; padding: 12px;">No programme reports added yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 400);
        };
    </script>
</body>
</html>
