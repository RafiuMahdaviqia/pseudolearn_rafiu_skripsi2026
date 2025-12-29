@extends('layouts.main')

@push('styles')
<style>
    .form-select {
        width: 200px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4" id="confidence-container">
    <div class="row">
        <div class="col-12 px-0">
            <div class="bg-white rounded-4 shadow-sm p-8">
                <div class="d-flex justify-content-between align-items-center mb-10">
                    <div class="d-flex gap-2">
                        <input type="text" class="form-control form-control-sm w-250px" placeholder="Cari Mahasiswa" id="search-confidence" />
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <select class="form-select form-select-sm" id="filter-kelas" data-control="select2"
                            data-hide-search="true" data-allow-clear="false">
                            @foreach ($list_kelas as $kelas)
                                <option value="{{ $kelas['id'] }}">
                                    {{ $kelas['name'] }}
                                    @if (!empty($kelas['angkatan']))
                                        ({{ $kelas['angkatan'] }})
                                    @endif
                                </option>
                            @endforeach
                        </select>

                        <select class="form-select form-select-sm" id="filter-level" data-control="select2"
                            data-hide-search="true" data-allow-clear="true">
                            <option value="">Pilih Level</option>
                            @foreach ($list_level as $level)
                                <option value="{{ $level['id'] }}">
                                    {{ $level['name'] }}
                                </option>
                            @endforeach
                        </select>

                        <select class="form-select form-select-sm" id="filter-soal" data-control="select2"
                            data-hide-search="true" data-allow-clear="true">
                            <option value="">Pilih Level dulu</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped" id="table-confidence">
                        <thead>
                            <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                                <th class="fw-semibold text-center align-middle" rowspan="2">No</th>
                                <th class="fw-semibold text-start align-middle" rowspan="2">NIM</th>
                                <th class="fw-semibold text-start align-middle" rowspan="2">Nama</th>
                                <th class="fw-semibold text-start align-middle" rowspan="2">Kelas</th>
                                <th class="fw-semibold text-center text-primary ps-0" colspan="2">Yakin</th>
                                <th class="fw-semibold text-center text-danger ps-0" colspan="2">Tidak Yakin</th>
                                <th class="fw-semibold text-center align-middle" rowspan="2">Aksi</th>
                            </tr>
                            <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                                <th class="fw-semibold text-start">Benar</th>
                                <th class="fw-semibold text-start">Salah</th>
                                <th class="fw-semibold text-start">Benar</th>
                                <th class="fw-semibold text-start">Salah</th>
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
    <script src="{{ asset('js/confidence/index.js') }}"></script>
@endpush


