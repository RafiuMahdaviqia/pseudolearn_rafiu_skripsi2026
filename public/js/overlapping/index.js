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
        var table = $("#table-overlapping").DataTable({
            ajax: {
                url: APP_URL + "overlapping/tableSoal",
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
                    searchable: true 
                },
                { 
                    data: "soal", 
                    orderable: true, 
                    searchable: true 
                },
                {
                    data: null,
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
                    render: function (data, type, row, meta) {
                        return `
                            <button type="button" class="btn btn-sm btn-outline btn-outline-info d-flex align-items-center gap-1 p-2" onclick="analisis('${row.id}')">
                                <i class="ki-outline ki-eye"></i>
                                <span>Analisis</span>
                            </button>
                        `;
                    },
                },
            ],
            createdRow: function (row, data, dataIndex) {
                $(row).attr("id", data.id || data[0]);
            },
            initComplete: function (settings, json) {
                var debounceTimer;
                $("#search-overlapping").on("keyup", function () {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(function () {
                        table.search($("#search-overlapping").val()).draw();
                    }, 300);
                });
                if (table.state && table.state.loaded()) {
                    $("#search-overlapping").val(table.state.loaded().search.search);
                }
                resolve(true);
            },
        });
    });
};

$('#filter-level').on('change', function() {
   initTable();
});

analisis = (id_soal) => {
    window.location.href = `${APP_URL}overlapping/analysis/${id_soal}`;
};