<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach (\App\Models\User::all() as $u) {
    echo "id={$u->id}, name={$u->name}, designation={$u->designation}, role={$u->role}, suspended={$u->is_suspended}\n";
}
