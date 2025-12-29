@php
    use Illuminate\Support\Facades\Auth;
    use App\Models\Mahasiswa;

    $userId = Auth::id();
    $user = Auth::user();
    $id_mahasiswa = Mahasiswa::where('id_user', $userId)->first();
    $name_mahasiswa = $id_mahasiswa ? $id_mahasiswa->name : '';
@endphp

@extends('layouts.main')

@push('styles')
    <style>
        .clickable-avatar {
            cursor: pointer;
        }

        .custom-modal-header {
            background-color: #F39C12;
            border-bottom: none;
            min-height: 40px;
        }

        .avatar-option:hover {
            /* opacity: 1;
                transform: scale(1.05);
                transition: 0.2s; */
            filter: grayscale(0%);
            transform: scale(1.05);
        }

        .avatar-option.selected {
            border: 3px solid #28a745 !important;
            background-color: #e6fff1;
            box-shadow: 0 0 5px #28a745;
            filter: grayscale(0%);
        }

        .avatar-option {
            filter: grayscale(70%);
            transition: 0.2s;
        }

        .avatar-upload {
            cursor: pointer;
        }

        .avatar-upload img {
            border: 2px dashed #ccc;
            padding: 5px;
            background-color: #f8f8f8;
        }

        .avatar-upload .small {
            font-size: 0.8rem;
            color: #444;
        }

        .modal-footer .btn {
            font-weight: bold;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4" id="profile-container">
        <div class="row">
            <div class="col-12 px-0">
                <div class="bg-white rounded-4 shadow-sm p-8">
                    <form action="" method="POST" enctype="multipart/form-data" id="profile-form">
                        @csrf
                        <input type="hidden" id="id-mahasiswa" value="{{ $id_mahasiswa->id ?? '' }}">
                        <div class="card-body p-9">
                            <div class="row mb-10">
                                <label class="col-lg-4 col-form-label fw-semibold fs-6">Avatar</label>
                                <div class="col-lg-8">
                                    <div class="clickable-avatar image-input image-input-outline" data-bs-toggle="modal" data-bs-target="#avatarModal">
                                        <div class="image-input-wrapper w-125px h-125px" id="avatar-preview"
                                            style="background-image: url('{{ $user->avatar ? asset('assets/media/avatars/' . $user->avatar) : asset('assets/media/avatars/blank.png') }}')">
                                        </div>
                                    </div>
                                    <!-- Input file tersembunyi -->
                                    <input type="file" name="avatar" id="avatar-input" hidden accept=".png, .jpg, .jpeg, .webp">
                                    <!-- Hidden input untuk avatar default -->
                                    <input type="hidden" name="avatar_default" id="avatar-default-url">
                                    <div class="form-text">Allowed file types: png, jpg, jpeg, webp. Max size: 2MB.</div>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label required fw-semibold fs-6">Nama</label>
                                <div class="col-lg-8 fv-row">
                                    <input type="text" name="nama" id="nama" class="form-control" placeholder="Nama lengkap" value="{{ $name_mahasiswa }}" />
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-semibold fs-6">New Password</label>
                                <div class="col-lg-8 fv-row">
                                    <div class="input-group">
                                        <input type="password" name="new_password" class="form-control" placeholder="Enter new password" aria-describedby="basic-addon2" id="new_password"/>
                                        <span class="input-group-text rounded-end" id="basic-addon2">
                                            <i class="ki-outline ki-eye fs-2"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-semibold fs-6">Confirm Password</label>
                                <div class="col-lg-8 fv-row">
                                    <div class="input-group">
                                        <input type="password" name="confirm_password" class="form-control" placeholder="Confirm new password" aria-describedby="basic-addon2" id="confirm_password"/>
                                        <span class="input-group-text rounded-end" id="basic-addon2">
                                            <i class="ki-outline ki-eye fs-2"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="row mb-6">
                                <label class="col-lg-4 col-form-label fw-semibold fs-6">Email</label>
                                <div class="col-lg-8 fv-row">
                                    <input type="text" name="website"
                                        class="form-control"
                                        placeholder="Company website" value="214172088@gmail.com" />
                                </div>
                            </div> --}}
                        </div>
                        <div class="card-footer d-flex justify-content-end py-6 px-9">
                            <button type="reset" class="btn btn-light btn-active-light-primary me-2" onclick="resetForm()">Batal</button>
                            <button type="submit" class="btn btn-primary" id="submit-profile">Simpan</button>
                        </div>
                    </form>
                    <div class="modal fade" id="avatarModal" tabindex="-1" aria-labelledby="avatarModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content custom-avatar-modal">
                                <div class="modal-header custom-modal-header">
                                    <div class="flex-grow-1 text-center text-white fw-bold fs-3">Pilih Avatar</div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                                        style="filter: brightness(200%) invert(1); transform: scale(1.4);"></button>
                                </div>
                                <div class="modal-body d-flex flex-wrap gap-3 justify-content-center">

                                    <!-- Trigger Upload -->
                                    <div class="avatar-upload text-center" id="trigger-upload">
                                        <img src="{{ asset('assets/media/avatars/uploadfoto.png') }}"
                                            class="rounded-circle" width="80" height="80" style="border: 2px dashed #ccc; padding: 5px;">
                                        <div class="small mt-1">Upload Image</div>
                                    </div>

                                    <!-- Avatar Pilihan -->
                                    @for ($i = 1; $i <= 8; $i++)
                                        <img src="{{ asset('assets/media/avatars/avatar' . $i . '.webp') }}"
                                            class="avatar-option rounded-circle border"
                                            style="width: 80px; height: 80px; cursor: pointer;"
                                            data-avatar="{{ asset('assets/media/avatars/avatar' . $i . '.webp') }}">
                                    @endfor
                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button class="btn btn-danger px-4" data-bs-dismiss="modal">Batal</button>
                                    <button class="btn btn-success px-4" id="confirm-avatar-btn">Pilih</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/mahasiswa/profile.js') }}"></script>
    <script>
        document.querySelectorAll('input[name="new_password"], input[name="confirm_password"]').forEach(function(input) {
            const parent = input.closest('.input-group');
            if (!parent) return;
            const toggleBtn = parent.querySelector('.input-group-text');
            if (!toggleBtn) return;
        
            toggleBtn.style.cursor = "pointer";
            toggleBtn.addEventListener('click', function() {
                if (input.type === "password") {
                    input.type = "text";
                    toggleBtn.querySelector('i').classList.remove('ki-eye');
                    toggleBtn.querySelector('i').classList.add('ki-eye-slash');
                } else {
                    input.type = "password";
                    toggleBtn.querySelector('i').classList.remove('ki-eye-slash');
                    toggleBtn.querySelector('i').classList.add('ki-eye');
                }
            });
        });

        let selectedAvatarUrl = null;
        let uploadedImageDataUrl = null;

        // === PILIH AVATAR DEFAULT ===
        document.querySelectorAll('.avatar-option').forEach(img => {
            img.addEventListener('click', function () {
                document.querySelectorAll('.avatar-option').forEach(el => el.classList.remove('selected'));
                this.classList.add('selected');
                selectedAvatarUrl = this.getAttribute('data-avatar');
                uploadedImageDataUrl = null;
                document.getElementById('avatar-default-url').value = selectedAvatarUrl;
                document.getElementById('avatar-input').value = ""; // Reset file input
            });
        });

        // === TRIGGER UPLOAD FILE ===
        document.getElementById('trigger-upload').addEventListener('click', () => {
            document.getElementById('avatar-input').click();
        });

        // === PREVIEW UPLOAD FILE ===
        document.getElementById('avatar-input').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    uploadedImageDataUrl = event.target.result;
                    selectedAvatarUrl = null;

                    document.querySelectorAll('.avatar-option').forEach(el => el.classList.remove('selected'));
                    document.getElementById('avatar-default-url').value = "";

                    // Preview sementara di modal
                    document.querySelector('#trigger-upload img').src = uploadedImageDataUrl;
                };
                reader.readAsDataURL(file);
            }
        });

        // === TOMBOL "PILIH" PADA MODAL ===
        document.getElementById('confirm-avatar-btn').addEventListener('click', function () {
            const previewDiv = document.getElementById('avatar-preview');

            if (selectedAvatarUrl) {
                previewDiv.style.backgroundImage = `url(${selectedAvatarUrl})`;
            } else if (uploadedImageDataUrl) {
                previewDiv.style.backgroundImage = `url(${uploadedImageDataUrl})`;
            } else {
                return;
            }

            const modal = bootstrap.Modal.getInstance(document.getElementById('avatarModal'));
            modal.hide();
        });
    </script>
@endpush
