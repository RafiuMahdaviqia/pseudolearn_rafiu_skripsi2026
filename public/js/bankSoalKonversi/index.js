var target = document.querySelector("#kt_app_body");
var blockUI = new KTBlockUI(target);
var APP_URL = window.APP_URL || "/";

$(() => {
    blockUI.block();
    initTable().then(() => {
        blockUI.release();
    });
});

const initTable = () => {
    let level = $('#filter-level').val();

    return new Promise((resolve, reject) => {
        var table = $('#table-bank_soal_konversi').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            ajax: {
                url: APP_URL + 'bank-soal-konversi/table',
                type: 'POST',
                data: function(d) {
                    d._token = $('meta[name="csrf-token"]').attr('content') || $('input[name=_token]').val();
                    d.level = level || '';
                }
            },
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                },
                { data: 'level_name', name: 'level_name' },
                { data: 'soal', name: 'soal' },
                { data: 'jawaban', name: 'jawaban' },
                { data: 'output', name: 'output' },
                {
                    data: 'id',
                    name: 'id',
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function(data, type, row) {
                        return `
                            <div class="d-flex gap-3 justify-content-center">
                                <a href="${APP_URL}bank-soal-konversi/form/${row.id}" class="btn btn-icon btn-sm btn-outline btn-outline-warning">
                                    <i class="ki-outline ki-pencil"></i>
                                </a>
                                <button class="btn btn-icon btn-sm btn-outline btn-outline-danger" onclick="destroy('${row.id}')">
                                    <i class="ki-outline ki-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            initComplete: function(settings, json) {
                let debounceTimer;
                $('#search-bank-soal-konversi').off('keyup').on('keyup', function() {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(function() {
                        $('#table-bank_soal_konversi').DataTable().search($('#search-bank-soal-konversi').val()).draw();
                    }, 300);
                });

                if (this.api().state && this.api().state.loaded()) {
                    $('#search-bank-soal-konversi').val(this.api().state.loaded().search.search);
                }

                resolve(true);
            }
        });
    });
};

function destroy(id) {
    Swal.fire({
        title: "Hapus Soal Konversi?",
        html: 'Apakah Anda yakin ingin menghapus soal ini?',
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, Hapus",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            blockUI.block();
            $.ajax({
                url: APP_URL + `bank-soal-konversi/${id}`,
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
