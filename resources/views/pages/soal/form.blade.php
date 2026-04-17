@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
    <style>
    </style>
@endpush


@section('content')
    <div class="container-fluid px-4" id="form-soal-container">
        <div class="row">
            <div class="col-12">
                <div class="bg-white rounded-4 shadow-sm p-8 mb-5">
                    <div class="d-flex justify-content-between align-items-center mb-8">
                        <h3 class="mb-0">Form Soal</h3>
                    </div>
                    <form method="POST" action="" id="form-soal">
                        <input type="hidden" name="id" id="id-soal">
                        <input type="hidden" name="data-soal" id="data-soal" value='@json($data)'>
                        @csrf

                        {{-- Baris 1: Select Level & Judul Soal --}}
                        <div class="row mb-10">
                            <div class="fv-row col-md-6">
                                <label for="level_id" class="form-label fs-5 required">Level</label>
                                <select name="level_id" id="level_id" class="form-select" required data-control="select2" data-hide-search="true" data-allow-clear="false" required>
                                    <option value="" selected disabled>Pilih Level</option>
                                    @foreach($levels as $level)
                                        <option value="{{ $level['id'] }}">{{ $level['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="fv-row col-md-6">
                                <label for="judul" class="form-label fs-5 required">Judul Soal</label>
                                <input type="text" name="judul" id="judul" class="form-control" placeholder="Judul Soal" required>
                            </div>
                        </div>

                        {{-- Baris 2: Soal (CKEditor) --}}
                        <div class="mb-10 fv-row ">
                            <label for="soal" class="form-label fs-5 required">Soal</label>
                            <textarea name="soal" id="soal" class="form-control" rows="5" required></textarea>
                        </div>

                        {{-- Baris 3: Form Repeater Tipe Data --}}
                       <div class="mb-10">
                            <label class="form-label fs-5">Tipe Data</label>
                            <div id="container-tipe-data">
                                <div class="row tipe-data-row mb-3 align-items-center">
                                    <div class="col-md-5">
                                        <input type="text" name="variabel[]" class="form-control" placeholder="Variabel (contoh: 10 meter; 1,5 meter)">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="tipe_data[]" class="form-control" placeholder="Tipe Data (contoh: int, float)">
                                    </div>
                                    <div class="col-md-3 d-flex align-items-center gap-3">
                                        <button type="button" class="btn btn-sm btn-light-danger remove-row ms-1 d-flex align-items-center">
                                            <i class="ki-duotone ki-trash fs-5 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                                <span class="path5"></span>
                                            </i>
                                            <span>Hapus</span>
                                        </button>
                                        <div class="form-check form-check-custom form-check-warning form-check-solid">
                                            <input type="checkbox" name="konversi_tipe_data[]" class="form-check-input me-1">
                                            <label class="form-check-label text-dark">konversi</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm add-tipe-data">Tambah Tipe Data</button>
                        </div>

                        {{-- Baris 4: Form Repeater Urutan Algoritma --}}
                        <div class="mb-10">
                            <label class="form-label fs-5">Urutan Algoritma</label>
                            <div id="container-algoritma">
                                <div class="row algoritma-row mb-3 align-items-center">
                                    <div class="col-md-8">
                                        <input type="text" name="langkah[]" class="form-control" placeholder="Langkah (contoh: START, READ panjang)">
                                    </div>
                                    <div class="col-md-1 d-flex align-items-center">
                                        <div class="form-check form-check-custom form-check-solid">
                                            <input type="checkbox" name="clue[]" class="form-check-input me-1" id="clue">
                                            <label class="form-check-label" for="clue">
                                                Clue
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-center gap-3">
                                        <button type="button" class="btn btn-sm btn-light-danger remove-row ms-1 d-flex align-items-center">
                                            <i class="ki-duotone ki-trash fs-5 me-1">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                                <span class="path5"></span>
                                            </i>
                                            <span>Hapus</span>
                                        </button>
                                        <div class="form-check form-check-custom form-check-warning form-check-solid">
                                            <input type="checkbox" name="konversi_algoritma[]" class="form-check-input me-1">
                                            <label class="form-check-label text-dark">
                                                konversi
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm add-algoritma">Tambah Langkah</button>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('soal.index') }}" class="btn btn-sm btn-secondary me-2">Batal</a>
                            <button type="button" class="btn btn-sm btn-primary" id="submit-form-soal">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/plugins/custom/ckeditor/ckeditor-classic.bundle.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="{{ asset('assets/plugins/custom/formrepeater/formrepeater.bundle.js') }}"></script>

    <script>
        let soalEditor;
        // Inisialisasi CKEditor
        ClassicEditor.create(document.querySelector('#soal'), {
            toolbar: [
                'heading', '|',
                'bold', 'italic', 'underline', 'strikethrough', '|',
                'bulletedList', 'numberedList', '|',
                'undo', 'redo'
            ]
        }).then(editor => {
            soalEditor = editor;
        });

        $(document).on('click', '.add-tipe-data', function () {
            const row = `
                <div class="row tipe-data-row mb-3 align-items-center">
                    <div class="col-md-5">
                        <input type="text" name="variabel[]" class="form-control" placeholder="Variabel (contoh: 10 meter; 1,5 meter)">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="tipe_data[]" class="form-control" placeholder="Tipe Data (contoh: int, float)">
                    </div>
                    <div class="col-md-3 d-flex align-items-center gap-3">
                        <button type="button" class="btn btn-sm btn-light-danger remove-row ms-1 d-flex align-items-center">
                            <i class="ki-duotone ki-trash fs-5 me-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                            </i>
                            <span>Hapus</span>
                        </button>
                        <div class="form-check form-check-custom form-check-warning form-check-solid">
                            <input type="checkbox" name="konversi_tipe_data[]" class="form-check-input me-1">
                            <label class="form-check-label text-dark">
                                konversi
                            </label>
                        </div>
                    </div>
                </div>`;
            $('#container-tipe-data').append(row);
        });

        $(document).on('click', '.add-algoritma', function () {
            const row = `
                <div class="row algoritma-row mb-3 align-items-center">
                    <div class="col-md-8">
                        <input type="text" name="langkah[]" class="form-control" placeholder="Langkah (contoh: START, READ panjang)">
                    </div>
                    <div class="col-md-1 d-flex align-items-center">
                        <div class="form-check form-check-custom form-check-solid">
                            <input type="checkbox" name="clue[]" class="form-check-input me-1" id="clue">
                            <label class="form-check-label" for="clue">
                                Clue
                            </label>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-center gap-3">
                        <button type="button" class="btn btn-sm btn-light-danger remove-row ms-1 d-flex align-items-center">
                            <i class="ki-duotone ki-trash fs-5 me-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                            </i>
                            <span>Hapus</span>
                        </button>
                        <div class="form-check form-check-custom form-check-warning form-check-solid">
                            <input type="checkbox" name="konversi_algoritma[]" class="form-check-input me-1">
                            <label class="form-check-label text-dark">
                                konversi
                            </label>
                        </div>
                    </div>
                </div>`;
            $('#container-algoritma').append(row);
        });

        $(document).on('click', '.remove-row', function () {
            // Untuk tipe data
            if ($(this).closest('#container-tipe-data').length) {
                if ($('#container-tipe-data .tipe-data-row').length <= 1) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Minimal 1 Tipe Data',
                        text: 'Minimal harus ada 1 tipe data.'
                    });
                    return;
                }
                $(this).closest('.tipe-data-row').remove();
            }
            // Untuk algoritma
            else if ($(this).closest('#container-algoritma').length) {
                if ($('#container-algoritma .algoritma-row').length <= 1) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Minimal 1 Langkah Algoritma',
                        text: 'Minimal harus ada 1 langkah algoritma.'
                    });
                    return;
                }
                $(this).closest('.algoritma-row').remove();
            }
        });

        // Cek apakah route mengandung id (edit mode)
        function getSoalIdFromUrl() {
            // Contoh: /soal/form/01986e87-0d8b-707e-9cee-532541434936
            const path = window.location.pathname;
            const regex = /\/soal\/form\/([a-zA-Z0-9\-]+)/;
            const match = path.match(regex);
            return match ? match[1] : null;
        }

        $(document).ready(function () {
            const soalId = getSoalIdFromUrl();
            const dataSoalVal = $('#data-soal').val();

            // Hanya isi form jika ada id di url dan ada data
            if (soalId && dataSoalVal) {
                try {
                    const dataSoal = JSON.parse(dataSoalVal);
                    // set id soal
                    if (dataSoal.id) {
                        $('#id-soal').val(dataSoal.id);
                    }
                    // Set level
                    if (dataSoal.id_level) {
                        $('#level_id').val(dataSoal.id_level).trigger('change');
                    }
                    // Set judul
                    if (dataSoal.judul) {
                        $('#judul').val(dataSoal.judul);
                    }
                    // Set soal (CKEditor)
                    if (dataSoal.soal && typeof soalEditor !== 'undefined') {
                        soalEditor.setData(dataSoal.soal);
                    } else if (dataSoal.soal) {
                        $('#soal').val(dataSoal.soal);
                    }

                    // Tipe Data
                    if (dataSoal.kunci_tipe_data) {
                        let tipeDataArr = typeof dataSoal.kunci_tipe_data === "string"
                            ? JSON.parse(dataSoal.kunci_tipe_data)
                            : dataSoal.kunci_tipe_data;
                            $('#container-tipe-data').empty();
                        tipeDataArr.forEach((item, i) => {
                            const rowId = 'tipe-data-row-' + i;
                            const row = `
                                <div class="row tipe-data-row mb-3 align-items-center" id="${rowId}">
                                    <div class="col-md-5">
                                        <input type="text" name="variabel[]" class="form-control" placeholder="Variabel (contoh: 10 meter; 1,5 meter)">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="tipe_data[]" class="form-control" placeholder="Tipe Data (contoh: int, float)">
                                    </div>
                                    <div class="col-md-3 d-flex align-items-center gap-3">
                                        <button type="button" class="btn btn-sm btn-light-danger remove-row ms-1 d-flex align-items-center">
                                            <i class="ki-duotone ki-trash fs-5 me-1">
                                                <span class="path1"></span><span class="path2"></span>
                                                <span class="path3"></span><span class="path4"></span><span class="path5"></span>
                                            </i>
                                            <span>Hapus</span>
                                        </button>
                                        <div class="form-check form-check-custom form-check-warning form-check-solid">
                                            <input type="checkbox" name="konversi_tipe_data[]" class="form-check-input me-1">
                                            <label class="form-check-label text-dark">
                                                konversi
                                            </label>
                                        </div>
                                    </div>
                                </div>`;
                            $('#container-tipe-data').append(row);
                            $('#' + rowId).find('input[name="variabel[]"]').val(item.variabel ?? '');
                            $('#' + rowId).find('input[name="tipe_data[]"]').val(item.tipe_data ?? '');
                            if (item.konversi == 1 || item.konversi === "1") {
                                $('#' + rowId).find('input[name="konversi_tipe_data[]"]').prop('checked', true);
                            }
                        });
                    }

                    // Algoritma
                    if (dataSoal.kunci_algoritma) {
                        let algoritmaArr = typeof dataSoal.kunci_algoritma === "string"
                            ? JSON.parse(dataSoal.kunci_algoritma)
                            : dataSoal.kunci_algoritma;
                            $('#container-algoritma').empty();
                        algoritmaArr.forEach((item, i) => {
                            const rowId = 'algoritma-row-' + i;
                            const checked = (item.clue == 1 || item.clue === "1") ? 'checked' : '';
                            const row = `
                                <div class="row algoritma-row mb-3 align-items-center" id="${rowId}">
                                    <div class="col-md-8">
                                        <input type="text" name="langkah[]" class="form-control" placeholder="Langkah (contoh: START, READ panjang)">
                                    </div>
                                    <div class="col-md-1 d-flex align-items-center">
                                        <div class="form-check form-check-custom form-check-solid">
                                            <input type="checkbox" name="clue[]" class="form-check-input me-1" ${checked}>
                                            <label class="form-check-label">Clue</label>
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-center gap-3">
                                        <button type="button" class="btn btn-sm btn-light-danger remove-row ms-1 d-flex align-items-center">
                                            <i class="ki-duotone ki-trash fs-5 me-1">
                                                <span class="path1"></span><span class="path2"></span>
                                                <span class="path3"></span><span class="path4"></span><span class="path5"></span>
                                            </i>
                                            <span>Hapus</span>
                                        </button>
                                        <div class="form-check form-check-custom form-check-warning form-check-solid">
                                            <input type="checkbox" name="konversi_algoritma[]" class="form-check-input me-1">
                                            <label class="form-check-label text-dark">
                                                konversi
                                            </label>
                                        </div>
                                    </div>
                                </div>`;
                            $('#container-algoritma').append(row);
                            // Set value aman (tidak terpotong walau ada kutip)
                            $('#' + rowId).find('input[name="langkah[]"]').val(item.langkah ?? '');
                            if (item.konversi == 1 || item.konversi === "1") {
                                $('#' + rowId).find('input[name="konversi_algoritma[]"]').prop('checked', true);
                            }
                        });
                    }
                } catch (e) {
                    Swal.fire({
                        text: "Data soal tidak valid atau terjadi kesalahan saat memuat data.",
                        icon: "error",
                        confirmButtonText: "OK",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        },
                    });
                }
            }
        });
    </script>

    <script src="{{ asset('js/soal/form.js') }}"></script>
@endpush
