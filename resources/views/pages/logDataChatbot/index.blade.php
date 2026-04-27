@extends('layouts.main')

@push('styles')
<style>
    .form-select {
        width: 200px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4" id="log-data-chatbot-container">
    <div class="row">
        <div class="col-12 px-0">
            <div class="bg-white rounded-4 shadow-sm p-8">
                <div class="d-flex justify-content-between align-items-center mb-10">
                    <div class="d-flex gap-2">
                        <input type="text" class="form-control form-control-sm w-250px" placeholder="Cari Mahasiswa" id="search-mahasiswa" />
                    </div>
                    <div class="d-flex gap-3 align-items-center">
                        <select class="form-select form-select-sm" id="filter-kelas" data-control="select2"
                            data-hide-search="true" data-allow-clear="false">
                            <option value="">Semua Kelas</option>
                            @foreach ($list_kelas ?? [] as $kelas)
                                <option value="{{ $kelas['id'] }}">
                                    {{ $kelas['name'] }}
                                    @if (!empty($kelas['angkatan']))
                                        ({{ $kelas['angkatan'] }})
                                    @endif
                                </option>
                            @endforeach
                        </select>

                        <select class="form-select form-select-sm" id="filter-level" data-control="select2"
                            data-hide-search="true" data-allow-clear="true">
                            <option value="">Pilih Level</option>
                            @foreach ($list_level ?? [] as $level)
                                <option value="{{ $level['id'] }}">
                                    {{ $level['name'] }}
                                </option>
                            @endforeach
                        </select>

                        <select class="form-select form-select-sm" id="filter-soal" data-control="select2"
                            data-hide-search="true" data-allow-clear="true">
                            <option value="">Pilih Level dulu</option>
                        </select>

                        <button type="button" class="btn btn-success btn-sm" id="btn-export" onclick="exportExcel()">
                            <i class="ki-outline ki-file-up"></i>
                            Export
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped" id="table-log-data-chatbot">
                        <thead>
                            <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                                <th class="text-center">No</th>
                                <th class="text-start">NIM</th>
                                <th class="text-start">Nama</th>
                                <th class="text-center">Kelas</th>
                                <th class="text-center">Jumlah Buka Chatbot</th>
                                <th class="text-center">Jumlah Buka Chatbot Adaptive</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data akan diload via DataTables --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Detail --}}
<div class="modal fade" tabindex="-1" id="modal-detail-chatbot">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Detail Log Chatbot</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body">
                <div class="row mb-5">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">NIM</label>
                        <p id="detail-nim" class="text-gray-700">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama</label>
                        <p id="detail-nama" class="text-gray-700">-</p>
                    </div>
                </div>
                <div class="row mb-5">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Kelas</label>
                        <p id="detail-kelas" class="text-gray-700">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Total Akses Chatbot</label>
                        <p id="detail-total-chatbot" class="text-gray-700">-</p>
                    </div>
                </div>
                
                <div class="separator my-5"></div>
                
                <h5 class="fw-bold mb-4">Riwayat Akses Chatbot</h5>
                <div class="table-responsive">
                    <table class="table table-row-bordered" id="table-detail-chatbot">
                        <thead>
                            <tr class="fw-semibold fs-7 text-gray-800 border-bottom border-gray-200">
                                <th class="text-center">No</th>
                                <th class="text-center">Tipe Chatbot</th>
                                <th class="text-center">Level</th>
                                <th class="text-center">Waktu Akses</th>
                                <th class="text-center">Jenis Soal</th>
                                <th class="text-center">Durasi (Menit)</th>
                            </tr>
                        </thead>
                        <tbody id="detail-chatbot-body">
                            {{-- Data detail akan diload via AJAX --}}
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/logDataChatbot/index.js') }}"></script>
@endpush
