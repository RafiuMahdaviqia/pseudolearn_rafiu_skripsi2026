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

        .panel {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .btn-custom {
            width: 100%;
            margin-bottom: 10px;
        }

        .answer-box {
            min-height: 40px;
            background-color: #CDD6E2;
            border: 2px dashed #022349;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4px 8px; /* rapat atas-bawah, cukup luas kiri-kanan */
        }

        .panel-box {
            border-radius: 12px;
            border: 4px solid #022349;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            background-color: #CDD6E2;
            margin-bottom: 2.5rem;
        }

        .panel-header {
            background-color: #0a3a71;
            color: white;
            padding: 12px 0;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .panel-body {
            padding: 1.2rem 1rem 1.2rem;
            font-size: small;
            text-align: left;
        }

        .panel-body-algoritma {
            padding: 1.2rem 1rem 1.2rem;
            font-size: small;
            display: flex;
            flex-direction: column;
            gap: 12px;
            text-align: left;
        }

        .drag-item {
            background-color: #0a3a71;
            color: white;
            font-weight: 500;
            width: 100%;
            border-radius: 8px;
            padding: 10px 0;
            cursor: grab;
            user-select: none;
            transition: background-color 0.2s;
            white-space: normal;
            word-wrap: break-word;
            text-align: center;
            margin-bottom: 2px; 
        }

        .drag-item:hover {
            background-color: #022349;
            transform: scale(1.02);
        }

        .drag-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1rem;
        }

        .drag-grid .drag-item {
            width: 47%;
        }

       @keyframes shake {
            0% { transform: translateX(0); }
            25% { transform: translateX(-6px); }
            50% { transform: translateX(6px); }
            75% { transform: translateX(-6px); }
            100% { transform: translateX(0); }
        }

        .answer-box.shake {
            animation: shake 0.4s;
            border-color: red !important;
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
                                                <div class="d-flex justify-content-between align-items-center px-4 py-3" style="min-height: 60px; background-color: #0C3B6F; border-radius: 12px;">
                                                    <button class="btn btn-danger btn-sm d-flex align-items-center px-3 py-2 fw-semibold" style="font-size: 0.95rem;" onclick="back('{{ $soal->id_level }}')">
                                                        <i class="bi bi-arrow-left me-2"></i> Kembali
                                                    </button>
                                                    <div class="flex-grow-1 text-center text-white fw-bold" style="font-size: 1.3rem;">
                                                        Waktu Pengerjaan <span class="fw-bolder" id="timer-ujian">00:00:00</span>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-7">
                                                        <button class="btn btn-primary btn-sm d-flex align-items-center px-3 py-2 fw-semibold" 
                                                            style="font-size: 0.95rem; box-shadow: -2px 2px 8px #0000004d;" onclick="openModalGuide()">
                                                            <img src="{{ asset('assets/media/img/iconbook.png') }}" class="me-2" style="height: 1.3em;"> Lihat Panduan
                                                        </button>
                                                        <div class="d-flex align-items-center" style="color: white; font-weight: bold; font-size: 1.2rem;">
                                                            <img class="heart-beat" src="{{ asset('assets/media/img/heart.png') }}" alt="Heart" style="height: 2em; margin-right: 6px;" />
                                                            <span id="lives-count">{{ $lives }}</span> / {{ $max_lives }} 
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="bg-white rounded-bottom p-8 fs-6">
                                                    {!! $soal['soal'] !!}
                                                </div>
                                            </div>
                                        </div>

                                        <input type="hidden" id="id-level" value="{{ $soal->id_level }}">
                                        <input type="hidden" id="id-soal" value="{{ $soal->id }}">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="panel-box text-center mb-8">
                                                    <div class="panel-header">Tipe Data</div>
                                                    @php
                                                        $tipeDataList = collect(json_decode($soal['kunci_tipe_data'], true))
                                                            ->pluck('tipe_data')
                                                            ->values()
                                                            ->shuffle();
                                                        $algoritmaList = collect(json_decode($soal['kunci_algoritma'], true))
                                                            ->where('clue', '0')
                                                            ->pluck('langkah')
                                                            ->values()
                                                            ->shuffle();
                                                        $dataId = 1;
                                                    @endphp
                                                    <div class="panel-body drag-grid">
                                                        @foreach($tipeDataList as $tipe)
                                                            <div class="drag-item" data-id="item-{{ $dataId }}">{{ $tipe }}</div>
                                                            @php $dataId++; @endphp
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="panel-box text-center">
                                                    <div class="panel-header">Algoritma</div>
                                                    <div class="panel-body-algoritma">
                                                        @foreach($algoritmaList as $langkah)
                                                            <div class="drag-item" data-id="item-{{ $dataId }}">{{ $langkah }}</div>
                                                            @php $dataId++; @endphp
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-7">
                                                <div class="rounded-4 shadow-sm p-0" style="background-color: #CDD6E2; border: 4px solid #022349; border-radius: 12px;">
                                                    <div class="rounded-top-2 text-white fw-bold d-flex justify-content-center align-items-center mb-2" 
                                                        style="background-color: #0a3a71; height: 50px; font-size: 1.2rem;">
                                                        Jawaban
                                                    </div>

                                                    <div class="p-3">
                                                        <div class="mb-3"> 
                                                            <div class="rounded-4 overflow-hidden" style="border: 4px solid #022349; background-color: #CDD6E2;">
                                                                <div class="text-white fw-bold text-center py-2" style="background-color: #0a3a71; font-size: 1.1rem;">
                                                                    Tipe Data
                                                                </div>
                                                                <div class="p-3">
                                                                    @php
                                                                        $tipeDataList = collect(json_decode($soal['kunci_tipe_data'], true))
                                                                            ->pluck('variabel')
                                                                            ->filter(function($v) { return !is_null($v); })
                                                                            ->values();
                                                                    @endphp
                                                                    @foreach($tipeDataList as $i => $variabel)
                                                                        <div class="row align-items-center mb-3">
                                                                            <div class="col-4 fw-bold">
                                                                                {{ $variabel }}
                                                                            </div>
                                                                            <div class="col-8">
                                                                                <div class="answer-box box-tipe" data-variable="{{ $variabel }}"></div>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <div class="rounded-4 overflow-hidden" style="border: 4px solid #022349; background-color: #CDD6E2;">
                                                                <div class="text-white fw-bold text-center py-2" style="background-color: #0a3a71; font-size: 1.1rem;">
                                                                    Algoritma
                                                                </div>
                                                                <div class="p-3">
                                                                    @php
                                                                        $algoritmaList = collect(json_decode($soal['kunci_algoritma'], true));
                                                                    @endphp
                                                                    @foreach($algoritmaList as $item)
                                                                        <div class="answer-box box-algo mb-2" data-index="{{ $loop->index }}" data-clue="{{ $item['clue'] }}">
                                                                            @if($item['clue'] == '1')
                                                                                <div class="drag-item" data-id="item-{{ $loop->index }}">
                                                                                    {{ $item['langkah'] }}
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" id="jawaban-user" name="jawaban_user">

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
    @include('components.chatbot')
    
    <script>
        var hostUrl = "assets/";
    </script>
    <script src="{{ asset('js/ujian/index.js') }}"></script>
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
    </script>

    <script>
        // Buat semua item bisa diseret
        document.querySelectorAll('.drag-item').forEach(makeDraggable);
        function makeDraggable(item) {
            item.setAttribute('draggable', true);

            // Tandai asalnya (tipe atau algoritma)
            if (item.closest('.panel-body')) {
                item.setAttribute('data-source', 'tipe');
            } else if (item.closest('.panel-body-algoritma')) {
                item.setAttribute('data-source', 'algo');
            }

            item.addEventListener('dragstart', function (e) {
                e.dataTransfer.setData('text/plain', e.target.innerText);
                e.target.classList.add('dragging');
            });
        }

        // Box jawaban menerima drop
        document.querySelectorAll('.answer-box').forEach(box => {
            box.addEventListener('dragover', e => e.preventDefault());

            box.addEventListener('drop', function (e) {
                e.preventDefault();
                const dragged = document.querySelector('.dragging');
                if (!dragged) return;

                // Cek asal item (source panel)
                const sourceClass = dragged.getAttribute('data-source');
                const isBoxTipe = this.classList.contains('box-tipe');
                const isBoxAlgo = this.classList.contains('box-algo');

                // Cegah jika asal dan target tidak cocok
                if ((isBoxTipe && sourceClass !== 'tipe') || (isBoxAlgo && sourceClass !== 'algo')) {
                    this.classList.add('shake');
                    let msg = '';
                    if (isBoxTipe) {
                        msg = 'Ini adalah bagian answer-box tipe data!';
                    } else if (isBoxAlgo) {
                        msg = 'Ini adalah bagian answer-box Algoritma!';
                    }
                    // Tampilkan pesan di dalam box tanpa menghapus isi box
                    // const originalContent = this.innerHTML;
                    // this.innerHTML = `<span style="color:red;font-weight:bold;">${msg}</span>`;
                    // setTimeout(() => {
                    //     this.classList.remove('shake');
                    //     this.innerHTML = originalContent;
                    // }, 2000);
                    // return;
                    // Tambahkan pesan error sementara TANPA menghapus isi box
                    if (!this.querySelector('.error-msg')) {
                        const errorMsg = document.createElement('div');
                        errorMsg.className = 'error-msg';
                        errorMsg.style.color = 'red';
                        errorMsg.style.fontWeight = 'bold';
                        errorMsg.style.marginLeft = '8px';
                        errorMsg.innerText = msg;
                        this.appendChild(errorMsg);


                        setTimeout(() => {
                            errorMsg.remove();
                            this.classList.remove('shake');
                        }, 2000);
                    }
                }

                // Cek jika sudah terisi
                if (this.children.length > 0) {
                    this.classList.add('shake');
                    setTimeout(() => {
                        this.classList.remove('shake');
                    }, 400);
                    return;
                }

                this.appendChild(dragged);
                dragged.classList.remove('dragging');
            });
        });

        // Area asal menerima kembali item
        document.querySelectorAll('.panel-body, .panel-body-algoritma').forEach(panel => {
            panel.addEventListener('dragover', e => e.preventDefault());

            panel.addEventListener('drop', function (e) {
                e.preventDefault();
                const dragged = document.querySelector('.dragging');
                if (!dragged) return;

                // Cek agar tipe hanya bisa kembali ke panel tipe, dan algoritma ke panel algoritma
                const panelIsTipe = this.classList.contains('panel-body');
                const panelIsAlgo = this.classList.contains('panel-body-algoritma');
                const sourceClass = dragged.getAttribute('data-source');

                // Perbaikan: izinkan drop jika item tipe/algo, baik dari panel maupun dari answer-box
                if ((panelIsTipe && sourceClass !== 'tipe') || (panelIsAlgo && sourceClass !== 'algo')) {
                    this.classList.add('shake');
                    setTimeout(() => {
                        this.classList.remove('shake');
                    }, 400);
                    return;
                }

                // Hapus item dari answer box jika dipindahkan dari sana
                if (dragged.parentElement.classList.contains('answer-box')) {
                    dragged.parentElement.removeChild(dragged);
                }

                // Kembalikan ke panel asal
                this.appendChild(dragged);
                dragged.classList.remove('dragging');
            });
        });

        // Hapus class dragging setelah drag selesai
        document.addEventListener('dragend', function () {
            document.querySelectorAll('.dragging').forEach(el => el.classList.remove('dragging'));
        });
        
    </script>
</html>
