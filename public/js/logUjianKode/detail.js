$(document).ready(function () {
    const idMahasiswa = $("#idMahasiswa").val();
    const initLevelId = $("#levelId").val();
    const initSoalId = $("#soalId").val();

    $("#filter-level, #filter-soal").select2({
        minimumResultsForSearch: Infinity,
    });

    // DataTables
    const table = $("#table-detail-log-ujian-kode").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "/log-ujian-kode/table-detail",
            type: "POST",
            data: function (d) {
                d._token = $('meta[name="csrf-token"]').attr("content");
                d.id_mahasiswa = idMahasiswa;
                d.id_level = $("#filter-level").val() || "";
                d.id_soal = $("#filter-soal").val() || "";
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
            { data: "judul_soal", className: "text-start" },
            {
                data: "created_at",
                className: "text-start",
                render: function (data) {
                    if (!data) return "-";
                    const d = new Date(data);
                    return d.toLocaleDateString("id-ID", {
                        day: "2-digit",
                        month: "short",
                        year: "numeric",
                    });
                },
            },
            {
                data: "drag_drop",
                className: "text-center",
                render: function (data) {
                    if (data === null || data === undefined) return "-";
                    return `<span class="badge badge-info">${data}</span>`;
                },
            },
            {
                data: "total_submit",
                className: "text-center",
                render: function (data) {
                    if (data === null || data === undefined) return "-";
                    return `<span class="badge badge-primary">${data}</span>`;
                },
            },
            {
                data: "waktu",
                className: "text-center",
                render: function (data) {
                    if (!data && data !== 0) return "-";
                    const sec = parseInt(data);
                    const h = Math.floor(sec / 3600);
                    const m = Math.floor((sec % 3600) / 60);
                    const s = sec % 60;
                    const parts = [];
                    if (h > 0) parts.push(h + " jam");
                    if (m > 0) parts.push(m + " mnt");
                    parts.push(s + " dtk");
                    return parts.join(" ");
                },
            },
            {
                data: null,
                className: "text-center",
                orderable: false,
                render: function (data) {
                    const levelId = $("#filter-level").val() || "";
                    const filterSoalId = $("#filter-soal").val() || "";
                    const url = `/log-ujian-kode/detail-kode/${data.id}?id_mahasiswa=${idMahasiswa}&id_level=${levelId}&id_soal=${filterSoalId}`;
                    return `<a href="${url}" class="btn btn-sm btn-info">
                                <i class="ki-outline ki-code fs-5"></i> Lihat Kode
                            </a>`;
                },
            },
        ],
        language: { url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json" },
        pageLength: 10,
        dom: "lrtip",
        order: [[2, "desc"]],
    });

    // Refresh stats di card
    function refreshStats() {
        $.ajax({
            url: "/log-ujian-kode/summary-stats",
            method: "GET",
            data: {
                id_mahasiswa: idMahasiswa,
                id_level: $("#filter-level").val() || "",
                id_soal: $("#filter-soal").val() || "",
            },
            success: function (res) {
                $("#stat-drag").text(res.total_drag);
                $("#stat-submit").text(res.total_submit);
                $("#stat-waktu").text(res.total_waktu);
            },
        });
    }

    // Card info Level & Nama Soal
    function updateInfoCard(levelName, soalName) {
        $("#info-level").html(
            levelName
                ? `<span class="badge badge-primary">${levelName}</span>`
                : "-",
        );
        $("#info-soal").text(soalName || "-");
    }

    function initFiltersFromHiddenInputs() {
        if (!initLevelId) return;

        // Pre-select level
        $("#filter-level").val(initLevelId).trigger("change.select2");

        // Load opsi soal untuk level tersebut
        $.ajax({
            url: "/bank-soal-konversi/getSoalByLevel",
            method: "GET",
            data: { level_id: initLevelId },
            success: function (data) {
                const $soalSelect = $("#filter-soal");
                $soalSelect.empty().append('<option value="">Pilih Soal</option>');

                $.each(data, function (i, soal) {
                    $soalSelect.append(
                        `<option value="${soal.id}">${soal.judul}</option>`,
                    );
                });

                $soalSelect.select2("destroy").select2({
                    minimumResultsForSearch: Infinity,
                });

                // Pre-select soal jika ada
                if (initSoalId) {
                    $soalSelect.val(initSoalId).trigger("change.select2");
                }

                // Update info card
                const levelName = $("#filter-level").find("option:selected").text();
                const soalName  = initSoalId
                    ? $soalSelect.find("option:selected").text()
                    : "";
                updateInfoCard(levelName, soalName);

                // Reload tabel dan stat dengan filter yang sudah ter-restore
                table.ajax.reload();
                refreshStats();
            },
        });
    }

    // Jalankan restore filter setelah DOM siap
    initFiltersFromHiddenInputs();

    // Pilih level
    $("#filter-level").on("change", function () {
        const levelId = $(this).val();
        const levelName = $(this).find("option:selected").text();
        const $soalSelect = $("#filter-soal");

        // Reset soal
        $soalSelect.empty().append('<option value="">Pilih Soal</option>');

        // Reset card info
        updateInfoCard(levelId ? levelName : "", "");

        // Reload tabel
        table.ajax.reload();

        if (!levelId) return;

        // Load soal sesuai level
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

                $soalSelect.select2("destroy");
                $soalSelect.select2({
                    minimumResultsForSearch: Infinity,
                });
            },
        });
    });

    // Pilih soal
    $(document).on("change", "#filter-soal", function () {
        const soalName = $(this).find("option:selected").text();
        const levelName = $("#filter-level").find("option:selected").text();
        const levelId = $("#filter-level").val();

        updateInfoCard(levelId ? levelName : "", $(this).val() ? soalName : "");

        table.ajax.reload();
    });

    // Export excel
    $("#btn-export").on("click", function () {
        const params = new URLSearchParams({
            id_mahasiswa: idMahasiswa,
            id_level: $("#filter-level").val() || "",
            id_soal: $("#filter-soal").val() || "",
        });
        window.location.href = `/log-ujian-kode/export-detail?${params.toString()}`;
    });
});
