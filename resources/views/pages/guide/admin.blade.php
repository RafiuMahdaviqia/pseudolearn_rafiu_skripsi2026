@extends('layouts.main')

@push('styles')
    <style>
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4" id="guide-container">
    <div class="row">
        <div class="col-12 px-0">
            <div class="bg-white rounded-4 shadow-sm p-4">
                @csrf
                <div class="d-flex justify-content-end mb-8 gap-3">
                    <button class="btn btn-primary btn-sm" id="btn-add">
                        <i class="ki-outline ki-plus fs-4"></i>
                        Tambah
                    </button>
                    <button class="btn btn-success btn-sm" id="btn-save" onclick="saveGuideChanges()">
                        <i class="ki-outline ki-send fs-4"></i>
                        Simpan Perubahan
                    </button>
                </div>

                {{-- Card Container --}}
                <div id="guide-card-container" class="row row-cols-1 row-cols-md-2 g-4 mb-4">
                    {{-- Card items akan di-render via JS --}}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Card -->
<div class="modal fade" id="modalEditCard" tabindex="-1" aria-labelledby="modalEditCardLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formEditCard" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalEditCardLabel">Edit Panduan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="editTitle" class="form-label">Judul</label>
            <input type="text" class="form-control" id="editTitle" name="title" required>
          </div>
          <div class="mb-3">
            <label for="editDesc" class="form-label">Deskripsi</label>
            <textarea class="form-control" id="editDesc" name="desc" rows="3" required></textarea>
          </div>
          <div class="mb-3">
            <label for="editImage" class="form-label">Gambar (opsional)</label>
            <input type="file" class="form-control" id="editImage" name="image" accept="image/*">
            <div id="editImagePreview" class="mt-2"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
    var guides = [];
    var editIdx = null;
    var APP_URL = window.APP_URL || "/";

    document.addEventListener('DOMContentLoaded', function () {
        // 🔹 Ambil data dari server ketika halaman load
        fetch(APP_URL + "guide/getData")
            .then(res => res.json())
            .then(res => {
                // Bisa return array langsung atau object {success, data}
                if (Array.isArray(res)) {
                    guides = res;
                } else if (res.success) {
                    guides = res.data;
                }
                renderGuideCards();
            });

        // Tambah card dummy
        document.getElementById('btn-add').addEventListener('click', function () {
            const nextNum = guides.length + 1;
            guides.push({
                title: `Panduan ${nextNum}`,
                desc: `Deskripsi panduan ${nextNum}`,
                img: null
            });
            renderGuideCards();
        });

        // Preview gambar ketika upload di modal
        document.getElementById('editImage').addEventListener('change', function (e) {
            const file = e.target.files[0];
            const preview = document.getElementById('editImagePreview');
            if (file) {
                const reader = new FileReader();
                reader.onload = function (ev) {
                    preview.innerHTML =
                        `<img src="${ev.target.result}" class="img-fluid rounded" style="max-height:120px;">`;
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = '';
            }
        });


        // Submit form edit modal
        document.getElementById('formEditCard').addEventListener('submit', function (e) {
            e.preventDefault();
            if (editIdx !== null) {
                guides[editIdx].title = document.getElementById('editTitle').value;
                guides[editIdx].desc = document.getElementById('editDesc').value;

                const fileInput = document.getElementById('editImage');
                if (fileInput.files && fileInput.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function (ev) {
                        // simpan base64 supaya bisa preview di card & terkirim ke backend
                        guides[editIdx].img = ev.target.result;
                        renderGuideCards();
                        bootstrap.Modal.getInstance(document.getElementById('modalEditCard')).hide();
                    };
                    reader.readAsDataURL(fileInput.files[0]);
                } else {
                    renderGuideCards();
                    bootstrap.Modal.getInstance(document.getElementById('modalEditCard')).hide();
                }
            }
        });
    });

    function renderGuideCards() {
        const container = document.getElementById('guide-card-container');
        container.innerHTML = '';

        // Sort by order/urutan kalau ada
        const sortedGuides = guides
            .map((g, i) => ({ ...g, _origIndex: i }))
            .sort((a, b) => {
                const ao = Number(a.order ?? a.urutan ?? a.sort ?? a._origIndex);
                const bo = Number(b.order ?? b.urutan ?? b.sort ?? b._origIndex);
                return ao - bo;
            });

        if (sortedGuides.length === 0) {
            container.innerHTML = '<div class="col"><div class="alert alert-warning">Tidak ada panduan.</div></div>';
            return;
        }

        sortedGuides.forEach((guide) => {
            const displayIdx = guide._origIndex; // index asli di guides array

            const imgSrc = guide.img
                ? (guide.img.startsWith("data:image") ? guide.img : APP_URL + guide.img)
                : null;

            const card = document.createElement('div');
            card.className = 'col';
            card.innerHTML = `
                <div class="card h-100 shadow-sm border border-2">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0 text-center flex-grow-1">${guide.judul || guide.title}</h5>
                            <span class="badge bg-primary ms-2">#${guide.order ?? (displayIdx + 1)}</span>
                        </div>
                        ${imgSrc ? `
                            <div class="text-center my-4">
                                <img src="${imgSrc}" 
                                    class="img-fluid rounded mx-auto d-block" 
                                    style="max-height:120px;object-fit:cover;">
                            </div>
                        ` : ''}
                        <p class="card-text flex-grow-1 text-center">${guide.desc ?? ''}</p>
                        <div class="mt-2 d-flex gap-2">
                            <button class="btn btn-outline btn-outline-info btn-sm btn-edit-card" data-idx="${displayIdx}">
                                <i class="ki-outline ki-pencil fs-5"></i> Edit
                            </button>
                            <button class="btn btn-outline btn-outline-danger btn-sm btn-delete-card" data-idx="${displayIdx}">
                                <i class="ki-outline ki-trash fs-5"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(card);
        });

        // Event hapus
        container.querySelectorAll('.btn-delete-card').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const idx = parseInt(this.getAttribute('data-idx'), 10);

                Swal.fire({
                    title: 'Hapus Card Panduan?',
                    text: 'Card akan terhapus',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        guides.splice(idx, 1);
                        renderGuideCards();
                        Swal.fire({
                            title: 'Terhapus!',
                            text: 'Panduan berhasil dihapus.',
                            icon: 'success',
                            timer: 1200,
                            showConfirmButton: false
                        });
                    }
                });
            });
        });

        // Event edit
        container.querySelectorAll('.btn-edit-card').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                editIdx = parseInt(this.getAttribute('data-idx'), 10);
                const guide = guides[editIdx];

                if (guide.id) {
                    fetch(APP_URL + `guide/getDataById/${guide.id}`)
                        .then(res => res.json())
                        .then(res => {
                            const data = res?.data ?? res;
                            openEditModal(data, editIdx);
                        });
                } else {
                    openEditModal(guide, editIdx);
                }
            });
        });
    }

    function openEditModal(guide, idx) {
        document.getElementById('editTitle').value = guide.judul ?? guide.title ?? '';
        document.getElementById('editDesc').value = guide.desc ?? '';
        document.getElementById('editImage').value = '';

        if (guide.img) {
            const imgSrc = guide.img.startsWith("data:image")
                ? guide.img
                : APP_URL + guide.img;

            document.getElementById('editImagePreview').innerHTML =
                `<img src="${imgSrc}" class="img-fluid rounded" style="max-height:120px;">`;
        } else {
            document.getElementById('editImagePreview').innerHTML = '';
        }

        const modal = new bootstrap.Modal(document.getElementById('modalEditCard'));
        modal.show();
    }

    function saveGuideChanges() {
        // 🔹 Urutkan dulu sesuai displayIdx (hasil render)
        const container = document.getElementById('guide-card-container');
        const cardEls = container.querySelectorAll('.btn-edit-card');

        // cardEls urut sesuai tampilan
        let newGuides = [];
        cardEls.forEach((btn, idx) => {
            const gIdx = parseInt(btn.getAttribute('data-idx'), 10);
            const guide = guides[gIdx];
            // kasih properti order sesuai urutan tampilan
            guide.order = idx + 1;
            newGuides.push(guide);
        });

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        // console.log('Menyimpan data:', newGuides);
        Swal.fire({
            title: 'Simpan Perubahan?',
            text: 'Perubahan akan disimpan',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, simpan',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            focusCancel: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: APP_URL + "guide/saveData",
                    type: "POST",
                    data: JSON.stringify({ data: newGuides }), // 🔹 kirim data yang sudah diurutkan
                    processData: false,
                    contentType: "application/json",
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function (response) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Data panduan berhasil disimpan.',
                            icon: 'success',
                            timer: 1200,
                            showConfirmButton: false
                        });

                        // Refresh ulang dari server
                        fetch(APP_URL + "guide/getData")
                            .then(res => res.json())
                            .then(res => {
                                if (Array.isArray(res)) {
                                    guides = res;
                                } else if (res.success) {
                                    guides = res.data;
                                }
                                renderGuideCards();
                            });
                    },
                    error: function (xhr) {
                        Swal.fire({
                            text: xhr.responseJSON?.message || "Terjadi kesalahan sistem.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "OK",
                            customClass: {
                                confirmButton: "btn btn-primary",
                            },
                        });
                    }
                });
            }
        });
    }


</script>
@endpush