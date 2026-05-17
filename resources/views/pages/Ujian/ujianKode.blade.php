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
        if (window.top != window.self) {
            window.top.location.replace(window.self.location.href);
        }
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            background: #f5f5f5;
        }

        /* ── Panel shared ── */
        .panel-box {
            background-color: #CDD6E2;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
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
            padding: 1.2rem 1rem;
        }

        /* ── Pseudocode box ── */
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

        /* ── Drag items ── */
        .drag-item {
            background-color: #0a3a71;
            color: white;
            font-weight: 500;
            width: 100%;
            border-radius: 8px;
            padding: 10px 14px;
            cursor: grab;
            user-select: none;
            transition: background-color 0.2s, transform 0.15s;
            white-space: normal;
            word-wrap: break-word;
            text-align: center;
            margin-bottom: 6px;
            font-size: 0.88rem;
            font-family: 'Courier New', monospace;
        }

        .drag-item:hover {
            background-color: #022349;
            transform: scale(1.02);
        }

        .drag-item.dragging {
            opacity: 0.4;
        }

        /* ── Answer boxes ── */
        .answer-box {
            min-height: 44px;
            background-color: #CDD6E2;
            border: 2px dashed #022349;
            border-radius: 8px;
            display: flex;
            align-items: center;
            padding: 4px 8px;
            transition: background-color 0.2s, border-color 0.2s;
        }

        .answer-box.drag-over {
            background-color: #b0c4de;
            border-color: #0a3a71;
            border-style: solid;
        }

        /* Java structure wrapper boxes */
        .code-box-input {
            background-color: #0a3a71;
            color: white;
            padding: 10px 16px;
            border-radius: 8px;
            margin-bottom: 10px;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
        }

        /* ── Input panel wrapper ── */
        .input-panel {
            border: 4px solid #022349;
            border-radius: 12px;
            background-color: #CDD6E2;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
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

        /* ── Animations ── */
        @keyframes shake {
            0% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-6px);
            }

            50% {
                transform: translateX(6px);
            }

            75% {
                transform: translateX(-6px);
            }

            100% {
                transform: translateX(0);
            }
        }

        .answer-box.shake {
            animation: shake 0.4s;
            border-color: red !important;
        }

        .heart-beat {
            animation: heartBeat 1s infinite;
        }

        @keyframes heartBeat {
            0% {
                transform: scale(1);
            }

            10% {
                transform: scale(1.1);
            }

            20% {
                transform: scale(1.2);
            }

            30% {
                transform: scale(1.1);
            }

            40% {
                transform: scale(1);
            }

            100% {
                transform: scale(1);
            }
        }

        /* Pilihan kode drag-grid */
        .drag-grid-java {
            display: flex;
            flex-direction: column;
            gap: 8px;
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

                                {{-- ── Topbar ── --}}
                                <div class="mb-8">
                                    <div class="card rounded-bottom shadow p-0 border-0"
                                        style="background-color: #0a3a71; border-radius: 1rem 1rem 0 0;">
                                        <div class="d-flex justify-content-between align-items-center px-4 py-3"
                                            style="min-height: 60px; background-color: #0a3a71; border-radius: 12px;">

                                            <button
                                                class="btn btn-danger btn-sm d-flex align-items-center px-3 py-2 fw-semibold"
                                                style="font-size: 0.95rem;" onclick="back('{{ $soal->id_level }}')">
                                                <i class="bi bi-arrow-left me-2"></i> Kembali
                                            </button>

                                            @csrf
                                            <div class="flex-grow-1 text-center text-white fw-bold"
                                                style="font-size: 1.3rem;">
                                                Waktu Pengerjaan
                                                <span class="fw-bolder" id="timer-ujian">00:00:00</span>
                                                <input type="hidden" id="waktu-ujian-detik" name="waktu"
                                                    value="0" />
                                            </div>

                                            <div class="d-flex align-items-center gap-7">
                                                <button
                                                    class="btn btn-primary btn-sm d-flex align-items-center px-3 py-2 fw-semibold"
                                                    style="font-size: 0.95rem; box-shadow: -2px 2px 8px #0000004d;"
                                                    onclick="openModalGuide()">
                                                    <img src="{{ asset('assets/media/img/iconbook.png') }}"
                                                        class="me-2" style="height: 1.3em;"> Lihat Panduan
                                                </button>
                                                <div class="d-flex align-items-center"
                                                    style="color: white; font-weight: bold; font-size: 1.2rem;">
                                                    <img class="heart-beat"
                                                        src="{{ asset('assets/media/img/heart.png') }}" alt="Heart"
                                                        style="height: 2rem; margin-right: 6px;" />
                                                    <span id="lives-count">{{ $lives }}</span> /
                                                    {{ $max_lives }}
                                                </div>
                                            </div>
                                        </div>

                                        <input type="hidden" id="id-soal-konversi" value="{{ $konversi->id }}">
                                        <div class="bg-white rounded-bottom p-8 fs-6">
                                            {!! $soal['soal'] !!}
                                        </div>
                                    </div>
                                </div>
                                {{-- ── End Topbar ── --}}

                                <input type="hidden" id="id-level" value="{{ $soal->id_level }}">

                                @php
                                    $tipeDataList = collect(json_decode($soal['kunci_tipe_data'], true));
                                    $algoritmaList = collect(json_decode($soal['kunci_algoritma'], true));
                                    $dataLangkah = 1;

                                    $algoritmaTerpilih = $algoritmaList->filter(function ($row) {
                                        if (isset($row['data_konversi'])) {
                                            return (int) $row['data_konversi'] === 1;
                                        }
                                        if (isset($row['konversi'])) {
                                            return (int) $row['konversi'] === 1;
                                        }
                                        return false;
                                    });

                                    // Format jawaban (JSON array atau teks per baris)
                                    $jawabanList = collect(
                                        \App\Models\BankSoalKonversi::parseJawabanLines($konversi['jawaban'] ?? ''),
                                    )->shuffle();

                                    $totalLangkah = $jawabanList->count();
                                @endphp

                                <div class="row mb-4">

                                    {{-- ── Kolom Kiri: Pseudocode + Pilihan Kode ── --}}
                                    <div class="col-md-5">

                                        {{-- Pseudocode Panel --}}
                                        {{-- <div class="panel-box">
                                            <div class="panel-header">Pseudocode</div>
                                            <div class="panel-body">
                                                @foreach ($tipeDataList as $item)
                                                    @if (!empty($item['variabel']))
                                                        <div class="step-title">Langkah {{ $dataLangkah }}</div>
                                                        <div class="code-box">
                                                            {{ $item['variabel'] }} : {{ $item['tipe_data'] ?? '-' }}
                                                        </div>
                                                        @php $dataLangkah++; @endphp
                                                    @endif
                                                @endforeach

                                                @foreach ($algoritmaTerpilih as $item)
                                                    <div class="step-title">Langkah {{ $dataLangkah }}</div>
                                                    <div class="code-box">{{ $item['langkah'] }}</div>
                                                    @php $dataLangkah++; @endphp
                                                @endforeach
                                            </div>
                                        </div> --}}

                                        {{-- Pilihan Kode Java (Drag source) --}}
                                        <div class="panel-box">
                                            <div class="panel-header">Pilihan Kode Java</div>
                                            <div class="panel-body drag-grid-java" id="panel-pilihan-kode">
                                                @foreach ($jawabanList as $jawaban)
                                                    <div class="drag-item" draggable="true" data-source="java">
                                                        {{ $jawaban }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                    </div>
                                    {{-- ── End Kolom Kiri ── --}}

                                    {{-- ── Kolom Kanan: Input Kode Java (Drop zone) ── --}}
                                    <div class="col-md-7">
                                        <div class="input-panel">
                                            <div class="input-header">Input Kode Java</div>
                                            <div class="input-body">

                                                @for ($i = 1; $i <= $totalLangkah; $i++)
                                                    <div class="ms-4 mb-3">
                                                        <div class="mb-1">
                                                            <strong style="font-size: 0.9rem;">Langkah
                                                                {{ $i }}</strong>
                                                        </div>
                                                        <div class="answer-box box-java"
                                                            data-index="{{ $i }}"
                                                            style="min-height: 46px;">
                                                            {{-- Drop item here --}}
                                                        </div>
                                                    </div>
                                                @endfor

                                            </div>

                                            <div class="d-flex justify-content-between px-4 pb-3">
                                                <button
                                                    class="btn fw-bold px-4 py-2 rounded-3 d-flex align-items-center gap-2"
                                                    style="background-color: #FF5C00; border: 2px solid #AA3D00; color: white;"
                                                    onclick="reloadUjian()">
                                                    <i class="bi bi-arrow-clockwise text-white fs-5"></i> Reload
                                                </button>
                                                <button
                                                    class="btn fw-bold px-4 py-2 rounded-3 d-flex align-items-center gap-2"
                                                    style="background-color: #22BB33; border: 2px solid #177D22; color: white;"
                                                    onclick="openModalKonfirmasi()">
                                                    <i class="bi bi-check-circle text-white fs-5"></i> Cek Jawaban
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- ── End Kolom Kanan ── --}}

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('pages.guide.index')
    @include('pages.Ujian.modal')

    <script>
        var hostUrl = "assets/";
    </script>
    <script src="{{ asset('js/ujian/indexUjianKode.js') }}"></script>
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
        // ══════════════════════════════════════════
        //  Timer (count-up, starts on first drop)
        // ══════════════════════════════════════════
        let timerInterval = null;
        let timerStarted = false;
        let elapsedSeconds = 0;
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
                elapsedSeconds++;
                window.waktuUjianDetik = elapsedSeconds;
                updateTimerDisplay();
                const hidden = document.getElementById('waktu-ujian-detik');
                if (hidden) hidden.value = elapsedSeconds;
            }, 1000);
        }

        //  Drag & Drop – Java code
        document.addEventListener('DOMContentLoaded', function() {
            updateTimerDisplay();

            // Semua drag-item yang ada bisa di-drag
            document.querySelectorAll('.drag-item[data-source="java"]').forEach(makeDraggable);

            // Drop: answer-box.box-java
            document.querySelectorAll('.answer-box.box-java').forEach(registerDropZone);

            // Panel pilihan: bisa menerima kembali item yang di-drag keluar dari box
            const panelPilihan = document.getElementById('panel-pilihan-kode');
            if (panelPilihan) {
                panelPilihan.addEventListener('dragover', e => e.preventDefault());
                panelPilihan.addEventListener('dragenter', () => panelPilihan.style.outline = '2px dashed #0a3a71');
                panelPilihan.addEventListener('dragleave', () => panelPilihan.style.outline = '');
                panelPilihan.addEventListener('drop', function(e) {
                    e.preventDefault();
                    panelPilihan.style.outline = '';
                    const dragged = document.querySelector('.drag-item.dragging');
                    if (!dragged) return;

                    // Kembalikan ke panel pilihan
                    panelPilihan.appendChild(dragged);
                    dragged.classList.remove('dragging');
                    startTimer();
                });
            }
        });

        function makeDraggable(item) {
            item.setAttribute('draggable', 'true');

            item.addEventListener('dragstart', function(e) {
                e.dataTransfer.setData('text/plain', e.target.innerText.trim());
                setTimeout(() => e.target.classList.add('dragging'), 0);
                startTimer();
            });

            item.addEventListener('dragend', function() {
                item.classList.remove('dragging');
            });
        }

        function registerDropZone(box) {
            box.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('drag-over');
            });

            box.addEventListener('dragleave', function() {
                this.classList.remove('drag-over');
            });

            box.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('drag-over');

                const dragged = document.querySelector('.drag-item.dragging');
                if (!dragged) return;

                const existing = this.querySelector('.drag-item');
                if (existing) {
                    this.classList.add('shake');
                    setTimeout(() => this.classList.remove('shake'), 400);
                    return;
                }

                this.appendChild(dragged);
                dragged.classList.remove('dragging');
                startTimer();

                // ── Log drag & drop ──
                const index = this.getAttribute('data-index');
                const itemText = dragged.innerText.trim();
                const idSoal = document.getElementById('id-soal-konversi').value;
                const idLevel = document.getElementById('id-level').value;

                fetch(APP_URL + 'ujian-kode/log-drag', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                        },
                        body: JSON.stringify({
                            id_bank_soal_konversi: idSoal,
                            id_level: idLevel,
                            index: index,
                            item_text: itemText,
                        }),
                    })
                    .then(res => res.json())
                    .then(data => console.log('Log drag berhasil:', data))
                    .catch(err => console.error('Log drag gagal:', err));
            });
        }

        // Double-click pada item di dalam answer-box → kembalikan ke panel
        document.addEventListener('dblclick', function(e) {
            const item = e.target.closest('.drag-item');
            if (!item) return;
            if (item.closest('.answer-box')) {
                const panel = document.getElementById('panel-pilihan-kode');
                if (panel) {
                    panel.appendChild(item);
                    item.classList.remove('dragging');
                }
            }
        });

        // ══════════════════════════════════════════
        //  Kumpulkan jawaban sebelum submit
        //  (dipanggil dari indexCodeProgram.js saat Cek Jawaban)
        // ══════════════════════════════════════════
        window.getJawabanKonversi = function() {
            const hasil = {};
            document.querySelectorAll('.answer-box.box-java').forEach(box => {
                const idx = box.getAttribute('data-index');
                const item = box.querySelector('.drag-item');
                hasil[idx] = item ? item.innerText.trim() : '';
            });
            return hasil;
        };
    </script>
</body>

</html>
