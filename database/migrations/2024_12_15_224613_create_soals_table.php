<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSoalsTable extends Migration
{
    public function up()
    {
        Schema::create('soals', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('latihan_soal_id');
            $table->text('pertanyaan');
            $table->enum('tipe_soal', ['pilihan_ganda', 'essay']);
            $table->json('pilihan')->nullable();
            $table->string('jawaban_benar')->nullable();
            $table->timestamps();

            $table->foreign('latihan_soal_id')->references('id')->on('latihan_soals')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('soals');
    }
}
