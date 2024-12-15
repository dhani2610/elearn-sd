<?php

namespace App\Models;

use App\Models\Soal;
use Illuminate\Database\Eloquent\Model;

class LatihanSoal extends Model
{
    protected $fillable = ['id_guru', 'id_kelas', 'id_pelajaran', 'judul'];
    protected $table = 'latihan_soals';
    public function soals()
    {
        return $this->hasMany(Soal::class);
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'id_pelajaran');
    }
    public function kelas()
    {
        return $this->belongsTo(Jadwal::class, 'id_kelas');
    }
}
