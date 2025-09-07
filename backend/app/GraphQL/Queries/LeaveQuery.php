<?php

namespace App\GraphQL\Queries;

use App\Models\Leave;
use Carbon\Carbon;

class LeaveQuery
{
    public function myLeaves($_, array $args)
    {
        $user = auth()->user();
        $status = $args['status'] ?? null;
        $month = $args['month'] ?? null;

        $query = Leave::where('user_id', $user->id);
        if ($status) {
            $query->where('status', $status);
        }
        if ($month) {
            $date = Carbon::createFromFormat('Y-m', $month);
            $query->whereYear('start_date', $date->year)->whereMonth('start_date', $date->month);
        }
        return $query->orderBy('start_date', 'desc')->get();
    }
}
