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
        var table = $("#table-level").DataTable({
            ajax: {
                url: APP_URL + "confidence/tableDetail",
                type: "POST",
                data: function (d) {
                    d._token = $('meta[name="csrf-token"]').attr("content"),
                    d.idMahasiswa = $('#idMahasiswa').val() || null;
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
                    data: "name", 
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
                        return row.name ?? '';
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
                                <button type="button" class="btn btn-sm btn-outline btn-outline-primary d-flex align-items-center gap-1 p-2" onclick="detailLevel('${row.id}', '${$('#idMahasiswa').val()}')">
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
                $("#search-level").on("keyup", function () {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(function () {
                        table.search($("#search-level").val()).draw();
                    }, 300);
                });
                if (table.state && table.state.loaded()) {
                    $("#search-level").val(table.state.loaded().search.search);
                }
                resolve(true);
            },
        });
    });
};

detailLevel = (idLevel, idMahasiswa) => {
    window.location.href = APP_URL + "confidence/detailLevel/" + idLevel + "?idMahasiswa=" + idMahasiswa;
};
