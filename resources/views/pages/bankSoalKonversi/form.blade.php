@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
    <style>
        .ck-editor__editable {
            min-height: 120px;
        }
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
                        <input type="hidden" name="id" id="id_konversi">
                        <input type="hidden" name="data-soal" id="data-soal" value="{{ $data ? json_encode($data) : '' }}">
                        @csrf

                        {{-- Baris 1: Select Level, Soal, Bobot --}}
                        <div class="row mb-5">
                            <div class="fv-row col-md-4">
                                <label for="level_id" class="form-label fs-5 required">Level</label>
                                <select name="level_id" id="level_id" class="form-select" data-control="select2"
                                    data-hide-search="true">
                                    <option value="" selected disabled>Pilih Level</option>
                                    @foreach ($levels as $level)
                                        <option value="{{ $level['id'] }}">{{ $level['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="fv-row col-md-4">
                                <label for="soal_id" class="form-label fs-5 required">Soal</label>
                                <select name="soal_id" id="soal_id" class="form-select" data-control="select2"
                                    data-hide-search="true">
                                    <option value="" selected disabled>Pilih Soal</option>
                                </select>
                            </div>

                            <div class="fv-row col-md-4">
                                <label for="bobot" class="form-label fs-5 required">Bobot</label>
                                <input type="number" name="bobot" id="bobot" class="form-control" min="0"
                                    required>
                            </div>
                        </div>

                        {{-- Soal --}}
                        <div class="row mb-5">
                            <div class="fv-row col-md-12">
                                <label for="soal" class="form-label fs-5 required">Soal</label>
                                <textarea name="soal" id="soal" class="form-control" rows="3" readonly></textarea>
                            </div>
                        </div>

                        <div id="row-konversi" class="row mb-8 d-none">
                            <div class="col-md-6">
                                <h5 class="mb-3">Kunci</h5>
                                <div id="col-kunci" class="small"></div>
                            </div>
                            <div class="col-md-6">
                                <h5 class="mb-3">Konversi Kode</h5>
                                <div id="col-input"></div>
                            </div>
                        </div>

                        <div class="row mb-10">
                            <div class="fv-row col-md-12">
                                <label for="output" class="form-label fs-5 required">Output</label>
                                <textarea name="output" id="output" class="form-control" rows="3" readonly required></textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <div class="">
                                <button type="button" class="btn btn-sm btn-primary" id="btn-run-konversi"
                                    onclick="runKonversi()">
                                    <i class="ki-outline ki-send fs-6"></i>
                                    Jalankan Konversi
                                </button>
                            </div>
                            <div>
                                <a href="{{ route('konversi.index') }}" class="btn btn-sm btn-secondary me-2">Batal</a>
                                <button type="button" class="btn btn-sm btn-primary" id="submit-form-soal">Simpan</button>
                            </div>
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
        var APP_URL = window.APP_URL || "/";
        // Inisialisasi CKEditor hanya readonly tanpa toolbar dan tinggi 3 row
        ClassicEditor.create(document.querySelector('#soal'), {
            // toolbar: [],
            readOnly: true
        }).then(editor => {
            soalEditor = editor;
            editor.enableReadOnlyMode("soal");
            editor.ui.view.editable.element.style.height = '120px';
        });

        $('#level_id').on('change', function() {
            soalEditor.setData('');
            clearKonversi();
            var levelId = $(this).val();
            if (levelId) {
                $.ajax({
                    url: APP_URL +
                        'konversi/getSoalByLevel', // pastikan route name/URL sesuai (konversi.getSoalByLevel)
                    type: "GET",
                    data: {
                        level_id: levelId
                    },
                    success: function(data) {
                        $('#soal_id').empty().append(
                            '<option value="" selected disabled>Pilih Soal</option>');
                        $.each(data, function(_, soal) {
                            $('#soal_id').append('<option value="' + soal.id + '">' + soal
                                .judul + '</option>');
                        });
                    }
                });
            } else {
                $('#soal_id').empty().append('<option value="" selected disabled>Pilih Soal</option>');
            }
        });

        $('#soal_id').on('change', function() {
            var soalId = $(this).val();
            clearKonversi();
            if (soalId) {
                $.ajax({
                    url: APP_URL + 'soal/' + soalId,
                    type: "GET",
                    data: {
                        soal_id: soalId
                    },
                    success: function(data) {
                        soalEditor.setData(data.soal || '');
                        renderKonversi(data);
                    }
                });
            } else {
                soalEditor.setData('');
            }
        });

        let baseKonversiRows = 0; // jumlah minimal (baris kunci)
        let extraKonversiIndex = 0; // penghitung baris Langkah
        let lastLineIndex = 0; // untuk penomoran baris Langkah

        function clearKonversi() {
            $('#col-kunci').empty();
            $('#col-input').empty();
            $('#row-konversi').addClass('d-none');
            baseKonversiRows = 0;
            extraKonversiIndex = 0;
            lastLineIndex = 0;
        }

        function renderKonversi(data) {
            clearKonversi();
            if (!data) return;

            let tipeData = [],
                algoritma = [];
            try {
                if (data.kunci_tipe_data) tipeData = JSON.parse(data.kunci_tipe_data) || [];
            } catch (e) {}
            try {
                if (data.kunci_algoritma) algoritma = JSON.parse(data.kunci_algoritma) || [];
            } catch (e) {}

            // Filter hanya yang konversi = 1
            tipeData = tipeData.filter(it => Number(it.konversi) === 1);
            algoritma = algoritma.filter(it => Number(it.konversi) === 1);

            // Gabung list
            const combined = [];
            tipeData.forEach(item => combined.push({
                kind: 'tipe_data',
                data: item
            }));
            algoritma.forEach(item => combined.push({
                kind: 'algoritma',
                data: item
            }));

            const kunciWrap = $('#col-kunci');
            const inputWrap = $('#col-input');

            let lineIndex = 0;

            combined.forEach((entry) => {
                lineIndex++;
                if (entry.kind === 'tipe_data') {
                    const variabel = entry.data.variabel ?? '-';
                    const tipe = entry.data.tipe_data ?? '-';
                    kunciWrap.append(`
                <div class="mb-5">
                    <label class="form-label mb-1">Langkah ${lineIndex}</label>
                    <input type="text" class="form-control" value="Variabel: ${variabel}, Tipe: ${tipe}" readonly>
                </div>
                `);
                    inputWrap.append(`
                <div class="mb-5 konversi-row" data-base="1">
                    <div class="d-flex justify-content-between">
                    <label class="form-label mb-1">Langkah ${lineIndex}</label>
                    </div>
                    <input type="text" name="jawaban[]" class="form-control" placeholder="Langkah ${lineIndex}">
                    <input type="hidden" name="jawaban_tipe[]" value="tipe_data_${lineIndex}">
                </div>
                `);
                } else {
                    const langkah = entry.data.langkah ?? '';
                    // const clue = (entry.data.clue == 1 || entry.data.clue === '1'); // Tidak ditampilkan lagi
                    const safe = langkah
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#39;');
                    kunciWrap.append(`
                <div class="mb-5">
                    <label class="form-label mb-1">Langkah ${lineIndex}</label>
                    <input type="text" class="form-control" value="${safe}" readonly>
                </div>
                `);
                    inputWrap.append(`
                <div class="mb-5 konversi-row" data-base="1">
                    <div class="d-flex justify-content-between">
                    <label class="form-label mb-1">Langkah ${lineIndex}</label>
                    </div>
                    <input type="text" name="jawaban[]" class="form-control" placeholder="Langkah ${lineIndex}">
                    <input type="hidden" name="jawaban_tipe[]" value="algoritma_${lineIndex}">
                </div>
                `);
                }
            });

            baseKonversiRows = lineIndex;
            lastLineIndex = lineIndex;
            if (lineIndex > 0) {
                $('#row-konversi').removeClass('d-none');
            }
        }

        // Tambah baris baru (boleh dihapus)
        $(document).on('click', '#btn-add-konversi-row', function() {
            if (baseKonversiRows === 0) {
                Swal.fire({
                    icon: 'warning',
                    text: 'Pilih soal terlebih dahulu.',
                    confirmButtonText: 'OK'
                });
                return;
            }
            extraKonversiIndex++;
            lastLineIndex++;
            $('#col-input').append(`
                <div class="mb-5 konversi-row" data-base="0">
                    <label class="form-label mb-1">Langkah ${lastLineIndex}</label>
                    <div class="row">
                        <div class="col-11">
                            <input type="text" name="jawaban[]" class="form-control" placeholder="Langkah ${lastLineIndex}">
                        </div>
                        <div class="col-1 d-flex justify-content-center align-item-middle">
                            <button type="button" class="btn btn-outline btn-outline-danger btn-remove-konversi-row btn-sm px-3 py-0" title="Hapus">
                                <i class="ki-outline ki-trash px-0 fs-4"></i>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="jawaban_tipe[]" value="extra_${extraKonversiIndex}">
                </div>
            `);
        });

        // Hapus baris
        $(document).on('click', '.btn-remove-konversi-row', function() {
            const row = $(this).closest('.konversi-row');
            if (row.data('base') == 1) {
                Swal.fire({
                    icon: 'warning',
                    text: `Baris kunci tidak boleh dihapus.`,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });
                return;
            }
            row.remove();
            updateLangkahLabels();
        });

        function updateLangkahLabels() {
            let index = 1;
            $('#col-input .konversi-row').each(function() {
                $(this).find('label.form-label').text('Langkah ' + index);
                $(this).find('input.form-control').attr('placeholder', 'Langkah ' + index);
                index++;
            });
            lastLineIndex = index - 1;
        }

        function addExtraKonversiRowWithValue(value) {
            extraKonversiIndex++;
            lastLineIndex++;
            const rowId = 'extra-row-' + extraKonversiIndex;
            $('#col-input').append(`
                <div class="mb-5 konversi-row" data-base="0" id="${rowId}">
                    <label class="form-label mb-1">Langkah ${lastLineIndex}</label>
                    <div class="row">
                        <div class="col-11">
                            <input type="text" name="jawaban[]" class="form-control" placeholder="Langkah ${lastLineIndex}">
                        </div>
                        <div class="col-1 d-flex justify-content-center align-item-middle">
                            <button type="button" class="btn btn-outline btn-outline-danger btn-remove-konversi-row btn-sm px-3 py-0" title="Hapus">
                                <i class="ki-outline ki-trash px-0 fs-4"></i>
                            </button>
                        </div>
                    </div>
                    <input type="hidden" name="jawaban_tipe[]" value="extra_${extraKonversiIndex}">
                </div>
            `);
            // Set value dengan .val agar tidak terpotong
            $('#' + rowId).find('input[name="jawaban[]"]').val(value);
        }

        function prefillEditData() {
            const raw = $('#data-soal').val();
            if (!raw) return;
            let existing;
            try {
                existing = JSON.parse(raw);
            } catch (e) {
                return;
            }
            if (!existing || !existing.id_level || !existing.id_soal) return;

            $('#id_konversi').val(existing.id);
            // Set level select (tanpa trigger clear manual)
            $('#level_id').val(existing.id_level).trigger('change.select2');

            // Muat daftar soal sesuai level, pilih soal yang cocok
            $.ajax({
                url: APP_URL +
                    'konversi/getSoalByLevel', // pastikan route name/URL sesuai (konversi.getSoalByLevel)
                type: 'GET',
                data: {
                    level_id: existing.id_level,
                    soal_id: existing.id_soal
                },
                success: function(list) {
                    $('#soal_id').empty().append('<option value="" disabled>Pilih Soal</option>');
                    (list || []).forEach(s => {
                        $('#soal_id').append(
                            `<option value="${s.id}" ${s.id === existing.id_soal ? 'selected':''}>${s.judul}</option>`
                        );
                    });

                    // Ambil detail soal untuk render kunci
                    $.ajax({
                        url: APP_URL + 'soal/' + existing.id_soal,
                        type: 'GET',
                        success: function(detail) {
                            if (soalEditor) soalEditor.setData(detail.soal || '');
                            renderKonversi(detail); // ini membuat baris dasar kosong

                            // Isi bobot & output lama
                            $('#bobot').val(existing.bobot ?? 0);
                            $('#output').val(existing.output ?? '');

                            // Flatten jawaban lama (array of object { "1": "code..." })
                            const oldAnswers = Array.isArray(existing.jawaban) ?
                                existing.jawaban.map(o => {
                                    if (!o) return '';
                                    const key = Object.keys(o)[0];
                                    return o[key] ?? '';
                                }) : [];

                            // Prefill ke baris dasar terlebih dahulu
                            const baseInputs = $('#col-input').find('input[name="jawaban[]"]');
                            oldAnswers.forEach((val, idx) => {
                                if (idx < baseInputs.length) {
                                    $(baseInputs[idx]).val(val);
                                } else {
                                    addExtraKonversiRowWithValue(val);
                                }
                            });
                        }
                    });
                }
            });
        }

        // Jalankan prefill setelah semua siap
        $(document).ready(function() {
            prefillEditData();
        });
    </script>

    <script src="{{ asset('js/konversi/form.js') }}"></script>
@endpush
