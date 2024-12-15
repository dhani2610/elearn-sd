<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\LatihanSoal;
use App\Models\Mapel;
use App\Models\Soal;
use Illuminate\Http\Request;
use PharIo\Manifest\Author;
use Illuminate\Support\Facades\Auth;

class LatihanSoalController extends Controller
{
    public function index()
    {
        $latihanSoals = LatihanSoal::with('soals')->get();

        return view('pages.latihan_soal.index', compact('latihanSoals'));
    }

    public function create()
    {
        $g = Guru::where('user_id', Auth::user()->id)->first();
        $data['guru'] = Guru::where('user_id', Auth::user()->id)->get();
        $data['mapel'] = Mapel::OrderBy('nama_mapel', 'asc')->get();
        $data['jadwal'] = Jadwal::where('mapel_id', $g->mapel_id)->get();

        return view('pages.latihan_soal.create',$data);
    }

    public function store(Request $request)
    {
        // $latihanSoal = LatihanSoal::create($request->only('id_guru', 'id_kelas', 'id_pelajaran', 'judul'));
        try {
            $latihanSoal = new LatihanSoal();
            $latihanSoal->judul = $request->judul;
            $latihanSoal->id_guru = $request->id_guru;
            $latihanSoal->id_kelas = $request->id_kelas;
            $latihanSoal->id_pelajaran = $request->id_pelajaran;
            $latihanSoal->save();
    
            foreach ($request->soals as $soalData) {
                $latihanSoal->soals()->create([
                    'pertanyaan' => $soalData['pertanyaan'],
                    'tipe_soal' => $soalData['tipe_soal'],
                    'pilihan' => $soalData['tipe_soal'] == 'pilihan_ganda' ? $soalData['pilihan'] : null,
                    'jawaban_benar' => $soalData['jawaban_benar'] ?? null,
                ]);
            }
            return redirect()->route('latihan_soal.index')->with('success', 'Latihan soal berhasil dibuat!');
        } catch (\Throwable $th) {
            dd($request->all(),$th->getMessage());
            return redirect()->route('latihan_soal.index')->with('success', 'Latihan soal berhasil dibuat!');
        }

    }

    public function edit(LatihanSoal $latihanSoal)
    {
        $g = Guru::where('user_id', Auth::user()->id)->first();
        $data['guru'] = Guru::where('user_id', Auth::user()->id)->get();
        $data['mapel'] = Mapel::OrderBy('nama_mapel', 'asc')->get();
        $data['jadwal'] = Jadwal::where('mapel_id', $g->mapel_id)->get();
        $data['latihanSoal']= $latihanSoal;

        return view('pages.latihan_soal.edit', $data);
    }

    public function update(Request $request, LatihanSoal $latihanSoal)
    {
        $latihanSoal->update($request->only('id_guru', 'id_kelas', 'id_pelajaran', 'judul'));

        $latihanSoal->soals()->delete();

        foreach ($request->soals as $soalData) {
            $latihanSoal->soals()->create([
                'pertanyaan' => $soalData['pertanyaan'],
                'tipe_soal' => $soalData['tipe_soal'],
                'pilihan' => $soalData['tipe_soal'] == 'pilihan_ganda' ? $soalData['pilihan'] : null,
                'jawaban_benar' => $soalData['jawaban_benar'] ?? null,
            ]);
        }

        return redirect()->route('latihan_soal.index')->with('success', 'Latihan soal berhasil diperbarui!');
    }

    public function destroy(LatihanSoal $latihanSoal)
    {
        $latihanSoal->delete();
        return redirect()->route('latihan_soal.index')->with('success', 'Latihan soal berhasil dihapus!');
    }
}
