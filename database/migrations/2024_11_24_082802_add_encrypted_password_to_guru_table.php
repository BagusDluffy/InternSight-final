<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEncryptedPasswordToGuruTable extends Migration
{
    public function up()
    {
        Schema::table('guru', function (Blueprint $table) {
            $table->text('encrypted_password')->nullable()->after('password'); // Kolom untuk password terenkripsi
        });
    }

    public function down()
    {
        Schema::table('guru', function (Blueprint $table) {
            $table->dropColumn('encrypted_password');
        });
    }
}