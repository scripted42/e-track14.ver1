<?php

namespace App\GraphQL\Queries;

use App\Models\Student;

class StudentQuery
{
    public function myStudents($_, array $args)
    {
        $user = auth()->user();
        if ($user->hasRole('Guru')) {
            $classIds = $user->classRooms()->pluck('id');
            if ($classIds->isEmpty()) {
                return [];
            }
            return Student::whereIn('class_room_id', $classIds)->get();
        }
        return [];
    }
}
