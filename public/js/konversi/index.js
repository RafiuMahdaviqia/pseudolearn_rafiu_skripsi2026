var target = document.querySelector("#kt_app_body");
var blockUI = new KTBlockUI(target);
var APP_URL = window.APP_URL || "/";

$(() => {
    blockUI.block();
    initTableKonversi();

    blockUI.release();
});

initTableKonversi = () => {
    return new Promise((resolve, reject) => {
        var table = $("#table-konversi").DataTable({
            ajax: {
                url: APP_URL + "konversi/table",
                type: "POST",
                data: function (d) {
                    d._token = $('meta[name="csrf-token"]').attr("content"),
                    d.id_level = $('#filter-level').val() || null;
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
                    data: "judul_soal", 
                    orderable: true, 
                    searchable: true 
                },
                { 
                    data: "soal_name", 
                    orderable: true, 
                    searchable: true 
                },
                {
                    data: "bobot",
                    orderable: false,
                    searchable: false,
                },
                {
                    data: "output",
                    orderable: false,
                    searchable: true,
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
                        return row.level_name ? row.level_name : '<span class="text-muted">Tidak ada level</span>';
                    },
                },
                {
                    targets: 2,
                    render: function (data, type, row) {
                        return row.judul_soal ? row.judul_soal : '<span class="text-muted">Tidak ada judul soal</span>';
                    },
                },
                {
                    targets: 3,
                    render: function (data, type, row) {
                        return row.soal_name ? row.soal_name : '<span class="text-muted">Tidak ada soal</span>';
                    },
                },
                {
                    targets: 4,
                    render: function (data, type, row) {
                        return row.bobot ? row.bobot : '<span class="text-muted">Tidak ada bobot</span>';
                    },
                },
                {
                    targets: 5,
                    render: function (data, type, row) {
                        return row.output ? row.output : '<span class="text-muted">Tidak ada output</span>';
                    },
                },
                {
                    targets: 6,
                    render: function (data, type, row) {
                        return `
                            <div class="d-flex gap-3 justify-content-center">
                                <a href="${APP_URL}konversi/form/${row.id}" class="btn btn-icon btn-sm btn-outline btn-outline-warning">
                                    <i class="ki-outline ki-pencil"></i>
                                </a>
                                <button class="btn btn-icon btn-sm btn-outline btn-outline-danger" onclick="destroy('${row.id}')">
                                    <i class="ki-outline ki-trash"></i>
                                </button>
                            </div>
                        `;
                    },
                }
            ],
            createdRow: function (row, data, dataIndex) {
                $(row).attr("id", data.id || data[0]);
            },
            initComplete: function (settings, json) {
                var debounceTimer;
                $("#search-konversi").on("keyup", function () {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(function () {
                        table.search($("#search-konversi").val()).draw();
                    }, 300);
                });
                if (table.state && table.state.loaded()) {
                    $("#search-konversi").val(table.state.loaded().search.search);
                }
                resolve(true);
            },
        });
    });
};

$('#filter-level').on('change', function () {
    initTableKonversi();
});

destroy = (id) => {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: 'Data yang dihapus tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `${APP_URL}konversi/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function () {
                    initTableKonversi();
                    Swal.fire('Berhasil!', 'Data berhasil dihapus.', 'success');
                },
                error: function () {
                    Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus data.', 'error');
                }
            });
        }
    });
};