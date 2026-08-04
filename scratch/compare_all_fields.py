import os, re, json, glob

views_dir = r"d:\LUKMAN\RCFI\New folder (2)\15\rcfi\resources\views\applications"
models_dir = r"d:\LUKMAN\RCFI\New folder (2)\15\rcfi\app\Models"

tables_info = {
    'cultural_center_applications': ('cultural_center.blade.php', 'CulturalCenterApplication.php'),
    'differently_abled_applications': ('differently_abled.blade.php', 'DifferentlyAbledApplication.php'),
    'drinking_water_group_applications': ('drinking_water_group.blade.php', 'DrinkingWaterGroupApplication.php'),
    'drinking_water_individual_applications': ('drinking_water_individual.blade.php', 'DrinkingWaterIndividualApplication.php'),
    'education_center_applications': ('education_center.blade.php', 'EducationCenterApplication.php'),
    'family_aid_applications': ('family_aid.blade.php', 'FamilyAidApplication.php'),
    'general_applications': ('general.blade.php', 'GeneralApplication.php'),
    'hospital_clinic_applications': ('hospital_clinics.blade.php', 'HospitalClinicApplication.php'),
    'house_applications': ('house.blade.php', 'HouseApplication.php'),
    'orphan_care_applications': ('orphan_care.blade.php', 'OrphanCareApplication.php'),
    'shop_other_applications': ('shops_others.blade.php', 'ShopOtherApplication.php')
}

report = {}

for tbl, (vfile, mfile) in tables_info.items():
    vpath = os.path.join(views_dir, vfile)
    mpath = os.path.join(models_dir, mfile)

    with open(vpath, 'r', encoding='utf-8') as f:
        vcontent = f.read()

    # Find Add Modal names
    add_names = re.findall(r'name=["\'](?:meta\[)?([a-zA-Z0-9_]+)\]?["\']', vcontent)
    
    # Find Edit Modal input IDs
    edit_ids = re.findall(r'id=["\'](edit_[a-zA-Z0-9_]+)["\']', vcontent)

    # Find JS assignments in openEditModal
    js_match = re.search(r'function openEditModal[\s\S]*?\{([\s\S]*?)\n\s*\}', vcontent)
    js_code = js_match.group(1) if js_match else ''
    
    # Find getElementById in openEditModal
    js_assigned_ids = re.findall(r'document\.getElementById\([\'"]([a-zA-Z0-9_]+)[\'"]\)', js_code)

    report[tbl] = {
        'view_file': vfile,
        'model_file': mfile,
        'add_form_names': sorted(list(set(add_names))),
        'edit_modal_ids': sorted(list(set(edit_ids))),
        'js_assigned_ids': sorted(list(set(js_assigned_ids)))
    }

print(json.dumps(report, indent=2))
