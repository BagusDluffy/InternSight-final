<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\MuridImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use App\Models\Murid;
use App\Models\Jurusan;
use Maatwebsite\Excel\Validators\ValidationException;

class MuridController extends Controller
{
    public function index()
    {
        $murid = Murid::with('jurusan')->get();
        return view('murid.index', compact('murid'));
    }

    public function import(Request $request)
    {
        $file = $request->file('file');

        // Ambil daftar sheet
        $sheets = Excel::toArray(new MuridImport(), $file);
        $sheetNames = array_keys($sheets);

        // Prioritaskan sheet bernama "Data Murid", kalau tidak ada pakai sheet pertama
        $selectedSheet = in_array('Data Murid', $sheetNames) ? 'Data Murid' : 0;

        Excel::import(new MuridImport($selectedSheet), $file);

        return redirect()->back()->with('success', 'Data murid berhasil diimpor!');
    }

    public function create()
    {
        $jurusan = Jurusan::all();  // Ambil semua data jurusan
        $kelas = ['XI', 'XII'];  // Contoh data kelas
        return view('murid.create', compact('kelas', 'jurusan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_murid' => 'required|string|max:255',
            'nis' => 'required|string|unique:murid,nis|max:255',
            'kelas' => 'required|string',
            'jurusan_id' => 'required|exists:jurusan,id',  // Kolom jurusan sekarang berupa nama jurusan
        ]);

        Murid::create([
            'nama_murid' => $request->nama_murid,
            'nis' => $request->nis,
            'kelas' => $request->kelas,
            'jurusan_id' => $request->jurusan_id, // Menyimpan nama jurusan
        ]);

        return redirect()->route('murid.index')->with('success', 'Murid berhasil ditambahkan.');
    }

    public function edit(Murid $murid)
    {
        $jurusan = Jurusan::all();  // Ambil semua data jurusan
        $kelas = ['XI', 'XII'];  // Contoh data kelas
        return view('murid.edit', compact('murid', 'kelas', 'jurusan'));
    }

    public function update(Request $request, Murid $murid)
    {
        $request->validate([
            'nama_murid' => 'required|string|max:255',
            'nis' => 'required|string|unique:murid,nis,' . $murid->id . '|max:255',
            'kelas' => 'required|string',
            'jurusan_id' => 'required|exists:jurusan,id',
        ]);

        $murid->update([
            'nama_murid' => $request->nama_murid,
            'nis' => $request->nis,
            'kelas' => $request->kelas,
            'jurusan_id' => $request->jurusan_id,
        ]);

        return redirect()->route('murid.index')->with('success', 'Murid berhasil diperbarui.');
    }

    public function destroy(Murid $murid)
    {
        $murid->delete();
        return redirect()->route('murid.index')->with('success', 'Murid berhasil dihapus.');
    }

    public function multiDelete(Request $request)
    {
        $selectedIds = $request->input('selected_ids', []);

        if (empty($selectedIds)) {
            return redirect()->back()->with('error', 'Tidak ada murid yang dipilih untuk dihapus.');
        }

        Murid::whereIn('id', $selectedIds)->delete();

        return redirect()->route('murid.index')->with('success', 'Murid terpilih berhasil dihapus.');
    }

    public function deleteAll()
    {
        Murid::truncate(); // Or muirid::query()->delete() if you want to keep auto-increment reset

        return response()->json(['message' => 'Semua data murid berhasil dihapus']);
    }

    public function downloadTemplate()
    {
        // Ambil semua data jurusan untuk dropdown referensi
        $jurusanList = Jurusan::select('id', 'jurusan')->get();
    
        // Buat spreadsheet baru
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    
        // Rename sheet utama
        $mainSheet = $spreadsheet->getActiveSheet();
        $mainSheet->setTitle('Data Murid');
    
        // Set header kolom sesuai dengan skema murid
        $mainSheet->setCellValue('A1', 'nama_murid');
        $mainSheet->setCellValue('B1', 'nis');
        $mainSheet->setCellValue('C1', 'kelas');
        $mainSheet->setCellValue('D1', 'jurusan_id');
    
        // Buat contoh data
        $mainSheet->setCellValue('A2', 'John Doe');
        $mainSheet->getCell('B2')->setValueExplicit('123456789', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $mainSheet->setCellValue('C2', 'X');
        $mainSheet->setCellValue('D2', '1');
    
        // Style untuk header
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
    
        // Terapkan style header
        $mainSheet->getStyle('A1:D1')->applyFromArray($headerStyle);
    
        // Validasi untuk kolom jurusan_id
        $validationJurusan = $mainSheet->getCell('D2')->getDataValidation();
        $validationJurusan->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $validationJurusan->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $validationJurusan->setAllowBlank(false);
        $validationJurusan->setShowInputMessage(true);
        $validationJurusan->setShowErrorMessage(true);
        $validationJurusan->setShowDropDown(true);
    
        // Buat string untuk validasi jurusan
        $jurusanOptions = $jurusanList->pluck('id')->implode(',');
        $validationJurusan->setFormula1('"' . $jurusanOptions . '"');
        $validationJurusan->setErrorTitle('Input error');
        $validationJurusan->setError('ID jurusan tidak valid.');
        $validationJurusan->setPromptTitle('Pilih ID jurusan');
        $validationJurusan->setPrompt('Pilih dari daftar ID jurusan yang tersedia');
    
        // Validasi untuk kolom kelas
        $validationKelas = $mainSheet->getCell('C2')->getDataValidation();
        $validationKelas->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $validationKelas->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $validationKelas->setAllowBlank(false);
        $validationKelas->setShowInputMessage(true);
        $validationKelas->setShowErrorMessage(true);
        $validationKelas->setShowDropDown(true);
        $validationKelas->setFormula1('"X,XI,XII"');
    
        // Terapkan validasi ke range
        $mainSheet->setDataValidation('C2:C1000', $validationKelas);
        $mainSheet->setDataValidation('D2:D1000', $validationJurusan);
    
        // Atur format kolom NIS sebagai teks
        $mainSheet->getStyle('B:B')->getNumberFormat()->setFormatCode('@');
    
        // Auto-size kolom
        foreach (range('A', 'D') as $column) {
            $mainSheet->getColumnDimension($column)->setAutoSize(true);
        }
    
        // Memberikan background warna ke contoh data
        $mainSheet->getStyle('A2:D2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $mainSheet->getStyle('A2:D2')->getFill()->getStartColor()->setRGB('F2F2F2');
    
        // Tambahkan label di atas tabel
        $mainSheet->setCellValue('A4', '*Hapus baris contoh ini dan mulai input data Anda dari baris ke-2');
        $mainSheet->getStyle('A4')->getFont()->setBold(true);
        $mainSheet->getStyle('A4')->getFont()->setItalic(true);
        $mainSheet->getStyle('A4')->getFont()->getColor()->setRGB('FF0000');
    
        // Buat sheet kedua untuk panduan dan referensi
        $guideSheet = $spreadsheet->createSheet();
        $guideSheet->setTitle('Panduan & Referensi');
    
        // Judul panduan
        $guideSheet->setCellValue('A1', 'PANDUAN PENGISIAN TEMPLATE');
        $guideSheet->getStyle('A1')->getFont()->setBold(true);
        $guideSheet->getStyle('A1')->getFont()->setSize(14);
    
        // Catatan panduan
        $guideSheet->setCellValue('A3', 'Catatan Penting:');
        $guideSheet->setCellValue('A4', '1. Semua kolom wajib diisi');
        $guideSheet->setCellValue('A5', '2. NIS harus unik dan berupa teks (jika diawali dengan angka 0, gunakan format teks)');
        $guideSheet->setCellValue('A6', '3. Kelas harus berupa X, XI, atau XII');
        $guideSheet->setCellValue('A7', '4. jurusan_id harus sesuai dengan ID jurusan yang tersedia (lihat tabel referensi di bawah)');
        $guideSheet->setCellValue('A8', '5. JANGAN mengubah baris header (baris 1)');
        $guideSheet->setCellValue('A9', '6. Hapus baris contoh (baris 2) sebelum import data sebenarnya');
    
        // Judul referensi
        $guideSheet->setCellValue('A12', 'REFERENSI JURUSAN');
        $guideSheet->getStyle('A12')->getFont()->setBold(true);
    
        // Header tabel referensi
        $guideSheet->setCellValue('A13', 'ID');
        $guideSheet->setCellValue('B13', 'Nama Jurusan');
        $guideSheet->getStyle('A13:B13')->applyFromArray($headerStyle);
    
        // Isi tabel referensi
        $row = 14;
        foreach ($jurusanList as $jurusan) {
            $guideSheet->setCellValue('A' . $row, $jurusan->id);
            $guideSheet->setCellValue('B' . $row, $jurusan->jurusan);
            $row++;
        }
    
        // Format tabel referensi
        $guideSheet->getStyle('A13:B' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    
        // Auto-size kolom di sheet panduan
        foreach (range('A', 'B') as $column) {
            $guideSheet->getColumnDimension($column)->setAutoSize(true);
        }
    
        // Aktifkan sheet utama sebagai default
        $spreadsheet->setActiveSheetIndex(0);
    
        // Buat file Excel
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    
        // Set header untuk download
        $filename = 'template_data_murid.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
    
        // Output file
        $writer->save('php://output');
        exit;
    }
}
