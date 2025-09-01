<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::paginate(15);
        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        return view('admin.students.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|string|unique:students',
            'name' => 'required|string|max:255',
            'class_name' => 'required|string|max:50',
            'gender' => 'required|in:L,P',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
        ]);

        Student::create($request->all());

        return redirect()->route('admin.students.index')
            ->with('success', 'Student created successfully.');
    }

    public function edit(Student $student)
    {
        return view('admin.students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'nis' => 'required|string|unique:students,nis,' . $student->id,
            'name' => 'required|string|max:255',
            'class_name' => 'required|string|max:50',
            'gender' => 'required|in:L,P',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
        ]);

        $student->update($request->all());

        return redirect()->route('admin.students.index')
            ->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
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

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $data = array_map('str_getcsv', file($file->path()));
        $header = array_shift($data);

        $imported = 0;
        $errors = [];

        foreach ($data as $row) {
            $studentData = array_combine($header, $row);
            
            try {
                Student::create([
                    'nis' => $studentData['nis'],
                    'name' => $studentData['name'],
                    'class_name' => $studentData['class_name'],
                    'gender' => $studentData['gender'],
                    'phone' => $studentData['phone'] ?? null,
                    'address' => $studentData['address'] ?? null,
                    'parent_name' => $studentData['parent_name'] ?? null,
                    'parent_phone' => $studentData['parent_phone'] ?? null,
                ]);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Error importing {$studentData['name']}: " . $e->getMessage();
            }
        }

        $message = "Imported {$imported} students successfully.";
        if (!empty($errors)) {
            $message .= " Errors: " . implode(', ', $errors);
        }

        return redirect()->route('admin.students.index')
            ->with('success', $message);
    }
}