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
    return new Promise((resolve, reject) => {
        var table = $("#table-ars").DataTable({
            ajax: {
                url: APP_URL + "ars/table",
                type: "POST",
                data: function (d) {
                    d._token = $('meta[name="csrf-token"]').attr("content"),
                    d.kelas = kelas || null;
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
                    searchable: true
                },
                { 
                    data: "name", 
                    orderable: true, 
                    searchable: true 
                },
                { 
                    data: "kelas_name", 
                    orderable: true, 
                    searchable: true,
                    className: "text-center"
                },
                { 
                    data: "totalArs", 
                    orderable: true, 
                    searchable: true,
                    className: "text-center"
                },
                { 
                    data: "jumlahSoalTambahan", 
                    orderable: true, 
                    searchable: true,
                    className: "text-center"
                },
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
                        return row.totalArs ?? '';
                    },
                },
                {
                    targets: 5,
                    render: function (data, type, row) {
                        return row.jumlahSoalTambahan ?? '';
                    },
                },
                {
                    targets: 6,
                    render: function (data, type, row) {
                        return row.totalWaktu ?? '';
                    },
                },
                {
                    targets: 7,
                    render: function (data, type, row, meta) {
                        return `
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-sm btn-outline btn-outline-primary d-flex align-items-center gap-1 p-2" onclick="detail('${row.id}')">
                                    <i class="ki-outline ki-eye"></i>
                                    <span>Detail</span>
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
                $("#search-ars").on("keyup", function () {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(function () {
                        table.search($("#search-ars").val()).draw();
                    }, 300);
                });
                if (table.state && table.state.loaded()) {
                    $("#search-ars").val(table.state.loaded().search.search);
                }
                resolve(true);
            },
        });
    });
};

$('#filter-kelas').on('change', function() {
   initTable();
});

function detail(id) {
    window.location.href = APP_URL + "ars/detail/" + id;
}