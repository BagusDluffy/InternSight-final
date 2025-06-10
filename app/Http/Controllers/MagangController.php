<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Imports\MagangImport;
use App\Exports\MagangExport;
use App\Models\Magang;
use App\Models\Jurusan;
use App\Models\Murid;
use App\Models\Dudika;
use App\Models\Guru;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MagangController extends Controller
{
    public function index()
    {
        $magang = Magang::with('jurusan', 'murid', 'dudika', 'guru')->paginate(10); // Gunakan pagination untuk menghindari query berat
        return view('magang.index', compact('magang'));
    }

    public function create()
    {
        $jurusan = Jurusan::all();
        $kelas = Murid::select('kelas')->distinct()->orderBy('kelas')->get();
        $dudika = Dudika::all();
        $guru = Guru::all();
        $murid = Murid::all();

        return view('magang.create', compact('jurusan', 'kelas', 'dudika', 'guru', 'murid'));
    }

    public function getMurid(Request $request)
    {
        $jurusanId = $request->get('jurusan_id');
        $kelas = $request->get('kelas');

        Log::info('Jurusan ID: ' . $jurusanId);
        Log::info('Kelas: ' . $kelas);

        if (!$jurusanId || !$kelas) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jurusan dan kelas wajib dipilih.',
                'data' => [],
            ], 400);
        }

        $murid = Murid::where('jurusan_id', $jurusanId)
            ->where('kelas', $kelas)
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Data murid berhasil diambil.',
            'data' => $murid,
        ]);
    }

    public function store(Request $request)
    {
        Log::info('Received Data:', $request->all());

        try {
            Log::info('Raw Dates:', [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date
            ]);

            // Konversi tanggal dari bahasa Indonesia ke bahasa Inggris
            try {
                $startDate = $this->convertIndonesianDateToEnglish($request->start_date);
                $endDate = $this->convertIndonesianDateToEnglish($request->end_date);

                Log::info('Formatted Dates:', [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]);
            } catch (\Exception $e) {
                Log::error('Invalid date format: ' . $e->getMessage());
                return back()->withInput()->withErrors(['error' => 'Format tanggal tidak valid! Pastikan formatnya seperti "1 Maret 2025"']);
            }

            // Cek validasi manual
            if (!strtotime($startDate) || !strtotime($endDate)) {
                return back()->withInput()->withErrors(['error' => 'Format tanggal tidak valid!']);
            }

            if ($startDate > $endDate) {
                return back()->withInput()->withErrors(['error' => 'Tanggal selesai harus setelah tanggal mulai!']);
            }

            // Validasi input lainnya
            $validatedData = $request->validate([
                'jurusan_id' => 'required|exists:jurusan,id',
                'kelas' => 'required|string',
                'dudika_id' => 'required|exists:dudika,id',
                'guru_id' => 'required|exists:guru,id',
                'murid_id' => 'required|array',
                'murid_id.*' => 'exists:murid,id',
            ]);

            Log::info('Validation Passed:', $validatedData);

            // Simpan periode dalam format asli "1 Maret 2025 - 31 Maret 2025"
            $periode = $request->start_date . ' - ' . $request->end_date;

            DB::beginTransaction();

            $magangData = $request->only(['jurusan_id', 'kelas', 'dudika_id', 'guru_id']);
            $magangData['periode'] = $periode;

            $magang = Magang::create($magangData);

            if (!empty($request->murid_id)) {
                $magang->murid()->attach($request->murid_id);
            }

            DB::commit();
            return redirect()->route('magang.index')->with('success', 'Data magang berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error saving magang: ' . $e->getMessage(), [
                'data' => $request->all(),
                'exception' => $e,
            ]);

            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    private function convertIndonesianDateToEnglish($date)
    {
        $bulanIndonesia = [
            'Januari' => 'January',
            'Februari' => 'February',
            'Maret' => 'March',
            'April' => 'April',
            'Mei' => 'May',
            'Juni' => 'June',
            'Juli' => 'July',
            'Agustus' => 'August',
            'September' => 'September',
            'Oktober' => 'October',
            'November' => 'November',
            'Desember' => 'December'
        ];

        foreach ($bulanIndonesia as $indo => $eng) {
            if (strpos($date, $indo) !== false) {
                $date = str_replace($indo, $eng, $date);
                break;
            }
        }

        return Carbon::createFromFormat('j F Y', $date)->format('Y-m-d');
    }

    public function edit(Magang $magang)
    {
        $jurusan = Jurusan::all();
        $kelas = Murid::select('kelas')->distinct()->orderBy('kelas')->get();
        $muridOptions = Murid::where('jurusan_id', $magang->jurusan_id)
            ->where('kelas', $magang->kelas)
            ->get();

        return view('magang.edit', compact('magang', 'jurusan', 'kelas', 'muridOptions'));
    }

    public function update(Request $request, Magang $magang)
    {
        $validatedData = $request->validate([
            'jurusan_id' => 'required|exists:jurusan,id',
            'kelas' => 'required|string',
            'murid_id' => 'required|array',
            'murid_id.*' => 'exists:murid,id'
        ]);

        DB::beginTransaction();
        try {
            // Update magang data
            $magang->update([
                'jurusan_id' => $request->jurusan_id,
                'kelas' => $request->kelas
            ]);

            // Sync murid relationships
            $magang->murid()->sync($request->murid_id);

            DB::commit();
            return redirect()->route('magang.index')
                ->with('success', 'Data magang berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating magang: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Gagal mengupdate data']);
        }
    }

    public function getPrintData(Magang $magang)
    {
        try {
            // Load relasi yang dibutuhkan
            $magang->load([
                'jurusan',
                'murid' => function ($query) use ($magang) {
                    // Filter murid sesuai dengan magang spesifik ini
                    $query->where('magang_id', $magang->id);
                },
                'dudika',
                'guru',
                'laporan' => function ($query) use ($magang) {
                    // Filter laporan sesuai dengan magang spesifik ini
                    $query->where('magang_id', $magang->id);
                }
            ]);

            // Pastikan relasi ada
            if (!$magang->jurusan || !$magang->dudika || !$magang->guru) {
                return response()->json([
                    'error' => 'Data tidak lengkap'
                ], 404);
            }

            // Siapkan data untuk ditampilkan di popup
            return response()->json([
                'id' => sprintf('%04d/MAGANG/%s/%d', $magang->id, date('m'), date('Y')),
                'periode' => $magang->periode ?? 'Tidak ditentukan',
                'jurusan' => $magang->jurusan->jurusan ?? 'Tidak diketahui',
                'kelas' => $magang->kelas ?? 'Tidak diketahui',
                'dudika' => $magang->dudika->dudika ?? 'Tidak diketahui',
                'alamat' => $magang->dudika->alamat ?? 'Tidak diketahui',
                'kontak' => $magang->dudika->kontak ?? 'Tidak diketahui',
                'guru' => $magang->guru->nama_guru ?? 'Tidak diketahui',
                'nip' => $magang->guru->nip ?? 'Tidak diketahui',
                'laporan' => $magang->laporan->map(function ($lap, $key) {
                    // Periksa dan pastikan path untuk foto laporan
                    $fotoPath = $lap->foto ? asset('storage/' . ltrim($lap->foto, '/')) : null;

                    // Periksa dan pastikan path untuk tanda tangan
                    $tandaTanganPath = $lap->tanda_tangan ? asset('storage/' . ltrim($lap->tanda_tangan, '/')) : null;

                    return [
                        'no' => $key + 1,
                        'hari_tanggal' => $this->getHariIndonesia(Carbon::parse($lap->tanggal_kunjungan)->format('l')) . ', ' . Carbon::parse($lap->tanggal_kunjungan)->format('d F Y'),
                        'keterangan' => $lap->keterangan,
                        'laporan_siswa' => $lap->laporan_siswa ? json_decode($lap->laporan_siswa, true) : [],
                        'foto' => $fotoPath, // URL foto laporan
                        'tanda_tangan' => $tandaTanganPath // URL tanda tangan
                    ];
                })->toArray(),
                'tanggal' => date('d F Y'),
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getPrintData: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan saat mengambil data'
            ], 500);
        }
    }

    protected function getHariIndonesia($namaHari)
    {
        $hariIndonesia = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];

        return array_key_exists($namaHari, $hariIndonesia) ? $hariIndonesia[$namaHari] : $namaHari;
    }

    public function destroy(Magang $magang)
    {
        DB::beginTransaction();
        try {
            // Hapus laporan yang terkait dengan magang ini
            foreach ($magang->laporan as $laporan) {
                // Hapus gambar laporan jika ada
                if ($laporan->foto && file_exists(public_path('storage/' . ltrim($laporan->foto, '/')))) {
                    unlink(public_path('storage/' . ltrim($laporan->foto, '/')));
                }

                // Hapus tanda tangan jika ada
                if ($laporan->tanda_tangan && file_exists(public_path('storage/' . ltrim($laporan->tanda_tangan, '/')))) {
                    unlink(public_path('storage/' . ltrim($laporan->tanda_tangan, '/')));
                }

                // Hapus laporan dari database
                $laporan->delete();
            }

            // Detach all students from this internship
            $magang->murid()->detach();

            // Delete the internship record
            $magang->delete();

            DB::commit();
            return redirect()->route('magang.index')
                ->with('success', 'Data magang berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error deleting magang: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menghapus data']);
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            $file = $request->file('file');

            Excel::import(new MagangImport, $file);

            Log::info('Berhasil mengimpor data magang');

            return redirect()->route('magang.index')
                ->with('success', 'Data magang berhasil diimpor');
        } catch (\Exception $e) {
            Log::error('Gagal impor magang: ' . $e->getMessage());

            return back()
                ->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    public function export()
    {
        $filename = 'data_magang ' . Carbon::now()->format('d-m-Y') . '.xlsx';

        return Excel::download(new MagangExport, $filename);
    }

    public function multiDelete(Request $request)
    {
        $selectedIds = $request->input('selected_ids', []);

        if (empty($selectedIds)) {
            return redirect()->back()->with('error', 'Tidak ada data magang yang dipilih untuk dihapus.');
        }

        Magang::whereIn('id', $selectedIds)->delete();

        return redirect()->route('magang.index')->with('success', 'Data magang terpilih berhasil dihapus.');
    }

    public function deleteAll()
    {
        Magang::truncate(); // Or Guru::query()->delete() if you want to keep auto-increment reset

        return response()->json(['message' => 'Semua data magang berhasil dihapus']);
    }

    public function downloadTemplate()
    {
        // Ambil semua data jurusan, dudika, guru, dan murid untuk dropdown referensi
        $jurusanList = Jurusan::select('id', 'jurusan')->get();
        $dudikaList = Dudika::select('id', 'dudika')->get();
        $guruList = Guru::select('id', 'nama_guru')->get();
        $muridList = Murid::select('id', 'nama_murid')->get(); // Ambil data murid untuk dropdown

        // Buat spreadsheet baru
        $spreadsheet = new Spreadsheet();

        // Rename sheet utama
        $mainSheet = $spreadsheet->getActiveSheet();
        $mainSheet->setTitle('Data Magang');

        // Set header kolom sesuai dengan skema magang
        $mainSheet->setCellValue('A1', 'jurusan_id');
        $mainSheet->setCellValue('B1', 'kelas');
        $mainSheet->setCellValue('C1', 'dudika_id');
        $mainSheet->setCellValue('D1', 'guru_id');
        $mainSheet->setCellValue('E1', 'periode');
        $mainSheet->setCellValue('F1', 'murid_id'); // Tambahkan kolom untuk murid_id

        // Buat contoh data
        $mainSheet->setCellValue('A2', '1'); // Contoh ID jurusan
        $mainSheet->setCellValue('B2', 'X');
        $mainSheet->setCellValue('C2', '1'); // Contoh ID dudika
        $mainSheet->setCellValue('D2', '1'); // Contoh ID guru
        $mainSheet->setCellValue('E2', '1 January 2025 - 30 Maret 2025');
        $mainSheet->setCellValue('F2', '1'); // Contoh ID murid

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
        $mainSheet->getStyle('A1:F1')->applyFromArray($headerStyle);

        // Validasi untuk kolom jurusan_id
        $validationJurusan = $mainSheet->getCell('A2')->getDataValidation();
        $validationJurusan->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $validationJurusan->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $validationJurusan->setAllowBlank(false);
        $validationJurusan->setShowInputMessage(true);
        $validationJurusan->setShowErrorMessage(true);
        $validationJurusan->setShowDropDown(true);
        $jurusanOptions = $jurusanList->pluck('id')->implode(',');
        $validationJurusan->setFormula1('"' . $jurusanOptions . '"');
        $validationJurusan->setErrorTitle('Input error');
        $validationJurusan->setError('ID jurusan tidak valid.');
        $validationJurusan->setPromptTitle('Pilih ID jurusan');
        $validationJurusan->setPrompt('Pilih dari daftar ID jurusan yang tersedia');

        // Validasi untuk kolom dudika_id
        $validationDudika = $mainSheet->getCell('C2')->getDataValidation();
        $validationDudika->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $validationDudika->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $validationDudika->setAllowBlank(false);
        $validationDudika->setShowInputMessage(true);
        $validationDudika->setShowErrorMessage(true);
        $validationDudika->setShowDropDown(true);
        $dudikaOptions = $dudikaList->pluck('id')->implode(',');
        $validationDudika->setFormula1('"' . $dudikaOptions . '"');
        $validationDudika->setErrorTitle('Input error');
        $validationDudika->setError('ID DUDIKA tidak valid.');
        $validationDudika->setPromptTitle('Pilih ID DUDIKA');
        $validationDudika->setPrompt('Pilih dari daftar ID DUDIKA yang tersedia');

        // Validasi untuk kolom guru_id
        $validationGuru = $mainSheet->getCell('D2')->getDataValidation();
        $validationGuru->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $validationGuru->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $validationGuru->setAllowBlank(false);
        $validationGuru->setShowInputMessage(true);
        $validationGuru->setShowErrorMessage(true);
        $validationGuru->setShowDropDown(true);
        $guruOptions = $guruList->pluck('id')->implode(',');
        $validationGuru->setFormula1('"' . $guruOptions . '"');
        $validationGuru->setErrorTitle('Input error');
        $validationGuru->setError('ID guru tidak valid.');
        $validationGuru->setPromptTitle('Pilih ID guru');
        $validationGuru->setPrompt('Pilih dari daftar ID guru yang tersedia');

        // Validasi untuk kolom kelas
        $validationKelas = $mainSheet->getCell('B2')->getDataValidation();
        $validationKelas->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $validationKelas->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $validationKelas->setAllowBlank(false);
        $validationKelas->setShowInputMessage(true);
        $validationKelas->setShowErrorMessage(true);
        $validationKelas->setShowDropDown(true);
        $validationKelas->setFormula1('"X,XI,XII"');

        // Validasi untuk kolom murid_id
        $validationMurid = $mainSheet->getCell('F2')->getDataValidation();
        $validationMurid->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
        $validationMurid->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
        $validationMurid->setAllowBlank(false);
        $validationMurid->setShowInputMessage(true);
        $validationMurid->setShowErrorMessage(true);
        $validationMurid->setShowDropDown(true);
        $muridOptions = $muridList->pluck('id')->implode(',');
        $validationMurid->setFormula1('"' . $muridOptions . '"');
        $validationMurid->setErrorTitle('Input error');
        $validationMurid->setError('ID murid tidak valid.');
        $validationMurid->setPromptTitle('Pilih ID murid');
        $validationMurid->setPrompt('Pilih dari daftar ID murid yang tersedia');

        // Terapkan validasi ke range
        $mainSheet->setDataValidation('A2:A1000', $validationJurusan);
        $mainSheet->setDataValidation('B2:B1000', $validationKelas);
        $mainSheet->setDataValidation('C2:C1000', $validationDudika);
        $mainSheet->setDataValidation('D2:D1000', $validationGuru);
        $mainSheet->setDataValidation('F2:F1000', $validationMurid);

        // Atur format kolom NIS sebagai teks
        $mainSheet->getStyle('B:B')->getNumberFormat()->setFormatCode('@');

        // Auto-size kolom
        foreach (range('A', 'F') as $column) {
            $mainSheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Memberikan background warna ke contoh data
        $mainSheet->getStyle('A2:F2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $mainSheet->getStyle('A2:F2')->getFill()->getStartColor()->setRGB('F2F2F2');

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
        $guideSheet->setCellValue('A8', '5. dudika_id harus sesuai dengan ID DUDIKA yang tersedia (lihat tabel referensi di bawah)');
        $guideSheet->setCellValue('A9', '6. guru_id harus sesuai dengan ID guru yang tersedia (lihat tabel referensi di bawah)');
        $guideSheet->setCellValue('A10', '7. JANGAN mengubah baris header (baris 1)');
        $guideSheet->setCellValue('A11', '8. Hapus baris contoh (baris 2) sebelum import data sebenarnya');
        $guideSheet->setCellValue('A12', '9. Jika satu DUDIKA memiliki lebih dari satu murid, buat baris baru dengan data magang yang sama, tetapi dengan ID murid yang berbeda di kolom murid_id.');

        // Tambahkan spasi sebelum referensi
        $guideSheet->setCellValue('A14', ''); // Spasi
        $guideSheet->setCellValue('B15', 'REFERENSI JURUSAN');
        $guideSheet->getStyle('B15')->getFont()->setBold(true);

        // Header tabel referensi
        $guideSheet->setCellValue('B16', 'ID');
        $guideSheet->setCellValue('C16', 'Nama Jurusan');
        $guideSheet->getStyle('B16:C16')->applyFromArray($headerStyle);

        // Isi tabel referensi jurusan
        $row = 17;
        foreach ($jurusanList as $jurusan) {
            $guideSheet->setCellValue('B' . $row, $jurusan->id);
            $guideSheet->setCellValue('C' . $row, $jurusan->jurusan);
            $row++;
        }

        // Tambahkan spasi sebelum referensi DUDIKA
        $guideSheet->setCellValue('A' . $row, ''); // Spasi
        $guideSheet->setCellValue('B' . $row, 'REFERENSI DUDIKA');
        $guideSheet->getStyle('B' . $row)->getFont()->setBold(true);
        $row++;

        // Header tabel referensi DUDIKA
        $guideSheet->setCellValue('B' . $row, 'ID');
        $guideSheet->setCellValue('C' . $row, 'Nama DUDIKA');
        $guideSheet->getStyle('B' . $row . ':C' . $row)->applyFromArray($headerStyle);
        $row++;

        // Isi tabel referensi DUDIKA
        foreach ($dudikaList as $dudika) {
            $guideSheet->setCellValue('B' . $row, $dudika->id);
            $guideSheet->setCellValue('C' . $row, $dudika->dudika);
            $row++;
        }

        // Tambahkan spasi sebelum referensi GURU
        $guideSheet->setCellValue('A' . $row, ''); // Spasi
        $guideSheet->setCellValue('B' . $row, 'REFERENSI GURU');
        $guideSheet->getStyle('B' . $row)->getFont()->setBold(true);
        $row++;

        // Header tabel referensi GURU
        $guideSheet->setCellValue('B' . $row, 'ID');
        $guideSheet->setCellValue('C' . $row, 'Nama GURU');
        $guideSheet->getStyle('B' . $row . ':C' . $row)->applyFromArray($headerStyle);
        $row++;

        // Isi tabel referensi GURU
        foreach ($guruList as $guru) {
            $guideSheet->setCellValue('B' . $row, $guru->id);
            $guideSheet->setCellValue('C' . $row, $guru->nama_guru);
            $row++;
        }

        // Tambahkan spasi sebelum referensi MURID
        $guideSheet->setCellValue('A' . $row, ''); // Spasi
        $guideSheet->setCellValue('B' . $row, 'REFERENSI MURID');
        $guideSheet->getStyle('B' . $row)->getFont()->setBold(true);
        $row++;

        // Header tabel referensi MURID
        $guideSheet->setCellValue('B' . $row, 'ID');
        $guideSheet->setCellValue('C' . $row, 'Nama MURID');
        $guideSheet->getStyle('B' . $row . ':C' . $row)->applyFromArray($headerStyle);
        $row++;

        // Isi tabel referensi MURID
        foreach ($muridList as $murid) {
            $guideSheet->setCellValue('B' . $row, $murid->id);
            $guideSheet->setCellValue('C' . $row, $murid->nama_murid);
            $row++;
        }

        // Format tabel referensi
        $guideSheet->getStyle('B15:C' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Auto-size kolom di sheet panduan
        foreach (range('A', 'C') as $column) {
            $guideSheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Aktifkan sheet utama sebagai default
        $spreadsheet->setActiveSheetIndex(0);

        // Buat file Excel
        $writer = new Xlsx($spreadsheet);

        // Set header untuk download
        $filename = 'template_data_magang.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Output file
        $writer->save('php://output');
        exit;
    }
}
