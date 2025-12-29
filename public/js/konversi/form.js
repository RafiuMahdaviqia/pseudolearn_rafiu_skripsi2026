var target = document.querySelector("#kt_app_body");
var blockUI = new KTBlockUI(target);
var APP_URL = window.APP_URL || "/";

$(() => {
    blockUI.block();
    // initFormRepeater();
    initFormValidation();

    blockUI.release();
});

var validator;

initFormValidation = () => {
    let submitButton = document.getElementById('submit-form-soal');
    validator = FormValidation.formValidation(
        document.getElementById('form-soal'),
        {
            fields: {
                level_id: {
                    validators: {
                        notEmpty: {
                            message: 'Level harus dipilih'
                        }
                    }
                },
                soal_id: {
                    validators: {
                        notEmpty: {
                            message: 'Soal harus dipilih'
                        }
                    }
                },
                bobot: {
                    validators: {
                        notEmpty: {
                            message: 'Bobot wajib diisi'
                        },
                        regexp: {
                            regexp: /^[0-9]+$/,
                            message: 'Bobot harus angka'
                        }
                    }
                },
                output: {
                    validators: {
                        notEmpty: {
                            message: 'Output Harus terisi'
                        }
                    }
                }
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap: new FormValidation.plugins.Bootstrap5({
                    rowSelector: ".fv-row",
                    eleInvalidClass: "",
                    eleValidClass: "",
                }),
            },
        }
    );

    submitButton.addEventListener('click', function (e) {
        e.preventDefault();
        if (soalEditor) {
            document.querySelector('#soal').value = soalEditor.getData();
        }
        if (validator) {
            validator.validate().then(function (status) {
                if (status === 'Valid') {
                    if($('#id_konversi').val() === '') {
                        saveKonversi();
                    } else {
                        updateKonversi();
                    }
                }
            });
        }
    });
};

saveKonversi = () => {
    if($('#output').val().trim() === '') {
        Swal.fire({
            icon: 'warning',
            text: 'Output Harus dijalankan',
            confirmButtonText: 'OK',
            customClass: { confirmButton: 'btn btn-primary' }
        });
        return;
    }

    // Ambil semua input jawaban (kolom kanan)
    const inputs = $('#col-input').find('input[name="jawaban[]"]');
    let filled = 0;
    inputs.each(function () {
        if ($(this).val().trim() !== '') filled++;
    });

    if (inputs.length > 0 && filled === 0) {
        Swal.fire({
            icon: 'warning',
            text: 'Mohon isi minimal satu jawaban.',
            confirmButtonText: 'OK',
            customClass: { confirmButton: 'btn btn-primary' }
        });
        return;
    }

    // Siapkan FormData
    const form = document.getElementById('form-soal');
    const formData = new FormData(form);

    // (Opsional) kirim juga total filled
    formData.append('jawaban_filled_count', filled);

    Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah Anda yakin ingin menyimpan konversi ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, simpan',
        cancelButtonText: 'Batal',
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-light'
        }
    }).then(result => {
        if (!result.isConfirmed) return;

        blockUI.block();
        $.ajax({
            type: 'POST',
            url: APP_URL + 'konversi/store',
            data: formData,
            processData: false,
            contentType: false,
            success: function () {
                blockUI.release();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Konversi berhasil disimpan.',
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'btn btn-primary' }
                }).then(() => {
                    window.location.href = APP_URL + 'konversi';
                });
            },
            error: function (xhr) {
                blockUI.release();
                Swal.fire({
                    icon: 'error',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan sistem.',
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
            }
        });
    });
};

runKonversi = () => {
    const levelId = $('#level_id').val();
    const soalId  = $('#soal_id').val();

    // Cek level & soal
    if (!levelId || !soalId) {
        Swal.fire({
            icon: 'warning',
            text: 'Pilih soal terlebih dahulu.',
            confirmButtonText: 'OK',
            customClass: { confirmButton: 'btn btn-primary' }
        });
        return;
    }

    // Ambil baris kode (jawaban) yang terisi
    const filledCodes = [];
    const allRows = [];
    $('#col-input .konversi-row').each(function(i){
        const val = $(this).find('input[name="jawaban[]"]').val() || '';
        const tipe = $(this).find('input[name="jawaban_tipe[]"]').val() || '';
        allRows.push({ tipe, value: val });
        if (val.trim() !== '') {
            filledCodes.push({ tipe, value: val.trim() });
        }
    });

    if (allRows.length === 0) {
        Swal.fire({
            icon: 'warning',
            text: 'Belum ada baris konversi. Tambah baris terlebih dahulu.',
            confirmButtonText: 'OK',
            customClass: { confirmButton: 'btn btn-primary' }
        });
        return;
    }

    if (filledCodes.length === 0) {
        Swal.fire({
            icon: 'warning',
            text: 'Isi minimal satu baris konversi terlebih dahulu.',
            confirmButtonText: 'OK',
            customClass: { confirmButton: 'btn btn-primary' }
        });
        return;
    }

    // Siapkan payload untuk eksekusi (gunakan hanya yang terisi atau semua sesuai kebutuhan)
    const payload = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        level_id: levelId,
        soal_id: soalId,
        codes: filledCodes           // atau ganti ke allRows jika backend butuh lengkap
    };

    blockUI.block();
    $.ajax({
        type: 'POST',
        url: APP_URL + 'konversi/runJava', // ganti ke route eksekusi sebenarnya
        data: payload,
        success: function (res) {
            blockUI.release();
            // Asumsikan backend return { output: '...' }
            const out = res.output || 'Tidak ada output.';
            $('#output').val(out);
            Swal.fire({
                icon: 'success',
                title: 'Berhasil Dijalankan',
                html: `<pre style="white-space:pre-wrap;">${out}</pre>`,
                confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn btn-primary' }
            });
        },
        error: function (xhr) {
            blockUI.release();
            Swal.fire({
                icon: 'error',
                text: xhr.responseJSON?.message || 'Gagal menjalankan konversi.',
                confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn btn-primary' }
            });
        }
    });
};

updateKonversi = () => {
    const id = $('#id_konversi').val();
    const form = document.getElementById('form-soal');
    const formData = new FormData(form);

    if (!id) {
        Swal.fire({
            icon: 'warning',
            text: 'ID konversi tidak ditemukan.',
            confirmButtonText: 'OK',
            customClass: { confirmButton: 'btn btn-primary' }
        });
        return;
    }

    Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah Anda yakin ingin memperbarui konversi ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, perbarui',
        cancelButtonText: 'Batal',
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-light'
        }
    }).then(result => {
        if (!result.isConfirmed) return;

        blockUI.block();
        $.ajax({
            type: 'POST',
            url: APP_URL + 'konversi/update',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                blockUI.release();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Konversi berhasil diperbarui.',
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'btn btn-primary' }
                }).then(() => {
                    window.location.href = APP_URL + 'konversi';
                });
            },
            error: function (xhr) {
                blockUI.release();
                Swal.fire({
                    icon: 'error',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan sistem.',
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
            }
        });
    });
};
