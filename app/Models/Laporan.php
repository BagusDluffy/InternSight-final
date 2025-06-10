<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika tidak sesuai dengan konvensi Laravel
    protected $table = 'laporan';

    // Tentukan kolom yang dapat diisi (mass assignable)
    protected $fillable = [
        'magang_id',
        'tanggal_kunjungan',
        'keterangan',
        'laporan_siswa',
        'foto',
        'tanda_tangan',
    ];

    // Definisikan relasi dengan model Magang
    public function magang()
    {
        return $this->belongsTo(Magang::class);
    }
}