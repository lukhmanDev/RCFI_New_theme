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
        $placeVal = $meta['place'] ?? ($meta['location'] ?? ($app->place ?? ($app->location ?? null)));
        
        if (!empty($placeVal)) {
            $addr = $app->address()->firstOrCreate([]);
            if (empty($addr->place)) {
                $addr->place = $placeVal;
                $addr->save();
            }
        }
    }
}

echo "Completed address place backfill.\n";
