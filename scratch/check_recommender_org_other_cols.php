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
    $has1 = \Illuminate\Support\Facades\Schema::hasColumn($t, 'recommender_org_other');
    $has2 = \Illuminate\Support\Facades\Schema::hasColumn($t, 'recommendation_organization_other');
    echo "$t: recommender_org_other=" . ($has1 ? 'YES' : 'NO') . ", recommendation_organization_other=" . ($has2 ? 'YES' : 'NO') . "\n";
}
