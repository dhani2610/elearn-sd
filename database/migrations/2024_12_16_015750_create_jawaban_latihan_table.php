<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jawaban_latihan', function (Blueprint $table) {
            $table->id();
            $table->integer('id_latihan_soal'); // Referensi ke latihan_soals
            $table->integer('id_siswa'); // Referensi ke siswa
            $table->integer('id_soal'); // Referensi ke soal
            $table->text('jawaban'); // Jawaban siswa
            $table->integer('skor')->nullable(); // Skor yang didapat oleh siswa

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jawaban_latihan');
    }
};
