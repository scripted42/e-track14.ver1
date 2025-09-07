<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use App\Imports\StudentsImport;
use App\Exports\StudentsTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Student::query();
        
        // For Guru role, only show students from their classes
        if ($user->hasRole('Guru')) {
            $myClassIds = $user->classRooms()->pluck('id');
            if ($myClassIds->count() > 0) {
                $query->whereIn('class_room_id', $myClassIds);
            } else {
                // If guru is not walikelas, show no students
                $query->where('id', 0);
            }
        }
        
        // Search by name or NISN
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }
        
        // Filter by class
        if ($request->filled('class_filter')) {
            $query->where('class_name', $request->class_filter);
        }
        
        // Filter by status
        if ($request->filled('status_filter')) {
            $query->where('status', $request->status_filter);
        }
        
        // Sort
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);
        
        $students = $query->paginate(15)->withQueryString();
        
        // Get classes from class_rooms table first, then fallback to students table
        $classes = ClassRoom::where('is_active', true)->orderBy('name')->pluck('name');
        
        // If no classes from class_rooms, get from students
        if ($classes->isEmpty()) {
            $classes = Student::distinct()->pluck('class_name')->filter()->sort()->values();
        }
        
        // Calculate summary statistics from ALL students (not just paginated)
        $allStudents = Student::query();
        
        // Apply same filters for statistics
        if ($user->hasRole('Guru')) {
            $myClassIds = $user->classRooms()->pluck('id');
            if ($myClassIds->count() > 0) {
                $allStudents->whereIn('class_room_id', $myClassIds);
            } else {
                $allStudents->where('id', 0);
            }
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $allStudents->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('class_filter')) {
            $allStudents->where('class_name', $request->class_filter);
        }
        
        if ($request->filled('status_filter')) {
            $allStudents->where('status', $request->status_filter);
        }
        
        $allStudentsData = $allStudents->get();
        
        $stats = [
            'total_students' => $allStudentsData->count(),
            'active_students' => $allStudentsData->where('status', 'Aktif')->count(),
            'inactive_students' => $allStudentsData->where('status', 'Non-Aktif')->count(),
        ];
        
        return view('admin.students.index', compact('students', 'classes', 'stats'));
    }

    public function create()
    {
        // Only admin can create students
        if (!auth()->user()->hasRole('Admin')) {
            abort(403, 'Anda tidak memiliki izin untuk menambah siswa.');
        }
        
        // Get classes from class_rooms table
        $classes = ClassRoom::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.students.create', compact('classes'));
    }

    public function show(Student $student)
    {
        // Generate QR code as base64 for direct display
        $qrCodeBase64 = null;
        if ($student->card_qr_code) {
            $renderer = new ImageRenderer(
                new RendererStyle(120, 10),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            $qrCodeString = $writer->writeString($student->card_qr_code);
            $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeString);
        }
        
        return view('admin.students.show', compact('student', 'qrCodeBase64'));
    }

    public function qrCode(Student $student)
    {
        if (!$student->card_qr_code) {
            abort(404, 'QR Code not found');
        }

        $renderer = new ImageRenderer(
            new RendererStyle(200, 10),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCodeString = $writer->writeString($student->card_qr_code);

        return response($qrCodeString)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    public function store(Request $request)
    {
        // Only admin can create students
        if (!auth()->user()->hasRole('Admin')) {
            abort(403, 'Anda tidak memiliki izin untuk menambah siswa.');
        }
        
        $request->validate([
            'nisn' => 'required|string|max:20|unique:students,nisn',
            'name' => 'required|string|max:200',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'class_name' => 'required|string|max:100',
            'address' => 'nullable|string',
            'card_qr_code' => 'required|string|unique:students,card_qr_code',
            'status' => 'required|in:Aktif,Non-Aktif',
        ]);

        $data = $request->only(['nisn', 'name', 'class_name', 'address', 'card_qr_code', 'status']);
        
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = time() . '_' . $photo->getClientOriginalName();
            $path = $photo->store('student-photos', 'public');
            $data['photo'] = $path;
            
            // Debug: Log the path
            \Log::info('Photo stored at: ' . $path);
            \Log::info('Photo data: ' . $data['photo']);
        }

        Student::create($data);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student created successfully.');
    }

    public function edit(Student $student)
    {
        // Only admin can edit students
        if (!auth()->user()->hasRole('Admin')) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit siswa.');
        }
        
        // Get classes from class_rooms table
        $classes = ClassRoom::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.students.edit', compact('student', 'classes'));
    }

    public function update(Request $request, Student $student)
    {
        // Only admin can update students
        if (!auth()->user()->hasRole('Admin')) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit siswa.');
        }
        
        $request->validate([
            'nisn' => 'required|string|max:20|unique:students,nisn,' . $student->id,
            'name' => 'required|string|max:200',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'class_name' => 'required|string|max:100',
            'address' => 'nullable|string',
            'card_qr_code' => 'required|string|unique:students,card_qr_code,' . $student->id,
            'status' => 'required|in:Aktif,Non-Aktif',
        ]);

        $data = $request->only(['nisn', 'name', 'class_name', 'address', 'card_qr_code', 'status']);
        
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($student->photo && \Storage::disk('public')->exists($student->photo)) {
                \Storage::disk('public')->delete($student->photo);
            }
            
            $photo = $request->file('photo');
            $filename = time() . '_' . $photo->getClientOriginalName();
            $path = $photo->store('student-photos', 'public');
            $data['photo'] = $path;
            
            // Debug: Log the path
            \Log::info('Photo updated at: ' . $path);
            \Log::info('Photo data: ' . $data['photo']);
        }

        $student->update($data);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        // Only admin can delete students
        if (!auth()->user()->hasRole('Admin')) {
            abort(403, 'Anda tidak memiliki izin untuk menghapus siswa.');
        }
        
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }

    public function attendance()
    {
        $attendances = StudentAttendance::with(['student'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.students.attendance', compact('attendances'));
    }

    public function showImport()
    {
        return view('admin.students.import');
    }

    public function previewImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:2048'
        ]);

        try {
            \Log::info('Starting student preview import...');
            \Log::info('File info: ', [
                'name' => $request->file('file')->getClientOriginalName(),
                'size' => $request->file('file')->getSize(),
                'mime' => $request->file('file')->getMimeType()
            ]);
            
            // Try direct PhpSpreadsheet approach
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($request->file('file')->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $data = $worksheet->toArray();
            
            \Log::info('Raw spreadsheet data count: ' . count($data));
            \Log::info('Raw spreadsheet data: ', ['data' => $data]);
            
            // Remove header row if exists
            if (count($data) > 0) {
                $header = array_shift($data);
                \Log::info('Header row: ', ['header' => $header]);
                \Log::info('Data rows count after removing header: ' . count($data));
            }
            
            $previewData = $data;
            
            // Validate each row for duplicates and errors
            $validationResults = $this->validateStudentPreviewData($previewData);
            
            // Debug log
            \Log::info('Preview data count: ' . count($previewData));
            \Log::info('Preview data sample: ', ['data' => $previewData]);
            \Log::info('Validation results: ', ['results' => $validationResults]);
            
            return response()->json([
                'success' => true,
                'data' => $previewData,
                'count' => count($previewData),
                'validation' => $validationResults
            ]);
        } catch (\Exception $e) {
            \Log::error('Preview import error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Error reading file: ' . $e->getMessage()
            ], 422);
        }
    }

    public function processImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:2048'
        ]);

        try {
            \Log::info('Starting student process import...');
            \Log::info('File info: ', [
                'name' => $request->file('file')->getClientOriginalName(),
                'size' => $request->file('file')->getSize(),
                'mime' => $request->file('file')->getMimeType()
            ]);
            
            // Use direct PhpSpreadsheet approach
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($request->file('file')->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $data = $worksheet->toArray();
            
            \Log::info('Raw spreadsheet data count: ' . count($data));
            
            // Remove header row if exists
            if (count($data) > 0) {
                $header = array_shift($data);
                \Log::info('Header row: ', ['header' => $header]);
                \Log::info('Data rows count after removing header: ' . count($data));
            }
            
            $successCount = 0;
            $failureCount = 0;
            $errors = [];
            
            foreach ($data as $index => $row) {
                try {
                    \Log::info('Processing row ' . ($index + 1) . ': ', ['data' => $row]);
                    $this->createStudentFromRow($row);
                    $successCount++;
                    \Log::info('Row ' . ($index + 1) . ' processed successfully');
                } catch (\Exception $e) {
                    $failureCount++;
                    $errors[] = [
                        'row' => $index + 1,
                        'name' => $row[1] ?? 'Unknown',
                        'nisn' => $row[0] ?? 'Unknown',
                        'error' => $e->getMessage()
                    ];
                    \Log::error('Row ' . ($index + 1) . ' failed: ' . $e->getMessage());
                }
            }
            
            $message = "Import selesai! Berhasil: {$successCount}, Gagal: {$failureCount}";
            
            if ($failureCount > 0) {
                $message .= "\n\nData yang gagal diimport:\n";
                foreach ($errors as $error) {
                    $message .= "Baris {$error['row']} - {$error['name']} ({$error['nisn']}): {$error['error']}\n";
                }
            }
            
            \Log::info('Import completed. Success: ' . $successCount . ', Failure: ' . $failureCount);
            
            return redirect()->route('admin.students.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            \Log::error('Process import error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new StudentsTemplateExport, 'template_import_siswa.xlsx');
    }

    private function validateStudentPreviewData($data)
    {
        $validationResults = [
            'valid' => [],
            'invalid' => [],
            'duplicates' => [],
            'summary' => [
                'total' => count($data),
                'valid_count' => 0,
                'invalid_count' => 0,
                'duplicate_count' => 0
            ]
        ];

        foreach ($data as $index => $row) {
            $rowNumber = $index + 1;
            $errors = [];
            $warnings = [];

            // Check if row has minimum required data
            if (count($row) < 3) {
                $errors[] = "Data tidak lengkap (minimal 3 kolom)";
                $validationResults['invalid'][] = [
                    'row' => $rowNumber,
                    'data' => $row,
                    'errors' => $errors,
                    'warnings' => $warnings
                ];
                $validationResults['summary']['invalid_count']++;
                continue;
            }

            $nisn = $row[0] ?? '';
            $name = $row[1] ?? '';
            $class = $row[2] ?? '';

            // Validate NISN
            if (!empty($nisn)) {
                // Check for duplicate NISN in database
                $existingStudent = \App\Models\Student::where('nisn', $nisn)->first();
                if ($existingStudent) {
                    $warnings[] = "NISN '{$nisn}' sudah ada di database (Siswa: {$existingStudent->name})";
                    $validationResults['duplicates'][] = [
                        'row' => $rowNumber,
                        'data' => $row,
                        'type' => 'nisn',
                        'existing_student' => $existingStudent->name,
                        'existing_class' => $existingStudent->class_name
                    ];
                }
            }

            // Validate Name
            if (empty($name)) {
                $errors[] = "Nama tidak boleh kosong";
            }

            // Validate Class
            if (empty($class)) {
                $errors[] = "Kelas tidak boleh kosong";
            } else {
                // Check if class exists in class_rooms table
                $classExists = \App\Models\ClassRoom::where('name', $class)->exists();
                if (!$classExists) {
                    $warnings[] = "Kelas '{$class}' tidak ditemukan di database";
                }
            }

            // Check for duplicates within the same file
            foreach ($data as $otherIndex => $otherRow) {
                if ($index !== $otherIndex && count($otherRow) >= 3) {
                    if (!empty($nisn) && !empty($otherRow[0]) && $nisn === $otherRow[0]) {
                        $warnings[] = "NISN '{$nisn}' duplikat dalam file yang sama (Baris " . ($otherIndex + 1) . ")";
                    }
                }
            }

            // Categorize the row
            if (count($errors) > 0) {
                $validationResults['invalid'][] = [
                    'row' => $rowNumber,
                    'data' => $row,
                    'errors' => $errors,
                    'warnings' => $warnings
                ];
                $validationResults['summary']['invalid_count']++;
            } else {
                $validationResults['valid'][] = [
                    'row' => $rowNumber,
                    'data' => $row,
                    'warnings' => $warnings
                ];
                $validationResults['summary']['valid_count']++;
            }
        }

        return $validationResults;
    }

    private function createStudentFromRow($row)
    {
        \Log::info('Creating student from row: ', ['data' => $row]);
        
        // Check for duplicate NISN
        if (!empty($row[0]) && \App\Models\Student::where('nisn', $row[0])->exists()) {
            throw new \Exception("NISN '{$row[0]}' sudah ada");
        }

        // Generate QR code if not provided
        $qrCode = !empty($row[4]) ? $row[4] : 'STU' . time() . rand(1000, 9999);

        // Check for duplicate QR code
        if (\App\Models\Student::where('card_qr_code', $qrCode)->exists()) {
            $qrCode = 'STU' . time() . rand(1000, 9999);
        }

        // Find class room
        $classRoom = null;
        if (!empty($row[2])) {
            $classRoom = \App\Models\ClassRoom::where('name', $row[2])->first();
        }

        // Prepare student data
        $studentData = [
            'nisn' => $row[0] ?? null, // NISN is first column
            'name' => $row[1], // Nama is second column
            'class_name' => $row[2], // Kelas is third column
            'address' => $row[3] ?? null, // Alamat is fourth column
            'status' => !empty($row[5]) && strtolower($row[5]) === 'non-aktif' ? 'Non-Aktif' : 'Aktif', // Status is sixth column
            'card_qr_code' => $qrCode,
        ];

        // Add class_room_id if class exists
        if ($classRoom) {
            $studentData['class_room_id'] = $classRoom->id;
        }

        // Create student
        $student = \App\Models\Student::create($studentData);
        \Log::info('Student created successfully: ', ['id' => $student->id, 'name' => $student->name]);
    }
    
    // Class-specific students for Guru
    public function classStudents(Request $request, $classId)
    {
        $user = auth()->user();
        
        // Verify that the user is a teacher and has access to this class
        if ($user->hasRole('Guru')) {
            $myClassIds = $user->classRooms()->pluck('id');
            if (!$myClassIds->contains($classId)) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Anda tidak memiliki akses ke kelas tersebut.');
            }
        }
        
        $class = ClassRoom::findOrFail($classId);
        $students = Student::where('class_room_id', $classId)
            ->with('classRoom')
            ->get();
        
        // Get attendance statistics for this class
        $attendanceStats = $this->getClassAttendanceStats($classId);
        
        return view('admin.students.class', compact('class', 'students', 'attendanceStats'));
    }
    
    // My class students for Guru
    public function myClassStudents(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->hasRole('Guru')) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Akses ditolak. Hanya untuk Guru.');
        }
        
        $myClasses = $user->classRooms;
        $classIds = $myClasses->pluck('id');
        
        if ($classIds->isEmpty()) {
            return view('admin.students.my-class', compact('myClasses'))
                ->with('message', 'Anda belum ditugaskan sebagai walikelas.');
        }
        
        $students = Student::whereIn('class_room_id', $classIds)
            ->with('classRoom')
            ->get();
        
        // Get attendance statistics for my classes
        $attendanceStats = $this->getMyClassesAttendanceStats($classIds);
        
        return view('admin.students.my-class', compact('myClasses', 'students', 'attendanceStats'));
    }
    
    // Helper methods
    private function getClassAttendanceStats($classId)
    {
        $today = now()->format('Y-m-d');
        $thisMonth = now()->format('Y-m');
        
        $totalStudents = Student::where('class_room_id', $classId)->count();
        $presentToday = StudentAttendance::whereHas('student', function($query) use ($classId) {
            $query->where('class_room_id', $classId);
        })->whereDate('created_at', $today)->where('status', 'hadir')->count();
        
        $lateToday = StudentAttendance::whereHas('student', function($query) use ($classId) {
            $query->where('class_room_id', $classId);
        })->whereDate('created_at', $today)->where('status', 'terlambat')->count();
        
        $thisMonthAttendance = StudentAttendance::whereHas('student', function($query) use ($classId) {
            $query->where('class_room_id', $classId);
        })->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$thisMonth])->count();
        
        return [
            'total_students' => $totalStudents,
            'present_today' => $presentToday,
            'late_today' => $lateToday,
            'absent_today' => $totalStudents - $presentToday - $lateToday,
            'this_month_attendance' => $thisMonthAttendance,
            'attendance_rate_today' => $totalStudents > 0 ? round(($presentToday / $totalStudents) * 100, 1) : 0
        ];
    }
    
    private function getMyClassesAttendanceStats($classIds)
    {
        $today = now()->format('Y-m-d');
        $thisMonth = now()->format('Y-m');
        
        $totalStudents = Student::whereIn('class_room_id', $classIds)->count();
        $presentToday = StudentAttendance::whereHas('student', function($query) use ($classIds) {
            $query->whereIn('class_room_id', $classIds);
        })->whereDate('created_at', $today)->where('status', 'hadir')->count();
        
        $lateToday = StudentAttendance::whereHas('student', function($query) use ($classIds) {
            $query->whereIn('class_room_id', $classIds);
        })->whereDate('created_at', $today)->where('status', 'terlambat')->count();
        
        $thisMonthAttendance = StudentAttendance::whereHas('student', function($query) use ($classIds) {
            $query->whereIn('class_room_id', $classIds);
        })->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$thisMonth])->count();
        
        return [
            'total_students' => $totalStudents,
            'present_today' => $presentToday,
            'late_today' => $lateToday,
            'absent_today' => $totalStudents - $presentToday - $lateToday,
            'this_month_attendance' => $thisMonthAttendance,
            'attendance_rate_today' => $totalStudents > 0 ? round(($presentToday / $totalStudents) * 100, 1) : 0,
            'classes_count' => $classIds->count()
        ];
    }
}