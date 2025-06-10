<?php

namespace App\Imports;

use App\Models\Guru;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithMultipleSheets; // Tambahan

class GuruImport implements ToModel, WithHeadingRow, WithValidation, WithMultipleSheets
{
    public function model(array $row)
    {
        // Generate password acak jika tidak ada di excel
        $plainPassword = $row['password'] ?? $this->generateRandomPassword();

        return new Guru([
            'nama_guru' => $row['nama_guru'],
            'email' => $row['email'],
            'password' => Hash::make($plainPassword), // Hash untuk autentikasi
            'encrypted_password' => Crypt::encryptString($plainPassword), // Enkripsi untuk penyimpanan
        ]);
    }

    // Validasi input
    public function rules(): array
    {
        return [
            'nama_guru' => 'required|string|max:255',
            'email' => 'required|email|unique:guru,email',
            'password' => 'nullable|min:6',
        ];
    }

    // Generate password acak
    private function generateRandomPassword($length = 10)
    {
        return substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'), 0, $length);
    }

    // Hanya mengambil sheet pertama
    public function sheets(): array
    {
        return [0 => $this]; // Hanya sheet pertama (index 0)
    }
}
