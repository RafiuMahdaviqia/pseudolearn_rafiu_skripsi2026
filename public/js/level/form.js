var target = document.querySelector("#kt_app_body");
var blockUI = new KTBlockUI(target);
var APP_URL = window.APP_URL || "/";

document.addEventListener('DOMContentLoaded', function () {
    blockUI.block();
    initFormValidation();
    blockUI.release();

    Dropzone.autoDiscover = false;
    window.dropzoneLevelLogo = new Dropzone("#dropzone-level-logo", {
        url: "#", // Tidak perlu URL karena tidak upload otomatis
        autoProcessQueue: false,
        uploadMultiple: false,
        maxFiles: 1,
        maxFilesize: 2, // Maksimal 2MB
        addRemoveLinks: true,
        acceptedFiles: ".png,.jpg,.jpeg",
        init: function () {
            this.on("addedfile", function(file) {
                var hiddenInput = document.getElementById('logo-level-hidden');
                if (hiddenInput) {
                    hiddenInput.value = file.name; // Simpan nama file di input tersembunyi
                }
            });
            this.on("removedfile", function(file) {
                var hiddenInput = document.getElementById('logo-level-hidden');
                if (hiddenInput) {
                    hiddenInput.value = '';
                }
            });
        }
    });
});

var validator;

function initFormValidation() {
    var submitButton = document.getElementById("submit-form-level");
    validator = FormValidation.formValidation(
        document.getElementById('form-level'),
        {
            fields: {
                nama: {
                    validators: {
                        notEmpty: {
                            message: 'Nama level tidak boleh kosong'
                        }
                    }
                },
                // feedback_tipe_data: {
                //     validators: {
                //         stringLength: {
                //             max: 500,
                //             message: 'Feedback maksimal 500 karakter'
                //         }
                //     }
                // },
                // feedback_algoritma: {
                //     validators: {
                //         stringLength: {
                //             max: 500,
                //             message: 'Feedback maksimal 500 karakter'
                //         }
                //     }
                // },
                logo_level: {
                    validators: {
                        notEmpty: {
                            message: 'Logo level tidak boleh kosong'
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
    });

    // Submit button handler
    submitButton.addEventListener("click", function (e) {
        e.preventDefault();

        if (validator) {
            validator.validate().then(function (status) {
                if (status == "Valid") {
                    if ($("#id-level").val()) {
                        update();
                    } else {
                        save();
                    }
                }
            });
        }
    });
};

save = () => {
    let form = $("#form-level")[0];
    let formData = new FormData(form);
    let nama = $("#nama").val();
    let feedbackTipeData = $("#feedback-tipe-data").val();
    let feedbackAlgoritma = $("#feedback-algoritma").val();

    formData.append("_token", $('meta[name="csrf-token"]').attr("content"));
    formData.set("nama", nama);
    formData.set("feedback_tipe_data", feedbackTipeData);
    formData.set("feedback_algoritma", feedbackAlgoritma);

    if (!nama) {
        Swal.fire({
            text: "Nama level harus diisi.",
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: "OK",
            customClass: {
                confirmButton: "btn btn-primary",
            },
        });
        return;
    }

    // Ambil file dari Dropzone dan masukkan ke FormData
    if (window.dropzoneLevelLogo && window.dropzoneLevelLogo.getAcceptedFiles().length > 0) {
        formData.set("logo_level", window.dropzoneLevelLogo.getAcceptedFiles()[0]);
    } else {
        Swal.fire({
            text: "Logo level harus diisi.",
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: "OK",
            customClass: {
                confirmButton: "btn btn-primary",
            },
        });
        return;
    }

    Swal.fire({
        title: "Simpan Level?",
        text: "Apakah Anda yakin ingin menyimpan data level ini?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Ya",
        cancelButtonText: "Tidak",
        customClass: {
            confirmButton: "btn btn-primary",
            cancelButton: "btn btn-secondary",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            blockUI.block();
            $.ajax({
                url: APP_URL + "level/store",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    blockUI.release();
                    if (response.success) {
                        Swal.fire({
                            text: "Level berhasil disimpan.",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "OK",
                            customClass: {
                                confirmButton: "btn btn-primary",
                            },
                        }).then(() => {
                            window.location.href = APP_URL + "level";
                        });
                    } else {
                        Swal.fire({
                            text:
                                response.message ||
                                "Terjadi kesalahan saat menyimpan level.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "OK",
                            customClass: {
                                confirmButton: "btn btn-primary",
                            },
                        });
                    }
                },
                error: function (xhr, status, error) {
                    blockUI.release();
                    Swal.fire({
                        text:
                            xhr.responseJSON?.message ||
                            "Terjadi kesalahan saat menyimpan level.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        },
                    });
                },
            });
        } else {
            blockUI.release();
        }
    });
};

resetForm = () => {
    $("#form-level")[0].reset();
    $("#logo-level-hidden").val('');
    if (window.dropzoneLevelLogo) {
        window.dropzoneLevelLogo.removeAllFiles(true);
    }

    if (validator) {
        validator.resetForm();
    }
}

update = () => {
    let form = $("#form-level")[0];
    let formData = new FormData(form);
    let nama = $("#nama").val();
    let feedbackTipeData = $("#feedback-tipe-data").val();
    let feedbackAlgoritma = $("#feedback-algoritma").val();

    formData.append("_token", $('meta[name="csrf-token"]').attr("content"));
    formData.set("nama", nama);
    formData.set("feedback_tipe_data", feedbackTipeData);
    formData.set("feedback_algoritma", feedbackAlgoritma);

    if (!nama) {
        Swal.fire({
            text: "Nama level harus diisi.",
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: "OK",
            customClass: {
                confirmButton: "btn btn-primary",
            },
        });
        return;
    }

    if (window.dropzoneLevelLogo && window.dropzoneLevelLogo.getAcceptedFiles().length > 0) {
        formData.set("logo_level", window.dropzoneLevelLogo.getAcceptedFiles()[0]);
    } else {
        var hiddenInput = document.getElementById('logo-level-hidden');
        if (hiddenInput && hiddenInput.value) {
            formData.set("logo_level", hiddenInput.value);
        } else {
            Swal.fire({
                text: "Logo level harus diisi.",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "OK",
                customClass: {
                    confirmButton: "btn btn-primary",
                },
            });
            return;
        }
    }

    Swal.fire({
        title: "Update Level?",
        text: "Apakah Anda yakin ingin mengupdate data level ini?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Ya",
        cancelButtonText: "Tidak",
        customClass: {
            confirmButton: "btn btn-primary",
            cancelButton: "btn btn-secondary",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            blockUI.block();
            $.ajax({
                url: APP_URL + "level/update",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    blockUI.release();
                    if (response.success) {
                        Swal.fire({
                            text: "Level berhasil diupdate.",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "OK",
                            customClass: {
                                confirmButton: "btn btn-primary",
                            },
                        }).then(() => {
                            window.location.href = APP_URL + "level";
                        });
                    } else {
                        Swal.fire({
                            text:
                                response.message ||
                                "Terjadi kesalahan saat mengupdate level.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "OK",
                            customClass: {
                                confirmButton: "btn btn-primary",
                            },
                        });
                    }
                },
                error: function (xhr, status, error) {
                    blockUI.release();
                    Swal.fire({
                        text:
                            xhr.responseJSON?.message ||
                            "Terjadi kesalahan saat mengupdate level.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        },
                    });
                }
            });
        } else {
            blockUI.release();
        }
    });
};
