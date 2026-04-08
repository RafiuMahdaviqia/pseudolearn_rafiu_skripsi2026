@extends('layouts.main')

@push('styles')

@endpush

@section('content')
<div class="container-fluid px-4" id="konversi-container">
    <div class="row">
        <div class="col-12 px-0">
            <div class="bg-white rounded-4 shadow-sm p-8">
                <div class="d-flex justify-content-between align-items-center mb-10">
                    @csrf
                    <div class="d-flex gap-2">
                        <input type="text" class="form-control form-control-sm w-250px" placeholder="Cari Soal Konversi" id="search-konversi" />
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <select class="form-select form-select-sm" id="filter-level" data-control="select2" data-hide-search="true" data-allow-clear="false">
                            @foreach($list_level as $level)
                                <option value="{{ $level['id'] }}">{{ $level['name'] }}</option>
                            @endforeach
                        </select>
                        <a href="{{ route('konversi.form') }}" class="btn btn-sm btn-primary d-flex align-items-center gap-1">
                                <i class="ki-outline ki-plus fs-5 d-flex align-items-center"></i>
                                <span>Tambah</span>
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped" id="table-konversi">
                        <thead>
                            <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                                <th class="text-center">No</th>
                                <th class="text-start">Level</th>
                                <th class="text-start">Soal</th>
                                <th class="text-start">Konversi</th>
                                <th class="text-start">Bobot</th>
                                <th class="text-start">Output</th>
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
    <script src="{{ asset('js/konversi/index.js') }}"></script>
@endpush


