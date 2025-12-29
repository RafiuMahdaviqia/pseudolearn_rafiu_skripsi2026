var target = document.querySelector("#kt_app_body");
var blockUI = new KTBlockUI(target);
var APP_URL = window.APP_URL || "/";

$(() => {
    blockUI.block();
    initTable();
    blockUI.release();
});

initSelectForm = () => {
    return new Promise((resolve, reject) => {
        $("#select-jenis-kelamin")
            .empty()
            .select2({
                data: [
                    { id: "", text: "Pilih Jenis Kelamin" },
                    { id: "p", text: "Perempuan" },
                    { id: "l", text: "Laki-laki" },
                ],
                placeholder: "Pilih Jenis Kelamin",
                allowClear: false,
                minimumResultsForSearch: Infinity,
                width: "100%",
            })
            .on("change.select2", function () {
                if (validator)
                    validator.revalidateField("select-jenis-kelamin");
            });

        $.ajax({
            url: APP_URL + "kelas/get-data",
            type: "POST",
            data: JSON.stringify({
                _token: $('meta[name="csrf-token"]').attr("content"),
            }),
            processData: false,
            contentType: "application/json",
            success: function (response) {
                const kelasData = response.map((item) => ({
                    id: item.id,
                    text: `${item.name} (${item.angkatan})`,
                }));
                $("#select-kelas")
                    .empty()
                    .select2({
                        data: kelasData,
                        placeholder: "Pilih Kelas",
                        allowClear: true,
                        width: "100%",
                    })
                    .on("change.select2", function () {
                        if (validator)
                            validator.revalidateField("select-jenis-kelamin");
                    });
            },
            error: function (xhr) {
                blockUI.release();
                Swal.fire({
                    text:
                        xhr.responseJSON?.message ||
                        "Terjadi kesalahan sistem.",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "OK",
                    customClass: {
                        confirmButton: "btn btn-primary",
                    },
                });
            },
        });

        resolve(true);
    });
};

resetForm = () => {
    $("#form-mahasiswa")[0].reset();
    $("#id").val("");
    $("#nim").val("");
    $("#nama").val("");
    // $("#email").val("");
    $("#select-kelas").val(null).trigger("change");
    $("#select-jenis-kelamin").val("").trigger("change");

    // Hapus semua tanda validasi
    $(".fv-row").removeClass("has-success has-error");
    $(".fv-plugins-message-container").html("");

    if (validator) {
        validator.resetForm(true);
    }
};


initTable = () => {
    let filterKelas = $("#filter-kelas").val() || "";
    return new Promise((resolve, reject) => {
        var table = $("#table-mahasiswa").DataTable({
            ajax: {
                url: APP_URL + "mahasiswa/table",
                type: "POST",
                data: function (d) {
                    d._token = $('meta[name="csrf-token"]').attr("content"),
                    d.filter_kelas = filterKelas;
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
                    data: "nim",
                    orderable: true,
                    searchable: true,
                },
                {
                    data: "name",
                    orderable: true,
                    searchable: true,
                },
                {
                    data: "kelas_name",
                    orderable: true,
                    searchable: true,
                    className: "text-center",
                },
                {
                    data: "email",
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
                    render: function (data, type, row, meta) {
                        return `<span class="badge badge-light-primary fs-7 fw-semibold">${row.nim}</span>`;
                    },
                },
                {
                    targets: 2,
                    render: function (data, type, row, meta) {
                        return row.name;
                    },
                },
                {
                    targets: 3,
                    render: function (data, type, row, meta) {
                        return row.kelas_name ?
                            `<span class="text-dark fw-semibold">${row.kelas_name} (${row.angkatan})</span>` :
                            `<span class="text-secondary fw-semibold">Tidak ada kelas</span>`;
                    },
                },
                {
                    targets: 4,
                    render: function (data, type, row, meta) {
                        return row.email;
                    },
                },
                {
                    targets: 5,
                    render: function (data, type, row, meta) {
                        return `
                            <div class="d-flex gap-3 justify-content-center">
                                <button class="btn btn-icon btn-sm btn-outline btn-outline-info" onclick="reset('${row.id}')" data-bs-toggle="tooltip" data-bs-placement="top" title="Reset Email dan Password">
                                    <i class="ki-outline ki-arrow-circle-left"></i>
                                </button>
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
                $("#search-mahasiswa").on("keyup", function () {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(function () {
                        table.search($("#search-mahasiswa").val()).draw();
                    }, 300);
                });
                if (table.state && table.state.loaded()) {
                    $("#search-mahasiswa").val(
                        table.state.loaded().search.search
                    );
                }

                // Inisialisasi Bootstrap tooltips pada elemen yang memiliki atribut data-bs-toggle="tooltip"
                $('[data-bs-toggle="tooltip"]').tooltip();
                resolve(true);
            },
        });
    });
};

var validator;
initFormValidation = () => {
    const form = $("#form-mahasiswa")[0];
    const submitButton = $("#btn-save-mahasiswa")[0];

    // Init form validation rules
    validator = FormValidation.formValidation(form, {
        fields: {
            nim: {
                validators: {
                    notEmpty: {
                        message: "NIM wajib diisi",
                    },
                },
            },
            nama: {
                validators: {
                    notEmpty: {
                        message: "Nama wajib diisi",
                    },
                },
            },
            // email: {
            //     validators: {
            //         notEmpty: {
            //             message: "Email wajib diisi",
            //         },
            //         emailAddress: {
            //             message: "Format email tidak valid",
            //         },
            //     },
            // },
            "select-kelas": {
                validators: {
                    notEmpty: {
                        message: "Kelas wajib dipilih",
                    },
                },
            },
            "select-jenis-kelamin": {
                validators: {
                    notEmpty: {
                        message: "Jenis kelamin wajib dipilih",
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

    $("#select-jenis-kelamin").on("change.select2", function () {
        if (validator) {
            validator.revalidateField("select-jenis-kelamin");
        }
    });
    
    $("#select-kelas").on("change.select2", function () {
        if (validator) {
            validator.revalidateField("select-kelas");
        }
    });

    // Submit button handler
    submitButton.addEventListener("click", function (e) {
        e.preventDefault();

        if (validator) {
            validator.validate().then(function (status) {
                if (status == "Valid") {
                    if ($("#id").val()) {
                        update();
                    } else {
                        save();
                    }
                }
            });
        }
    });
};

createMahasiswa = () => {
    resetForm();
    initSelectForm();
    $("#modal-form-mahasiswa").modal("show");
    initFormValidation();
};

$("#modal-form-mahasiswa").on("hidden.bs.modal", function () {
        // Reset form
    resetForm();

    // Destroy FormValidation instance
    if (validator) {
        validator.destroy();
        validator = null;
    }
});

save = () => {
    let form = $("#form-mahasiswa")[0];
    let formData = new FormData(form);
    let name = $("#nama").val();
    formData.append("_token", $('meta[name="csrf-token"]').attr("content"));

    if (!name) {
        Swal.fire({
            text: "Nama wajib diisi.",
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
        title: "Konfirmasi",
        text: "Apakah Anda yakin ingin menyimpan data mahasiswa ini?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, simpan",
        cancelButtonText: "Tidak",
        customClass: {
            confirmButton: "btn btn-primary",
            cancelButton: "btn btn-light",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            blockUI.block();
            $.ajax({
                url: APP_URL + "mahasiswa/store",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    blockUI.release();
                    Swal.fire({
                        text: response.message,
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        },
                    }).then(() => {
                        $("#modal-form-mahasiswa").modal("hide");
                        initTable();
                    });
                },
                error: function (xhr) {
                    blockUI.release();
                    Swal.fire({
                        text:
                            xhr.responseJSON?.message ||
                            "Terjadi kesalahan sistem.",
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
    }
    );
};

edit = (id) => {
    resetForm();
    initSelectForm();
    blockUI.block();

    $.ajax({
        url: APP_URL + "mahasiswa/" + id,
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        type: "GET",
        success: function (response) {
            blockUI.release();
            $("#id").val(response.id);
            $("#nim").val(response.nim);
            $("#nama").val(response.name);
            // $("#email").val(response.email);
            $("#select-kelas").val(response.id_kelas).trigger("change");
            $("#select-jenis-kelamin").val(response.jenis_kelamin).trigger("change");

            $("#modal-form-mahasiswa").modal("show");
            initFormValidation();
        },
        error: function (xhr) {
            blockUI.release();
            Swal.fire({
                text:
                    xhr.responseJSON?.message ||
                    "Terjadi kesalahan sistem.",
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

update = () => {
    let form = $("#form-mahasiswa")[0];
    let formData = new FormData(form);
    let name = $("#nama").val();
    formData.append("_token", $('meta[name="csrf-token"]').attr("content"));
    // formData.append("_method", "PUT");

    if (!name) {
        Swal.fire({
            text: "Nama wajib diisi.",
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
        title: "Konfirmasi",
        text: "Apakah Anda yakin ingin memperbarui data mahasiswa ini?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, perbarui",
        cancelButtonText: "Tidak",
        customClass: {
            confirmButton: "btn btn-primary",
            cancelButton: "btn btn-light",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            blockUI.block();
            $.ajax({
                url: APP_URL + "mahasiswa/update",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    blockUI.release();
                    Swal.fire({
                        text: response.message,
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        },
                    }).then(() => {
                        $("#modal-form-mahasiswa").modal("hide");
                        initTable();
                    });
                },
                error: function (xhr) {
                    blockUI.release();
                    Swal.fire({
                        text:
                            xhr.responseJSON?.message ||
                            "Terjadi kesalahan sistem.",
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
}

destroy = (id) => {
    Swal.fire({
        title: "Konfirmasi",
        text: "Apakah Anda yakin ingin menghapus data mahasiswa ini?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, hapus",
        cancelButtonText: "Tidak",
        customClass: {
            confirmButton: "btn btn-primary",
            cancelButton: "btn btn-light",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            blockUI.block();
            $.ajax({
                url: APP_URL + "mahasiswa/" + id,
                type: "DELETE",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (response) {
                    blockUI.release();
                    Swal.fire({
                        text: response.message,
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        },
                    }).then(() => {
                        initTable();
                    });
                },
                error: function (xhr) {
                    blockUI.release();
                    Swal.fire({
                        text:
                            xhr.responseJSON?.message ||
                            "Terjadi kesalahan sistem.",
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

reset = (id) => {
    Swal.fire({
        title: "Konfirmasi",
        text: "Apakah Anda yakin ingin mereset email dan password mahasiswa ini?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, reset",
        cancelButtonText: "Tidak",
        customClass: {
            confirmButton: "btn btn-primary",
            cancelButton: "btn btn-light",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            blockUI.block();
            $.ajax({
                url: APP_URL + "mahasiswa/reset/" + id,
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (response) {
                    blockUI.release();
                    Swal.fire({
                        text: response.message,
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        },
                    }).then(() => {
                        initTable();
                    });
                },
                error: function (xhr) {
                    blockUI.release();
                    Swal.fire({
                        text:
                            xhr.responseJSON?.message ||
                            "Terjadi kesalahan sistem.",
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

$('#filter-kelas').on('change', function() {
    initTable();
});

function exportMahasiswa(){
    Swal.fire({
        title: "Konfirmasi",
        text: "Apakah Anda yakin ingin mengekspor data mahasiswa?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, ekspor",
        cancelButtonText: "Tidak",
        customClass: {
            confirmButton: "btn btn-primary",
            cancelButton: "btn btn-light",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            blockUI.block();
            $.ajax({
                url: APP_URL + "mahasiswa/export",
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    id_kelas: $('#filter-kelas').val()
                },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function (data, status, xhr) {
                    blockUI.release();
                    // Ambil nama file dari header X-Filename jika ada, fallback ke Content-Disposition, lalu default
                    var fileName = "mahasiswa_export.xlsx";
                    var backendFileName = xhr.getResponseHeader('X-Filename');
                    if (backendFileName) {
                        fileName = decodeURIComponent(backendFileName);
                    } else {
                        var disposition = xhr.getResponseHeader('Content-Disposition');
                        if (disposition && disposition.indexOf('attachment') !== -1) {
                            var matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(disposition);
                            if (matches != null && matches[1]) {
                                fileName = matches[1].replace(/['"]/g, '');
                            }
                        }
                    }
                    var url = window.URL.createObjectURL(new Blob([data]));
                    var a = document.createElement('a');
                    a.href = url;
                    a.download = fileName;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    a.remove();
                },
                error: function (xhr) {
                    blockUI.release();
                    Swal.fire({
                        text:
                            xhr.responseJSON?.message ||
                            "Terjadi kesalahan sistem.",
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
}

// Buka modal import mahasiswa
function openModalImport() {
    resetFormImport();
    initFormValidationImport();
    $('#modal-import-mahasiswa').modal('show');
}

// Reset form import mahasiswa
function resetFormImport() {
    $('#form-import-mahasiswa')[0].reset();
    $('#import_mahasiswa_file_placeholder').val('Tidak ada file');
    if( validatorImport) {
        validatorImport.resetForm(true);
    }
}

// Ketika file diinput, tampilkan nama file di placeholder
function onImportMahasiswaFileChange() {
    const fileInput = document.getElementById('import_mahasiswa_file');
    const placeholder = document.getElementById('import_mahasiswa_file_placeholder');
    if (fileInput.files.length > 0) {
        placeholder.value = fileInput.files[0].name;
    } else {
        placeholder.value = 'Tidak ada file';
    }
}

// Hapus file yang sudah dipilih
function removeImportMahasiswaFile() {
    $('#import_mahasiswa_file').val('');
    $('#import_mahasiswa_file_placeholder').val('Tidak ada file');
}

var validatorImport;

initFormValidationImport = () => {
    const form = $("#form-import-mahasiswa")[0];
    const submitButton = $("#btn-save-import-mahasiswa")[0];

    // Init form validation rules
    validatorImport = FormValidation.formValidation(form, {
        fields: {
            import_mahasiswa_file: {
                validators: {
                    notEmpty: {
                        message: "File import wajib diisi",
                    },
                    file: {
                        extension: "xlsx,xls,csv",
                        type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,text/csv",
                        message: "Format file tidak valid. Hanya diperbolehkan .xlsx, .xls, atau .csv.",
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

    // Submit button handler
    submitButton.addEventListener("click", function (e) {
        e.preventDefault();

        if (validatorImport) {
            validatorImport.validate().then(function (status) {
                if (status == "Valid") {
                    importMahasiswa();
                }
            });
        }
    });
}

importMahasiswa = () => {
    let form = $("#form-import-mahasiswa")[0];
    let formData = new FormData(form);
    let fileInput = $("#import_mahasiswa_file")[0];
    if (fileInput.files.length === 0) {
        Swal.fire({
            text: "File import wajib diisi.",
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: "OK",
            customClass: {
                confirmButton: "btn btn-primary",
            },
        });
        return;
    }

    formData.append("_token", $('meta[name="csrf-token"]').attr("content"));

    Swal.fire({
        title: "Konfirmasi",
        text: "Apakah Anda yakin ingin mengimpor data mahasiswa?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, impor",
        cancelButtonText: "Tidak",
        customClass: {
            confirmButton: "btn btn-primary",
            cancelButton: "btn btn-light",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            blockUI.block();
            $.ajax({
                url: APP_URL + "mahasiswa/import",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    blockUI.release();
                    Swal.fire({
                        text: response.message,
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        },
                    }).then(() => {
                        $('#modal-import-mahasiswa').modal('hide');
                        initTable();
                    });
                },
                error: function (xhr) {
                    blockUI.release();
                    Swal.fire({
                        text:
                            xhr.responseJSON?.message ||
                            "Terjadi kesalahan sistem.",
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