var target = document.querySelector("#kt_app_body");
var blockUI = new KTBlockUI(target);
var APP_URL = window.APP_URL || "/";
var tableDetail;

$(() => {
    blockUI.block();
    initTable();

    $('#filter-level').on('change', function () {
        reloadDetail();
    });
});

function initTable() {
    tableDetail = $("#table-ars").DataTable({
        ajax: {
            url: APP_URL + "ars/tableArsLog",
            type: "POST",
            data: function (d) {
                d._token = $('meta[name="csrf-token"]').attr("content");
                d.idMahasiswa = $('#idMahasiswa').val() || null;
                d.idLevel = $('#filter-level').val() || null;
            },
        },
        processing: true,
        serverSide: true, // Penting untuk log data
        destroy: true,
        responsive: false,
        order: [[0, "desc"]],
        columns: [
            { data: null },
            { data: "level" },
            { data: "soal" },
            { data: "ars_batch" },
            { data: "difficulty" },
            { data: "pseudo_label" },
            { data: "pseudo_durasi" },
            { data: "konversi_label" },
            { data: "konversi_durasi" },
            { data: "total_durasi" },
            { data: "created_at" },
        ],
        columnDefs: [
            {
                targets: 0,
                render: (d, t, r, m) => m.row + 1
            },
            {
                targets: 3,
                className: "text-center",
                render: function (data) {
                    return data ?? '-';
                }
            },
            {
                targets: 7,
                render: function (d, t, r) {
                    const sec = parseInt(r.durasi || 0);
                    const h = String(Math.floor(sec / 3600)).padStart(2, '0');
                    const m = String(Math.floor((sec % 3600) / 60)).padStart(2, '0');
                    const s = String(sec % 60).padStart(2, '0');
                    return `${h}:${m}:${s}`;
                }
            },
            {
                targets: [6,8,9],
                className: "text-center",
                render: function (data) {

                    const sec = parseInt(data || 0);

                    const h = String(Math.floor(sec / 3600)).padStart(2, '0');
                    const m = String(Math.floor((sec % 3600) / 60)).padStart(2, '0');
                    const s = String(sec % 60).padStart(2, '0');

                    return `${h}:${m}:${s}`;
                }
            },
            {
                targets: 10,
                render: function (d, t, r) {
                    if (!r.created_at) return '';

                    const date = new Date(r.created_at);
                    if (isNaN(date)) return '';

                    const bulan = [
                        'Jan','Feb','Mar','Apr','Mei','Jun',
                        'Jul','Agu','Sep','Okt','Nov','Des'
                    ];

                    return `${date.getDate()} ${bulan[date.getMonth()]} ${date.getFullYear()}`;
                }
            }
        ],
        initComplete: function () {
            blockUI.release();
        }
    });
}

function reloadDetail() {
    if (tableDetail) {
        tableDetail.ajax.reload();
    }
}