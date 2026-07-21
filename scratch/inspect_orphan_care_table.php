<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;

$columns = Schema::getColumnListing('orphan_care_projects');
echo "Columns in orphan_care_projects table:\n";
foreach ($columns as $col) {
    echo "- $col\n";
}
