<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StaffTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        return [
            [
                '1234567890',
                'Ahmad Fauzi',
                'ahmad@smpn14.sch.id',
                'Guru',
                'Aktif',
                'Jl. Contoh No. 1, Surabaya',
                'foto_ahmad.jpg',
                'Ya',
                '7A'
            ],
            [
                '1234567891',
                'Siti Nurhaliza',
                'siti@smpn14.sch.id',
                'Admin',
                'Aktif',
                'Jl. Contoh No. 2, Surabaya',
                '',
                'Tidak',
                ''
            ],
            [
                '1234567892',
                'Budi Santoso',
                'budi@smpn14.sch.id',
                'Kepala Sekolah',
                'Aktif',
                'Jl. Contoh No. 3, Surabaya',
                '',
                'Tidak',
                ''
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'NIP/NIK',
            'Nama',
            'Email',
            'Peran',
            'Status',
            'Alamat',
            'Foto',
            'Walikelas',
            'Kelas'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ]
            ],
            // Data rows styling
            'A2:I4' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F2F2F2']
                ]
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // NIP/NIK
            'B' => 25, // Nama
            'C' => 30, // Email
            'D' => 15, // Peran
            'E' => 10, // Status
            'F' => 35, // Alamat
            'G' => 15, // Foto
            'H' => 12, // Walikelas
            'I' => 10, // Kelas
        ];
    }
}