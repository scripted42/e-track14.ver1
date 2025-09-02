<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\ClassRoom;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Validators\ValidationException;
use Maatwebsite\Excel\Concerns\Importable;

class StudentsImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure, WithBatchInserts, WithChunkReading
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
                $this->createStudent($row);
                $this->successCount++;
            } catch (\Exception $e) {
                $this->failureCount++;
                $this->errors[] = [
                    'row' => $row->getIndex() + 2, // +2 because of header and 0-based index
                    'nisn' => $row['nisn'] ?? '',
                    'name' => $row['nama'] ?? '',
                    'error' => $e->getMessage()
                ];
            }
        }
    }

    private function createStudent($row)
    {
        // Skip empty rows
        if (empty($row['nisn']) && empty($row['nama']) && empty($row['kelas'])) {
            throw new \Exception("Data tidak lengkap");
        }

        // Check for duplicate NISN
        if (!empty($row['nisn']) && Student::where('nisn', $row['nisn'])->exists()) {
            throw new \Exception("NISN '{$row['nisn']}' sudah ada");
        }

        // Validate class exists in class_rooms table
        if (!empty($row['kelas'])) {
            $classExists = ClassRoom::where('name', $row['kelas'])->exists();
            if (!$classExists) {
                // If class doesn't exist in class_rooms, still allow but log warning
                // This maintains backward compatibility
            }
        }

        // Generate QR code if not provided
        $qrCode = $row['qr_code'] ?? 'STU' . time() . rand(1000, 9999);

        // Check for duplicate QR code
        if (Student::where('card_qr_code', $qrCode)->exists()) {
            $qrCode = 'STU' . time() . rand(1000, 9999);
        }

        Student::create([
            'nisn' => $row['nisn'] ?? null,
            'name' => $row['nama'],
            'class_name' => $row['kelas'],
            'address' => $row['alamat'] ?? null,
            'status' => $row['status'] ?? 'Aktif',
            'card_qr_code' => $qrCode,
        ]);
    }

    public function rules(): array
    {
        return [
            'nisn' => 'nullable|string|max:20|unique:students,nisn',
            'nama' => 'required|string|max:255',
            'kelas' => 'required|string|max:50',
            'alamat' => 'nullable|string|max:500',
            'status' => 'nullable|in:Aktif,Non-Aktif',
            'qr_code' => 'nullable|string|max:100|unique:students,card_qr_code',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama.required' => 'Nama siswa wajib diisi',
            'nama.max' => 'Nama siswa maksimal 255 karakter',
            'kelas.required' => 'Kelas wajib diisi',
            'kelas.max' => 'Kelas maksimal 50 karakter',
            'nisn.unique' => 'NISN sudah ada dalam database',
            'nisn.max' => 'NISN maksimal 20 karakter',
            'alamat.max' => 'Alamat maksimal 500 karakter',
            'status.in' => 'Status harus Aktif atau Non-Aktif',
            'qr_code.unique' => 'QR Code sudah ada dalam database',
            'qr_code.max' => 'QR Code maksimal 100 karakter',
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
                'nisn' => $failure->values()['nisn'] ?? '',
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
