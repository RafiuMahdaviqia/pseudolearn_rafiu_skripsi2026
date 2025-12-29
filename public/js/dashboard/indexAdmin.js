var target = document.querySelector("#kt_app_body");
var blockUI = new KTBlockUI(target);
var APP_URL = window.APP_URL || "/";

$(() => {
    blockUI.block();
    getDataFilter();
    getChartLabeling();
    getChartScoring();
    getChartConfidence();
    blockUI.release();
});

// get data filter
function getDataFilter() {
    $.ajax({
        url: APP_URL + "dashboard/data-filter-admin",
        type: "GET",
        success: function (data) {

            // ===== Filter Tahun Aktivitas Ujian (Static 2010 - currentYear + 5) =====
            const $tahunAktivitas = $("#filter-tahun-aktivitas");
            const startYear = 2010;
            const currentYear = new Date().getFullYear();
            const endYear = currentYear + 5;

            // Simpan nilai sebelum di-reset (jika sudah ada dari server / user)
            const previousYearValue = $tahunAktivitas.val();

            $tahunAktivitas.empty().append('<option value=""></option>');
            for (let y = startYear; y <= endYear; y++) {
                const selectedAttr =
                    (!previousYearValue && y === currentYear) || Number(previousYearValue) === y
                        ? ' selected'
                        : '';
                $tahunAktivitas.append(`<option value="${y}"${selectedAttr}>${y}</option>`);
            }

            if ($tahunAktivitas.data('select2')) {
                $tahunAktivitas.select2('destroy');
            }
            if ($tahunAktivitas.attr('data-control') === 'select2') {
                $tahunAktivitas.select2({
                    placeholder: 'Semua Tahun',
                    allowClear: false,
                    width: '100%',
                    minimumResultsForSearch: Infinity
                });
            }

            // Jika tidak ada nilai sebelumnya, pastikan set tahun sekarang dan trigger change
            if (!previousYearValue) {
                $tahunAktivitas.val(String(currentYear)).trigger('change');
            }

            // ===== Filter Bulan Aktivitas Ujian (Static 1-12) =====
            const monthNames = [
                "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                "Juli", "Agustus", "September", "Oktober", "November", "Desember"
            ];
            const $bulanAktivitas = $("#filter-bulan-aktivitas");
            const currentMonthVal = String(new Date().getMonth() + 1).padStart(2, '0');

            // Simpan nilai sebelum di-reset (jika sudah pernah diset lewat server / user)
            const previousValue = $bulanAktivitas.val();

            $bulanAktivitas.empty().append('<option value=""></option>');
            monthNames.forEach((name, index) => {
                const monthValue = (index + 1).toString().padStart(2, '0');
                const selectedAttr =
                    (!previousValue && monthValue === currentMonthVal) || previousValue === monthValue
                        ? ' selected'
                        : '';
                $bulanAktivitas.append(`<option value="${monthValue}"${selectedAttr}>${name}</option>`);
            });

            // Reinit select2 jika digunakan
            if ($bulanAktivitas.data('select2')) {
                $bulanAktivitas.select2('destroy');
            }
            if ($bulanAktivitas.attr('data-control') === 'select2') {
                $bulanAktivitas.select2({
                    placeholder: 'Semua Bulan',
                    allowClear: false,
                    width: '100%',
                    minimumResultsForSearch: Infinity
                });
            }

            // Jika tidak ada nilai sebelumnya, set ke bulan sekarang dan trigger change
            if (!previousValue) {
                $bulanAktivitas.val(currentMonthVal).trigger('change');
            }

            // ===== Filter Kelas Aktivitas Ujian =====
            const kelasListAktivitas = data?.filter_kelas || [];
            const $kelasAktivitas = $("#filter-kelas-aktivitas");
            $kelasAktivitas.empty().append('<option value=""></option>');
            kelasListAktivitas.forEach(item => {
                $kelasAktivitas.append(`<option value="${item.id}">${item.name}</option>`);
            });

            if ($kelasAktivitas.data('select2')) {
                $kelasAktivitas.select2('destroy');
            }

            if ($kelasAktivitas.attr('data-control') === 'select2') {
                $kelasAktivitas.select2({
                    placeholder: 'Semua Kelas',
                    allowClear: true,
                    width: '100%',
                    minimumResultsForSearch: Infinity
                });
            }

            $('#filter-kelas-aktivitas').on('change', function () {
                getChartAktivitasUjian();
            });

            $('#filter-tahun-aktivitas, #filter-bulan-aktivitas').on('change', function () {
                getChartAktivitasUjian();
            });

            getChartAktivitasUjian();

            // ===== Filter Kelas Labeling =====
            const kelasList = data?.filter_kelas || [];

            const $labeling = $("#filter-kelas-labeling");
            $labeling.empty().append('<option value=""></option>');
            kelasList.forEach(item => {
                $labeling.append(`<option value="${item.id}">${item.name}</option>`);
            });

            if ($labeling.data('select2')) {
                $labeling.select2('destroy');
            }
            if ($labeling.attr('data-control') === 'select2') {
                $labeling.select2({
                    placeholder: 'Semua Kelas',
                    allowClear: true,
                    width: '100%',
                    minimumResultsForSearch: Infinity
                });
            }

            // ===== Filter Kelas Scoring (new) =====
            const $scoring = $("#filter-kelas-scoring");
            if ($scoring.length) {
                $scoring.empty().append('<option value=""></option>');
                kelasList.forEach(item => {
                    $scoring.append(`<option value="${item.id}">${item.name}</option>`);
                });

                if ($scoring.data('select2')) {
                    $scoring.select2('destroy');
                }
                if ($scoring.attr('data-control') === 'select2') {
                    $scoring.select2({
                        placeholder: 'Semua Kelas',
                        allowClear: true,
                        width: '100%',
                        minimumResultsForSearch: Infinity
                    });
                }
            }

            // ===== Filter Kelas Scoring (new) =====
            const $confidence = $("#filter-kelas-confidence");
            if ($confidence.length) {
                $confidence.empty().append('<option value=""></option>');
                kelasList.forEach(item => {
                    $confidence.append(`<option value="${item.id}">${item.name}</option>`);
                });

                if ($confidence.data('select2')) {
                    $confidence.select2('destroy');
                }
                if ($confidence.attr('data-control') === 'select2') {
                    $confidence.select2({
                        placeholder: 'Semua Kelas',
                        allowClear: true,
                        width: '100%',
                        minimumResultsForSearch: Infinity
                    });
                }
            }
        },
        error: function (xhr) {
            blockUI.release();
            Swal.fire({
                text: xhr.responseJSON?.message || "Gagal mengambil data Filter.",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "OK",
                customClass: { confirmButton: "btn btn-primary" },
            });
        },
    });
}

// Change handler to reload chart when user picks a class
$(document).on('change', '#filter-kelas-labeling', function () {
    getChartLabeling();
});

// Change handler to reload chart when user picks a class
$(document).on('change', '#filter-kelas-scoring', function () {
    getChartScoring();
});

// Change handler to reload chart when user picks a class
$(document).on('change', '#filter-kelas-confidence', function () {
    getChartConfidence();
});

// chart labeling
let chartLabelingInstance;

function getChartLabeling() {
    let filterKelasLabeling = $("#filter-kelas-labeling").val() || "";
    $.ajax({
        url: APP_URL + "dashboard/chart-labeling",
        data: { kelas_id: filterKelasLabeling },
        type: "GET",
        success: function (res) {
            const ctx = document.getElementById('chart-labeling');
            if (!ctx) return;

            const source = res?.data || res || {};
            const labels = ["Ideal", "Normal", "Struggling", "Gaming the System"];
            const counts = labels.map(l => Number(source[l]) || 0);

            const backgroundColors = [
                '#0d6efd',
                '#17a2b8',
                '#ffc107',
                '#dc3545'
            ];

            if (chartLabelingInstance) {
                chartLabelingInstance.destroy();
            }

            chartLabelingInstance = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels,
                    datasets: [{
                        label: 'Jumlah',
                        data: counts,
                        backgroundColor: backgroundColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            align: 'center',
                            labels: {
                                boxWidth: 18,
                                padding: 12
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const total = counts.reduce((a, b) => a + b, 0) || 1;
                                    const val = context.parsed;
                                    const pct = ((val / total) * 100).toFixed(1);
                                    return `${context.label}: ${val} (${pct}%)`;
                                }
                            }
                        },
                        title: { display: false }
                    },
                    layout: {
                        padding: { top: 0, right: 10, bottom: 0, left: 0 }
                    }
                }
            });
        },
        error: function (xhr) {
            blockUI.release();
            Swal.fire({
                text: xhr.responseJSON?.message || "Gagal mengambil data Labeling.",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "OK",
                customClass: { confirmButton: "btn btn-primary" },
            });
        },
    });
}

// chart scoring (new)
let chartScoringInstance;

function getChartScoring() {
    let filterKelasScoring = $("#filter-kelas-scoring").val() || "";
    $.ajax({
        url: APP_URL + "dashboard/chart-scoring",
        data: { kelas_id: filterKelasScoring },
        type: "GET",
        success: function (res) {
            const ctx = document.getElementById('chart-scoring');
            if (!ctx) return;

            const source = res?.data || res || {};
            // Hanya 4 data: 30, 50, 70, 90
            const labels = ["90", "70", "50", "30"];
            const counts = labels.map(k => Number(source[k]) || 0);

            const backgroundColors = [
                '#0d6efd',
                '#17a2b8',
                '#dc3545',
                '#ffc107',
            ];

            if (chartScoringInstance) {
                chartScoringInstance.destroy();
            }

            chartScoringInstance = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels,
                    datasets: [{
                        label: 'Jumlah',
                        data: counts,
                        backgroundColor: backgroundColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            align: 'center',
                            labels: {
                                boxWidth: 18,
                                padding: 12
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const total = counts.reduce((a, b) => a + b, 0) || 1;
                                    const val = context.parsed;
                                    const pct = ((val / total) * 100).toFixed(1);
                                    return `${context.label}: ${val} (${pct}%)`;
                                }
                            }
                        },
                        title: { display: false }
                    },
                    layout: {
                        padding: { top: 0, right: 10, bottom: 0, left: 0 }
                    }
                }
            });
        },
        error: function (xhr) {
            blockUI.release();
            Swal.fire({
                text: xhr.responseJSON?.message || "Gagal mengambil data Scoring.",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "OK",
                customClass: { confirmButton: "btn btn-primary" },
            });
        },
    });
}

// chart Confidence (new)
let chartConfidenceInstance;

function getChartConfidence() {
    const filterKelasConfidence = $("#filter-kelas-confidence").val() || "";
    $.ajax({
        url: APP_URL + "dashboard/chart-confidence",
        data: { kelas_id: filterKelasConfidence },
        type: "GET",
        success: function (res) {
            const ctx = document.getElementById('chart-confidence');
            if (!ctx) return;

            // Ambil sumber data (API bisa kembalikan {data: ...} atau langsung)
            const source = res?.data !== undefined ? res.data : res;

            if (chartConfidenceInstance) {
                chartConfidenceInstance.destroy();
            }

            let chartConfig = {};

            // Jika hasil array => banyak kelas (grouped bar: x-axis = kelas, 4 dataset kategori)
            if (Array.isArray(source)) {
                const kelasLabels = source.map(r => r.kelas_name || r.kelas_id);

                const dataBenarYakin        = source.map(r => Number(r.benar_yakin) || 0);
                const dataBenarTidakYakin   = source.map(r => Number(r.benar_tidak_yakin) || 0);
                const dataSalahYakin        = source.map(r => Number(r.salah_yakin) || 0);
                const dataSalahTidakYakin   = source.map(r => Number(r.salah_tidak_yakin) || 0);

                const datasets = [
                    {
                        label: 'Benar Yakin',
                        data: dataBenarYakin,
                        backgroundColor: '#30c47fff'
                    },
                    {
                        label: 'Benar Tidak Yakin',
                        data: dataBenarTidakYakin,
                        backgroundColor: '#4088f3ff'
                    },
                    {
                        label: 'Salah Yakin',
                        data: dataSalahYakin,
                        backgroundColor: '#fd4154ff'
                    },
                    {
                        label: 'Salah Tidak Yakin',
                        data: dataSalahTidakYakin,
                        backgroundColor: '#f7c737ff'
                    }
                ];

                chartConfig = {
                    type: 'bar',
                    data: {
                        labels: kelasLabels,
                        datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { position: 'top' },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        const idx = context.dataIndex;
                                        // Jumlah total semua kategori untuk kelas tersebut
                                        const totalForClass = context.chart.data.datasets
                                            .reduce((sum, ds) => sum + (Number(ds.data[idx]) || 0), 0) || 1;
                                        const val = context.parsed.y;
                                        const pct = ((val / totalForClass) * 100).toFixed(1);
                                        return `${context.dataset.label}: ${val} (${pct}%)`;
                                    }
                                }
                            },
                            title: { display: false }
                        },
                        scales: {
                            x: {
                                ticks: { autoSkip: false }
                            },
                            y: {
                                beginAtZero: true,
                                precision: 0
                            }
                        }
                    }
                };
            } else {
                // Satu kelas => tampilkan 4 kategori sebagai label di sumbu X
                const obj = source || {};
                const labels = [
                    'Benar Yakin',
                    'Benar Tidak Yakin',
                    'Salah Yakin',
                    'Salah Tidak Yakin'
                ];
                const counts = [
                    Number(obj.benar_yakin) || 0,
                    Number(obj.benar_tidak_yakin) || 0,
                    Number(obj.salah_yakin) || 0,
                    Number(obj.salah_tidak_yakin) || 0
                ];
                const colors = ['#198754', '#0d6efd', '#dc3545', '#ffc107'];

                chartConfig = {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Jumlah',
                            data: counts,
                            backgroundColor: colors
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        const total = counts.reduce((a, b) => a + b, 0) || 1;
                                        const val = context.parsed.y;
                                        const pct = ((val / total) * 100).toFixed(1);
                                        return `${context.label}: ${val} (${pct}%)`;
                                    }
                                }
                            },
                            title: { display: false }
                        },
                        scales: {
                            x: { ticks: { autoSkip: false } },
                            y: { beginAtZero: true, precision: 0 }
                        }
                    }
                };
            }

            chartConfidenceInstance = new Chart(ctx, chartConfig);
        },
        error: function (xhr) {
            blockUI.release();
            Swal.fire({
                text: xhr.responseJSON?.message || "Gagal mengambil data Scoring.",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "OK",
                customClass: { confirmButton: "btn btn-primary" },
            });
        },
    });
}


// chart aktivitas ujian
let chartAktivitasUjianInstance;

function getChartAktivitasUjian() {
    if (chartAktivitasUjianInstance) {
        chartAktivitasUjianInstance.destroy();
    }

    const filterTahunAktivitas = $("#filter-tahun-aktivitas").val() || "";
    const filterBulanAktivitas = $("#filter-bulan-aktivitas").val() || "";
    const filterKelasAktivitas = $("#filter-kelas-aktivitas").val() || "";

    $.ajax({
        url: APP_URL + "dashboard/chart-aktivitas-ujian",
        data: { tahun: filterTahunAktivitas, bulan: filterBulanAktivitas, kelas_id: filterKelasAktivitas },
        type: "GET",
        success: function (res) {
            const ctx = document.getElementById('chart-aktivitas-ujian');
            if (!ctx) return;

            const source = res?.data || res || [];
            const rows = Array.isArray(source) ? source : [];

            const labels = rows.map(r => {
                if (!r.tanggal) return "";
                const parts = r.tanggal.split("-");
                return parts[2];
            });

            const values = rows.map(r => Number(r.total) || 0);

            if (!labels.length) {
                for (let i = 1; i <= 30; i++) {
                    labels.push(String(i).padStart(2, "0"));
                    values.push(0);
                }
            }

            let gradient = null;
            if (ctx.getContext) {
                const g = ctx.getContext('2d');
                gradient = g.createLinearGradient(0, 0, 0, 200);
                gradient.addColorStop(0, 'rgba(68, 255, 168, 0.35)');
                gradient.addColorStop(1, 'rgba(25,135,84,0)');
            }

            chartAktivitasUjianInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Aktivitas Ujian (per Hari)',
                        data: values,
                        fill: true,
                        backgroundColor: gradient || 'rgba(38, 255, 154, 0.15)',
                        borderColor: '#2bf094ff',
                        borderWidth: 2,
                        tension: 0.25,
                        pointRadius: 0,          // tanpa dot
                        pointHoverRadius: 0,     // tanpa dot saat hover
                        pointHitRadius: 6        // masih bisa tooltip
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: true },
                        tooltip: {
                            callbacks: {
                                title: items => {
                                    if (!items.length) return "";
                                    return "Hari " + items[0].label;
                                },
                                label: ctx => `Total: ${ctx.parsed.y}`
                            }
                        },
                        title: { display: false }
                    },
                    scales: {
                        x: {
                            title: { display: true, text: 'Hari' },
                            grid: { display: false, drawBorder: false }, // tanpa grid
                            ticks: {
                                maxRotation: 0,
                                autoSkip: true
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Jumlah' },
                            ticks: { precision: 0 },
                            grid: { display: false, drawBorder: false } // tanpa grid
                        }
                    }
                }
            });
        },
        error: function (xhr) {
            blockUI.release();
            Swal.fire({
                text: xhr.responseJSON?.message || "Gagal mengambil data Aktivitas Ujian.",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "OK",
                customClass: { confirmButton: "btn btn-primary" },
            });
        },
    });
}
