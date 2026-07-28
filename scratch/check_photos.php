<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$projects = DB::table('projects')->get();
foreach ($projects as $p) {
    $files = json_decode($p->files ?? '[]', true);
    if (!empty($files)) {
        echo "Project ID: {$p->id}, Name: {$p->project_name}
";
        print_r($files);
        echo "-----------------------------------
";
    }
}
