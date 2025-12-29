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
    // Hanya validasi field utama, tanpa validasi form repeater
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
                judul: {
                    validators: {
                        notEmpty: {
                            message: 'Judul soal tidak boleh kosong'
                        }
                    }
                },
                soal: {
                    validators: {
                        notEmpty: {
                            message: 'Soal tidak boleh kosong'
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
        // Sinkronkan CKEditor ke textarea
        if (soalEditor) {
            document.querySelector('#soal').value = soalEditor.getData();
        }

        if (validator) {
            validator.validate().then(function (status) {
                if (status === 'Valid') {
                    saveSoal();
                }
            });
        }
    });
};

saveSoal = () => {
    const form = document.getElementById('form-soal');
    const formData = new FormData(form);

    // Sinkronkan CKEditor ke textarea sebelum ambil FormData
    if (soalEditor) {
        formData.set('soal', soalEditor.getData());
    }

    // Reset tipe_data dan algoritma agar tidak double
    formData.delete('tipe_data[]');
    formData.delete('variabel[]');
    formData.delete('langkah[]');
    formData.delete('clue[]');
    formData.delete('konversi_tipe_data[]');
    formData.delete('konversi_algoritma[]');

    // Ambil semua row tipe data
    let tipeDataArr = [];
    $('#container-tipe-data .tipe-data-row').each(function () {
        tipeDataArr.push({
            variabel: $(this).find('input[name="variabel[]"]').val(),
            tipe_data: $(this).find('input[name="tipe_data[]"]').val(),
            konversi: $(this).find('input[name="konversi_tipe_data[]"]').is(':checked') ? 1 : 0
        });
    });
    formData.append('kunci_tipe_data', JSON.stringify(tipeDataArr));

    // Ambil semua row algoritma
    let algoritmaArr = [];
    $('#container-algoritma .algoritma-row').each(function () {
        algoritmaArr.push({
            langkah: $(this).find('input[name="langkah[]"]').val(),
            clue: $(this).find('input[name="clue[]"]').is(':checked') ? 1 : 0,
            konversi: $(this).find('input[name="konversi_algoritma[]"]').is(':checked') ? 1 : 0
        });
    });
    formData.append('kunci_algoritma', JSON.stringify(algoritmaArr));


    Swal.fire({
        title: 'Konfirmasi',
        text: "Apakah Anda yakin ingin menyimpan soal ini?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, simpan',
        cancelButtonText: 'Tidak'
    }).then((result) => {
        if (result.isConfirmed) {
            blockUI.block();
            $.ajax({
                type: 'POST',
                url: APP_URL + 'soal/store',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    blockUI.release();
                    Swal.fire({
                        text: response.message,
                        icon: "success",
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        },
                    }).then(() => {
                        window.location.href = APP_URL + 'soal';
                    });
                },
                error: function (xhr) {
                    blockUI.release();
                    Swal.fire({
                        text: xhr.responseJSON?.message || "Terjadi kesalahan sistem.",
                        icon: "error",
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        },
                    });
                }
            });
        }
    });
};
