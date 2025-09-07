<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

echo "roles: ".Role::count()." permissions: ".Permission::count()."\n";
$adminRole = Role::where('name','Admin')->first();
echo "adminRolePerms: ".($adminRole?$adminRole->permissions->pluck('name')->implode('|'):'NULL')."\n";
$u = User::where('email','admin@smpn14.sch.id')->first();
if ($u) {
    echo "userRoles: ".$u->getRoleNames()->implode('|')."\n";
    echo "userPerms: ".$u->getPermissionNames()->implode('|')."\n";
    $links = DB::table('model_has_roles')->where('model_type', App\Models\User::class)->where('model_id',$u->id)->get();
    echo "model_has_roles: ".json_encode($links)."\n";
}


