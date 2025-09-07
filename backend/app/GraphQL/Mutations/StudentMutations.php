<?php

namespace App\GraphQL\Mutations;

use App\Models\Student;
use App\Models\StudentAttendance;
use Carbon\Carbon;

class StudentMutations
{
    public function submitScanBatch($_, array $args)
    {
        $items = $args['input']['items'] ?? [];
        $results = [];
        foreach ($items as $item) {
            $student = Student::where('nisn', $item['nisn'])->first();
            if (!$student) {
                continue;
            }
            $scannedAt = Carbon::parse($item['scanned_at']);
            StudentAttendance::create([
                'student_id' => $student->id,
                'teacher_id' => auth()->id(),
                'status' => 'hadir',
                'created_at' => $scannedAt,
                'updated_at' => $scannedAt,
            ]);
            $results[] = [
                'student' => $student,
                'scanned_at' => $scannedAt,
            ];
        }
        return $results;
    }
}
