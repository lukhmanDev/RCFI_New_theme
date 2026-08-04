import os, re, json

views_dir = r"d:\LUKMAN\RCFI\New folder (2)\15\rcfi\resources\views\applications"

category_files = {
    'cultural_center_applications': 'cultural_center.blade.php',
    'differently_abled_applications': 'differently_abled.blade.php',
    'drinking_water_group_applications': 'drinking_water_group.blade.php',
    'drinking_water_individual_applications': 'drinking_water_individual.blade.php',
    'education_center_applications': 'education_center.blade.php',
    'family_aid_applications': 'family_aid.blade.php',
    'general_applications': 'general.blade.php',
    'hospital_clinic_applications': 'hospital_clinics.blade.php',
    'house_applications': 'house.blade.php',
    'orphan_care_applications': 'orphan_care.blade.php',
    'shop_other_applications': 'shops_others.blade.php'
}

results = {}

for table, filename in category_files.items():
    filepath = os.path.join(views_dir, filename)
    if not os.path.exists(filepath):
        continue
    
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # Find the Add Modal section or first form
    modal_match = re.search(r'id=["\']addAppModal["\'][\s\S]*?</form>', content)
    if not modal_match:
        modal_match = re.search(r'<form[\s\S]*?</form>', content)
    
    form_html = modal_match.group(0) if modal_match else content

    # Find all input/select/textarea names in order
    names = re.findall(r'name=["\'](?:meta\[)?([a-zA-Z0-9_]+)\]?["\']', form_html)
    
    # Filter out csrf, method, redirect_category, category
    ignored = {'_token', '_method', 'category', 'redirect_category', 'status'}
    
    field_order = []
    for n in names:
        if n not in ignored and n not in field_order:
            field_order.append(n)
            
    results[table] = field_order

print(json.dumps(results, indent=2))
