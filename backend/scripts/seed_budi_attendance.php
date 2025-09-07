<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

$user = User::where('name', 'like', 'Budi Santoso%')->first();

if (!$user) {
    echo "USER_NOT_FOUND\n";
    exit(1);
}

$today = Carbon::today();

// Hapus absen hari ini agar tidak duplikat
Attendance::where('user_id', $user->id)
    ->whereDate('timestamp', $today)
    ->delete();

// Insert checkin (07:00) dan checkout (16:00)
Attendance::create([
    'user_id' => $user->id,
    'type' => 'checkin',
    'timestamp' => $today->copy()->setTime(7, 0, 0),
]);

Attendance::create([
    'user_id' => $user->id,
    'type' => 'checkout',
    'timestamp' => $today->copy()->setTime(16, 0, 0),
]);

echo "OK\n";


