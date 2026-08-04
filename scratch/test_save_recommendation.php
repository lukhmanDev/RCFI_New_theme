<?php
define('LARAVEL_START', microtime(true));
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$data = [
    'applicant_name' => 'Test Recommender',
    'status' => 'Pending',
    'meta' => [
        'committee_name' => 'Test Comm',
        'reg_number' => '9999',
        'year' => '2024',
        'recommendation_name' => 'Dr. Ali',
        'recommendation_organization' => 'KMJ',
        'recommendation_phone' => '9895000000',
        'recommendation_position' => 'President'
    ]
];

$model = \App\Models\EducationCenterApplication::create($data);

echo "Created ID: " . $model->id . "\n";
echo "recommender_name in DB attr: " . var_export($model->getRawOriginal('recommender_name'), true) . "\n";
echo "recommender_org in DB attr: " . var_export($model->getRawOriginal('recommender_org'), true) . "\n";
echo "recommender_phone in DB attr: " . var_export($model->getRawOriginal('recommender_phone'), true) . "\n";
echo "recommender_position in DB attr: " . var_export($model->getRawOriginal('recommender_position'), true) . "\n";

// Clean up test record
$model->delete();
