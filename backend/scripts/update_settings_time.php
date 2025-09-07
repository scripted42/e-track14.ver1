<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Setting;

$s = Setting::getSettings();
$s->checkin_start = '06:00:00';
$s->checkin_end = '07:05:00';
$s->checkout_start = '15:00:00';
$s->checkout_end = '21:00:00';
$s->save();

echo "OK\n";


