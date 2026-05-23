// var target = document.querySelector("#kt_app_main");
// var blockUI = new KTBlockUI(target);
var APP_URL = window.APP_URL || "/";

function buildQuizQuestionListUrl(levelId) {
    const base = window.QUIZ_QUESTION_LIST_URL || (APP_URL + "quiz/question-list-z");
    return base + "?level=" + encodeURIComponent(levelId);
}

// $(() => {
//     blockUI.block();
//     initTable();
//     blockUI.release();
// });


function collectJawabanUser() {
    const tipe = [];
    document.querySelectorAll('.answer-box.box-tipe').forEach(box => {
        tipe.push({
            variabel: box.dataset.variable || null,
            jawaban: box.querySelector('.drag-item') ? box.querySelector('.drag-item').innerText.trim() : null
        });
    });

    const algoritma = [];
    document.querySelectorAll('.answer-box.box-algo').forEach(box => {
        algoritma.push({
            urutan: box.dataset.index || null,
            clue: box.dataset.clue === '1' ? 1 : 0,
            langkah: box.querySelector('.drag-item') ? box.querySelector('.drag-item').innerText.trim() : null
        });
    });

    return { tipe_data: tipe, algoritma: algoritma };
}

function openModalKonfirmasi() {
    const data = collectJawabanUser();
    const hidden = document.getElementById('jawaban-user');
    if (hidden) hidden.value = JSON.stringify(data);

    const modalEl = document.getElementById('modal-konfirmasi-jawaban');
    if (modalEl && typeof bootstrap !== 'undefined') new bootstrap.Modal(modalEl).show();
}

// Submit jawaban ke server
function submitForm(confidence) {
    const soalId = new URLSearchParams(window.location.search).get('id');
    const jawabanData = collectJawabanUser();
    const waktu = window.timerElapsed;

    $.ajax({
        url: APP_URL + "ujian/submit",
        type: "POST",
        data: JSON.stringify({
            _token: $('meta[name="csrf-token"]').attr("content"),
            soal_id: soalId,
            jawaban: jawabanData,
            waktu: waktu,
            confidence: confidence // 1: yakin, 0: tidak yakin
        }),
        processData: false,
        contentType: "application/json",
        success: function (response) {
            if(confidence == 1) {
                if (response.correct === false) {
                    let feedbackText = '';
                    if (response.correct_tipe_data === false && response.correct_algoritma === false) {
                        feedbackText = 'Jawaban kamu salah pada Tipe Data dan Algoritma';
                    } else if (response.correct_tipe_data === false) {
                        feedbackText = response.tipe_mismatch ? response.tipe_mismatch : 'Kesalahan pada Tipe Data';
                    } else if (response.correct_algoritma === false) {
                        feedbackText = response.algoritma_mismatch ? response.algoritma_mismatch : 'Kesalahan pada Algoritma';
                    }

                    $.ajax({
                        url: APP_URL + "nyawa/status",
                        type: "GET",
                        dataType: "json",
                        headers: {
                            "Accept": "application/json",
                            "X-Requested-With": "XMLHttpRequest"
                        },
                        success: function (data) {
                            const livesEl = document.getElementById("lives-count");
                            if (livesEl) livesEl.innerText = (data && typeof data.lives !== 'undefined') ? data.lives : 0;

                            openModalFeedbackIncorrect(feedbackText, data.lives, response.decoy || null);
                        },
                        error: function (xhr) {
                            // console.error("Gagal mendapatkan status nyawa", xhr);
                        }
                    });
                } else {
                    openModalFeedbackCorrect(response.pencapaian, response.badge);
                }
            } else if(confidence == 0) {
                $('#modal-konfirmasi-jawaban').modal('hide');
            }
        },
        error: function (xhr) {
            blockUI.release();
            Swal.fire({
                text:
                    xhr.responseJSON?.message ||
                    "Terjadi kesalahan sistem.",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "OK",
                customClass: {
                    confirmButton: "btn btn-primary",
                },
            });
        },
    });
}

function renderDecoyList(listEl, items) {
    if (!listEl) return 0;
    listEl.innerHTML = '';
    if (!Array.isArray(items)) return 0;

    let count = 0;
    items.forEach(item => {
        if (!item) return;
        const li = document.createElement('li');
        li.textContent = item;
        listEl.appendChild(li);
        count++;
    });

    return count;
}

function setDecoyUjian(decoy, lives) {
    const section = document.getElementById('decoy-section-ujian');
    if (!section) return;

    const livesInt = parseInt(lives, 10);
    const noLives = Number.isFinite(livesInt) && livesInt <= 0;

    if (!decoy || noLives) {
        section.classList.add('d-none');
        const tipeList = document.getElementById('decoy-tipe-list');
        const algoList = document.getElementById('decoy-algo-list');
        if (tipeList) tipeList.innerHTML = '';
        if (algoList) algoList.innerHTML = '';
        return;
    }

    const tipeList = document.getElementById('decoy-tipe-list');
    const algoList = document.getElementById('decoy-algo-list');
    const tipeCount = renderDecoyList(tipeList, decoy.tipe_data || []);
    const algoCount = renderDecoyList(algoList, decoy.algoritma || []);

    if (tipeCount === 0 && algoCount === 0) {
        section.classList.add('d-none');
        return;
    }

    section.classList.remove('d-none');
}

function openModalFeedbackIncorrect(feedbackText, lives = null, decoy = null) {
    var modal = new bootstrap.Modal(document.getElementById('modal-feedback-incorrect'));
    var modalKonfirmasi = bootstrap.Modal.getInstance(document.getElementById('modal-konfirmasi-jawaban'));
    document.getElementById('feedback-ujian').innerText = feedbackText;
    var id_level = document.getElementById('id-level').value;

    // Ganti tombol dan pesan jika nyawa habis
    if (parseInt(lives) <= 0) {
        // Ganti pesan
        document.getElementById('feedback-ujian').innerHTML =
            '<span style="color:red;font-weight:bold;">Nyawa anda sudah habis, harap menunggu nyawa bertambah.</span>';

        // Ganti tombol modal
        var modalFooter = document.querySelector('#modal-feedback-incorrect .modal-footer');
        if (modalFooter) {
            modalFooter.innerHTML = `<button type="button" class="btn btn-primary" onclick="window.location.href='${buildQuizQuestionListUrl(id_level)}'">Kembali ke Daftar Soal</button>`;
        }

        // Sembunyikan tombol silang (X) pada header modal
        var closeBtn = document.querySelector('#modal-feedback-incorrect .btn-close');
        if (closeBtn) closeBtn.style.display = 'none';
    } else {
        // Tampilkan kembali tombol silang jika masih ada nyawa
        var closeBtn = document.querySelector('#modal-feedback-incorrect .btn-close');
        if (closeBtn) closeBtn.style.display = '';
    }

    if (modalKonfirmasi) {
        modalKonfirmasi.hide();
    }
    setDecoyUjian(decoy, lives);
    modal.show();
}

function openModalFeedbackCorrect(pencapaian = null, badge = null) {
    var modal = new bootstrap.Modal(document.getElementById('modal-feedback-correct'));
    var modalKonfirmasi = bootstrap.Modal.getInstance(document.getElementById('modal-konfirmasi-jawaban'));
    if (modalKonfirmasi) {
        modalKonfirmasi.hide();
    }
    modal.show();
    // console.log(pencapaian, badge);
    // Setelah klik tombol selesai, redirect dengan parameter pencapaian
    document.querySelector('#modal-feedback-correct .btn-primary').onclick = function() {
        let url = buildQuizQuestionListUrl(document.getElementById('id-level').value);
        if (pencapaian) {
            url += `&pencapaian_id=${encodeURIComponent(pencapaian.id)}`;
        }
        if (badge) {
            url += `&badge_id=${encodeURIComponent(badge.id)}`;
        }
        window.location.href = url;
    };
}

window.timerStarted = false;
window.timerInterval = null;
window.timerElapsed = 0; // detik berjalan

function startUjianTimer() {
    if (window.timerStarted) return;
    window.timerStarted = true;

    const el = document.getElementById('timer-ujian');

    function render(sec) {
        const h = String(Math.floor(sec / 3600)).padStart(2,'0');
        const m = String(Math.floor((sec % 3600) / 60)).padStart(2,'0');
        const s = String(sec % 60).padStart(2,'0');
        el.textContent = `${h}:${m}:${s}`;
    }
    render(window.timerElapsed);

    window.timerInterval = setInterval(() => {
        window.timerElapsed++;
        render(window.timerElapsed);
    }, 1000);
}

function logAnswerDrop({ type, itemText, variabel = null, index = null }) {
    try {
        const soalId = new URLSearchParams(window.location.search).get('id');
        if (!soalId) return;

        $.ajax({
            url: APP_URL + "ujian/send-log",
            type: "POST",
            data: JSON.stringify({
                _token: $('meta[name="csrf-token"]').attr("content"),
                soal_id: soalId,
                jenis: type,
                item: itemText,
                variabel: variabel,
                index: index,
                timer_second: window.timerElapsed
            }),
            processData: false,
            contentType: "application/json",
            error: function (xhr) {
                Swal.fire({
                    text: xhr.responseJSON?.message || "Terjadi kesalahan sistem.",
                    icon: "error",
                    confirmButtonText: "OK",
                    customClass: {
                        confirmButton: "btn btn-primary",
                    },
                });
            }
        });
    } catch (e) {
        console.error("Gagal mengirim log:", e);
    }
}

// (PATCH) di handler drop answer-box, setelah this.appendChild(dragged); tambahkan pemicu startUjianTimer()
document.querySelectorAll('.answer-box').forEach(box => {
    box.addEventListener('dragover', e => e.preventDefault());
    box.addEventListener('drop', function (e) {
        e.preventDefault();
        const dragged = document.querySelector('.dragging');
        if (!dragged) return;

        const sourceClass = dragged.getAttribute('data-source');
        const isBoxTipe = this.classList.contains('box-tipe');
        const isBoxAlgo = this.classList.contains('box-algo');

        if ((isBoxTipe && sourceClass !== 'tipe') || (isBoxAlgo && sourceClass !== 'algo')) {
            this.classList.add('shake');
            if (!this.querySelector('.error-msg')) {
                const msg = isBoxTipe ? 'Ini adalah bagian answer-box tipe data!' : 'Ini adalah bagian answer-box Algoritma!';
                const errorMsg = document.createElement('div');
                errorMsg.className = 'error-msg';
                errorMsg.style.color = 'red';
                errorMsg.style.fontWeight = 'bold';
                errorMsg.style.marginLeft = '8px';
                errorMsg.innerText = msg;
                this.appendChild(errorMsg);
                setTimeout(() => { errorMsg.remove(); this.classList.remove('shake'); }, 2000);
            }
            return;
        }

        // Cegah isi lebih dari satu (hanya 1 jawaban per box)
        if (this.querySelector('.drag-item')) {
            this.classList.add('shake');
            setTimeout(() => this.classList.remove('shake'), 400);
            return;
        }

        this.appendChild(dragged);
        dragged.classList.remove('dragging');

        // LOG hanya saat sukses isi answer-box
        const itemText = dragged.innerText.trim();
        if (isBoxTipe) {
            logAnswerDrop({
                type: 'tipe_data',
                itemText,
                variabel: this.dataset.variable || null
            });
        } else if (isBoxAlgo) {
            logAnswerDrop({
                type: 'algoritma',
                itemText,
                index: this.dataset.index || null
            });
        }

        startUjianTimer();
    });
});

function reloadUjian() {
    Swal.fire({
        title: 'Muat Ulang Ujian?',
        text: "Jawaban sebelumnya akan hilang.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Tidak'
    }).then((result) => {
        if (result.isConfirmed) {
            location.reload();
        }
    });
}

function back(id_level){
    Swal.fire({
        title: 'Kembali ke Daftar Soal?',
        text: "Jawaban sebelumnya akan hilang.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Tidak'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = buildQuizQuestionListUrl(id_level);
        }
    });
}
