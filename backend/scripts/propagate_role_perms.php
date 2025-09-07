<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;

$roleName = $argv[1] ?? null;
if (!$roleName) { echo "USAGE: php scripts/propagate_role_perms.php <RoleName>\n"; exit(1);} 

$role = Role::where('name',$roleName)->first();
if (!$role) { echo "ROLE_NOT_FOUND\n"; exit(1);} 

$perms = $role->permissions->pluck('name')->toArray();
$count = 0;
foreach (User::role($roleName)->get() as $u) {
    $u->syncPermissions($perms);
    $count++;
}

app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

echo "DONE users={$count} perms=".implode('|',$perms)."\n";


