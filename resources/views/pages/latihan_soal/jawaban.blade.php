@extends('layouts.main')
@section('title', 'List User')

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4> Latihan Soal</h4>
                        </div>
                        <div class="card-body">
                            @include('partials.alert')
                            <div class="table-responsive">
                                <table class="table table-striped" id="table-2">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Siswa</th>
                                            <th>Nilai</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($siswaJawaban as $data)
                                        {{-- @dd($data) --}}
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    @php
                                                        $ceksis = App\Models\User::where(
                                                            'id',
                                                            $data['siswa_id'],
                                                        )->first();
                                                    @endphp
                                                    {{ $ceksis->name ?? '-' }}
                                                </td>
                                                <td>{{ $data['total_skor'] }}</td>

                                                <td>
                                                    <div class="d-flex">
                                                        <a href="{{ route('jawaban-latihan', ['id' => $data['id_latihan'] , 'id_siswa' => $data['siswa_id']]) }}"
                                                            class="btn btn-primary btn-sm mr-2"><i
                                                                class="nav-icon fas fa-edit"></i> &nbsp; Lihat Jawaban</a>
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
