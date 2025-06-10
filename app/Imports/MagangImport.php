<?php

namespace App\Imports;

use App\Models\Magang;
use App\Models\Murid;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets; // Tambahkan ini
use Illuminate\Support\Facades\Log;

class MagangImport implements 
    ToCollection, 
    WithHeadingRow, 
    WithValidation, 
    WithStartRow,
    WithMultipleSheets // Tambahkan ini
{
    public function startRow(): int
    {
        return 2; // Mulai membaca dari baris kedua karena baris pertama adalah header
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            Log::info('Row data: ', $row->toArray()); // Log data baris yang diterima
    
            try {
                // Cek apakah sudah ada entri dengan guru_id, dudika_id, dan kelas yang sama
                $magang = Magang::where('kelas', $row['kelas'])
                    ->where('jurusan_id', $row['jurusan_id'])
                    ->where('dudika_id', $row['dudika_id'])
                    ->where('guru_id', $row['guru_id'])
                    ->first();
    
                // Jika tidak ada, buat entri baru
                if (!$magang) {
                    $magang = Magang::create([
                        'kelas' => $row['kelas'],
                        'jurusan_id' => $row['jurusan_id'],
                        'dudika_id' => $row['dudika_id'],
                        'guru_id' => $row['guru_id'],
                        'periode' => $row['periode'],
                    ]);
                }
    
                // Jika ada murid_id, masukkan ke tabel pivot `magang_murid`
                if (!empty($row['murid_id'])) {
                    $muridIds = explode(',', $row['murid_id']); // Pisahkan ID murid yang dipisahkan koma
                    $validMuridIds = Murid::whereIn('id', $muridIds)->pluck('id')->toArray(); // Pastikan ID valid
    
                    // Hubungkan magang dengan murid-muridnya di tabel pivot
                    $magang->murid()->syncWithoutDetaching($validMuridIds); // Gunakan syncWithoutDetaching untuk menghindari penghapusan ID yang sudah ada
                }
            } catch (\Exception $e) {
                Log::error('Gagal mengimpor data magang: ' . $e->getMessage());
            }
        }
    }

    public function rules(): array
    {
        return [
            'kelas' => 'required|string|max:255',
            'jurusan_id' => 'required|exists:jurusan,id',
            'dudika_id' => 'required|exists:dudika,id',
            'guru_id' => 'required|exists:guru,id',
            'periode' => 'required|string|max:255',
            'murid_id' => 'nullable', // Bisa kosong, atau berupa daftar ID murid yang dipisahkan koma
        ];
    }

    // Tambahkan metode ini untuk menentukan sheet yang akan diambil
    public function sheets(): array
    {
        return [
            // Hanya ambil data dari sheet pertama
            0 => new self(),
        ];
    }
}