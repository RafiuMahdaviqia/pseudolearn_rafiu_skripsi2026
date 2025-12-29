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
        var table = $("#table-leaderboard").DataTable({
            ajax: {
                url: APP_URL + "leaderboard/table",
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
                    data: "rank",
                    className: "text-center",
                    orderable: true,
                    searchable: false,
                },
                { 
                    data: "mahasiswa_name", 
                    orderable: true, 
                    searchable: true 
                },
                { 
                    data: "total_skor", 
                    orderable: true, 
                    searchable: false 
                },
                {
                    data: "total_waktu",
                    orderable: true,
                    searchable: false,
                },
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type, row, meta) {
                        return row.rank;
                    },
                },
                {
                    targets: 1,
                    render: function (data, type, row, meta) {
                        const esc = (v) =>
                            String(v ?? "").replace(/&/g, "&amp;")
                                           .replace(/</g, "&lt;")
                                           .replace(/>/g, "&gt;")
                                           .replace(/"/g, "&quot;")
                                           .replace(/'/g, "&#39;");
                        const name = esc(row.mahasiswa_name);
                        const id = esc(row.id_user);
                        // keep id_user hidden, show only mahasiswa_name
                        return `
                        <input type="hidden" class="js-id-user" value="${id}">${name}
                        `;
                    },
                },
                {
                    targets: 2,
                    render: function (data, type, row, meta) {
                        const v = parseFloat(row.total_skor);
                        return Number.isFinite(v) ? Math.ceil(v) : row.total_skor;
                    },
                },
                {
                    targets: 3,
                    render: function (data, type, row, meta) {
                        const raw = row.total_waktu;
                        if (raw === null || raw === undefined || raw === '') return '';
                        const sec = parseInt(raw, 10);
                        if (isNaN(sec)) return String(raw);

                        const h = Math.floor(sec / 3600);
                        const m = Math.floor((sec % 3600) / 60);
                        const s = sec % 60;

                        const parts = [];
                        if (h > 0) parts.push(`${h} jam`);
                        if (m > 0) parts.push(`${m} menit`);
                        if (s > 0 || parts.length === 0) parts.push(`${s} detik`);

                        return parts.join(' ');
                    },
                },
            ],
            createdRow: function (row, data, dataIndex) {
                $(row).attr("id", data.id || data[0]);
            },
            initComplete: function (settings, json) {
                var debounceTimer;
                $("#search-mahasiswa").on("keyup", function () {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(function () {
                        table.search($("#search-mahasiswa").val()).draw();
                    }, 300);
                });
                if (table.state && table.state.loaded()) {
                    $("#search-mahasiswa").val(table.state.loaded().search.search);
                }
                resolve(true);
            },
        });
    });
};