@extends('layouts.main')

@push('styles')
<style>
    .achievement-img {
        width: 56px;
        height: 56px;
        object-fit: cover;
    }

    .img-thumbnail img {
        max-width: 100%;
        max-height: 120px;
        width: auto;
        height: auto;
        display: block;
        margin: 0 auto;
        object-fit: contain;
    }
    .img-thumbnail {
        max-height: 140px;
        min-height: 120px;
        overflow: hidden;
        background: #fff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4" id="quiz-container">
    <div class="row">
        <div class="col-12 px-0">
            <div class="bg-white rounded-4 shadow-sm p-4">
                <div class="row g-5 mb-5">
                    <div class="col-md-3 col-6 mb-8">
                        <div class="h-100 rounded-3 p-3 border border-1 border-gray-200 bg-light-primary">
                            <div class="fw-bold fs-3 d-flex gap-3 align-items-center mb-4 text-primary">
                                <i class="ki-duotone ki-calendar text-primary fs-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Jam
                            </div>
                            <div class="fw-semibold fs-6 text-primary d-flex flex-column align-items-start">
                                {{-- <span>
                                    {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, j F Y') }}
                                </span> --}}
                                <span class="fw-semibold fs-6 text-primary" id="clock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-8">
                        <div class="h-100 rounded-3 p-3 border border-1 border-gray-200 bg-light-warning">
                            <div class="fw-bold fs-3 d-flex gap-3 align-items-center mb-4 text-warning">
                                <img src="{{ asset('assets/media/img/badge.png') }}" alt="badge" class="me-2" style="width: 20px;">
                                algobadge
                            </div>
                            <div class="fw-semibold fs-6 text-warning">{{ $algobadge ?? 0 }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-8">
                        <div class="h-100 rounded-3 p-3 border border-1 border-gray-200" style="background: #e0f2f1;">
                            <div class="fw-bold fs-3 d-flex gap-3 align-items-center mb-4" style="color: #14b8a6;">
                                <img src="{{ asset('assets/media/img/star.png') }}" alt="Total AlgoPoin" class="me-2" style="width: 20px;">
                                Total AlgoPoin
                            </div>
                            <div class="fw-semibold fs-6" style="color: #14b8a6;">{{ $algopoin ?? 0 }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-8">
                        <div class="h-100 rounded-3 p-3 border border-1 border-gray-200" style="background: #ffe4e6;">
                            <div class="fw-bold fs-3 d-flex gap-3 align-items-center mb-4" style="color: #e11d48;">
                                <img src="{{ asset('assets/media/img/heart.png') }}" alt="Heart" class="me-2" style="width: 20px;">
                                Nyawa
                            </div>
                            <div class="fw-semibold fs-6" style="color: #e11d48;"><span id="lives-count">{{ $lives }}</span> / {{ $max_lives }} 
                                @if($lives < $max_lives && $next_regen_at)
                                    <span class="ms-2" style="font-weight:400; color:#e11d48;" id="regen-timer" data-time="{{ $next_regen_at }}"></span>
                                @else
                                    <span class="ms-2" style="font-weight:400; color:#e11d48;" id="regen-timer">Nyawa Penuh</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- <div class="separator my-5"></div> --}}

                <div class="row">
                    <div class="col-12 px-5">
                        <h2 class="fw-bold fs-3 mb-1 text-primary-emphasis">List Level Materi</h2>
                        <h3 class="fw-semibold fs-5 mb-4 text-primary-emphasis">Capai maksimal <span class="text-warning">AlgoPoinmu</span> dan jadilah nomor <span class="text-warning">1 </span>di <span class="text-warning">Leaderboard!</span></h3>
                    </div>
                </div>

                @php
                    $levels = collect($dataLevel)->values();
                @endphp
                <div class="row my-2 px-3">
                    @forelse($levels as $i => $level)
                        @php
                            $img = asset($level['image'] ?? 'assets/media/img/placeholder.png');
                            $isLocked = $level['isLocked'] ?? true; // ambil status per level
                        @endphp

                        <div class="col-md-3 col-6 mb-8">
                            @if(!$isLocked)
                                <a href="{{ route('quiz.question-list', ['level' => $level['id']]) }}"
                                    class="card hover-elevate-up border border-1 border-gray-100 parent-hover bg-light-success h-100">
                            @else
                                <div class="card border bg-gray-200 h-100 position-relative">
                            @endif
                                <div class="card-body p-6 mb-7">
                                    <div class="img-thumbnail mx-auto rounded-3 overflow-hidden d-flex justify-content-center align-items-center" style="width: 100%; height: 100%;">
                                        <img src="{{ $img }}" alt="{{ $level['name'] }}" style="object-fit:cover;max-width:60%;">
                                        @if($isLocked)
                                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center"
                                                    style="background:rgba(0,0,0,0.5); border-radius: 8px;">
                                                <img src="{{ asset('assets/media/img/lock.png') }}" alt="Lock" style="max-width:40%;">
                                            </div>
                                        @endif
                                    </div>
                                    <span class="text-primary-emphasis fs-4 fw-bold mt-5 d-flex justify-content-center">
                                        Level {{ $level['order'] }}: {{ $level['name'] }}
                                    </span>
                                    <div class="mt-4 text-start d-flex flex-column gap-3 ms-3">
                                        <span class="text-primary-emphasis fs-7 fw-semibold">
                                            <img src="{{ asset('assets/media/img/star.png') }}" alt="Total AlgoPoin" class="me-2" style="width:20px;">
                                            AlgoPoin: {{ $level['algopoin'] ?? 0 }}
                                        </span>
                                        <span class="text-primary-emphasis fs-7 fw-semibold d-flex align-items-center">
                                            <i class="ki-outline ki-book fs-3 me-3 text-warning"></i>
                                            Soal Pseudocode: {{ $level['jumlahSoalPseudocode'] ?? 0 }}
                                        </span>
                                        <span class="text-primary-emphasis fs-7 fw-semibold d-flex align-items-center">
                                            <i class="ki-outline ki-book fs-3 me-3 text-warning"></i>
                                            Soal Konversi: {{ $level['jumlahSoalKonversi'] ?? 0 }}
                                        </span>
                                    </div>
                                </div>
                            @if(!$isLocked)
                                </a>
                            @else
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-warning">Belum ada level tersedia.</div>
                        </div>
                    @endforelse
                </div>

                {{-- <div class="row my-2 px-3">
                    <div class="col-md-3 col-6 mb-8">
                        <a href="{{ route('quiz.question-list') }}" class="card hover-elevate-up border border-1 border-gray-100 parent-hover bg-light-success">
                            <div class="card-body p-5">
                                <div class="img-thumbnail mx-auto rounded-3 overflow-hidden d-flex justify-content-center align-items-center" style="width: 100%; height: 100%;">
                                    <img src="{{ asset('assets/media/img/level-tipe-data.png') }}" alt="Top Image" style="object-fit: cover; max-width: 60%">
                                </div>
                                <span class="text-primary-emphasis fs-4 fw-bold mt-5 d-flex justify-content-center">
                                    Level 1: Tipe Data
                                </span>
                                <div class="mt-4 text-start d-flex flex-column gap-3 ms-3">
                                    <span class="text-primary-emphasis fs-7 fw-semibold">
                                        <img src="{{ asset('assets/media/img/star.png') }}" alt="Total AlgoPoin" class="me-2" style="width: 20px;">
                                        AlgoPoin: 100
                                    </span>
                                    <span class="text-primary-emphasis fs-7 fw-semibold d-flex align-items-center">
                                        <i class="ki-duotone ki-book fs-3 me-3 text-warning">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                        Jumlah Soal: 10
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-6 mb-8">
                        <a class="card border bg-gray-200">
                            <div class="card-body p-5">
                                <div class="img-thumbnail mx-auto rounded-3 overflow-hidden d-flex justify-content-center align-items-center position-relative" style="width: 100%; height: 100%;">
                                    <img src="{{ asset('assets/media/img/level-kondisi.png') }}" alt="Top Image" style="object-fit: cover; max-width: 60%; filter: brightness(0.5);">
                                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="background: rgba(0,0,0,0.5);">
                                        <img src="{{ asset('assets/media/img/lock.png') }}" alt="Lock" style="max-width:40%;">
                                    </div>
                                </div>
                                <span class="text-primary-emphasis fs-4 fw-bold mt-5 d-flex justify-content-center">
                                    Level 2: Kondisi
                                </span>
                                <div class="mt-4 text-start d-flex flex-column gap-3 ms-3">
                                    <span class="text-primary-emphasis fs-7 fw-semibold">
                                        <img src="{{ asset('assets/media/img/star.png') }}" alt="Total AlgoPoin" class="me-2" style="width: 20px;">
                                        AlgoPoin: 0
                                    </span>
                                    <span class="text-primary-emphasis fs-7 fw-semibold d-flex align-items-center">
                                        <i class="ki-duotone ki-book fs-3 me-3 text-warning">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                        Jumlah Soal: 5
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-6 mb-8">
                        <a class="card border bg-gray-200">
                            <div class="card-body p-5">
                                <div class="img-thumbnail mx-auto rounded-3 overflow-hidden d-flex justify-content-center align-items-center position-relative" style="width: 100%; height: 100%;">
                                    <img src="{{ asset('assets/media/img/level-perulangan.png') }}" alt="Top Image" style="object-fit: cover; max-width: 60%; filter: brightness(0.5);">
                                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="background: rgba(0,0,0,0.5);">
                                        <img src="{{ asset('assets/media/img/lock.png') }}" alt="Lock" style="max-width:40%;">
                                    </div>
                                </div>
                                <span class="text-primary-emphasis fs-4 fw-bold mt-5 d-flex justify-content-center">
                                    Level 3: Perulangan
                                </span>
                                <div class="mt-4 text-start d-flex flex-column gap-3 ms-3">
                                    <span class="text-primary-emphasis fs-7 fw-semibold">
                                        <img src="{{ asset('assets/media/img/star.png') }}" alt="Total AlgoPoin" class="me-2" style="width: 20px;">
                                        AlgoPoin: 0
                                    </span>
                                    <span class="text-primary-emphasis fs-7 fw-semibold d-flex align-items-center">
                                        <i class="ki-duotone ki-book fs-3 me-3 text-warning">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                        Jumlah Soal: 5
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-6 mb-8">
                        <a  class="card border bg-gray-200">
                            <div class="card-body p-5">
                                <div class="img-thumbnail mx-auto rounded-3 overflow-hidden d-flex justify-content-center align-items-center position-relative" style="width: 100%; height: 100%;">
                                    <img src="{{ asset('assets/media/img/level-fungsi.png') }}" alt="Top Image" style="object-fit: cover; max-width: 60%; filter: brightness(0.5);">
                                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="background: rgba(0,0,0,0.5);">
                                        <img src="{{ asset('assets/media/img/lock.png') }}" alt="Lock" style="max-width:40%;">
                                    </div>
                                </div>
                                <span class="text-primary-emphasis fs-4 fw-bold mt-5 d-flex justify-content-center">
                                    Level 4: Fungsi
                                </span>
                                <div class="mt-4 text-start d-flex flex-column gap-3 ms-3">
                                    <span class="text-primary-emphasis fs-7 fw-semibold">
                                        <img src="{{ asset('assets/media/img/star.png') }}" alt="Total AlgoPoin" class="me-2" style="width: 20px;">
                                        AlgoPoin: 0
                                    </span>
                                    <span class="text-primary-emphasis fs-7 fw-semibold d-flex align-items-center">
                                        <i class="ki-duotone ki-book fs-3 me-3 text-warning">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                        Jumlah Soal: 5
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-6 mb-8">
                        <a  class="card border bg-gray-200">
                            <div class="card-body p-5">
                                <div class="img-thumbnail mx-auto rounded-3 overflow-hidden d-flex justify-content-center align-items-center position-relative" style="width: 100%; height: 100%;">
                                    <img src="{{ asset('assets/media/img/level-array.png') }}" alt="Top Image" style="object-fit: cover; max-width: 60%; filter: brightness(0.5);">
                                    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="background: rgba(0,0,0,0.5);">
                                        <img src="{{ asset('assets/media/img/lock.png') }}" alt="Lock" style="max-width:40%;">
                                    </div>
                                </div>
                                <span class="text-primary-emphasis fs-4 fw-bold mt-5 d-flex justify-content-center">
                                    Level 5: Array
                                </span>
                                <div class="mt-4 text-start d-flex flex-column gap-3 ms-3">
                                    <span class="text-primary-emphasis fs-7 fw-semibold">
                                        <img src="{{ asset('assets/media/img/star.png') }}" alt="Total AlgoPoin" class="me-2" style="width: 20px;">
                                        AlgoPoin: 0
                                    </span>
                                    <span class="text-primary-emphasis fs-7 fw-semibold d-flex align-items-center">
                                        <i class="ki-duotone ki-book fs-3 me-3 text-warning">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                        Jumlah Soal: 5
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/quiz/index.js') }}"></script>
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
                console.error("Gagal ambil data:", err);
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
@endpush


