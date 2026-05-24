var target = document.querySelector("#kt_app_body");
var blockUI = new KTBlockUI(target);
var APP_URL = window.APP_URL || "/";

$(() => {
    blockUI.block();
    initTable();
    blockUI.release();

    $('#filter-kelas').on('select2:select select2:unselect', function () {
    reloadTable();
});

    // search debounce
    let debounceTimer;
    $('#search-ars').on('keyup', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            tableArs.search(this.value).draw();
        }, 300);
    });

    // EXPORT
    $('#btn-export-ars').on('click', function () {

        const kelas = $('#filter-kelas').val() || '';

        window.location.href =
            APP_URL + 'ars/export?kelas=' + kelas;
    });
});

var tableArs;

function initTable() {
    let kelas = $('#filter-kelas').val();
    return new Promise((resolve, reject) => {

    window.tableArs = $("#table-ars").DataTable({
        ajax: {
    url: APP_URL + "ars/table",
    type: "POST",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: function (d) {
        d.kelas = $('#filter-kelas').val();
    },
    error: function (xhr) {
        console.log("AJAX ERROR:", xhr.responseText);
    }
},
        processing: true,
        serverSide: true,
        destroy: true,
        responsive: false,
        order: [[0, "desc"]],
        columns: [
            { data: null },
            { data: "nim" },
            { data: "name" },
            { data: "kelas" },
            { data: "total_ars" },
            { data: "total_soal" },
            { data: "total_waktu" },
            { data: "id" }
        ],
        columnDefs: [
            {
                targets: 0,
                render: (d, t, r, m) => m.row + 1
            },
            { targets: 1, render: (d) => d ?? '' },
            { targets: 2, render: (d) => d ?? '' },
            { targets: 3, render: (d, t, row) => row.kelas ?? '' },
            { targets: 4, className: "text-center" },
            { targets: 5, className: "text-center" },
            {
                targets: 6,
                className: "text-center",
                render: function (d, t, r) {
                    const sec = parseInt(d || 0);

                    const h = String(Math.floor(sec / 3600)).padStart(2, '0');
                    const m = String(Math.floor((sec % 3600) / 60)).padStart(2, '0');
                    const s = String(sec % 60).padStart(2, '0');

                    return `${h}:${m}:${s}`;
                }},
            {
                targets: 7,
                orderable: false,
                render: function (d, t, row) {
                    return `
                        <div class="d-flex justify-content-center">
                            <button class="btn btn-sm btn-outline-primary"
                                onclick="detail('${row.id}')">
                                <i class="ki-outline ki-eye"></i> Detail
                            </button>
                        </div>
                    `;
                }
            }
        ]
    });
});
};

function reloadTable() {
    if (tableArs) {
        tableArs.ajax.reload();
    }
}

function detail(id) {
    window.location.href = APP_URL + "ars/detail/" + id;
<<<<<<< HEAD
}
=======
}
>>>>>>> new-pseudolearn-ars-dda-decoy
