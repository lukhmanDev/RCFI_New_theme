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
        $updated = false;
        
        if (empty($app->recommender_name) && !empty($meta['recommendation_name'])) {
            $app->recommender_name = $meta['recommendation_name'];
            $updated = true;
        }
        if (empty($app->recommender_org) && !empty($meta['recommendation_organization'])) {
            $app->recommender_org = $meta['recommendation_organization'];
            $updated = true;
        }
        if (empty($app->recommender_phone) && !empty($meta['recommendation_phone'])) {
            $app->recommender_phone = $meta['recommendation_phone'];
            $updated = true;
        }
        if (empty($app->recommender_position) && !empty($meta['recommendation_position'])) {
            $app->recommender_position = $meta['recommendation_position'];
            $updated = true;
        }
        
        if ($updated) {
            $app->save();
        }
    }
}

echo "Backfill completed successfully.\n";
