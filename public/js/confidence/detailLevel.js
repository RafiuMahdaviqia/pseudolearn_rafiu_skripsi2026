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
        var table = $("#table-soal").DataTable({
            ajax: {
                url: APP_URL + "confidence/tableDetailSoal",
                type: "POST",
                data: function (d) {
                    d._token = $('meta[name="csrf-token"]').attr("content"),
                    d.idMahasiswa = $('#idMahasiswa').val() || null;
                    d.idLevel = $('#idLevel').val() || null;
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
                    data: "judul", 
                    orderable: true, 
                    searchable: true
                },
                { 
                    data: "yakinSalah", 
                    orderable: false, 
                    searchable: false,
                    className: "text-center"
                },
                { 
                    data: "yakinBenar", 
                    orderable: false, 
                    searchable: false,
                    className: "text-center" 
                },
                { 
                    data: "tidakYakinSalah", 
                    orderable: false, 
                    searchable: false,
                    className: "text-center" 
                },
                { 
                    data: "tidakYakinBenar", 
                    orderable: false, 
                    searchable: false,
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
                        return row.judul ?? '';
                    },
                },
                {
                    targets: 2,
                    render: function (data, type, row) {
                        return row.yakinSalah ?? '';
                    },
                },
                {
                    targets: 3,
                    render: function (data, type, row) {
                        return row.yakinBenar ?? '';
                    },
                },
                {
                    targets: 4,
                    render: function (data, type, row) {
                        return row.tidakYakinSalah ?? '';
                    },
                },
                {
                    targets: 5,
                    render: function (data, type, row) {
                        return row.tidakYakinBenar ?? '';
                    },
                },
                {
                    targets: 6,
                    render: function (data, type, row, meta) {
                        return `
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-sm btn-outline btn-outline-primary d-flex align-items-center gap-1 p-2" onclick="detailSoal('${row.id}', '${$('#idMahasiswa').val()}')">
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

detailSoal = (idSoal, idMahasiswa) => {
    window.location.href = APP_URL + "confidence/detailSoal/" + idSoal + "?idMahasiswa=" + idMahasiswa;
};
