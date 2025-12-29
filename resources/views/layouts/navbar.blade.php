@php
    $isAdmin = Auth::check() && Auth::user()->is_admin;
@endphp

<div class="app-navbar-item d-flex align-items-stretch flex-lg-grow-1">
    <div id="kt_header" class="header d-flex align-items-center w-lg-400px">
        <h2>{{ $title ?? 'Menu' }}</h2>
    </div>
</div>

@unless($isAdmin)
<div class="app-navbar-item ms-1 ms-md-3 me-3">
    <div class="btn btn-icon btn-custom btn-color-gray-600 btn-active-light btn-active-color-primary position-relative"
        data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent"
        data-kt-menu-placement="bottom-end">
        <span class="position-absolute top-0 start-50 translate-middle  badge badge-circle badge-warning w-20px h-20px mt-2 ms-4" id="countPencapaianOuter"></span>
        <i class="ki-outline ki-messages fs-2x text-primary"></i>
    </div>
    <div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px" data-kt-menu="true"
        id="kt_menu_notifications">
        <div class="d-flex flex-column bgi-no-repeat rounded-top"
            style="background-image:url('{{ asset("assets/media/misc/menu-header-bg.jpg") }}'); background-position: center bottom; background-size: cover;">
            <h3 class="text-white fw-semibold px-9 mt-10 mb-6">Pencapaian
            <span class="fs-8 opacity-75 ps-3"><span id="countPencapaian"></span> Pencapaian</span>
            </h3>
        </div>
        <div class="scroll-y mh-325px my-5 ps-8 pe-5">
            
        </div>
        <div class="py-2 text-center border-top">
            <a href="{{ route('pencapaian.index') }}"
                class="btn btn-color-gray-600 btn-active-color-warning fs-6">Lihat Semua Pencapaian
                <i class="ki-outline ki-arrow-right fs-5"></i></a>
        </div>
    </div>
</div>
@endunless

@if($isAdmin)
<div class="app-navbar-item ms-1 ms-md-3 me-10">
    <div class="form-check form-switch form-check-custom form-check-warning">
        <label class="form-check-label me-3 text-dark fs-6 mb-1 fw-semibold" for="toggle-maintenance-mahasiswa">
            Mode Pemeliharaan Mahasiswa
        </label>
        <input class="form-check-input w-50px" type="checkbox" id="toggle-maintenance-mahasiswa" {{ \App\Models\Setting::getValue('maintenance_mahasiswa', '0') === '1' ? 'checked' : '' }}>
    </div>
</div>
@endif

<div class="app-navbar-item ms-1 ms-md-3" id="kt_header_user_menu_toggle">
    <div class="cursor-pointer symbol symbol-circle symbol-35px symbol-md-45px border border-1"
        data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent"
        data-kt-menu-placement="bottom-end">
        <img alt="avatar" src="{{ auth()->user()->avatar ? asset('assets/media/avatars/' . auth()->user()->avatar) : asset('assets/media/avatars/blank.png') }}" />
    </div>
    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px"
        data-kt-menu="true">
        <div class="menu-item px-3">
            <div class="menu-content d-flex align-items-center px-3">
                <div class="symbol symbol-50px me-5">
                    <img alt="Logo" src="{{ auth()->user()->avatar ? asset('assets/media/avatars/' . auth()->user()->avatar) : asset('assets/media/avatars/blank.png') }}" />
                </div>
                <div class="d-flex flex-column">
                    <div class="fw-bold d-flex align-items-center fs-5">
                        {{ auth()->user()->name }}
                    </div>
                    <a href="#" class="fw-semibold text-muted text-hover-primary fs-7">
                        {{ auth()->user()->email }}
                    </a>
                </div>
            </div>
        </div>
        @unless($isAdmin)
        <div class="menu-item px-5 my-1">
            <a href="{{ route('mahasiswa.profile') }}" class="menu-link px-5">Pengaturan Akun</a>
        </div>
        @endunless
        <div class="menu-item px-5">
            <a href="{{ route('logout') }}" class="menu-link px-5" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Fungsi untuk render notifikasi pencapaian di navbar
        function renderPencapaianNotif(items) {
            let html = '';
            if (!items.length) {
                html = `<div class="text-center text-muted py-4">Belum ada pencapaian yang bisa diklaim.</div>`;
            } else {
                items.forEach(item => {
                    html += `
                    <div class="d-flex flex-stack py-3 border-bottom pencapaian-notif-item"
                         style="cursor:pointer"
                         data-category="${item.category}">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-35px me-4">
                                <img src="${item.img || '/assets/media/img/badge_sempurna.png'}" alt="badge" class="rounded-2" style="width:32px;height:32px;">
                            </div>
                            <div>
                                <div class="fw-bold text-gray-800 fs-7">${item.name}</div>
                                <div class="text-gray-500 fs-8">${item.desc || ''}</div>
                            </div>
                        </div>
                    </div>
                    `;
                });
            }
            $('#kt_menu_notifications .scroll-y').html(html);
        }
        
        // Fungsi AJAX untuk ambil pencapaian notif
        function loadPencapaianNotif() {
            $.ajax({
                url: APP_URL + "dashboard/pencapaian-list",
                type: "GET",
                dataType: "json",
                success: function (data) {
                    if (data.length) {
                        $('#countPencapaian').text(data[0].countPencapaian || 0);
                        $('#countPencapaianOuter').text(data[0].countPencapaian || 0);
                        // Filter status 1 (claimable) saja, ambil maksimal 5 teratas
                        const claimable = data.filter(item => Number(item.status) === 1);
                        renderPencapaianNotif(claimable);
                    }
                },
                error: function () {
                    $('#kt_menu_notifications .scroll-y').html('<div class="text-center text-danger py-4">Gagal memuat notifikasi pencapaian.</div>');
                }
            });
        }
        
        $(document).on('click', '.pencapaian-notif-item', function() {
            var category = $(this).data('category') || 'badge';
            window.location.href = APP_URL + 'pencapaian?tab=' + encodeURIComponent(category);
        });
        
        // Atau panggil sekali saat halaman siap (jika ingin auto-load)
        $(document).ready(loadPencapaianNotif);
    </script>

    // Toggle Maintenance Mahasiswa
    <script>
        $('#toggle-maintenance-mahasiswa').on('change', function() {
            const status = $(this).is(':checked') ? 1 : 0;
            $.ajax({
                url: APP_URL + "dashboard/toggle-maintenance",
                type: "POST",
                data: { status, _token: "{{ csrf_token() }}" },
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Status diperbarui',
                        text: status ? 'Maintenance diaktifkan.' : 'Maintenance dinonaktifkan.'
                    });
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'Gagal memperbarui status!' });
                }
            });
        });
    </script>
@endpush