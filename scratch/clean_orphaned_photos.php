<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = DB::table('project_files')->get();
$photoCols = ['photos', 'photos_before', 'photos_starting', 'photos_inbetween', 'photos_after', 'photos_banner', 'photos_stone', 'photos_inauguration'];

foreach ($rows as $row) {
    $updates = [];
    foreach ($photoCols as $col) {
        if (!empty($row->$col)) {
            $paths = json_decode($row->$col, true);
            if (is_array($paths)) {
                $validPaths = array_values(array_filter($paths, function($p) {
                    return !empty($p) && file_exists(public_path($p));
                }));
                if (count($validPaths) !== count($paths)) {
                    $updates[$col] = json_encode($validPaths);
                }
            } elseif (is_string($paths)) {
                if (!file_exists(public_path($paths))) {
                    $updates[$col] = json_encode([]);
                }
            }
        }
    }
    if (!empty($updates)) {
        DB::table('project_files')->where('id', $row->id)->update($updates);
        echo "Cleaned orphaned photos for project_files ID {$row->id}\n";
    }
}
echo "Orphan photo cleanup complete.\n";
