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
    'shop_other_projects',
    'hospital_clinic_projects',
    'cultural_center_projects',
    'education_center_projects',
    'orphan_care_projects',
    'differently_abled_projects',
    'family_aid_projects'
];

$categories = [
    'before',
    'starting',
    'inbetween',
    'after',
    'banner',
    'stone',
    'inauguration'
];

foreach ($tables as $t) {
    if (!Schema::hasTable($t)) {
        echo "Table $t does not exist. Skipping.
";
        continue;
    }

    foreach ($categories as $cat) {
        $cols = ['photo_' . $cat, 'photos_' . $cat];

        foreach ($cols as $col) {
            if (!Schema::hasColumn($t, $col)) {
                Schema::table($t, function (Illuminate\Database\Schema\Blueprint $tableBlueprint) use ($col) {
                    $tableBlueprint->string($col, 255)->nullable();
                });
                echo "Added column $col to $t
";
            }
        }
    }
}

echo "Neat photo DB columns setup complete.
";
