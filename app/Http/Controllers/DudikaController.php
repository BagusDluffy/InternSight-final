<?php

namespace App\Http\Controllers;

use App\Models\Dudika;
use App\Imports\DudikaImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DudikaController extends Controller
{
    public function index()
    {
        $dudika = Dudika::all();
        return view('dudika.index', compact('dudika'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new DudikaImport, $request->file('file'));

            return redirect()->route('dudika.index')
                ->with('success', 'Data DUDIKA berhasil diimpor');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('dudika.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'dudika' => 'required',
            'alamat' => 'required',
            'kontak' => 'nullable|string', // Validasi opsional
        ]);

        Dudika::create($request->all());
        return redirect()->route('dudika.index')->with('success', 'Data DUDIKA berhasil ditambahkan');
    }

    public function edit(Dudika $dudika)
    {
        return view('dudika.edit', compact('dudika'));
    }

    public function update(Request $request, Dudika $dudika)
    {
        $request->validate([
            'dudika' => 'required',
            'alamat' => 'required',
            'kontak' => 'nullable|string', // Validasi opsional
        ]);

        $dudika->update($request->all());
        return redirect()->route('dudika.index')->with('success', 'Data DUDIKA berhasil diperbarui');
    }

    public function destroy(Dudika $dudika)
    {
        $dudika->delete();
        return redirect()->route('dudika.index')->with('success', 'Data DUDIKA berhasil dihapus');
    }

    public function multiDelete(Request $request)
    {
        $selectedIds = $request->input('selected_ids', []);

        if (empty($selectedIds)) {
            return redirect()->back()->with('error', 'Tidak ada dudika yang dipilih untuk dihapus.');
        }

        Dudika::whereIn('id', $selectedIds)->delete();

        return redirect()->route('dudika.index')->with('success', 'Dudika terpilih berhasil dihapus.');
    }

    public function deleteAll()
    {
        Dudika::truncate(); // Or dudika::query()->delete() if you want to keep auto-increment reset

        return response()->json(['message' => 'Semua data dudika berhasil dihapus']);
    }

    public function downloadTemplate()
    {
        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();

        /** SHEET 1: DATA DUDIKA **/
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Data Dudika'); // Nama sheet pertama

        // Set header kolom
        $sheet1->setCellValue('A1', 'dudika');
        $sheet1->setCellValue('B1', 'alamat');
        $sheet1->setCellValue('C1', 'kontak');

        // Contoh data
        $sheet1->setCellValue('A2', 'DPTSI - ITS');
        $sheet1->setCellValue('B2', 'Kampus Sukolilo, Gedung Pusat Riset Lantai 4, Jl. Teknik Kimia, Keputih, Sukolilo, Surabaya, Jawa Timur 60117');
        $sheet1->setCellValue('C2', '0856301921');

        // Style header
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet1->getStyle('A1:C1')->applyFromArray($headerStyle);

        // Style contoh data
        $exampleStyle = [
            'font' => [
                'italic' => true,
                'color' => ['rgb' => '808080'],
            ],
        ];
        $sheet1->getStyle('A2:C2')->applyFromArray($exampleStyle);

        // Auto-size kolom
        foreach (range('A', 'C') as $column) {
            $sheet1->getColumnDimension($column)->setAutoSize(true);
        }

        /** SHEET 2: PETUNJUK **/
        $spreadsheet->createSheet(); // Tambah sheet kedua
        $sheet2 = $spreadsheet->setActiveSheetIndex(1); // Pilih sheet kedua
        $sheet2->setTitle('Petunjuk'); // Nama sheet kedua

        // Isi petunjuk
        $sheet2->setCellValue('A1', 'Petunjuk Pengisian Data');
        $sheet2->setCellValue('A2', '- Semua Kolom Wajib diisi');
        $sheet2->setCellValue('A3', '- Kolom "dudika" diisi dengan nama dudika tempat magang');
        $sheet2->setCellValue('A4', '- Ubah kolom "kontak" menjadi teks (wajib)');

        // Style petunjuk judul
        $noteStyle = [
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FF0000'],
            ],
        ];
        $sheet2->getStyle('A1')->applyFromArray($noteStyle);

        // Auto-size kolom
        $sheet2->getColumnDimension('A')->setAutoSize(true);

        // Kembali ke sheet pertama sebelum download
        $spreadsheet->setActiveSheetIndex(0);

        // Buat file Excel
        $writer = new Xlsx($spreadsheet);

        // Set header untuk download
        $filename = 'template_data_dudika.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Output file
        $writer->save('php://output');
        exit;
    }
}
