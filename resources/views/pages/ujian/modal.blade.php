<div class="modal fade" tabindex="-1" id="modal-confidence" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    Konfirmasi Jawaban
                </h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <form action="" method="post" id="form-mahasiswa">
                @csrf
                <div class="modal-body">
                    <p>Apakah Anda yakin dengan jawaban yang telah Anda pilih?</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" id="-submittidak-yakin">Tidak Yakin</button>
                    <button type="button" class="btn btn-primary" id="submit-yakin">Yakin</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- modal-konfirmasi-jawaban -->
<div class="modal fade" tabindex="-1" id="modal-konfirmasi-jawaban" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    Konfirmasi Jawaban
                </h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <form action="" method="post" id="form-mahasiswa">
                @csrf
                <div class="modal-body">
                    <p style="text-align: center; font-size: medium; font-weight: 600;">Apakah kamu yakin dengan jawaban kamu?</p>
                    <img src="{{ asset('assets/media/img/bingung.webp') }}" alt="Bingung" style="max-width: 170px; display: block; margin: 10px auto;">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" id="submit-tidak-yakin" onclick="submitForm(0)">Tidak</button>
                    <button type="button" class="btn btn-primary" id="submit-yakin" onclick="submitForm(1)">Ya</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- modal-feedback-incorrect -->
<div class="modal fade" tabindex="-1" id="modal-feedback-incorrect" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    Konfirmasi Jawaban
                </h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2 btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <form action="" method="post" id="form-mahasiswa">
                @csrf
                <div class="modal-body d-flex align-items-center">
                    <div style="flex: 1;">
                        <p style="font-size: medium; font-weight: 400; margin-bottom: 0;"><span
                            style="color: #0a3a71; font-weight: bold;">Algoritma </span>dan<span style="font-weight: bold; color: #0a3a71;"> Tipe Data </span>
                            yang kamu susun masih salah, teliti ulang jawaban kamu!
                        </p>
                        <br>
                        <p id="feedback-ujian"></p>
                    </div>
                    <div style="flex-shrink: 0; margin-left: 24px;">
                        <img src="{{ asset('assets/media/img/fail.webp') }}" alt="fail" style="max-width: 170px; display: block;">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Ulang</button>
                    <!-- <button type="button" class="btn btn-primary" id="submit-yakin">Yakin</button> -->
                </div>
            </form>
        </div>
    </div>
</div>


<!-- modal-feedback-correct -->
<div class="modal fade" tabindex="-1" id="modal-feedback-correct" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    Konfirmasi Jawaban
                </h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <form action="" method="post" id="form-mahasiswa">
                @csrf
                <div class="modal-body d-flex align-items-center">
                    <div style="flex: 1;">
                        <p style="font-size: medium; font-weight: 400; margin-bottom: 0;"><span
                            style="color: #0a3a71; font-weight: bold;">Algoritma </span>dan<span style="font-weight: bold; color: #0a3a71;"> Tipe Data </span>
                            yang kamu susun sudah benar, selamat ya!
                        </p>
                    </div>
                    {{-- <div style="flex-shrink: 0; margin-left: 24px;">
                        <img src="{{ asset('assets/media/img/fail.webp') }}" alt="fail" style="max-width: 170px; display: block;">
                    </div> --}}
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="window.location.href='{{ route('quiz.question-list', ['level' => $soal->id_level]) }}'">Selesai</button>
                    <!-- <button type="button" class="btn btn-primary" id="submit-yakin">Yakin</button> -->
                </div>
            </form>
        </div>
    </div>
</div>


<!-- modal-konfirmasi-jawaban KONVERSI -->
<div class="modal fade" tabindex="-1" id="modal-konfirmasi-jawaban-konversi" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    Konfirmasi Jawaban
                </h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <form action="" method="post" id="form-mahasiswa">
                @csrf
                <div class="modal-body">
                    <p style="text-align: center; font-size: medium; font-weight: 600;">Apakah kamu yakin dengan jawaban kamu?</p>
                    <img src="{{ asset('assets/media/img/bingung.webp') }}" alt="Bingung" style="max-width: 170px; display: block; margin: 10px auto;">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" id="submit-tidak-yakin" data-bs-dismiss="modal">Tidak</button>
                    <button type="button" class="btn btn-primary" id="submit-yakin" onclick="submitKonversi()">Ya</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- modal-feedback-incorrect KONVERSI-->
<div class="modal fade" tabindex="-1" id="modal-feedback-incorrect-konversi" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    Konfirmasi Jawaban
                </h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2 btn-close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <form action="" method="post" id="form-mahasiswa">
                @csrf
                <div class="modal-body d-flex align-items-center">
                    <div style="flex: 1;">
                        <p style="font-size: medium; font-weight: 400; margin-bottom: 0;"><span
                            style="color: #0a3a71; font-weight: bold;">Konversi </span>yang kamu susun masih salah, teliti ulang jawaban kamu!
                        </p>
                        <br>
                        <p id="feedback-ujian-konversi"></p>
                    </div>
                    <div style="flex-shrink: 0; margin-left: 24px;">
                        <img src="{{ asset('assets/media/img/fail.webp') }}" alt="fail" style="max-width: 170px; display: block;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                    <!-- <button type="button" class="btn btn-primary" id="submit-yakin">Yakin</button> -->
                </div>
            </form>
        </div>
    </div>
</div>


<!-- modal-feedback-correct KONVERSI-->
<div class="modal fade" tabindex="-1" id="modal-feedback-correct-konversi" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    Konfirmasi Jawaban
                </h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <form action="" method="post" id="form-mahasiswa">
                @csrf
                <div class="modal-body">
                    <div class="d-flex align-items-center mb-3">
                        <div style="flex: 1;">
                            <p style="font-size: medium; font-weight: 400; margin-bottom: 0;">
                                <span style="color: #0a3a71; font-weight: bold;">Konversi </span>Yang kamu susun sudah benar, selamat ya!
                            </p>
                        </div>
                        <div style="flex-shrink: 0; margin-left: 24px;">
                            <img src="{{ asset('assets/media/img/benar.webp') }}" alt="benar" style="max-width: 170px; display: block;">
                        </div>
                    </div>

                    {{-- Output awal dari server (selalu tampil) --}}
                    <div id="java-run-result" style="background: #f5f5f5; border-radius: 6px; padding: 12px; font-family: monospace; color: #333;">
                        <!-- Hasil output Java akan ditampilkan di sini -->
                    </div>

                    {{-- Scanner Section: hanya muncul jika kode mengandung Scanner --}}
                    <div id="scanner-section-ujian" class="d-none" style="margin-top: 16px;">
                        <hr>
                        <p style="font-size: small; font-weight: 600; margin-bottom: 8px;">
                            <i class="bi bi-keyboard me-1"></i> Input Scanner
                        </p>
                        <p style="font-size: small; color: #6b7280; margin-bottom: 12px;">
                            Masukkan nilai input lalu klik <strong>Jalankan</strong> untuk melihat hasilnya.
                            Input ini tidak mempengaruhi nilai kamu.
                        </p>

                        {{-- Field input — diisi dinamis oleh renderScannerFieldsUjian() --}}
                        <div id="scanner-fields-ujian" class="d-flex flex-column gap-2 mb-3"></div>

                        <button type="button" id="btn-run-scanner-ujian" class="btn btn-sm btn-primary" onclick="runScannerUjian()">
                            <i class="bi bi-play-fill me-1"></i> Jalankan
                        </button>

                        {{-- Output setelah run dengan input siswa --}}
                        <div id="java-run-result-scanner"
                             style="margin-top: 12px; background: #f5f5f5; border-radius: 6px; padding: 12px; font-family: monospace; color: #333; display: none;">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-lanjut-correct">Selesai</button>
                </div>
            </form>
        </div>
    </div>
</div>
