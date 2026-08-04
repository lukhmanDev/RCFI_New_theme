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

updated_files = []

for fname in app_files:
    fpath = os.path.join(views_dir, fname)
    if not os.path.exists(fpath):
        continue
        
    with open(fpath, 'r', encoding='utf-8') as f:
        content = f.read()

    # Pattern matching lines like: if (document.getElementById('edit_place')) ...
    # Or replacing the block where place/location/post/village/panchayat/district/state are assigned
    
    # We can inject a robust setter logic inside openEditModal(appItem) { ... }
    
    replacement_block = """            const meta = appItem.meta || {};
            
            const getVal = (primary, alts = []) => {
                if (meta[primary] !== undefined && meta[primary] !== null && meta[primary] !== '') return meta[primary];
                if (appItem[primary] !== undefined && appItem[primary] !== null && appItem[primary] !== '') return appItem[primary];
                for (let a of alts) {
                    if (meta[a] !== undefined && meta[a] !== null && meta[a] !== '') return meta[a];
                    if (appItem[a] !== undefined && appItem[a] !== null && appItem[a] !== '') return appItem[a];
                }
                return '';
            };

            const setField = (id, val) => {
                const el = document.getElementById(id);
                if (el) el.value = val;
            };

            setField('edit_house_name', getVal('house_name'));
            setField('edit_location', getVal('location', ['place']));
            setField('edit_place', getVal('place', ['location']));
            setField('edit_village', getVal('village'));
            setField('edit_post', getVal('post', ['post_office']));
            setField('edit_post_office', getVal('post_office', ['post']));
            setField('edit_panchayath', getVal('panchayath', ['panchayat']));
            setField('edit_panchayat', getVal('panchayat', ['panchayath']));
            setField('edit_district', getVal('district'));
            setField('edit_state', getVal('state'));
            setField('edit_pin_code', getVal('pin_code', ['pin', 'locality_pin_code']));
            setField('edit_pin', getVal('pin', ['pin_code']));"""

    # Match old block from `const meta = appItem.meta || {};` down to `document.getElementById('edit_state')...`
    pattern = r'const\s+meta\s*=\s*appItem\.meta\s*\|\|\s*\{\};[\s\S]*?if\s*\(\s*document\.getElementById\([\'"]edit_state[\'"]\)\s*\)[\s\S]*?\n'
    
    if re.search(pattern, content):
        new_content = re.sub(pattern, replacement_block + "\n", content)
        with open(fpath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        updated_files.append(fname)
    else:
        # Try matching lines individually if pattern didn't match directly
        # Replace individual lines
        lines_to_replace = [
            (r'if\s*\(\s*document\.getElementById\([\'"]edit_place[\'"]\)\s*\).*?\n', "setField('edit_place', getVal('place', ['location']));\nsetField('edit_location', getVal('location', ['place']));\n"),
            (r'if\s*\(\s*document\.getElementById\([\'"]edit_post_office[\'"]\)\s*\).*?\n', "setField('edit_post_office', getVal('post_office', ['post']));\nsetField('edit_post', getVal('post', ['post_office']));\n"),
            (r'if\s*\(\s*document\.getElementById\([\'"]edit_village[\'"]\)\s*\).*?\n', "setField('edit_village', getVal('village'));\n"),
            (r'if\s*\(\s*document\.getElementById\([\'"]edit_panchayat[\'"]\)\s*\).*?\n', "setField('edit_panchayat', getVal('panchayat', ['panchayath']));\nsetField('edit_panchayath', getVal('panchayath', ['panchayat']));\n"),
            (r'if\s*\(\s*document\.getElementById\([\'"]edit_district[\'"]\)\s*\).*?\n', "setField('edit_district', getVal('district'));\n"),
            (r'if\s*\(\s*document\.getElementById\([\'"]edit_state[\'"]\)\s*\).*?\n', "setField('edit_state', getVal('state'));\n")
        ]
        modified = False
        new_content = content
        for pat, rep in lines_to_replace:
            if re.search(pat, new_content):
                new_content = re.sub(pat, rep, new_content)
                modified = True
        
        if modified:
            # Also ensure getVal and setField exist right after const meta = appItem.meta || {};
            helper_defs = """const meta = appItem.meta || {};
            const getVal = (primary, alts = []) => {
                if (meta[primary] !== undefined && meta[primary] !== null && meta[primary] !== '') return meta[primary];
                if (appItem[primary] !== undefined && appItem[primary] !== null && appItem[primary] !== '') return appItem[primary];
                for (let a of alts) {
                    if (meta[a] !== undefined && meta[a] !== null && meta[a] !== '') return meta[a];
                    if (appItem[a] !== undefined && appItem[a] !== null && appItem[a] !== '') return appItem[a];
                }
                return '';
            };
            const setField = (id, val) => {
                const el = document.getElementById(id);
                if (el) el.value = val;
            };
"""
            new_content = re.sub(r'const\s+meta\s*=\s*appItem\.meta\s*\|\|\s*\{\};', helper_defs, new_content)
            with open(fpath, 'w', encoding='utf-8') as f:
                f.write(new_content)
            updated_files.append(fname)

print("Updated JS populate logic in:")
for uf in updated_files:
    print(f" - {uf}")
