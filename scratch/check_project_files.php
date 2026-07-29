<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$files = DB::table('project_files')->get();
echo json_encode($files, JSON_PRETTY_PRINT);
