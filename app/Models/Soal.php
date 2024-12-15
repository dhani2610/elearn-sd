<?php

namespace App\Models;

use App\Models\LatihanSoal;
use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    protected $fillable = ['latihan_soal_id', 'pertanyaan', 'tipe_soal', 'pilihan', 'jawaban_benar'];
    protected $table = 'soals';

    protected $casts = [
        'pilihan' => 'array',
    ];

    public function latihanSoal()
    {
        return $this->belongsTo(LatihanSoal::class);
    }
}
