<?php

namespace App\Exports;

use App\Models\Magang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class MagangExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    public function collection()
    {
        return Magang::with(['murid', 'jurusan', 'dudika', 'guru'])
            ->get()
            ->map(function ($magang) {
                return [
                    'Kelas' => $magang->kelas,
                    'Jurusan' => $magang->jurusan->jurusan,
                    'DUDIKA' => $magang->dudika->dudika,
                    'Guru Pembimbing' => $magang->guru->nama_guru,
                    'Nama Murid' => $magang->murid->pluck('nama_murid')->implode(', '),
                    'Periode' => $magang->periode, // Added periode column
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Kelas',
            'Jurusan',
            'DUDIKA',
            'Guru Pembimbing',
            'Nama Murid',
            'Periode', // Added periode column
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => Color::COLOR_WHITE],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF4CAF50'], // Green background
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);

        // Row styling
        $sheet->getStyle('A2:F' . ($sheet->getHighestRow()))->applyFromArray([
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ]);

        // Set row height
        $sheet->getRowDimension('1')->setRowHeight(30);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Kelas
            'B' => 20, // Jurusan
            'C' => 25, // DUDIKA
            'D' => 25, // Guru Pembimbing
            'E' => 30, // Nama Murid
            'F' => 20, // Periode
        ];
    }
}