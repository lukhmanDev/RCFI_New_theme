import os, sys, re, json

views_dir = r"d:\LUKMAN\RCFI\New folder (2)\15\rcfi\resources\views\applications"
models_dir = r"d:\LUKMAN\RCFI\New folder (2)\15\rcfi\app\Models"
migrations_dir = r"d:\LUKMAN\RCFI\New folder (2)\15\rcfi\database\migrations"

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

extracted_orders = {}

for table, (vfile, mfile) in tables_info.items():
    vpath = os.path.join(views_dir, vfile)
    with open(vpath, 'r', encoding='utf-8') as f:
        content = f.read()

    # Find Add Modal section or form
    modal_match = re.search(r'id=["\']addAppModal["\'][\s\S]*?</form>', content)
    if not modal_match:
        modal_match = re.search(r'<form[\s\S]*?</form>', content)
    form_html = modal_match.group(0) if modal_match else content

    names = re.findall(r'name=["\'](?:meta\[)?([a-zA-Z0-9_]+)\]?["\']', form_html)
    ignored = {'_token', '_method', 'category', 'redirect_category', 'status'}
    
    order = []
    for n in names:
        if n not in ignored and n not in order:
            order.append(n)
            
    extracted_orders[table] = order

# Build PHP Migration Code
migration_content = """<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Support\\Facades\\DB;

return new class extends Migration
{
    /**
     * Reorder columns across all application tables to match application form field model.
     */
    public function up(): void
    {
        $tablesConfig = """ + json.dumps(extracted_orders, indent=8) + """;

        foreach ($tablesConfig as $tableName => $desiredOrder) {
            if (!DB::getSchemaBuilder()->hasTable($tableName)) {
                continue;
            }

            $columns = DB::select("SHOW FULL COLUMNS FROM `{$tableName}`");
            $colMap = [];
            foreach ($columns as $col) {
                $colMap[$col->Field] = $col;
            }

            // System & meta columns that should be ordered gracefully at the end if not in desiredOrder
            $standardTail = [
                'status', 'rejected_reason', 'cluster_id', 'agency_number', 'agency_name', 
                'application_date', 'whatsapp_number', 'current_beneficiaries',
                'project_id', 'created_at', 'updated_at'
            ];

            $orderedList = [];
            foreach ($desiredOrder as $col) {
                if (isset($colMap[$col]) && !in_array($col, $orderedList)) {
                    $orderedList[] = $col;
                }
            }

            foreach ($standardTail as $col) {
                if (isset($colMap[$col]) && !in_array($col, $orderedList)) {
                    $orderedList[] = $col;
                }
            }

            foreach ($colMap as $field => $col) {
                if ($field !== 'id' && !in_array($field, $orderedList)) {
                    $orderedList[] = $field;
                }
            }

            $prev = 'id';
            foreach ($orderedList as $colName) {
                if (!isset($colMap[$colName])) continue;

                $col = $colMap[$colName];
                $type = $col->Type;
                $null = $col->Null === 'YES' ? 'NULL' : 'NOT NULL';
                $default = '';
                if ($col->Default !== null) {
                    $default = "DEFAULT '" . addslashes($col->Default) . "'";
                } elseif ($col->Null === 'YES') {
                    $default = 'DEFAULT NULL';
                }
                $extra = $col->Extra ? $col->Extra : '';
                $collation = $col->Collation ? "CHARACTER SET utf8mb4 COLLATE {$col->Collation}" : '';

                $sql = "ALTER TABLE `{$tableName}` MODIFY COLUMN `{$colName}` {$type} {$collation} {$null} {$default} {$extra} AFTER `{$prev}`";

                try {
                    DB::statement($sql);
                } catch (\\Exception $e) {
                    // Ignore column reorder error for individual column if constraint blocks it
                }

                $prev = $colName;
            }
        }
    }

    public function down(): void
    {
    }
};
"""

mig_path = os.path.join(migrations_dir, "2026_08_04_180000_reorder_all_application_tables_columns.php")
with open(mig_path, "w", encoding="utf-8") as f:
    f.write(migration_content)

print(f"Migration created at {mig_path}")

# Update $metaFields in each Model file
for table, (vfile, mfile) in tables_info.items():
    mpath = os.path.join(models_dir, mfile)
    if not os.path.exists(mpath):
        continue
    
    with open(mpath, "r", encoding="utf-8") as f:
        mcontent = f.read()

    order = extracted_orders[table]
    # format array string
    array_lines = "[\n" + ",\n".join([f"        '{field}'" for field in order]) + "\n    ];"
    
    mcontent_new = re.sub(r'public\s+\$metaFields\s*=\s*\[[\s\S]*?\];', f"public $metaFields = {array_lines}", mcontent)
    
    with open(mpath, "w", encoding="utf-8") as f:
        f.write(mcontent_new)
        
    print(f"Updated model: {mfile}")
