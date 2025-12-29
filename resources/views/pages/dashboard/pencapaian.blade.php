@extends('layouts.main')

@push('styles')
<style>
    .nav-tabs .nav-link {
        color: #565656ff;
        font-weight: normal;
        background-color: transparent;
        transition: color 0.3s ease;
        font-weight: 500;
    }

    .nav-tabs .nav-link.active {
        color: #03346E !important;
        font-weight: bold;
    }

    .nav-tabs .nav-link:not(.active):hover {
        color: #03346E !important; 
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
@endpush

@section('content')
    <input type="hidden" id="lives-count" value="{{ $lives }}">
    <input type="hidden" id="max-lives-count" value="{{ $max_lives }}">
    
    <div class="container">
        <div class="row mb-8">
            <div class="col-9 d-flex align-items-center">
                <a href="{{ url()->previous() }}" class="me-5" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Kembali ke halaman sebelumnya">
                    <i class="ki-outline ki-double-left fs-2hx text-primary hover-elevate-up"></i>
                </a>
                <h2 class="mb-1">Tuntaskan <strong style="color: #F39C12;">misimu</strong>, dapatkan <strong style="color: #F39C12;">nyawa</strong>, dan buktikan kemampuanmu!</h2>
            </div>
            <div class="col-3 d-flex justify-content-end align-items-center">
                <img src="{{ asset('assets/media/img/heart.png') }}" alt="Life" class="me-2 mb-1 heart-beat" style="width: 20px;">
                <div class="fw-bold text-danger fs-4"><span id="lives-count-text">{{ $lives }}</span> / {{ $max_lives }}</div>
            </div>
        </div>

        <ul class="nav nav-tabs mb-6">
            {{-- <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#leaderboard">Leaderboard</a>
            </li> --}}
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#badge">Badge</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#soal">Soal</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#konversi">Ujian Konversi</a>
            </li>
        </ul>


        <div class="tab-content">
            <div class="tab-pane fade show active" id="badge">
                <div id="badge-content" class="d-flex flex-column gap-3"></div>
            </div>
            <div class="tab-pane fade" id="soal">
                <div id="soal-content" class="d-flex flex-column gap-3"></div>
            </div>
            <div class="tab-pane fade" id="konversi">
                <div id="konversi-content" class="d-flex flex-column gap-3"></div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/dashboard/pencapaian.js') }}"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if(tab) {
            const tabLink = document.querySelector(`.nav-link[href="#${tab}"]`);
            if(tabLink) {
                new bootstrap.Tab(tabLink).show();
                tabLink.classList.add('active');
                const tabPane = document.getElementById(tab);
                if(tabPane) {
                    tabPane.classList.add('show', 'active');
                }

                const category = tab;
                if (typeof loadPencapaian === 'function') {
                    const tabId = `#${category}`;
                    if (tabId === '#badge') loadPencapaian('badge', '#badge-content');
                    if (tabId === '#soal') loadPencapaian('soal', '#soal-content');
                    if (tabId === '#konversi') loadPencapaian('konversi', '#konversi-content');
                } else {
                    const url = `${window.location.origin}/pencapaian/data?category=${encodeURIComponent(category)}`;
                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(res => res.text())
                        .then(html => {
                            const container = document.getElementById(`${category}-content`);
                            if (container) container.innerHTML = html;
                        })
                        .catch(err => console.error('Gagal memuat pencapaian:', err));
                }
            }
        }
    });
    </script>
@endpush


