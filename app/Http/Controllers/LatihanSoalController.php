<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\JawabanLatihan;
use App\Models\Kelas;
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
        $data['jadwal'] = Kelas::all();

        return view('pages.latihan_soal.create', $data);
    }

    public function show($id)
    {
        // $g = Guru::where('user_id', Auth::user()->id)->first();
        $data['questions'] = LatihanSoal::find($id);
        return view('pages.latihan_soal.latihan', $data);
    }
    public function jawabanLatihan($id,$id_siswa)
    {
        // $g = Guru::where('user_id', Auth::user()->id)->first();
        $data['questions'] = LatihanSoal::find($id);
        $data['id_siswa'] = $id_siswa;

        return view('pages.latihan_soal.jawaban-latihan', $data);
    }
    public function cekJawabanLatihan($id)
    {
        // Ambil jawaban yang sudah disimpan untuk latihan soal dengan ID $id
        $jawabanLatihan = JawabanLatihan::where('id_latihan_soal', $id)->get();
    
        // Ambil data soal latihan
        $data['questions'] = LatihanSoal::find($id);
    
        // Ambil data siswa yang telah menjawab latihan soal beserta total skor mereka
        $data['siswaJawaban'] = $jawabanLatihan->groupBy('id_siswa','id_latihan_soal')->map(function($jawaban) use($id) {
            // Hitung total skor untuk masing-masing siswa
            $totalSkor = $jawaban->sum('skor'); // Menghitung jumlah total skor
            return [
                'id_latihan' => $id, // ID siswa pertama dari grup
                'siswa_id' => $jawaban->first()->id_siswa, // ID siswa pertama dari grup
                'total_skor' => $totalSkor,
                'jawaban' => $jawaban // Semua jawaban siswa untuk latihan ini
            ];
        });
        // Menampilkan hasil ke tampilan
        return view('pages.latihan_soal.jawaban', $data);
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
            return redirect()->route('latihan_soal.index')->with('success', 'Latihan soal gagal dibuat!');
        }
    }

    public function storeJawaban(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'answers' => 'required|array', // Pastikan answers yang diterima dalam bentuk array
        ]);
    
        // Ambil jumlah total soal
        $totalSoal = count($request->answers); // Menghitung jumlah soal
        $skorPerSoal = 100 / $totalSoal; // Menghitung skor per soal
    
        // Loop untuk menyimpan setiap jawaban dari soal
        $datapush = [];
        foreach ($request->answers as $id_soal => $jawaban) {
            // Ambil soal berdasarkan id
            $soal = Soal::find($id_soal);
    
            // Jika soal adalah pilihan ganda
            if ($soal->tipe_soal === 'pilihan_ganda') {
                // Pastikan jawaban untuk soal pilihan ganda berupa array, pilih jawaban pertama
                $jawabanPilihan = is_array($jawaban) ? $jawaban[0] : $jawaban;
    
                // Perhitungan skor untuk soal pilihan ganda
                $skor = ($soal->jawaban_benar == $jawabanPilihan) ? $skorPerSoal : 0; // Skor dihitung berdasarkan jumlah soal
            }
            // Jika soal adalah essay
            elseif ($soal->tipe_soal === 'essay') {
                // Set skor untuk soal essay menjadi null agar dapat dinilai oleh guru
                $skor = null;
                $jawabanPilihan = $jawaban; // Jawaban untuk soal essay adalah teks
            }
    
            // Persiapkan data untuk disimpan
            $datapush[] = [
                'jawaban' => $jawabanPilihan,
                'skor' => $skor,
            ];
    
            // Simpan atau update jawaban yang sudah ada
            JawabanLatihan::updateOrCreate(
                [
                    'id_latihan_soal' => $id,
                    'id_siswa' => Auth::user()->id,
                    'id_soal' => $id_soal,
                ],
                [
                    'jawaban' => $jawabanPilihan,
                    'skor' => $skor,
                ]
            );
        }
    
        // Debugging output (untuk melihat jawaban yang dikirim)
        // dd($datapush, $request->all());
    
        // Redirect kembali dengan pesan sukses
        return redirect()->route('latihan.index')
            ->with('success', 'Jawaban Anda telah disimpan! Guru akan mengecek kembali jawaban Anda.');
    }
    

    public function updateNilaiLatihan(Request $request,$id){

        try {
            $nilai = $request->nilai;
            foreach ($nilai as $id_answ => $skor) {
                $update = JawabanLatihan::find($id_answ);
                $update->skor = $skor;
                $update->save();
            }
            return redirect()->back()
            ->with('success', 'Penilaian telah disimpan.');
        } catch (\Throwable $th) {
            return redirect()->back()
            ->with('failed', 'Penilaian gagal disimpan.');
        }
    }


    public function edit(LatihanSoal $latihanSoal)
    {
        $g = Guru::where('user_id', Auth::user()->id)->first();
        $data['guru'] = Guru::where('user_id', Auth::user()->id)->get();
        $data['mapel'] = Mapel::OrderBy('nama_mapel', 'asc')->get();
        $data['jadwal'] = Kelas::all();
        $data['latihanSoal'] = $latihanSoal;

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
