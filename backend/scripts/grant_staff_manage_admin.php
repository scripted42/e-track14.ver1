<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

$perm = Permission::firstOrCreate(['name' => 'staff.manage', 'guard_name' => 'web']);
$role = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
$role->givePermissionTo($perm);

echo "OK\n";


