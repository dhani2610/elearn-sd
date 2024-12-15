@extends('layouts.main')
@section('title', 'List User')

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>List Latihan Soal</h4>
                            <a href="{{ route('latihan_soal.create') }}" class="btn btn-primary"><i
                                    class="nav-icon fas fa-folder-plus"></i>&nbsp; Tambah Latihan Soal</a>
                        </div>
                        <div class="card-body">
                            @include('partials.alert')
                            <div class="table-responsive">
                                <table class="table table-striped" id="table-2">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Judul</th>
                                            <th>Mata Pelajaran</th>
                                            @if (Auth::user()->roles != 'guru')
                                            <th>Nilai</th>
                                            @endif
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($latihanSoals as $data)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $data->judul }}</td>
                                                <td>{{ $data->mapel->nama_mapel }}</td>
                                                @if (Auth::user()->roles != 'guru')
                                                <td>
                                                    @php
                                                        $cekskor = App\Models\JawabanLatihan::where(
                                                            'id_latihan_soal',
                                                            $data->id,
                                                        )
                                                            ->where('id_siswa', Auth::user()->id)
                                                            ->get();
                                                    @endphp
                                                    @if (count($cekskor) == 0)
                                                        0
                                                    @else
                                                        {{ $cekskor->sum('skor') }}
                                                    @endif
                                                </td>
                                                @endif
                                                <td>
                                                    <div class="d-flex">
                                                        @if (Auth::user()->roles == 'guru')
                                                            <a href="{{ route('cek-jawaban-latihan', $data->id) }}"
                                                                class="btn btn-primary btn-sm mr-2"><i
                                                                    class="nav-icon fas fa-edit"></i> &nbsp; Lihat Jawaban</a>

                                                            <a href="{{ route('latihan_soal.edit', $data->id) }}"
                                                                class="btn btn-success btn-sm"><i
                                                                    class="nav-icon fas fa-edit"></i> &nbsp; Edit</a>
                                                            <form method="POST"
                                                                action="{{ route('latihan_soal.destroy', $data->id) }}">
                                                                @csrf
                                                                @method('delete')
                                                                <button class="btn btn-danger btn-sm show_confirm"
                                                                    data-toggle="tooltip" title='Delete'
                                                                    style="margin-left: 8px"><i
                                                                        class="nav-icon fas fa-trash-alt"></i> &nbsp;
                                                                    Hapus</button>
                                                            </form>
                                                        @else
                                                            @if (count($cekskor) == 0)
                                                                <a href="{{ route('latihan.show', $data->id) }}"
                                                                    class="btn btn-success btn-sm"><i
                                                                        class="nav-icon fas fa-edit"></i> &nbsp; Jawab</a>
                                                            @else
                                                            Tidak Ada Aksi
                                                            @endif
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
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
