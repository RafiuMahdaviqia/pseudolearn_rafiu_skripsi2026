@extends('layouts.main')

@section('content')
    <div class="container-fluid px-4" id="analysis-container">
        <div class="row">
            <div class="col-12">
                <div class="bg-white rounded-4 shadow-sm p-8 mb-5">
                    <div class="d-flex justify-content-between align-items-center mb-6">
                        <div class="d-flex gap-2">
                            <a href="{{ url("ujian-konversi/detail/$id_mahasiswa") }}?level={{ $id_level }}&soal={{ $id_soal }}" class="btn btn-sm btn-secondary ps-2">
                                <i class="ki-outline ki-left fs-2"></i>
                                Kembali
                            </a>
                        </div>
                        {{-- <div class="d-flex gap-2 align-items-center">
                            <input type="text" class="form-control form-control-sm w-250px" placeholder="Cari level" id="search-level" />
                        </div> --}}
                    </div>

                    <div class="row g-4">
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
                                    <h5 class="card-title mb-4 text-success">Informasi Soal</h5>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <dl class="row mb-0">
                                                <dt class="col-sm-5">Level</dt>
                                                <dd class="col-sm-7">{{ optional($konversi)->level_name ?? '-' }}</dd>

                                                <dt class="col-sm-5">Judul Soal</dt>
                                                <dd class="col-sm-7">{{ optional($konversi)->judul_soal ?? '-' }}</dd>

                                                <dt class="col-sm-5">Tanggal Ujian</dt>
                                                <dd class="col-sm-7">
                                                    {{ optional($konversi)->updated_at ? \Carbon\Carbon::parse($konversi->updated_at)->translatedFormat('d M Y') : '-' }}
                                                </dd>

                                                <dt class="col-sm-5">Waktu</dt>
                                                <dd class="col-sm-7">
                                                    <?php
                                                        $raw = optional($konversi)->waktu;
                                                        if ($raw === null || $raw === '') {
                                                            echo '';
                                                        } else {
                                                            if (preg_match('/^\s*[+-]?\d+/', (string) $raw, $matches)) {
                                                                $sec = intval($matches[0], 10);
                                                                $h = intdiv($sec, 3600);
                                                                $minutes = intdiv($sec % 3600, 60);
                                                                $s = $sec % 60;

                                                                $parts = [];
                                                                if ($h > 0) $parts[] = $h . ' jam';
                                                                if ($minutes > 0) $parts[] = $minutes . ' menit';
                                                                if ($s > 0 || count($parts) === 0) $parts[] = $s . ' detik';

                                                                echo implode(' ', $parts);
                                                            } else {
                                                                echo e((string) $raw);
                                                            }
                                                        }
                                                    ?>
                                                </dd>
                                            </dl>
                                        </div>
                                        <div class="col-sm-6">
                                            <dl class="row mb-0">
                                                <dt class="col-sm-5">Nilai</dt>
                                                <dd class="col-sm-7">{{ optional($konversi)->nilai ?? '-' }}</dd>
                                            
                                                
                                                @php
                                                    $debugItems = data_get($debugKonversi, 'debug', []);
                                                    if (!is_iterable($debugItems)) {
                                                        $debugItems = [];
                                                    }
                                                
                                                    // Hitung rata-rata similarity dan jumlah perfect match
                                                    $totalSim = 0;
                                                    $countSim = 0;
                                                    $perfectMatch = 0;
                                                    foreach ($debugItems as $item) {
                                                        $sim = is_numeric(data_get($item, 'similarity')) ? (float) data_get($item, 'similarity') : null;
                                                        if ($sim !== null) {
                                                            $totalSim += $sim;
                                                            $countSim++;
                                                            if ($sim == 1.0) $perfectMatch++;
                                                        }
                                                    }
                                                    $avgSim = $countSim > 0 ? round($totalSim / $countSim * 100, 2) : 0;
                                                @endphp
                                                
                                                @if ($countSim > 0)
                                                <dt class="col-sm-5"> Avg Similarity: </dt>
                                                <dd class="col-sm-7">{{ $avgSim }}%</dd>

                                                <dt class="col-sm-5"> Perfect Match: </dt>
                                                <dd class="col-sm-7">{{ $perfectMatch }} langkah</dd>
                                                @else
                                                <dt class="col-sm-5"> Rata-rata Similarity: </dt>
                                                <dd class="col-sm-7">-</dd>
                                                <dt class="col-sm-5"> Perfect Match: </dt>
                                                <dd class="col-sm-7">-</dd>
                                                @endif
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @php
                        $debugItems = data_get($debugKonversi, 'debug', []);
                        if (!is_iterable($debugItems)) {
                            $debugItems = [];
                        }
                    @endphp

                    @if (empty($debugItems))
                        <div class="alert alert-info">Tidak ada data perbandingan.</div>
                    @else
                        <div class="row g-4 mt-5">
                            @foreach ($debugItems as $idx => $item)
                                @php
                                    $userAnswer = (string) data_get($item, 'user_answer', '');
                                    $correctAnswer = (string) data_get($item, 'correct_answer', '');
                                    $lenUser = function_exists('mb_strlen') ? mb_strlen($userAnswer, 'UTF-8') : strlen($userAnswer);
                                    $lenCorrect = function_exists('mb_strlen') ? mb_strlen($correctAnswer, 'UTF-8') : strlen($correctAnswer);
                                    $step = data_get($item, 'step', $idx + 1);
                                    $similarity = data_get($item, 'similarity');
                                    $similarityText = is_numeric($similarity) ? number_format((float) $similarity * 100, 2) . '%' : '-';
                                
                                    if ($similarity >= 0.8) {
                                        $cardBadgeClass = 'badge-success';
                                    } elseif ($similarity >= 0.5) {
                                        $cardBadgeClass = 'badge-warning';
                                    } else {
                                        $cardBadgeClass = 'badge-danger';
                                    }
                                    // dd($item);
                                @endphp

                                <div class="col-12">
                                    <div class="card border-2">
                                        <div class="card-header d-flex justify-content-between align-items-center" style="min-height: 50px">
                                            <span>Langkah {{ $step }}</span>
                                            {{-- <span class="badge {{ $cardBadgeClass }}">Similarity: {{ $similarityText }}</span> --}}
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive mx-auto">
                                                <table class="table table-bordered m-5 align-middle rounded-3" style="max-width: 880px;">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Jenis Jawaban</th>
                                                            <th>Konten</th>
                                                            <th class="text-center">Panjang Karakter</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>Jawaban Mahasiswa</td>
                                                            <td>
                                                                <pre class="mb-0" style="white-space: pre-wrap; word-break: break-word; overflow-wrap: anywhere; max-width: 100%;">{{ $userAnswer }}</pre>
                                                            </td>
                                                            <td class="text-center">{{ $lenUser }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Jawaban Benar</td>
                                                            <td>
                                                                <pre class="mb-0" style="white-space: pre-wrap; word-break: break-word; overflow-wrap: anywhere; max-width: 100%;">{{ $correctAnswer }}</pre>
                                                            </td>
                                                            <td class="text-center">{{ $lenCorrect }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            
                                            <div class="row mt-5 p-5">
                                                @php
                                                    $userTokens = data_get($item, 'user_tokens', []);
                                                    $correctTokens = data_get($item, 'correct_tokens', []);

                                                    if ($userTokens instanceof \Illuminate\Support\Collection) {
                                                        $userTokens = $userTokens->values()->all();
                                                    }
                                                    if ($correctTokens instanceof \Illuminate\Support\Collection) {
                                                        $correctTokens = $correctTokens->values()->all();
                                                    }

                                                    if (!is_iterable($userTokens)) {
                                                        $userTokens = [];
                                                    }
                                                    if (!is_iterable($correctTokens)) {
                                                        $correctTokens = [];
                                                    }
                                                @endphp
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold mb-2">1. Tokenisasi Jawaban Mahasiswa</label>
                                                    <div class="text-muted mb-2">Mahasiswa: {{ count($userTokens) }} token</div>
                                                    <textarea class="form-control" rows="5" readonly style="resize: none;">@json($userTokens, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)</textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold mb-2">1. Tokenisasi (Jawaban Benar)</label>
                                                    <div class="text-muted mb-2">Benar: {{ count($correctTokens) }} token</div>
                                                    <textarea class="form-control" rows="5" readonly style="resize: none;">@json($correctTokens, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)</textarea>
                                                </div>
                                            </div>


                                            <div class="row p-5">
                                                @php
                                                    $userKgram = data_get($item, 'user_kgrams', []);
                                                    $correctKgram = data_get($item, 'correct_kgrams', []);

                                                    if ($userKgram instanceof \Illuminate\Support\Collection) {
                                                        $userKgram = $userKgram->values()->all();
                                                    }
                                                    if ($correctKgram instanceof \Illuminate\Support\Collection) {
                                                        $correctKgram = $correctKgram->values()->all();
                                                    }

                                                    if (!is_iterable($userKgram)) {
                                                        $userKgram = [];
                                                    }
                                                    if (!is_iterable($correctKgram)) {
                                                        $correctKgram = [];
                                                    }
                                                @endphp
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold mb-2">2. K-Grams (k=2)</label>
                                                    <div class="text-muted mb-2">Mahasiswa: {{ count($userKgram) }} k-gram</div>
                                                    <textarea class="form-control" rows="5" readonly style="resize: none;">@json($userKgram, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)</textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold mb-2">2. K-Grams (Jawaban Benar)</label>
                                                    <div class="text-muted mb-2">Benar: {{ count($correctKgram) }} k-gram</div>
                                                    <textarea class="form-control" rows="5" readonly style="resize: none;">@json($correctKgram, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)</textarea>
                                                </div>
                                            </div>


                                            <div class="row p-5">
                                                @php
                                                    $userHashes = data_get($item, 'user_hashes', []);
                                                    $correctHashes = data_get($item, 'correct_hashes', []);

                                                    if ($userHashes instanceof \Illuminate\Support\Collection) {
                                                        $userHashes = $userHashes->values()->all();
                                                    }
                                                    if ($correctHashes instanceof \Illuminate\Support\Collection) {
                                                        $correctHashes = $correctHashes->values()->all();
                                                    }

                                                    if (!is_iterable($userHashes)) {
                                                        $userHashes = [];
                                                    }
                                                    if (!is_iterable($correctHashes)) {
                                                        $correctHashes = [];
                                                    }
                                                @endphp
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold mb-2">3. Hash Values</label>
                                                    <div class="text-muted mb-2">Mahasiswa: {{ count($userHashes) }} hash</div>
                                                    <textarea class="form-control" rows="5" readonly style="resize: none;">@json($userHashes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)</textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold mb-2">3. Hash Values (Jawaban Benar)</label>
                                                    <div class="text-muted mb-2">Benar: {{ count($correctHashes) }} hash</div>
                                                    <textarea class="form-control" rows="5" readonly style="resize: none;">@json($correctHashes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)</textarea>
                                                </div>
                                            </div>

                                            <div class="row p-5">
                                                @php
                                                    $userWindows = data_get($item, 'user_windows', []);
                                                    $correctWindows = data_get($item, 'correct_windows', []);

                                                    if ($userWindows instanceof \Illuminate\Support\Collection) {
                                                        $userWindows = $userWindows->values()->all();
                                                    }
                                                    if ($correctWindows instanceof \Illuminate\Support\Collection) {
                                                        $correctWindows = $correctWindows->values()->all();
                                                    }

                                                    if (!is_iterable($userWindows)) {
                                                        $userWindows = [];
                                                    }
                                                    if (!is_iterable($correctWindows)) {
                                                        $correctWindows = [];
                                                    }
                                                @endphp
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold mb-2">4. Windows (window=3)</label>
                                                    <div class="text-muted mb-2">Mahasiswa: {{ count($userWindows) }} window</div>
                                                    <textarea class="form-control" rows="5" readonly style="resize: none;">@json($userWindows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)</textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold mb-2">4. Windows (Jawaban Benar)</label>
                                                    <div class="text-muted mb-2">Benar: {{ count($correctWindows) }} window</div>
                                                    <textarea class="form-control" rows="5" readonly style="resize: none;">@json($correctWindows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)</textarea>
                                                </div>
                                            </div>

                                            <div class="row p-5">
                                                @php
                                                    $userFingerprints = data_get($item, 'user_fingerprints', []);
                                                    $correctFingerprints = data_get($item, 'correct_fingerprints', []);

                                                    if ($userFingerprints instanceof \Illuminate\Support\Collection) {
                                                        $userFingerprints = $userFingerprints->values()->all();
                                                    }
                                                    if ($correctFingerprints instanceof \Illuminate\Support\Collection) {
                                                        $correctFingerprints = $correctFingerprints->values()->all();
                                                    }

                                                    if (!is_iterable($userFingerprints)) {
                                                        $userFingerprints = [];
                                                    }
                                                    if (!is_iterable($correctFingerprints)) {
                                                        $correctFingerprints = [];
                                                    }
                                                @endphp
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold mb-2">5. Fingerprints</label>
                                                    <div class="text-muted mb-2">Mahasiswa: {{ count($userFingerprints) }} Fingerprint</div>
                                                    <textarea class="form-control" rows="5" readonly style="resize: none;">@json($userFingerprints, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)</textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold mb-2">5. Fingerprints (Jawaban Benar)</label>
                                                    <div class="text-muted mb-2">Benar: {{ count($correctFingerprints) }} Fingerprint</div>
                                                    <textarea class="form-control" rows="5" readonly style="resize: none;">@json($correctFingerprints, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)</textarea>
                                                </div>
                                            </div>


                                            <div class="row p-5">
                                                <div class="col-md-12">
                                                    <h5>6. Jaccard Similarity Coefficient</h5>
                                                    @php
                                                        $sim = is_numeric($similarity) ? (float) $similarity : null;
                                                        $similarityPercentage = $sim !== null ? round($sim * 100, 2) : null;

                                                        $similarityBadgeClass = 'badge-danger';
                                                        $statusText = 'NO MATCH';
                                                        $statusBadgeClass = 'badge-danger';

                                                        if ($sim === 1.0) {
                                                            $similarityBadgeClass = 'badge-success';
                                                            $statusBadgeClass = 'badge-success';
                                                            $statusText = 'PERFECT MATCH';
                                                        } elseif ($sim > 0.8) {
                                                            $similarityBadgeClass = 'badge-primary';
                                                            $statusBadgeClass = 'badge-primary';
                                                            $statusText = 'GOOD MATCH';
                                                        } elseif ($sim > 0.5) {
                                                            $similarityBadgeClass = 'badge-warning';
                                                            $statusBadgeClass = 'badge-warning';
                                                            $statusText = 'PARTIAL MATCH';
                                                        }

                                                        $intersection = [];
                                                        $union = [];

                                                        if (!empty($userFingerprints) && !empty($correctFingerprints)) {
                                                            $intersection = array_values(array_intersect($userFingerprints, $correctFingerprints));
                                                            $union = array_values(array_unique(array_merge($userFingerprints, $correctFingerprints)));
                                                        }
                                                    @endphp

                                                    <div class="alert alert-info">
                                                        <h4 class="mb-3">
                                                            Similarity:
                                                            @if ($similarityPercentage !== null)
                                                                <span class="badge {{ $similarityBadgeClass }}" style="font-size:16px;">
                                                                    {{ number_format($similarityPercentage, 2) }}%
                                                                </span>
                                                                <span class="badge {{ $statusBadgeClass }}">{{ $statusText }}</span>
                                                            @else
                                                                <span class="badge badge-secondary">-</span>
                                                            @endif
                                                        </h4>

                                                        @if (!empty($intersection) && !empty($union))
                                                            <p class="text-muted">
                                                                <strong>Rumus:</strong>
                                                                Intersection ÷ Union = {{ count($intersection) }} ÷ {{ count($union) }} =
                                                                {{ $similarityPercentage !== null ? number_format($similarityPercentage, 2) . '%' : '-' }}
                                                            </p>
                                                            <p><strong>Intersection:</strong> @json($intersection, JSON_UNESCAPED_UNICODE)</p>
                                                            <p><strong>Union:</strong> @json($union, JSON_UNESCAPED_UNICODE)</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- <script src="{{ asset('js/ujianKonversi/detail.js') }}"></script> --}}
@endpush
