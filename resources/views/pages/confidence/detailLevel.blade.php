@extends('layouts.main')

@section('content')
    <div class="container-fluid px-4" id="analysis-container">
        <div class="row">
            <div class="col-12">
                <div class="bg-white rounded-4 shadow-sm p-8 mb-5">
                    <div class="d-flex justify-content-between align-items-center mb-6">
                        <div class="d-flex gap-2">
                            <a href="{{ route('confidence.detail', ['id' => $mahasiswa->id]) }}" class="btn btn-sm btn-secondary ps-2">
                                <i class="ki-outline ki-left fs-2"></i>
                                Kembali
                            </a>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="text" class="form-control form-control-sm w-250px" placeholder="Cari soal" id="search-soal" />
                        </div>
                    </div>

                    <div class="mb-6">
                        <h6 class="fw-bold mb-3">Informasi Mahasiswa</h6>
                        <div class="row">
                            <div class="col-md-4 col-12 mb-2">
                                <div class="bg-light rounded-3 p-3">
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="fw-semibold me-2">Nama:</span>
                                        <span>{{ $mahasiswa->name }}</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="fw-semibold me-2">NIM:</span>
                                        <span>{{ $mahasiswa->nim }}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="fw-semibold me-2">Kelas:</span>
                                        <span>{{ $mahasiswa->kelas_name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="idMahasiswa" value="{{ $mahasiswa->id }}">
                    <input type="hidden" id="idLevel" value="{{ $level->id }}">
                    <div class="table-responsive">
                        <table class="table table-striped" id="table-soal">
                            <thead>
                                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                                    <th class="text-center">No</th>
                                    <th class="text-start">Judul Soal</th>
                                    <th class="text-center">Yakin + Salah</th>
                                    <th class="text-center">Yakin + Benar</th>
                                    <th class="text-center">Tidak Yakin + Salah</th>
                                    <th class="text-center">Tidak Yakin + Benar</th>
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
    <script src="{{ asset('js/confidence/detailLevel.js') }}"></script>
@endpush
