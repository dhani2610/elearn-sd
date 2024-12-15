<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLatihanSoalsTable extends Migration
{
    public function up()
    {
        Schema::create('latihan_soals', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_guru');
            $table->unsignedInteger('id_kelas');
            $table->unsignedInteger('id_pelajaran');
            $table->string('judul');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('latihan_soals');
    }
}
