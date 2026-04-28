var target = document.querySelector('#kt_app_body');
var blockUI = new KTBlockUI(target);
var APP_URL = window.APP_URL || '/';

var adaptiveTable = null;
var adaptiveHistory = [];

function renderBadge(text, variant) {
    var label = (text ?? '-').toString();
    var styleClass = 'adaptive-pill--soft';

    if (variant === 'blue') {
        styleClass = 'adaptive-pill--blue';
    } else if (variant === 'purple') {
        styleClass = 'adaptive-pill--purple';
    }

    return '<span class="adaptive-pill ' + styleClass + '">' + label + '</span>';
}

function renderLevelBadge(levelName) {
    return renderBadge(levelName, 'blue');
}

function renderLabelBadge(label) {
    return renderBadge(label, 'purple');
}

function escapeHtml(text) {
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function renderAdaptiveMessageText(text) {
    var safeText = escapeHtml(text ?? '-');

    // Convert markdown bold (**) into real bold HTML while keeping escaped content safe.
    safeText = safeText.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');

    return safeText.replace(/\n/g, '<br>');
}

function initTable() {
    if (adaptiveTable) {
        adaptiveTable.ajax.reload();
        return;
    }

    adaptiveTable = $('#table-log-chatbot-adaptive').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: APP_URL + 'log-chatbot-adaptive/table',
            type: 'POST',
            data: function (d) {
                d._token = $('meta[name="csrf-token"]').attr('content');
                var kelasValue = ($('#filter-kelas').val() || '').toString().trim();
                var searchValue = ($('#search-mahasiswa').val() || '').toString().trim();
                d.kelas = kelasValue;
                d.search_custom = searchValue;
            },
        },
        order: [],
        columns: [
            { data: null, className: 'text-center', orderable: false, searchable: false },
            { data: 'nim', className: 'text-start', orderable: false, },
            { data: 'name', className: 'text-start', orderable: false, },
            { data: 'kelas_name', className: 'text-center', orderable: false, },
            { data: 'waktu', className: 'text-center', orderable: false, },
            { data: 'jumlah_langkah', className: 'text-center', orderable: false, },
            { data: 'durasi', className: 'text-center', orderable: false, },
            { data: 'id', className: 'text-center', orderable: false, searchable: false },
        ],
        columnDefs: [
            {
                targets: 0,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
            },
            { targets: 1, render: function (data, type, row) { return row.nim ?? '-'; } },
            { targets: 2, render: function (data, type, row) { return row.name ?? '-'; } },
            { targets: 3, render: function (data, type, row) { return row.kelas_name ?? '-'; } },
            { targets: 4, render: function (data, type, row) { return row.waktu ?? '-'; } },
            {
                targets: 5,
                render: function (data, type, row) {
                    var value = row.jumlah_langkah ?? 0;
                    var variant = value > 0 ? 'blue' : 'soft';
                    return renderBadge(value, variant);
                }
            },
            {
                targets: 6,
                render: function (data, type, row) {
                    var value = row.durasi ?? '-';
                    var variant = value !== '-' ? 'purple' : 'soft';
                    return renderBadge(value, variant);
                }
            },
            {
                targets: 7,
                render: function (data, type, row) {
                    return '<button type="button" class="btn btn-sm btn-outline btn-outline-primary d-flex align-items-center gap-1 p-2 mx-auto" onclick="showDetail(\'' + row.id + '\')">'
                        + '<i class="ki-outline ki-eye"></i><span>Detail</span></button>';
                },
            },
        ],
        createdRow: function (row, data) {
            $(row).attr('id', data.id || data[0]);
        },
    });
}

function showDetail(studentId) {
    blockUI.block();

    $.ajax({
        url: APP_URL + 'log-chatbot-adaptive/detail/' + studentId,
        type: 'GET',
        success: function (res) {
            if (!res.success) {
                blockUI.release();
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Belum ada Data Chatbot Adaptive.',
                });
                return;
            }

            var data = res.data;
            adaptiveHistory = data.history || [];

            $('#detail-nim').text(data.nim ?? '-');
            $('#detail-nama').text(data.name ?? '-');
            $('#detail-kelas').text(data.kelas_name ?? '-');
            $('#detail-total-akses').text(data.total_akses_adaptive ?? 0);
            $('#detail-total-pesan').text(data.total_messages ?? 0);

            var tbody = '';
            if (adaptiveHistory.length > 0) {
                adaptiveHistory.forEach(function (item, index) {
                    tbody += '<tr>'
                        + '<td class="text-center">' + (index + 1) + '</td>'
                        + '<td class="text-center">' + renderLevelBadge(item.level_name) + '</td>'
                        + '<td class="text-start">' + (item.soal_title ?? '-') + '</td>'
                        + '<td class="text-center">' + (item.waktu_akses ?? '-') + '</td>'
                        + '<td class="text-center">' + renderBadge((item.durasi ?? '-'), (item.durasi ?? '-') !== '-' ? 'purple' : 'soft') + '</td>'
                        + '<td class="text-center">' + renderBadge((item.jumlah_langkah ?? 0), (item.jumlah_langkah ?? 0) > 0 ? 'blue' : 'soft') + '</td>'
                        + '<td class="text-center">' + renderLabelBadge(item.labeling ?? '-') + '</td>'
                        + '<td class="text-center">' + renderBadge((item.total_messages ?? 0), (item.total_messages ?? 0) > 0 ? 'purple' : 'soft') + '</td>'
                        + '<td class="text-center">'
                        + '<button type="button" class="btn btn-sm btn-light-primary" onclick="showMessages(' + index + ')">Lihat</button>'
                        + '</td>'
                        + '</tr>';
                });
            } else {
                tbody = '<tr><td colspan="9" class="text-center text-muted">Belum ada riwayat akses chatbot adaptive.</td></tr>';
            }

            $('#history-table-body').html(tbody);

            var modal = new bootstrap.Modal(document.getElementById('modal-detail-adaptive'));
            modal.show();
            blockUI.release();
        },
        error: function () {
            blockUI.release();
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi kesalahan saat memuat data',
            });
        }
    });
}

function showMessages(historyIndex) {
    var history = adaptiveHistory[historyIndex];
    if (!history) {
        return;
    }

    var messagesHtml = '<div class="alert alert-info mb-4">'
        + '<strong>Level:</strong> ' + (history.level_name ?? '-') + ' | '
        + '<strong>Soal:</strong> ' + (history.soal_title ?? '-') + ' | '
        + '<strong>Waktu Total Pengerjaan:</strong> ' + (history.waktu_akses ?? '-')
        + '</div>';

    if (!history.messages || history.messages.length === 0) {
        messagesHtml += '<div class="alert alert-warning">Tidak ada pesan dalam sesi ini.</div>';
    } else {
        // Create chat container for message bubbles
        messagesHtml += '<div class="adaptive-chat-container">';

        history.messages.forEach(function (msg, idx) {
            var waktu = msg.waktu ?? '-';
            var mahasiswaName = msg.mahasiswa_name || 'Mahasiswa';
            var botName = msg.bot_name || 'PseudoLearn Chatbot AI';
            var studentMessage = msg.mahasiswa_message || '';
            var botResponse = msg.bot_response || msg.respons || '';
            var systemNote = '';

            if (!studentMessage && msg.pesan && msg.pesan !== '-') {
                systemNote = msg.pesan;
            }

            // Render system note if exists
            if (systemNote) {
                messagesHtml += '<div class="adaptive-message-group">'
                    + '<div class="adaptive-system-note">'
                    + '<strong>Catatan Sistem:</strong> ' + renderAdaptiveMessageText(systemNote)
                    + '</div>'
                    + '</div>';
            }

            // Render student message bubble (right aligned, blue)
            if (studentMessage) {
                messagesHtml += '<div class="adaptive-message adaptive-message-user">'
                    + '<div class="adaptive-message-bubble adaptive-bubble-user">'
                    + '<div class="adaptive-message-name">' + escapeHtml(mahasiswaName) + '</div>'
                    + '<div class="adaptive-message-text">' + renderAdaptiveMessageText(studentMessage) + '</div>'
                    + '<div class="adaptive-message-time">' + escapeHtml(waktu) + '</div>'
                    + '</div>'
                    + '</div>';
            }

            // Render bot response bubble (left aligned, white)
            if (botResponse) {
                messagesHtml += '<div class="adaptive-message adaptive-message-bot">'
                    + '<div class="adaptive-message-bubble adaptive-bubble-bot">'
                    + '<div class="adaptive-message-name">' + escapeHtml(botName) + '</div>'
                    + '<div class="adaptive-message-text">' + renderAdaptiveMessageText(botResponse) + '</div>'
                    + '<div class="adaptive-message-time">' + escapeHtml(waktu) + '</div>'
                    + '</div>'
                    + '</div>';
            }
        });

        messagesHtml += '</div>'; // close chat container
    }

    $('#messages-container').html(messagesHtml);
    var modal = new bootstrap.Modal(document.getElementById('modal-adaptive-messages'));
    modal.show();
}

function exportExcel() {
    var kelasValue = ($('#filter-kelas').val() || '').toString().trim();
    var searchValue = ($('#search-mahasiswa').val() || '').toString().trim();

    var form = $('<form>').attr({
        method: 'POST',
        action: APP_URL + 'log-chatbot-adaptive/export',
    });

    form.append($('<input>').attr({ type: 'hidden', name: '_token', value: $('meta[name="csrf-token"]').attr('content') }));
    form.append($('<input>').attr({ type: 'hidden', name: 'kelas', value: kelasValue }));
    form.append($('<input>').attr({ type: 'hidden', name: 'search', value: searchValue }));

    $('body').append(form);
    form.submit();
    form.remove();
}

$(document).ready(function () {
    blockUI.block();
    initTable();
    blockUI.release();

    $('#search-mahasiswa').on('keyup', function () {
        if (adaptiveTable) {
            adaptiveTable.ajax.reload();
        }
    });

    $('#filter-kelas').on('select2:select select2:clear change', function () {
        if (adaptiveTable) {
            adaptiveTable.ajax.reload();
        }
    });

    $('#modal-detail-adaptive').on('hidden.bs.modal', function () {
        $('body').removeClass('modal-open');
        $('body').css('overflow', '');
        $('body').css('padding-right', '');
        $('.modal-backdrop').remove();
    });

    $('#modal-adaptive-messages').on('hidden.bs.modal', function () {
        $('body').removeClass('modal-open');
        $('body').css('overflow', '');
        $('body').css('padding-right', '');
        $('.modal-backdrop').remove();
    });
});
