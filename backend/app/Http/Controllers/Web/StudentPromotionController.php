<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentPromotionController extends Controller
{
    /**
     * Display the promotion management page
     */
    public function index()
    {
        // Debug: Check if user is authenticated and has role
        if (!auth()->check()) {
            return redirect()->route('admin.login');
        }
        
        $user = auth()->user();
        if (!$user->role) {
            return redirect()->route('admin.dashboard')->with('error', 'User tidak memiliki role.');
        }
        
        // Check if user has required role
        $allowedRoles = ['Admin', 'Kepala Sekolah', 'Waka Kurikulum'];
        if (!in_array($user->role->role_name, $allowedRoles)) {
            return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini. Role: ' . $user->role->role_name);
        }
        try {
            $currentYear = now()->year;
            $academicYear = $currentYear . '/' . ($currentYear + 1);
            
            // Get students by class and status
            $studentsByClass = Student::active()
                ->with('classRoom')
                ->get()
                ->groupBy('class_name');
        } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')->with('error', 'Error loading students: ' . $e->getMessage());
        }
        
        // Get statistics
        $stats = [
            'total_active_students' => Student::active()->count(),
            'graduated_students' => Student::graduated()->count(),
            'transferred_students' => Student::transferred()->count(),
            'dropout_students' => Student::dropOut()->count(),
            'retained_students' => Student::retained()->count(),
        ];
        
        // Get class distribution
        $classDistribution = [];
        foreach ($studentsByClass as $className => $students) {
            $classDistribution[$className] = $students->count();
        }
        
        try {
            return view('admin.students.promotion', compact(
                'studentsByClass', 
                'stats', 
                'classDistribution',
                'academicYear'
            ));
        } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')->with('error', 'Error loading view: ' . $e->getMessage());
        }
    }
    
    /**
     * Process class promotion (7->8, 8->9)
     */
    public function promoteClass(Request $request)
    {
        $request->validate([
            'from_class' => 'required|string',
            'to_class' => 'required|string',
            'academic_year' => 'required|string',
        ]);
        
        $fromClass = $request->from_class;
        $toClass = $request->to_class;
        $academicYear = $request->academic_year;
        
        // Get students from the source class
        $students = Student::active()
            ->where('class_name', $fromClass)
            ->get();
        
        if ($students->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Tidak ada siswa aktif di kelas ' . $fromClass);
        }
        
        // Get target classroom
        $targetClassRoom = ClassRoom::where('name', $toClass)->first();
        
        DB::beginTransaction();
        try {
            $promotedCount = 0;
            
            foreach ($students as $student) {
                $student->promoteToClass($toClass, $targetClassRoom?->id);
                $student->academic_year = $academicYear;
                $student->save();
                $promotedCount++;
            }
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', "Berhasil mempromosikan {$promotedCount} siswa dari kelas {$fromClass} ke kelas {$toClass}");
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mempromosikan siswa: ' . $e->getMessage());
        }
    }
    
    /**
     * Process graduation (9->Lulus)
     */
    public function graduateClass(Request $request)
    {
        $request->validate([
            'class_name' => 'required|string',
            'graduation_date' => 'required|date',
            'academic_year' => 'required|string',
        ]);
        
        $className = $request->class_name;
        $graduationDate = $request->graduation_date;
        $academicYear = $request->academic_year;
        
        // Get students from the class
        $students = Student::active()
            ->where('class_name', $className)
            ->get();
        
        if ($students->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Tidak ada siswa aktif di kelas ' . $className);
        }
        
        DB::beginTransaction();
        try {
            $graduatedCount = 0;
            
            foreach ($students as $student) {
                $student->markAsGraduated($graduationDate);
                $student->academic_year = $academicYear;
                $student->save();
                $graduatedCount++;
            }
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', "Berhasil meluluskan {$graduatedCount} siswa dari kelas {$className}");
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat meluluskan siswa: ' . $e->getMessage());
        }
    }
    
    /**
     * Process batch promotion for all classes
     */
    public function batchPromotion(Request $request)
    {
        $request->validate([
            'academic_year' => 'required|string',
            'graduation_date' => 'required|date',
        ]);
        
        $academicYear = $request->academic_year;
        $graduationDate = $request->graduation_date;
        
        DB::beginTransaction();
        try {
            $results = [];
            
            // Promote class 7 to 8
            $class7Students = Student::active()->where('class_name', 'like', '7%')->get();
            foreach ($class7Students as $student) {
                $newClassName = str_replace('7', '8', $student->class_name);
                $targetClassRoom = ClassRoom::where('name', $newClassName)->first();
                $student->promoteToClass($newClassName, $targetClassRoom?->id);
                $student->academic_year = $academicYear;
                $student->save();
            }
            $results['class_7_to_8'] = $class7Students->count();
            
            // Promote class 8 to 9
            $class8Students = Student::active()->where('class_name', 'like', '8%')->get();
            foreach ($class8Students as $student) {
                $newClassName = str_replace('8', '9', $student->class_name);
                $targetClassRoom = ClassRoom::where('name', $newClassName)->first();
                $student->promoteToClass($newClassName, $targetClassRoom?->id);
                $student->academic_year = $academicYear;
                $student->save();
            }
            $results['class_8_to_9'] = $class8Students->count();
            
            // Graduate class 9
            $class9Students = Student::active()->where('class_name', 'like', '9%')->get();
            foreach ($class9Students as $student) {
                $student->markAsGraduated($graduationDate);
                $student->academic_year = $academicYear;
                $student->save();
            }
            $results['class_9_graduated'] = $class9Students->count();
            
            DB::commit();
            
            $message = "Batch promotion berhasil: ";
            $message .= "Kelas 7→8: {$results['class_7_to_8']} siswa, ";
            $message .= "Kelas 8→9: {$results['class_8_to_9']} siswa, ";
            $message .= "Kelas 9 lulus: {$results['class_9_graduated']} siswa";
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat batch promotion: ' . $e->getMessage());
        }
    }
    
    /**
     * Update individual student status
     */
    public function updateStudentStatus(Request $request, Student $student)
    {
        $request->validate([
            'status' => 'required|in:Aktif,Lulus,Pindah,Drop Out,Tidak Naik Kelas',
            'date' => 'nullable|date',
        ]);
        
        $status = $request->status;
        $date = $request->date;
        
        DB::beginTransaction();
        try {
            switch ($status) {
                case 'Lulus':
                    $student->markAsGraduated($date);
                    break;
                case 'Pindah':
                    $student->markAsTransferred($date);
                    break;
                case 'Drop Out':
                    $student->markAsDroppedOut($date);
                    break;
                case 'Tidak Naik Kelas':
                    $student->markAsRetained();
                    break;
                case 'Aktif':
                    $student->status = 'Aktif';
                    $student->save();
                    break;
            }
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', "Status siswa {$student->name} berhasil diubah menjadi {$status}");
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Get students by status for reporting
     */
    public function getStudentsByStatus($status)
    {
        $students = Student::where('status', $status)
            ->with('classRoom')
            ->orderBy('name')
            ->get();
        
        return response()->json($students);
    }
    
    /**
     * Export graduation report
     */
    public function exportGraduationReport(Request $request)
    {
        $academicYear = $request->get('academic_year', now()->year . '/' . (now()->year + 1));
        
        $graduatedStudents = Student::graduated()
            ->where('academic_year', $academicYear)
            ->with('classRoom')
            ->orderBy('graduation_date', 'desc')
            ->get();
        
        $filename = 'laporan_kelulusan_' . str_replace('/', '_', $academicYear) . '_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];
        
        $callback = function() use ($graduatedStudents) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fwrite($file, "\xEF\xBB\xBF");
            
            // Add headers
            fputcsv($file, ['No', 'NISN', 'Nama Siswa', 'Kelas Terakhir', 'Tanggal Lulus', 'Tahun Ajaran']);
            
            // Add data
            foreach ($graduatedStudents as $index => $student) {
                fputcsv($file, [
                    (int)$index + 1,
                    $student->nisn ?? 'N/A',
                    $student->name ?? 'N/A',
                    $student->previous_class ?? $student->class_name ?? 'N/A',
                    $student->graduation_date ?? 'N/A',
                    $student->academic_year ?? 'N/A'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}