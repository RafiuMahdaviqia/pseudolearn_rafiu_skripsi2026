var target = document.querySelector("#kt_app_body");
var blockUI = new KTBlockUI(target);
var APP_URL = window.APP_URL || "/";


function renderPencapaianItem(item) {
    const status = Number(item.status ?? 0); // 0: belum hak, 1: not claimed, 2: claimed
    const canClaim = status === 1;
    const claimed = status === 2;
    const notEligible = status === 0;

    const btnText = notEligible ? 'Claim' : (claimed ? 'Claimed' : 'Claim');
    const btnClass = notEligible ? 'btn-secondary' : (claimed ? 'btn-outline btn-outline-primary' : 'btn-success');
    const disabledAttr = canClaim ? '' : 'disabled';

    const max = Number(item.max_progress ?? 0);
    const prog = Number(item.progress ?? 0);
    const percent = Math.max(0, Math.min(100, max > 0 ? (prog / max) * 100 : 0));

    const imgSrc = item.img || '/assets/media/img/badge_sempurna.png';
    const heart = item.heart ?? '';

    return `
    <div class="d-flex align-items-center p-3 rounded-3 bg-white border achievement-item">
        <img src="${imgSrc}" alt="${item.name ?? ''}" class="achievement-img bg-light rounded-3" style="width: 70px; height: 70px;">
        <div class="flex-grow-1 ms-3 me-5">
            <div class="fw-bold text-primary-emphasis achievement-title fs-6">${item.name ?? ''}</div>
            <div class="small mb-2 text-dark achievement-desc">${item.desc ?? ''}</div>
            <div class="d-flex align-items-center">
                <div class="flex-grow-1 progress rounded-pill achievement-progress" role="progressbar" aria-valuenow="${Math.round(percent)}" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar ${claimed ? 'bg-primary' : 'bg-warning'}" style="width: ${percent}%;"></div>
                </div>
                <span class="ms-2 text-muted achievement-progress-text">${prog}/${max}</span>
            </div>
        </div>
        <button class="btn ${btnClass} ms-3 btn-sm d-flex align-items-center rounded-3 fw-semibold achievement-claim-btn flex-shrink-0 shadow-sm"
                style="min-width: 120px;"
                ${disabledAttr}
                data-id="${item.id ?? ''}"
                data-status="${status}"
                ${notEligible ? 'title="Belum berhak untuk klaim"' : ''}>
            ${btnText} ${heart !== '' ? `<img src="/assets/media/img/heart.png" alt="Heart" class="mx-1" style="width: 18px;"> ${heart}x` : ''}

        </button>
    </div>
    `;
}

function loadPencapaian(category, target) {
     $.ajax({
        url: APP_URL + "pencapaian/data",
        type: "GET",
        data: { category: category },
        success: function (response) {
            let html = '';
            if (response.length === 0) {
                html = '<div class="text-center text-muted py-5">Belum ada pencapaian.</div>';
            } else {
                response.forEach(item => {
                    html += renderPencapaianItem(item);
                });
            }
            $(target).html(html);
        },
        error: function (xhr, status, error) {
            blockUI.release();
            Swal.fire({
                text:
                    xhr.responseJSON?.message ||
                    "Terjadi kesalahan saat memuat data. Silakan coba lagi.",
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

$(document).ready(function() {
    // Load default (badge)
    loadPencapaian('badge', '#badge-content');

    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        const tabId = $(e.target).attr('href');
        if (tabId === '#badge') loadPencapaian('badge', '#badge-content');
        if (tabId === '#soal') loadPencapaian('soal', '#soal-content');
        if (tabId === '#konversi') loadPencapaian('konversi', '#konversi-content');
    });
});


function claimPencapaian(id, category, target) {
    // Ambil perkiraan jumlah nyawa dari tombol (jika ada)
    const $btn = $(`${target} .achievement-claim-btn[data-id="${id}"]`);
    let estimasiNyawa = null;
    if ($btn.length) {
        const txt = $btn.text();
        const m = txt.match(/(\d+)\s*x/i);
        if (m) estimasiNyawa = parseInt(m[1], 10);
    }

    // Ambil nyawa sekarang dari elemen (atau variabel global jika ada)
    let nyawaSekarang = parseInt($('#lives-count').val() || '0', 10);
    let maxNyawa = 100; // Default, bisa diganti jika ada variabel global/max dari server

    // Jika ada elemen max lives di halaman, ambil dari situ
    if ($('#max-lives-count').length) {
        maxNyawa = parseInt($('#max-lives-count').val() || '100', 10);
    }

    let pesan = '';
    if (estimasiNyawa && (nyawaSekarang + estimasiNyawa) > maxNyawa) {
        pesan = `Nyawa Anda akan bertambah sebanyak ${estimasiNyawa}, namun maksimal nyawa adalah ${maxNyawa}. Nyawa Anda akan tetap ${maxNyawa} setelah klaim berhasil.`;
    } else {
        pesan = `Nyawa Anda akan bertambah${estimasiNyawa ? ' sebanyak ' + estimasiNyawa : ''} setelah klaim berhasil.`;
    }

    Swal.fire({
        title: 'Klaim pencapaian?',
        text: pesan,
        icon: 'question',
        showCancelButton: true,
        buttonsStyling: false,
        confirmButtonText: 'Ya, klaim',
        cancelButtonText: 'Batal',
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-light'
        }
    }).then((result) => {
        if (!result.isConfirmed) return;

        blockUI.block();
        $.ajax({
            url: APP_URL + "pencapaian/claim",
            type: "POST",
            data: {
                id: id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                blockUI.release();

                // Coba ambil jumlah nyawa dari response jika tersedia
                const nyawaDariServer = response?.nyawa ?? response?.data?.nyawa;

                const tambahanNyawa = Number.isFinite(nyawaDariServer) ? nyawaDariServer : estimasiNyawa;
                updateLivesCount();

                Swal.fire({
                    text: (response.message || "Berhasil klaim pencapaian!") + ` Nyawa Anda bertambah${tambahanNyawa ? ' sebanyak ' + tambahanNyawa : ''}.`,
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: "OK",
                    customClass: {
                        confirmButton: "btn btn-primary",
                    },
                }).then(() => {
                    loadPencapaian(category, target);
                });
            },
            error: function (xhr) {
                blockUI.release();
                Swal.fire({
                    text: xhr.responseJSON?.message || "Gagal klaim pencapaian.",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "OK",
                    customClass: {
                        confirmButton: "btn btn-primary",
                    },
                });
            }
        });
    });
}
// Event delegation untuk tombol claim
$(document).on('click', '.achievement-claim-btn', function () {
    const id = $(this).data('id');
    const status = $(this).data('status');
    if (status !== 1) return; // hanya bisa claim jika status 1

    // Tentukan kategori dan target tab
    let category = 'badge', target = '#badge-content';
    if ($(this).closest('#soal-content').length) {
        category = 'soal'; target = '#soal-content';
    }
    if ($(this).closest('#konversi-content').length) {
        category = 'konversi'; target = '#konversi-content';
    }

    claimPencapaian(id, category, target);
});

function updateLivesCount(){
    $.ajax({
        url: APP_URL + "nyawa/status",
        type: "GET",
        success: function (response) {
            $('#lives-count-text').text(response.lives);
        },
        error: function (xhr, status, error) {
            // console.error("Gagal ambil data:", err);
        },
    });
}