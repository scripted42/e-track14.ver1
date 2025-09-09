<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$u = User::where('email','admin@smpn14.sch.id')->first();
if (!$u) { echo "NO_ADMIN\n"; exit; }
// echo $u->can('staff.manage') ? "YES\n" : "NO\n";


