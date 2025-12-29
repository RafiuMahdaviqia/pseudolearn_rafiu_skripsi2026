var target = document.querySelector("#kt_app_body");
var blockUI = new KTBlockUI(target);
var APP_URL = window.APP_URL || "/";

$(() => {
    blockUI.block();
    calculateAvgSkor();
    blockUI.release();
});

function calculateAvgSkor() {
    let levelId = $("#level_id").val();

    $.ajax({
        url: APP_URL + "quiz/calculateAvgSkor",
        type: "POST",
        data: { 
            _token: $('meta[name="csrf-token"]').attr('content'),
            level_id: levelId
        },
        success: function(response) {
            if (typeof response?.avgSkor !== 'undefined') {
                const avg = Number(response.avgSkor);
                // Format: 2 desimal, hilangkan .00
                const formatted = isNaN(avg) ? '0' : avg.toFixed(2).replace(/\.00$/, '');
                $('#total-algo-poin').text(formatted);

                const msg = String(response.message || '').toLowerCase();
                const isUpdated = msg.includes('diperbarui') || msg.includes('berhasil disimpan');

                // Jika ada update nilai → refresh konten question list
                if (isUpdated) {
                    blockUI.block();
                    $('#question-list-container').load(window.location.href + ' #question-list-container > *', function () {
                        blockUI.release();
                    });
                }
            }
        },
        error: function(xhr, status, error) {
            Swal.fire("Error", "Terjadi kesalahan saat menghitung rata-rata skor.", "error");
        }
    });
}