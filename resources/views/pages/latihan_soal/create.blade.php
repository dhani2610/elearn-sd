@extends('layouts.main')
@section('title', 'List User')

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Tambah Latihan Soal</h4>
                        </div>
                        <div class="card-body">
                            @include('partials.alert')
                            <form action="{{ route('latihan_soal.store') }}" method="POST">
                                @csrf

                                <div class="form-group">
                                    <label for="kelas_id">Guru</label>
                                    <select id="kelas_id" name="id_guru"
                                        class="select2 form-control @error('id_guru') is-invalid @enderror">
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach ($guru as $g)
                                            <option value="{{ $g->id }}">{{ $g->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="kelas_id">Kelas</label>
                                    <select id="kelas_id" name="id_kelas"
                                        class="select2 form-control @error('id_kelas') is-invalid @enderror">
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach ($jadwal as $data)
                                            <option value="{{ $data->id }}">{{ $data->nama_kelas }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="kelas_id">Mata Pelajaran</label>
                                    <select id="kelas_id" name="id_pelajaran"
                                        class="select2 form-control @error('id_pelajaran') is-invalid @enderror">
                                        <option value="">-- Pilih Mata Pelajaran --</option>
                                        @foreach ($mapel as $m)
                                            <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="judul">Judul Latihan</label>
                                    <input type="text" name="judul" class="form-control" required>
                                </div>

                                <hr>

                                <h4>Tambah Soal</h4>
                                <div id="soal-container">
                                    <div class="soal-item mb-4">
                                        <label for="pertanyaan">Pertanyaan</label>
                                        <textarea name="soals[0][pertanyaan]" class="form-control" required></textarea>

                                        <label for="tipe_soal">Tipe Soal</label>
                                        <select name="soals[0][tipe_soal]" class="form-control tipe-soal" required>
                                            <option value="" disabled selected>Pilihan Tipe</option>
                                            <option value="pilihan_ganda">Pilihan Ganda</option>
                                            <option value="essay">Essay</option>
                                        </select>

                                        <div class="pilihan-ganda mt-2" style="display: none;">
                                            <label for="pilihan">Pilihan</label>
                                            <input type="text" name="soals[0][pilihan][]" class="form-control mb-2"
                                                placeholder="Opsi A">
                                            <input type="text" name="soals[0][pilihan][]" class="form-control mb-2"
                                                placeholder="Opsi B">
                                            <input type="text" name="soals[0][pilihan][]" class="form-control mb-2"
                                                placeholder="Opsi C">
                                            <input type="text" name="soals[0][pilihan][]" class="form-control mb-2"
                                                placeholder="Opsi D">

                                            <label for="jawaban_benar">Jawaban Benar</label>
                                            <select name="soals[0][jawaban_benar]" class="form-control jawaban-benar">
                                                <option value="">-- Pilih Jawaban Benar --</option>
                                                <option value="Opsi A">Opsi A</option>
                                                <option value="Opsi B">Opsi B</option>
                                                <option value="Opsi C">Opsi C</option>
                                                <option value="Opsi D">Opsi D</option>
                                            </select>
                                        </div>

                                        <!-- Remove button -->
                                        <button type="button" class="btn btn-danger remove-soal mt-2">Hapus Soal</button>
                                    </div>

                                </div>

                                <button type="button" id="add-soal" class="btn btn-success">Tambah Soal</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
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
            let soalIndex = 1;

            document.getElementById('add-soal').addEventListener('click', function() {
                let soalContainer = document.getElementById('soal-container');
                let soalItem = document.createElement('div');
                soalItem.classList.add('soal-item', 'mb-4');
                soalItem.innerHTML = `
            <label for="pertanyaan">Pertanyaan</label>
            <textarea name="soals[${soalIndex}][pertanyaan]" class="form-control" required></textarea>

            <label for="tipe_soal">Tipe Soal</label>
            <select name="soals[${soalIndex}][tipe_soal]" class="form-control tipe-soal" required>
                <option value="" disabled selected>Pilihan Tipe</option>
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

            <!-- Remove button -->
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
    <script type="text/javascript">
        $('.show_confirm').click(function(event) {
            var form = $(this).closest("form");
            var name = $(this).data("name");
            event.preventDefault();
            swal({
                    title: `Yakin ingin menghapus data ini?`,
                    text: "Data akan terhapus secara permanen!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        form.submit();
                    }
                });
        });
    </script>
@endpush
