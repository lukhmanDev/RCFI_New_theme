<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$apps = \App\Models\Application::all();
echo "\n=== Applications (Count: {$apps->count()}) ===\n";
foreach ($apps as $a) {
    echo "App ID: {$a->id} | type: '{$a->type_of_project}' | agency_name: '{$a->agency_name}' | agency_number: '{$a->agency_number}' | donor_id: '{$a->donor_id}' | meta_agency: '" . ($a->meta['agency_name'] ?? '') . "'\n";
}

$donors = \App\Models\Donor::all();
echo "\n=== Donors (Count: {$donors->count()}) ===\n";
foreach ($donors as $d) {
    echo "Donor ID: {$d->id} | Name: '{$d->name}' | Code: '{$d->donor_code}'\n";
}
