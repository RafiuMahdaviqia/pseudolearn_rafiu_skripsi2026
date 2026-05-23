@extends('layouts.main')

@push('styles')
<style> 
    .form-select {
        width: 200px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4" id="ars-report-container">
    <div class="row">
        <div class="col-12 px-0">
            <div class="bg-white rounded-4 shadow-sm p-8">
                <div class="d-flex justify-content-between align-items-center mb-10">
                    <div class="d-flex gap-2">
                        <a href="#" id="btn-export-ars" class="btn btn-success btn-sm">
                            <i class="ki-outline ki-file-down"></i>
                            Export Excel
                        </a>
                        <input type="text" class="form-control form-control-sm w-250px" placeholder="Cari Mahasiswa" id="search-ars" />
                    </div>
                    <div class="d-flex gap-3 align-items-center">
                        <select class="form-select form-select-sm"
                                id="filter-kelas"
                                data-control="select2"
                                data-hide-search="true"
                                data-allow-clear="false">
                            <option value="">Semua Kelas</option>
                            @foreach ($list_kelas as $kelas)
                                <option value="{{ $kelas['id'] }}">
                                    {{ $kelas['name'] }}
                                    @if (!empty($kelas['angkatan']))
                                        ({{ $kelas['angkatan'] }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped" id="table-ars">
                        <thead>
                            <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                                <th class="text-center">No</th>
                                <th class="text-start">NIM</th>
                                <th class="text-start">Nama</th>
                                <th class="text-center">Kelas</th>
                                <th class="text-center">Total ARS</th>
                                <th class="text-center">Jumlah Soal Tambahan</th>
                                <th class="text-center">Total Waktu</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/arsReport/index.js') }}"></script>
<<<<<<< HEAD
@endpush
=======
@endpush
>>>>>>> new-pseudolearn-ars-dda-decoy
