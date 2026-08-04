<?php
define('LARAVEL_START', microtime(true));
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$req = \Illuminate\Http\Request::create('/admin/applications', 'POST', [
    'applicant_name' => 'Place Test User',
    'category' => 'Education Center',
    'status' => 'Pending',
    'redirect_category' => 'education-center',
    'meta' => [
        'committee_name' => 'Comm Test',
        'reg_number' => '8888',
        'year' => '2023',
        'location' => 'Calicut Central',
        'village' => 'Mavoor',
        'post' => 'Mavoor PO',
        'panchayath' => 'Mavoor GP',
        'district' => 'Kozhikode',
        'state' => 'Kerala',
        'pin_code' => '673661',
        'contact_number_1' => '9995551111',
    ]
]);

$controller = app(\App\Http\Controllers\ApplicationController::class);
$response = $controller->store($req);

$latestApp = \App\Models\EducationCenterApplication::latest()->first();
echo "Latest App ID: " . $latestApp->id . "\n";
echo "Applicant Name: " . $latestApp->applicant_name . "\n";
echo "Place from Model ->place getter: " . var_export($latestApp->place, true) . "\n";
echo "Place from Model ->location getter: " . var_export($latestApp->location, true) . "\n";
$addr = $latestApp->address;
echo "Place in applicant_addresses table: " . var_export($addr->place ?? null, true) . "\n";
echo "Meta array location: " . var_export($latestApp->meta['location'] ?? null, true) . "\n";
echo "Meta array place: " . var_export($latestApp->meta['place'] ?? null, true) . "\n";

// Delete test record
$latestApp->delete();
