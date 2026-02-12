@extends('layouts.main')

@push('styles')
<style>
    .form-select {
        width: 200px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4" id="log-activity-container">
    <div class="row">
        <div class="col-12 px-0">
            <div class="bg-white rounded-4 shadow-sm p-8">
                <div class="d-flex justify-content-between align-items-center mb-10">
                    <div class="d-flex gap-2">
                        <input type="text" class="form-control form-control-sm w-250px" placeholder="Cari Mahasiswa" id="search-confidence" />
                    </div>
                    <div class="d-flex gap-3 align-items-center">
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

                        <button type="button" class="btn btn-success btn-sm" id="btn-export" onclick="exportExcel()">
                            <i class="ki-outline ki-file-up"></i>
                            Export
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped" id="table-log-activity">
                        <thead>
                            <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                                <th class="text-center">No</th>
                                <th class="text-start">NIM</th>
                                <th class="text-start">Nama</th>
                                <th class="text-center">Kelas</th>
                                <th class="text-center">Drag and Drop</th>
                                <th class="text-center">Total Submit</th>
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
    <script src="{{ asset('js/logActivity/index.js') }}"></script>
@endpush


