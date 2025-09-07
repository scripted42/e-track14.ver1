<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;

$updated = 0;
$skipped = 0;

$users = User::with('role')->get();
foreach ($users as $user) {
    $roleName = $user->role->role_name ?? null;
    if (!$roleName) { $skipped++; continue; }
    $spRole = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
    $user->syncRoles([$spRole->name]);
    $updated++;
}

echo "SYNC_DONE updated={$updated} skipped={$skipped}\n";


