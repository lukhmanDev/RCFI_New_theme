<?php
define('LARAVEL_START', microtime(true));
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$latestApp = \App\Models\EducationCenterApplication::latest()->first();
if ($latestApp) {
    echo "ID: " . $latestApp->id . "\n";
    echo "Applicant Name: " . $latestApp->applicant_name . "\n";
    echo "Place: " . ($latestApp->place ?? 'NULL') . "\n";
    echo "Location: " . ($latestApp->location ?? 'NULL') . "\n";
    echo "Post: " . ($latestApp->post ?? 'NULL') . "\n";
    echo "Post Office: " . ($latestApp->post_office ?? 'NULL') . "\n";
    echo "Panchayath: " . ($latestApp->panchayath ?? 'NULL') . "\n";
    echo "Panchayat: " . ($latestApp->panchayat ?? 'NULL') . "\n";
    echo "Village: " . ($latestApp->village ?? 'NULL') . "\n";
    echo "District: " . ($latestApp->district ?? 'NULL') . "\n";
    echo "State: " . ($latestApp->state ?? 'NULL') . "\n";
    echo "Pin Code: " . ($latestApp->pin_code ?? 'NULL') . "\n";
    print_r($latestApp->meta);
} else {
    echo "No application found.";
}
