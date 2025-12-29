@php
    $data = isset($data) ? $data : null;
@endphp
@extends('layouts.main')

@push('styles')
    <style>
       .dz-progress {
            display: none !important;
        }
    </style>
@endpush


@section('content')
    <div class="container-fluid px-4" id="form-level-container">
        <div class="row">
            <div class="col-12">
                <div class="bg-white rounded-4 shadow-sm p-8 mb-5">
                    <div class="d-flex justify-content-between align-items-center mb-8">
                        <h3 class="mb-0">Form Level</h3>
                    </div>
                    <form method="POST" action="" id="form-level">
                        <input type="hidden" name="id" id="id-level">
                        <input type="hidden" name="data" id="data-level" value="{{ $data ? json_encode($data) : '' }}">
                        @csrf
                        <div class="fv-row mb-5">
                            <label for="nama" class="form-label fs-6 required">Nama Level</label>
                            <input type="text" class="form-control form-control-sm" id="nama" name="nama" placeholder="Masukkan nama level" required>
                        </div>
                        <div class="row fv-row mb-5">
                            <div class="col-6">
                                <label for="feedback-tipe-data" class="form-label fs-6">Feedback Tipe Data</label>
                                <textarea class="form-control form-control-sm" id="feedback-tipe-data" name="feedback_tipe_data" placeholder="Masukkan feedback tipe data" rows="4"></textarea>
                            </div>
                            <div class="col-6">
                                <label for="feedback-algoritma" class="form-label fs-6">Feedback Algoritma</label>
                                <textarea class="form-control form-control-sm" id="feedback-algoritma" name="feedback_algoritma" placeholder="Masukkan feedback algoritma" rows="4"></textarea>
                            </div>
                        </div>
                        <div class="fv-row mb-5">
                            <label class="form-label fs-6 required">Logo Level</label>
                            <div class="border rounded-3 p-4 bg-light">
                                <div class="dropzone" id="dropzone-level-logo" data-max-files="1" data-max-file-size="2">
                                    <div class="dz-message needsclick">
                                        <i class="ki-duotone ki-file-up fs-3x text-primary"><span class="path1"></span><span class="path2"></span></i>
                                        <div class="ms-4">
                                            <h3 class="fs-5 fw-bold text-gray-900 mb-1">Drop file here or click to upload.</h3>
                                            <span class="fs-7 fw-semibold text-gray-500">Upload maksimal 1 file, maksimal 2 MB (.png, .jpg, .jpeg)</span>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="logo_level" id="logo-level-hidden">
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('level.index') }}" class="btn btn-sm btn-secondary me-2">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-sm btn-primary" id="submit-form-level">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/level/form.js') }}"></script>
    <script>
        $(function() {
            let data = $('#data-level').val();
            if (data) {
                try {
                    data = JSON.parse(data);
                    // Handle response structure: check if 'original' exists
                    if (data.original) {
                        data = data.original;
                    }
                    $('#id-level').val(data.id ?? '');
                    $('#nama').val(data.name ?? '');
                    $('#feedback-tipe-data').val(data.feedback_data_type ?? '');
                    $('#feedback-algoritma').val(data.feedback_algorithm ?? '');
                    $('#logo-level-hidden').val(data.image ?? '');
                    
                    if (data.image) {
                        let mockFile = { 
                            name: data.image, 
                            size: 123456, // Ganti dengan ukuran file yang sesuai jika tahu
                            type: 'image/png' // atau image/jpeg sesuai kebutuhan
                        };
                        let dropzone = Dropzone.forElement("#dropzone-level-logo");
                        let imageUrl = '{{ asset('') }}' + data.image.replace(/^\/+/, '');
                        dropzone.emit("addedfile", mockFile);
                        dropzone.emit("thumbnail", mockFile, imageUrl);
                        dropzone.emit("complete", mockFile);
                        dropzone.files.push(mockFile);

                        // Set file as accepted and success
                        mockFile.status = Dropzone.SUCCESS;
                        dropzone.options.maxFiles = 1;
                        dropzone._updateMaxFilesReachedClass();

                        // Optional: prevent further uploads if already filled
                        dropzone.options.acceptedFiles = ".png,.jpg,.jpeg";

                        // Resize preview image
                        setTimeout(function() {
                            $('#dropzone-level-logo .dz-image img').css({
                                'max-width': '120px',
                                'max-height': '120px',
                                'width': 'auto',
                                'height': 'auto',
                                'object-fit': 'contain'
                            });
                        }, 100);
                    }

                    
                } catch (e) {
                    Swal.fire({
                        title: "Error",
                        text: "Gagal memuat data level.",
                        icon: "error",
                        confirmButtonText: "OK",
                        customClass: { confirmButton: "btn btn-primary" }
                    });
                }
            }
        });
    </script>
@endpush
