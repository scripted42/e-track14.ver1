<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\StudentAttendance;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StudentAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Build base query
        $query = StudentAttendance::with(['student:id,name,nisn,class_room_id', 'student.classRoom:id,name', 'teacher:id,name']);
        
        // For non-admin users, only show students from their classes
        if (!$user->hasRole('Admin')) {
            if ($user->hasRole('Guru')) {
                // Get classes taught by this guru
                $classIds = $user->classRooms()->pluck('id');
                if ($classIds->isEmpty()) {
                    // If guru is not a walikelas, show no students
                    $query->whereRaw('1 = 0');
                } else {
                    $query->whereHas('student', function($q) use ($classIds) {
                        $q->whereIn('class_room_id', $classIds);
                    });
                }
            } else {
                // For other roles, show all students
                // No additional filtering needed
            }
        }

        // Apply filters (same as attendance management)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Apply period filter
        if ($request->filled('period')) {
            $period = $request->period;
            $today = Carbon::today();
            
            switch ($period) {
                case 'today':
                    $query->whereDate('created_at', $today);
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', $today->subDay());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [
                        $today->startOfWeek(),
                        $today->endOfWeek()
                    ]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', $today->month)
                          ->whereYear('created_at', $today->year);
                    break;
                case 'custom':
                    if ($request->filled('date')) {
                        $query->whereDate('created_at', $request->date);
                    }
                    break;
            }
        }

        // Apply status filter
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'on-time') {
                // On-Time: ≤ 06:30 (390 minutes)
                $query->whereRaw('(HOUR(created_at) * 60 + MINUTE(created_at)) <= 390');
            } elseif ($status === 'terlambat') {
                // Terlambat: > 06:30 (390 minutes)
                $query->whereRaw('(HOUR(created_at) * 60 + MINUTE(created_at)) > 390');
            } else {
                // For other statuses, use original logic
                $query->where('status', $request->status);
            }
        }

        // Apply class filter
        if ($request->filled('class_room_id')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('class_room_id', $request->class_room_id);
            });
        }

        // Get all student attendance records
        $allAttendance = $query->orderBy('created_at', 'desc')->get();

        // Group by student and date to create daily summary
        $groupedAttendance = $allAttendance->groupBy(function ($item) {
            return $item->student_id . '_' . $item->created_at->format('Y-m-d');
        });

        $attendanceSummary = collect();
        
        foreach ($groupedAttendance as $key => $records) {
            $studentRecord = $records->first();
            $attendance = $records->first(); // Student attendance only has one record per day
            
            // Determine status based on attendance time (not database status)
            $status = null;
            if ($attendance) {
                $attendanceHour = (int) $attendance->created_at->format('H');
                $attendanceMinute = (int) $attendance->created_at->format('i');
                $attendanceTime = $attendanceHour * 60 + $attendanceMinute; // Convert to minutes
                
                // Student rule: On-Time if ≤ 06:30 (390 minutes), Late if > 06:30
                $deadlineMinutes = 6 * 60 + 30; // 06:30 = 390 minutes
                
                if ($attendanceTime <= $deadlineMinutes) {
                    $status = 'On-Time';
                } else {
                    $status = 'Terlambat';
                }
            }
            
            $attendanceSummary->push([
                'student' => $studentRecord->student,
                'date' => $studentRecord->created_at->format('Y-m-d'),
                'attendance' => $attendance ? [
                    'time' => $attendance->created_at->format('H:i:s'),
                    'status' => $status,
                    'original_status' => $attendance->status,
                    'teacher' => $attendance->teacher,
                ] : null,
                'timestamp' => $attendance ? $attendance->created_at : $studentRecord->created_at, // For sorting
            ]);
        }

        // Sort by timestamp desc and paginate manually
        $attendanceSummary = $attendanceSummary->sortByDesc('timestamp');
        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $items = $attendanceSummary->slice($offset, $perPage)->values();
        
        // Create paginator manually with query parameters preserved
        $attendance = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $attendanceSummary->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page',
                'query' => $request->query(), // Preserve all query parameters
            ]
        );

        // Get filter options
        $students = collect();
        $classRooms = collect();
        
        if ($user->hasRole('Admin')) {
            $students = Student::orderBy('name')->get(['id', 'name', 'nisn']);
            $classRooms = ClassRoom::orderBy('name')->get(['id', 'name']);
        } elseif ($user->hasRole('Guru')) {
            $classIds = $user->classRooms()->pluck('id');
            if (!$classIds->isEmpty()) {
                $students = Student::whereIn('class_room_id', $classIds)->orderBy('name')->get(['id', 'name', 'nisn']);
                $classRooms = ClassRoom::whereIn('id', $classIds)->orderBy('name')->get(['id', 'name']);
            }
        } else {
            $students = Student::orderBy('name')->get(['id', 'name', 'nisn']);
            $classRooms = ClassRoom::orderBy('name')->get(['id', 'name']);
        }

        $statuses = [
            'on-time' => 'On-Time',
            'terlambat' => 'Terlambat',
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            'alpha' => 'Alpha',
        ];

        // Apply final filters to the combined data (same as attendance management)
        if ($request->filled('period')) {
            $period = $request->period;
            $today = Carbon::today();
            
            switch ($period) {
                case 'today':
                    $attendanceSummary = $attendanceSummary->filter(function ($item) use ($today) {
                        return $item['date'] === $today->format('Y-m-d');
                    });
                    break;
                case 'yesterday':
                    $yesterday = $today->subDay();
                    $attendanceSummary = $attendanceSummary->filter(function ($item) use ($yesterday) {
                        return $item['date'] === $yesterday->format('Y-m-d');
                    });
                    break;
                case 'this_week':
                    $startWeek = $today->startOfWeek();
                    $endWeek = $today->endOfWeek();
                    $attendanceSummary = $attendanceSummary->filter(function ($item) use ($startWeek, $endWeek) {
                        $itemDate = Carbon::parse($item['date']);
                        return $itemDate->between($startWeek, $endWeek);
                    });
                    break;
                case 'this_month':
                    $attendanceSummary = $attendanceSummary->filter(function ($item) use ($today) {
                        $itemDate = Carbon::parse($item['date']);
                        return $itemDate->month === $today->month && $itemDate->year === $today->year;
                    });
                    break;
                case 'custom':
                    if ($request->filled('date')) {
                        $customDate = $request->date;
                        $attendanceSummary = $attendanceSummary->filter(function ($item) use ($customDate) {
                            return $item['date'] === $customDate;
                        });
                    }
                    break;
            }
        }

        // Apply status filter to final result
        if ($request->filled('status')) {
            $status = $request->status;
            $attendanceSummary = $attendanceSummary->filter(function ($item) use ($status) {
                if ($status === 'on-time') {
                    return isset($item['attendance']) && $item['attendance']['status'] === 'On-Time';
                } elseif ($status === 'terlambat') {
                    return isset($item['attendance']) && $item['attendance']['status'] === 'Terlambat';
                } elseif (in_array($status, ['izin', 'sakit', 'alpha'])) {
                    return isset($item['attendance']) && $item['attendance']['original_status'] === $status;
                }
                return true;
            });
        }

        // Apply class filter to final result
        if ($request->filled('class_room_id')) {
            $classRoomId = $request->class_room_id;
            $attendanceSummary = $attendanceSummary->filter(function ($item) use ($classRoomId) {
                return $item['student']->class_room_id == $classRoomId;
            });
        }

        // Calculate statistics (same style as attendance management)
        $today = Carbon::today();
        $stats = [
            'present_today' => 0,
            'ontime_today' => 0,
            'late_today' => 0,
            'not_attended_today' => 0,
            'total_students' => 0,
        ];

        // Count today's statistics
        $todayAttendance = $attendanceSummary->where('date', $today->format('Y-m-d'));
        $stats['present_today'] = $todayAttendance->where('attendance', '!=', null)->count();
        $stats['ontime_today'] = $todayAttendance->where('attendance.status', 'On-Time')->count();
        $stats['late_today'] = $todayAttendance->where('attendance.status', 'Terlambat')->count();
        
        // Count total students based on user role and filters
        if ($user->hasRole('Admin')) {
            $stats['total_students'] = Student::count();
        } elseif ($user->hasRole('Guru')) {
            $classIds = $user->classRooms()->pluck('id');
            if (!$classIds->isEmpty()) {
                $stats['total_students'] = Student::whereIn('class_room_id', $classIds)->count();
            } else {
                $stats['total_students'] = 0;
            }
        } else {
            $stats['total_students'] = Student::count();
        }
        
        // Calculate students who have NOT attended today
        $stats['not_attended_today'] = max(0, $stats['total_students'] - $stats['present_today']); // Ensure no negative values

        return view('admin.student-attendance.index', compact(
            'attendance',
            'students',
            'classRooms',
            'statuses',
            'stats'
        ));
    }

    public function export(Request $request)
    {
        $query = StudentAttendance::with(['student:id,name,nisn,class_room_id', 'student.classRoom:id,name', 'teacher:id,name']);

        // Apply same filters as index
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('class_room_id')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('class_room_id', $request->class_room_id);
            });
        }

        if ($request->filled('status')) {
            // Handle status filtering based on time for students
            $status = $request->status;
            if ($status === 'on-time') {
                // On-Time: ≤ 06:30 (390 minutes)
                $query->whereRaw('(HOUR(created_at) * 60 + MINUTE(created_at)) <= 390');
            } elseif ($status === 'terlambat') {
                // Terlambat: > 06:30 (390 minutes)
                $query->whereRaw('(HOUR(created_at) * 60 + MINUTE(created_at)) > 390');
            } else {
                // For other statuses, use original logic
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $attendance = $query->orderBy('created_at', 'desc')->get();

        $filename = 'student_attendance_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($attendance) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Nama Siswa',
                'NISN',
                'Kelas',
                'Status',
                'Tanggal',
                'Waktu',
                'Dilaporkan Oleh'
            ]);

            // CSV data
            foreach ($attendance as $record) {
                fputcsv($file, [
                    $record->student->name,
                    $record->student->nisn,
                    $record->student->classRoom ? $record->student->classRoom->name : 'N/A',
                    ucfirst($record->status),
                    $record->created_at->format('Y-m-d'),
                    $record->created_at->format('H:i:s'),
                    $record->teacher ? $record->teacher->name : 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function detail(Request $request, $studentId, $date)
    {
        $user = auth()->user();
        
        // Check permissions
        if (!$user->hasRole('Admin')) {
            if ($user->hasRole('Guru')) {
                // Check if guru is walikelas for this student
                $classIds = $user->classRooms()->pluck('id');
                $student = Student::findOrFail($studentId);
                if (!$classIds->contains($student->class_room_id)) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
            } else {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        // Get student attendance data for specific student and date
        $attendance = StudentAttendance::with(['student:id,name,nisn,class_room_id', 'student.classRoom:id,name', 'teacher:id,name'])
            ->where('student_id', $studentId)
            ->whereDate('created_at', $date)
            ->orderBy('created_at', 'asc')
            ->first();

        // Calculate status
        $status = null;
        if ($attendance) {
            $attendanceHour = (int) $attendance->created_at->format('H');
            $attendanceMinute = (int) $attendance->created_at->format('i');
            $attendanceTime = $attendanceHour * 60 + $attendanceMinute;
            
            // Student rule: On-Time if ≤ 06:30 (390 minutes), Late if > 06:30
            if ($attendanceTime <= 390) {
                $status = 'On-Time';
            } else {
                $status = 'Terlambat';
            }
        }

        $data = [
            'student' => $attendance?->student ?? Student::with('classRoom:id,name')->findOrFail($studentId),
            'date' => $date,
            'attendance' => $attendance ? [
                'time' => $attendance->created_at->format('H:i:s'),
                'status' => $status,
                'original_status' => $attendance->status,
                'teacher' => $attendance->teacher,
            ] : null,
        ];

        return response()->json($data);
    }
}
