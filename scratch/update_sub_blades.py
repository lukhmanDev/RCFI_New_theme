import os, re

sub_dir = r"d:\LUKMAN\RCFI\New folder (2)\15\rcfi\resources\views\admin\project_detail"

def update_file(filename):
    filepath = os.path.join(sub_dir, filename)
    if not os.path.exists(filepath):
        print(f"File not found: {filename}")
        return
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # 1. Update JS variables if present
    var_target = "const dist = getVal(['district']);\n            const st = getVal(['state']);\n            const districtState = (dist || st) ? `${formatVal(dist)} / ${formatVal(st)}` : '<span style=\"color: var(--text-muted); font-style: italic;\">N/A</span>';\n            const c1 = getVal(['contact_number_1', 'mobile_1', 'mobile']);\n            const c2 = getVal(['contact_number_2', 'mobile_2']);\n            const contact = (c1 || c2) ? `${formatVal(c1)} / ${formatVal(c2)}` : '<span style=\"color: var(--text-muted); font-style: italic;\">N/A</span>';"
    
    var_replacement = """const dist = formatVal(getVal(['district']));
            const st = formatVal(getVal(['state']));
            const pinCode = formatVal(getVal(['pin_code', 'pin']));
            const c1 = formatVal(getVal(['contact_number_1', 'mobile_1', 'mobile', 'contact1']));
            const c2 = formatVal(getVal(['contact_number_2', 'mobile_2', 'contact2']));
            const mahalluName = formatVal(getVal(['mahallu_name']));
            const localityPlace = formatVal(getVal(['locality_place', 'locality_location', 'location']));
            const localityVillage = formatVal(getVal(['locality_village', 'village']));
            const localityPost = formatVal(getVal(['locality_post', 'post']));
            const localityPanchayath = formatVal(getVal(['locality_panchayath', 'panchayath']));
            const localityDist = formatVal(getVal(['locality_district', 'district']));
            const localitySt = formatVal(getVal(['locality_state', 'state']));
            const localityPin = formatVal(getVal(['locality_pin_code', 'locality_pin']));"""

    if var_target in content:
        content = content.replace(var_target, var_replacement)

    # Replace combined rows in HTML template literal:
    row_target_1 = """<tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">District / State:</td><td>${districtState}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Contact Number 1:</td><td>{!! $formatVal($metaData['contact_number_1'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Contact Number 2:</td><td>{!! $formatVal($metaData['contact_number_2'] ?? null) !!}</td></tr>"""

    row_replacement_1 = """<tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">District:</td><td>${dist}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">State:</td><td>${st}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Pin Code:</td><td>${pinCode}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Contact Number 1:</td><td>${c1}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Contact Number 2:</td><td>${c2}</td></tr>"""

    if row_target_1 in content:
        content = content.replace(row_target_1, row_replacement_1)

    row_target_2 = """<tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px; color: var(--text-muted);">Mahallu Name:</td><td>${mahalluName}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Place:</td><td>${localityLocation}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Village:</td><td>${localityVillage}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">District / State:</td><td>${localityDistState}</td></tr>"""

    row_replacement_2 = """<tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; width: 140px; color: var(--text-muted);">Mahallu Name:</td><td>${mahalluName}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Place:</td><td>${localityPlace}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Village:</td><td>${localityVillage}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Post:</td><td>${localityPost}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Panchayath:</td><td>${localityPanchayath}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">District:</td><td>${localityDist}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">State:</td><td>${localitySt}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Pin Code:</td><td>${localityPin}</td></tr>"""

    if row_target_2 in content:
        content = content.replace(row_target_2, row_replacement_2)

    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f"Updated {filename}")

for f in ['education_center.blade.php', 'cultural_center.blade.php', 'general.blade.php', 'hospital_clinics.blade.php', 'shops_others.blade.php']:
    update_file(f)
