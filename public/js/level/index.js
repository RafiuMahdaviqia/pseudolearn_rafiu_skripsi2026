var target = document.querySelector("#kt_app_body");
var blockUI = new KTBlockUI(target);
var APP_URL = window.APP_URL || "/";

$(() => {
    blockUI.block();
    initTable();
    blockUI.release();
});

initTable = () => {
    return new Promise((resolve, reject) => {
        var table = $("#level-table").DataTable({
            ajax: {
                url: APP_URL + "level/table",
                type: "POST",
                data: function (d) {
                    d._token = $('meta[name="csrf-token"]').attr("content");
                },
            },
            processing: true,
            serverSide: true,
            destroy: true,
            responsive: false,
            order: [[0, "asc"]],
            columns: [
                {
                    data: "order",
                    className: "text-center",
                    orderable: true,
                    searchable: false,
                },
                { 
                    data: "image", 
                    orderable: false, 
                    searchable: false },
                { 
                    data: "name", 
                    orderable: true, 
                    searchable: true 
                },
                {
                    data: "limit_soal",
                    className: "text-center",
                    orderable: false,
                    searchable: false,
                },
                {
                    data: "feedback_data_type",
                    orderable: false,
                    searchable: false,
                },
                {
                    data: "feedback_algorithm",
                    orderable: false,
                    searchable: false,
                },
                {
                    data: "manual_active",
                    className: "text-center",
                    orderable: false,
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
                    render: function (data, type, row) {
                        return row.order ?? '';
                    },
                },
                {
                    targets: 1,
                    render: function (data, type, row) {
                        return row.image
                            ? `<img src="${APP_URL + row.image}" class="img-fluid" style="width: 50px; height: 50px;">`
                            : "";
                    },
                },
                {
                    targets: 2,
                    render: function (data, type, row) {
                        return row.name;
                    },
                },
                {
                    targets: 3,
                    render: function (data, type, row) {
                        return row.limit_soal ?? '-';
                    },
                },
                {
                    targets: 4,
                    render: function (data, type, row) {
                        return row.feedback_data_type || "";
                    },
                },
                {
                    targets: 5,
                    render: function (data, type, row) {
                        return row.feedback_algorithm || "";
                    },
                },
                {
                    targets: 6,
                    className: "text-center",
                    render: function (data, type, row) {
                        const isActive = Number(row.manual_active) === 1;
                        return `
                            <div class="d-flex flex-column align-items-center gap-2">
                                <span class="badge ${isActive ? 'badge-light-success' : 'badge-light-danger'} status-badge">
                                    ${isActive ? 'Aktif' : 'Tidak Aktif'}
                                </span>
                                <div class="form-check form-switch form-switch-sm">
                                    <input class="form-check-input manual-active-toggle"
                                           type="checkbox"
                                           data-id="${row.id}"
                                           ${isActive ? 'checked' : ''}>
                                </div>
                            </div>
                        `;
                    },
                },
                {
                    targets: 7,
                    render: function (data, type, row, meta) {
                        return `
                            <div class="d-flex gap-3 justify-content-center">
                                <a href="${APP_URL}level/form/${row.id}" class="btn btn-icon btn-sm btn-outline btn-outline-warning">
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
            initComplete: function (settings, json) {
                var debounceTimer;
                $("#search-level").on("keyup", function () {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(function () {
                        table.search($("#search-level").val()).draw();
                    }, 300);
                });
                if (table.state && table.state.loaded()) {
                    $("#search-level").val(table.state.loaded().search.search);
                }
                attachManualActiveHandler();
                resolve(true);
            },
        });
    });
};

destroy = (id) => {
    Swal.fire({
        title: "Hapus Level",
        text: "Apakah Anda yakin ingin menghapus level ini? Semua soal dan konversi yang terkait akan terhapus.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, Hapus!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            blockUI.block();
            $.ajax({
                url: APP_URL + `level/${id}`,
                type: "DELETE",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (response) {
                    blockUI.release();
                    Swal.fire("Berhasil!", response.message, "success");
                    initTable();
                },
                error: function (xhr) {
                    blockUI.release();
                    Swal.fire("Gagal!", xhr.responseJSON.message, "error");
                },
            });
        }
    });
}

function attachManualActiveHandler() {
    $('#level-table').off('change', '.manual-active-toggle').on('change', '.manual-active-toggle', function() {
        const $el = $(this);
        const id = $el.data('id');
        const newVal = $el.is(':checked') ? 1 : 0;
        $el.prop('disabled', true);
        $.ajax({
            url: APP_URL + 'level/update-active',
            type: 'POST',
            data: {
                id: id,
                manual_active: newVal,
                _token: $('meta[name="csrf-token"]').attr("content")
            },
            success: function(resp) {
                const badge = $el.closest('td').find('.status-badge');
                if (newVal === 1) {
                    badge.removeClass('badge-light-danger').addClass('badge-light-success').text('Aktif');
                } else {
                    badge.removeClass('badge-light-success').addClass('badge-light-danger').text('Tidak Aktif');
                }
            },
            error: function(xhr) {
                Swal.fire('Gagal', xhr.responseJSON?.message || 'Gagal memperbarui status', 'error');
                // revert
                $el.prop('checked', !newVal);
            },
            complete: function() {
                $el.prop('disabled', false);
            }
        });
    });
}

// Re-attach after every draw (pagination/search)
$(document).on('draw.dt', '#level-table', function() {
    attachManualActiveHandler();
});