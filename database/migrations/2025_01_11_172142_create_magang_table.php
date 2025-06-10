<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('magang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jurusan_id');
            $table->string('kelas');
            $table->unsignedBigInteger('dudika_id');
            $table->unsignedBigInteger('guru_id');
            $table->string('periode');
            $table->timestamps();
        
            $table->foreign('jurusan_id')->references('id')->on('jurusan');
            $table->foreign('dudika_id')->references('id')->on('dudika');
            $table->foreign('guru_id')->references('id')->on('guru');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('magang');
    }
};
