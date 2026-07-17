<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$themeCols = Illuminate\Support\Facades\Schema::getColumnListing('themes');
$subthemeCols = Illuminate\Support\Facades\Schema::getColumnListing('subthemes');

echo "themes columns:\n";
print_r($themeCols);

echo "subthemes columns:\n";
print_r($subthemeCols);

echo "\nthemes dump:\n";
$themes = Illuminate\Support\Facades\DB::table('themes')->get(['id', 'name']);
foreach($themes as $theme) {
    echo "ID: {$theme->id}, Name: {$theme->name}\n";
}
