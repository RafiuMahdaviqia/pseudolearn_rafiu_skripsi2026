@extends('layouts.main')

@push('styles')
    <style>

    </style>
@endpush

@section('content')
    <div class="container-fluid px-4" id="mahasiswa-container">
        <div class="row">
            <div class="col-12 px-0">
                <div class="bg-white rounded-4 shadow-sm p-8">
                    <div class="d-flex justify-content-between align-items-center mb-10">
                        <div class="d-flex gap-2">
                            <input type="text" class="form-control form-control-sm" placeholder="Cari mahasiswa" id="search-mahasiswa" />
                            {{-- <button class="btn btn-sm btn-outline d-flex align-items-center text-gray-700">
                                <i class="ki-duotone ki-filter-search fs-3 me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                Filter
                            </button> --}}
                            <select class="form-select form-select-sm" id="filter-kelas" data-control="select2" data-hide-search="true" data-allow-clear="false">
                                @foreach($dataKelas as $kelas)
                                    <option value="{{ $kelas['id'] }}">{{ $kelas['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('kelas.index') }}" class="btn btn-sm btn-outline btn-outline-info">
                                <i class="ki-outline ki-people"></i>
                                Data Kelas
                            </a>
                            <button class="btn btn-sm btn-outline btn-outline-success" onclick="openModalImport()">
                                <i class="ki-duotone ki-file-down fs-3 text-success">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Import
                            </button>
                            <button class="btn btn-sm btn-outline btn-outline-primary" onclick="exportMahasiswa()">
                                <i class="ki-duotone ki-file-up fs-3 text-primary">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Export
                            </button>
                            <button class="btn btn-sm btn-info" onclick="createMahasiswa()">
                                <i class="ki-duotone ki-user-edit fs-3 text-white">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                Tambah Mahasiswa
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped " id="table-mahasiswa">
                            <thead>
                                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                                    <th class="text-center">No</th>
                                    <th class="text-start">NIM</th>
                                    <th class="text-start">Nama</th>
                                    <th class="text-center px-0">Kelas</th>
                                    <th class="text-start">Email</th>
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

@include('pages.mahasiswa.modal')

@endsection

@push('scripts')
    <script src="{{ asset('js/mahasiswa/index.js') }}"></script>
@endpush
