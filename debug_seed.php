<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$db = app('db');

echo "=== USERS ===\n";
$users = $db->table('users')->select('id','name','role')->get();
foreach ($users as $u) echo "  id={$u->id}, name={$u->name}, role={$u->role}\n";

echo "\n=== DONORS ===\n";
$donors = $db->table('donors')->select('id','name')->get();
foreach ($donors as $d) echo "  id={$d->id}, name={$d->name}\n";

echo "\n=== SHOP_OTHER_PROJECTS columns ===\n";
$cols = $db->select("DESCRIBE shop_other_projects");
foreach ($cols as $c) echo "  {$c->Field} | {$c->Type} | {$c->Null} | default={$c->Default}\n";

echo "\n=== EDUCATION_CENTER_PROJECTS columns ===\n";
$cols = $db->select("DESCRIBE education_center_projects");
foreach ($cols as $c) echo "  {$c->Field} | {$c->Type} | {$c->Null} | default={$c->Default}\n";
