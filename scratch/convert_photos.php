<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = [
    'general_projects',
    'drinking_water_group_projects',
    'drinking_water_individual_projects',
    'house_projects',
    'shops_other_projects',
    'hospital_clinic_projects',
    'cultural_center_projects',
    'education_center_projects',
    'orphan_care_projects',
    'differently_abled_projects',
    'family_aid_projects'
];

$photoCols = [
    'photos_before', 'photos_starting', 'photos_inbetween', 'photos_after',
    'photos_banner', 'photos_stone', 'photos_inauguration',
    'before_photos', 'starting_photos', 'inbetween_photos', 'after_photos',
    'banner_photos', 'stone_photos', 'inauguration_photos'
];

foreach ($tables as $table) {
    if (!Schema::hasTable($table)) continue;

    $rows = DB::table($table)->get();
    foreach ($rows as $row) {
        $updates = [];

        // Check columns on row
        foreach ($photoCols as $col) {
            if (isset($row->$col) && !empty($row->$col)) {
                $val = $row->$col;
                if (is_string($val) && str_starts_with(trim($val), '[')) {
                    $decoded = json_decode($val, true);
                    if (is_array($decoded) && !empty($decoded)) {
                        $first = reset($decoded);
                        if (is_string($first)) {
                            $updates[$col] = $first;
                        }
                    }
                }
            }
        }

        // Check files JSON column
        if (isset($row->files) && !empty($row->files)) {
            $files = json_decode($row->files, true);
            if (is_array($files)) {
                $filesChanged = false;
                foreach ($files as $k => $v) {
                    if (is_array($v)) {
                        $first = reset($v);
                        $files[$k] = is_string($first) ? $first : null;
                        $filesChanged = true;
                    }
                }
                if ($filesChanged) {
                    $updates['files'] = json_encode($files);
                }
            }
        }

        if (!empty($updates)) {
            DB::table($table)->where('id', $row->id)->update($updates);
            echo "Updated $table ID {$row->id}
";
        }
    }
}

echo "Database photo column conversion complete.
";
