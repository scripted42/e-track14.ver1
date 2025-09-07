<?php

namespace App\GraphQL\Mutations;

use App\Models\Leave;
use Illuminate\Support\Facades\Validator;

class LeaveMutations
{
    public function requestLeave($_, array $args)
    {
        $input = $args['input'];
        Validator::make($input, [
            'leave_type' => 'required|in:izin,sakit,cuti,dinas_luar',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ])->validate();

        return Leave::create([
            'user_id' => auth()->id(),
            'leave_type' => $input['leave_type'],
            'start_date' => $input['start_date'],
            'end_date' => $input['end_date'],
            'reason' => $input['reason'],
            'status' => 'menunggu',
        ]);
    }
}
