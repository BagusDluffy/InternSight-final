<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMagangMuridTable extends Migration
{
    public function up()
    {
        Schema::create('magang_murid', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('magang_id');
            $table->unsignedBigInteger('murid_id');
            $table->timestamps();
        
            $table->foreign('magang_id')->references('id')->on('magang')->onDelete('cascade');
            $table->foreign('murid_id')->references('id')->on('murid')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('magang_murid');
    }
}
