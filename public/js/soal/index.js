var target = document.querySelector("#kt_app_body");
var blockUI = new KTBlockUI(target);
var APP_URL = window.APP_URL || "/";

$(() => {
    blockUI.block();
    initTable();
    blockUI.release();
});

initTable = () => {
    let level = $('#filter-level').val();
    return new Promise((resolve, reject) => {
        var table = $("#table-soal").DataTable({
            ajax: {
                url: APP_URL + "soal/table",
                type: "POST",
                data: function (d) {
                    d._token = $('meta[name="csrf-token"]').attr("content"),
                    d.level = level || null;
                },
            },
            processing: true,
            serverSide: true,
            destroy: true,
            responsive: false,
            order: [[0, "desc"]],
            columns: [
                {
                    data: null,
                    className: "text-center",
                    orderable: true,
                    searchable: false,
                },
                { 
                    data: "level_name", 
                    orderable: true, 
                    searchable: false
                },
                { 
                    data: "judul", 
                    orderable: true, 
                    searchable: true,
                    width: "20%",
                },
                { 
                    data: "soal", 
                    orderable: true, 
                    searchable: true,
                    width: "35%",
                },
                {
                    data: "status",
                    orderable: true,
                    searchable: false,
                },
                {
                    data: null,
                    className: "text-center",
                    orderable: false,
                    searchable: false,
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
                        return row.level_name ?? '';
                    },
                },
                {
                    targets: 2,
                    render: function (data, type, row) {
                        return row.judul ?? '';
                    },
                },
                {
                    targets: 3,
                    render: function (data, type, row) {
                        return row.soal ?? '';
                    },
                },
                {
                    targets: 4,
                    render: function(data, type, row) {
                        const isActive = Number(row.status) === 1;
                        const badgeClass = isActive ? 'badge-light-success' : 'badge-light-danger';
                        const badgeText  = isActive ? 'Aktif' : 'Nonaktif';
                        return `
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <span class="badge ${badgeClass} mb-3">${badgeText}</span>
                                <div class="form-check form-switch form-switch-sm ms-3">
                                    <input class="form-check-input status-toggle" type="checkbox" data-id="${row.id}" ${isActive ? 'checked' : ''}>
                                </div>
                            </div>
                        `;
                    }
                },
                {
                    targets: 5,
                    render: function (data, type, row, meta) {
                        return `
                            <div class="d-flex gap-3 justify-content-center">
                                <a href="${APP_URL}soal/form/${row.id}" class="btn btn-icon btn-sm btn-outline btn-outline-warning">
                                    <i class="ki-outline ki-pencil"></i>
                                </a>
                                <button class="btn btn-icon btn-sm btn-outline btn-outline-danger" onclick="destroy('${row.id}')">
                                    <i class="ki-outline ki-trash"></i>
                                </button>
                            </div>
                        `;
                    },
                },
            ],
            createdRow: function (row, data, dataIndex) {
                $(row).attr("id", data.id || data[0]);
            },
            drawCallback: function() {
                // Re-attach toggle handler after each draw
                attachStatusToggleHandler();
            },
            initComplete: function (settings, json) {
                var debounceTimer;
                $("#search-soal").on("keyup", function () {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(function () {
                        table.search($("#search-soal").val()).draw();
                    }, 300);
                });
                if (table.state && table.state.loaded()) {
                    $("#search-soal").val(table.state.loaded().search.search);
                }
                resolve(true);
            },
        });
    });
};

function attachStatusToggleHandler() {
    $('#table-soal').off('change', '.status-toggle').on('change', '.status-toggle', function() {
        const $el = $(this);
        const id = $el.data('id');
        const newStatus = $el.is(':checked') ? 1 : 0;

        $el.prop('disabled', true);

        $.ajax({
            url: APP_URL + 'soal/updateStatusSoal',
            type: 'POST',
            data: {
                id: id,
                status: newStatus,
                _token: $('meta[name="csrf-token"]').attr("content")
            },
            success: function(resp) {
                if (!resp.success) {
                    Swal.fire('Gagal', resp.message || 'Gagal update status', 'error');
                    // revert
                    $el.prop('checked', !newStatus);
                } else {
                    // refresh only status cell (optional: redraw row)
                    const badge = $el.closest('td').find('.badge');
                    if (newStatus === 1) {
                        badge.removeClass('badge-light-danger').addClass('badge-light-success').text('Aktif');
                    } else {
                        badge.removeClass('badge-light-success').addClass('badge-light-danger').text('Nonaktif');
                    }
                }
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                $el.prop('checked', !newStatus);
            },
            complete: function() {
                $el.prop('disabled', false);
            }
        });
    });
}

function destroy(id) {
    Swal.fire({
        title: "Hapus Soal?",
        html: 'Apakah Anda yakin ingin menghapus soal ini?<br><strong>Catatan:</strong> Soal konversi terkait akan terhapus juga.',
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, Hapus",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            blockUI.block();
            $.ajax({
                url: APP_URL + `soal/${id}`,
                type: "DELETE",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (response) {
                    blockUI.release();
                    if (response.success) {
                        Swal.fire("Berhasil!", response.message, "success").then(() => {
                            initTable();
                        });
                    } else {
                        Swal.fire("Gagal!", response.message, "error");
                    }
                },
                error: function (xhr) {
                    blockUI.release();
                    Swal.fire("Gagal!", xhr.responseJSON?.message || "Terjadi kesalahan.", "error");
                },
            });
        }
    });
}

$('#filter-level').on('change', function() {
   initTable();
});