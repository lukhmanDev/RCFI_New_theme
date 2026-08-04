<?php
define('LARAVEL_START', microtime(true));
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tables = [
    'cultural_center_applications',
    'differently_abled_applications',
    'drinking_water_group_applications',
    'drinking_water_individual_applications',
    'education_center_applications',
    'family_aid_applications',
    'general_applications',
    'hospital_clinic_applications',
    'house_applications',
    'orphan_care_applications',
    'shop_other_applications',
];

foreach ($tables as $t) {
    $has = \Illuminate\Support\Facades\Schema::hasColumn($t, 'permitted_type');
    echo "$t: " . ($has ? 'YES' : 'NO') . "\n";
}
