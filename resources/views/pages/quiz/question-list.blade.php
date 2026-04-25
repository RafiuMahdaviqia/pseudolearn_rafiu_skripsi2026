<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <base href="" />
    <title>PseudoLearn</title>
    <meta charset="utf-8" />
    <meta name="description"
        content="Pseudolearn adalah aplikasi pembelajaran dasar pemrograman berbasis pseudocode yang dirancang untuk membantu siswa memahami konsep dasar pemrograman dengan cara yang interaktif." />
    <meta name="keywords"
        content="Pseudolearn, pembelajaran pemrograman, dasar pemrograman, pseudocode, aplikasi edukasi, belajar pemrograman, interaktif, siswa, pendidikan, teknologi pendidikan" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="Pseudolearn" />
    <meta property="og:url" content="https://pseudolearn.web.id" />
    <meta property="og:site_name" content="Pseudolearn" />
    <link rel="canonical" href="https://pseudolearn.web.id" />
    <link rel="shortcut icon" href="{!! asset('assets/media/logos/logo-polinema.ico') !!}" />
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700&display=swap" rel="stylesheet"
        type="text/css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="{!! asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') !!}" rel="stylesheet" type="text/css" />
    <link href="{!! asset('assets/plugins/custom/datatables/datatables.bundle.css') !!}" rel="stylesheet" type="text/css" />
    <link href="{!! asset('assets/plugins/global/plugins.bundle.css') !!}" rel="stylesheet" type="text/css" />
    <link href="{!! asset('assets/css/style.bundle.css') !!}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <script>
        // Frame-busting to prevent site from being loaded within a frame without permission (click-jacking) if (window.top != window.self) { window.top.location.replace(window.self.location.href); }
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            background: linear-gradient(135deg, #FFFEBA 0%, #FFC7B2 100%);
            background-repeat: no-repeat;
            background-attachment: fixed;
            /* background-size: cover;
            background-image: url("{{ asset('assets/media/img/bg-soal.webp') }}");
            background-position: center center;             */
        }

        .circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #03346E;
            color: #fff;
            font-weight: bold;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }

        .circle-left {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #F39C12;
            color: #fff;
            font-weight: bold;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }

        .how-it-works.row .col-2 {
            align-self: stretch;
            position: relative;
        }

        .how-it-works.row .col-2::after {
            content: "";
            position: absolute;
            border-left: 3px solid #03346E;
            z-index: 1;
        }

        .how-it-works.row .col-2.bottom::after {
            height: 50%;
            left: 50%;
            top: 50%;
        }

        .how-it-works.row .col-2.full::after {
            height: 100%;
            left: calc(50% - 1.5px);
        }

        .how-it-works.row .col-2.top::after {
            height: 50%;
            left: 50%;
            top: 0;
        }

        .timeline div {
            padding: 0;
            height: 40px;
        }

        .timeline hr {
            border-top: 3px solid #03346E;
            margin: 0;
            top: 17px;
            position: relative;
        }

        .timeline .col-2 {
            display: flex;
            overflow: hidden;
        }

        .timeline .corner {
            border: 3px solid #03346E;
            width: 100%;
            position: relative;
            border-radius: 15px;
        }

        .timeline .top-right {
            left: 50%;
            top: -50%;
        }

        .timeline .left-bottom {
            left: -49%;
            top: calc(50% - 3px);
        }

        .timeline .top-left {
            left: -50%;
            top: -50%;
        }

        .timeline .right-bottom {
            left: 49%;
            top: calc(50% - 3px);
        }

        .circle-left.done::after {
            color: blue;
        }

        .circle.locked,
        .circle-left.locked {
            background-color: #cccccc !important;
            color: #999999 !important;
            cursor: not-allowed;
        }

        .timeline.inactive hr,
        .timeline.inactive .corner {
            border-color: #cccccc !important;
        }

        .circle.done::after,
        .circle-left.done::after {
            content: "\f00c";
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 40px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            z-index: 3;
        }

        .circle-left.done::after {
            color: white;
        }

        .circle.done,
        .circle-left.done {
            color: #e8e8e8ff !important;
        }

        .how-it-works .col-6 {
            padding-left: 0 !important;
            padding-right: 0 !important;
            margin-left: -40px;
        }

        .how-it-works h5,
        .how-it-works p {
            margin-left: 0;
            margin-right: 0;
            margin-right: -40px;
        }

    </style>
</head>

<body id="kt_app_body" data-kt-app-header-fixed="true" data-kt-app-header-fixed-mobile="true"
    data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true"
    data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true"
    data-kt-app-sidebar-push-footer="true" class="app-default">
    <script>
        var defaultThemeMode = "light";
        var themeMode;
        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else {
                if (localStorage.getItem("data-bs-theme") !== null) {
                    themeMode = localStorage.getItem("data-bs-theme");
                } else {
                    themeMode = defaultThemeMode;
                }
            }
            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }
            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }
    </script>
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
                <div class="app-main flex-column flex-row-fluid" id="kt_app_main">

                    <div id="kt_docs_toast_stack_container" class="toast-container position-fixed top-0 end-0 p-3 z-index-3">

                    </div>

                    <div class="d-flex flex-column flex-column-fluid">
                        <div id="kt_app_content" class="app-content flex-column-fluid py-0">
                            <div id="kt_app_content_container" class="app-container container-fluid p-10">
                                <div class="container-fluid px-4" id="question-list-container">
                                    <div class="card bg-white p-4 ">
                                        <div class="row align-items-center">
                                            <div class="col-1 text-center px-0">
                                                <a href="{{ route('quiz.index') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Kembali ke Daftar Level">
                                                    <i class="ki-duotone ki-double-left fs-3x text-primary hover-elevate-up">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </a>
                                            </div>
                                            <div class="col-9 text-start ps-0">
                                                <h1 class="fw-bold fs-3 mb-1 text-dark">Level {{ $dataLevel->order . ' : ' . $dataLevel->name }}</h1>
                                                {{-- <h3 class="fw-semibold fs-7 text-dark">
                                                    Kondisi memungkinkan program memilih langkah berdasarkan pernyataan
                                                    benar atau salah.
                                                    Yuk pelajari cara kerja if dan if-else melalui studi drag and drop
                                                    yang kasus seru!
                                                </h3> --}}
                                            </div>
                                            <div class="col-2 d-flex justify-content-center px-0">
                                                <button class="btn btn-primary hover-elevate-up d-flex align-items-center" style="box-shadow: -2px 2px 8px #0000004d;" onclick="openModalGuide()">
                                                <img src="{{ asset('assets/media/img/iconbook.png') }}" class="sidebar-guide-icon me-2" style="height: 1.5em; width: auto;">
                                                Lihat Panduan
                                            </button>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" id="level_id" value="{{ $levelId ?? '' }}">
                                    @csrf
                                    <div class="row mt-8">
                                        <div class="col-9">
                                            <div class="container py-5">
                                                @php
                                                    $steps = $dataSoal ?? [];
                                                @endphp

                                                @foreach ($steps as $i => $step)
                                                    @php
                                                        $index = $i + 1;
                                                        $isEven = $index % 2 === 0;
                                                        $status = $step['status']; // done, active, locked
                                                        $circleClass = $isEven ? 'circle' : 'circle-left';
                                                        $timelineClass = ($loop->last || (isset($steps[$i+1]) && $steps[$i+1]['status'] === 'locked')) ? 'timeline inactive' : 'timeline';
                                                        $judul = $step['type'] === 'konversi' ? 'Konversi Program' : 'Pseudocode';
                                                        $deskripsi = $step['type'] === 'konversi'
                                                            ? (isset($step['soal']['judul']) ? $step['soal']['judul'] : (isset($step['judul']) ? $step['judul'] : ''))
                                                            : (isset($step['judul']) ? $step['judul'] : '');
                                                    @endphp

                                                    <div class="row align-items-center how-it-works d-flex {{ $isEven ? 'justify-content-end' : '' }}">
                                                        @if ($isEven)
                                                            <div class="col-6 d-flex flex-column align-items-end text-black mb-4 mt-6">
                                                                <h5 class="text-black">{{ $judul }}</h5>
                                                                <p>{{ $deskripsi }}</p>
                                                            </div>
                                                            <div class="col-2 text-center full d-inline-flex justify-content-center align-items-center text-black"
                                                                @if($status !== 'locked')
                                                                    @if($step['type'] === 'konversi')
                                                                        onclick="ujianKode('{{ $step['id'] }}')" style="cursor: pointer;"
                                                                    @endif
                                                                @endif>
                                                                <div class="{{ $circleClass }} {{ $status }}">
                                                                    @if($status === 'done')
                                                                    @else
                                                                        {{ $index }}
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="col-2 text-center full d-inline-flex justify-content-center align-items-center text-black"
                                                                @if($status !== 'locked')
                                                                    @if($step['type'] === 'soal')
                                                                        onclick="ujian('{{ $step['id'] }}')" style="cursor: pointer;"
                                                                    @endif
                                                                @endif>
                                                                <div class="{{ $circleClass }} {{ $status }}">
                                                                    @if($status === 'done')
                                                                    @else
                                                                        {{ $index }}
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="col-6 text-black mb-4 mt-6">
                                                                <h5 class="text-black">{{ $judul }}</h5>
                                                                <p>{{ $deskripsi }}</p>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    @if (!$loop->last)
                                                        <div class="row {{ $timelineClass }}">
                                                            @if ($isEven)
                                                                <div class="col-2">
                                                                    <div class="corner right-bottom"></div>
                                                                </div>
                                                                <div class="col-8">
                                                                    <hr style="opacity: 1;" />
                                                                </div>
                                                                <div class="col-2">
                                                                    <div class="corner top-left"></div>
                                                                </div>
                                                            @else
                                                                <div class="col-2">
                                                                    <div class="corner top-right"></div>
                                                                </div>
                                                                <div class="col-8">
                                                                    <hr style="opacity: 1;" />
                                                                </div>
                                                                <div class="col-2">
                                                                    <div class="corner left-bottom"></div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                @endforeach

                                            </div>
                                        </div>
                                        <div class="col-3 d-flex flex-column gap-4">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="h-100 rounded-3 p-3 border border-2 border-danger bg-light-danger">
                                                        <div class="fw-bold fs-3 d-flex gap-3 align-items-center mb-4 text-danger">
                                                            <img src="{{ asset('assets/media/img/heart.png') }}"
                                                                alt="Heart" class="me-2" style="width: 20px;">
                                                            Nyawa
                                                        </div>
                                                        <div class="d-flex flex-column justify-content-center align-items-center" style="min-height: 60px;">
                                                            <div class="d-flex align-items-end justify-content-center gap-3">
                                                                <div class="fw-bold text-danger" style="font-size: 2.5rem;"><span id="lives-count">{{ $lives }}</span> / {{ $max_lives }} </div>
                                                                @if($lives < $max_lives && $next_regen_at)
                                                                    <span class="fw-normal text-danger" style="font-size: 1.3rem;" id="regen-timer" data-time="{{ $next_regen_at }}"></span>
                                                                @else
                                                                    <span class="fw-normal text-danger" style="font-size: 1.3rem;" id="regen-timer">Nyawa Penuh</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="h-100 rounded-3 p-3 border border-2 border-success bg-light-success">
                                                        <div class="fw-bold fs-3 d-flex gap-3 align-items-center mb-4 text-success">
                                                            <img src="{{ asset('assets/media/img/star.png') }}"
                                                                alt="Total AlgoPoin" class="me-2"
                                                                style="width: 20px;">
                                                            AlgoPoin
                                                        </div>
                                                        <div class="fw-bold text-success d-flex justify-content-center align-items-center" style="font-size: 3rem; min-height: 30px;" id="total-algo-poin">
                                                            {{ $algopoin ?? 0 }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                             <div class="row">
                                                <div class="col-12">
                                                    <div class="h-100 rounded-3 p-5 border border-2 border-primary bg-light-primary">
                                                        <div class="fw-bold fs-3 d-flex gap-2 align-items-center mb-4 text-primary">
                                                            <i class="ki-duotone ki-medal-star fs-2x text-primary">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                                <span class="path4"></span>
                                                            </i>
                                                            Konversi Program
                                                        </div>
                                                        <div class="fw-bold text-primary" style="font-size: 1.1rem; min-height: 30px;" id="total-algo-poin">
                                                            @php
                                                                $list = $nilaiKonversiList ?? [];
                                                                $avg = count($list) ? round(array_sum($list) / count($list), 2) : 0;
                                                                $jumlahSoalKonversi = $jumlahSoalKonversi ?? 0;
                                                            @endphp

                                                            @if(count($list))
                                                                @if(count($list) == $jumlahSoalKonversi && $jumlahSoalKonversi > 0)
                                                                    <div class="mb-4 d-flex align-items-center gap-2 px-2">
                                                                        <i class="fa-solid fa-chart-line text-primary"></i>
                                                                        <span class="fw-semibold">Rata-rata Nilai:</span>
                                                                        <span class="fw-bold">{{ $avg }}</span>
                                                                    </div>
                                                                @endif
                                                                <ul class="mb-0 ps-2" style="list-style-type: disc;">
                                                                    @foreach ($list as $judul => $nilai)
                                                                        <li class="mb-1 d-flex justify-content-between align-items-center fs-7">
                                                                            <span class="fw-semibold">{{ $judul }}</span>
                                                                            <span class="fw-bold">{{ $nilai }}</span>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @else
                                                                <span class="text-muted">Belum ada nilai konversi</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="h-100 rounded-4 p-3 border border-3 border-info bg-light-info" style="background-color: #EFD0FB;">
                                                        <div class="fw-bold fs-3 d-flex gap-2 align-items-center mb-4 text-info" style="color: #8019A9;">
                                                            <img src="{{ asset('assets/media/img/badge.png') }}" alt="Badge Icon" class="me-2" style="width: 30px;">
                                                            AlgoBadge
                                                        </div>

                                                        <div class="row g-4">
                                                            @php
                                                                $labeledSoal = collect($dataSoal ?? [])
                                                                    ->filter(fn($s) => !empty($s['badge']))
                                                                    ->values();
                                                            @endphp

                                                            @if($labeledSoal->count())
                                                                @foreach($labeledSoal as $idx => $soal)
                                                                    @php
                                                                        $label = $soal['badge'];
                                                                        $map = [
                                                                            'Ideal' => 'assets/media/badge/ideal.png',
                                                                            'Struggling' => 'assets/media/badge/struggling.png',
                                                                            'Normal' => 'assets/media/badge/normal.png',
                                                                            'Gaming the System' => 'assets/media/badge/gaming.png',
                                                                        ];
                                                                        $img = asset($map[$label] ?? 'assets/media/badge/normal.png');
                                                                    @endphp
                                                                    <div class="col-6 text-center">
                                                                        <img src="{{ $img }}" alt="Soal {{ $loop->iteration }}" class="img-fluid" style="max-height: 100px;">
                                                                        <div class="fw-semibold mt-2 mb-2">
                                                                            Soal {{ $loop->iteration }}
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
    @extends('pages.guide.index')
    <script>
        var hostUrl = "assets/";
    </script>
    <script src="{!! asset('assets/plugins/global/plugins.bundle.js') !!}"></script>
    <script src="{!! asset('assets/js/scripts.bundle.js') !!}"></script>
    <script src="{!! asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.js') !!}"></script>
    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/radar.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/map.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/geodata/worldLow.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/geodata/continentsLow.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/geodata/usaLow.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/geodata/worldTimeZonesLow.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/geodata/worldTimeZoneAreasLow.js"></script>
    <script src="{!! asset('assets/plugins/custom/datatables/datatables.bundle.js') !!}"></script>
    <script src="{!! asset('assets/js/widgets.bundle.js') !!}"></script>
    <script src="{!! asset('assets/js/custom/widgets.js') !!}"></script>
    <script src="{!! asset('assets/js/custom/apps/chat/chat.js') !!}"></script>
    <script src="{!! asset('assets/js/custom/utilities/modals/upgrade-plan.js') !!}"></script>
    <script src="{!! asset('assets/js/custom/utilities/modals/users-search.js') !!}"></script>
    <script src="{!! asset('assets/plugins/custom/iconify/iconify-icon.min.js') !!}"></script>
    <script>
        function openModalGuide() {
            var modal = new bootstrap.Modal(document.getElementById('modal-guide'));
            modal.show();
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const container = document.getElementById('dashboard-container');
            if (sidebarToggle && container) {
                sidebarToggle.addEventListener('click', function() {
                    container.classList.toggle('container-fluid');
                    container.classList.toggle('container');
                });
            }
        });
    </script>

    <script>
        function toggleSidebarBookContainer() {
            var isMinimized = $('#kt_app_body').attr('data-kt-app-sidebar-minimize') === 'on';
            var $bookContainer = $('.sidebar-book-container');
            if (isMinimized) {
                $bookContainer.addClass('d-none');
            } else {
                $bookContainer.removeClass('d-none');
            }
        }

        $(document).ready(function() {
            toggleSidebarBookContainer();
            // Jika ada event perubahan pada sidebar, panggil lagi
            // Misal: jika ada event custom, ganti sesuai event yang digunakan
            $(document).on('sidebar:minimize sidebar:maximize', function() {
                toggleSidebarBookContainer();
            });
            // Atau pantau perubahan attribute secara langsung
            const observer = new MutationObserver(toggleSidebarBookContainer);
            observer.observe(document.getElementById('kt_app_body'), { attributes: true, attributeFilter: ['data-kt-app-sidebar-minimize'] });
        });

        // Tambahkan di dalam <script> tag pada bagian bawah file
        function ujian(id) {
            if ($('#lives-count').text().trim() === '0') {
                Swal.fire({
                    icon: 'error',
                    title: 'Nyawa Habis',
                    text: 'Maaf, nyawa Anda habis. Silakan tunggu hingga nyawa Anda terisi kembali.',
                });
                return;
            }

            window.location.href = "{{ route('ujian.index') }}?id=" + id;
        }

        function ujianKode(id) {
            if ($('#lives-count').text().trim() === '0') {
                Swal.fire({
                    icon: 'error',
                    title: 'Nyawa Habis',
                    text: 'Maaf, nyawa Anda habis. Silakan tunggu hingga nyawa Anda terisi kembali.',
                });
                return;
            }

            window.location.href = "{{ route('ujian-kode.index') }}?id=" + id;
        }
    </script>
    <script src="{{ asset('js/questionList/index.js') }}"></script>

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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const pencapaianId = urlParams.get('pencapaian_id');
        const badgeId = urlParams.get('badge_id');
        const konversiId = urlParams.get('konversi_id');
        const container = document.getElementById('kt_docs_toast_stack_container');

        // Jika kedua id null, tidak perlu request ajax
        if (!pencapaianId && !badgeId && !konversiId) return;

        // Jika salah satu ada, tetap request ajax
        $.ajax({
            url: APP_URL + "pencapaian/getById",
            type: "GET",
            data: { pencapaian_id: pencapaianId, badge_id: badgeId, konversi_id: konversiId },
            success: function (data) {
                // Fungsi untuk membuat toast baru
                function createToast({iconClass, title, desc, href}) {
                    const toastDiv = document.createElement('div');
                    toastDiv.className = "toast align-items-center border-0 mb-3";
                    toastDiv.setAttribute("role", "alert");
                    toastDiv.setAttribute("aria-live", "assertive");
                    toastDiv.setAttribute("aria-atomic", "true");
                    toastDiv.setAttribute("data-kt-docs-toast", "stack");
                    toastDiv.style.cursor = "pointer";
                    toastDiv.innerHTML = `
                        <div class="toast-header">
                            <i class="${iconClass} fs-2 me-3"></i>
                            <strong class="me-auto py-1">${title}</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                        <div class="toast-body">
                            <div class="mb-2">${desc}</div>
                        </div>
                    `;
                    if (href) {
                        toastDiv.addEventListener('click', function(e) {
                            // Hindari klik tombol close
                            if (!e.target.classList.contains('btn-close')) {
                                window.location.href = href;
                            }
                        });
                    }
                    container.appendChild(toastDiv);
                    const toast = bootstrap.Toast.getOrCreateInstance(toastDiv, { delay: 7000 });
                    toast.show();
                }

                // Tampilkan toast pencapaian jika ada
                if (data.pencapaian) {
                    createToast({
                        iconClass: "ki-solid ki-star text-warning",
                        title: data.badge.name,
                        desc: data.badge.desc,
                        href: APP_URL + "pencapaian?tab=soal",
                    });
                }

                // Tampilkan toast badge jika ada
                if (data.badge) {
                    createToast({
                        iconClass: "ki-solid ki-verify text-primary",
                        title: data.badge.name,
                        desc: data.badge.desc,
                        href: APP_URL + "pencapaian?tab=badge",
                    });
                }

                // Tampilkan toast konversi jika ada
                if (data.konversi) {
                    createToast({
                        iconClass: "ki-solid ki-arrow-mix text-info",
                        title: data.konversi.name,
                        desc: data.konversi.desc,
                        href: APP_URL + "pencapaian?tab=konversi",
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error("Error fetching achievement data:", error);
            }
        });
    });
    </script>
</html>
