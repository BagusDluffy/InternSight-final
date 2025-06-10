<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\JurusanImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\Jurusan;

class JurusanController extends Controller
{
    public function index()
    {
        $jurusan = Jurusan::all();
        return view('jurusan.index', compact('jurusan'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new JurusanImport, $request->file('file'));

            return redirect()->route('jurusan.index')
                ->with('success', 'Data Jurusan berhasil diimpor');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('jurusan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'jurusan' => 'required|string|max:10|unique:jurusan,jurusan',
            'deskripsi' => 'required|string|max:255',
        ]);

        Jurusan::create([
            'jurusan' => $request->jurusan,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('jurusan.index')->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function edit(Jurusan $jurusan)
    {
        return view('jurusan.edit', compact('jurusan'));
    }

    public function update(Request $request, Jurusan $jurusan)
    {
        $request->validate([
            'jurusan' => 'required|string|max:255|unique:jurusan,deskripsi,' . $jurusan->id,
            'deskripsi' => 'required|string|max:255' . $jurusan->id,
        ]);

        $jurusan->update([
            'jurusan' => $request->jurusan,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('jurusan.index')->with('success', 'Jurusan berhasil diperbarui.');
    }

    public function destroy(Jurusan $jurusan)
    {
        $jurusan->delete();
        return redirect()->route('jurusan.index')->with('success', 'Jurusan berhasil dihapus.');
    }

    public function multiDelete(Request $request)
    {
        $selectedIds = $request->input('selected_ids', []);

        if (empty($selectedIds)) {
            return redirect()->back()->with('error', 'Tidak ada jurusan yang dipilih untuk dihapus.');
        }

        Jurusan::whereIn('id', $selectedIds)->delete();

        return redirect()->route('jurusan.index')->with('success', 'Jurusan terpilih berhasil dihapus.');
    }

    public function deleteAll()
    {
        jurusan::truncate(); // Or jurusan::query()->delete() if you want to keep auto-increment reset

        return response()->json(['message' => 'Semua data jurusan berhasil dihapus']);
    }

    public function downloadTemplate()
    {
        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();

        // ================= SHEET 1: Data Jurusan =================
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Data Jurusan'); // Nama sheet

        // Set header kolom
        $sheet1->setCellValue('A1', 'jurusan');
        $sheet1->setCellValue('B1', 'deskripsi');

        // Contoh data
        $sheet1->setCellValue('A2', 'RPL 1');
        $sheet1->setCellValue('B2', 'Rekayasa Perangkat Lunak');

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ];
        $sheet1->getStyle('A1:B1')->applyFromArray($headerStyle);

        // Style contoh data
        $exampleStyle = ['font' => ['italic' => true, 'color' => ['rgb' => '808080']]];
        $sheet1->getStyle('A2:B2')->applyFromArray($exampleStyle);

        // Auto-size kolom
        foreach (range('A', 'B') as $column) {
            $sheet1->getColumnDimension($column)->setAutoSize(true);
        }

        // ================= SHEET 2: Petunjuk =================
        $sheet2 = $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1);
        $sheet2->setTitle('Petunjuk'); // Nama sheet

        // Isi petunjuk
        $sheet2->setCellValue('A1', 'Petunjuk Pengisian Template');
        $sheet2->setCellValue('A2', '1. Kolom "jurusan" wajib diisi dengan nama jurusan.');
        $sheet2->setCellValue('A3', '2. Kolom "deskripsi" berisi keterangan tentang jurusan.');
        $sheet2->setCellValue('A4', '3. Pastikan tidak ada duplikasi data pada kolom "jurusan".');
        $sheet2->setCellValue('A5', '4. Simpan file dalam format .xlsx sebelum mengunggah.');

        // Style judul petunjuk
        $noteStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FF0000'], 'size' => 14],
        ];
        $sheet2->getStyle('A1')->applyFromArray($noteStyle);

        // Auto-size kolom di sheet petunjuk
        $sheet2->getColumnDimension('A')->setAutoSize(true);

        // Set sheet pertama sebagai aktif saat file dibuka
        $spreadsheet->setActiveSheetIndex(0);

        // Buat file Excel
        $writer = new Xlsx($spreadsheet);
        $filename = 'template_data_jurusan.xlsx';

        // Set header untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Output file
        $writer->save('php://output');
        exit;
    }
}
