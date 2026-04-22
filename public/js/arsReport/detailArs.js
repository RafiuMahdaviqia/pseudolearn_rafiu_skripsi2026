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
        var table = $("#table-ars").DataTable({
            ajax: {
                url: APP_URL + "ars/tableArsLog",
                type: "POST",
                data: function (d) {
                    d._token = $('meta[name="csrf-token"]').attr("content"),
                    d.idMahasiswa = $('#idMahasiswa').val() || null;
                    d.idLevel = $('#filter-level').val() || null;
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
                },
                { 
                    data: "level", 
                    className: "text-center" 
                },
                { 
                    data: "soal", 
                    className: "text-center" 
                },
                { 
                    data: "jenis_soal", 
                    className: "text-center" 
                },
                { 
                    data: "difficulty", 
                    className: "text-center" 
                },
                { 
                    data: "label", 
                     className: "text-center" 
                },
                { 
                    data: "durasi", 
                     className: "text-center" 
                },
                { 
                    data: "created_at", 
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
                    targets: 3,
                    render: function (data) {
                        if (data === 'pseudo') {
                            return '<span class="badge badge-primary">Pseudo</span>';
                        }
                        if (data === 'konversi') {
                            return '<span class="badge badge-info">Konversi</span>';
                        }
                        return '-';
                    },
                },
                {
                    targets: 6,
                    render: function (data, type, row) {
                        // Convert seconds to HH:MM:SS
                        const seconds = parseInt(row.durasi, 10);
                        if (isNaN(seconds)) return '-';
                        const h = Math.floor(seconds / 3600).toString().padStart(2, '0');
                        const m = Math.floor((seconds % 3600) / 60).toString().padStart(2, '0');
                        const s = (seconds % 60).toString().padStart(2, '0');
                        return `${h}:${m}:${s}`;
                    },
                },
                {
                    targets: 7,
                    render: function (data, type, row) {
                        if (!row.created_at) return '';
                        let dateStr = row.created_at;
                        if (!/Z$|[+-]\d{2}:\d{2}$/.test(dateStr)) {
                            dateStr += 'Z';
                        }
                        const utcDate = new Date(dateStr);
                        if (isNaN(utcDate.getTime())) return '';
                        // Convert to GMT+7
                        utcDate.setHours(utcDate.getHours());
                        // Format 20 April 2026
                        const bulan = [
                            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                        ];
                        const day = utcDate.getDate().toString().padStart(2, '0');
                        const month = bulan[utcDate.getMonth()];
                        const year = utcDate.getFullYear();
                        const hours = utcDate.getHours().toString().padStart(2, '0');
                        const minutes = utcDate.getMinutes().toString().padStart(2, '0');
                        const seconds = utcDate.getSeconds().toString().padStart(2, '0');
                        return `${day} ${month} ${year} ${hours}:${minutes}:${seconds}`;
                    },
                },
            ],
            createdRow: function (row, data, dataIndex) {
                $(row).attr("id", data.id || data[0]);
            },
            initComplete: function (settings, json) {
                var debounceTimer;
                resolve(true);
            },
        });
    });
};
