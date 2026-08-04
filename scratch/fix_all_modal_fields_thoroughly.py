import os, re

project_dir = r"d:\LUKMAN\RCFI\New folder (2)\15\rcfi"
meta_trait_path = os.path.join(project_dir, "app", "Traits", "HasCategoryMeta.php")
views_dir = os.path.join(project_dir, "resources", "views", "applications")

# 1. Update HasCategoryMeta.php with recommendation aliases
with open(meta_trait_path, 'r', encoding='utf-8') as f:
    meta_code = f.read()

aliases_replacement = """        $aliases = [
            'post' => 'post_office',
            'post_office' => 'post',
            'panchayath' => 'panchayat',
            'panchayat' => 'panchayath',
            'location' => 'place',
            'place' => 'location',
            'mobile' => 'contact_number_1',
            'mobile_1' => 'contact_number_1',
            'mobile_2' => 'contact_number_2',
            'contact_number_1' => 'mobile_1',
            'contact_number_2' => 'mobile_2',
            'pin' => 'pin_code',
            'pin_code' => 'pin',
            'recommender_name' => 'recommendation_name',
            'recommendation_name' => 'recommender_name',
            'recommender_org' => 'recommendation_organization',
            'recommendation_organization' => 'recommender_org',
            'recommender_phone' => 'recommendation_phone',
            'recommendation_phone' => 'recommender_phone',
            'recommender_position' => 'recommendation_position',
            'recommendation_position' => 'recommender_position',
        ];"""

meta_code = re.sub(r'\$aliases\s*=\s*\[[\s\S]*?\];', aliases_replacement, meta_code)

old_check = "if (\\Illuminate\\Support\\Facades\\Schema::hasColumn($table, $key)) {"
new_check = """if (\\Illuminate\\Support\\Facades\\Schema::hasColumn($table, $key)) {
                        $this->setAttribute($key, $val);
                    } else {
                        $aliasMap = [
                            'recommendation_name' => 'recommender_name',
                            'recommendation_organization' => 'recommender_org',
                            'recommendation_phone' => 'recommender_phone',
                            'recommendation_position' => 'recommender_position',
                            'recommender_name' => 'recommendation_name',
                            'recommender_org' => 'recommendation_organization',
                            'recommender_phone' => 'recommendation_phone',
                            'recommender_position' => 'recommendation_position',
                        ];
                        if (isset($aliasMap[$key]) && \\Illuminate\\Support\\Facades\\Schema::hasColumn($table, $aliasMap[$key])) {
                            $this->setAttribute($aliasMap[$key], $val);
                        }
                    }"""

if "if (\\Illuminate\\Support\\Facades\\Schema::hasColumn($table, $key)) {\n                        $this->setAttribute($key, $val);" in meta_code:
    meta_code = meta_code.replace(
        "if (\\Illuminate\\Support\\Facades\\Schema::hasColumn($table, $key)) {\n                        $this->setAttribute($key, $val);",
        new_check
    )

with open(meta_trait_path, 'w', encoding='utf-8') as f:
    f.write(meta_code)

print("Updated HasCategoryMeta.php")

# 2. Update JS populate logic in all application blade views
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

    # Find openEditModal block
    if "function openEditModal(" in content:
        patch_code = """
            setField('edit_committee_name', getVal('committee_name'));
            setField('edit_reg_number', getVal('reg_number'));
            setField('edit_year', getVal('year'));
            setField('edit_permitted_type', getVal('permitted_type'));
            setField('edit_area', getVal('area'));
            setField('edit_details', appItem.details || appItem.additional_note || meta.details || meta.additional_note || '');

            const recName = getVal('recommendation_name', ['recommender_name']);
            setField('edit_recommendation_name', recName);
            setField('edit_recommender_name', recName);

            const recOrg = getVal('recommendation_organization', ['recommender_org']);
            setField('edit_recommendation_organization', recOrg);
            setField('edit_recommender_org', recOrg);

            const recOrgOther = getVal('recommendation_organization_other', ['recommender_org_other']);
            setField('edit_recommendation_organization_other', recOrgOther);

            const recPhone = getVal('recommendation_phone', ['recommender_phone']);
            setField('edit_recommendation_phone', recPhone);
            setField('edit_recommender_phone', recPhone);

            const recPos = getVal('recommendation_position', ['recommender_position']);
            setField('edit_recommendation_position', recPos);
            setField('edit_recommender_position', recPos);
"""
        if "setField('edit_pin', getVal('pin', ['pin_code']));" in content and "setField('edit_committee_name'" not in content:
            content = content.replace(
                "setField('edit_pin', getVal('pin', ['pin_code']));",
                "setField('edit_pin', getVal('pin', ['pin_code']));\n" + patch_code
            )
            with open(fpath, 'w', encoding='utf-8') as f:
                f.write(content)
            print(f"Patched openEditModal in {fname}")

print("Completed thorough modal populating fix.")
