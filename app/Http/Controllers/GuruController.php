<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use App\Imports\GuruImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GuruController extends Controller
{
    public function index()
    {
        $guru = Guru::all();
        return view('guru.index', compact('guru'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            $file = $request->file('file');

            Excel::import(new GuruImport, $file);

            Log::info('Berhasil mengimpor data guru');

            return redirect()->route('guru.index')
                ->with('success', 'Data guru berhasil diimpor');
        } catch (\Exception $e) {
            Log::error('Gagal impor guru: ' . $e->getMessage());

            return back()
                ->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('guru.create');
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nama_guru' => 'required|string|max:255',
                'email' => 'required|email|unique:guru,email',
                'password' => 'required|string|min:6',
                'nip' => 'nullable|string|max:50',
                'no_hp' => 'nullable|string|max:15',
            ]);

            $plainPassword = $validatedData['password'];

            $guru = Guru::create([
                'nama_guru' => $validatedData['nama_guru'],
                'email' => $validatedData['email'],
                'password' => Hash::make($plainPassword),
                'encrypted_password' => Crypt::encryptString($plainPassword), // Simpan encrypted password
                'nip' => $validatedData['nip'],
                'no_hp' => $validatedData['no_hp'],
            ]);

            Log::info('Guru berhasil ditambahkan: ' . $guru->id);

            return redirect()->route('guru.index')
                ->with('success', 'Guru berhasil ditambahkan.')
                ->with('password_plain', $plainPassword);
        } catch (ValidationException $e) {
            Log::error('Validasi gagal: ' . $e->getMessage());
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('Gagal menambahkan guru: ' . $e->getMessage());
            return back()->with('error', 'Gagal menambahkan guru');
        }
    }

    public function edit(Guru $guru)
    {
        return view('guru.edit', compact('guru'));
    }

    public function update(Request $request, Guru $guru)
    {
        try {
            $validatedData = $request->validate([
                'nama_guru' => 'required|string|max:255',
                'email' => 'required|email|unique:guru,email,' . $guru->id,
                'password' => 'nullable|min:6',
                'nip' => 'nullable|string|max:50', // Validasi NIP
                'no_hp' => 'nullable|string|max:15', // Validasi No HP
            ]);

            // Update data yang tidak termasuk password
            $guru->nama_guru = $validatedData['nama_guru'];
            $guru->email = $validatedData['email'];
            $guru->nip = $validatedData['nip'] ?? null; // Update NIP
            $guru->no_hp = $validatedData['no_hp'] ?? null; // Update No HP

            // Update password jika diisi
            if ($request->filled('password')) {
                $plainPassword = $request->password;
                $guru->password = Hash::make($plainPassword);
                $guru->encrypted_password = Crypt::encryptString($plainPassword);
            }

            $guru->save();

            Log::info('Guru berhasil diupdate: ' . $guru->id);

            return redirect()->route('guru.index')
                ->with('success', 'Data guru berhasil diperbarui.');
        } catch (ValidationException $e) {
            Log::error('Validasi update gagal: ' . $e->getMessage());
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('Gagal update guru: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui data guru');
        }
    }

    public function destroy(Guru $guru)
    {
        $guru->delete();
        return redirect()->route('guru.index')->with('success', 'Guru berhasil dihapus.');
    }

    public function multiDelete(Request $request)
    {
        $selectedIds = $request->input('selected_ids', []);
        
        if (empty($selectedIds)) {
            return redirect()->back()->with('error', 'Tidak ada guru yang dipilih untuk dihapus.');
        }
    
        Guru::whereIn('id', $selectedIds)->delete();
    
        return redirect()->route('guru.index')->with('success', 'Guru terpilih berhasil dihapus.');
    }
    
    public function deleteAll()
    {
        Guru::truncate(); // Or Guru::query()->delete() if you want to keep auto-increment reset
    
        return response()->json(['message' => 'Semua data guru berhasil dihapus']);
    }

    public function downloadTemplate()
    {
        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();

        // ==============================
        // SHEET 1: TEMPLATE DATA GURU
        // ==============================
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Data Guru');

        // Set header kolom
        $sheet1->setCellValue('A1', 'nama_guru');
        $sheet1->setCellValue('B1', 'email');
        $sheet1->setCellValue('C1', 'password');
        $sheet1->setCellValue('D1', 'nip');
        $sheet1->setCellValue('E1', 'no_hp');

        // Contoh data
        $sheet1->setCellValue('A2', 'Budi Santoso');
        $sheet1->setCellValue('B2', 'budi@example.com');
        $sheet1->setCellValue('C2', 'password123'); // Opsional
        $sheet1->setCellValue('D2', '198501232010011002'); // Opsional
        $sheet1->setCellValue('E2', '08123456789'); // Opsional

        // Style untuk header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ];
        $sheet1->getStyle('A1:E1')->applyFromArray($headerStyle);

        // Style untuk contoh data
        $exampleStyle = ['font' => ['italic' => true, 'color' => ['rgb' => '808080']]];
        $sheet1->getStyle('A2:E2')->applyFromArray($exampleStyle);

        // Auto-size kolom
        foreach (range('A', 'E') as $column) {
            $sheet1->getColumnDimension($column)->setAutoSize(true);
        }

        // ==============================
        // SHEET 2: PETUNJUK
        // ==============================
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Petunjuk');

        // Judul Petunjuk
        $sheet2->setCellValue('A1', 'Petunjuk Pengisian Template Data Guru');
        $sheet2->mergeCells('A1:D1');

        // Style untuk Judul
        $titleStyle = [
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '0000FF']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ];
        $sheet2->getStyle('A1')->applyFromArray($titleStyle);

        // Isi Petunjuk
        $instructions = [
            'A3' => '- Kolom nama_guru, email, dan password wajib diisi',
            'A4' => '- Kolom nip dan no_hp opsional',
            'A5' => '- Pastikan format email valid',
            'A6' => '- Gunakan format teks untuk kolom nip dan no_hp agar angka tidak berubah',
        ];

        // Menulis Petunjuk
        foreach ($instructions as $cell => $text) {
            $sheet2->setCellValue($cell, $text);
        }

        // Warna untuk petunjuk
        $noteStyle = ['font' => ['color' => ['rgb' => 'FF0000']]];
        $sheet2->getStyle('A3:A6')->applyFromArray($noteStyle);

        // Auto-size kolom
        $sheet2->getColumnDimension('A')->setAutoSize(true);

        // ==============================
        // GENERATE FILE EXCEL
        // ==============================
        $writer = new Xlsx($spreadsheet);
        $filename = 'template_data_guru.xlsx';

        // Set header untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Output file
        $writer->save('php://output');
        exit;
    }
}
