var target = document.querySelector("#kt_app_body");
var blockUI = new KTBlockUI(target);
var APP_URL = window.APP_URL || "/";

$(() => {
    blockUI.block();
    initTable();
    resetSoalSelect();
    blockUI.release();
});

initTable = () => {
    let kelas = $('#filter-kelas').val();
    let level = $('#filter-level').val();
    let soal  = $('#filter-soal').val();
    return new Promise((resolve, reject) => {
        var table = $("#table-ujian-konversi").DataTable({
            ajax: {
                url: APP_URL + "ujian-konversi/table",
                type: "POST",
                data: function (d) {
                    d._token = $('meta[name="csrf-token"]').attr("content"),
                    d.kelas = kelas || null;
                    d.level = level || null;
                    d.soal  = soal  || null;
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
                    data: "nim", 
                    orderable: true, 
                    searchable: true,
                },
                { 
                    data: "name", 
                    orderable: true, 
                    searchable: true,
                },
                { 
                    data: "kelas_name", 
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
                    render: function (data, type, row, meta) {
                        return `
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-sm btn-outline btn-outline-info d-flex align-items-center gap-1" onclick="detail('${row.id}')">
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

$('#filter-kelas').on('change', function() {
   initTable();
});

detail = (idMahasiswa) => {
    const level = $('#filter-level').val();
    const soal  = $('#filter-soal').val();

    if (!level) {
        Swal.fire({
            title: 'Peringatan',
            text: 'Level harus dipilih',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return;
    }

    const params = new URLSearchParams();
    params.set('level', level);
    if (soal !== undefined && soal !== null && soal !== '') {
        params.set('soal', soal);
    }

    window.location.href = APP_URL + "ujian-konversi/detail/" + idMahasiswa + "?" + params.toString();
};


function resetSoalSelect() {
    $('#filter-soal').html('<option value="">Filter Soal</option>').val('').trigger('change');
}

// Change Level -> load soal
$('#filter-level').on('change', function() {
    const levelId = $(this).val();
    if (!levelId) {
        resetSoalSelect();
        // initTable();
        return;
    }

    let opts = '<option value="">Pilih Soal</option>';
    $.ajax({
        url: APP_URL + "log-activity/getSoalByLevel",
        type: "GET",
        data: {
            level_id: levelId,
            _token: $('meta[name="csrf-token"]').attr("content")
        },
        success: function(res){
            // Append from AJAX response
            (res || []).forEach(s => {
                opts += `<option value="${s.id}">${s.judul}</option>`;
            });

            $('#filter-soal').html(opts).val('').trigger('change');
            // initTable();
        },
        error: function(){
            resetSoalSelect();
        }
    });
});

// Change Soal
// $('#filter-soal').on('change', function() {
//     initTable();
// });