var target = document.querySelector("#kt_app_body");
var blockUI = new KTBlockUI(target);
var APP_URL = window.APP_URL || "/";

$(() => {
    blockUI.block();
    // getData();
    initProfileFormValidation();
    blockUI.release();
});

// function getData() {
//     // let idMahasiswa = $("#id-mahasiswa").val();

//     $.ajax({
//         url: APP_URL + "mahasiswa/profile/get-data",
//         data: {
//             _token: $('meta[name="csrf-token"]').attr("content"),
//             id: idMahasiswa,
//         },
//         type: "POST",
//         success: function (response) {
//             $('#nama').val(response.name ?? '');
//             $('#avatar-preview').css('background-image', `url(${response.avatar ? APP_URL + 'assets/media/avatars/' + response.avatar : APP_URL + 'assets/media/avatars/blank.png'})`);
//         },
//         error: function (xhr) {
//             blockUI.release();
//             Swal.fire({
//                 text:
//                     xhr.responseJSON?.message ||
//                     "Terjadi kesalahan sistem.",
//                 icon: "error",
//                 buttonsStyling: false,
//                 confirmButtonText: "OK",
//                 customClass: {
//                     confirmButton: "btn btn-primary",
//                 },
//             });
//         },
//     });
// }

function resetForm() {
    location.reload();
}


var validator;
initProfileFormValidation = () => {
    const form = $("#profile-form")[0];
    const submitButton = $("#submit-profile")[0];

    validator = FormValidation.formValidation(form, {
        fields: {
            nama: {
                validators: {
                    notEmpty: {
                        message: "Nama wajib diisi",
                    },
                },
            },
            email: {
                validators: {
                    notEmpty: {
                        message: "Email wajib diisi",
                    },
                    emailAddress: {
                        message: "Format email tidak valid",
                    },
                },
            },
            // Password tidak wajib diisi, jadi validasi hanya jika diisi
            new_password: {
                validators: {
                    // Tidak required, tapi jika diisi, validasi min length bisa ditambah jika perlu
                },
            },
            confirm_password: {
                validators: {
                    callback: {
                        message: "Konfirmasi password wajib diisi dan harus sama",
                        callback: function(input) {
                            const newPassword = form.querySelector('[name="new_password"]').value;
                            const confirmPassword = input.value;
                            if (newPassword) {
                                if (!confirmPassword) {
                                    return {
                                        valid: false,
                                        message: "Konfirmasi password wajib diisi",
                                    };
                                }
                                if (newPassword !== confirmPassword) {
                                    return {
                                        valid: false,
                                        message: "Konfirmasi password tidak sama",
                                    };
                                }
                            }
                            return { valid: true };
                        }
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

    submitButton.addEventListener("click", function (e) {
        e.preventDefault();
        if (validator) {
            validator.validate().then(function (status) {
                if (status == "Valid") {
                    updateProfile();
                }
            });
        }
    });
};

function updateProfile() {
    let form = document.getElementById("profile-form");
    let formData = new FormData(form);

    const avatarInput = document.getElementById("avatar-input");
    const avatarFile = avatarInput?.files?.[0];
    const selectedAvatarUrl = document.getElementById("avatar-default-url")?.value || "";
    const id_user = document.getElementById("id-user").value;
    const name = document.getElementById("name").value;
    const email = document.getElementById("email").value;

    formData.append("name", name);
    formData.append("email", email);
    formData.append("id", id_user);
    formData.append("_token", document.querySelector('meta[name="csrf-token"]').getAttribute("content"));

    // --- Avatar logic ---
    if (avatarFile) {
        // user upload file baru
        formData.set("avatar", avatarFile);
        formData.delete("avatar_default");
    } else if (selectedAvatarUrl) {
        // user pilih avatar default → ambil basename (contoh: avatar1.webp)
        const basename = selectedAvatarUrl.split('/').pop();
        formData.set("avatar_default", basename);
        formData.delete("avatar");
    } else {
        // tidak pilih apa-apa
        formData.set("avatar", "");
        formData.delete("avatar_default");
    }

    // --- Validasi nama ---
    if (!name) {
        Swal.fire({
            text: "Nama wajib diisi.",
            icon: "error",
            confirmButtonText: "OK",
            customClass: { confirmButton: "btn btn-primary" },
        });
        return;
    }

    // --- Konfirmasi dan AJAX ---
    Swal.fire({
        title: "Konfirmasi",
        text: "Apakah Anda yakin ingin memperbarui profil?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Ya, perbarui",
        cancelButtonText: "Batal",
        buttonsStyling: false,
        customClass: {
            confirmButton: "btn btn-primary",
            cancelButton: "btn btn-secondary"
        }
    }).then((result) => {
        if (result.isConfirmed) {
            blockUI.block();
            $.ajax({
                url: APP_URL + "setting-admin/update",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    blockUI.release();
                    Swal.fire({
                        text: response.message || "Profil berhasil diperbarui.",
                        icon: "success",
                        confirmButtonText: "OK",
                        customClass: { confirmButton: "btn btn-primary" },
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function (xhr) {
                    blockUI.release();
                    Swal.fire({
                        text: xhr.responseJSON?.message || "Terjadi kesalahan sistem.",
                        icon: "error",
                        confirmButtonText: "OK",
                        customClass: { confirmButton: "btn btn-primary" },
                    });
                }
            });
        } else {
            blockUI.release();
        }
    });
}


