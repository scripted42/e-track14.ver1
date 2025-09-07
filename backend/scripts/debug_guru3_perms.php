<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;

$email = 'guru3@smpn14.sch.id';
$u = User::where('email',$email)->first();
if (!$u) { echo "NO_USER\n"; exit; }
echo 'USER_ROLES=' . $u->getRoleNames()->implode('|') . "\n";
echo 'USER_PERMS=' . $u->getPermissionNames()->implode('|') . "\n";
$role = Role::where('name','Guru')->first();
echo 'ROLE_PERMS=' . ($role?$role->permissions->pluck('name')->implode('|'):'NULL') . "\n";
echo 'CAN_report.view=' . ($u->can('report.view')?'YES':'NO') . "\n";
echo 'CAN_student.manage=' . ($u->can('student.manage')?'YES':'NO') . "\n";


