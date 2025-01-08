@extends('layouts.main')
@section('title', 'Edit Latihan Soal')

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Edit Latihan Soal</h4>
                        </div>
                        <div class="card-body">
                            @include('partials.alert')
                            <form action="{{ route('latihan_soal.update', $latihanSoal->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label for="id_guru">Guru</label>
                                    <select id="id_guru" name="id_guru"
                                        class="select2 form-control @error('id_guru') is-invalid @enderror">
                                        <option value="">-- Pilih Guru --</option>
                                        @foreach ($guru as $g)
                                            <option value="{{ $g->id }}"
                                                {{ $latihanSoal->id_guru == $g->id ? 'selected' : '' }}>
                                                {{ $g->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="id_kelas">Kelas</label>
                                    <select id="id_kelas" name="id_kelas"
                                        class="select2 form-control @error('id_kelas') is-invalid @enderror">
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach ($jadwal as $data)
                                            <option value="{{ $data->id }}"
                                                {{ $latihanSoal->id_kelas == $data->id ? 'selected' : '' }}>
                                                {{ $data->nama_kelas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="id_pelajaran">Mata Pelajaran</label>
                                    <select id="id_pelajaran" name="id_pelajaran"
                                        class="select2 form-control @error('id_pelajaran') is-invalid @enderror">
                                        <option value="">-- Pilih Mata Pelajaran --</option>
                                        @foreach ($mapel as $m)
                                            <option value="{{ $m->id }}"
                                                {{ $latihanSoal->id_pelajaran == $m->id ? 'selected' : '' }}>
                                                {{ $m->nama_mapel }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="judul">Judul Latihan</label>
                                    <input type="text" name="judul" class="form-control"
                                        value="{{ old('judul', $latihanSoal->judul) }}" required>
                                </div>

                                <hr>

                                <h4>Tambah Soal</h4>
                                <div id="soal-container">
                                    @foreach ($latihanSoal->soals as $index => $soal)
                                        <div class="soal-item mb-4">
                                             
                                            <hr>
                                            <label for="pertanyaan">Pertanyaan</label>
                                            <textarea name="soals[{{ $index }}][pertanyaan]" class="form-control" required>{{ old('soals.' . $index . '.pertanyaan', $soal->pertanyaan) }}</textarea>

                                            <label for="tipe_soal">Tipe Soal</label>
                                            <select name="soals[{{ $index }}][tipe_soal]"
                                                class="form-control tipe-soal" required>
                                                <option value="" disabled selected>Pilih Tipe</option>
                                                <option value="pilihan_ganda"
                                                    {{ $soal->tipe_soal == 'pilihan_ganda' ? 'selected' : '' }}>Pilihan
                                                    Ganda</option>
                                                <option value="essay" {{ $soal->tipe_soal == 'essay' ? 'selected' : '' }}>
                                                    Essay</option>
                                            </select>

                                            @if ($soal->tipe_soal == 'pilihan_ganda')
                                            <div class="pilihan-ganda mt-2"
                                                style="{{ $soal->tipe_soal == 'pilihan_ganda' ? 'display:block;' : 'display:none;' }}">
                                                <label for="pilihan">Pilihan</label>
                                                {{-- @dd($soal->pilihan); --}}
                                                @foreach ($soal->pilihan as $key => $pilihan)
                                                    <input type="text" name="soals[{{ $index }}][pilihan][]"
                                                        class="form-control mb-2"
                                                        value="{{ old('soals.' . $index . '.pilihan.' . $key, $pilihan) }}"
                                                        placeholder="Opsi {{ chr(65 + $key) }}">
                                                @endforeach

                                                <label for="jawaban_benar">Jawaban Benar</label>
                                                <select name="soals[{{ $index }}][jawaban_benar]"
                                                    class="form-control jawaban-benar">
                                                    <option value="Opsi A"
                                                        {{ $soal->jawaban_benar == 'Opsi A' ? 'selected' : '' }}>Opsi A
                                                    </option>
                                                    <option value="Opsi B"
                                                        {{ $soal->jawaban_benar == 'Opsi B' ? 'selected' : '' }}>Opsi B
                                                    </option>
                                                    <option value="Opsi C"
                                                        {{ $soal->jawaban_benar == 'Opsi C' ? 'selected' : '' }}>Opsi C
                                                    </option>
                                                    <option value="Opsi D"
                                                        {{ $soal->jawaban_benar == 'Opsi D' ? 'selected' : '' }}>Opsi D
                                                    </option>
                                                </select>
                                            </div>
                                            @endif
                                            <button type="button" class="btn btn-danger remove-soal mt-2">Hapus Soal</button>
                                        </div>
                                    @endforeach

                                </div>

                                <button type="button" id="add-soal" class="btn btn-success">Tambah Soal</button>
                                <button type="submit" class="btn btn-primary" style="float: right">Simpan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let soalIndex = {{ count($latihanSoal->soals) }};
            document.getElementById('add-soal').addEventListener('click', function() {
                let soalContainer = document.getElementById('soal-container');
                let soalItem = document.createElement('div');
                soalItem.classList.add('soal-item', 'mb-4');
                soalItem.innerHTML = `
                    <label for="pertanyaan">Pertanyaan</label>
                    <textarea name="soals[${soalIndex}][pertanyaan]" class="form-control" required></textarea>

                    <label for="tipe_soal">Tipe Soal</label>
                    <select name="soals[${soalIndex}][tipe_soal]" class="form-control tipe-soal" required>
                        <option value="" disabled selected>Pilih Tipe</option>
                        <option value="pilihan_ganda">Pilihan Ganda</option>
                        <option value="essay">Essay</option>
                    </select>

                    <div class="pilihan-ganda mt-2" style="display: none;">
                        <label for="pilihan">Pilihan</label>
                        <input type="text" name="soals[${soalIndex}][pilihan][]" class="form-control mb-2" placeholder="Opsi A">
                        <input type="text" name="soals[${soalIndex}][pilihan][]" class="form-control mb-2" placeholder="Opsi B">
                        <input type="text" name="soals[${soalIndex}][pilihan][]" class="form-control mb-2" placeholder="Opsi C">
                        <input type="text" name="soals[${soalIndex}][pilihan][]" class="form-control mb-2" placeholder="Opsi D">

                        <label for="jawaban_benar">Jawaban Benar</label>
                        <select name="soals[${soalIndex}][jawaban_benar]" class="form-control jawaban-benar">
                            <option value="">-- Pilih Jawaban Benar --</option>
                            <option value="Opsi A">Opsi A</option>
                            <option value="Opsi B">Opsi B</option>
                            <option value="Opsi C">Opsi C</option>
                            <option value="Opsi D">Opsi D</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-danger remove-soal mt-2">Hapus Soal</button>

                `;
                soalContainer.appendChild(soalItem);
                soalIndex++;
            });

            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('tipe-soal')) {
                    let parent = e.target.closest('.soal-item');
                    let pilihanGanda = parent.querySelector('.pilihan-ganda');
                    if (e.target.value === 'pilihan_ganda') {
                        pilihanGanda.style.display = 'block';
                    } else {
                        pilihanGanda.style.display = 'none';
                    }
                }
            });

            // Remove soal logic
            document.getElementById('soal-container').addEventListener('click', function(e) {
                if (e.target && e.target.classList.contains('remove-soal')) {
                    e.target.closest('.soal-item').remove();
                }
            });
        });
    </script>
@endpush
