@extends('layouts.main')

@push('styles')
    <style>
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4" id="kelas-container">
        <div class="row">
            <div class="col-7">
                <div class="bg-white rounded-4 shadow-sm p-6 mb-5">
                    <div class="d-flex mb-5 mw-100 justify-content-between align-items-center">
                        <div class="mw-200px">
                            <input type="text" class="form-control form-control-sm" placeholder="Cari Kelas" id="search-kelas">
                        </div>
                        <a href="{{ route('mahasiswa.index') }}" class="btn btn-sm btn-secondary ps-2">
                            <i class="ki-outline ki-left fs-2"></i>
                            Kembali
                        </a>
                    </div>
                    <table class="table table-striped" id="kelas-table">
                        <thead class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
                                <th>Nama</th>
                                <th>Angkatan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-5">
                <div class="bg-white rounded-4 shadow-sm p-7">
                    <h3 class="mb-8">Form Kelas</h3>
                    <form method="POST" action="" id="form-kelas">
                        <input type="hidden" name="id" id="id-kelas">
                        @csrf
                        <div class="fv-row mb-5">
                            <label for="nama" class="form-label fs-6 required">Nama Kelas</label>
                            <input type="text" class="form-control form-control-sm" id="nama" name="nama" placeholder="Masukkan nama kelas" required>
                        </div>
                        <div class="fv-row mb-10">
                            <label for="angkatan" class="form-label fs-6 required">Angkatan</label>
                            <select class="form-select form-select-sm" id="select-angkatan" name="select-angkatan" required></select>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-sm btn-secondary me-2" id="batal-kelas" onclick="resetForm()">Batal</button>
                            <button type="submit" class="btn btn-sm btn-primary" id="submit-form-kelas">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/kelas/index.js') }}"></script>
@endpush
