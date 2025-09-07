<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

$role = Role::firstOrCreate(['name'=>'Guru','guard_name'=>'web']);
$view = Permission::firstOrCreate(['name'=>'report.view','guard_name'=>'web']);
$role->givePermissionTo($view);

echo "OK\n";


