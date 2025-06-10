<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dudika extends Model
{
    use HasFactory;

    protected $table = 'dudika';
    protected $fillable = ['dudika', 'alamat', 'kontak'];

    // Relationship with Magang model (assuming you have one)
    public function magang()
    {
        return $this->hasMany(Magang::class, 'dudika_id');
    }
}