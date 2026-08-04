import os, re

views_dir = r"d:\LUKMAN\RCFI\New folder (2)\15\rcfi\resources\views\applications"

app_files = [
    'education_center.blade.php',
    'cultural_center.blade.php',
    'hospital_clinics.blade.php',
    'shops_others.blade.php',
    'house.blade.php',
    'drinking_water_group.blade.php',
    'drinking_water_individual.blade.php',
    'general.blade.php',
    'family_aid.blade.php',
    'differently_abled.blade.php',
    'orphan_care.blade.php'
]

for fname in app_files:
    fpath = os.path.join(views_dir, fname)
    if not os.path.exists(fpath):
        continue

    with open(fpath, 'r', encoding='utf-8') as f:
        content = f.read()

    # Replace individual lines in openEditModal where edit_location / edit_place / edit_post / etc are assigned
    content = content.replace(
        "document.getElementById('edit_place').value = meta.place || '';",
        "const locVal = meta.location || meta.place || appItem.location || appItem.place || '';\n"
        "            if (document.getElementById('edit_location')) document.getElementById('edit_location').value = locVal;\n"
        "            if (document.getElementById('edit_place')) document.getElementById('edit_place').value = locVal;"
    )
    content = content.replace(
        "document.getElementById('edit_village').value = meta.village || '';",
        "const vilVal = meta.village || appItem.village || '';\n"
        "            if (document.getElementById('edit_village')) document.getElementById('edit_village').value = vilVal;"
    )
    content = content.replace(
        "document.getElementById('edit_post').value = meta.post || '';",
        "const postVal = meta.post || meta.post_office || appItem.post || appItem.post_office || '';\n"
        "            if (document.getElementById('edit_post')) document.getElementById('edit_post').value = postVal;\n"
        "            if (document.getElementById('edit_post_office')) document.getElementById('edit_post_office').value = postVal;"
    )
    content = content.replace(
        "document.getElementById('edit_panchayath').value = meta.panchayath || '';",
        "const panVal = meta.panchayath || meta.panchayat || appItem.panchayath || appItem.panchayat || '';\n"
        "            if (document.getElementById('edit_panchayath')) document.getElementById('edit_panchayath').value = panVal;\n"
        "            if (document.getElementById('edit_panchayat')) document.getElementById('edit_panchayat').value = panVal;"
    )
    content = content.replace(
        "document.getElementById('edit_district').value = meta.district || '';",
        "const distVal = meta.district || appItem.district || '';\n"
        "            if (document.getElementById('edit_district')) document.getElementById('edit_district').value = distVal;"
    )
    content = content.replace(
        "document.getElementById('edit_state').value = meta.state || '';",
        "const stVal = meta.state || appItem.state || '';\n"
        "            if (document.getElementById('edit_state')) document.getElementById('edit_state').value = stVal;"
    )

    with open(fpath, 'w', encoding='utf-8') as f:
        f.write(content)

print("Updated all 11 application edit modal JS populating logic.")
