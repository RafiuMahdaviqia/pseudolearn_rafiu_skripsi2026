var target = document.querySelector("#kt_app_body");
var blockUI = new KTBlockUI(target);
var APP_URL = window.APP_URL || "/";
var dataTable = null;
var currentDetailMahasiswa = null;

function escapeHtml(value) {
    return $('<div>').text(value ?? '').html();
}

function nl2brSafe(value) {
    return escapeHtml(value ?? '').replace(/\n/g, '<br>');
}

function isSystemText(text) {
    const normalized = String(text ?? '').toLowerCase();
    return normalized.includes('catatan sistem') || normalized.includes('[adaptive') || normalized.includes('[system');
}

function showDetailLogPanel() {
    $('#detail-log-panel').removeClass('detail-panel-hidden');
    $('#detail-pesan-panel').addClass('detail-panel-hidden');
}

function showDetailPesanPanel() {
    $('#detail-log-panel').addClass('detail-panel-hidden');
    $('#detail-pesan-panel').removeClass('detail-panel-hidden');
}

function resetDetailPanels() {
    showDetailLogPanel();
    $('#detail-pesan-access-id').text('-');
    $('#detail-pesan-waktu-akses').text('-');
    $('#detail-pesan-durasi').text('-');
    $('#detail-pesan-total').text('0');
    $('#detail-chat-context').addClass('detail-panel-hidden').empty();
    $('#detail-pesan-chatbot-body').html('<div class="detail-chat-empty">Pilih sesi untuk melihat detail pesan.</div>');
}

$(() => {
    blockUI.block();
    initTable();
    blockUI.release();
});

initTable = () => {
    return new Promise((resolve, reject) => {
        if (dataTable) {
            dataTable.ajax.reload();
            resolve(true);
            return;
        }

        dataTable = $("#table-log-data-chatbot").DataTable({
            ajax: {
                url: APP_URL + "log-chatbot/table",
                type: "POST",
                data: function (d) {
                    const keyword = ($('#search-mahasiswa').val() || '').trim();
                    d._token = $('meta[name="csrf-token"]').attr("content");
                    d.kelas  = $('#filter-kelas').val() || null;
                    d.q = keyword;
                    d.searchTerm = keyword;
                    d.search = d.search || {};
                    d.search.value = keyword;
                },
            },
            processing: true,
            serverSide: true,
            destroy: false,
            responsive: false,
            pageLength: 20,
            lengthMenu: [[20], [20]],
            lengthChange: false,
            order: [[1, "asc"]],
            columns: [
                { data: null, className: "text-center", orderable: false, searchable: false },
                { data: "nim", orderable: false, searchable: true, className: "text-start" },
                { data: "nama_mahasiswa", orderable: false, searchable: true, className: "text-start" },
                { data: "kelas_name", orderable: false, searchable: true, className: "text-center" },
                { data: "total_waktu", orderable: false, searchable: false, className: "text-start" },
                { data: "total_langkah", orderable: false, searchable: false, className: "text-center" },
                { data: "total_durasi", orderable: false, searchable: false, className: "text-center" },
                { data: "id", orderable: false, searchable: false, className: "text-center" },
            ],
            columnDefs: [
                { targets: 0, render: (data, type, row, meta) => meta.row + 1 },
                {
                    targets: 1,
                    render: (data, type, row) => escapeHtml(row.nim ?? '-'),
                },
                {
                    targets: 2,
                    render: (data, type, row) => escapeHtml(row.nama_mahasiswa ?? '-'),
                },
                {
                    targets: 3,
                    render: (data, type, row) => escapeHtml(row.kelas_name ?? '-'),
                },
                {
                    targets: 4,
                    render: (data, type, row) => escapeHtml(row.total_waktu ?? '-'),
                },
                {
                    targets: 5,
                    render: (data, type, row) => {
                        const langkah = Number(row.total_langkah ?? 0);
                        const extraClass = langkah > 0 ? '' : ' list-badge-empty';
                        return `<span class="badge list-badge-step${extraClass}">${langkah}</span>`;
                    },
                },
                {
                    targets: 6,
                    render: (data, type, row) => {
                        const value = String(row.total_durasi ?? '-').trim();
                        if (!value || value === '-') {
                            return '<span class="badge list-badge-duration list-badge-empty">-</span>';
                        }

                        return `<span class="badge list-badge-duration">${escapeHtml(value)}</span>`;
                    },
                },
                {
                    targets: 7,
                    render: (data, type, row) => `
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-sm btn-outline btn-outline-primary d-flex align-items-center gap-1 p-2" onclick="showDetail('${row.id}')">
                                <i class="ki-outline ki-eye"></i>
                                <span>Detail</span>
                            </button>
                        </div>
                    `,
                },
            ],
            createdRow: (row, data) => $(row).attr("id", data.id || data[0]),
            initComplete: function (settings, json) {
                var debounceTimer;
                $("#search-mahasiswa").on("input", function () {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        const keyword = ($('#search-mahasiswa').val() || '').trim();
                        dataTable.search(keyword).draw();
                    }, 300);
                });
                resolve(true);
            },
        });
    });
};

$('#filter-kelas').on('change', function() {
   initTable();
});

function showDetail(mahasiswaId) {
    blockUI.block();

    $.ajax({
        url: APP_URL + "log-chatbot/detail/" + mahasiswaId,
        type: "GET",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        success: function(res) {
            if (res.success) {
                let data = res.data;
                currentDetailMahasiswa = data;

                $('#detail-nim').text(data.nim ?? '-');
                $('#detail-nama').text(data.name ?? '-');
                $('#detail-kelas').text(data.kelas_name ?? '-');
                $('#detail-waktu-akses').text(data.waktu_akses ?? '-');
                $('#detail-total-akses').text(
                    data.total_akses ?? (Array.isArray(data.history) ? data.history.length : 0)
                );
                $('#detail-total-pesan').text(data.total_pesan ?? 0);
                $('#detail-pesan-nim').text(data.nim ?? '-');

                let tbody = '';
                if (data.history && data.history.length > 0) {
                    data.history.forEach((item, index) => {
                        tbody += `
                            <tr>
                                <td class="text-center">${index + 1}</td>
                                <td class="text-center">${escapeHtml(item.level ?? 'Tidak tercatat')}</td>
                                <td class="text-center">${escapeHtml(item.soal ?? 'Tidak tercatat')}</td>
                                <td class="text-center">${escapeHtml(item.total_waktu_pengerjaan ?? '-')}</td>
                                <td class="text-center">${escapeHtml(item.durasi_popup ?? '-')}</td>
                                <td class="text-center">${escapeHtml(item.jumlah_langkah ?? 0)}</td>
                                <td class="text-center">${escapeHtml(item.labelling ?? 'Tidak tercatat')}</td>
                                <td class="text-center">${escapeHtml(item.total_pesan ?? 0)}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-primary" onclick="showDetailPesan('${item.detail_pesan_access_id}')">
                                        Lihat
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    tbody = `
                        <tr>
                            <td colspan="9" class="text-center text-muted">Belum ada riwayat akses chatbot untuk filter ini</td>
                        </tr>
                    `;
                }

                $('#detail-chatbot-body').html(tbody);
                resetDetailPanels();
                $('#modal-detail-chatbot').modal('show');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: res.message ?? 'Terjadi kesalahan saat memuat data',
                });
            }
            blockUI.release();
        },
        error: function(xhr) {
            blockUI.release();
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi kesalahan saat memuat data',
            });
        },
    });
}

function renderChatHistory(history) {
    const $container = $('#detail-pesan-chatbot-body');
    const $context = $('#detail-chat-context');

    if (!Array.isArray(history) || history.length === 0) {
        $context.addClass('detail-panel-hidden').empty();
        $container.html('<div class="detail-chat-empty">Belum ada percakapan pada sesi ini.</div>');
        return;
    }

    let html = '';
    let contextHtml = '';

    history.forEach((item) => {
        const waktu = escapeHtml(item.waktu ?? '-');
        const pesan = String(item.pesan ?? '').trim();
        const respons = String(item.respons ?? '').trim();

        if (pesan && pesan !== '-') {
            html += `
                <div class="detail-chat-item user">
                    <div class="detail-chat-bubble user">${nl2brSafe(pesan)}</div>
                    <div class="detail-chat-time">${waktu}</div>
                </div>
            `;
        }

        if (respons && respons !== '-') {
            const systemClass = isSystemText(respons) ? ' system' : '';

            if (!contextHtml && isSystemText(respons)) {
                contextHtml = `
                    <div>
                        <span>Catatan Sistem</span>
                        <span class="detail-chat-context-time">${waktu}</span>
                    </div>
                    <div class="mt-1">${nl2brSafe(respons)}</div>
                `;
            }

            html += `
                <div class="detail-chat-item bot">
                    <div class="detail-chat-bubble bot${systemClass}">${nl2brSafe(respons)}</div>
                    <div class="detail-chat-time">${waktu}</div>
                </div>
            `;
        }
    });

    if (!html) {
        $container.html('<div class="detail-chat-empty">Belum ada percakapan pada sesi ini.</div>');
    } else {
        $container.html(html);
        const shell = document.getElementById('detail-pesan-chatbot-body');
        if (shell) {
            shell.scrollTop = shell.scrollHeight;
        }
    }

    if (contextHtml) {
        $context.removeClass('detail-panel-hidden').html(contextHtml);
    } else {
        $context.addClass('detail-panel-hidden').empty();
    }
}

function showDetailPesan(accessId) {
    blockUI.block();

    $.ajax({
        url: APP_URL + "log-chatbot/detail-pesan/" + accessId,
        type: "GET",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        success: function(res) {
            if (res.success) {
                const data = res.data;

                $('#detail-pesan-access-id').text(data.access_id ?? '-');
                $('#detail-pesan-waktu-akses').text(data.waktu_akses ?? '-');
                $('#detail-pesan-durasi').text(data.durasi ?? '-');
                $('#detail-pesan-total').text(data.total_pesan ?? 0);

                renderChatHistory(data.history ?? []);
                showDetailPesanPanel();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: res.message ?? 'Terjadi kesalahan saat memuat detail pesan',
                });
            }
            blockUI.release();
        },
        error: function() {
            blockUI.release();
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi kesalahan saat memuat detail pesan',
            });
        },
    });
}

function backToDetailLog() {
    showDetailLogPanel();
}

$('#modal-detail-chatbot').on('hidden.bs.modal', function () {
    currentDetailMahasiswa = null;
    resetDetailPanels();
    $('body').removeClass('modal-open');
    $('body').css('overflow', '');
    $('body').css('padding-right', '');
    $('.modal-backdrop').remove();
});

function exportExcel() {
    let kelas = $('#filter-kelas').val();

    var form = document.createElement('form');
    form.method = 'POST';
    form.action = APP_URL + 'log-chatbot/export';
    form.style.display = 'none';

    var csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = $('meta[name="csrf-token"]').attr('content');
    form.appendChild(csrfInput);

    if (kelas) {
        var kelasInput = document.createElement('input');
        kelasInput.type = 'hidden';
        kelasInput.name = 'kelas';
        kelasInput.value = kelas;
        form.appendChild(kelasInput);
    }

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
