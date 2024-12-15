<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JawabanLatihan extends Model
{
    use HasFactory;

    protected $fillable = ['id_latihan_soal', 'id_siswa', 'id_soal', 'jawaban', 'skor'];
    protected $table = 'jawaban_latihan';

    public function soal()
    {
        return $this->belongsTo(Soal::class, 'id_soal');
    }

    public function latihanSoal()
    {
        return $this->belongsTo(LatihanSoal::class, 'id_latihan_soal');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }
}
