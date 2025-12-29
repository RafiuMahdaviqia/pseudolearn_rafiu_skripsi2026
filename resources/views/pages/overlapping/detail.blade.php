@extends('layouts.main')

@push('styles')
<style>
    .form-select {
        width: 200px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4" id="overlapping-detail-container">
    <div class="row">
        <div class="col-12 px-0">
            <div class="bg-white rounded-4 shadow-sm p-8">
                <div class="d-flex justify-content-between align-items-center mb-10">
                    <div class="d-flex gap-2">
                        <a href="{{ route('overlapping.analysis', ['id' => $id_soal]) }}" class="btn btn-sm btn-secondary ps-2">
                            <i class="ki-outline ki-left fs-2"></i>
                            Kembali
                        </a>
                    </div>
                    <div class="d-flex gap-2 align-items-center ms-auto justify-content-end">
                        <input type="text" class="form-control form-control-sm" placeholder="Cari Mahasiswa" id="search-detail" />
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
                    </div>
                </div>

                <input type="hidden" name="id_soal" value="{{ $id_soal }}">
                <input type="hidden" name="index" value="{{ $index }}">
                <input type="hidden" name="type" value="{{ $type }}">
                <input type="hidden" name="value" value="{{ $value }}">
                <div class="table-responsive">
                    <table class="table table-striped " id="table-detail">
                        <thead>
                            <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                                <th class="text-center">No</th>
                                <th class="text-start">NIM</th>
                                <th class="text-start">Nama</th>
                                <th class="text-start">Kelas</th>
                                <th class="text-center">Index</th>
                                <th class="text-center">Tipe Data/Algoritma</th>
                                <th class="text-center">Status Jawaban</th>
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
    <script src="{{ asset('js/overlapping/detail.js') }}"></script>
@endpush


