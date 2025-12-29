var target = document.querySelector("#kt_app_body");
var blockUI = new KTBlockUI(target);
var APP_URL = window.APP_URL || "/";

$(() => {
    blockUI.block();
    initSelectAngkatan();
    initTable();
    initFormValidation();
    blockUI.release();
});

initSelectAngkatan = () => {
    let selectAngkatan = document.querySelector("#select-angkatan");
    if (selectAngkatan) {
        let currentYear = new Date().getFullYear();
        let startYear = 2000;
        let endYear = currentYear + 5;
        for (let year = endYear; year >= startYear; year--) {
            let option = document.createElement("option");
            option.value = year;
            option.textContent = year;
            if (year === currentYear) {
                option.selected = true;
            }
            selectAngkatan.appendChild(option);
        }
        $(selectAngkatan).select2({
            placeholder: "Pilih Angkatan",
            width: "100%",
            minimumResultsForSearch: Infinity,
        });
    }
};

initTable = () => {
    return new Promise((resolve, reject) => {
        var table = $("#kelas-table").DataTable({
            ajax: {
                url: APP_URL + "kelas/table",
                type: "POST",
                data: function (d) {
                    d._token = $('meta[name="csrf-token"]').attr("content");
                },
            },
            processing: true,
            serverSide: true,
            destroy: true,
            responsive: false,
            order: [[0, "desc"]],
            columns: [
                {
                    data: null,
                    className: "text-center",
                    orderable: true,
                    searchable: false,
                },
                {
                    data: "name",
                    orderable: true,
                    searchable: true,
                },
                {
                    data: "angkatan",
                    orderable: true,
                    searchable: true,
                },
                {
                    data: null,
                    className: "text-center",
                    orderable: false,
                    searchable: false,
                },
            ],
            columnDefs: [
                {
                    targets: 0,
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    },
                },
                {
                    targets: 1,
                    render: function (data, type, row) {
                        return row.name;
                    },
                },
                {
                    targets: 2,
                    render: function (data, type, row) {
                        return row.angkatan;
                    },
                },
                {
                    targets: 3,
                    render: function (data, type, row, meta) {
                        return `
                            <div class="d-flex gap-3 justify-content-center">
                                <button class="btn btn-icon btn-sm btn-outline btn-outline-warning" onclick="edit('${row.id}')">
                                    <i class="ki-outline ki-pencil"></i>
                                </button>
                                <button class="btn btn-icon btn-sm btn-outline btn-outline-danger" onclick="destroy('${row.id}')">
                                    <i class="ki-outline ki-trash"></i>
                                </button>
                            </div>
                        `;
                    },
                },
            ],
            createdRow: function (row, data, dataIndex) {
                $(row).attr("id", data.id || data[0]);
            },
            initComplete: function (settings, json) {
                var debounceTimer;
                $("#search-kelas").on("keyup", function () {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(function () {
                        table.search($("#search-kelas").val()).draw();
                    }, 300);
                });
                if (table.state && table.state.loaded()) {
                    $("#search-kelas").val(table.state.loaded().search.search);
                }
                resolve(true);
            },
        });
    });
};

var validator;
initFormValidation = () => {
    const form = $("#form-kelas")[0];
    const submitButton = $("#submit-form-kelas")[0];

    // Init form validation rules
    validator = FormValidation.formValidation(form, {
        fields: {
            nama: {
                validators: {
                    notEmpty: {
                        message: "Nama kelas wajib diisi",
                    },
                },
            },
            "select-angkatan": {
                validators: {
                    notEmpty: {
                        message: "Angkatan wajib dipilih",
                    },
                },
            },
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

    $("#select-angkatan").on("change", function () {
        validator.revalidateField("angkatan");
    });

    // Submit button handler
    submitButton.addEventListener("click", function (e) {
        e.preventDefault();

        if (validator) {
            validator.validate().then(function (status) {
                if (status == "Valid") {
                    if ($("#id-kelas").val()) {
                        update();
                    } else {
                        save();
                    }
                }
            });
        }
    });
};

resetForm = () => {
    $("#form-kelas")[0].reset();
    $("#id-kelas").val("");
    $("#nama").val("");
    $("#select-angkatan").val("").trigger("change");
    initSelectAngkatan();
    if (validator) {
        validator.resetForm();
    }
};

save = () => {
    let form = $("#form-kelas")[0];
    let formData = new FormData(form);
    let nama = $("#nama").val();
    formData.append("_token", $('meta[name="csrf-token"]').attr("content"));
    formData.append("name", $("#nama").val());
    formData.append("angkatan", $("#select-angkatan").val());

    if (!nama) {
        Swal.fire({
            text: "Nama kelas harus diisi.",
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
        title: "Simpan Kelas?",
        text: "Apakah Anda yakin ingin menyimpan data kelas ini?",
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
                url: APP_URL + "kelas/store",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    blockUI.release();
                    if (response.success) {
                        Swal.fire({
                            text: "Kelas berhasil disimpan.",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "OK",
                            customClass: {
                                confirmButton: "btn btn-primary",
                            },
                        }).then(() => {
                            initTable();
                            resetForm();
                        });
                    } else {
                        Swal.fire({
                            text:
                                response.message ||
                                "Terjadi kesalahan saat menyimpan kelas.",
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
                            "Terjadi kesalahan saat menyimpan kelas.",
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

edit = (id) => {
    blockUI.block();
    $.ajax({
        url: APP_URL + "kelas/" + id,
        type: "GET",
        success: function (data) {
            blockUI.release();
            if (data && data.id) {
                $("#nama").val(data.name);
                $("#select-angkatan").val(data.angkatan).trigger("change");
                $('#id-kelas').val(data.id);
            } else {
                Swal.fire({
                    text: "Data kelas tidak ditemukan.",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "OK",
                    customClass: {
                        confirmButton: "btn btn-primary",
                    },
                });
            }
        },
        error: function (xhr) {
            blockUI.release();
            Swal.fire({
                text: xhr.responseJSON?.message || "Gagal mengambil data kelas.",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "OK",
                customClass: {
                    confirmButton: "btn btn-primary",
                },
            });
        },
    });
};

destroy = (id) => {
    Swal.fire({
        title: "Hapus Kelas?",
        text: "Apakah Anda yakin ingin menghapus kelas ini?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, Hapus",
        cancelButtonText: "Tidak",
        customClass: {
            confirmButton: "btn btn-danger",
            cancelButton: "btn btn-secondary",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            blockUI.block();
            $.ajax({
                url: APP_URL + "kelas/" + id,
                type: "DELETE",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (response) {
                    blockUI.release();
                    if (response.success) {
                        Swal.fire({
                            text: "Kelas berhasil dihapus.",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "OK",
                            customClass: {
                                confirmButton: "btn btn-primary",
                            },
                        }).then(() => {
                            initTable();
                        });
                    } else {
                        Swal.fire({
                            text:
                                response.message ||
                                "Terjadi kesalahan saat menghapus kelas.",
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
                            "Terjadi kesalahan saat menghapus kelas.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        },
                    });
                },
            });
        }
    });
};

update = () => {
    let form = $("#form-kelas")[0];
    let formData = new FormData(form);
    let nama = $("#nama").val();
    formData.append("_token", $('meta[name="csrf-token"]').attr("content"));
    formData.append("name", $("#nama").val());
    formData.append("angkatan", $("#select-angkatan").val());
    formData.append("id", $("#id-kelas").val());

    if (!nama) {
        Swal.fire({
            text: "Nama kelas harus diisi.",
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
        title: "Perbarui Kelas?",
        text: "Apakah Anda yakin ingin memperbarui data kelas ini?",
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
                url: APP_URL + "kelas/update",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    blockUI.release();
                    if (response.success) {
                        Swal.fire({
                            text: "Kelas berhasil diperbarui.",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "OK",
                            customClass: {
                                confirmButton: "btn btn-primary",
                            },
                        }).then(() => {
                            initTable();
                            resetForm();
                        });
                    } else {
                        Swal.fire({
                            text:
                                response.message ||
                                "Terjadi kesalahan saat memperbarui kelas.",
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
                            "Terjadi kesalahan saat memperbarui kelas.",
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
    }
    );
}