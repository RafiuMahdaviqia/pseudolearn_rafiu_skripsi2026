@extends('layouts.main')

@section('content')
<div class="container-fluid px-4" id="log-ujian-kode-detail-kode-container">
    <div class="row">
        <div class="col-12">
            <div class="bg-white rounded-4 shadow-sm p-8 mb-5">

                <div class="d-flex justify-content-between align-items-center mb-6">
                    <a href="{{ route('log-ujian-kode.detail', $id_mahasiswa) }}?level={{ $id_level }}&soal={{ $id_soal }}"
                       class="btn btn-sm btn-secondary ps-2">
                        <i class="ki-outline ki-left fs-2"></i>
                        Kembali
                    </a>
                </div>

                <div class="row g-4 mb-8">
                    <div class="col-md-4">
                        <div class="card bg-light-primary border-1 border-primary">
                            <div class="card-body p-5">
                                <h5 class="card-title mb-4 text-primary">Informasi Mahasiswa</h5>
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">Nama</dt>
                                    <dd class="col-sm-8">{{ optional($dataMahasiswa)->name ?? '-' }}</dd>

                                    <dt class="col-sm-4">NIM</dt>
                                    <dd class="col-sm-8">{{ optional($dataMahasiswa)->nim ?? '-' }}</dd>

                                    <dt class="col-sm-4">Kelas</dt>
                                    <dd class="col-sm-8">{{ optional($dataMahasiswa)->kelas_name ?? '-' }}</dd>

                                    <dt class="col-sm-4">Angkatan</dt>
                                    <dd class="col-sm-8">{{ optional($dataMahasiswa)->angkatan ?? '-' }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="card bg-light-success border-1 border-success">
                            <div class="card-body p-5">
                                <h5 class="card-title mb-4 text-success">Informasi Ujian Kode</h5>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <dl class="row mb-0">
                                            <dt class="col-sm-5">Level</dt>
                                            <dd class="col-sm-7">{{ optional($ujianKode)->level_name ?? '-' }}</dd>

                                            <dt class="col-sm-5">Judul Soal</dt>
                                            <dd class="col-sm-7">{{ optional($ujianKode)->judul_soal ?? '-' }}</dd>

                                            <dt class="col-sm-5">Tanggal Ujian</dt>
                                            <dd class="col-sm-7">
                                                {{ optional($ujianKode)->updated_at
                                                    ? \Carbon\Carbon::parse($ujianKode->updated_at)->translatedFormat('d M Y')
                                                    : '-' }}
                                            </dd>

                                            <dt class="col-sm-5">Waktu</dt>
                                            <dd class="col-sm-7">
                                                @php
                                                    $raw = optional($ujianKode)->waktu;
                                                    if (!is_null($raw) && $raw !== '') {
                                                        $sec   = intval($raw);
                                                        $h     = intdiv($sec, 3600);
                                                        $m     = intdiv($sec % 3600, 60);
                                                        $s     = $sec % 60;
                                                        $parts = [];
                                                        if ($h > 0) $parts[] = $h . ' jam';
                                                        if ($m > 0) $parts[] = $m . ' menit';
                                                        if ($s > 0 || count($parts) === 0) $parts[] = $s . ' detik';
                                                        echo implode(' ', $parts);
                                                    } else {
                                                        echo '-';
                                                    }
                                                @endphp
                                            </dd>
                                        </dl>
                                    </div>
                                    <div class="col-sm-6">
                                        <dl class="row mb-0">
                                            <dt class="col-sm-5">Nilai</dt>
                                            <dd class="col-sm-7">
                                                @php $nilai = optional($ujianKode)->nilai; @endphp
                                                @if (!is_null($nilai))
                                                    @if ($nilai >= 80)
                                                        <span class="badge badge-success">{{ $nilai }}</span>
                                                    @elseif ($nilai >= 60)
                                                        <span class="badge badge-warning">{{ $nilai }}</span>
                                                    @else
                                                        <span class="badge badge-danger">{{ $nilai }}</span>
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Jawaban Mahasiswa --}}
                <div class="card border-2 mb-6">
                    <div class="card-header d-flex align-items-center" style="min-height: 50px;">
                        <i class="ki-outline ki-code fs-2 text-primary me-2"></i>
                        <span class="fw-bold">Jawaban Mahasiswa (Kode Program)</span>
                    </div>
                    <div class="card-body p-5">
                        @if (!empty(optional($ujianKode)->jawaban))
                            <pre class="bg-light rounded p-4 mb-0"
                                 style="white-space: pre-wrap; word-break: break-word; overflow-wrap: anywhere; max-height: 500px; overflow-y: auto;">{{ $ujianKode->jawaban }}</pre>
                        @else
                            <div class="alert alert-warning mb-0">Belum ada jawaban.</div>
                        @endif
                    </div>
                </div>

                {{-- Output Program --}}
                <div class="card border-2">
                    <div class="card-header d-flex align-items-center" style="min-height: 50px;">
                        <i class="ki-outline ki-terminal fs-2 text-success me-2"></i>
                        <span class="fw-bold">Output Program</span>
                    </div>
                    <div class="card-body p-5">
                        @if (!empty(optional($ujianKode)->output))
                            <pre class="bg-dark text-white rounded p-4 mb-0"
                                 style="white-space: pre-wrap; word-break: break-word; overflow-wrap: anywhere; max-height: 400px; overflow-y: auto;">{{ $ujianKode->output }}</pre>
                        @else
                            <div class="alert alert-secondary mb-0">Tidak ada output.</div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
