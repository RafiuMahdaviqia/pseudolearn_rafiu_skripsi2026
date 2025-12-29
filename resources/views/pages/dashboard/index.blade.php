<?php 
    use App\Models\Mahasiswa;
    $user = auth()->user();
    $mahasiswa = Mahasiswa::where('id_user', $user->id)->first();
?>
@extends('layouts.main')

@push('styles')
<style>
    .achievement-img {
        width: 56px;
        height: 56px;
        object-fit: cover;
    }
</style>
@endpush

@php

@endphp

@section('content')
{{-- Dashboard Mahasiswa --}}
@unless($isAdmin)
@if(optional($mahasiswa)->open_panduan == 0)
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof openModalGuide === 'function') {
                openModalGuide();
            }
        });
    </script>
    @endpush
@endif
<div class="container-fluid px-4" id="dashboard-container">
    <div class="row">
        <div class="col-12 px-0">
            <div class="bg-white rounded-4 shadow-sm p-4">
                <div class="row">
                    <div class="col-md-3 d-flex justify-content-center align-items-center">
                        <div class="position-relative d-flex justify-content-center align-items-center" style="width: 120px; height: 120px;">
                            <img 
                                alt="Logo" 
                                src="{{ auth()->user()->avatar ? asset('assets/media/avatars/' . auth()->user()->avatar) : asset('assets/media/avatars/blank.png') }}" 
                                class="img-fluid rounded-3 object-fit-cover" 
                                style="width: 120px; height: 120px; object-fit: cover;"
                            />
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div>
                            <div class="fs-1 fw-bold mb-5">
                                <span style="color: #1a237e;">Selamat <span style="color: #ffb300;">Datang</span></span>
                                <img src="{{ asset('assets/media/icons-imports/wave.png') }}" alt="Wave" style="height: 28px; vertical-align: middle;">
                            </div>
                            <div class="mt-2 fs-5 gap-3">
                                <div class="row mb-3">
                                    <div class="col-3 fw-bold" style="max-width: 100px;">Nama</div>
                                    <div class="col-1" style="max-width: 20px;">:</div>
                                    <div class="col-8">{{ auth()->user()->name }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-3 fw-bold" style="max-width: 100px;">Email</div>
                                    <div class="col-1" style="max-width: 20px;">:</div>
                                    <div class="col-8">{{ auth()->user()->email }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-3 fw-bold" style="max-width: 100px;">Kelas</div>
                                    <div class="col-1" style="max-width: 20px;">:</div>
                                    <div class="col-8">
                                        @if($name_kelas)
                                            {{ $name_kelas }}
                                        @else
                                            <span class="text-danger">Tidak ada kelas</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-md-2 mt-5 pe-5">
                        <button  class="btn btn-info btn-sm px-3 shadow-sm"> 
                            <i class="ki-duotone ki-book fs-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                            Lihat Panduan
                        </button>
                    </div> -->
                </div>

                <div class="separator my-5"></div>

                <div class="mt-4 p-4 rounded-4 mb-7 border border-2" style="background: #f9fbfd;">
                    <div class="fw-bold fs-4 text-primary">Progres Belajar Kamu</div>
                    <div class="fs-6 text-dark mb-5">
                        Ayo tingkatkan kemampuan algoritmamu, kumpulkan Algopoin dan Algobadge sebanyak-banyaknya dan jadilah nomor 1 di leaderboard
                    </div>
                    <div class="row g-3">
                        <div class="col-md-3 col-6">
                            <div class="h-100 rounded-3 p-3" style="background: #2ec4b6;">
                                <div class="d-flex align-items-center mb-2">
                                    <img src="{{ asset('assets/media/img/star.png') }}" alt="Star" class="me-2" style="width: 20px;">
                                    <span class="fw-semibold fs-5 text-white">AlgoPoin</span>
                                </div>
                                <div class="fw-bold fs-1 text-white">{{ $algopoin ?? 0 }}</div>
                                <div class="text-white fs-6">AlgoPoin</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="h-100 rounded-3 p-3" style="background: #8f5fd4;">
                                <div class="d-flex align-items-center mb-2">
                                    <img src="{{ asset('assets/media/img/badge.png') }}" alt="badge" class="me-2" style="width: 20px;">
                                    <span class="fw-semibold fs-5 text-white">AlgoBadge</span>
                                </div>
                                <div class="fw-bold fs-1 text-white">{{ $algobadge ?? 0 }}</div>
                                <div class="text-white fs-6">AlgoBadge</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="h-100 rounded-3 p-3" style="background: #3b82c4;">
                                <div class="d-flex align-items-center mb-2">
                                    <img src="{{ asset('assets/media/img/leaderboard.png') }}" alt="Leaderboard" class="me-2" style="width: 20px;">
                                    <span class="fw-semibold fs-5 text-white">Leaderboard</span>
                                </div>
                                <div class="fw-bold fs-1 text-white">{{ $leaderboard['rank'] ?? 0 }}</div>
                                <div class="text-white fs-6">Peringkat</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="h-100 rounded-3 p-3" style="background: #ffe5ec;">
                                <div class="d-flex align-items-center mb-2">
                                    <img src="{{ asset('assets/media/img/heart.png') }}" alt="Life" class="me-2" style="width: 20px;">
                                    <span class="fw-semibold fs-5 text-danger">Nyawa</span>
                                </div>
                                <div class="fw-bold text-danger fs-1"><span id="lives-count">{{ $lives }}</span> / {{ $max_lives }}</div>
                                    @if($lives < $max_lives && $next_regen_at)
                                        <div class="text-danger fw-semibold fs-6" id="regen-timer" data-time="{{ $next_regen_at }}"></div>
                                    @else
                                        <div class="text-danger fw-semibold fs-6" id="regen-timer">Nyawa penuh</div>
                                    @endif
                                {{-- <div class="text-danger fs-6">Nyawa</div> --}}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="separator my-5"></div>
                
                <div class="row">
                    <div class="col-12 d-flex justify-content-between align-items-center px-5">
                        <h2 class="fw-bold fs-3 mb-0 text-dark">Pencapaian</h2>
                        <!-- <a href="#" class="fw-semibold text-info fs-7 text-decoration-none">Lihat Semua</a> -->
                        <a href="{{ route('pencapaian.index') }}" class="fw-semibold text-info fs-7 text-decoration-none">Lihat Semua</a>
                    </div>
                </div>
                {{-- Tambahkan di dalam dashboard, misal setelah judul "Pencapaian" --}}
                <div id="dashboard-pencapaian-list" class="mt-4 p-4 rounded-4 bg-gray-200 border achievement-list position-relative"></div>
            </div>
        </div>
    </div>
</div>
@endunless

{{-- Dashboard Admin/Dosen --}}
@if($isAdmin)
<div class="container-fluid px-4" id="dashboard-container">
    <div class="row">
        <div class="col-12 px-0">
            <div class="bg-white rounded-4 shadow-sm p-7">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold text-dark mb-0">Dashboard Dosen</h2>
                    <span class="text-muted small">Ringkasan statistik & aktivitas Mahasiswa</span>
                </div>
                <hr class="mt-0 mb-4 text-gray-500">
                <div class="row g-4 mb-5">
                    <div class="col-12 col-lg-12">
                        <div class="h-100 rounded-4 border p-4 border-2 border-gray-200">
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3 gap-3">
                                <h5 class="mb-0 fw-bold text-success me-sm-4 flex-shrink-0">Aktifitas Ujian</h5>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <div class="select2-fixed" style="width:150px;">
                                        <select id="filter-tahun-aktivitas" class="form-select form-select-sm" data-control="select2" data-placeholder="Filter Tahun" data-width="100%"></select>
                                    </div>
                                    <div class="select2-fixed" style="width:200px;">
                                        <select id="filter-bulan-aktivitas" class="form-select form-select-sm" data-control="select2" data-placeholder="Filter Bulan" data-width="100%"></select>
                                    </div>
                                    <div class="select2-fixed" style="min-width:200px; width:200px;">
                                        <select id="filter-kelas-aktivitas" class="form-select form-select-sm" data-control="select2" data-placeholder="Filter Kelas" data-width="100%"> </select>
                                    </div>
                                </div>
                            </div>
                            <style>
                                /* Paksa width karena Select2 menimpa inline width select asli */
                                .select2-fixed .select2-container { width:100% !important; }
                            </style>
                            {{-- <p class="mb-4 text-muted small">Deskripsi singkat konten card pertama untuk admin / dosen.</p> --}}
                            <div class="d-flex align-items-center my-8">
                                <canvas id="chart-aktivitas-ujian" class="mh-600px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-4 mb-5">
                    <div class="col-12 col-lg-6">
                        <div class="h-100 rounded-4 border p-4 border-2 border-gray-200">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0 fw-bold text-info">Clustering Labeling</h5>
                                <div class="ms-3" style="min-width:150px;">
                                    <select id="filter-kelas-labeling" class="form-select form-select-sm" data-control="select2" data-placeholder="Filter Kelas">
                                        {{-- <option value="">Semua Kelas</option> --}}
                                    </select>
                                </div>
                            </div>
                            {{-- <p class="mb-4 text-muted small">Deskripsi singkat konten card pertama untuk admin / dosen.</p> --}}
                            <div class="d-flex align-items-center my-8">
                                <canvas id="chart-labeling" class="mh-200px"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="h-100 rounded-4 border p-4 border-2 border-gray-200">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0 fw-bold text-info">Clustering Scoring</h5>
                                <div class="ms-3" style="min-width:150px;">
                                    <select id="filter-kelas-scoring" class="form-select form-select-sm" data-control="select2" data-placeholder="Filter Kelas">
                                        {{-- <option value="">Semua Kelas</option> --}}
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex align-items-center my-8">
                                <canvas id="chart-scoring" class="mh-200px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-4 mb-5">
                    <div class="col-12 col-lg-12">
                        <div class="h-100 rounded-4 border p-4 border-2 border-gray-200">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0 fw-bold text-primary">Confidence Tag</h5>
                                <div class="ms-3" style="min-width:250px;">
                                    <select id="filter-kelas-confidence" class="form-select form-select-sm" data-control="select2" data-placeholder="Filter Kelas">
                                        {{-- <option value="">Semua Kelas</option> --}}
                                    </select>
                                </div>
                            </div>
                            {{-- <p class="mb-4 text-muted small">Deskripsi singkat konten card pertama untuk admin / dosen.</p> --}}
                            <div class="d-flex align-items-center my-8">
                                <canvas id="chart-confidence" class="mh-600px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-4 mb-5">
                    <div class="col-12 col-lg-12">
                        <div class="h-100 rounded-4 border p-4 border-2 border-gray-200">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0 fw-bold text-primary">Data Mahasiswa Online</h5>
                            </div>
                            {{-- <p class="mb-4 text-muted small">Deskripsi singkat konten card pertama untuk admin / dosen.</p> --}}
                            <div class="d-flex align-items-center my-8">
                                <div class="w-100">
                                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                        <div class="small text-muted">
                                            Menampilkan mahasiswa aktif (aktivitas ≤ <span id="online-window">5</span> menit terakhir)
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <select id="online-minutes" class="form-select form-select-sm" style="width: auto;">
                                                <option value="5" selected>5 Menit</option>
                                                <option value="10">10 Menit</option>
                                                <option value="15">15 Menit</option>
                                                <option value="30">30 Menit</option>
                                                <option value="60">60 Menit</option>
                                            </select>
                                            <button id="refresh-online" class="btn btn-sm btn-light-primary">
                                                <i class="ki-duotone ki-arrows-circle fs-5"></i> Muat
                                            </button>
                                        </div>
                                    </div>
                                    <table class="table table-sm table-striped align-middle" id="table-mahasiswa-online">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width:40px;">#</th>
                                                <th>Nama</th>
                                                <th>Email</th>
                                                <th>Kelas</th>
                                                <th>Terakhir Aktif</th>
                                                <th>Selisih</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr><td colspan="6" class="text-center text-muted py-5">Memuat...</td></tr>
                                        </tbody>
                                    </table>
                                    <div class="small text-muted mt-2" id="online-summary"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
@if($isAdmin)
    <script src="{{ asset('js/dashboard/indexAdmin.js') }}"></script>
@endif
<script>
    // Contoh: Toggle class pada container saat sidebar dibuka/tutup
    document.addEventListener('DOMContentLoaded', function () {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const container = document.getElementById('dashboard-container');
        if (sidebarToggle && container) {
            sidebarToggle.addEventListener('click', function () {
                container.classList.toggle('container-fluid');
                container.classList.toggle('container');
            });
        }
    });
</script>

<script>
    var APP_URL = window.APP_URL || "/";
    const EXTRA_DELAY_MS = 5000;
    const FETCH_MAX_TRIES = 5; // batas percobaan gagal beruntun
    let fetchStatusAttempts = 0;

    // Rate limit: lebih dari 5x dalam 10 detik => tampilkan pesan sinkronisasi
    const MAX_CALLS_PER_WINDOW = 5;
    const RATE_LIMIT_WINDOW_MS = 10000;
    let fetchTimestamps = [];

    function updateTimerDisplay(text) {
        const timerEl = document.getElementById("regen-timer");
        if (timerEl) timerEl.innerText = text;
    }

    function isRateLimited() {
        const now = Date.now();
        // simpan timestamp
        fetchTimestamps.push(now);
        // hanya simpan yang masih dalam window
        fetchTimestamps = fetchTimestamps.filter(t => now - t <= RATE_LIMIT_WINDOW_MS);
        // jika lebih dari 5x (artinya ke-6) dalam 10 detik
        return fetchTimestamps.length > MAX_CALLS_PER_WINDOW;
    }

    async function fetchLivesStatus() {
        if (isRateLimited()) {
            updateTimerDisplay("Sedang sinkronisasi...");
            // jeda 60 detik sebelum boleh lagi
            setTimeout(() => {
                fetchTimestamps = [];
                fetchLivesStatus();
            }, 15000);
            return;
        }

        const livesEl = document.getElementById("lives-count");
        const timerEl = document.getElementById("regen-timer");

        if (fetchStatusAttempts >= FETCH_MAX_TRIES) {
            updateTimerDisplay("Sedang sinkronisasi...");
            setTimeout(() => {
                fetchStatusAttempts = 0;
                fetchLivesStatus();
            }, 15000);
            return;
        }

        fetchStatusAttempts++;

        try {
            const url = APP_URL + 'nyawa/status';
            const response = await fetch(url, {
                method: "GET",
                headers: {
                    "Accept": "application/json",
                    "X-Requested-With": "XMLHttpRequest"
                },
                credentials: "same-origin"
            });

            if (!response.ok) throw new Error("HTTP " + response.status);

            const data = await response.json();

            if (livesEl) livesEl.innerText = data.lives ?? 0;
            fetchStatusAttempts = 0; // reset jika sukses

            if (data.next_regen_at) {
                startCountdown(data.next_regen_at, EXTRA_DELAY_MS);
            } else if (timerEl) {
                timerEl.innerText = "Nyawa penuh";
            }
        } catch (err) {
            // console.error("Gagal ambil data:", err);
            if (fetchStatusAttempts < FETCH_MAX_TRIES) {
                setTimeout(fetchLivesStatus, 5000);
            } else {
                updateTimerDisplay("Sedang sinkronisasi, tunggu +- 1 menit");
                setTimeout(() => {
                    fetchStatusAttempts = 0;
                    fetchLivesStatus();
                }, 60000);
            }
        }
    }

    function startCountdown(endTime, extraMs = EXTRA_DELAY_MS) {
        const end = new Date(endTime).getTime() + extraMs;
        const timer = setInterval(function() {
            const now = Date.now();
            const distance = end - now;

            if (distance <= 0) {
                clearInterval(timer);
                fetchLivesStatus();
            } else {
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                updateTimerDisplay(minutes + "m " + seconds + "s");
            }
        }, 1000);
    }

    const regenDiv = document.getElementById("regen-timer");
    if (regenDiv && regenDiv.dataset.time) {
        startCountdown(regenDiv.dataset.time, EXTRA_DELAY_MS);
    }
</script>

<script>
    function renderDashboardPencapaianItem(item) {
        const status = Number(item.status ?? 0);
        const canClaim = status === 1;
        const claimed = status === 2;
        const notEligible = status === 0;
    
        const btnText = notEligible ? 'Claim' : (claimed ? 'Claimed' : 'Claim');
        const btnClass = notEligible ? 'btn-secondary' : (claimed ? 'btn-outline btn-outline-primary' : 'btn-success');
        const disabledAttr = canClaim ? '' : 'disabled';
    
        const max = Number(item.max_progress ?? 0);
        const prog = Number(item.progress ?? 0);
        const percent = Math.max(0, Math.min(100, max > 0 ? (prog / max) * 100 : 0));
    
        const imgSrc = item.img || '/assets/media/img/badge_sempurna.png';
        const heart = item.heart ?? '';
    
        return `
        <div class="d-flex align-items-center p-3 rounded-3 bg-white border achievement-item">
            <img src="${imgSrc}" alt="${item.name ?? ''}" class="achievement-img bg-light rounded-3" style="width: 70px; height: 70px;">
            <div class="flex-grow-1 ms-3 me-5">
                <div class="fw-bold text-primary-emphasis achievement-title fs-6">${item.name ?? ''}</div>
                <div class="small mb-2 text-dark achievement-desc">${item.desc ?? ''}</div>
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1 progress rounded-pill achievement-progress" role="progressbar" aria-valuenow="${Math.round(percent)}" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar ${claimed ? 'bg-primary' : 'bg-warning'}" style="width: ${percent}%;"></div>
                    </div>
                    <span class="ms-2 text-muted achievement-progress-text">${prog}/${max}</span>
                </div>
            </div>
            <button class="btn ${btnClass} ms-3 btn-sm d-flex align-items-center rounded-3 fw-semibold achievement-claim-btn flex-shrink-0 shadow-sm"
                    style="min-width: 120px;"
                    ${disabledAttr}
                    data-id="${item.id ?? ''}"
                    data-status="${status}"
                    ${notEligible ? 'title="Belum berhak untuk klaim"' : ''}>
                ${btnText} ${heart !== '' ? `<img src="/assets/media/img/heart.png" alt="Heart" class="mx-1" style="width: 18px;"> ${heart}x` : ''}
            </button>
        </div>
        `;
    }
    
    function loadDashboardPencapaian() {
        $.ajax({
            url: APP_URL + "dashboard/pencapaian-list",
            data: { limit: 5 }, // ambil maksimal 3 item
            type: "GET",
            success: function (response) {
                let html = '';
                if (response.length === 0) {
                    html = '<div class="text-center text-muted py-5">Belum ada pencapaian.</div>';
                } else {
                    response.forEach(item => {
                        html += renderDashboardPencapaianItem(item);
                    });
                }
                $("#dashboard-pencapaian-list").html(html);
            },
            error: function (xhr, status, error) {
                $("#dashboard-pencapaian-list").html('<div class="text-center text-danger py-5">Gagal memuat pencapaian.</div>');
            },
        });
    }
    
    $(document).ready(function() {
        loadDashboardPencapaian();
    });

    function claimDashboardPencapaian(id) {
        const $btn = $(`#dashboard-pencapaian-list .achievement-claim-btn[data-id="${id}"]`);
        let estimasiNyawa = null;
        if ($btn.length) {
            const txt = $btn.text();
            const m = txt.match(/(\d+)\s*x/i);
            if (m) estimasiNyawa = parseInt(m[1], 10);
        }

        let nyawaSekarang = parseInt($('#lives-count').text() || '0', 10);
        let maxNyawa = parseInt($('#max-lives-count').text() || '25', 10);

        let pesan = '';
        if (estimasiNyawa && (nyawaSekarang + estimasiNyawa) > maxNyawa) {
            pesan = `Nyawa Anda akan bertambah sebanyak ${estimasiNyawa}, namun maksimal nyawa adalah ${maxNyawa}. Nyawa Anda akan tetap ${maxNyawa} setelah klaim berhasil.`;
        } else {
            pesan = `Nyawa Anda akan bertambah${estimasiNyawa ? ' sebanyak ' + estimasiNyawa : ''} setelah klaim berhasil.`;
        }

        Swal.fire({
            title: 'Klaim pencapaian?',
            text: pesan,
            icon: 'question',
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: 'Ya, klaim',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-light'
            }
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: APP_URL + "pencapaian/claim",
                type: "POST",
                data: {
                    id: id,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    // Update nyawa di dashboard
                    $('#lives-count').text(response.lives);
                    // Jika ada max_lives, update juga
                    if (response.max_lives) $('#max-lives-count').text(response.max_lives);

                    Swal.fire({
                        text: (response.message || "Berhasil klaim pencapaian!") + ` Nyawa Anda bertambah${estimasiNyawa ? ' sebanyak ' + estimasiNyawa : ''}.`,
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        },
                    }).then(() => {
                        // Refresh data pencapaian
                        loadDashboardPencapaian();
                    });
                },
                error: function (xhr) {
                    Swal.fire({
                        text: xhr.responseJSON?.message || "Gagal klaim pencapaian.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        },
                    });
                }
            });
        });
    }

    // Event delegation untuk tombol claim di dashboard
    $(document).on('click', '#dashboard-pencapaian-list .achievement-claim-btn', function () {
        const id = $(this).data('id');
        const status = $(this).data('status');
        if (status !== 1) return; // hanya bisa claim jika status 1
        claimDashboardPencapaian(id);
    });
</script>

<script>
    (function() {
        const ONLINE_ENDPOINT = APP_URL + 'dashboard/mahasiswa-online';
        let onlineTimer = null;
    
        function renderMahasiswaOnline(json) {
            const tbody = document.querySelector('#table-mahasiswa-online tbody');
            if (!tbody) return;
            tbody.innerHTML = '';
    
            if (!json.data || json.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-5">Tidak ada mahasiswa online.</td></tr>';
                document.getElementById('online-summary').innerText = '0 mahasiswa aktif.';
                return;
            }
    
            json.data.forEach((row, idx) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="text-center">${idx + 1}</td>
                    <td>${row.name}</td>
                    <td>${row.email}</td>
                    <td>${row.kelas}</td>
                    <td><span class="badge badge-light-primary fw-semibold">${row.last_activity_at}</span></td>
                    <td>${row.last_activity_diff}</td>
                `;
                tbody.appendChild(tr);
            });
    
            document.getElementById('online-summary').innerText =
                `${json.count} mahasiswa aktif dalam ${json.minutes_window} menit terakhir.`;
        }
    
        async function loadMahasiswaOnline(showLoading = true) {
            const minutes = document.getElementById('online-minutes')?.value || 5;
            if (showLoading) {
                const tbody = document.querySelector('#table-mahasiswa-online tbody');
                if (tbody) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Memuat...</td></tr>';
                }
            }
            try {
                const res = await fetch(ONLINE_ENDPOINT + '?minutes=' + minutes, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin'
                });
                if (!res.ok) throw new Error(res.status);
                const json = await res.json();
                document.getElementById('online-window').innerText = json.minutes_window;
                renderMahasiswaOnline(json);
            } catch (e) {
                const tbody = document.querySelector('#table-mahasiswa-online tbody');
                if (tbody) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-4">Gagal memuat data.</td></tr>';
                }
            }
        }
    
        function startAutoRefresh() {
            if (onlineTimer) clearInterval(onlineTimer);
            onlineTimer = setInterval(() => loadMahasiswaOnline(false), 60000); // 60s
        }
    
        document.addEventListener('DOMContentLoaded', () => {
            const minutesSel = document.getElementById('online-minutes');
            const btnRefresh = document.getElementById('refresh-online');
    
            if (minutesSel) {
                minutesSel.addEventListener('change', () => {
                    loadMahasiswaOnline();
                    startAutoRefresh();
                });
            }
            if (btnRefresh) {
                btnRefresh.addEventListener('click', () => loadMahasiswaOnline());
            }
    
            loadMahasiswaOnline();
            startAutoRefresh();
        });
    })();
    // ...existing code...
</script>

@endpush
