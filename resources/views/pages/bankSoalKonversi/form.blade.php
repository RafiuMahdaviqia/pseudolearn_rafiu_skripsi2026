@extends('layouts.main')

@push('styles')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
    <style>
        .ck-editor__editable {
            min-height: 120px;
        }

        #jawaban-textarea {
            font-family: monospace;
            font-size: 13px;
            line-height: 1.8;
            resize: vertical;
            min-height: 180px;
        }

        /* Panel pseudocode */
        #panel-pseudocode {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
        }

        #panel-pseudocode .pseudo-header {
            background: #1a2744;
            color: #fff;
            padding: 10px 16px;
            font-size: 13px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        #panel-pseudocode .pseudo-body {
            padding: 12px 16px;
            background: #f8f9fa;
        }

        .pseudo-legend {
            display: flex;
            gap: 16px;
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
        }

        .pseudo-legend span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .pseudo-legend .dot {
            width: 10px;
            height: 10px;
            border-radius: 2px;
            flex-shrink: 0;
        }

        .pseudo-step {
            display: flex;
            align-items: stretch;
            gap: 10px;
            margin-bottom: 7px;
        }

        .pseudo-step-num {
            min-width: 28px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 600;
            color: #fff;
            background: #1a2744;
            border-radius: 6px;
            flex-shrink: 0;
        }

        .pseudo-step-text {
            flex: 1;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-family: monospace;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .pseudo-step-text.tipe-data {
            background: #e8f0fe;
            color: #1a2744;
            border-left: 3px solid #4a6fa5;
        }

        .pseudo-step-text.algoritma {
            background: #e6f4ea;
            color: #14532d;
            border-left: 3px solid #34a853;
        }

        .pseudo-badge {
            font-size: 10px;
            padding: 2px 7px;
            border-radius: 4px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .pseudo-badge.tipe {
            background: #4a6fa5;
            color: #fff;
        }

        .pseudo-badge.algo {
            background: #34a853;
            color: #fff;
        }

        .jawaban-preview-chip {
            display: inline-block;
            background: #1a2744;
            color: #e0e8ff;
            font-family: monospace;
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 6px;
            margin: 3px 4px 3px 0;
        }

        #btn-run-konversi.loading {
            pointer-events: none;
            opacity: 0.7;
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
                    <form method="POST" action="{{ route('bank-soal-konversi.store') }}" id="form-soal">
                        <input type="hidden" name="id" id="id_konversi">
                        <input type="hidden" name="data-soal" id="data-soal" value="{{ $data ? json_encode($data) : '' }}">
                        @csrf

                        {{-- Baris 1: Select Level, Soal --}}
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
                        </div>

                        {{-- Soal (readonly CKEditor) --}}
                        <div class="row mb-5">
                            <div class="fv-row col-md-12">
                                <label for="soal" class="form-label fs-5 required">Soal</label>
                                <textarea name="soal" id="soal" class="form-control" rows="3" readonly></textarea>
                            </div>
                        </div>

                        {{-- Panel Pseudocode — muncul otomatis saat soal dipilih --}}
                        <div class="row mb-5 d-none" id="row-pseudocode">
                            <div class="fv-row col-md-12">
                                <label class="form-label fs-5">Pseudocode</label>
                                <div id="panel-pseudocode">
                                    <div class="pseudo-header">
                                        <i class="ki-outline ki-code fs-6"></i>
                                        Langkah-langkah pseudocode soal ini
                                    </div>
                                    <div class="pseudo-body">
                                        <div class="pseudo-legend">
                                            <span>
                                                <span class="dot" style="background:#4a6fa5;"></span>
                                                Tipe Data / Variabel
                                            </span>
                                            <span>
                                                <span class="dot" style="background:#34a853;"></span>
                                                Algoritma / Langkah
                                            </span>
                                        </div>
                                        <div id="pseudo-steps"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Jawaban Kode Java --}}
                        <div class="row mb-5">
                            <div class="fv-row col-md-12">
                                <label for="jawaban-textarea" class="form-label fs-5 required">
                                    Jawaban (Kode Java)
                                </label>
                                <textarea
                                    name="jawaban"
                                    id="jawaban-textarea"
                                    class="form-control"
                                    {{-- placeholder="Kode Java akan muncul di sini setelah klik Jalankan Konversi, atau ketik langsung. Satu baris per baris kode." --}}
                                ></textarea>
                                {{-- <div class="form-text text-muted mt-1">
                                    Setiap baris = satu langkah kode Java di dalam <code>main()</code>.
                                    Baris kosong diabaikan.
                                </div> --}}
                            </div>
                        </div>

                        {{-- Preview chip acak --}}
                        <div class="row mb-5 d-none" id="row-preview-chip">
                            <div class="fv-row col-md-12">
                                <label class="form-label fs-6 text-muted">
                                    Preview tampilan drag &amp; drop siswa (urutan diacak)
                                </label>
                                <div id="preview-chip-wrap" class="p-3 bg-light rounded border"></div>
                            </div>
                        </div>

                        {{-- Output --}}
                        <div class="row mb-10">
                            <div class="fv-row col-md-12">
                                <label for="output" class="form-label fs-5 required">Output</label>
                                <textarea name="output" id="output" class="form-control" rows="3" readonly required></textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <div>
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

    <script>
        let soalEditor;
        var APP_URL = window.APP_URL || "/";

        // ─── CKEditor (readonly) ───────────────────────────────────────────────────
        ClassicEditor.create(document.querySelector('#soal'), {
            readOnly: true
        }).then(editor => {
            soalEditor = editor;
            editor.enableReadOnlyMode('soal');
            editor.ui.view.editable.element.style.height = '120px';
        });

        // ─── Level change → load soal list ────────────────────────────────────────
        $('#level_id').on('change', function () {
            soalEditor.setData('');
            clearAll();
            const levelId = $(this).val();
            if (!levelId) {
                $('#soal_id').empty().append('<option value="" selected disabled>Pilih Soal</option>');
                return;
            }
            $.get(APP_URL + 'konversi/getSoalByLevel', { level_id: levelId }, function (data) {
                $('#soal_id').empty().append('<option value="" selected disabled>Pilih Soal</option>');
                $.each(data, function (_, soal) {
                    $('#soal_id').append(`<option value="${soal.id}">${soal.judul}</option>`);
                });
            });
        });

        // ─── Soal change → load detail + tampilkan pseudocode ─────────────────────
        $('#soal_id').on('change', function () {
            console.log('Soal dipilih:', $(this).val());
            const soalId = $(this).val();
            clearAll();
            if (!soalId) {
                soalEditor.setData('');
                return;
            }
            $.get(APP_URL + 'soal/' + soalId, function (data) {
                soalEditor.setData(data.soal || '');
                $('#output').val(data.output || '');
                renderPseudocode(data);

                // Jika sudah ada jawaban tersimpan di soal, tampilkan langsung
                if (data.jawaban) {
                    setJawaban(data.jawaban);
                }
            });
        });

        // ─── Render pseudocode ─────────────────────────────────────────────────────
        function renderPseudocode(data) {
            const toArray = (value) => {
                if (Array.isArray(value)) return value;
                if (value && typeof value === 'object') return [value];
                if (typeof value === 'string') {
                    const trimmed = value.trim();
                    if (!trimmed) return [];
                    try {
                        const parsed = JSON.parse(trimmed);
                        if (Array.isArray(parsed)) return parsed;
                        if (parsed && typeof parsed === 'object') return [parsed];
                    } catch (e) {
                        return [];
                    }
                }
                return [];
            };

            const tipeData = toArray(data.kunci_tipe_data);
            const algoritma = toArray(data.kunci_algoritma);

            // Gabungkan: tipe data dulu, lalu algoritma
            const combined = [
                ...tipeData.map(item => ({ kind: 'tipe_data', data: item })),
                ...algoritma.map(item => ({ kind: 'algoritma', data: item })),
            ];

            if (combined.length === 0) {
                $('#row-pseudocode').addClass('d-none');
                return;
            }

            const container = $('#pseudo-steps').empty();

            combined.forEach((entry, index) => {
                const num = index + 1;
                let label = '';
                let klass = '';
                let badge = '';

                if (entry.kind === 'tipe_data') {
                    const variabel = entry.data.variabel ?? '-';
                    const tipe     = entry.data.tipe_data ?? '-';
                    label = `Variabel: <strong>${escHtml(variabel)}</strong>, Tipe: <strong>${escHtml(tipe)}</strong>`;
                    klass = 'tipe-data';
                    badge = '<span class="pseudo-badge tipe">Tipe Data</span>';
                } else {
                    label = escHtml(entry.data.langkah ?? '');
                    klass = 'algoritma';
                    badge = '<span class="pseudo-badge algo">Algoritma</span>';
                }

                container.append(`
                    <div class="pseudo-step">
                        <div class="pseudo-step-num">${num}</div>
                        <div class="pseudo-step-text ${klass}">
                            <span>${label}</span>
                            ${badge}
                        </div>
                    </div>
                `);
            });

            $('#row-pseudocode').removeClass('d-none');
        }

        // ─── Clear semua section ───────────────────────────────────────────────────
        function clearAll() {
            $('#jawaban-textarea').val('');
            $('#output').val('');
            $('#preview-chip-wrap').empty();
            $('#row-preview-chip').addClass('d-none');
            $('#pseudo-steps').empty();
            $('#row-pseudocode').addClass('d-none');
        }

        // ─── Set jawaban ke textarea + render preview chip ─────────────────────────
        function setJawaban(plainText) {
            $('#jawaban-textarea').val(plainText);
            renderPreviewChip(plainText);
        }

        // ─── Render preview chip diacak ────────────────────────────────────────────
        function renderPreviewChip(plainText) {
            const lines = plainText.split('\n').map(l => l.trim()).filter(l => l.length > 0);
            if (lines.length === 0) {
                $('#row-preview-chip').addClass('d-none');
                return;
            }
            const shuffled = [...lines].sort(() => Math.random() - 0.5);
            const wrap = $('#preview-chip-wrap').empty();
            shuffled.forEach(line => {
                wrap.append(`<span class="jawaban-preview-chip">${escHtml(line)}</span>`);
            });
            $('#row-preview-chip').removeClass('d-none');
        }

        // Update preview tiap kali textarea diketik manual
        $('#jawaban-textarea').on('input', function () {
            renderPreviewChip($(this).val());
        });

        // ─── Jalankan Konversi (jalankan kode Java dari textarea jawaban) ─────────
        async function runKonversi() {
            const levelId = $('#level_id').val();
            const soalId  = $('#soal_id').val();
            const jawabanText = $('#jawaban-textarea').val().trim();

            if (!levelId || !soalId) {
                Swal.fire({ icon: 'warning', text: 'Pilih soal terlebih dahulu.', confirmButtonText: 'OK' });
                return;
            }
            if (!jawabanText) {
                Swal.fire({ icon: 'warning', text: 'Isi jawaban kode Java terlebih dahulu.', confirmButtonText: 'OK' });
                return;
            }

            const btn = document.getElementById('btn-run-konversi');
            btn.classList.add('loading');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menjalankan...';

            const codes = jawabanText
                .split('\n')
                .map(line => line.trim())
                .filter(line => line.length > 0)
                .map(line => ({ value: line }));

            try {
                const res = await $.ajax({
                    type: 'POST',
                    url: APP_URL + 'bank-soal-konversi/runJava',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        level_id: levelId,
                        soal_id: soalId,
                        codes: codes
                    }
                });

                const out = (res && typeof res.output !== 'undefined') ? res.output : '';
                $('#output').val(String(out).trim());

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Dijalankan',
                    html: `<pre style="white-space:pre-wrap;">${escHtml(String(out))}</pre>`,
                    confirmButtonText: 'OK'
                });
            } catch (xhr) {
                Swal.fire({
                    icon: 'error',
                    text: xhr?.responseJSON?.message || 'Gagal menjalankan kode Java.',
                    confirmButtonText: 'OK'
                });
            } finally {
                btn.classList.remove('loading');
                btn.innerHTML = '<i class="ki-outline ki-send fs-6"></i> Jalankan Konversi';
            }
        }

        // ─── Helper escape HTML ────────────────────────────────────────────────────
        function escHtml(s) {
            return String(s)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        // ─── Submit form ───────────────────────────────────────────────────────────
        $('#submit-form-soal').on('click', function () {
            const jawaban = $('#jawaban-textarea').val().trim();
            const output  = $('#output').val().trim();
            const idKonversi = $('#id_konversi').val().trim();

            if (!jawaban) {
                Swal.fire({ icon: 'warning', text: 'Jawaban belum diisi.', confirmButtonText: 'OK' });
                return;
            }
            if (!output) {
                Swal.fire({ icon: 'warning', text: 'Output belum diisi.', confirmButtonText: 'OK' });
                return;
            }

            if (idKonversi) {
                $('#form-soal').attr('action', APP_URL + 'bank-soal-konversi/update/' + idKonversi);
            } else {
                $('#form-soal').attr('action', APP_URL + 'bank-soal-konversi/store');
            }

            $('#form-soal').submit();
        });

        // ─── Prefill data edit ─────────────────────────────────────────────────────
        function prefillEditData() {
            const raw = $('#data-soal').val();
            if (!raw) return;

            let existing;
            try { existing = JSON.parse(raw); } catch (e) { return; }
            if (!existing?.id_level || !existing?.id_soal) return;

            $('#id_konversi').val(existing.id);
            $('#level_id').val(existing.id_level).trigger('change.select2');

            $.get(APP_URL + 'konversi/getSoalByLevel', { level_id: existing.id_level }, function (list) {
                $('#soal_id').empty().append('<option value="" disabled>Pilih Soal</option>');
                (list || []).forEach(s => {
                    $('#soal_id').append(
                        `<option value="${s.id}" ${s.id === existing.id_soal ? 'selected' : ''}>${s.judul}</option>`
                    );
                });

                $.get(APP_URL + 'soal/' + existing.id_soal, function (detail) {
                    if (soalEditor) soalEditor.setData(detail.soal || '');
                    $('#output').val(existing.output ?? detail.output ?? '');

                    // Render pseudocode dari detail soal
                    renderPseudocode(detail);

                    // Prefill jawaban — support format lama & baru
                    let jawabanText = '';

                    if (typeof existing.jawaban === 'string') {
                        // Format baru: plain text
                        jawabanText = existing.jawaban;
                    } else if (Array.isArray(existing.jawaban)) {
                        // Format lama: [{"1": "code"}, ...]
                        jawabanText = existing.jawaban
                            .map(o => o ? Object.values(o)[0] ?? '' : '')
                            .filter(l => l.trim())
                            .join('\n');
                    }

                    if (jawabanText) setJawaban(jawabanText);
                });
            });
        }

        $(document).ready(function () {
            prefillEditData();
        });
    </script>
@endpush
