var target = document.querySelector("#kt_app_body");
var blockUI = new KTBlockUI(target);
var APP_URL = window.APP_URL || "/";

$(() => {
    blockUI.block();
    initTable();
    resetSoalSelect();
    blockUI.release();
});

var dataTable = null; // simpan instance global

initTable = () => {
    return new Promise((resolve, reject) => {
        // Kalau sudah ada, reload saja
        if (dataTable) {
            dataTable.ajax.reload();
            resolve(true);
            return;
        }

        dataTable = $("#table-log-data-chatbot").DataTable({
            ajax: {
                url: APP_URL + "log-data-chatbot/table",
                type: "POST",
                data: function (d) {
                    d._token = $('meta[name="csrf-token"]').attr("content");
                    d.kelas  = $('#filter-kelas').val() || null;
                    d.level  = $('#filter-level').val() || null;
                    d.soal   = $('#filter-soal').val()  || null;
                },
            },
            processing: true,
            serverSide: true,
            destroy: false, // JANGAN destroy
            responsive: false,
            order: [[1, "asc"]],
            columns: [
                { data: null, className: "text-center", orderable: false, searchable: false },
                { data: "nim", orderable: true, searchable: true },
                { data: "name", orderable: true, searchable: true },
                { data: "kelas_name", orderable: true, searchable: true, className: "text-center" },
                { data: "jumlah_chatbot", orderable: true, searchable: false, className: "text-center" },
                { data: "jumlah_chatbot_adaptive", orderable: true, searchable: false, className: "text-center" },
                { data: "id", orderable: false, searchable: false, className: "text-center" },
            ],
            columnDefs: [
                { targets: 0, render: (data, type, row, meta) => meta.row + 1 },
                { targets: 1, render: (data, type, row) => row.nim ?? '-' },
                { targets: 2, render: (data, type, row) => row.name ?? '-' },
                { targets: 3, render: (data, type, row) => row.kelas_name ?? '-' },
                {
                    targets: 4,
                    render: (data, type, row) =>
                        `<span class="badge badge-light-primary fs-7">${row.jumlah_chatbot ?? 0}</span>`
                },
                {
                    targets: 5,
                    render: (data, type, row) =>
                        `<span class="badge badge-light-info fs-7">${row.jumlah_chatbot_adaptive ?? 0}</span>`
                },
                {
                    targets: 6,
                    render: (data, type, row) => `
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-sm btn-outline btn-outline-primary d-flex align-items-center gap-1 p-2" onclick="showDetail('${row.id}')">
                                <i class="ki-outline ki-eye"></i>
                                <span>Detail</span>
                            </button>
                        </div>
                    `
                },
            ],
            createdRow: (row, data) => $(row).attr("id", data.id || data[0]),
            initComplete: function (settings, json) {
                var debounceTimer;
                $("#search-mahasiswa").on("keyup", function () {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        dataTable.search($("#search-mahasiswa").val()).draw();
                    }, 300);
                });
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
        url: APP_URL + "log-data-chatbot/getSoalByLevel",
        type: "GET",
        data: {
            level_id: levelId,
            _token: $('meta[name="csrf-token"]').attr("content")
        },
        success: function(res){
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

// Change Kelas
$('#filter-kelas').on('change', function() {
   initTable();
});

// Change Soal
$('#filter-soal').on('change', function() {
    initTable();
});

// Show detail modal
function showDetail(idMahasiswa) {
    blockUI.block();

    $.ajax({
        url: APP_URL + "log-data-chatbot/detail/" + idMahasiswa,
        type: "GET",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content")
        },
        success: function(res) {
            if (res.success) {
                let data = res.data;
                
                $('#detail-nim').text(data.nim ?? '-');
                $('#detail-nama').text(data.name ?? '-');
                $('#detail-kelas').text(data.kelas_name ?? '-');
                $('#detail-total-chatbot').text((data.jumlah_chatbot ?? 0) + (data.jumlah_chatbot_adaptive ?? 0));

                let tbody = '';
                if (data.history && data.history.length > 0) {
                    data.history.forEach((item, index) => {
                        let typeBadge = item.type === 'adaptive'
                            ? '<span class="badge badge-light-info">Adaptive</span>'
                            : '<span class="badge badge-light-primary">Biasa</span>';

                        tbody += `
                            <tr>
                                <td class="text-center">${index + 1}</td>
                                <td class="text-center">${typeBadge}</td>
                                <td class="text-center">${item.level || 'Tidak tercatat'}</td>
                                <td class="text-center">${item.waktu_akses ?? '-'}</td>
                                <td class="text-center">${item.jenis_soal || 'Tidak tercatat'}</td>
                                <td class="text-center">${item.durasi ?? '-'}</td>
                            </tr>
                        `;
                    });
                } else {
                    tbody = `
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada riwayat akses chatbot</td>
                        </tr>
                    `;
                }
                
                $('#detail-chatbot-body').html(tbody);

                // Tampilkan modal
                $('#modal-detail-chatbot').modal('show');

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: res.message ?? 'Terjadi kesalahan saat memuat data'
                });
            }
            blockUI.release();
        },
        error: function(xhr) {
            blockUI.release();
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi kesalahan saat memuat data'
            });
        }
    });
}

// FIX: Release scroll lock setelah modal ditutup
$('#modal-detail-chatbot').on('hidden.bs.modal', function () {
    $('body').removeClass('modal-open');
    $('body').css('overflow', '');
    $('body').css('padding-right', '');
    $('.modal-backdrop').remove();
});

// Export Excel
function exportExcel() {
    let kelas = $('#filter-kelas').val();
    let level = $('#filter-level').val();
    let soal  = $('#filter-soal').val();

    // Create a form and submit it
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = APP_URL + 'log-data-chatbot/export';
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
