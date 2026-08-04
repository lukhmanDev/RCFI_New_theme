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
        if (empty($app->permitted_type) && !empty($meta['permitted_type'])) {
            $app->permitted_type = $meta['permitted_type'];
            $app->save();
        }
    }
}

echo "Completed permitted_type backfill.\n";
