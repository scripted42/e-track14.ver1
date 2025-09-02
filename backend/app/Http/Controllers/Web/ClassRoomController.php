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
        
        return view('admin.classrooms.index', compact('classRooms'));
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
}
