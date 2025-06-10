<?php

namespace App\Imports;

use App\Models\Dudika;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\Log;

class DudikaImport implements ToModel, WithHeadingRow, WithValidation, WithMultipleSheets
{
    public function model(array $row)
    {
        Log::info(json_encode($row));
        return new Dudika([
            'dudika' => trim($row['dudika']),
            'alamat' => trim($row['alamat']),
            'kontak' => trim($row['kontak']) ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'dudika' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'kontak' => 'nullable|string|max:255',
        ];
    }

    public function sheets(): array
    {
        return [0 => $this]; // Hanya mengambil sheet pertama (index 0)
    }
}
