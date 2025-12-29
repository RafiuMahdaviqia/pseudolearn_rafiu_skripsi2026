var target = document.querySelector("#kt_app_body");
var blockUI = new KTBlockUI(target);
var APP_URL = window.APP_URL || "/";

$(() => {
    blockUI.block();
    initTable();
    blockUI.release();
});

initTable = () => {
    let kelas = $('#filter-kelas').val();
    // Ambil data dari input hidden
    let id_soal = $('input[name="id_soal"]').val();
    let index = $('input[name="index"]').val();
    let type = $('input[name="type"]').val();
    let typePrint = $('input[name="type"]').val();
    let value = $('input[name="value"]').val();

    return new Promise((resolve, reject) => {
        var table = $("#table-detail").DataTable({
            ajax: {
                url: APP_URL + "overlapping/analysis/table-detail",
                type: "POST",
                data: function (d) {
                    d._token = $('meta[name="csrf-token"]').attr("content"),
                    d.kelas = kelas || null;
                    d.id_soal = id_soal;
                    d.index = index;
                    d.type = type;
                    d.value = value;
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
                    data: "nim", 
                    orderable: true, 
                    searchable: false
                },
                { 
                    data: "name", 
                    orderable: true, 
                    searchable: true 
                },
                { 
                    data: "kelas_name", 
                    orderable: true, 
                    searchable: true 
                },
                { 
                    data: typePrint == "tipe_data" ? "index_tipe_data" : "index_algoritma", 
                    orderable: true, 
                    searchable: false,
                    className: "text-center"
                },
                { 
                    data: typePrint == "tipe_data" ?  "tipe_data" : "algoritma", 
                    orderable: false, 
                    searchable: false,
                    className: "text-center"
                },
                { 
                    data: "status", 
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
                    }
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
                        return typePrint == "tipe_data" ? row.index_tipe_data ?? '' : row.index_algoritma ?? '';
                    },
                },
                {
                    targets: 5,
                    render: function (data, type, row) {
                        return typePrint == "tipe_data" ? row.tipe_data ?? '' : row.algoritma ?? '';
                    },
                },
                {
                    targets: 6,
                    render: function (data, type, row) {
                        return row.status ?? '';
                    },
                },
            ],
            createdRow: function (row, data, dataIndex) {
                $(row).attr("id", data.id || data[0]);
            },
            initComplete: function (settings, json) {
                var debounceTimer;
                $("#search-detail").on("keyup", function () {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(function () {
                        table.search($("#search-detail").val()).draw();
                    }, 300);
                });
                if (table.state && table.state.loaded()) {
                    $("#search-detail").val(table.state.loaded().search.search);
                }
                resolve(true);
            },
        });
    });
};

$('#filter-kelas').on('change', function() {
   initTable();
});