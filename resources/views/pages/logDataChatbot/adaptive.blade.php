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

        .adaptive-message-user {
            justify-content: flex-end;
        }

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

        .adaptive-bubble-user {
            background: linear-gradient(135deg, #0a3a71 0%, #1565c0 100%);
            color: #ffffff;
            border-radius: 16px 16px 4px 16px;
        }

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
            font-size: 0.82rem;
            font-weight: 700;
            opacity: 1;
            text-align: right;
            margin-top: 6px;
            letter-spacing: 0.2px;
        }

        .adaptive-message-user .adaptive-message-time {
            color: #f8fbff;
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.15);
        }

        .adaptive-message-bot .adaptive-message-time {
            color: #4b5563;
        }

        .adaptive-system-note {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 12px 16px;
            border-radius: 4px;
            margin: 8px 0;
            font-size: 0.95rem;
            color: #856404;
        }

        .adaptive-system-note-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 6px;
            font-weight: 700;
        }

        .adaptive-system-note-time {
            font-size: 0.82rem;
            font-weight: 800;
            color: #7c5b00;
            white-space: nowrap;
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
                        <div class="d-flex gap-2">
                            <input type="text" class="form-control form-control-sm w-250px" placeholder="Cari Mahasiswa"
                                id="search-mahasiswa" />
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

                            <button type="button" class="btn btn-success btn-sm" onclick="exportExcel()">
                                <i class="ki-outline ki-file-up"></i>
                                Export
                            </button>
                        </div>
                    </div>

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
                    <div class="row g-3 mb-6">
                        <div class="col-md-4">
                            <div class="bg-light rounded-2 px-4 py-3">
                                <p class="text-muted fs-7 mb-1">NIM</p>
                                <p id="detail-nim" class="fw-semibold fs-6 mb-0 text-gray-800">-</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light rounded-2 px-4 py-3">
                                <p class="text-muted fs-7 mb-1">Nama</p>
                                <p id="detail-nama" class="fw-semibold fs-6 mb-0 text-gray-800">-</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light rounded-2 px-4 py-3">
                                <p class="text-muted fs-7 mb-1">Kelas</p>
                                <p id="detail-kelas" class="fw-semibold fs-6 mb-0 text-gray-800">-</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="bg-light rounded-2 px-4 py-3 d-flex align-items-center gap-4">
                                <div
                                    class="w-40px h-40px rounded-2 bg-light-primary d-flex align-items-center justify-content-center flex-shrink-0">
                                    <i class="ki-outline ki-profile-user fs-2 text-primary"></i>
                                </div>
                                <div>
                                    <p class="text-muted fs-7 mb-1">Total Akses Chatbot Adaptive</p>
                                    <p id="detail-total-akses" class="fw-semibold fs-3 mb-0 text-primary">0</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-2 px-4 py-3 d-flex align-items-center gap-4">
                                <div
                                    class="w-40px h-40px rounded-2 bg-light-success d-flex align-items-center justify-content-center flex-shrink-0">
                                    <i class="ki-outline ki-message-text-2 fs-2 text-success"></i>
                                </div>
                                <div>
                                    <p class="text-muted fs-7 mb-1">Total Pesan Chatbot Adaptive</p>
                                    <p id="detail-total-pesan" class="fw-semibold fs-3 mb-0 text-success">0</p>
                                </div>
                            </div>
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
                                    <th class="text-center">Waktu Pengerjaan</th>
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
