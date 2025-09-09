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
    /**
     * Show current user's profile (accessible by all roles)
     */
    public function profile(Request $request)
    {
        $user = $request->user()->load(['role', 'classRoom.students']);
        // Reuse existing detailed view
        return view('admin.users.show', compact('user'));
    }

    /**
     * Update basic profile fields for current user
     */
    public function profileUpdate(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $update = [
            'name' => $request->name,
            'address' => $request->address,
        ];
        if ($request->filled('password')) {
            $update['password'] = Hash::make($request->password);
        }

        $user->update($update);

        return redirect()->route('admin.profile')->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update profile photo for current user
     */
    public function profilePhoto(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $photo = $request->file('photo');
        $ext = strtolower($photo->getClientOriginalExtension());
        $safeName = preg_replace('/[^a-z0-9\-_.]+/i', '_', pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME));
        $filename = time() . '_' . $safeName . '.' . $ext;
        $photo->storeAs('user-photos', $filename, 'public');
        $user->update(['photo' => $filename]);

        return redirect()->route('admin.profile')->with('success', 'Foto profil diperbarui.');
    }
    public function index(Request $request)
    {
        // Build base query for filtering
        $query = User::with('role');
        
        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Apply role filter
        if ($request->filled('role')) {
            $query->whereHas('role', function($q) use ($request) {
                $q->where('role_name', $request->role);
            });
        }
        
        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Apply sorting
        $sortBy = $request->get('sort_by', 'name');
        switch ($sortBy) {
            case 'email':
                $query->orderBy('email');
                break;
            case 'role':
                $query->orderBy('role_id');
                break;
            case 'created_at':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('name');
                break;
        }
        
        // Get paginated results
        $users = $query->paginate(15);
        
        // Calculate summary statistics from ALL users (not just paginated)
        $allUsers = User::with('role')->get();
        $stats = [
            'total_users' => $allUsers->count(),
            'admin_count' => $allUsers->where('role.role_name', 'Admin')->count(),
            'kepala_sekolah_count' => $allUsers->where('role.role_name', 'Kepala Sekolah')->count(),
            'waka_kurikulum_count' => $allUsers->where('role.role_name', 'Waka Kurikulum')->count(),
            'teacher_count' => $allUsers->where('role.role_name', 'Guru')->count(),
            'employee_count' => $allUsers->where('role.role_name', 'Pegawai')->count(),
        ];
        
        return view('admin.users.index', compact('users', 'stats'));
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
        // Assign Spatie role name based on selected role_id
        $spatieRole = \Spatie\Permission\Models\Role::where('name', optional(\App\Models\Role::find($request->role_id))->role_name)->first();
        if ($spatieRole) {
            $user->syncRoles([$spatieRole->name]);
        }

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
        // Sync Spatie role if changed
        $newRoleName = optional(\App\Models\Role::find($request->role_id))->role_name;
        if ($newRoleName) {
            $user->syncRoles([$newRoleName]);
        }

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
            \Log::info('Starting preview import...');
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
            $validationResults = $this->validatePreviewData($previewData);
            
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
            \Log::info('Starting process import...');
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
                    $this->createUserFromRow($row);
                    $successCount++;
                    \Log::info('Row ' . ($index + 1) . ' processed successfully');
                } catch (\Exception $e) {
                    $failureCount++;
                    $errors[] = [
                        'row' => $index + 1,
                        'name' => $row[1] ?? 'Unknown',
                        'nip' => $row[0] ?? 'Unknown',
                        'error' => $e->getMessage()
                    ];
                    \Log::error('Row ' . ($index + 1) . ' failed: ' . $e->getMessage());
                }
            }
            
            $message = "Import selesai! Berhasil: {$successCount}, Gagal: {$failureCount}";
            
            if ($failureCount > 0) {
                $message .= "\n\nData yang gagal diimport:\n";
                foreach ($errors as $error) {
                    $message .= "Baris {$error['row']} - {$error['name']} ({$error['nip']}): {$error['error']}\n";
                }
            }
            
            \Log::info('Import completed. Success: ' . $successCount . ', Failure: ' . $failureCount);
            
            return redirect()->route('admin.users.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            \Log::error('Process import error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }

    private function createUserFromRow($row)
    {
        \Log::info('Creating user from row: ', ['data' => $row]);
        
        // Get role with mapping for common mistakes
        $roleName = strtolower($row[3]);
        
        // Map common role name mistakes
        $roleMapping = [
            'petugas' => 'pegawai',
            'staff' => 'pegawai',
            'karyawan' => 'pegawai',
            'employee' => 'pegawai'
        ];
        
        if (isset($roleMapping[$roleName])) {
            $roleName = $roleMapping[$roleName];
            \Log::info('Role mapped from ' . $row[3] . ' to ' . $roleName);
        }
        
        $role = \App\Models\Role::where('role_name', $roleName)->first();
        if (!$role) {
            throw new \Exception("Peran '{$row[3]}' tidak ditemukan. Peran yang tersedia: Admin, Guru, Pegawai, Siswa, Waka Kurikulum, Kepala Sekolah");
        }

        // Check for duplicate NIP/NIK
        if (\App\Models\User::where('nip_nik', $row[0])->exists()) {
            throw new \Exception("NIP/NIK '{$row[0]}' sudah ada");
        }

        // Check for duplicate email
        if (\App\Models\User::where('email', $row[2])->exists()) {
            throw new \Exception("Email '{$row[2]}' sudah ada");
        }

        // Prepare user data
        $userData = [
            'nip_nik' => $row[0], // NIP/NIK is first column
            'name' => $row[1], // Nama is second column
            'email' => $row[2], // Email is third column
            'role_id' => $role->id,
            'status' => strtolower($row[4]) === 'aktif' ? 'aktif' : 'non-aktif', // Status is fifth column
            'address' => $row[5] ?? null, // Alamat is sixth column
            'password' => \Illuminate\Support\Facades\Hash::make(env('DEFAULT_PASSWORD', 'ChangeMe123!')), // Default password from env
            'must_change_password' => true, // Force password change on first login
        ];

        // Handle photo if provided
        if (!empty($row[6])) { // Foto is seventh column
            $userData['photo'] = $row[6];
        }

        // Create user
        $user = \App\Models\User::create($userData);
        \Log::info('User created successfully: ', ['id' => $user->id, 'name' => $user->name]);

        // Handle walikelas assignment
        if (strtolower($row[3]) === 'guru' && // Peran is fourth column
            strtolower($row[7]) === 'ya' && // Walikelas is eighth column
            !empty($row[8])) { // Kelas is ninth column
            
            $classRoom = \App\Models\ClassRoom::where('name', $row[8])->first();
            if ($classRoom) {
                // Check if class already has walikelas
                if ($classRoom->walikelas_id) {
                    throw new \Exception("Kelas '{$row[8]}' sudah memiliki walikelas");
                }
                
                // Assign user as walikelas
                $classRoom->update(['walikelas_id' => $user->id]);
                \Log::info('User assigned as walikelas for class: ' . $row[8]);
            } else {
                throw new \Exception("Kelas '{$row[8]}' tidak ditemukan");
            }
        }
    }

    private function validatePreviewData($data)
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
            if (count($row) < 4) {
                $errors[] = "Data tidak lengkap (minimal 4 kolom)";
                $validationResults['invalid'][] = [
                    'row' => $rowNumber,
                    'data' => $row,
                    'errors' => $errors,
                    'warnings' => $warnings
                ];
                $validationResults['summary']['invalid_count']++;
                continue;
            }

            $nipNik = $row[0] ?? '';
            $name = $row[1] ?? '';
            $email = $row[2] ?? '';
            $role = $row[3] ?? '';

            // Validate NIP/NIK
            if (empty($nipNik)) {
                $errors[] = "NIP/NIK tidak boleh kosong";
            } else {
                // Check for duplicate NIP/NIK in database
                $existingUser = \App\Models\User::where('nip_nik', $nipNik)->first();
                if ($existingUser) {
                    $warnings[] = "NIP/NIK '{$nipNik}' sudah ada di database (User: {$existingUser->name})";
                    $validationResults['duplicates'][] = [
                        'row' => $rowNumber,
                        'data' => $row,
                        'type' => 'nip_nik',
                        'existing_user' => $existingUser->name,
                        'existing_email' => $existingUser->email
                    ];
                }
            }

            // Validate Name
            if (empty($name)) {
                $errors[] = "Nama tidak boleh kosong";
            }

            // Validate Email
            if (empty($email)) {
                $errors[] = "Email tidak boleh kosong";
            } else {
                // Check for duplicate email in database
                $existingUser = \App\Models\User::where('email', $email)->first();
                if ($existingUser) {
                    $warnings[] = "Email '{$email}' sudah ada di database (User: {$existingUser->name})";
                    $validationResults['duplicates'][] = [
                        'row' => $rowNumber,
                        'data' => $row,
                        'type' => 'email',
                        'existing_user' => $existingUser->name,
                        'existing_nip' => $existingUser->nip_nik
                    ];
                }
            }

            // Validate Role
            if (empty($role)) {
                $errors[] = "Peran tidak boleh kosong";
            } else {
                // Map common role name mistakes
                $roleName = strtolower($role);
                $roleMapping = [
                    'petugas' => 'pegawai',
                    'staff' => 'pegawai',
                    'karyawan' => 'pegawai',
                    'employee' => 'pegawai'
                ];
                
                if (isset($roleMapping[$roleName])) {
                    $roleName = $roleMapping[$roleName];
                }
                
                $existingRole = \App\Models\Role::where('role_name', $roleName)->first();
                if (!$existingRole) {
                    $errors[] = "Peran '{$role}' tidak ditemukan. Peran yang tersedia: Admin, Guru, Pegawai, Siswa, Waka Kurikulum, Kepala Sekolah";
                }
            }

            // Check for duplicates within the same file
            foreach ($data as $otherIndex => $otherRow) {
                if ($index !== $otherIndex && count($otherRow) >= 4) {
                    if (!empty($nipNik) && !empty($otherRow[0]) && $nipNik === $otherRow[0]) {
                        $warnings[] = "NIP/NIK '{$nipNik}' duplikat dalam file yang sama (Baris " . ($otherIndex + 1) . ")";
                    }
                    if (!empty($email) && !empty($otherRow[2]) && $email === $otherRow[2]) {
                        $warnings[] = "Email '{$email}' duplikat dalam file yang sama (Baris " . ($otherIndex + 1) . ")";
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
}