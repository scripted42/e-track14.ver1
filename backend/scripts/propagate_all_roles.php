<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;
use Spatie\Permission\Models\Role;

$roles = Role::all();
$totalUsers = 0;

foreach ($roles as $role) {
    $perms = $role->permissions->pluck('name')->toArray();
    $userIds = DB::table('model_has_roles')
        ->where('role_id', $role->id)
        ->where('model_type', App\Models\User::class)
        ->pluck('model_id');
    $users = User::whereIn('id', $userIds)->get();
    foreach ($users as $u) {
        $u->syncPermissions($perms);
        $totalUsers++;
    }
}

app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

echo "DONE roles={$roles->count()} users_updated={$totalUsers}\n";


