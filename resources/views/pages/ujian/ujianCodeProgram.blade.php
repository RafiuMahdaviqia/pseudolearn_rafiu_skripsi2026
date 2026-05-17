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
            background: #f5f5f5;
        }

        .input-panel {
            border: 4px solid #022349;
            border-radius: 12px;
            background-color: #CDD6E2;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .input-header {
            background-color: #0a3a71;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .input-body {
            padding: 20px;
        }

        .button-group {
            display: flex;
            justify-content: space-between;
            padding: 20px;
        }

        .btn-reload {
            background-color: #FF5C00;
            border: 2px solid #AA3D00;
            color: white;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 10px;
        }

        .panel-box {
            background-color: #CDD6E2;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow: hidden;
            border: 4px solid #022349;
        }

        .panel-header {
            background-color: #0a3a71;
            color: white;
            font-weight: bold;
            text-align: center;
            padding: 12px;
            font-size: 1.2rem;
        }

        .panel-body {
            padding: 1.2rem 1rem 1.2rem;
        }

        .step-title {
            font-weight: bold;
            margin-bottom: 4px;
        }

        .code-box {
            background-color: #0a3a71;
            color: white;
            padding: 10px 16px;
            border-radius: 8px;
            margin-bottom: 10px;
            text-align: center;
        }

        .code-box-input {
            background-color: #0a3a71;
            color: white;
            padding: 10px 16px;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .is-invalid {
            border: 2px solid red !important;
            background-color: #ffe6e6;
        }

        /* Tambahkan di bagian <style> */
        .heart-beat {
            animation: heartBeat 1s infinite;
        }

        @keyframes heartBeat {
            0% { transform: scale(1); }
            10% { transform: scale(1.1); }
            20% { transform: scale(1.2); }
            30% { transform: scale(1.1); }
            40% { transform: scale(1); }
            100% { transform: scale(1); }
        }

    </style>
</head>

<body id="kt_app_body" data-kt-app-header-fixed="true" data-kt-app-header-fixed-mobile="true"
    data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true"
    data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true"
    data-kt-app-sidebar-push-footer="true" class="app-default">
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
                            <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                    <div class="d-flex flex-column flex-column-fluid">
                        <div id="kt_app_content" class="app-content flex-column-fluid py-0">
                            <div id="kt_app_content_container" class="app-container container-fluid p-10">
                                <div class="container card-container">
                                    <div class="mb-8">
                                        <div class="card rounded-bottom shadow p-0 border-0" style="background-color: #0a3a71; border-radius: 1rem 1rem 0 0;">
                                            <div class="d-flex justify-content-between align-items-center px-4 py-3" style="min-height: 60px; background-color: #0a3a71; border-radius: 12px;">
                                                <button class="btn btn-danger btn-sm d-flex align-items-center px-3 py-2 fw-semibold" style="font-size: 0.95rem;" onclick="back('{{ $soal->id_level }}')">
                                                    <i class="bi bi-arrow-left me-2"></i> Kembali
                                                </button>
                                                @csrf
                                                <!-- Ubah nilai awal timer di HTML -->
                                                <div class="flex-grow-1 text-center text-white fw-bold" style="font-size: 1.3rem;">
                                                    Waktu Pengerjaan <span class="fw-bolder" id="timer-ujian">00:00:00</span>
                                                    <input type="hidden" id="waktu-ujian-detik" name="waktu" value="0" />
                                                </div>
                                                <div class="d-flex align-items-center gap-7">
                                                    <button class="btn btn-primary btn-sm d-flex align-items-center px-3 py-2 fw-semibold" style="font-size: 0.95rem; box-shadow: -2px 2px 8px #0000004d;" onclick="openModalGuide()">
                                                        <img src="{{ asset('assets/media/img/iconbook.png') }}" class="me-2" style="height: 1.3em;"> Lihat Panduan 
                                                    </button>
                                                    <div class="d-flex align-items-center" style="color: white; font-weight: bold; font-size: 1.2rem;">
                                                        <img class="heart-beat" src="{{ asset('assets/media/img/heart.png') }}" alt="Heart" style="height: 2rem; margin-right: 6px;" />
                                                        <span id="lives-count">{{ $lives }}</span> / {{ $max_lives }} 
                                                    </div>
                                                </div>
                                            </div>

                                            <input type="hidden" id="id-soal-konversi" value="{{ $konversi->id }}">
                                            <div class="bg-white rounded-bottom p-8 fs-6">
                                                    {!! $soal['soal'] !!}
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" id="id-level" value="{{ $soal->id_level }}">
                                    <input type="hidden" id="id-soal" value="{{ $soal->id }}">
                                    <div class="row mb-4">
                                        <div class="col-md-5">
                                            {{-- <div class="panel-box">
                                                <div class="panel-header">Tipe Data</div>
                                                <div class="panel-body">
                                                    @php
                                                        $tipeDataList = collect(json_decode($soal['kunci_tipe_data'], true));
                                                    @endphp

                                                    @foreach($tipeDataList as $item)
                                                        @if(!empty($item['variabel']))
                                                            <div class="code-box">
                                                                {{ $item['variabel'] }} : {{ $item['tipe_data'] ?? '-' }}
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div> --}}

                                            <div class="panel-box">
                                                <div class="panel-header">Pseudocode</div>
                                                <div class="panel-body">
                                                    @php
                                                        $tipeDataList = collect(
                                                            is_array($soal['kunci_tipe_data'])
                                                                ? $soal['kunci_tipe_data']
                                                                : json_decode($soal['kunci_tipe_data'], true)
                                                        );

                                                        $algoritmaList = collect(
                                                            is_array($soal['kunci_algoritma'])
                                                                ? $soal['kunci_algoritma']
                                                                : json_decode($soal['kunci_algoritma'], true)
                                                        );
                                                        $dataLangkah = 1;

                                                        // Filter algoritma yang punya data konversi = 1 (field bisa 'data_konversi' atau 'konversi')
                                                        $algoritmaTerpilih = $algoritmaList->filter(function($row){
                                                            if (isset($row['data_konversi'])) return (int)$row['data_konversi'] === 1;
                                                            if (isset($row['konversi'])) return (int)$row['konversi'] === 1;
                                                            return false;
                                                        });
                                                    @endphp

                                                    {{-- Langkah: Tipe Data --}}
                                                    @foreach($tipeDataList as $item)
                                                        @if(!empty($item['variabel']))
                                                            <div class="step-title">Langkah {{ $dataLangkah }}</div>
                                                            <div class="code-box">
                                                                {{ $item['variabel'] }} : {{ $item['tipe_data'] ?? '-' }}
                                                            </div>
                                                            @php $dataLangkah++; @endphp
                                                        @endif
                                                    @endforeach

                                                    {{-- Langkah: Algoritma dengan konversi = 1 --}}
                                                    @foreach($algoritmaTerpilih as $item)
                                                        <div class="step-title">Langkah {{ $dataLangkah }}</div>
                                                        <div class="code-box">
                                                            {{ $item['langkah'] }}
                                                        </div>
                                                        @php $dataLangkah++; @endphp
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-7">
                                            <div class="input-panel">
                                                <div class="input-header">Input Kode Java</div>
                                                <div class="input-body">
                                                <div class="code-box-input">Public class HitungBatasUmur{</div>
                                                <div class="code-box-input">Public static void main(String[] args) {</div>
                                                @php
                                                    $jawabanList = collect(
                                                        is_array($konversi['jawaban'])
                                                            ? $konversi['jawaban']
                                                            : json_decode($konversi['jawaban'], true)
                                                    );
                                                    $totalLangkah = $jawabanList->count();
                                                @endphp

                                                @for($i = 1; $i <= $totalLangkah; $i++)
                                                    <div class="mb-2"><strong>Langkah {{ $i }}</strong></div>
                                                    <input type="text" class="form-control mb-3" placeholder="Masukan kode java untuk langkah ini">
                                                @endfor

                                                <div class="code-box-input">}</div>
                                                <div class="code-box-input">}</div>
                                                </div>

                                                 <div class="d-flex justify-content-between px-4 pb-3">
                                                        <button class="btn fw-bold px-4 py-2 rounded-3 d-flex align-items-center gap-2"
                                                            style="background-color: #FF5C00; border: 2px solid #AA3D00; color: white;" onclick="reloadUjian()">
                                                            <i class="bi bi-arrow-clockwise text-white fs-5"></i> Reload
                                                        </button>
                                                        <button class="btn fw-bold px-4 py-2 rounded-3 d-flex align-items-center gap-2"
                                                            style="background-color: #22BB33; border: 2px solid #177D22; color: white;" onclick="openModalKonfirmasi()">
                                                        <i class="bi bi-check-circle text-white fs-5"></i> Cek Jawaban
                                                    </button>
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
    @extends('pages.ujian.modal')
    
    {{-- Chatbot Component --}}
    @include('components.chatbot', [
        'id_soal' => $soal->id ?? null,
        'id_level' => $soal->id_level ?? null
    ])
    <script>
        var hostUrl = "assets/";
    </script>
    <script src="{{ asset('js/ujian/indexCodeProgram.js') }}"></script>
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
        // ====== Timer logic (count up) ======
        let timerInterval = null;
        let timerStarted = false;
        let elapsedSeconds = 0;

        // expose ke global
        window.waktuUjianDetik = 0;

        function formatHHMMSS(sec) {
            sec = Math.max(0, sec | 0);
            const h = String(Math.floor(sec / 3600)).padStart(2, '0');
            const m = String(Math.floor((sec % 3600) / 60)).padStart(2, '0');
            const s = String(sec % 60).padStart(2, '0');
            return `${h}:${m}:${s}`;
        }

        function updateTimerDisplay() {
            const el = document.getElementById('timer-ujian');
            if (el) el.textContent = formatHHMMSS(elapsedSeconds);
        }

        function startTimer() {
            if (timerStarted) return;
            timerStarted = true;

            timerInterval = setInterval(() => {
                elapsedSeconds += 1;
                window.waktuUjianDetik = elapsedSeconds;

                updateTimerDisplay();

                // update hidden input
                const hidden = document.getElementById('waktu-ujian-detik');
                if (hidden) hidden.value = elapsedSeconds;
            }, 1000);
        }

        // Mulai timer saat pertama kali user mengetik di input
        document.addEventListener('DOMContentLoaded', function () {
            // set tampilan awal 00:00:00
            updateTimerDisplay();

            const container = document.querySelector('.input-panel');
            if (container) {
                container.addEventListener('input', function onFirstType(e) {
                    if (e.target && e.target.matches('input[type="text"]')) {
                        startTimer();
                        container.removeEventListener('input', onFirstType);
                    }
                });
            }
        });
        // ====== End timer logic ======

    </script>
</html>
