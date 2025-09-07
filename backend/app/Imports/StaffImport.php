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

class StaffImport implements ToCollection, WithValidation, SkipsOnError, SkipsOnFailure, WithBatchInserts, WithChunkReading
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
            \Log::info('Collection received in preview mode, rows count: ' . $rows->count());
            \Log::info('Collection type: ' . get_class($rows));
            \Log::info('Collection empty: ' . ($rows->isEmpty() ? 'true' : 'false'));
            
            // Debug: Check if rows is actually empty or has data
            \Log::info('Collection toArray: ', ['data' => $rows->toArray()]);
            \Log::info('Collection keys: ', ['keys' => $rows->keys()->toArray()]);
            
            if ($rows->count() > 0) {
                \Log::info('First row sample: ', ['data' => $rows->first()->toArray()]);
                \Log::info('All rows data: ', ['data' => $rows->toArray()]);
            } else {
                \Log::info('No rows found in Excel file');
                \Log::info('This might be due to Excel file structure or format');
            }
            
            $this->previewData = $rows->toArray();
            \Log::info('Preview data set: ', ['count' => count($this->previewData)]);
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
        $role = Role::where('role_name', strtolower($row[3]))->first();
        if (!$role) {
            throw new \Exception("Peran '{$row[3]}' tidak ditemukan");
        }

        // Check for duplicate NIP/NIK
        if (User::where('nip_nik', $row[0])->exists()) {
            throw new \Exception("NIP/NIK '{$row[0]}' sudah ada");
        }

        // Check for duplicate email
        if (User::where('email', $row[2])->exists()) {
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
            'password' => Hash::make('SMPN14@2024'), // Default password
            'must_change_password' => true, // Force password change on first login
        ];

        // Handle photo if provided
        if (!empty($row[6])) { // Foto is seventh column
            $userData['photo'] = $row[6];
        }

        // Create user
        $user = User::create($userData);

        // Handle walikelas assignment
        if (strtolower($row[3]) === 'guru' && // Peran is fourth column
            strtolower($row[7]) === 'ya' && // Walikelas is eighth column
            !empty($row[8])) { // Kelas is ninth column
            
            $classRoom = ClassRoom::where('name', $row[8])->first();
            if ($classRoom) {
                // Check if class already has walikelas
                if ($classRoom->walikelas_id) {
                    throw new \Exception("Kelas '{$row[8]}' sudah memiliki walikelas");
                }
                
                // Assign user as walikelas
                $classRoom->update(['walikelas_id' => $user->id]);
            } else {
                throw new \Exception("Kelas '{$row[8]}' tidak ditemukan");
            }
        }
    }

    public function rules(): array
    {
        return [
            '0' => 'required|string|max:20', // NIP/NIK
            '1' => 'required|string|max:255', // Nama
            '2' => 'required|email|max:255', // Email
            '3' => 'required|string|in:Admin,Guru,Kepala Sekolah,Pegawai', // Peran
            '4' => 'required|string|in:Aktif,Non-Aktif', // Status
            '5' => 'nullable|string', // Alamat
            '6' => 'nullable|string', // Foto
            '7' => 'nullable|string|in:Ya,Tidak', // Walikelas
            '8' => 'nullable|string', // Kelas
        ];
    }

    public function customValidationMessages()
    {
        return [
            '0.required' => 'NIP/NIK wajib diisi',
            '1.required' => 'Nama wajib diisi',
            '2.required' => 'Email wajib diisi',
            '2.email' => 'Format email tidak valid',
            '3.required' => 'Peran wajib diisi',
            '3.in' => 'Peran harus Admin, Guru, Kepala Sekolah, atau Pegawai',
            '4.required' => 'Status wajib diisi',
            '4.in' => 'Status harus Aktif atau Non-Aktif',
            '7.in' => 'Walikelas harus Ya atau Tidak',
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