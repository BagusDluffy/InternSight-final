<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens; // Import trait


class Guru extends Authenticatable
{
    use HasApiTokens,HasFactory;

    protected $table = 'guru';

    protected $fillable = [
        'nama_guru',
        'nip',
        'no_hp',
        'email',
        'password',
        'encrypted_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function magang()
    {
        return $this->hasMany(Magang::class, 'guru_pembimbing_id');
    }
}