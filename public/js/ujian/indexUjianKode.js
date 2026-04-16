var APP_URL = window.APP_URL || "/";

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
            window.location.href = APP_URL + "quiz/question-list?level=" + id_level;
        }
    });
}

function openModalGuide() {
    var modal = new bootstrap.Modal(document.getElementById('modal-guide'));
    modal.show();
}

function openModalKonfirmasi() {
    var modal = new bootstrap.Modal(document.getElementById('modal-konfirmasi-jawaban-konversi'));
    modal.show();
}

function openModalFeedback() {
    var modal = new bootstrap.Modal(document.getElementById('modal-feedback'));
    var modalKonfirmasi = bootstrap.Modal.getInstance(document.getElementById('modal-konfirmasi-jawaban-konversi'));
    if (modalKonfirmasi) {
        modalKonfirmasi.hide();
    }
    modal.show();
}

function submitKonversi() {
    var modalKonfirmasi = bootstrap.Modal.getInstance(document.getElementById('modal-konfirmasi-jawaban-konversi'));
    var waktu = $('#waktu-ujian-detik').val();

    // Ambil jawaban dari drag-and-drop box
    var kodeLangkah = [];
    var boxes = document.querySelectorAll('.answer-box.box-java');
    boxes.forEach(function(box) {
        var item = box.querySelector('.drag-item');
        kodeLangkah.push(item ? item.innerText.replace(/\s+/g, ' ').trim() : '');
    });

    // Reset highlight box sebelumnya
    boxes.forEach(function(box) {
        box.style.borderColor = '';
        box.classList.remove('shake');
    });

    $.ajax({
        url: APP_URL + 'ujian-kode/submit-konversi',
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            id_soal_konversi: $('#id-soal-konversi').val(),
            kode_langkah: kodeLangkah,
            waktu: waktu
        },
        success: function(response) {
            modalKonfirmasi.hide();

            // Tampilkan modal correct dan hasil run Java
            var modalCorrect = new bootstrap.Modal(document.getElementById('modal-feedback-correct-konversi'));
            document.getElementById('java-run-result').textContent = response.java_output || '';
            modalCorrect.show();

            document.querySelector('#modal-feedback-correct-konversi .btn-primary').onclick = function() {
                let url = `${APP_URL}quiz/question-list?level=${document.getElementById('id-level').value}`;
                if (response.konversi) {
                    url += `&konversi_id=${encodeURIComponent(response.konversi.id)}`;
                }
                window.location.href = url;
            };
        },
        error: function(xhr) {
            const res = xhr.responseJSON;

            if (res?.message?.errors) {
                var allBoxes = document.querySelectorAll('.answer-box.box-java');
                res.message.errors.forEach(function(err) {
                    if (allBoxes[err.index]) {
                        allBoxes[err.index].style.borderColor = 'red';
                        allBoxes[err.index].classList.add('shake');
                        setTimeout(function() {
                            allBoxes[err.index].classList.remove('shake');
                        }, 400);
                    }
                });
            }

            $.ajax({
                url: APP_URL + "nyawa/status",
                type: "GET",
                dataType: "json",
                headers: {
                    "Accept": "application/json",
                    "X-Requested-With": "XMLHttpRequest"
                },
                success: function(data) {
                    const livesEl = document.getElementById("lives-count");
                    if (livesEl) livesEl.innerText = (data && typeof data.lives !== 'undefined') ? data.lives : 0;

                    openModalFeedbackIncorrect(res?.message?.message ?? 'Terdapat jawaban salah', data.lives);
                },
                error: function(xhr) {
                    // console.error("Gagal mendapatkan status nyawa", xhr);
                }
            });
        }
    });
}

function openModalFeedbackIncorrect(feedbackText, lives = null) {
    // Tampilkan modal incorrect konversi
    var modalIncorrect = new bootstrap.Modal(document.getElementById('modal-feedback-incorrect-konversi'));
    var modalKonfirmasi = bootstrap.Modal.getInstance(document.getElementById('modal-konfirmasi-jawaban-konversi'));
    var id_level = document.getElementById('id-level').value;

    // Ganti pesan dan tombol jika nyawa habis
    if (parseInt(lives) <= 0) {
        document.getElementById('feedback-ujian-konversi').innerHTML =
            '<span style="color:red;font-weight:bold;">Nyawa anda sudah habis, harap menunggu nyawa bertambah.</span>';

        // Ganti tombol modal
        var modalFooter = document.querySelector('#modal-feedback-incorrect-konversi .modal-footer');
        if (modalFooter) {
            modalFooter.innerHTML = `<button type="button" class="btn btn-primary" onclick="window.location.href='${APP_URL}quiz/question-list?level=${id_level}'">Kembali ke Daftar Soal</button>`;
        }

        // Sembunyikan tombol silang (X) pada header modal
        var closeBtn = document.querySelector('#modal-feedback-incorrect-konversi .btn-close');
        if (closeBtn) closeBtn.style.display = 'none';
    } else {
        // Tampilkan kembali tombol silang jika masih ada nyawa
        var closeBtn = document.querySelector('#modal-feedback-incorrect-konversi .btn-close');
        if (closeBtn) closeBtn.style.display = '';
    }

    if (modalKonfirmasi) {
        modalKonfirmasi.hide();
    }
    modalIncorrect.show();
}
