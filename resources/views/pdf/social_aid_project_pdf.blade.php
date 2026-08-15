<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Aid Report - {{ $project->project_id ?? 'PDF' }}</title>
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
            font-size: 11.5px;
            line-height: 1.45;
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
            gap: 6px;
            transition: background 0.2s;
        }
        .btn-print:hover {
            background: #059669;
        }

        /* Watermark Background */
        .watermark-bg {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.035;
            width: 480px;
            max-width: 85%;
            pointer-events: none;
            z-index: -1;
            text-align: center;
        }

        .watermark-bg img {
            width: 100%;
            height: auto;
        }

        /* Header Top Branding */
        .doc-header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .doc-title {
            font-size: 20px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.3px;
            text-transform: uppercase;
            margin: 0;
            line-height: 1.1;
        }

        .doc-subtitle {
            font-size: 10px;
            color: #00b074;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-top: 4px;
        }

        .doc-meta-box {
            text-align: right;
            font-size: 11px;
            color: #475569;
            line-height: 1.6;
        }

        .doc-meta-box strong {
            color: #0f172a;
            font-weight: 700;
        }

        /* Profile Summary Card */
        .profile-card {
            width: 100%;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #f8fafc;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 12px;
            overflow: hidden;
        }

        .photo-cell {
            width: 120px;
            padding: 8px;
            vertical-align: middle;
            text-align: center;
            border-right: 1px solid #e2e8f0;
            background: #ffffff;
        }

        .photo-box {
            width: 96px;
            height: 116px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            padding: 2px;
            background: #ffffff;
            margin: 0 auto;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .photo-box img {
            width: 100%;
            height: 100%;
            border-radius: 4px;
            object-fit: cover;
        }

        .photo-placeholder {
            width: 100%;
            height: 100%;
            border-radius: 4px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
        }

        .beneficiary-cell {
            padding: 8px 12px;
            vertical-align: top;
            width: 44%;
            border-right: 1px solid #e2e8f0;
        }

        .project-cell {
            padding: 8px 12px;
            vertical-align: top;
            width: 44%;
        }

        /* Two Column Field Table inside profile card */
        .card-table {
            width: 100%;
            border-collapse: collapse;
        }

        .card-table td {
            padding: 2.5px 0;
            vertical-align: top;
            font-size: 10.5px;
        }

        .card-table td.lbl {
            width: 82px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            font-size: 9.5px;
            letter-spacing: 0.03em;
        }

        .card-table td.cln {
            width: 10px;
            text-align: center;
            font-weight: 700;
            color: #94a3b8;
        }

        .card-table td.val {
            font-weight: 700;
            color: #0f172a;
            word-break: break-word;
        }

        /* Modern Section Headings */
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
            border-left: 3px solid #00b074;
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
            margin-bottom: 4px;
        }

        .grid-2col-table > tbody > tr > td {
            width: 50%;
            vertical-align: top;
        }

        .grid-2col-table > tbody > tr > td:first-child {
            padding-right: 10px;
        }

        .grid-2col-table > tbody > tr > td:last-child {
            padding-left: 10px;
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
            width: 110px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            font-size: 9.5px;
            letter-spacing: 0.02em;
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

        /* Page 2 Data Tables */
        .pdf-data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            margin-bottom: 16px;
            font-size: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
        }

        .pdf-data-table thead th {
            background-color: #00b074 !important;
            color: #ffffff !important;
            padding: 7px 9px;
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
            padding: 6.5px 9px;
            border-bottom: 1px solid #e2e8f0;
            color: #1e293b;
            vertical-align: middle;
        }

        .pdf-data-table tfoot td {
            background-color: #f1f5f9;
            padding: 7px 9px;
            border-top: 1.5px solid #cbd5e1;
        }

        /* Document Badges Grid */
        .doc-checklist-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 3px 5px;
            align-items: center;
        }

        .doc-badge {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            padding: 2px 5px;
            border-radius: 4px;
            font-size: 8.5px;
            font-weight: 600;
            line-height: 1.2;
            white-space: nowrap;
        }

        .doc-badge.is-checked {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .doc-badge.is-checked .doc-icon {
            font-weight: 800;
            color: #059669;
            font-size: 9px;
        }

        .doc-badge.is-unchecked {
            background-color: #f8fafc;
            color: #94a3b8;
            border: 1px solid #e2e8f0;
        }

        .doc-badge.is-unchecked .doc-icon {
            color: #cbd5e1;
            font-size: 8px;
        }

        .section-header, .grid-2col-table, .pdf-data-table, .profile-card {
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
            .section-header, .grid-2col-table, .pdf-data-table, .profile-card {
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
        <h3>Social Aid Project Report &bull; {{ $project->project_id ?? 'Report' }}</h3>
        <button onclick="window.print()" class="btn-print">
            &#128438; &nbsp;Print / Save PDF
        </button>
    </div>

    <!-- DOCUMENT HEADER -->
    <table class="doc-header-table">
        <tr>
            <td style="vertical-align: middle;">
                <div class="doc-title">Social Aid Report</div>
                <div class="doc-subtitle">
                    {{ $project->type_of_project ?? 'Orphan Care / Differently Abled / Family Aid' }} &bull; RCFI
                </div>
            </td>
            <td style="text-align: right; vertical-align: middle;">
                <img src="{{ asset('images/logo.png') }}" alt="RCFI Logo" style="height: 38px; width: auto; object-fit: contain; margin-bottom: 2px;"><br>
                <div class="doc-meta-box">
                    <strong>Project ID:</strong> {{ $project->project_id ?? '—' }} &nbsp;|&nbsp; 
                    <strong>Generated:</strong> {{ date('d/m/Y H:i') }}
                </div>
            </td>
        </tr>
    </table>

    @php
        $app = $application ?? ($project->application ?? null);
        $meta = (isset($app->meta) && is_array($app->meta)) ? $app->meta : [];

        // Beneficiary Info
        $beneficiaryName = strtoupper($project->beneficiary_name 
            ?? ($app?->applicant_name 
            ?? ($project->project_name 
            ?? ($meta['applicant_name'] ?? ($meta['name_of_orphan'] ?? 'N/A')))));

        $dob = $project->dob ?? ($app?->dob ?? ($meta['dob'] ?? ($meta['date_of_birth'] ?? 'N/A')));
        if ($dob !== 'N/A') {
            try { $dob = \Carbon\Carbon::parse($dob)->format('d/m/Y'); } catch(\Exception $e) {}
        }

        $age = $project->age ?? ($app?->age ?? ($meta['age'] ?? 'N/A'));
        $place = strtoupper($project->place ?? ($app?->place ?? ($meta['place'] ?? 'N/A')));
        
        $contactNo = $project->contact_number 
            ?? ($app?->contact_number_1 
            ?? ($app?->mobile_1 
            ?? ($meta['contact_number_1'] ?? ($meta['mobile_1'] ?? ($app?->contact_number_2 ?? ($app?->mobile_2 ?? ($meta['mobile_2'] ?? 'N/A')))))));

        $photoSrc = null;
        if (!empty($project->photo)) {
            $photoSrc = asset('storage/' . $project->photo);
        } elseif ($app && !empty($app->student_photo)) {
            $photoSrc = asset('storage/' . $app->student_photo);
        } elseif (!empty($meta['student_photo'])) {
            $photoSrc = asset('storage/' . $meta['student_photo']);
        } elseif ($app && !empty($app->photo)) {
            $photoSrc = asset('storage/' . $app->photo);
        } elseif (!empty($meta['photo'])) {
            $photoSrc = asset('storage/' . $meta['photo']);
        }

        // Project Info
        $projectId = $project->project_id ?? 'N/A';
        $agencyNameResolved = $project->donor?->name 
            ?? ($app?->agency_name 
            ?? ($meta['agency_name'] 
            ?? ($project->agency_name ?? null)));
        if ((!$agencyNameResolved || is_numeric($agencyNameResolved)) && !empty($project->donor_id)) {
            $agencyNameResolved = \App\Models\Donor::find($project->donor_id)?->name ?? $agencyNameResolved;
        }
        if (is_numeric($agencyNameResolved)) {
            $agencyNameResolved = \App\Models\Donor::find($agencyNameResolved)?->name ?? 'N/A';
        }
        $agencyName = strtoupper($agencyNameResolved ?: 'N/A');
        $agencyId = $project->agency_project_no ?? ($app?->agency_number ?? ($meta['agency_number'] ?? 'N/A'));
        $clusterCode = strtoupper($project->cluster_code ?? ($app?->cluster?->name ?? ($app?->cluster?->code ?? ($meta['cluster'] ?? 'N/A'))));
        
        $sponsoredDate = 'N/A';
        if (!empty($project->sponsored_date)) {
            $sponsoredDate = \Carbon\Carbon::parse($project->sponsored_date)->format('d/m/Y');
        } elseif (!empty($project->created_at)) {
            $sponsoredDate = $project->created_at->format('d/m/Y');
        } elseif ($app && !empty($app->created_at)) {
            $sponsoredDate = $app->created_at->format('d/m/Y');
        }

        // Family Details
        $fatherName = strtoupper($project->father_name ?? ($app?->father_name ?? ($meta['father_name'] ?? 'N/A')));
        $grandFatherName = strtoupper($project->grand_father_name ?? ($project->grandfather_name ?? ($app?->grandfather_name ?? ($app?->fathers_father ?? ($meta['grandfather_name'] ?? ($meta['fathers_father'] ?? 'N/A'))))));
        $motherName = strtoupper($project->mother_name ?? ($app?->mother_name ?? ($meta['mother_name'] ?? 'N/A')));
        $motherFatherName = strtoupper($project->mother_father_name ?? ($project->mothers_father_name ?? ($app?->mothers_father_name ?? ($app?->mother_father_name ?? ($meta['mothers_father_name'] ?? ($meta['mothers_father'] ?? 'N/A'))))));
        
        $guardianName = strtoupper($project->guardian_name ?? ($app?->guardian_name ?? ($meta['guardian_name'] ?? 'N/A')));
        $guardianRelation = strtoupper($project->guardian_relation ?? ($app?->guardian_relation ?? ($meta['guardian_relation'] ?? '')));

        $brothers = $project->brothers_count ?? ($app?->siblings_male ?? ($app?->children_male ?? ($app?->male_members ?? ($meta['brothers_count'] ?? ($meta['brothers'] ?? 0)))));
        $sisters = $project->sisters_count ?? ($app?->siblings_female ?? ($app?->children_female ?? ($app?->female_members ?? ($meta['sisters_count'] ?? ($meta['sisters'] ?? 0)))));
        $totalSiblings = $project->family_members ?? ($app?->siblings_total ?? ($app?->children_total ?? ($app?->total_members ?? ($meta['family_members'] ?? ($brothers + $sisters)))));

        $phone1 = $project->mobile_1 ?? ($app?->contact_number_1 ?? ($app?->mobile_1 ?? ($meta['contact_number_1'] ?? ($meta['mobile_1'] ?? $contactNo))));
        $phone2 = $project->mobile_2 ?? ($app?->contact_number_2 ?? ($app?->mobile_2 ?? ($meta['contact_number_2'] ?? ($meta['mobile_2'] ?? '—'))));
        $whatsapp = $project->whatsapp_number ?? ($app?->whatsapp_number ?? ($meta['whatsapp_number'] ?? ($meta['whatsapp'] ?? $phone1)));

        // Address Details
        $houseName = strtoupper($project->house_name ?? ($app?->house_name ?? ($meta['house_name'] ?? 'N/A')));
        $postOffice = strtoupper($project->post_office ?? ($app?->post_office ?? ($meta['post_office'] ?? ($meta['po'] ?? 'N/A'))));
        $panchayat = strtoupper($project->panchayat ?? ($app?->panchayat ?? ($meta['panchayat'] ?? ($meta['panjayath'] ?? 'N/A'))));
        $village = strtoupper($project->village ?? ($app?->village ?? ($meta['village'] ?? 'N/A')));
        $district = strtoupper($project->district ?? ($app?->district ?? ($meta['district'] ?? ($meta['dist'] ?? 'MALAPPURAM'))));
        $state = strtoupper($project->state ?? ($app?->state ?? ($meta['state'] ?? 'KERALA')));
        $pinCode = $project->pin_code ?? ($app?->pin_code ?? ($meta['pin_code'] ?? ($meta['pincode'] ?? '673641')));

        // Socio-Economic Details
        $schoolName = strtoupper($project->school_name ?? ($app?->school_name ?? ($app?->studying_institution ?? ($meta['school_name'] ?? ($meta['school'] ?? ($app?->school_class ?? ($meta['school_class'] ?? 'N/A')))))));
        $madrassaName = strtoupper($project->madrassa_name ?? ($app?->madrassa_name ?? ($meta['madrassa_name'] ?? ($meta['madrassa'] ?? ($app?->madrassa_class ?? ($meta['madrassa_class'] ?? 'N/A'))))));
        $notStudying = strtoupper($project->not_studying_reason ?? ($app?->not_studying_reason ?? ($meta['not_studying_reason'] ?? '—')));
        $healthStatus = strtoupper($project->health_status ?? ($app?->health_status ?? ($app?->disability_type ?? ($meta['health_status'] ?? 'OK'))));
        $income = $project->monthly_income ?? ($app?->monthly_income ?? ($meta['monthly_income'] ?? ($meta['income'] ?? 1000)));
        $expense = $project->monthly_expense ?? ($app?->monthly_expense ?? ($app?->monthly_cost ?? ($meta['monthly_expense'] ?? ($meta['expense'] ?? 1000))));
        $houseType = strtoupper($project->house_type ?? ($app?->house_type ?? ($app?->residence_info ?? ($app?->accommodation ?? ($meta['house_type'] ?? 'OWN HOUSE')))));

        // Funds and Programmes collections
        $projectFunds = (isset($funds) && count($funds) > 0) ? $funds : ($project->funds ?? collect());
        $projectProgrammes = (isset($programmes) && count($programmes) > 0) ? $programmes : ($project->programmes ?? collect());
    @endphp

    <!-- PROFILE SUMMARY CARD -->
    <table class="profile-card">
        <tr>
            <!-- PHOTO BOX -->
            <td class="photo-cell">
                <div class="photo-box">
                    @if($photoSrc)
                        <img src="{{ $photoSrc }}" alt="Photo" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="photo-placeholder" style="display: none;">
                            <svg width="36" height="36" fill="#94a3b8" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        </div>
                    @else
                        <div class="photo-placeholder">
                            <svg width="36" height="36" fill="#94a3b8" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        </div>
                    @endif
                </div>
            </td>

            <!-- BENEFICIARY INFO -->
            <td class="beneficiary-cell">
                <table class="card-table">
                    <tr><td class="lbl">NAME</td><td class="cln">:</td><td class="val">{{ $beneficiaryName }}</td></tr>
                    <tr><td class="lbl">DOB</td><td class="cln">:</td><td class="val">{{ $dob }}</td></tr>
                    <tr><td class="lbl">AGE</td><td class="cln">:</td><td class="val">{{ $age }}</td></tr>
                    <tr><td class="lbl">PLACE</td><td class="cln">:</td><td class="val">{{ $place }}</td></tr>
                    <tr><td class="lbl">CONTACT NO</td><td class="cln">:</td><td class="val">{{ $contactNo }}</td></tr>
                </table>
            </td>

            <!-- PROJECT INFO -->
            <td class="project-cell">
                <table class="card-table">
                    <tr><td class="lbl">PROJECT ID</td><td class="cln">:</td><td class="val">{{ $projectId }}</td></tr>
                    <tr><td class="lbl">AGENCY</td><td class="cln">:</td><td class="val">{{ $agencyName }}</td></tr>
                    <tr><td class="lbl">AGENCY ID</td><td class="cln">:</td><td class="val">{{ $agencyId }}</td></tr>
                    <tr><td class="lbl">CLUSTER</td><td class="cln">:</td><td class="val">{{ $clusterCode }}</td></tr>
                    <tr><td class="lbl">SPONSORED DATE</td><td class="cln">:</td><td class="val">{{ $sponsoredDate }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- SECTION 1: FAMILY DETAILS -->
    <table class="section-header">
        <tr>
            <td class="section-badge-td"><span class="section-badge">Family Details</span></td>
            <td class="section-line-td"><div></div></td>
        </tr>
    </table>

    <table class="grid-2col-table">
        <tbody>
            <tr>
                <td>
                    <table class="field-list-table">
                        <tr><td class="lbl">Father Name</td><td class="cln">:</td><td class="val">{{ $fatherName }}</td></tr>
                        <tr><td class="lbl">Grand Father</td><td class="cln">:</td><td class="val">{{ $grandFatherName }}</td></tr>
                        <tr><td class="lbl">Mother</td><td class="cln">:</td><td class="val">{{ $motherName }}</td></tr>
                        <tr><td class="lbl">Mother's Father</td><td class="cln">:</td><td class="val">{{ $motherFatherName }}</td></tr>
                    </table>
                </td>
                <td>
                    <table class="field-list-table">
                        <tr><td class="lbl">Guardian</td><td class="cln">:</td><td class="val">{{ $guardianName }} @if($guardianRelation) <span style="font-weight:400; color:#64748b;">({{ $guardianRelation }})</span> @endif</td></tr>
                        <tr><td class="lbl">Siblings</td><td class="cln">:</td><td class="val">{{ $totalSiblings }} <span style="font-weight:400; color:#64748b;">(Brothers: {{ $brothers }}, Sisters: {{ $sisters }})</span></td></tr>
                        <tr><td class="lbl">Phone 1 & 2</td><td class="cln">:</td><td class="val">{{ $phone1 }} @if($phone2 && $phone2 !== '—' && $phone2 !== $phone1) , {{ $phone2 }} @endif</td></tr>
                        <tr><td class="lbl">WhatsApp</td><td class="cln">:</td><td class="val">{{ $whatsapp }}</td></tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- SECTION 2: ADDRESS DETAILS -->
    <table class="section-header">
        <tr>
            <td class="section-badge-td"><span class="section-badge">Address Details</span></td>
            <td class="section-line-td"><div></div></td>
        </tr>
    </table>

    <table class="grid-2col-table">
        <tbody>
            <tr>
                <td>
                    <table class="field-list-table">
                        <tr><td class="lbl">House Name</td><td class="cln">:</td><td class="val">{{ $houseName }}</td></tr>
                        <tr><td class="lbl">Place</td><td class="cln">:</td><td class="val">{{ $place }}</td></tr>
                        <tr><td class="lbl">Post Office</td><td class="cln">:</td><td class="val">{{ $postOffice }} P/O</td></tr>
                        <tr><td class="lbl">Panchayat</td><td class="cln">:</td><td class="val">{{ $panchayat }}</td></tr>
                    </table>
                </td>
                <td>
                    <table class="field-list-table">
                        <tr><td class="lbl">Village</td><td class="cln">:</td><td class="val">{{ $village }}</td></tr>
                        <tr><td class="lbl">District</td><td class="cln">:</td><td class="val">{{ $district }}</td></tr>
                        <tr><td class="lbl">State</td><td class="cln">:</td><td class="val">{{ $state }}</td></tr>
                        <tr><td class="lbl">Pincode</td><td class="cln">:</td><td class="val">{{ $pinCode }}</td></tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- SECTION 3: EDUCATION & SOCIO-ECONOMIC DETAILS -->
    <table class="section-header">
        <tr>
            <td class="section-badge-td"><span class="section-badge">Education & Socio-Economic Details</span></td>
            <td class="section-line-td"><div></div></td>
        </tr>
    </table>

    <table class="grid-2col-table">
        <tbody>
            <tr>
                <td>
                    <table class="field-list-table">
                        <tr><td class="lbl">School</td><td class="cln">:</td><td class="val">{{ $schoolName }}</td></tr>
                        <tr><td class="lbl">Madrassa</td><td class="cln">:</td><td class="val">{{ $madrassaName }}</td></tr>
                        <tr><td class="lbl">If Not Studying</td><td class="cln">:</td><td class="val">{{ $notStudying }}</td></tr>
                    </table>
                </td>
                <td>
                    <table class="field-list-table">
                        <tr><td class="lbl">Health Status</td><td class="cln">:</td><td class="val">{{ $healthStatus }}</td></tr>
                        <tr><td class="lbl">Monthly Income</td><td class="cln">:</td><td class="val">₹{{ number_format((float)$income, 0) }} <span style="font-weight:400; color:#64748b;">(Expense: ₹{{ number_format((float)$expense, 0) }})</span></td></tr>
                        <tr><td class="lbl">House Type</td><td class="cln">:</td><td class="val">{{ $houseType }}</td></tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- SECTION 4: FINANCIAL DETAILS -->
    <table class="section-header" style="margin-top: 5px;">
        <tr>
            <td class="section-badge-td"><span class="section-badge">Financial Details</span></td>
            <td class="section-line-td"><div></div></td>
        </tr>
    </table>

    <table class="pdf-data-table">
        <thead>
            <tr>
                <th style="width: 8%; text-align: center;">SL NO</th>
                <th style="width: 20%;">DATE TRANSFERRED</th>
                <th style="width: 15%;">AMOUNT</th>
                <th style="width: 18%;">AGENCY</th>
                <th style="width: 15%;">ACCOUNT NAME</th>
                <th style="width: 14%;">ACCOUNT NO</th>
                <th style="width: 10%;">IFSC CODE</th>
            </tr>
        </thead>
        <tbody>
            @php $totalFundAmount = 0; @endphp
            @forelse($projectFunds as $idx => $fund)
                @php
                    $fAmt = (float)($fund->amount ?? 0);
                    $totalFundAmount += $fAmt;
                    $fDate = '—';
                    if (!empty($fund->date)) {
                        $fDate = \Carbon\Carbon::parse($fund->date)->format('d/m/Y');
                    } elseif (!empty($fund->created_at)) {
                        $fDate = $fund->created_at->format('d/m/Y');
                    }
                @endphp
                <tr>
                    <td style="text-align: center; font-weight: 700; color: #64748b;">{{ $idx + 1 }}</td>
                    <td style="font-weight: 600;">{{ $fDate }}</td>
                    <td style="color: #00b074; font-weight: 700;">₹{{ number_format($fAmt, 2) }}</td>
                    <td>{{ $fund->agency ?? ($fund->donor ?? ($agencyName !== 'N/A' ? $agencyName : '—')) }}</td>
                    <td>{{ $fund->account_name ?? '—' }}</td>
                    <td>{{ $fund->account_number ?? '—' }}</td>
                    <td>{{ $fund->ifsc_code ?? ($fund->ifsc ?? '—') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #94a3b8; padding: 14px;">No financial records transferred yet.</td>
                </tr>
            @endforelse
        </tbody>
        @if(count($projectFunds) > 0)
        <tfoot>
            <tr>
                <td colspan="2" style="font-weight: 800; color: #0f172a; text-transform: uppercase;">Total Transferred</td>
                <td style="color: #00b074; font-weight: 800; font-size: 11px;">₹{{ number_format($totalFundAmount, 2) }}</td>
                <td colspan="4"></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- SECTION 5: REPORT DETAILS (PAGE 2) -->
    <table class="section-header" style="margin-top: 18px;">
        <tr>
            <td class="section-badge-td"><span class="section-badge">Programme & Report Details</span></td>
            <td class="section-line-td"><div></div></td>
        </tr>
    </table>

    <table class="pdf-data-table">
        <thead>
            <tr>
                <th style="width: 6%; text-align: center;">SL NO</th>
                <th style="width: 22%;">PROGRAMME NAME</th>
                <th style="width: 12%;">DATE</th>
                <th style="width: 12%;">PLACE</th>
                <th style="width: 14%;">REMARKS</th>
                <th style="width: 34%;">CHECKLIST & DOCUMENTS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($projectProgrammes as $pidx => $prog)
                @php
                    $pDate = '—';
                    if (!empty($prog->date)) {
                        $pDate = \Carbon\Carbon::parse($prog->date)->format('d/m/Y');
                    } elseif (!empty($prog->created_at)) {
                        $pDate = $prog->created_at->format('d/m/Y');
                    }
                @endphp
                <tr>
                    <td style="text-align: center; font-weight: 700; color: #64748b;">{{ $pidx + 1 }}</td>
                    <td style="font-weight: 700; color: #0f172a;">{{ $prog->name ?? ($prog->programme_name ?? 'Report Collection Programme') }}</td>
                    <td style="font-weight: 600;">{{ $pDate }}</td>
                    <td>{{ $prog->place ?: '—' }}</td>
                    <td>{{ $prog->remarks ?: '—' }}</td>
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
                    <td colspan="6" style="text-align: center; color: #94a3b8; padding: 14px;">No programme reports added yet.</td>
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