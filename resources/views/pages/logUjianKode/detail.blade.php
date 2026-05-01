@extends('layouts.main')

@section('content')
    <div class="container-fluid px-4" id="log-ujian-kode-detail-container">
        <div class="row">
            <div class="col-12">
                <div class="bg-white rounded-4 shadow-sm p-8 mb-5">
                    <div class="d-flex justify-content-between align-items-center mb-8">
                        <a href="{{ route('log-ujian-kode.index') }}" class="btn btn-sm btn-secondary ps-2">
                            <i class="ki-outline ki-left fs-2"></i>
                            Kembali
                        </a>
                        <div class="d-flex gap-3 align-items-center">
                            <select class="form-select form-select-sm w-175px" id="filter-level" data-control="select2"
                                data-hide-search="true" data-allow-clear="true" data-placeholder="Pilih Level">
                                <option value="">Pilih Level</option>
                                @foreach ($list_level as $lvl)
                                    <option value="{{ $lvl['id'] }}">{{ $lvl['name'] }}</option>
                                @endforeach
                            </select>
                            <select class="form-select form-select-sm w-175px" id="filter-soal" data-control="select2"
                                data-hide-search="true" data-allow-clear="true" data-placeholder="Pilih Soal">
                                <option value="">Pilih Level dulu</option>
                            </select>
                        </div>
                    </div>


                    <div class="mb-6">
                        <div class="row g-4">
                            <div class="col-md-6 col-12">
                                <div class="bg-light-primary rounded-4 p-4 h-100">
                                    <div class="d-flex align-items-center mb-5">
                                        <div
                                            class="symbol symbol-40px me-3 bg-white bg-opacity-50 rounded-circle
                                                d-flex align-items-center justify-content-center">
                                            <i class="ki-outline ki-user fs-2 text-primary"></i>
                                        </div>
                                        <div class="fw-bold text-gray-900">Informasi Mahasiswa</div>
                                    </div>
                                    <dl class="row mb-0">
                                        <dt class="col-2 text-gray-700">Nama</dt>
                                        <dd class="col-10 text-gray-900">{{ $mahasiswa?->name ?? '-' }}</dd>
                                        <dt class="col-2 text-gray-700">NIM</dt>
                                        <dd class="col-10 text-gray-900">
                                            @if (!empty($mahasiswa?->nim))
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
                                        <div
                                            class="symbol symbol-40px me-3 bg-white bg-opacity-50 rounded-circle
                                                d-flex align-items-center justify-content-center">
                                            <i class="ki-outline ki-code fs-2 text-info"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-gray-900">Detail Log Ujian Kode</div>
                                            <div class="text-gray-600 small">Level & Soal</div>
                                        </div>
                                    </div>
                                    <dl class="row mb-0">
                                        <dt class="col-4 text-gray-700">Level</dt>
                                        <dd class="col-8 text-gray-900" id="info-level">
                                            @if (!empty($level?->name))
                                                <span class="badge badge-primary">{{ $level->name }}</span>
                                            @else
                                                -
                                            @endif
                                        </dd>
                                        <dt class="col-sm-4 text-gray-600 fw-semibold">Nama Soal</dt>
                                        <dd class="col-sm-8 text-gray-900 mb-0" id="info-soal">-</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="idMahasiswa" value="{{ $id_user }}">
                    <input type="hidden" id="levelId" value="{{ $level->id ?? '' }}">
                    <input type="hidden" id="soalId" value="{{ $soal->id ?? '' }}">

                    <div class="table-responsive">
                        <table class="table table-striped" id="table-detail-log-ujian-kode">
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
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/logUjianKode/detail.js') }}"></script>
@endpush
