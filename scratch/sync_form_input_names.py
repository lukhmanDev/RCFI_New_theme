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

    # Update input names for recommendation fields in Add Modal
    content = content.replace('name="meta[recommendation_name]"', 'name="meta[recommender_name]"')
    content = content.replace('name="meta[recommendation_organization]"', 'name="meta[recommender_org]"')
    content = content.replace('name="meta[recommendation_organization_other]"', 'name="meta[recommender_org_other]"')
    content = content.replace('name="meta[recommendation_phone]"', 'name="meta[recommender_phone]"')
    content = content.replace('name="meta[recommendation_position]"', 'name="meta[recommender_position]"')

    with open(fpath, 'w', encoding='utf-8') as f:
        f.write(content)

print("Updated recommendation input names across all views.")
