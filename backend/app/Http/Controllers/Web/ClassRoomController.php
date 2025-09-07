<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Http\Request;

class ClassRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ClassRoom::with(['walikelas', 'students']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('level', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by level
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $classRooms = $query->orderBy('level')->orderBy('name')->paginate(15);
        
        // Calculate summary statistics from ALL classrooms (not just paginated)
        $allClassRooms = ClassRoom::query();
        
        // Apply same filters for statistics
        if ($request->filled('search')) {
            $search = $request->search;
            $allClassRooms->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('level', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('level')) {
            $allClassRooms->where('level', $request->level);
        }
        
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $allClassRooms->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $allClassRooms->where('is_active', false);
            }
        }
        
        $allClassRoomsData = $allClassRooms->with(['students', 'walikelas'])->get();
        
        $stats = [
            'total_classrooms' => $allClassRoomsData->count(),
            'active_classrooms' => $allClassRoomsData->where('is_active', true)->count(),
            'total_students' => $allClassRoomsData->sum(function($class) {
                return $class->students->count();
            }),
            'classrooms_with_walikelas' => $allClassRoomsData->where('walikelas_id', '!=', null)->count(),
        ];
        
        // Get available teachers for modal
        $availableTeachers = User::whereHas('role', function($query) {
            $query->where('role_name', 'Guru');
        })
        ->whereDoesntHave('classRooms')
        ->select('id', 'name', 'email')
        ->get();
        
        return view('admin.classrooms.index', compact('classRooms', 'stats', 'availableTeachers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $teachers = User::whereHas('role', function($query) {
            $query->where('role_name', 'guru');
        })->with('classRoom')->get();
        
        return view('admin.classrooms.create', compact('teachers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:class_rooms',
            'level' => 'required|string|max:255',
            'description' => 'nullable|string',
            'walikelas_id' => 'nullable|exists:users,id',
        ]);

        $classRoom = ClassRoom::create($request->all());

        // Update user's class_room_id if walikelas is assigned
        if ($request->walikelas_id) {
            $user = User::find($request->walikelas_id);
            if ($user) {
                // Check if user is already walikelas of another class
                $existingClass = ClassRoom::where('walikelas_id', $user->id)->first();
                if ($existingClass) {
                    $classRoom->delete(); // Rollback the created class
                    return redirect()->back()
                        ->withErrors(['walikelas_id' => 'Guru ini sudah menjadi walikelas di kelas ' . $existingClass->name . '.'])
                        ->withInput();
                }
                
                // Remove user from other classes first
                ClassRoom::where('walikelas_id', $user->id)->update(['walikelas_id' => null]);
                
                // Assign user to this class
                User::where('id', $request->walikelas_id)->update(['class_room_id' => $classRoom->id]);
            }
        }

        return redirect()->route('admin.classrooms.index')
            ->with('success', 'Kelas berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassRoom $classroom)
    {
        $classroom->load(['walikelas', 'students']);
        return view('admin.classrooms.show', compact('classroom'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClassRoom $classroom)
    {
        $teachers = User::whereHas('role', function($query) {
            $query->where('role_name', 'guru');
        })->with('classRoom')->get();
        
        return view('admin.classrooms.edit', compact('classroom', 'teachers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClassRoom $classroom)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:class_rooms,name,' . $classroom->id,
            'level' => 'required|string|max:255',
            'description' => 'nullable|string',
            'walikelas_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        $oldWalikelasId = $classroom->walikelas_id;
        
        // Check if new walikelas is already assigned to another class
        if ($request->walikelas_id && $request->walikelas_id != $oldWalikelasId) {
            $existingClass = ClassRoom::where('walikelas_id', $request->walikelas_id)
                ->where('id', '!=', $classroom->id)
                ->first();
            if ($existingClass) {
                return redirect()->back()
                    ->withErrors(['walikelas_id' => 'Guru ini sudah menjadi walikelas di kelas ' . $existingClass->name . '.'])
                    ->withInput();
            }
        }
        
        $classroom->update($request->all());

        // Handle walikelas assignment changes
        if ($oldWalikelasId != $request->walikelas_id) {
            // Remove old walikelas from class_room_id
            if ($oldWalikelasId) {
                User::where('id', $oldWalikelasId)->update(['class_room_id' => null]);
            }
            
            // Assign new walikelas to class_room_id
            if ($request->walikelas_id) {
                // Remove user from other classes first
                ClassRoom::where('walikelas_id', $request->walikelas_id)
                    ->where('id', '!=', $classroom->id)
                    ->update(['walikelas_id' => null]);
                
                User::where('id', $request->walikelas_id)->update(['class_room_id' => $classroom->id]);
            }
        }

        return redirect()->route('admin.classrooms.index')
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassRoom $classroom)
    {
        // Remove walikelas assignment before deleting
        if ($classroom->walikelas_id) {
            User::where('id', $classroom->walikelas_id)->update(['class_room_id' => null]);
        }
        
        $classroom->delete();

        return redirect()->route('admin.classrooms.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }

    /**
     * Assign walikelas to classroom
     */
    public function assignWalikelas(Request $request, ClassRoom $classroom)
    {
        $request->validate([
            'walikelas_id' => 'required|exists:users,id',
        ]);

        // Check if teacher is already walikelas of another class
        $existingClass = ClassRoom::where('walikelas_id', $request->walikelas_id)
            ->where('id', '!=', $classroom->id)
            ->first();
        
        if ($existingClass) {
            return redirect()->back()
                ->withErrors(['walikelas_id' => 'Guru ini sudah menjadi walikelas di kelas ' . $existingClass->name . '.'])
                ->withInput();
        }

        // Check if teacher has role 'Guru'
        $teacher = User::find($request->walikelas_id);
        if (!$teacher->hasRole('Guru')) {
            return redirect()->back()
                ->withErrors(['walikelas_id' => 'User yang dipilih bukan guru.'])
                ->withInput();
        }

        $oldWalikelasId = $classroom->walikelas_id;

        // Update classroom
        $classroom->update(['walikelas_id' => $request->walikelas_id]);

        // Handle user assignments
        if ($oldWalikelasId) {
            User::where('id', $oldWalikelasId)->update(['class_room_id' => null]);
        }
        
        User::where('id', $request->walikelas_id)->update(['class_room_id' => $classroom->id]);

        return redirect()->back()
            ->with('success', 'Wali kelas berhasil ditugaskan ke kelas ' . $classroom->name . '.');
    }

    /**
     * Remove walikelas from classroom
     */
    public function removeWalikelas(ClassRoom $classroom)
    {
        if (!$classroom->walikelas_id) {
            return redirect()->back()
                ->with('error', 'Kelas ini tidak memiliki wali kelas.');
        }

        $walikelasId = $classroom->walikelas_id;
        
        // Update classroom
        $classroom->update(['walikelas_id' => null]);
        
        // Update user
        User::where('id', $walikelasId)->update(['class_room_id' => null]);

        return redirect()->back()
            ->with('success', 'Wali kelas berhasil dihapus dari kelas ' . $classroom->name . '.');
    }

    /**
     * Transfer walikelas from one class to another
     */
    public function transferWalikelas(Request $request)
    {
        $request->validate([
            'from_classroom_id' => 'required|exists:class_rooms,id',
            'to_classroom_id' => 'required|exists:class_rooms,id',
            'walikelas_id' => 'required|exists:users,id',
        ]);

        $fromClassroom = ClassRoom::find($request->from_classroom_id);
        $toClassroom = ClassRoom::find($request->to_classroom_id);
        $walikelas = User::find($request->walikelas_id);

        // Validate that walikelas is currently assigned to from_classroom
        if ($fromClassroom->walikelas_id != $request->walikelas_id) {
            return redirect()->back()
                ->withErrors(['walikelas_id' => 'Guru yang dipilih bukan wali kelas dari kelas ' . $fromClassroom->name . '.'])
                ->withInput();
        }

        // Check if to_classroom already has a walikelas
        if ($toClassroom->walikelas_id) {
            return redirect()->back()
                ->withErrors(['to_classroom_id' => 'Kelas ' . $toClassroom->name . ' sudah memiliki wali kelas.'])
                ->withInput();
        }

        // Perform transfer
        $fromClassroom->update(['walikelas_id' => null]);
        $toClassroom->update(['walikelas_id' => $request->walikelas_id]);
        $walikelas->update(['class_room_id' => $request->to_classroom_id]);

        return redirect()->back()
            ->with('success', 'Wali kelas ' . $walikelas->name . ' berhasil dipindahkan dari kelas ' . $fromClassroom->name . ' ke kelas ' . $toClassroom->name . '.');
    }

    /**
     * Get available teachers for walikelas assignment
     */
    public function getAvailableTeachers()
    {
        try {
            $teachers = User::whereHas('role', function($query) {
                $query->where('role_name', 'Guru');
            })
            ->whereDoesntHave('classRooms')
            ->select('id', 'name', 'email')
            ->get();

            \Log::info('Available teachers found: ' . $teachers->count());
            
            return response()->json($teachers);
        } catch (\Exception $e) {
            \Log::error('Error in getAvailableTeachers: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load teachers'], 500);
        }
    }
}
