<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Magang extends Model
{
    use HasFactory;

    protected $table = 'magang';

    protected $fillable = [
        'jurusan_id','kelas','dudika_id', 'guru_id', 'periode',
    ];

    // Relasi ke model Murid (banyak ke banyak)
    public function murid()
    {
        return $this->belongsToMany(Murid::class, 'magang_murid', 'magang_id', 'murid_id')
            ->withTimestamps()
            ->select('murid.id', 'murid.nama_murid'); // Gunakan prefix tabel
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id', 'id');
    }

    public function dudika()
    {
        return $this->belongsTo(Dudika::class);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function laporan()
    {
        return $this->hasMany(Laporan::class);
    }
}
