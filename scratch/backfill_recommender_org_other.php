<?php
define('LARAVEL_START', microtime(true));
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$models = [
    \App\Models\CulturalCenterApplication::class,
    \App\Models\DifferentlyAbledApplication::class,
    \App\Models\DrinkingWaterGroupApplication::class,
    \App\Models\DrinkingWaterIndividualApplication::class,
    \App\Models\EducationCenterApplication::class,
    \App\Models\FamilyAidApplication::class,
    \App\Models\GeneralApplication::class,
    \App\Models\HospitalClinicApplication::class,
    \App\Models\HouseApplication::class,
    \App\Models\OrphanCareApplication::class,
    \App\Models\ShopOtherApplication::class,
];

foreach ($models as $model) {
    $apps = $model::all();
    foreach ($apps as $app) {
        $meta = $app->meta ?? [];
        $val = $meta['recommendation_organization_other'] ?? ($meta['recommender_org_other'] ?? null);
        if (empty($app->recommender_org_other) && !empty($val)) {
            $app->recommender_org_other = $val;
            $app->save();
        }
    }
}

echo "Completed recommender_org_other backfill.\n";
