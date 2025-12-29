var target = document.querySelector("#kt_app_body");
var blockUI = new KTBlockUI(target);
var APP_URL = window.APP_URL || "/";

$(() => {
    blockUI.block();
    initTable();
    blockUI.release();
});

initTable = () => {
    let id_mahasiswa = $('#idMahasiswa').val();
    let level_id = $('#levelId').val();
    let soal_id = $('#soalId').val();

    return new Promise((resolve, reject) => {
        var table = $("#table-detail-ujian-konversi").DataTable({
            ajax: {
                url: APP_URL + "ujian-konversi/table-detail",
                type: "POST",
                data: function (d) {
                    d._token = $('meta[name="csrf-token"]').attr("content");
                    d.id_mahasiswa = id_mahasiswa || null;
                    d.id_level = level_id || null;
                    d.id_soal = soal_id || null;
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
                    orderable: false,
                    searchable: false,
                    width: "5%",
                },
                { 
                    data: "judul_soal", 
                    orderable: true, 
                    searchable: true,
                },
                { 
                    data: "created_at", 
                    orderable: true, 
                    searchable: true,
                },
                { 
                    data: "nilai", 
                    orderable: true, 
                    searchable: true,
                    className: "text-center",
                },
                { 
                    data: "waktu", 
                    orderable: true, 
                    searchable: true,
                    className: "text-center",
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
                    render: function (data, type, row, meta) {
                        return row.judul_soal ?? '';
                    },
                },
                {
                    targets: 2,
                    render: function (data, type, row) {
                        const created = row.created_at;
                        if (!created) return '';
                        const [datePart = '', timePart = ''] = created.split(' ');
                        const [y, m, d] = datePart.split('-');
                        if (!y || !m || !d) return created;
                        return `${d}-${m}-${y}${timePart ? ' ' + timePart : ''}`;
                    },
                },
                {
                    targets: 3,
                    render: function (data, type, row) {
                        return row.nilai ?? '';
                    },
                },
                {
                    targets: 4,
                    render: function (data, type, row) {
                        const raw = row.waktu;
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
                {
                    targets: 5,
                    render: function (data, type, row, meta) {
                        return `
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-sm btn-outline btn-outline-info d-flex align-items-center gap-1" onclick="detailKonversi('${row.id}')">
                                    <i class="ki-outline ki-eye p-0 fs-5 me-1"></i>
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
                // $("#search-mahasiswa").on("keyup", function () {
                //     clearTimeout(debounceTimer);
                //     debounceTimer = setTimeout(function () {
                //         table.search($("#search-mahasiswa").val()).draw();
                //     }, 300);
                // });
                // if (table.state && table.state.loaded()) {
                //     $("#search-mahasiswa").val(table.state.loaded().search.search);
                // }
                resolve(true);
            },
        });
    });
};

detailKonversi = (id) => {
    const id_mahasiswa = $('#idMahasiswa').val();
    const level_id = $('#levelId').val();
    const soal_id = $('#soalId').val();

    const base = APP_URL + "ujian-konversi/detail-konversi/" + encodeURIComponent(id);
    const url = new URL(base, window.location.origin);

    if (id_mahasiswa) url.searchParams.set('id_mahasiswa', id_mahasiswa);
    if (level_id) url.searchParams.set('id_level', level_id);
    if (soal_id) url.searchParams.set('id_soal', soal_id);

    window.location.href = url.toString();
};