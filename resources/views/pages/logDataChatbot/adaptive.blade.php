@extends('layouts.main')

@push('styles')
    <style>
        .form-select {
            width: 200px;
        }

        .adaptive-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            padding: 2px 10px;
            font-size: 0.9rem;
            font-weight: 700;
            line-height: 1.4;
            min-width: 24px;
        }

        .adaptive-pill--blue {
            background-color: #eaf1ff;
            color: #3b82f6;
        }

        .adaptive-pill--purple {
            background-color: #f3efff;
            color: #7c3aed;
        }

        .adaptive-pill--soft {
            background-color: #edf2f7;
            color: #60a5fa;
        }

        /* Chat Container Styles */
        .adaptive-chat-container {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
            max-height: 500px;
            overflow-y: auto;
        }

        .adaptive-message {
            display: flex;
            margin-bottom: 8px;
            animation: fadeInUp 0.3s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Student Message - Right Side */
        .adaptive-message-user {
            justify-content: flex-end;
        }

        /* Bot Message - Left Side */
        .adaptive-message-bot {
            justify-content: flex-start;
        }

        .adaptive-message-bubble {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 12px;
            word-wrap: break-word;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
        }

        /* Student Bubble - Blue */
        .adaptive-bubble-user {
            background: linear-gradient(135deg, #0a3a71 0%, #1565c0 100%);
            color: #ffffff;
            border-radius: 16px 16px 4px 16px;
        }

        /* Bot Bubble - White */
        .adaptive-bubble-bot {
            background: #ffffff;
            border: 1px solid #e0e0e0;
            color: #333333;
            border-radius: 16px 16px 16px 4px;
        }

        .adaptive-message-name {
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 4px;
            opacity: 0.9;
        }

        .adaptive-message-text {
            font-size: 0.95rem;
            line-height: 1.4;
            margin-bottom: 4px;
        }

        .adaptive-message-time {
            font-size: 0.75rem;
            opacity: 0.7;
            text-align: right;
        }

        .adaptive-message-user .adaptive-message-time {
            color: #ffffff;
        }

        .adaptive-message-bot .adaptive-message-time {
            color: #999999;
        }

        /* System Note */
        .adaptive-system-note {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 12px 16px;
            border-radius: 4px;
            margin: 8px 0;
            font-size: 0.95rem;
            color: #856404;
        }

        .adaptive-message-group {
            margin-bottom: 4px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4" id="log-chatbot-adaptive-container">
        <div class="row">
            <div class="col-12 px-0">
                <div class="bg-white rounded-4 shadow-sm p-8">

                    <div class="d-flex justify-content-between align-items-center mb-10">
                        {{-- 🔹 Cari Mahasiswa --}}
                        <div class="d-flex gap-2">
                            <input type="text" class="form-control form-control-sm w-250px"
                                placeholder="Cari Mahasiswa" id="search-mahasiswa" />
                        </div>

                        {{-- 🔹 Filter Kelas + Export --}}
                        <div class="d-flex gap-3 align-items-center">
                            <select class="form-select form-select-sm" id="filter-kelas"
                                data-control="select2"
                                data-hide-search="true"
                                data-allow-clear="false">
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

                            <button type="button" class="btn btn-success btn-sm" onclick="exportExcel()">
                                <i class="ki-outline ki-file-up"></i>
                                Export
                            </button>
                        </div>
                    </div>

                    {{-- 🔹 TABLE --}}
                    <div class="table-responsive">
                        <table class="table table-striped align-middle" id="table-log-chatbot-adaptive">
                            <thead>
                                <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                                    <th class="text-center">No</th>
                                    <th class="text-start">NIM</th>
                                    <th class="text-start">Nama</th>
                                    <th class="text-center">Kelas</th>
                                    <th class="text-center">Total Waktu</th>
                                    <th class="text-center">Total Langkah</th>
                                    <th class="text-center">Total Durasi</th>
                                    <th class="text-center">Detail</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Modal Detail Adaptive --}}
    <div class="modal fade" tabindex="-1" id="modal-detail-adaptive">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Detail Log Chatbot Adaptive</h3>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row g-5 mb-6">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">NIM</label>
                            <p id="detail-nim" class="mb-0 text-gray-700">-</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nama</label>
                            <p id="detail-nama" class="mb-0 text-gray-700">-</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kelas</label>
                            <p id="detail-kelas" class="mb-0 text-gray-700">-</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Total Akses Chatbot Adaptive</label>
                            <p id="detail-total-akses" class="mb-0 text-gray-700">0</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Total Pesan Chatbot Adaptive</label>
                            <p id="detail-total-pesan" class="mb-0 text-gray-700">0</p>
                        </div>
                    </div>

                    <div class="separator my-6"></div>

                    <h5 class="mb-4">Riwayat Akses Chatbot Adaptive</h5>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr class="fw-semibold fs-7 text-gray-800 border-bottom border-gray-200">
                                    <th class="text-center">No</th>
                                    <th class="text-center">Level</th>
                                    <th class="text-start">Soal</th>
                                    <th class="text-center">Waktu Akses</th>
                                    <th class="text-center">Durasi Popup</th>
                                    <th class="text-center">Jumlah Langkah</th>
                                    <th class="text-center">Labeling</th>
                                    <th class="text-center">Total Pesan</th>
                                    <th class="text-center">Pesan</th>
                                </tr>
                            </thead>
                            <tbody id="history-table-body">
                                <tr>
                                    <td colspan="9" class="text-center text-muted">Belum ada data</td>
                                </tr>
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

    {{-- Modal Pesan Adaptive --}}
    <div class="modal fade" tabindex="-1" id="modal-adaptive-messages">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Detail Pesan Chatbot Adaptive</h3>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body">
                    <div id="messages-container"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.APP_URL = window.APP_URL || '{{ url('/') }}/';
    </script>
    <script src="{{ asset('js/logDataChatbot/adaptive.js') }}"></script>
@endpush