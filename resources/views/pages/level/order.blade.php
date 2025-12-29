@extends('layouts.main')

@push('styles')
@endpush

@section('content')
    <div class="container-fluid px-4" id="form-level-container">
        <div class="row">
            <div class="col-12">
                <div class="bg-white rounded-4 shadow-sm p-8 mb-5">
                    <form action="" method="POST" id="form-level-order" onsubmit="updateOrderLevel(); return false;">
                        @csrf
                        <h4 class="mb-2">Urutkan Level</h4>
                        <p class="text-muted mb-5">Drag and drop untuk mengurutkan level sesuai keinginan Anda.</p>

                        <div class="row draggable-zone" id="sortable-level-list">
                            @foreach($dataLevel as $level)
                                <div class="col-12 draggable" data-id="{{ $level->id }}">
                                    <div class="card bg-light-primary rounded-3 mb-5 border-primary">
                                        <div class="card-body d-flex align-items-center py-3 px-5">
                                            <span class="draggable-handle me-5">
                                                <i class="ki-duotone ki-abstract-30 fs-2 text-dark">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </span>
                                            <div>
                                                <div class="d-flex align-items-center">
                                                    {{-- <span class="badge bg-warning me-3">{{ $level->order }}</span> --}}
                                                    <div class="fw-bold fs-5">{{ $level->name }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-end mt-5">
                            <a href="{{ route('level.index') }}" class="btn btn-sm btn-secondary me-2">Batal</a>
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
        // Inisialisasi SortableJS
        var container = document.getElementById('sortable-level-list');
        if (container) {
            new Sortable(container, {
                animation: 100,
                draggable: ".draggable",
                handle: ".draggable-handle",
                ghostClass: 'sortable-chosen'
            });
        }
    </script>

    <script>
        var target = document.querySelector("#kt_app_body");
        var blockUI = new KTBlockUI(target);
        var APP_URL = window.APP_URL || "/";

        function updateOrderLevel() {
            let orderData = [];
            document.querySelectorAll('.draggable').forEach(function(item, index) {
                orderData.push({
                    id: item.getAttribute('data-id'),
                    order: index + 1
                });
            });

            Swal.fire({
                title: "Simpan Urutan Level?",
                text: "Apakah Anda yakin ingin menyimpan urutan level ini?",
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
                        url: "{{ url('level/update-order') }}",
                        type: "POST",
                        data: {
                            order: orderData,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (window.blockUI) blockUI.release();
                            if (response.success) {
                                Swal.fire({
                                    text: "Urutan level berhasil disimpan.",
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "OK",
                                    customClass: {
                                        confirmButton: "btn btn-primary",
                                    },
                                }).then(() => {
                                    window.location.href = "{{ route('level.index') }}";
                                });
                            } else {
                                Swal.fire({
                                    text: response.message || "Gagal menyimpan urutan level.",
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