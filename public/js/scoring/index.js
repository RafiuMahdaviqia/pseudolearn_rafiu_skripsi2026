var target = document.querySelector("#kt_app_body");
var blockUI = new KTBlockUI(target);
var APP_URL = window.APP_URL || "/";

$(() => {
    blockUI.block();
    initTable();
    resetSoalSelect();
    blockUI.release();
});

initTable = () => {
    let kelas = $('#filter-kelas').val();
    let level = $('#filter-level').val();
    let soal  = $('#filter-soal').val();
    return new Promise((resolve, reject) => {
        var table = $("#table-scoring").DataTable({
            ajax: {
                url: APP_URL + "scoring/table",
                type: "POST",
                data: function (d) {
                    d._token = $('meta[name="csrf-token"]').attr("content"),
                    d.kelas = kelas || null;
                    d.level = level || null;
                    d.soal  = soal  || null;
                },
            },
            processing: false,
            serverSide: true,
            destroy: true,
            responsive: true,
            order: [[0, "desc"]],
            columns: [
                {
                    data: null,
                    className: "text-center",
                    orderable: true,
                    searchable: false,
                    width: "5%",
                },
                { 
                    data: "nim", 
                    orderable: true, 
                    searchable: true,
                    width: "10%",
                },
                { 
                    data: "name", 
                    orderable: true, 
                    searchable: true,
                    width: "20%"
                },
                { 
                    data: "kelas_name", 
                    orderable: true, 
                    searchable: true,
                    className: "text-center",
                    width: "10%"
                },
                { 
                    data: "pre_test", 
                    orderable: true, 
                    searchable: true,
                    className: "text-center",
                    width: "8%"
                },
                { 
                    data: "post_test", 
                    orderable: true, 
                    searchable: true,
                    className: "text-center",
                    width: "8%"
                },
                { 
                    data: "totalDrag", 
                    orderable: true, 
                    searchable: true,
                    className: "text-center"
                },
                // { 
                //     data: "totalSubmit", 
                //     orderable: true, 
                //     searchable: true,
                //     className: "text-center"
                // },
                { 
                    data: "totalWaktu", 
                    orderable: true, 
                    searchable: true,
                    className: "text-center"
                },
                {
                    data: "id",
                    orderable: false,
                    searchable: false,
                    className: "text-center"
                },
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    },
                },
                {
                    targets: 1,
                    render: function (data, type, row) {
                        return row.nim ?? '';
                    },
                },
                {
                    targets: 2,
                    render: function (data, type, row) {
                        return row.name ?? '';
                    },
                },
                {
                    targets: 3,
                    render: function (data, type, row) {
                        return row.kelas_name ?? '';
                    },
                },
                {
                    targets: 4,
                    render: function (data, type, row) {
                        const value = row.pre_test ?? '';
                        return `<input type="number" class="form-control form-control-sm pre-test-input" value="${value}" data-id="${row.id}" min="0" />`;
                    },
                },
                {
                    targets: 5,
                    render: function (data, type, row) {
                        const value = row.post_test ?? '';
                        return `<input type="number" class="form-control form-control-sm post-test-input" value="${value}" data-id="${row.id}" min="0" />`;
                    },
                },
                // {
                //     targets: 6,
                //     render: function (data, type, row) {
                //         return row.totalSubmit ?? '';
                //     },
                // },
                {
                    targets: 6,
                    render: function (data, type, row) {
                        return row.totalDrag ?? '';
                    },
                },
                {
                    targets: 7,
                    render: function (data, type, row) {
                        const waktu = row.totalWaktu ?? '';
                        const detik = row.totalWaktuDetik ?? '';

                        return `
                            <span>${waktu}</span>
                            <br>
                            <span class="text-muted fs-8">${detik ? detik + ' detik' : ''}</span>
                        `;
                    }
                },
                {
                    targets: 8,
                    render: function (data, type, row, meta) {
                        return row.skor ?? '-';
                    },
                },
            ],
            createdRow: function (row, data, dataIndex) {
                $(row).attr("id", data.id || data[0]);
            },
            initComplete: function (settings, json) {
                var debounceTimer;
                $("#search-mahasiswa").on("keyup", function () {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(function () {
                        table.search($("#search-mahasiswa").val()).draw();
                    }, 300);
                });
                if (table.state && table.state.loaded()) {
                    $("#search-mahasiswa").val(table.state.loaded().search.search);
                }
                resolve(true);
            },
        });
    });
};

function resetSoalSelect() {
    $('#filter-soal').html('<option value="">Filter Soal</option>').val('').trigger('change');
}

// Change Level -> load soal
$('#filter-level').on('change', function() {
    const levelId = $(this).val();
    if (!levelId) {
        resetSoalSelect();
        initTable();
        return;
    }

    let opts = '<option value="">Pilih Soal</option>';
    $.ajax({
        url: APP_URL + "log-activity/getSoalByLevel",
        type: "GET",
        data: {
            level_id: levelId,
            _token: $('meta[name="csrf-token"]').attr("content")
        },
        success: function(res){
            // Append from AJAX response
            (res || []).forEach(s => {
                opts += `<option value="${s.id}">${s.judul}</option>`;
            });

            $('#filter-soal').html(opts).val('').trigger('change');
            initTable();
        },
        error: function(){
            resetSoalSelect();
        }
    });
});

$('#filter-kelas').on('change', function() {
   initTable();
});

// Change Soal
$('#filter-soal').on('change', function() {
    initTable();
});

// Debounce map per row+field
const debounceTimers = {};
// Cache nilai terakhir yang tersimpan, key = `${id}|${field}`
const lastSaved = {};

// Util simpan nilai ke backend (kembalikan jqXHR agar bisa .done/.fail)
function saveScore({ id, field, value, el }) {
    // Skip jika value null (tidak perlu simpan)
    if (value === null) {
        return $.Deferred().resolve().promise();
    }

    let idLevel = $('#filter-level').val();
    if (!idLevel) {
        Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Level harus dipilih.' });
        return $.Deferred().reject().promise();
    }

    $(el).removeClass('is-valid is-invalid').addClass('is-loading');

    return $.ajax({
        url: APP_URL + "scoring/update-test",
        type: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
            id: id,
            field: field,          // 'pre_test' | 'post_test'
            value: value,
            level: idLevel
        }
    }).done(function () {
        $(el).removeClass('is-loading is-invalid').addClass('is-valid');
    }).fail(function (xhr) {
        $(el).removeClass('is-loading is-valid').addClass('is-invalid');
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: xhr.responseJSON?.message || 'Gagal menyimpan nilai. Silakan coba lagi.',
        });
    });
}

// Hanya simpan setelah berhenti mengetik (debounce 700ms)
$(document).off('input.labeling').on('input.labeling', '.pre-test-input, .post-test-input', function () {
    const el = this;
    const id = $(el).data('id');
    const field = $(el).hasClass('pre-test-input') ? 'pre_test' : 'post_test';
    const raw = $(el).val();
    const value = raw === '' ? null : parseInt(raw, 10);
    const key = `${id}|${field}`;

    // Jika kosong → batalkan debounce & jangan simpan
    if (value === null) {
        clearTimeout(debounceTimers[key]);
        return;
    }

    // Jika nilai sama dengan terakhir tersimpan, tidak perlu simpan lagi
    if (lastSaved[key] === value) return;

    clearTimeout(debounceTimers[key]);
    debounceTimers[key] = setTimeout(() => {
        saveScore({ id, field, value, el }).done(() => {
            lastSaved[key] = value;
        });
    }, 700);
});

// Saat blur, jalankan pending debounce segera (flush)
$(document).off('blur.labeling').on('blur.labeling', '.pre-test-input, .post-test-input', function () {
    const el = this;
    const id = $(el).data('id');
    const field = $(el).hasClass('pre-test-input') ? 'pre_test' : 'post_test';
    const raw = $(el).val();
    const value = raw === '' ? null : parseInt(raw, 10);
    const key = `${id}|${field}`;

    // Jika kosong → batalkan debounce & jangan simpan
    if (value === null) {
        if (debounceTimers[key]) {
            clearTimeout(debounceTimers[key]);
            delete debounceTimers[key];
        }
        return;
    }

    // Jika ada timer pending -> batalkan dan simpan sekarang
    if (debounceTimers[key]) {
        clearTimeout(debounceTimers[key]);
        delete debounceTimers[key];
    }

    if (lastSaved[key] !== value) {
        saveScore({ id, field, value, el }).done(() => {
            lastSaved[key] = value;
        });
    }
});

function exportExcel() {
    let kelas = $('#filter-kelas').val();
    let level = $('#filter-level').val();
    let soal  = $('#filter-soal').val();

    // Create a form and submit it
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = APP_URL + 'scoring/export';
    form.style.display = 'none';

    // CSRF Token
    var csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = $('meta[name="csrf-token"]').attr('content');
    form.appendChild(csrfInput);

    // Kelas
    if (kelas) {
        var kelasInput = document.createElement('input');
        kelasInput.type = 'hidden';
        kelasInput.name = 'kelas';
        kelasInput.value = kelas;
        form.appendChild(kelasInput);
    }

    // Level
    if (level) {
        var levelInput = document.createElement('input');
        levelInput.type = 'hidden';
        levelInput.name = 'level';
        levelInput.value = level;
        form.appendChild(levelInput);
    }

    // Soal
    if (soal) {
        var soalInput = document.createElement('input');
        soalInput.type = 'hidden';
        soalInput.name = 'soal';
        soalInput.value = soal;
        form.appendChild(soalInput);
    }

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

function calculateManual() {
    let kelas = $('#filter-kelas').val();
    let level = $('#filter-level').val();
    let soal  = $('#filter-soal').val();

    if (!kelas || !level) {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Filter Kelas dan Level harus dipilih.',
        });
        return;
    }

    // Jika soal diisi, kalkulasi skor
    if (soal) {
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin melakukan kalkulasi Skor?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: APP_URL + "scoring/calculate-manual",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        kelas: kelas,
                        level: level,
                        soal: soal
                    },
                    success: function(res){
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: res.message || 'Kalkulasi ulang selesai.',
                        });
                        initTable();
                    },
                    error: function(xhr){
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Gagal melakukan kalkulasi ulang. Silakan coba lagi.',
                        });
                    }
                });
            }
        });
    } else {
        // Jika hanya kelas & level, kalkulasi rata-rata
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin melakukan kalkulasi Rata-rata Skor?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: APP_URL + "scoring/calculate-average",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        kelas: kelas,
                        level: level
                    },
                    success: function(res){
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: res.message || 'Kalkulasi rata-rata selesai.',
                        });
                        initTable();
                    },
                    error: function(xhr){
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Gagal melakukan kalkulasi rata-rata. Silakan coba lagi.',
                        });
                    }
                });
            }
        });
    }
}