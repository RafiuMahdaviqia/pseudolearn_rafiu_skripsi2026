@extends('layouts.main')

@push('styles')
@endpush

@section('content')
    <div class="container-fluid px-4" id="banksoalkonversi-order-container">
        <div class="row">
            <div class="col-12">
                <div class="bg-white rounded-4 shadow-sm p-8 mb-5">
                    <form action="" method="POST" id="form-level-order" onsubmit="updateOrderSoal(); return false;">
                        @csrf
                        <div class="row align-items-start mb-5">
                            <div class="col-md-8">
                                <h4 class="mb-2">Urutkan Bank Soal Konversi</h4>
                                <p class="text-muted mb-0">Drag and drop untuk mengurutkan Bank Soal Konversi sesuai keinginan Anda.</p>
                            </div>
                            <div class="col-md-4">
                                <select id="level_select" name="level_id" class="form-select form-select-sm" data-control="select2" data-hide-search="true" data-allow-clear="false" required>
                                    <option value="">Pilih Level</option>
                                    @foreach($dataLevel as $level)
                                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row draggable-zone" id="sortable-banksoal-list">
                        </div>

                        <div class="d-flex justify-content-end mt-5">
                            <a href="{{ route('bank-soal-konversi.index') }}" class="btn btn-sm btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-sm btn-primary">Simpan Urutan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- SortableJS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        var bankSoalSortable = null;

        function initSortableIfNeeded() {
            const el = document.getElementById('sortable-banksoal-list');
            if (!el) return;
            if (bankSoalSortable) {
                // optional: update if needed
                return;
            }
            bankSoalSortable = new Sortable(el, {
                animation: 120,
                draggable: ".draggable",
                handle: ".draggable-handle",
                ghostClass: 'sortable-chosen',
                dragClass: 'sortable-drag'
            });
        }

        $('#level_select').on('change', function() {
            var selectedLevelId = $(this).val();
            const $list = $('#sortable-banksoal-list');
            $list.empty();
            bankSoalSortable = null; // reset agar fresh untuk level baru

            if (!selectedLevelId) {
                return;
            }

            $.ajax({
                url: APP_URL + 'bank-soal-konversi/getByLevelForOrder',
                type: 'GET',
                data: {
                    level_id: selectedLevelId,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (!Array.isArray(response) || response.length === 0) {
                        $list.html('<div class="col-12"><div class="alert alert-warning mb-0">Tidak ada bank soal konversi.</div></div>');
                        return;
                    }

                    response.forEach(element => {
                        $list.append(`
                            <div class="col-12 draggable" data-id="${element.id}">
                                <div class="card bg-light-primary rounded-3 mb-5 border-primary">
                                    <div class="card-body d-flex align-items-center py-3 px-5">
                                        <span class="draggable-handle me-5 cursor-move">
                                            <i class="ki-duotone ki-abstract-30 fs-2 text-dark">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold fs-5 mb-0">${element.judul}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                    });

                    initSortableIfNeeded();
                },
                error: function(xhr) {
                    Swal.fire({
                        text: xhr.responseJSON?.message || "Terjadi kesalahan saat memuat bank soal konversi untuk level yang dipilih.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "OK",
                        customClass: { confirmButton: "btn btn-primary" }
                    });
                }
            });
        });

        // Jika ingin load langsung level pertama (opsional):
        // $('#level_select').val($('#level_select option:eq(1)').val()).trigger('change');
    </script>

    <script>
        var target = document.querySelector("#kt_app_body");
        var blockUI = new KTBlockUI(target);
        var APP_URL = window.APP_URL || "/";

        function updateOrderSoal() {
            let orderData = [];
            document.querySelectorAll('.draggable').forEach(function(item, index) {
                orderData.push({
                    id: item.getAttribute('data-id'),
                    order: index + 1
                });
            });

            Swal.fire({
                title: "Simpan Urutan Bank Soal Konversi?",
                text: "Apakah Anda yakin ingin menyimpan urutan Bank Soal Konversi?",
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
                    if (window.blockUI) blockUI.block();
                    $.ajax({
                        url: APP_URL + 'bank-soal-konversi/saveOrder',
                        type: "POST",
                        data: {
                            order: orderData,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (window.blockUI) blockUI.release();
                            if (response.success) {
                                Swal.fire({
                                    text: "Urutan Bank Soal Konversi berhasil disimpan.",
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "OK",
                                    customClass: {
                                        confirmButton: "btn btn-primary",
                                    },
                                }).then(() => {
                                    $('#level_select').trigger('change'); // reload soal untuk level yg sama
                                });
                            } else {
                                Swal.fire({
                                    text: response.message || "Gagal menyimpan urutan Bank Soal Konversi.",
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "OK",
                                    customClass: {
                                        confirmButton: "btn btn-primary",
                                    },
                                });
                            }
                        },
                        error: function(xhr) {
                            if (window.blockUI) blockUI.release();
                            Swal.fire({
                                text: xhr.responseJSON?.message || "Terjadi kesalahan saat menyimpan urutan level.",
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
                    if (window.blockUI) blockUI.release();
                }
            });
        }
    </script>
@endpush