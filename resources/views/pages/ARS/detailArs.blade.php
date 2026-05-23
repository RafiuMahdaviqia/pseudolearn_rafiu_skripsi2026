@extends('layouts.main')

@section('content')
    <div class="container-fluid px-4" id="detail-ars-container">
        <div class="row">
            <div class="col-12">
                <div class="bg-white rounded-4 shadow-sm p-8 mb-5">
                    <div class="d-flex justify-content-between align-items-center mb-6">
                        <div class="d-flex gap-2">
                            <a href="{{ route('ars.index') }}" class="btn btn-sm btn-secondary ps-2">
                                <i class="ki-outline ki-left fs-2"></i>
                                Kembali
                            </a>
                        </div>
                        <div>
                            <select class="form-select form-select-sm w-150px" id="filter-level" data-control="select2"
                                data-hide-search="true" data-allow-clear="false">
                                <option value="">Semua Level</option>
                                @foreach ($list_level as $level)
                                    <option value="{{ $level['id'] }}">
                                        {{ $level['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h6 class="fw-bold mb-3">Informasi Mahasiswa</h6>
                        <div class="row">
                            <div class="col-md-6 col-12 mb-2">
                                <div class="bg-light-primary rounded-3 p-3 h-100">
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="fw-semibold me-2">Nama  :</span>
                                        <span>{{ $mahasiswa->name }}</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="fw-semibold me-2">NIM   :</span>
                                        <span>{{ $mahasiswa->nim }}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="fw-semibold me-2">Kelas :</span>
                                        <span>{{ $mahasiswa->kelas_name }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 col-12 mb-2">
                                <div class="bg-light-success rounded-3 p-3 h-100 text-center">
                                    <span class="fw-semibold fw-bold d-block mb-1">Total Waktu</span>
                                    <div class="fs-5 mt-3">{{ $totalWaktu ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-2 col-12 mb-2">
                                <div class="bg-light-warning rounded-3 p-3 h-100 text-center">
                                    <span class="fw-semibold fw-bold d-block mb-1">Total ARS</span>
                                    <div class="fs-5 mt-3">{{ $totalArs ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-2 col-12 mb-2">
                                <div class="bg-light-danger rounded-3 p-3 h-100 text-center">
                                    <span class="fw-semibold fw-bold d-block mb-1">Jumlah Soal Tambahan</span>
                                    <div class="fs-5 mt-3">{{ $jumlahSoalTambahan ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="idMahasiswa" value="{{ $mahasiswa->id }}">
                    <div class="table-responsive">
                        <table class="table table-striped" id="table-ars">
                            <thead>
                                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                                    <th class="text-center">No</th>
                                    <th class="text-center">Level</th>
                                    <th class="text-center">Soal Tambahan</th>
                                    <th class="text-center">Batch</th>
                                    <th class="text-center">Difficulty</th>
                                    <th class="text-center">Label Pseudo</th>
                                    <th class="text-center">Durasi Pseudo</th>
                                    <th class="text-center">Label Konversi</th>
                                    <th class="text-center">Durasi Konversi</th>
                                    <th class="text-center">Total Durasi</th>
                                    <th class="text-center">Tanggal</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/arsReport/detailArs.js') }}"></script>
<<<<<<< HEAD
@endpush
=======
@endpush
>>>>>>> new-pseudolearn-ars-dda-decoy
