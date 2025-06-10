<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;

    protected $table = 'jurusan';

    protected $fillable = ['jurusan', 'deskripsi'];  // Menambahkan kolom yang dapat diisi

    // Relasi dengan model Murid
    public function murid()
    {
        return $this->hasMany(Murid::class);
    }

    public function magang()
    {
        return $this->hasMany(Magang::class, 'jurusan_id', 'id');
    }
}