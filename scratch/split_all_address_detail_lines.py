import os, re, glob

views_dir = r"d:\LUKMAN\RCFI\New folder (2)\15\rcfi\resources\views"

subdirs = [
    os.path.join(views_dir, "applications"),
    os.path.join(views_dir, "approved_applications"),
    os.path.join(views_dir, "admin", "project_detail"),
]

updated_count = 0

for sdir in subdirs:
    if not os.path.exists(sdir):
        continue
    for root, _, files in os.walk(sdir):
        for file in files:
            if not file.endswith('.blade.php'):
                continue
            fpath = os.path.join(root, file)
            with open(fpath, 'r', encoding='utf-8') as f:
                content = f.read()

            orig_content = content

            # JS template literal replacements (for viewAppModal JS inside blade files)
            
            # 1. District / State / Pin: -> District, State, Pin Code
            content = re.sub(
                r'<tr[^>]*>\s*<td[^>]*>District\s*/\s*State\s*/\s*Pin:</td>\s*<td>\$\{formatVal\([^)]+\)\}\s*/\s*\$\{formatVal\([^)]+\)\}\s*/\s*\$\{formatVal\([^)]+\)\}</td>\s*</tr>',
                '''<tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">District:</td><td>${formatVal(meta.district)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">State:</td><td>${formatVal(meta.state)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Pin Code:</td><td>${formatVal(meta.pin_code || meta.pin)}</td></tr>''',
                content
            )

            # 2. Contact 1 / 2: or Mobile 1 / 2:
            content = re.sub(
                r'<tr[^>]*>\s*<td[^>]*>(?:Contact|Mobile)\s*1\s*/\s*2:</td>\s*<td>\$\{formatVal\([^)]+\)\}\s*/\s*\$\{formatVal\([^)]+\)\}</td>\s*</tr>',
                '''<tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Contact Number 1:</td><td>${formatVal(meta.contact_number_1)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Contact Number 2:</td><td>${formatVal(meta.contact_number_2)}</td></tr>''',
                content
            )

            # 3. Pin / Place / Village: (Mahallu)
            content = re.sub(
                r'<tr[^>]*>\s*<td[^>]*>Pin\s*/\s*Place\s*/\s*Village:</td>\s*<td>\$\{formatVal\([^)]+\)\}\s*/\s*\$\{formatVal\([^)]+\)\}\s*/\s*\$\{formatVal\([^)]+\)\}</td>\s*</tr>',
                '''<tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Pin Code:</td><td>${formatVal(meta.locality_pin_code || meta.locality_pin)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Place:</td><td>${formatVal(meta.locality_place)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Village:</td><td>${formatVal(meta.locality_village)}</td></tr>''',
                content
            )

            # 4. Post / Panchayath: (Mahallu)
            content = re.sub(
                r'<tr[^>]*>\s*<td[^>]*>Post\s*/\s*Panchayath:</td>\s*<td>\$\{formatVal\([^)]+\)\}\s*/\s*\$\{formatVal\([^)]+\)\}</td>\s*</tr>',
                '''<tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Post:</td><td>${formatVal(meta.locality_post)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">Panchayath:</td><td>${formatVal(meta.locality_panchayath || meta.locality_panchayat)}</td></tr>''',
                content
            )

            # 5. District / State: (Mahallu / Applicant)
            content = re.sub(
                r'<tr[^>]*>\s*<td[^>]*>District\s*/\s*State:</td>\s*<td>\$\{formatVal\([^)]+\)\}\s*/\s*\$\{formatVal\([^)]+\)\}</td>\s*</tr>',
                '''<tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">District:</td><td>${formatVal(meta.locality_district || meta.district)}</td></tr>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600;">State:</td><td>${formatVal(meta.locality_state || meta.state)}</td></tr>''',
                content
            )

            # Blade PHP template replacements (in admin project detail views / approved application blade files)
            # {!! $formatVal($metaData['district'] ?? null) !!} / {!! $formatVal($metaData['state'] ?? null) !!}
            old_blade_dist_state_pin = r'<tr[^>]*>\s*<td[^>]*>District\s*/\s*State\s*/\s*Pin:</td>[\s\S]*?</tr>'
            new_blade_dist_state_pin = '''<tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">District:</td><td>{!! $formatVal($metaData['district'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">State:</td><td>{!! $formatVal($metaData['state'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Pin Code:</td><td>{!! $formatVal($metaData['pin_code'] ?? ($metaData['pin'] ?? null)) !!}</td></tr>'''

            if re.search(old_blade_dist_state_pin, content):
                content = re.sub(old_blade_dist_state_pin, new_blade_dist_state_pin, content)

            old_blade_mobile = r'<tr[^>]*>\s*<td[^>]*>(?:Contact|Mobile)\s*1\s*/\s*2:</td>[\s\S]*?</tr>'
            new_blade_mobile = '''<tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Contact Number 1:</td><td>{!! $formatVal($metaData['contact_number_1'] ?? null) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">Contact Number 2:</td><td>{!! $formatVal($metaData['contact_number_2'] ?? null) !!}</td></tr>'''

            if re.search(old_blade_mobile, content):
                content = re.sub(old_blade_mobile, new_blade_mobile, content)

            if content != orig_content:
                with open(fpath, 'w', encoding='utf-8') as f:
                    f.write(content)
                updated_count += 1
                print(f"Updated {file}")

print(f"Total view files updated: {updated_count}")
