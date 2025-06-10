<?php

namespace App\Imports;

use App\Models\Murid;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithSheetSelector;
use Maatwebsite\Excel\Concerns\Importable;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class MuridImport extends StringValueBinder implements 
    ToCollection, 
    WithHeadingRow, 
    WithValidation, 
    WithStartRow, 
    WithCustomValueBinder, 
    WithCalculatedFormulas,
    SkipsEmptyRows,
    WithMultipleSheets
{
    use Importable;

    private $selectedSheet = 0; // Default: sheet pertama

    public function __construct($sheetName = null)
    {
        if ($sheetName) {
            $this->selectedSheet = $sheetName;
        }
    }

    public function sheets(): array
    {
        return [$this->selectedSheet => $this];
    }

    public function startRow(): int
    {
        return 2; // Mulai membaca dari baris kedua (header ada di baris pertama)
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if ($this->isValidRow($row)) {
                Murid::create([
                    'nama_murid' => $row['nama_murid'],
                    'nis' => (string) $row['nis'], // Pastikan NIS tetap string
                    'kelas' => $row['kelas'],
                    'jurusan_id' => $row['jurusan_id'],
                ]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'nama_murid' => 'required|string|max:255',
            'nis' => 'required|string|max:255', // Pastikan NIS sebagai string
            'kelas' => 'required|string|max:255',
            'jurusan_id' => 'required|exists:jurusan,id',
        ];
    }

    private function isValidRow($row)
    {
        return !empty($row['nama_murid']) && 
               !empty($row['nis']) && 
               !empty($row['kelas']) && 
               !empty($row['jurusan_id']) &&
               !str_contains($row['nama_murid'], 'Catatan') &&
               !str_contains($row['nama_murid'], 'PANDUAN') &&
               !str_contains($row['nama_murid'], 'REFERENSI') &&
               !str_contains($row['nama_murid'], 'ID');
    }
}
