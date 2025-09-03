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
        
        return view('admin.students.index', compact('students', 'classes'));
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
            $import = new StudentsImport(true); // Preview mode
            Excel::import($import, $request->file('file'));
            
            $previewData = $import->getPreviewData();
            
            return response()->json([
                'success' => true,
                'data' => $previewData,
                'count' => count($previewData)
            ]);
        } catch (\Exception $e) {
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
            $import = new StudentsImport(false); // Import mode
            Excel::import($import, $request->file('file'));
            
            $successCount = $import->getSuccessCount();
            $failureCount = $import->getFailureCount();
            $errors = $import->getErrors();
            
            $message = "Import selesai! Berhasil: {$successCount}, Gagal: {$failureCount}";
            
            if ($failureCount > 0) {
                $message .= "\n\nData yang gagal diimport:\n";
                foreach ($errors as $error) {
                    $message .= "Baris {$error['row']} - {$error['name']} ({$error['nisn']}): {$error['error']}\n";
                }
            }
            
            return redirect()->route('admin.students.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new StudentsTemplateExport, 'template_import_siswa.xlsx');
    }
}