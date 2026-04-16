@extends('layouts.main')

@push('styles')
<style>
    .form-select {
        width: 200px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4" id="banksoalkonversi-container">
    <div class="row">
        <div class="col-12 px-0">
            <div class="bg-white rounded-4 shadow-sm p-8">

                <div class="d-flex justify-content-between align-items-center mb-10">
                    <div class="d-flex gap-2">
                        <input
                            type="text"
                            class="form-control form-control-sm w-250px"
                            placeholder="Cari Soal"
                            id="search-bank-soal-konversi"
                        />

                        <select
                            class="form-select form-select-sm"
                            id="filter-level"
                            data-control="select2"
                            data-hide-search="true"
                        >
                            @foreach ($list_level as $level)
                                <option value="{{ $level['id'] }}">
                                    {{ $level['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="d-flex gap-2 align-items-center">
                        <a href="{{ route('bank-soal-konversi.order') }}"
                            class="btn btn-sm btn-info d-flex align-items-center gap-1">
                            <i class="ki-outline ki-abstract-26 fs-5"></i>
                            <span>Urutan Soal Level</span>
                        </a>

                        <a href="{{ route('bank-soal-konversi.form') }}"
                            class="btn btn-sm btn-primary d-flex align-items-center gap-1">
                            <i class="ki-outline ki-plus fs-5"></i>
                            <span>Tambah</span>
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped" id="table-bank-soal-konversi">
                        <thead>
                            <tr class="fw-semibold fs-6 text-gray-800 border-bottom">
                                <th class="text-center">No</th>
                                <th>Level</th>
                                <th>Soal</th>
                                <th>Jawaban</th>
                                <th>Output</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/bank-soal-konversi/index.js') }}"></script>
@endpush
