@extends('layouts.main')

@push('styles')
<style>
    .form-select {
        width: 200px;
    }

    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }

    .form-select {
        width: 150px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4" id="labeling-container">
    <div class="row">
        <div class="col-12 px-0">
            <div class="bg-white rounded-4 shadow-sm p-8">
                <div class="mb-3">
                    <div class="p-2 fs-6 fw-normal bg-light rounded">
                        <span class="text-primary mb-1">
                            *Pilih Level terlebih dahulu untuk menampilkan nilai <strong>Pre-Test</strong> dan <strong>Post-Test</strong>.
                        </span>
                        <br>
                        <span class="text-warning">
                            *Pilih Kelas, Level, dan Soal terlebih dahulu untuk melihat Label.
                        </span>
                    </div>
                </div>
                <div class="d-flex justify-content-between mb-4 gap-3">
                    <div class="d-flex gap-2">
                        <input type="text" class="form-control form-control-sm w-200px" placeholder="Cari Mahasiswa" id="search-mahasiswa" />
                    </div>

                    {{-- filter select --}}
                    <div class="d-flex gap-3">
                        <select class="form-select form-select-sm" id="filter-kelas" data-control="select2"
                            data-hide-search="true" data-allow-clear="false" >
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
                            data-hide-search="true" data-allow-clear="true" >
                            <option value="">Pilih Level</option>
                            @foreach ($list_level as $level)
                                <option value="{{ $level['id'] }}">
                                    {{ $level['name'] }}
                                </option>
                            @endforeach
                        </select>

                        <select class="form-select form-select-sm" id="filter-soal" data-control="select2"
                            data-hide-search="true" data-allow-clear="true" >
                            <option value="">Pilih Level dulu</option>
                        </select>
                    </div>

                    {{-- tombol dipisah --}}
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary btn-sm" id="btn-calculate-manual" onclick="calculateManual()">
                            Kalkulasi Ulang
                        </button>
                        <button type="button" class="btn btn-success btn-sm" id="btn-export" onclick="exportExcel()">
                            <i class="ki-outline ki-file-up"></i>
                            Export
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped" id="table-labeling">
                        <thead>
                            <tr class="fw-semibold fs-7 text-gray-800 border-bottom border-gray-200">
                                <th class="text-center">No</th>
                                <th class="text-start">NIM</th>
                                <th class="text-start">Nama</th>
                                <th class="text-center">Kelas</th>
                                <th class="text-start ps-0">Pre-Test</th>
                                <th class="text-start ps-0">Post-Test</th>
                                {{-- <th class="text-center">Total Submit</th> --}}
                                <th class="text-center">Drag and Drop</th>
                                <th class="text-center">Total Waktu</th>
                                <th class="text-center">Label</th>
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
    <script src="{{ asset('js/labeling/index.js') }}"></script>
@endpush


