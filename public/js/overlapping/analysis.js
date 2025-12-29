var target = document.querySelector("#kt_app_body");
var blockUI = new KTBlockUI(target);
var APP_URL = window.APP_URL || "/";

$(() => {
    getData();
});

$('#filter-kelas').on('change', function () {
    getData();
});

function getData() {
    let idSoal = $('#soal-id').val();
    let filterKelas = $('#filter-kelas').val();

    $.ajax({
        url: APP_URL + "overlapping/analysis/data",
        method: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
            id_soal: idSoal,
            filter_kelas: filterKelas
        },
        success: function (response) {
            let kunciTipe = response.kunciTipe || [];
            let kunciAlgo = response.kunciAlgo || [];
            let html = `<div class="container mt-8">`;

            // Jika data kosong
            const isEmptyTipe = !response.data.tipe_data || response.data.tipe_data.length === 0;
            const isEmptyAlgo = !response.data.algoritma || response.data.algoritma.length === 0;

            if (isEmptyTipe && isEmptyAlgo) {
                html += `
                    <div class="text-center py-10">
                        <h4>Belum ada data</h4>
                        <p class="text-muted">Data analisis belum tersedia.</p>
                    </div>
                `;
            } else {
                html += `<h4 class="text-center mb-6">Tipe Data</h4>`;
                // Loop tiap index tipe_data
                response.data.tipe_data.forEach((group, idx) => {
                    html += `
                    <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border border-2">
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-5">
                    `;
                    group.labels.forEach(label => {
                        let kunci = kunciTipe[idx] ? kunciTipe[idx].tipe_data : null;
                        let badgeClass = (label.tipe_data === kunci) ? "badge-success" : "badge-danger";
                        html += `
                        <div class="text-center">
                            <div class="rounded-circle bg-white d-flex align-items-center justify-content-center mx-auto mb-4 border border-1 border-secondary cursor-pointer"
                                style="width:50px; height:50px;"
                                onclick="detailData('${idSoal}', ${idx}, 'tipe_data', '${label.tipe_data}')">
                                <span class="fw-bold fs-2">${label.total}</span>
                            </div>
                            <span class="badge ${badgeClass} px-4 py-3 fs-7 fw-semibold cursor-pointer"
                                onclick="detailData('${idSoal}', ${idx}, 'tipe_data', '${label.tipe_data}')">
                                ${label.tipe_data}
                            </span>
                        </div>
                        `;
                    });
                    html += `
                            </div>
                        </div>
                        </div>
                    </div>
                    </div>
                    `;
                });

                html += `<h4 class="text-center mb-6 mt-10">Algoritma</h4>`;
                // Loop tiap index algoritma
                response.data.algoritma.forEach((group, idx) => {
                    html += `
                    <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border border-2">
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-5">
                    `;
                    group.labels.forEach(label => {
                        let kunci = kunciAlgo[idx] ? kunciAlgo[idx].langkah : null;
                        let badgeClass = (label.algoritma === kunci) ? "badge-success" : "badge-danger";
                        html += `
                        <div class="text-center">
                            <div class="rounded-circle bg-white d-flex align-items-center justify-content-center mx-auto mb-4 border border-1 border-secondary cursor-pointer"
                                style="width:50px; height:50px;"
                                onclick="detailData('${idSoal}', ${idx}, 'algoritma', '${label.algoritma}')">
                                <span class="fw-bold fs-2">${label.total}</span>
                            </div>
                            <span class="badge ${badgeClass} px-4 py-3 fs-7 fw-semibold cursor-pointer"
                                onclick="detailData('${idSoal}', ${idx}, 'algoritma', '${label.algoritma}')">
                                ${label.algoritma}
                            </span>
                        </div>
                        `;
                    });
                    html += `
                            </div>
                        </div>
                        </div>
                    </div>
                    </div>
                    `;
                });
            }

            html += `</div>`; // container
            $("#analysis-result").html(html);
        },
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
}

function detailData(idSoal, index, type, value) {
    // Redirect ke halaman detail dengan parameter di URL
    const params = new URLSearchParams({
        id_soal: idSoal,
        index: index,
        type: type,
        value: value
    });
    window.location.href = APP_URL + "overlapping/analysis/detail?" + params.toString();
}
