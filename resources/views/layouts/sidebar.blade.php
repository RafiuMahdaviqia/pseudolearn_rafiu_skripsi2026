@php
    $isAdmin = Auth::check() && Auth::user()->is_admin;
@endphp

@push('styles')
    <style>
    .sidebar-guide-btn {
        transition: box-shadow 0.2s, transform 0.2s;
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    }
    .sidebar-guide-btn:hover {
        box-shadow: 0 8px 24px rgba(0,0,0,0.20);
        transform: translateY(-6px);
    }
    </style>
@endpush

<div class="app-sidebar-header d-flex flex-stack d-none d-lg-flex pt-8 pb-2" id="kt_app_sidebar_header">
    <a href="#" class="app-sidebar-logo">
        <img alt="Logo" src="{!! asset('assets/media/logos/logo-transparan.png') !!}" class="h-55px d-none d-sm-inline app-sidebar-logo-default theme-light-show" />
        <img alt="Logo" src="{!! asset('assets/media/logos/logo-transparan.png') !!}" class="h-55px theme-dark-show" />
    </a>
    <div id="kt_app_sidebar_toggle" class="app-sidebar-toggle btn btn-sm btn-icon bg-light btn-color-gray-700 btn-active-color-primary d-none d-lg-flex rotate" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="app-sidebar-minimize">
        <i class="ki-outline ki-right rotate-180 fs-1"></i>
    </div>
</div>
<div class="app-sidebar-navs flex-column-fluid py-6" id="kt_app_sidebar_navs">
    <div id="kt_app_sidebar_navs_wrappers" class="app-sidebar-wrapper hover-scroll-y my-2" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_sidebar_header" data-kt-scroll-wrappers="#kt_app_sidebar_navs" data-kt-scroll-offset="5px">
        <div id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false" class="app-sidebar-menu-primary menu menu-column menu-rounded menu-sub-indention menu-state-bullet-primary">
            
            {{-- Menu untuk Mahasiswa --}}
            @unless($isAdmin)
                <div class="menu-item mb-2">
                    <div class="menu-heading text-uppercase fs-7 fw-bold">Menu</div>
                    <div class="app-sidebar-separator separator"></div>
                </div>
                <div class="menu-item">
                    <a class="menu-link{{ request()->is('dashboard') ? ' active' : '' }}" href="{{ url('dashboard') }}" @if(request()->is('dashboard')) style="background-color: #CDD6E2;" @endif>
                        <span class="menu-icon me-5">
                            <img src="{{ asset('assets/media/icons-imports/lets-icons_home-duotone.png') }}" class="h-25px">
                        </span>
                        <span class="menu-title fs-6 text-primary-emphasis {{ request()->is('dashboard') ? 'fw-bold' : 'fw-normal' }}">Dashboard</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link{{ request()->is('quiz') ? ' active' : '' }}" href="{{ url('quiz') }}" @if(request()->is('quiz')) style="background-color: #CDD6E2;" @endif>
                        <span class="menu-icon me-5">
                            <img src="{{ asset('assets/media/icons-imports/lets-icons_book-duotone.png') }}" class="h-25px">
                        </span>
                        <span class="menu-title fs-6 text-primary-emphasis {{ request()->is('quiz') ? 'fw-bold' : 'fw-normal' }}">Latihan Soal</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link{{ request()->is('leaderboard') ? ' active' : '' }}" href="{{ url('leaderboard') }}" @if(request()->is('leaderboard')) style="background-color: #CDD6E2;" @endif>
                        <span class="menu-icon me-5">
                            <img src="{{ asset('assets/media/icons-imports/ranking-people.png') }}" class="h-25px">
                        </span>
                        <span class="menu-title fs-6 text-primary-emphasis {{ request()->is('leaderboard') ? 'fw-bold' : 'fw-normal' }}">Leaderboard</span>
                    </a>
                </div>
                <!-- panduan -->
                <div class="sidebar-book-container position-fixed bottom-0 start-0 z-1050" style="width: 160px; bottom: 30px !important;">
                    <div class="position-relative">
                        <img src="{{ asset('assets/media/img/book.webp') }}" alt="Book" class="img-fluid ms-7 mb-3 hover-elevate-up" style="max-width: 200px;">
                        <button class="btn btn-sm btn-primary sidebar-guide-btn position-absolute d-inline-flex align-items-center fw-semibold text-white text-decoration-none hover-elevate-up"
                            style="bottom: 10px;white-space: nowrap;left: 50px; box-shadow: 0px 2px 11px #0000004d;" onclick="openModalGuide()">
                            <img src="{{ asset('assets/media/img/iconbook.png') }}" class="sidebar-guide-icon me-2" style="height: 25px; width: auto;">
                            Lihat Panduan
                        </button>
                    </div>
                </div>
            @endunless

                      {{-- Menu untuk Admin/Dosen --}}
            @if($isAdmin)
                <div class="menu-item">
                    <div class="menu-heading text-uppercase fs-7 fw-bold">Menu</div>
                    <div class="app-sidebar-separator separator my-2"></div>
                </div>
                <div class="menu-item">
                    <a class="menu-link{{ request()->is('dashboard') ? ' active' : '' }}" href="{{ url('dashboard') }}">
                        <span class="menu-icon me-2">
                            <i class="ki-solid ki-home fs-2 "></i>
                        </span>
                        <span class="menu-title {{ request()->is('dashboard') ? 'fw-bold' : 'fw-semibold' }}">Dashboard</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link{{ request()->is('log-activity*') ? ' active' : '' }}" href="{{ url('log-activity') }}">
                        <span class="menu-icon me-2">
                            <i class="ki-solid ki-tablet-text-up fs-2"></i>
                        </span>
                        <span class="menu-title {{ request()->is('log-activity*') ? 'fw-bold' : 'fw-semibold' }}">Data Log Aktivitas</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link{{ request()->is('confidence*') ? ' active' : '' }}" href="{{ url('confidence') }}">
                        <span class="menu-icon me-2">
                            <i class="ki-solid ki-arrow-circle-left fs-2"></i>
                        </span>
                        <span class="menu-title {{ request()->is('confidence*') ? 'fw-bold' : 'fw-semibold' }}">Confidence Tag</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link{{ request()->is('overlapping*') ? ' active' : '' }}" href="{{ url('overlapping') }}">
                        <span class="menu-icon me-2">
                            <i class="ki-solid ki-chart fs-2"></i>
                        </span>
                        <span class="menu-title {{ request()->is('overlapping*') ? 'fw-bold' : 'fw-semibold' }}">Overlapping Analysis</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link{{ request()->is('labeling*') ? ' active' : '' }}" href="{{ url('labeling') }}">
                        <span class="menu-icon me-2">
                            <i class="ki-solid ki-flag fs-2"></i>
                        </span>
                        <span class="menu-title {{ request()->is('labeling*') ? 'fw-bold' : 'fw-semibold' }}">Clustering Labeling</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link{{ request()->is('scoring*') ? ' active' : '' }}" href="{{ url('scoring') }}">
                        <span class="menu-icon me-2">
                            <i class="ki-solid ki-percentage fs-2"></i>
                        </span>
                        <span class="menu-title {{ request()->is('scoring*') ? 'fw-bold' : 'fw-semibold' }}">Clustering Scoring</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link{{ request()->is('ujian-konversi*') ? ' active' : '' }}" href="{{ url('ujian-konversi') }}">
                        <span class="menu-icon me-2">
                            <i class="ki-solid ki-underlining fs-2"></i>
                        </span>
                        <span class="menu-title {{ request()->is('ujian-konversi*') ? 'fw-bold' : 'fw-semibold' }}">Ujian Konversi</span>
                    </a>
                </div>
                <div class="menu-item mt-5">
                    <div class="menu-heading text-uppercase fs-7 fw-bold">Master</div>
                    <div class="app-sidebar-separator separator my-2"></div>
                </div>
                <div class="menu-item">
                    <a class="menu-link{{ (request()->is('mahasiswa*') || request()->is('kelas*')) ? ' active' : '' }}" href="{{ url('mahasiswa') }}">
                        <span class="menu-icon me-2">
                            <i class="ki-solid ki-user-square fs-2 "></i>
                        </span>
                        <span class="menu-title {{ (request()->is('mahasiswa*') || request()->is('kelas*')) ? 'fw-bold' : 'fw-semibold' }}">Data Mahasiswa</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link{{ request()->is('level*') ? ' active' : '' }}" href="{{ url('level') }}">
                        <span class="menu-icon me-2">
                            <i class="ki-solid ki-category fs-2"></i>
                        </span>
                        <span class="menu-title {{ request()->is('level') ? 'fw-bold' : 'fw-semibold' }}">Level</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link{{ request()->is('soal*') ? ' active' : '' }}" href="{{ url('soal') }}">
                        <span class="menu-icon me-2">
                            <i class="ki-solid ki-text fs-2 "></i>
                        </span>
                        <span class="menu-title {{ request()->is('soal') ? 'fw-bold' : 'fw-semibold' }}">Bank Soal</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link{{ request()->is('konversi*') ? ' active' : '' }}" href="{{ url('konversi') }}">
                        <span class="menu-icon me-2">
                            <i class="ki-solid ki-arrow-mix fs-2 "></i>
                        </span>
                        <span class="menu-title {{ request()->is('konversi*') ? 'fw-bold' : 'fw-semibold' }}">Soal Konversi</span>
                    </a>
                </div>
                <div class="menu-item mt-5">
                    <div class="menu-heading text-uppercase fs-7 fw-bold">Pengaturan</div>
                    <div class="app-sidebar-separator separator my-2"></div>
                </div>
                <div class="menu-item">
                    <a class="menu-link{{ request()->is('guide*') ? ' active' : '' }}" href="{{ url('guide') }}">
                        <span class="menu-icon me-2">
                            <i class="ki-solid ki-book-square fs-2 "></i>
                        </span>
                        <span class="menu-title {{ request()->is('guide') ? 'fw-bold' : 'fw-semibold' }}">Panduan</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link{{ request()->is('setting-admin*') ? ' active' : '' }}" href="{{ url('setting-admin') }}">
                        <span class="menu-icon me-2">
                            <i class="ki-solid ki-security-user fs-2 "></i>
                        </span>
                        <span class="menu-title {{ request()->is('setting-admin') ? 'fw-bold' : 'fw-semibold' }}">Akun Admin</span>
                    </a>
                </div>
            @endif
        </div>
    </div>
    {{-- <div class="position-relative">
        <img src="{{ asset('assets/media/img/book.png') }}" alt="Book" style="max-width:180px; position: absolute; left: 50%; transform: translateX(-50%); bottom: 0px;">
    </div> --}}
</div>

@push('scripts')
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
@endpush