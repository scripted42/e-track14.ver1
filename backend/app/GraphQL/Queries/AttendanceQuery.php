<?php

namespace App\GraphQL\Queries;

use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceQuery
{
    public function myAttendance($_, array $args)
    {
        $user = auth()->user();
        $todayOnly = $args['todayOnly'] ?? false;
        $month = $args['month'] ?? null;

        $query = Attendance::with([])->where('user_id', $user->id);

        if ($todayOnly) {
            $query->whereDate('timestamp', Carbon::today());
        } elseif ($month) {
            $date = Carbon::createFromFormat('Y-m', $month);
            $query->whereYear('timestamp', $date->year)->whereMonth('timestamp', $date->month);
        }

        $records = $query->orderBy('timestamp', 'desc')->get()->groupBy(function ($r) {
            return Carbon::parse($r->timestamp)->format('Y-m-d');
        });

        $result = [];
        foreach ($records as $date => $items) {
            $checkin = $items->firstWhere('type', 'checkin');
            $checkout = $items->firstWhere('type', 'checkout');
            $result[] = [
                'date' => $date,
                'checkin' => $checkin,
                'checkout' => $checkout,
            ];
        }
        return $result;
    }
}
