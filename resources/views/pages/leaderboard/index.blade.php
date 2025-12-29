@extends('layouts.main')

@push('styles')
<style>
    .leaderboard-wrapper {
        display: flex;
        justify-content: center;
        gap: 2rem;
        flex-wrap: wrap;
        text-align: center;
        margin-bottom: 3rem; /* Space between cards and table */
    }

    .card-leader {
        border-radius: 12px;
        padding: 20px;
        width: 260px;
        position: relative;
        color: white;
    }

    .gold { background-color: #E3A915; }
    .silver { background-color: #bfbfbf; }
    .bronze { background-color: #9C6000; }

    .avatar-wrapper {
        position: relative;
        display: flex;
        justify-content: center;
        margin-bottom: 14px;
    }

    .avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        background: #fff;
    }

    .crown {
        position: absolute;
        top: -68px;
        left: 50%;
        transform: translateX(-50%);
        width: 76px;
        height: 76px;
        z-index: 10;
    }

    .trophy-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin: 20px 0 24px;
        font-size: medium;
        font-weight: 600;
    }

    .trophy-wrapper img {
        width: 50px;
        height: 50px;
        filter: drop-shadow(1px 1px 2px rgba(0, 0, 0, 0.6));
    }

    .info-text {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-weight: 400;
        font-size: medium;
        color: white;
        margin: 8px 0;
    }

    .name-text {
        font-size: large;
        color: white;
        margin: 8px 0;
    }

    .info-text svg {
        width: 20px;
        height: 20px;
        fill: white;
    }

    .leaderboard-title {
        font-weight: bold;
        font-size: 1.4rem;
        margin-bottom: 8px;
    }

    .card-container {
        margin-top: 40px;
    }

    .highlight-user {
        background-color: #F39C12 !important;
        font-weight: bold;
        transition: background-color 0.5s ease;
    }

    /* Animasi naik (geser ke atas) */
    @keyframes slideUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .animate-up {
        animation: slideUp 0.6s ease-out;
    }

    /* Perbesar header Leaderboard */
    .card-header h4 {
        font-size: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="container card-container">

    @php
        $hasJuara = isset($rank1['mahasiswa_name']) && $rank1['mahasiswa_name']
            && isset($rank2['mahasiswa_name']) && $rank2['mahasiswa_name']
            && isset($rank3['mahasiswa_name']) && $rank3['mahasiswa_name'];
    @endphp

    <div class="leaderboard-wrapper">
        {{-- Juara 2 --}}
        @if(isset($rank2['mahasiswa_name']) && $rank2['mahasiswa_name'])
            <div class="card-leader silver">
                <div class="avatar-wrapper">
                    <img src="{{ $rank2['user_avatar'] ? asset('assets/media/avatars/' . $rank2['user_avatar']) : asset('assets/media/avatars/blank.png') }}" class="avatar" alt="{{ $rank2['mahasiswa_name'] }}">
                </div>
                <h5 class="leaderboard-title text-white">{{ $rank2['mahasiswa_name'] }}</h5>
                <div class="trophy-wrapper">
                    <img src="assets/media/img/piala2.png" alt="Trophy">
                    <p>Juara 2</p>
                </div>
                <p class="info-text">
                    <svg ...></svg>
                    Total AlgoPoin = <strong>{{ $rank2['total_skor'] }}</strong>
                </p>
                <p class="info-text">
                    <svg ...></svg>
                    Total waktu = <strong>{{ $rank2['total_waktu'] }}</strong>
                </p>
            </div>
        @else
            <div class="card-leader silver">
                <div class="avatar-wrapper">
                    <img src="{{ asset('assets/media/avatars/blank.png') }}" class="avatar" alt="Belum ada juara">
                </div>
                <h5 class="leaderboard-title text-white">Belum ada juara</h5>
                <div class="trophy-wrapper">
                    <img src="assets/media/img/piala2.png" alt="Trophy">
                    <p>Juara 2</p>
                </div>
                <p class="info-text">Total AlgoPoin = <strong>0</strong></p>
                <p class="info-text">Total waktu = <strong>0</strong></p>
            </div>
        @endif
    
        {{-- Juara 1 --}}
        @if(isset($rank1['mahasiswa_name']) && $rank1['mahasiswa_name'])
            <div class="card-leader gold">
                <div class="avatar-wrapper">
                    <img src="{{ $rank1['user_avatar'] ? asset('assets/media/avatars/' . $rank1['user_avatar']) : asset('assets/media/avatars/blank.png') }}" class="avatar" alt="{{ $rank1['mahasiswa_name'] }}">
                    <img src="assets/media/img/crown.png" class="crown" alt="Crown">
                </div>
                <h5 class="leaderboard-title text-white">{{ $rank1['mahasiswa_name'] }}</h5>
                <div class="trophy-wrapper">
                    <img src="assets/media/img/piala1.png" alt="Trophy">
                    <p>Juara 1</p>
                </div>
                <p class="info-text">
                    <svg ...></svg>
                    Total AlgoPoin = <strong>{{ $rank1['total_skor'] }}</strong>
                </p>
                <p class="info-text">
                    <svg ...></svg>
                    Total waktu = <strong>{{ $rank1['total_waktu'] }}</strong>
                </p>
            </div>
        @else
            <div class="card-leader gold">
                <div class="avatar-wrapper">
                    <img src="{{ asset('assets/media/avatars/blank.png') }}" class="avatar" alt="Belum ada juara">
                    <img src="assets/media/img/crown.png" class="crown" alt="Crown">
                </div>
                <h5 class="leaderboard-title text-white">Belum ada juara</h5>
                <div class="trophy-wrapper">
                    <img src="assets/media/img/piala1.png" alt="Trophy">
                    <p>Juara 1</p>
                </div>
                <p class="info-text">Total AlgoPoin = <strong>0</strong></p>
                <p class="info-text">Total waktu = <strong>0</strong></p>
            </div>
        @endif
    
        {{-- Juara 3 --}}
        @if(isset($rank3['mahasiswa_name']) && $rank3['mahasiswa_name'])
            <div class="card-leader bronze">
                <div class="avatar-wrapper">
                    <img src="{{ $rank3['user_avatar'] ? asset('assets/media/avatars/' . $rank3['user_avatar']) : asset('assets/media/avatars/blank.png') }}" class="avatar" alt="{{ $rank3['mahasiswa_name'] }}">
                </div>
                <h5 class="leaderboard-title text-white">{{ $rank3['mahasiswa_name'] }}</h5>
                <div class="trophy-wrapper">
                    <img src="assets/media/img/piala3.png" alt="Trophy">
                    <p>Juara 3</p>
                </div>
                <p class="info-text">
                    <svg ...></svg>
                    Total AlgoPoin = <strong>{{ $rank3['total_skor'] }}</strong>
                </p>
                <p class="info-text">
                    <svg ...></svg>
                    Total waktu = <strong>{{ $rank3['total_waktu'] }}</strong>
                </p>
            </div>
        @else
            <div class="card-leader bronze">
                <div class="avatar-wrapper">
                    <img src="{{ asset('assets/media/avatars/blank.png') }}" class="avatar" alt="Belum ada juara">
                </div>
                <h5 class="leaderboard-title text-white">Belum ada juara</h5>
                <div class="trophy-wrapper">
                    <img src="assets/media/img/piala3.png" alt="Trophy">
                    <p>Juara 3</p>
                </div>
                <p class="info-text">Total AlgoPoin = <strong>0</strong></p>
                <p class="info-text">Total waktu = <strong>0</strong></p>
            </div>
        @endif
    </div>

    {{-- Table Leaderboard --}}
    <div class="card shadow-sm mt-5">
       <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-3 text-white" style="background-color: #03346E;">
            <div class="d-flex align-items-center gap-3">
                <img src="assets/media/img/leaderboard.png" alt="Leaderboard Icon" style="width: 40px; height: 40px;">
                <h1 class="mb-0 fw-bold text-white">Leaderboard</h1>
            </div>
        </div>

        <div class="card-body">
            {{-- <table id="leaderboardTable" class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>Juara</th>
                        <th>Username</th>
                        <th>AlgoPoin</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>1</td><td>Candra Bunga Kurnia</td><td>500</td><td>01:30:00</td></tr>
                    <tr ><td>2</td><td>Ahmad Agus</td><td>400</td><td>01:30:00</td></tr>
                    <tr><td>3</td><td>Rizki Rizal</td><td>400</td><td>01:33:00</td></tr>
                    <tr><td>4</td><td>........</td><td>......</td><td>......</td></tr>
                    <tr class="highlight-user animate-up"><td>41</td><td>Ahmad Agus</td><td>400</td><td>01:30:00</td></tr>
                    <tr><td>42</td><td>Cika</td><td>400</td><td>01:33:00</td></tr>
                </tbody>
            </table> --}}

            <div class="d-flex gap-2 mb-5 justify-content-between align-items-center">
                <p class="text-muted" style="font-size: 1rem;">🟡 Baris kuning menunjukkan posisi Anda di leaderboard</p>
                <input type="text" class="form-control form-control-sm w-250px" placeholder="Cari Nama" id="search-mahasiswa" />
            </div>

            @php
                $authUserId = auth()->id();
            @endphp

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle" id="table-leaderboard" data-auth-user="{{ $authUserId }}">
                    <thead>
                        <tr>
                            <th>Juara</th>
                            <th>Username</th>
                            <th>AlgoPoin</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <script>
            (function () {
                const AUTH_ID = @json($authUserId);

                function applyHighlight() {
                    document.querySelectorAll('#table-leaderboard tbody tr').forEach(tr => {
                        const hidden = tr.querySelector('input.js-id-user');
                        if (!hidden) return;

                        if (hidden.value === AUTH_ID) {
                            tr.classList.add('highlight-user', 'animate-up');
                        } else {
                            tr.classList.remove('highlight-user', 'animate-up');
                        }
                    });
                }

                document.addEventListener('DOMContentLoaded', function () {
                    applyHighlight();

                    // If DataTables is used, re-apply on every draw
                    if (window.jQuery && $.fn && $.fn.dataTable) {
                        $('#table-leaderboard').on('draw.dt', applyHighlight);
                    } else {
                        // Fallback: observe tbody changes
                        const tbody = document.querySelector('#table-leaderboard tbody');
                        if (tbody && 'MutationObserver' in window) {
                            const observer = new MutationObserver(applyHighlight);
                            observer.observe(tbody, { childList: true });
                        }
                    }
                });
            })();
            </script>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/leaderboard/index.js') }}"></script>
@endpush
