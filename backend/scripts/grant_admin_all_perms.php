<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

$role = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
$all = Permission::pluck('name')->toArray();
$role->syncPermissions($all);

echo "OK\n";


