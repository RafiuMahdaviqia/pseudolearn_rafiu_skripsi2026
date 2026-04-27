@extends('layouts.main')

@push('styles')
<style>
    .form-select {
        width: 200px;
    }

    .log-chatbot-detail-modal .modal-dialog {
        max-width: 1200px;
    }

    .log-chatbot-detail-modal .modal-content {
        border-radius: 12px;
        overflow: hidden;
    }

    .detail-label-card {
        background: #f5f7fb;
        border-radius: 10px;
        padding: 12px 14px;
    }

    .detail-label-title {
        color: #8a93a5;
        font-size: 0.95rem;
        font-weight: 600;
        line-height: 1.2;
        margin-bottom: 6px;
    }

    .detail-label-value {
        color: #1f2a44;
        font-size: 1.85rem;
        font-weight: 700;
        line-height: 1.15;
        margin-bottom: 0;
    }

    .summary-card {
        background: #f5f7fb;
        border-radius: 10px;
        padding: 14px;
        display: flex;
        align-items: center;
        gap: 12px;
        min-height: 84px;
    }

    .summary-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .summary-icon-akses {
        background: #eaf3ff;
        color: #1f8be3;
    }

    .summary-icon-pesan {
        background: #e9f9f2;
        color: #2bb784;
    }

    .summary-meta {
        color: #8a93a5;
        font-size: 0.95rem;
        font-weight: 600;
        line-height: 1.2;
        margin-bottom: 6px;
    }

    .summary-value {
        color: #1f2a44;
        font-size: 2rem;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 0;
    }

    .list-badge-step {
        background: #e9f2ff;
        color: #3f79d0;
        font-weight: 700;
        border-radius: 8px;
        font-size: 0.8rem;
        min-width: 30px;
        display: inline-flex;
        justify-content: center;
    }

    .list-badge-duration {
        background: #efe8ff;
        color: #7a54d8;
        font-weight: 700;
        border-radius: 10px;
        font-size: 0.8rem;
        padding: 4px 10px;
        display: inline-block;
    }

    .list-badge-empty {
        background: #eef1f5;
        color: #8a93a5;
    }

    .detail-panel-hidden {
        display: none;
    }

    .detail-chat-shell {
        background: #edf1f7;
        border: 1px solid #dde3ef;
        border-radius: 12px;
        padding: 16px;
        height: 440px;
        overflow-y: auto;
    }

    .detail-chat-item {
        display: flex;
        flex-direction: column;
        margin-bottom: 12px;
    }

    .detail-chat-item.user {
        align-items: flex-end;
    }

    .detail-chat-item.bot {
        align-items: flex-start;
    }

    .detail-chat-bubble {
        max-width: 80%;
        border-radius: 14px;
        padding: 10px 14px;
        font-size: 0.92rem;
        line-height: 1.5;
        word-break: break-word;
    }

    .detail-chat-bubble.user {
        background: #0d4d93;
        color: #ffffff;
        border-bottom-right-radius: 6px;
    }

    .detail-chat-bubble.bot {
        background: #ffffff;
        color: #2b3445;
        border: 1px solid #d9e0ec;
        border-bottom-left-radius: 6px;
    }

    .detail-chat-bubble.system {
        background: #fff4d0;
        border: 1px solid #f0de9b;
        color: #59490b;
    }

    .detail-chat-time {
        color: #8a93a5;
        font-size: 0.78rem;
        margin-top: 5px;
        font-weight: 600;
    }

    .detail-chat-empty {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #8a93a5;
        font-weight: 600;
    }

    .detail-chat-context {
        background: #fff8dc;
        border: 1px solid #f0de9b;
        border-left: 4px solid #f1c232;
        border-radius: 10px;
        padding: 10px 12px;
        color: #59490b;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 12px;
    }

    .detail-chat-context-time {
        color: #897638;
        float: right;
        font-size: 0.78rem;
        font-weight: 700;
    }

    @media (max-width: 767.98px) {
        .form-select {
            width: 100%;
        }

        .detail-label-value {
            font-size: 1.35rem;
        }

        .summary-value {
            font-size: 1.45rem;
        }

        .detail-chat-shell {
            height: 360px;
        }

        .detail-chat-bubble {
            max-width: 95%;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4" id="log-chatbot-container">
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
                                <th class="text-start">Nama Mahasiswa</th>
                                <th class="text-center">Kelas</th>
                                <th class="text-start">Total Waktu</th>
                                <th class="text-center">Total Langkah</th>
                                <th class="text-center">Total Durasi</th>
                                <th class="text-center">Detail</th>
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
<div class="modal fade log-chatbot-detail-modal" tabindex="-1" id="modal-detail-chatbot">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Detail Log Chatbot</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body p-6">
                <div id="detail-log-panel">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="detail-label-card h-100">
                                <div class="detail-label-title">NIM</div>
                                <p id="detail-nim" class="detail-label-value">-</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label-card h-100">
                                <div class="detail-label-title">Nama</div>
                                <p id="detail-nama" class="detail-label-value fs-2">-</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label-card h-100">
                                <div class="detail-label-title">Kelas</div>
                                <p id="detail-kelas" class="detail-label-value">-</p>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-5">
                        <div class="col-md-6">
                            <div class="summary-card h-100">
                                <span class="summary-icon summary-icon-akses">
                                    <i class="ki-outline ki-user fs-2"></i>
                                </span>
                                <div>
                                    <div class="summary-meta">Total Akses Chatbot</div>
                                    <p id="detail-total-akses" class="summary-value">0</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="summary-card h-100">
                                <span class="summary-icon summary-icon-pesan">
                                    <i class="ki-outline ki-message-text-2 fs-2"></i>
                                </span>
                                <div>
                                    <div class="summary-meta">Total Pesan Chatbot</div>
                                    <p id="detail-total-pesan" class="summary-value">0</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                        <h4 class="fw-bold mb-0">Riwayat Akses Chatbot</h4>
                        <div class="text-muted fs-7">Rentang Akses: <span id="detail-waktu-akses">-</span></div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-row-bordered" id="table-detail-chatbot">
                            <thead>
                                <tr class="fw-semibold fs-7 text-gray-800 border-bottom border-gray-200">
                                    <th class="text-center">No</th>
                                    <th class="text-center">Level</th>
                                    <th class="text-center">Soal</th>
                                    <th class="text-center">Waktu Pengerjaan</th>
                                    <th class="text-center">Durasi Popup</th>
                                    <th class="text-center">Jumlah Langkah</th>
                                    <th class="text-center">Labeling</th>
                                    <th class="text-center">Total Pesan</th>
                                    <th class="text-center">Pesan</th>
                                </tr>
                            </thead>
                            <tbody id="detail-chatbot-body">
                                {{-- Data detail akan diload via AJAX --}}
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="detail-pesan-panel" class="detail-panel-hidden">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                        <div>
                            <h4 class="fw-bold mb-1">Detail Pesan Log Chatbot</h4>
                            <div class="text-muted fs-7">Access ID: <span id="detail-pesan-access-id">-</span></div>
                        </div>
                        <button type="button" class="btn btn-light-primary btn-sm" onclick="backToDetailLog()">
                            <i class="ki-outline ki-left fs-4 me-1"></i>Kembali
                        </button>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="detail-label-card h-100">
                                <div class="detail-label-title">NIM</div>
                                <p id="detail-pesan-nim" class="detail-label-value fs-4">-</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-label-card h-100">
                                <div class="detail-label-title">Waktu Akses</div>
                                <p id="detail-pesan-waktu-akses" class="detail-label-value fs-5">-</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-label-card h-100">
                                <div class="detail-label-title">Durasi</div>
                                <p id="detail-pesan-durasi" class="detail-label-value fs-4">-</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-label-card h-100">
                                <div class="detail-label-title">Total Pesan</div>
                                <p id="detail-pesan-total" class="detail-label-value">0</p>
                            </div>
                        </div>
                    </div>

                    <div id="detail-chat-context" class="detail-chat-context detail-panel-hidden"></div>

                    <div class="detail-chat-shell" id="detail-pesan-chatbot-body">
                        <div class="detail-chat-empty">Pilih sesi untuk melihat detail pesan.</div>
                    </div>
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
    <script src="{{ asset('js/logChatbot/index.js') }}?v={{ filemtime(public_path('js/logChatbot/index.js')) }}"></script>
@endpush
