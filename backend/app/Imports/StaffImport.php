<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Role;
use App\Models\ClassRoom;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Validators\ValidationException;
use Maatwebsite\Excel\Concerns\Importable;

class StaffImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure, WithBatchInserts, WithChunkReading
{
    use Importable;

    private $errors = [];
    private $successCount = 0;
    private $failureCount = 0;
    private $previewMode = false;
    private $previewData = [];

    public function __construct($previewMode = false)
    {
        $this->previewMode = $previewMode;
    }

    public function collection(Collection $rows)
    {
        if ($this->previewMode) {
            $this->previewData = $rows->toArray();
            return;
        }

        foreach ($rows as $row) {
            try {
                $this->createUser($row);
                $this->successCount++;
            } catch (\Exception $e) {
                $this->failureCount++;
                $this->errors[] = [
                    'row' => $row->getIndex() + 2, // +2 because of header and 0-based index
                    'nip' => $row['nip_nik'] ?? '',
                    'name' => $row['nama'] ?? '',
                    'error' => $e->getMessage()
                ];
            }
        }
    }

    private function createUser($row)
    {
        // Get role
        $role = Role::where('role_name', strtolower($row['peran']))->first();
        if (!$role) {
            throw new \Exception("Peran '{$row['peran']}' tidak ditemukan");
        }

        // Check for duplicate NIP/NIK
        if (User::where('nip_nik', $row['nip_nik'])->exists()) {
            throw new \Exception("NIP/NIK '{$row['nip_nik']}' sudah ada");
        }

        // Check for duplicate email
        if (User::where('email', $row['email'])->exists()) {
            throw new \Exception("Email '{$row['email']}' sudah ada");
        }

        // Prepare user data
        $userData = [
            'nip_nik' => $row['nip_nik'],
            'name' => $row['nama'],
            'email' => $row['email'],
            'role_id' => $role->id,
            'status' => strtolower($row['status']) === 'aktif' ? 'aktif' : 'non-aktif',
            'address' => $row['alamat'] ?? null,
            'password' => Hash::make(env('DEFAULT_PASSWORD', 'ChangeMe123!')), // Default password from env
            'must_change_password' => true, // Force password change on first login
        ];

        // Handle photo if provided
        if (!empty($row['foto'])) {
            $userData['photo'] = $row['foto'];
        }

        // Create user
        $user = User::create($userData);

        // Handle walikelas assignment
        if (strtolower($row['peran']) === 'guru' && 
            strtolower($row['walikelas']) === 'ya' && 
            !empty($row['kelas'])) {
            
            $classRoom = ClassRoom::where('name', $row['kelas'])->first();
            if ($classRoom) {
                // Check if class already has walikelas
                if ($classRoom->walikelas_id) {
                    throw new \Exception("Kelas '{$row['kelas']}' sudah memiliki walikelas");
                }
                
                // Assign user as walikelas
                $classRoom->update(['walikelas_id' => $user->id]);
            } else {
                throw new \Exception("Kelas '{$row['kelas']}' tidak ditemukan");
            }
        }
    }

    public function rules(): array
    {
        return [
            'nip_nik' => 'required|string|max:20',
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'peran' => 'required|string|in:Admin,Guru,Kepala Sekolah',
            'status' => 'required|string|in:Aktif,Non-Aktif',
            'alamat' => 'nullable|string',
            'foto' => 'nullable|string',
            'walikelas' => 'nullable|string|in:Ya,Tidak',
            'kelas' => 'nullable|string',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nip_nik.required' => 'NIP/NIK wajib diisi',
            'nama.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'peran.required' => 'Peran wajib diisi',
            'peran.in' => 'Peran harus Admin, Guru, atau Kepala Sekolah',
            'status.required' => 'Status wajib diisi',
            'status.in' => 'Status harus Aktif atau Non-Aktif',
            'walikelas.in' => 'Walikelas harus Ya atau Tidak',
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function onError(\Throwable $e)
    {
        $this->failureCount++;
    }

    public function onFailure(\Maatwebsite\Excel\Validators\Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->failureCount++;
            $this->errors[] = [
                'row' => $failure->row(),
                'nip' => $failure->values()['nip_nik'] ?? '',
                'name' => $failure->values()['nama'] ?? '',
                'error' => implode(', ', $failure->errors())
            ];
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }

    public function getFailureCount()
    {
        return $this->failureCount;
    }

    public function getPreviewData()
    {
        return $this->previewData;
    }
}