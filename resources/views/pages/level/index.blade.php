@extends('layouts.main')

@push('styles')
    <style>
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4" id="level-container">
        <div class="row">
            <div class="col-12">
                <div class="bg-white rounded-4 shadow-sm p-6 mb-5">
                    <div class="d-flex mb-5 mw-100 justify-content-between align-items-center">
                        <div class="mw-250px">
                            <input type="text" class="form-control form-control-sm" placeholder="Cari Level" id="search-level">
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('level.order') }}" class="btn btn-sm btn-outline btn-outline-info">
                                <i class="ki-outline ki-abstract-26 fs-2"></i>
                                Urutan Level
                            </a>
                            <a href="{{ route('level.form') }}" class="btn btn-sm btn-primary">
                                <i class="ki-outline ki-plus fs-2"></i>
                                Tambah Level
                            </a>
                        </div>
                    </div>
                    <table class="table table-striped" id="level-table">
                        <thead class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                            <tr>
                                <th class="text-center">Urutan</th>
                                <th>Logo</th>
                                <th>Nama</th>
                                <th>Feedback Tipe Data</th>
                                <th>Feedback Algoritma</th>
                                <th class="text-center">Manual Aktif</th>
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
@endsection

@push('scripts')
    <script src="{{ asset('js/level/index.js') }}"></script>
@endpush
