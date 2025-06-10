<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaporanTable extends Migration
{
    public function up()
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('magang_id'); // Relasi ke tabel magang
            $table->string('tanggal_kunjungan');
            $table->text('keterangan');
            $table->text('laporan_siswa'); // Menyimpan laporan siswa
            $table->text('foto'); // Menyimpan path foto
            $table->text('tanda_tangan'); // Menyimpan tanda tangan
            $table->timestamps();

            $table->foreign('magang_id')->references('id')->on('magang')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('laporan');
    }
}