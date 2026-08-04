<?php
define('LARAVEL_START', microtime(true));
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$latestApp = \App\Models\EducationCenterApplication::latest()->first();
if ($latestApp) {
    echo "Application ID: " . $latestApp->id . "\n";
    echo "Applicant Name: " . $latestApp->applicant_name . "\n";
    echo "Model ->place: " . var_export($latestApp->place, true) . "\n";
    echo "Model ->location: " . var_export($latestApp->location, true) . "\n";
    
    $addr = $latestApp->address;
    if ($addr) {
        echo "Address object found:\n";
        print_r($addr->toArray());
    } else {
        echo "No address object linked via address() relation.\n";
    }
    
    echo "Raw attributes:\n";
    print_r($latestApp->getAttributes());
}
