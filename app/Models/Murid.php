<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Murid extends Model
{
    use HasFactory;

    protected $table = 'murid';
    protected $fillable = ['nama_murid', 'nis', 'kelas', 'jurusan_id'];

    // Relasi ke model Jurusan
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    // Relasi ke model Magang (banyak ke banyak)
    public function magang()
    {
        return $this->belongsToMany(Magang::class, 'magang_murid', 'murid_id', 'magang_id')
            ->withTimestamps();
    }

}
