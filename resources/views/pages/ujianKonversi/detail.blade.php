@extends('layouts.main')

@section('content')
    <div class="container-fluid px-4" id="analysis-container">
        <div class="row">
            <div class="col-12">
                <div class="bg-white rounded-4 shadow-sm p-8 mb-5">
                    <div class="d-flex justify-content-between align-items-center mb-6">
                        <div class="d-flex gap-2">
                            <a href="{{ route('ujian-konversi.index') }}" class="btn btn-sm btn-secondary ps-2">
                                <i class="ki-outline ki-left fs-2"></i>
                                Kembali
                            </a>
                        </div>
                        {{-- <div class="d-flex gap-2 align-items-center">
                            <input type="text" class="form-control form-control-sm w-250px" placeholder="Cari level" id="search-level" />
                        </div> --}}
                    </div>

                    <div class="mb-6">
                        <div class="row g-4">
                            <div class="col-md-6 col-12">
                                <div class="bg-light-primary rounded-4 p-4 h-100">
                                    <div class="d-flex align-items-center mb-5">
                                        <div class="symbol symbol-40px me-3 bg-white bg-opacity-50 rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="ki-outline ki-user fs-2 text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-gray-900">Informasi Mahasiswa</div>
                                            {{-- <div class="text-gray-600 small">Mahasiswa</div> --}}
                                        </div>
                                    </div>
                                    <dl class="row mb-0">
                                        <dt class="col-2 text-gray-700">Nama</dt>
                                        <dd class="col-10 text-gray-900">{{ $mahasiswa?->name ?? '-' }}</dd>
                                        <dt class="col-2 text-gray-700">NIM</dt>
                                        <dd class="col-10 text-gray-900">
                                            @if(!empty($mahasiswa?->nim))
                                                <span class="badge badge-primary">{{ $mahasiswa->nim }}</span>
                                            @else
                                                -
                                            @endif
                                        </dd>
                                        <dt class="col-2 text-gray-700">Kelas</dt>
                                        <dd class="col-10 text-gray-900">{{ $mahasiswa?->kelas_name ?? '-' }}</dd>
                                    </dl>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="bg-light-info rounded-4 p-4 h-100">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="symbol symbol-40px me-3 bg-white bg-opacity-50 rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="ki-outline ki-element-11 fs-2 text-info"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-gray-900">Detail Ujian</div>
                                            <div class="text-gray-600 small">Konversi & Soal</div>
                                        </div>
                                    </div>
                                    <dl class="row mb-0">
                                        <dt class="col-4 text-gray-700">Level</dt>
                                        <dd class="col-8 text-gray-900">
                                            @if(!empty($level?->name))
                                                <span class="badge badge-primary">{{ $level->name }}</span>
                                            @else
                                                -
                                            @endif
                                        </dd>
                                        <dt class="col-4 text-gray-700">Nama Soal</dt>
                                        <dd class="col-8 text-gray-900">{{ $soal?->judul ?? '-' }}</dd>
                                        <dt class="col-4 text-gray-700">Bobot Konversi</dt>
                                        <dd class="col-8 text-gray-900">
                                            @if(!empty($konversi?->bobot))
                                                <span class="badge badge-info">{{ $konversi->bobot }}</span>
                                            @else
                                                -
                                            @endif
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="idMahasiswa" value="{{ $mahasiswa->id }}">
                    <input type="hidden" id="soalId" value="{{ $soal->id ?? null}}">
                    <input type="hidden" id="levelId" value="{{ $level->id }}">
                    <div class="table-responsive">
                        <table class="table table-striped" id="table-detail-ujian-konversi">
                            <thead>
                                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                                    <th class="text-center">No</th>
                                    <th class="text-start">Soal</th>
                                    <th class="text-start">Tanggal Ujian</th>
                                    <th class="text-center">Nilai</th>
                                    <th class="text-center">Waktu</th>
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
    <script src="{{ asset('js/ujianKonversi/detail.js') }}"></script>
@endpush
