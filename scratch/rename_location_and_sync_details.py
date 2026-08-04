import os, re

views_dir = r"d:\LUKMAN\RCFI\New folder (2)\15\rcfi\resources\views"

subdirs = [
    os.path.join(views_dir, "applications"),
    os.path.join(views_dir, "approved_applications"),
    os.path.join(views_dir, "admin"),
    os.path.join(views_dir, "admin", "project_detail"),
]

updated_files = []

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

            # 1. Rename any `Location:` table header/cell label to `Place:`
            # <td...Color... >Location:</td>
            content = re.sub(r'(<td[^>]*>)\s*Location:\s*(</td>)', r'\1Place:\2', content)

            # 2. In admin project detail views, split any remaining District / State: or Pin / Place / Village: or Post / Panchayath:
            
            # District / State:
            content = re.sub(
                r'<tr[^>]*>\s*<td[^>]*>District\s*/\s*State:</td>\s*<td>\{\!\!\s*\$formatVal\(\$metaData\[[\'"](?:locality_)?district[\'"]\]\s*[^)]*\)\s*\!\!\}\s*/\s*\{\!\!\s*\$formatVal\(\$metaData\[[\'"](?:locality_)?state[\'"]\]\s*[^)]*\)\s*\!\!\}</td>\s*</tr>',
                '''<tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">District:</td><td>{!! $formatVal($metaData['locality_district'] ?? ($metaData['district'] ?? null)) !!}</td></tr>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);"><td style="padding: 0.5rem 0; font-weight: 600; color: var(--text-muted);">State:</td><td>{!! $formatVal($metaData['locality_state'] ?? ($metaData['state'] ?? null)) !!}</td></tr>''',
                content
            )

            if content != orig_content:
                with open(fpath, 'w', encoding='utf-8') as f:
                    f.write(content)
                updated_files.append(file)

print("Updated files:")
for uf in set(updated_files):
    print(f" - {uf}")
