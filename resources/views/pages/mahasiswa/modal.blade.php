<div class="modal fade" tabindex="-1" id="modal-form-mahasiswa" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    Form Mahasiswa
                </h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                    aria-label="Close" onclick="resetForm()">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <form action="" method="post" id="form-mahasiswa">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <div class="row">
                        <div class="col-6">
                            <div class="fv-row mb-5">
                                <label for="nim" class="form-label">NIM</label>
                                <input type="text" class="form-control" id="nim" name="nim"
                                    placeholder="Masukkan NIM">
                            </div>
                            <div class="fv-row mb-5">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="nama" name="nama"
                                    placeholder="Masukkan Nama">
                            </div>
                            {{-- <div class="fv-row mb-5">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Masukkan Email">
                            </div> --}}
                        </div>
                        <div class="col-6">
                            <div class="fv-row mb-5">
                                <label for="kelas" class="form-label">Kelas</label>
                                <select class="form-select" id="select-kelas" name="select-kelas">
                                </select>
                            </div>
                            <div class="fv-row mb-5">
                                <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                <select class="form-select" id="select-jenis-kelamin"
                                    name="select-jenis-kelamin"></select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" onclick="resetForm()">Close</button>
                    <button type="button" class="btn btn-primary" id="btn-save-mahasiswa">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="modal-import-mahasiswa" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    Import Mahasiswa
                </h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                    aria-label="Close" onclick="resetFormImport()">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <form action="" method="post" id="form-import-mahasiswa" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="fv-row row w-100">
                        <div class="col-11">
                            <label for="import_mahasiswa_file" class="form-label">Import Mahasiswa</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="import_mahasiswa_file_placeholder"
                                    onclick="$('#import_mahasiswa_file').click()" style="cursor: pointer;"
                                    placeholder="Tidak ada file" readonly>
                                <div class="input-group-append">
                                    <button class="btn btn-primary rounded-0 rounded-end" type="button"
                                        onclick="$('#import_mahasiswa_file').click()">Upload File</button>
                                </div>
                                <input type="file" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                    name="import_mahasiswa_file"
                                    id="import_mahasiswa_file" style="display: none" onchange="onImportMahasiswaFileChange()">
                            </div>
                            <small class="form-text text-muted">
                                File yang diupload hanya boleh bertipe: Excel (xls, xlsx).
                            </small>
                        </div>
                        <div class="col-1">
                            <a id="download-template-mahasiswa-excel" target="_blank" class="btn btn-sm btn-outline btn-outline-primary me-2 mt-8 mb-3" data-bs-toggle="tooltip" data-bs-placement="top" title="Download Template Import Mahasiswa"
                               onclick="event.preventDefault(); window.open('{{ asset('assets/template/template_import_mahasiswa.xlsx') }}', '_blank');">
                                <i class="ki-outline ki-cloud-download fs-1 p-0 py-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal" onclick="resetFormImport()">Batal</button>
                    <button type="button" class="btn btn-primary btn-sm" id="btn-save-import-mahasiswa">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
