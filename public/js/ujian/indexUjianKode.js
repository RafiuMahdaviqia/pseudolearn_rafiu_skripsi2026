var APP_URL = window.APP_URL || "/";

function reloadUjian() {
    Swal.fire({
        title: "Muat Ulang Ujian?",
        text: "Jawaban sebelumnya akan hilang.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya",
        cancelButtonText: "Tidak",
    }).then((result) => {
        if (result.isConfirmed) {
            location.reload();
        }
    });
}

function back(id_level) {
    Swal.fire({
        title: "Kembali ke Daftar Soal?",
        text: "Jawaban sebelumnya akan hilang.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya",
        cancelButtonText: "Tidak",
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href =
                APP_URL + "quiz/question-list?level=" + id_level;
        }
    });
}

function openModalGuide() {
    var modal = new bootstrap.Modal(document.getElementById("modal-guide"));
    modal.show();
}

function openModalKonfirmasi() {
    var modal = new bootstrap.Modal(
        document.getElementById("modal-konfirmasi-jawaban-konversi"),
    );
    modal.show();
}

function openModalFeedback() {
    var modal = new bootstrap.Modal(document.getElementById("modal-feedback"));
    var modalKonfirmasi = bootstrap.Modal.getInstance(
        document.getElementById("modal-konfirmasi-jawaban-konversi"),
    );
    if (modalKonfirmasi) {
        modalKonfirmasi.hide();
    }
    modal.show();
}

function submitKonversi() {
    var modalKonfirmasi = bootstrap.Modal.getInstance(
        document.getElementById("modal-konfirmasi-jawaban-konversi"),
    );
    var waktu = $("#waktu-ujian-detik").val();

    // Ambil jawaban dari drag-and-drop box
    var kodeLangkah = [];
    var boxes = document.querySelectorAll(".answer-box.box-java");
    boxes.forEach(function (box) {
        var item = box.querySelector(".drag-item");
        kodeLangkah.push(
            item ? item.innerText.replace(/\s+/g, " ").trim() : "",
        );
    });

    // Reset highlight box sebelumnya
    boxes.forEach(function (box) {
        box.style.borderColor = "";
        box.classList.remove("shake");
    });

    $.ajax({
        url: APP_URL + "ujian-kode/submit-konversi",
        type: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
            id_soal_konversi: $("#id-soal-konversi").val(),
            kode_langkah: kodeLangkah,
            waktu: waktu,
        },
        success: function (response) {
            modalKonfirmasi.hide();

            // Ambil baris kode yang diisi mahasiswa
            var filledLines = kodeLangkah.filter(function (l) {
                return l.length > 0;
            });
            var hasScanner = filledLines.some(function (line) {
                return /\bScanner\b/.test(line);
            });

            if (hasScanner) {
                // Jika ada Scanner, sembunyikan output DB karena menggunakan nilai input guru
                document.getElementById("java-run-result").textContent = "";
                document.getElementById("java-run-result").style.display =
                    "none";
                renderScannerFieldsUjian(filledLines);
            } else {
                // Jika tidak ada Scanner, tampilkan output DB langsung
                document.getElementById("java-run-result").textContent =
                    response.java_output || "";
                document.getElementById("java-run-result").style.display =
                    "block";
                var scannerSection = document.getElementById(
                    "scanner-section-ujian",
                );
                if (scannerSection) scannerSection.classList.add("d-none");
            }

            var modalCorrect = new bootstrap.Modal(
                document.getElementById("modal-feedback-correct-konversi"),
            );
            modalCorrect.show();

            // Tombol selesai — assign URL dinamis sesuai response
            document.querySelector(
                "#modal-feedback-correct-konversi .btn-lanjut-correct",
            ).onclick = function () {
                let url = `${APP_URL}quiz/question-list?level=${document.getElementById("id-level").value}`;
                if (response.konversi) {
                    url += `&konversi_id=${encodeURIComponent(response.konversi.id)}`;
                }
                window.location.href = url;
            };
        },
        error: function (xhr) {
            const res = xhr.responseJSON;

            if (res?.message?.errors) {
                var allBoxes = document.querySelectorAll(
                    ".answer-box.box-java",
                );
                res.message.errors.forEach(function (err) {
                    if (allBoxes[err.index]) {
                        allBoxes[err.index].style.borderColor = "red";
                        allBoxes[err.index].classList.add("shake");
                        setTimeout(function () {
                            allBoxes[err.index].classList.remove("shake");
                        }, 400);
                    }
                });
            }

            $.ajax({
                url: APP_URL + "nyawa/status",
                type: "GET",
                dataType: "json",
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
                success: function (data) {
                    const livesEl = document.getElementById("lives-count");
                    if (livesEl)
                        livesEl.innerText =
                            data && typeof data.lives !== "undefined"
                                ? data.lives
                                : 0;

                    openModalFeedbackIncorrect(
                        res?.message?.message ?? "Terdapat jawaban salah",
                        data.lives,
                    );
                },
                error: function (xhr) {
                    // console.error("Gagal mendapatkan status nyawa", xhr);
                },
            });
        },
    });
}

function openModalFeedbackIncorrect(feedbackText, lives = null) {
    // Tampilkan modal incorrect konversi
    var modalIncorrect = new bootstrap.Modal(
        document.getElementById("modal-feedback-incorrect-konversi"),
    );
    var modalKonfirmasi = bootstrap.Modal.getInstance(
        document.getElementById("modal-konfirmasi-jawaban-konversi"),
    );
    var id_level = document.getElementById("id-level").value;

    // Ganti pesan dan tombol jika nyawa habis
    if (parseInt(lives) <= 0) {
        document.getElementById("feedback-ujian-konversi").innerHTML =
            '<span style="color:red;font-weight:bold;">Nyawa anda sudah habis, harap menunggu nyawa bertambah.</span>';

        // Ganti tombol modal
        var modalFooter = document.querySelector(
            "#modal-feedback-incorrect-konversi .modal-footer",
        );
        if (modalFooter) {
            modalFooter.innerHTML = `<button type="button" class="btn btn-primary" onclick="window.location.href='${APP_URL}quiz/question-list?level=${id_level}'">Kembali ke Daftar Soal</button>`;
        }

        // Sembunyikan tombol silang (X) pada header modal
        var closeBtn = document.querySelector(
            "#modal-feedback-incorrect-konversi .btn-close",
        );
        if (closeBtn) closeBtn.style.display = "none";
    } else {
        // Tampilkan kembali tombol silang jika masih ada nyawa
        var closeBtn = document.querySelector(
            "#modal-feedback-incorrect-konversi .btn-close",
        );
        if (closeBtn) closeBtn.style.display = "";
    }

    if (modalKonfirmasi) {
        modalKonfirmasi.hide();
    }
    modalIncorrect.show();
}

//  Parse label input dari baris kode Scanner
function parseScannerFieldsUjian(lines) {
    var fields = [];
    var printPattern = /System\.out\.print(?:ln)?\s*\(\s*["'](.+?)["']\s*\)/;
    var scannerPattern =
        /\.\s*next(?:Int|Double|Float|Long|Line|Boolean|Short|Byte)?\s*\(\s*\)/i;

    for (var i = 0; i < lines.length; i++) {
        if (scannerPattern.test(lines[i])) {
            var label = "";
            if (i > 0 && printPattern.test(lines[i - 1])) {
                var match = lines[i - 1].match(printPattern);
                label = match ? match[1] : "";
            }
            fields.push({ label: label || "Input", index: fields.length });
        }
    }
    return fields;
}

//  Render input fields scanner di modal
function renderScannerFieldsUjian(lines) {
    var section = document.getElementById("scanner-section-ujian");
    var container = document.getElementById("scanner-fields-ujian");
    var resultEl = document.getElementById("java-run-result-scanner");

    if (!section || !container) return;

    var fields = parseScannerFieldsUjian(lines);

    // Reset output scanner
    if (resultEl) {
        resultEl.textContent = "";
        resultEl.style.display = "none";
    }

    if (fields.length === 0) {
        section.classList.add("d-none");
        return;
    }

    container.innerHTML = "";
    fields.forEach(function (field) {
        var div = document.createElement("div");
        div.className = "d-flex align-items-center gap-3";
        div.innerHTML =
            '<label style="min-width:200px;font-family:monospace;font-size:13px;margin-bottom:0;">' +
            field.label +
            "</label>" +
            '<input type="text" class="form-control form-control-sm scanner-ujian-field" ' +
            'data-index="' +
            field.index +
            '" placeholder="Masukkan nilai…" />';
        container.appendChild(div);
    });

    section.classList.remove("d-none");
}

//  Kirim scanner input -> tampilkan output
window.runScannerUjian = function () {
    var values = [];
    var allFilled = true;

    document.querySelectorAll(".scanner-ujian-field").forEach(function (input) {
        var val = input.value.trim();
        if (!val) {
            allFilled = false;
            input.classList.add("is-invalid");
        } else {
            input.classList.remove("is-invalid");
            values.push(val);
        }
    });

    if (!allFilled) {
        Swal.fire({
            icon: "warning",
            text: "Semua input harus diisi.",
            confirmButtonText: "OK",
        });
        return;
    }

    //console.log('id_soal_konversi:', document.getElementById('id-soal-konversi').value);

    var btnRun = document.getElementById("btn-run-scanner-ujian");
    if (btnRun) {
        btnRun.disabled = true;
        btnRun.innerHTML =
            '<span class="spinner-border spinner-border-sm me-1"></span>Menjalankan…';
    }

    $.ajax({
        url: APP_URL + "ujian-kode/run-scanner",
        type: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
            id_soal_konversi: document.getElementById('id-soal-konversi').value,
            scanner_input: values.join("\n"),
        },
        success: function (res) {
            var resultEl = document.getElementById("java-run-result-scanner");
            if (resultEl) {
                resultEl.textContent = String(res.output ?? "").trim();
                resultEl.style.display = "block";
            }
        },
        error: function (xhr) {
            Swal.fire({
                icon: "error",
                text: xhr?.responseJSON?.message || "Gagal menjalankan kode.",
                confirmButtonText: "OK",
            });
        },
        complete: function () {
            if (btnRun) {
                btnRun.disabled = false;
                btnRun.innerHTML =
                    '<i class="bi bi-play-fill me-1"></i> Jalankan';
            }
        },
    });
};
