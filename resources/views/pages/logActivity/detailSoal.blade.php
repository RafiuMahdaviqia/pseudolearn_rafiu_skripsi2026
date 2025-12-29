@extends('layouts.main')

@section('content')
    <div class="container-fluid px-4" id="detail-soal-container">
        <div class="row">
            <div class="col-12">
                <div class="bg-white rounded-4 shadow-sm p-8 mb-5">
                    <div class="d-flex justify-content-between align-items-center mb-6">
                        <div class="d-flex gap-2">
                            <a href="{{ route('log-activity.index') }}" class="btn btn-sm btn-secondary ps-2">
                                <i class="ki-outline ki-left fs-2"></i>
                                Kembali
                            </a>
                        </div>
                        {{-- <div class="d-flex gap-2 align-items-center">
                            <input type="text" class="form-control form-control-sm w-250px" placeholder="Cari Soal" id="search-soal" />
                        </div> --}}
                    </div>

                    <div class="mb-6">
                        <h6 class="fw-bold mb-3">Informasi Mahasiswa</h6>
                        <div class="row">
                            <div class="col-md-6 col-12 mb-2">
                                <div class="bg-light-primary rounded-3 p-3 h-100">
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
                            <div class="col-md-2 col-12 mb-2">
                                <div class="bg-light-success rounded-3 p-3 h-100 text-center">
                                    <span class="fw-semibold fw-bold d-block mb-1">Total Waktu</span>
                                    <div class="fs-5 mt-3">{{ $totalWaktu ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-2 col-12 mb-2">
                                <div class="bg-light-warning rounded-3 p-3 h-100 text-center">
                                    <span class="fw-semibold fw-bold d-block mb-1">Total Drag & Drop</span>
                                    <div class="fs-5 mt-3">{{ $totalDrag ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-md-2 col-12 mb-2">
                                <div class="bg-light-danger rounded-3 p-3 h-100 text-center">
                                    <span class="fw-semibold fw-bold d-block mb-1">Total Submit</span>
                                    <div class="fs-5 mt-3">{{ $totalSubmit ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="idMahasiswa" value="{{ $mahasiswa->id }}">
                    <input type="hidden" id="idSoal" value="{{ $soal->id ?? '' }}">
                    <input type="hidden" id="idLevel" value="{{ $level->id ?? '' }}">
                    <div class="table-responsive">
                        <table class="table table-striped" id="table-log">
                            <thead>
                                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                                    <th class="text-center">No</th>
                                    <th class="text-center">Confidence Tag</th>
                                    <th class="text-center">Status Jawaban</th>
                                    <th class="text-center">Waktu</th>
                                    <th class="text-center">Dibuat</th>
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
    <script src="{{ asset('js/logActivity/detailSoal.js') }}"></script>
@endpush
