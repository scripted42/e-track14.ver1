<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Imports\StaffImport;
use App\Exports\StaffTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::orderBy('role_name')->get();
        $classRooms = ClassRoom::where('is_active', true)
            ->with('walikelas') // Load walikelas info
            ->orderBy('name')
            ->get();
        return view('admin.users.create', compact('roles', 'classRooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nip_nik' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'address' => 'nullable|string',
            'status' => 'required|in:Aktif,Non-Aktif',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'is_walikelas' => 'nullable|boolean',
            'class_room_id' => 'nullable|exists:class_rooms,id',
        ]);

        $data = [
            'name' => $request->name,
            'nip_nik' => $request->nip_nik,
            'address' => $request->address,
            'status' => $request->status,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'is_walikelas' => $request->has('is_walikelas') ? true : false,
            'class_room_id' => $request->class_room_id,
        ];

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = time() . '_' . $photo->getClientOriginalName();
            $photo->storeAs('user-photos', $filename, 'public');
            $data['photo'] = $filename;
        }

        $user = User::create($data);

        // Update class room walikelas if user is walikelas
        if ($request->has('is_walikelas') && $request->class_room_id) {
            $classRoom = ClassRoom::find($request->class_room_id);
            if ($classRoom) {
                // Check if class already has walikelas
                if ($classRoom->walikelas_id && $classRoom->walikelas_id != $user->id) {
                    return redirect()->back()
                        ->withErrors(['class_room_id' => 'Kelas ini sudah memiliki walikelas.'])
                        ->withInput();
                }
                
                // Remove user from other classes first
                ClassRoom::where('walikelas_id', $user->id)->update(['walikelas_id' => null]);
                
                // Assign to new class
                $classRoom->update(['walikelas_id' => $user->id]);
            }
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load(['role', 'classRoom.students']);
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('role_name')->get();
        $classRooms = ClassRoom::where('is_active', true)
            ->where(function($query) use ($user) {
                $query->whereNull('walikelas_id')
                      ->orWhere('walikelas_id', $user->id); // Include current user's class
            })
            ->orderBy('name')
            ->get();
        return view('admin.users.edit', compact('user', 'roles', 'classRooms'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nip_nik' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'address' => 'nullable|string',
            'status' => 'required|in:Aktif,Non-Aktif',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'is_walikelas' => 'nullable|boolean',
            'class_room_id' => 'nullable|exists:class_rooms,id',
        ]);

        $data = [
            'name' => $request->name,
            'nip_nik' => $request->nip_nik,
            'address' => $request->address,
            'status' => $request->status,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'is_walikelas' => $request->has('is_walikelas') ? true : false,
            'class_room_id' => $request->class_room_id,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = time() . '_' . $photo->getClientOriginalName();
            $photo->storeAs('user-photos', $filename, 'public');
            $data['photo'] = $filename;
        }

        $user->update($data);

        // Handle walikelas assignment
        if ($request->has('is_walikelas') && $request->class_room_id) {
            $classRoom = ClassRoom::find($request->class_room_id);
            if ($classRoom) {
                // Check if class already has walikelas (and it's not the current user)
                if ($classRoom->walikelas_id && $classRoom->walikelas_id != $user->id) {
                    return redirect()->back()
                        ->withErrors(['class_room_id' => 'Kelas ini sudah memiliki walikelas.'])
                        ->withInput();
                }
                
                // Remove user from other classes first
                ClassRoom::where('walikelas_id', $user->id)->update(['walikelas_id' => null]);
                
                // Assign to new class
                $classRoom->update(['walikelas_id' => $user->id]);
            }
        } else {
            // Remove user from all classes if not walikelas
            ClassRoom::where('walikelas_id', $user->id)->update(['walikelas_id' => null]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function import()
    {
        return view('admin.users.import');
    }

    public function downloadTemplate()
    {
        return Excel::download(new StaffTemplateExport, 'template_import_staff.xlsx');
    }

    public function previewImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:2048'
        ]);

        try {
            $import = new StaffImport(true); // Preview mode
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
            $import = new StaffImport(false); // Import mode
            Excel::import($import, $request->file('file'));
            
            $successCount = $import->getSuccessCount();
            $failureCount = $import->getFailureCount();
            $errors = $import->getErrors();
            
            $message = "Import selesai! Berhasil: {$successCount}, Gagal: {$failureCount}";
            
            if ($failureCount > 0) {
                $message .= "\n\nData yang gagal diimport:\n";
                foreach ($errors as $error) {
                    $message .= "Baris {$error['row']} - {$error['name']} ({$error['nip']}): {$error['error']}\n";
                }
            }
            
            return redirect()->route('admin.users.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }
}