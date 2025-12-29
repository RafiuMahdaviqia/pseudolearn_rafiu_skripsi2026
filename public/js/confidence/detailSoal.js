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
        var table = $("#table-confidence").DataTable({
            ajax: {
                url: APP_URL + "confidence/tableConfidence",
                type: "POST",
                data: function (d) {
                    d._token = $('meta[name="csrf-token"]').attr("content"),
                    d.idMahasiswa = $('#idMahasiswa').val() || null,
                    d.idSoal = $('#idSoal').val() || null;
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
                    data: "judul_soal", 
                    orderable: true, 
                    searchable: true
                },
                { 
                    data: "status_jawaban_text", 
                    orderable: false, 
                    searchable: false,
                    className: "text-center"
                },
                { 
                    data: "status_confidence_text", 
                    orderable: false, 
                    searchable: false,
                    className: "text-center" 
                },
                {
                    data: "created_at",
                    orderable: true,
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
                        return row.judul_soal ?? '';
                    },
                },
                {
                    targets: 2,
                    render: function (data, type, row) {
                        return row.status_jawaban_text ?? '';
                    },
                },
                {
                    targets: 3,
                    render: function (data, type, row) {
                        return row.status_confidence_text ?? '';
                    },
                },
                {
                    targets: 4,
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
                        // Format as 03 Agustus 2025 13:11:39
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
                // $("#search-soal").on("keyup", function () {
                //     clearTimeout(debounceTimer);
                //     debounceTimer = setTimeout(function () {
                //         table.search($("#search-soal").val()).draw();
                //     }, 300);
                // });
                // if (table.state && table.state.loaded()) {
                //     $("#search-soal").val(table.state.loaded().search.search);
                // }
                resolve(true);
            },
        });
    });
};
