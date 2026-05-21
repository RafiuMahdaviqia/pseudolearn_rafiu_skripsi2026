// public/js/logUjianKode/index.js

$(document).ready(function () {
    // ── Inisialisasi Select2 ──────────────────────────────────────────────────
    $("#filter-kelas, #filter-level, #filter-soal").select2({
        minimumResultsForSearch: Infinity,
    });

    // ── Load soal berdasarkan level ───────────────────────────────────────────
    $("#filter-level").on("change", function () {
        const levelId = $(this).val();
        const $soalSelect = $("#filter-soal");

        $soalSelect
            .empty()
            .append('<option value="">Pilih Soal (opsional)</option>');

        if (!levelId) return;

        $.ajax({
            url: "/bank-soal-konversi/getSoalByLevel",
            method: "GET",
            data: { level_id: levelId },
            success: function (data) {
                $.each(data, function (i, soal) {
                    $soalSelect.append(
                        `<option value="${soal.id}">${soal.judul}</option>`,
                    );
                });
                $soalSelect.trigger("change");
            },
        });
    });

    // ── DataTables ────────────────────────────────────────────────────────────
    const table = $("#table-log-ujian-kode").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "/log-ujian-kode/table",
            type: "POST",
            data: function (d) {
                d._token = $('meta[name="csrf-token"]').attr("content");
                d.kelas = $("#filter-kelas").val();
                d.level = $("#filter-level").val();
                d.soal = $("#filter-soal").val();
                d.search = { value: $("#search-mahasiswa").val() };
            },
        },
        columns: [
            {
                data: null,
                className: "text-center",
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
                orderable: false,
            },
            { data: "nim", className: "text-start" },
            { data: "name", className: "text-start" },
            {
                data: "kelas_name",
                className: "text-start",
                render: function (data) {
                    return data ?? "-";
                },
            },
            {
                data: null,
                className: "text-center",
                orderable: false,
                render: function (data) {
                    const levelId = $("#filter-level").val() || "";
                    const soalId = $("#filter-soal").val() || "";
                    const url = `/log-ujian-kode/detail/${data.id_mahasiswa}?level=${levelId}&soal=${soalId}`;
                    return `<a href="${url}" class="btn btn-sm btn-primary">
                                <i class="ki-outline ki-eye fs-5"></i> Detail
                            </a>`;
                },
            },
        ],
        language: { url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json" },
        pageLength: 10,
        dom: "lrtip",
    });

    // ── Trigger reload saat filter berubah ───────────────────────────────────
    $("#filter-kelas, #filter-level, #filter-soal").on("change", function () {
        table.ajax.reload();
    });

    $("#search-mahasiswa").on("keyup", function () {
        table.ajax.reload();
    });
});
