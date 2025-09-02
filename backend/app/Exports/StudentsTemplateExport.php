<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        // Return example data for template
        return [
            [
                'NISN001',
                'Ahmad Fauzi',
                '7A',
                'Jl. Contoh No. 123',
                'Aktif',
                'STU123456789'
            ],
            [
                'NISN002',
                'Siti Nurhaliza',
                '7B',
                'Jl. Contoh No. 456',
                'Aktif',
                'STU987654321'
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'NISN',
            'Nama',
            'Kelas',
            'Alamat',
            'Status',
            'QR Code'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // NISN
            'B' => 25, // Nama
            'C' => 10, // Kelas
            'D' => 30, // Alamat
            'E' => 12, // Status
            'F' => 20, // QR Code
        ];
    }
}
