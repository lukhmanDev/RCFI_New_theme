import os, json, re

views_dir = r"d:\LUKMAN\RCFI\New folder (2)\15\rcfi\resources\views\applications"
mig_path = r"d:\LUKMAN\RCFI\New folder (2)\15\rcfi\database\migrations\2026_08_04_180000_reorder_all_application_tables_columns.php"

tables_info = {
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

extracted_orders = {}

for table, vfile in tables_info.items():
    vpath = os.path.join(views_dir, vfile)
    with open(vpath, 'r', encoding='utf-8') as f:
        content = f.read()

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

php_array_str = "[\n"
for tbl, cols in extracted_orders.items():
    cols_str = ",\n".join([f"            '{c}'" for c in cols])
    php_array_str += f"        '{tbl}' => [\n{cols_str}\n        ],\n"
php_array_str += "    ];"

template = """<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Support\\Facades\\DB;

return new class extends Migration
{
    /**
     * Reorder columns across all application tables to match application form field model.
     */
    public function up(): void
    {
        $tablesConfig = __PHP_ARRAY_STR__;

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

final_code = template.replace("__PHP_ARRAY_STR__", php_array_str)

with open(mig_path, "w", encoding="utf-8") as f:
    f.write(final_code)

print("Migration file updated successfully.")
