<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <base href="" />
    <title>PseudoLearn</title>
    <meta charset="utf-8" />
    <meta name="description"
        content="Pseudolearn adalah aplikasi pembelajaran dasar pemrograman berbasis pseudocode yang dirancang untuk membantu siswa memahami konsep dasar pemrograman dengan cara yang interaktif." />
    <meta name="keywords"
        content="Pseudolearn, pembelajaran pemrograman, dasar pemrograman, pseudocode, aplikasi edukasi, belajar pemrograman, interaktif, siswa, pendidikan, teknologi pendidikan" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title"
        content="Pseudolearn" />
    <meta property="og:url" content="https://pseudolearn.web.id" />
    <meta property="og:site_name" content="Pseudolearn" />
    <link rel="canonical" href="https://pseudolearn.web.id" />
    <link rel="shortcut icon" href="{!! asset('assets/media/logos/logo-polinema.ico') !!}" />
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700&display=swap" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="{!! asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') !!}" rel="stylesheet" type="text/css" />
    <link href="{!! asset('assets/plugins/custom/datatables/datatables.bundle.css') !!}" rel="stylesheet" type="text/css" />
    <link href="{!! asset('assets/plugins/global/plugins.bundle.css') !!}" rel="stylesheet" type="text/css" />
    <link href="{!! asset('assets/css/style.bundle.css') !!}" rel="stylesheet" type="text/css" />
    <script>
        // Frame-busting to prevent site from being loaded within a frame without permission (click-jacking) if (window.top != window.self) { window.top.location.replace(window.self.location.href); }
    </script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('styles')
    <style>
        /* Perbaikan: blur hanya pada gambar background, bukan seluruh konten */
        body {
            position: relative;
            min-height: 100vh;
            background: transparent;
            overflow-x: hidden;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            /* Geser background sedikit ke atas (dari center center -> center 75%) */
            background: url("{{ secure_asset('assets/media/img/bg-login3.webp') }}") center 80% / cover no-repeat;
            filter: blur(1px) brightness(0.8);
            transform: scale(1.06);
            z-index: -1;

            /* 🔹 Tambahkan animasi brightness */
            animation: brightnessPulse 20s ease-in-out infinite;
        }

        @keyframes brightnessPulse {
            0% {
                filter: blur(1px) brightness(0.8);
            }
            50% {
                filter: blur(1px) brightness(2);
            }
            100% {
                filter: blur(1px) brightness(0.8);
            }
        }

        .elevate-img {
            animation: elevate 3s ease-in-out infinite alternate;
        }

        @keyframes elevate {
            0% {
                transform: translateY(0);
            }
            100% {
                transform: translateY(-15px);
            }
        }

        @media (max-width: 1024px) {
            .d-flex.flex-column-fluid.flex-lg-row-auto.justify-content-center.justify-content-lg-end.p-12 {
                padding: 2rem !important;  
                margin: 0 1rem !important;  
                width: 100% !important;     
            }

            .w-md-600px {
                width: 85% !important; 
                max-width: 600px;  
            }

            .w-md-450px {
                width: 100% !important;
                max-width: 450px;
            }

            .mx-auto.w-650px {
                width: 380px !important;
            }
        }

        @media (max-width: 576px) {
            /* body {
                background: linear-gradient(160deg, #FFFEBA 0%, #FFC7B2 100%) !important;
            } */
            .d-flex.flex-lg-row {
                flex-direction: column !important;
            }
            .mx-auto.w-650px {
                width: 250px !important;
                margin-bottom: 20px;
            }
            .w-md-600px,
            .w-md-450px {
                width: 100% !important;
                padding: 1.5rem !important;
            }
            .text-center.mb-20 h1 {
                font-size: 1.5rem !important;
            }
            .text-gray-500.fs-5 {
                font-size: 0.9rem !important;
            }
            .btn-lg {
                font-size: 1rem !important;
                padding: 0.75rem !important;
            }
            .mx-auto.w-650px.elevate-img {
                display: none !important;
            }
        } 
    </style>
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function () {
            let deg = 135;
            setInterval(function () {
                deg = (deg + 1) % 360;
                document.body.style.background = `linear-gradient(${deg}deg, #FFFEBA 0%, #FFC7B2 100%)`;
            }, 20);
        });
    </script> --}}
</head>

<body id="kt_body" class="app-blank bgi-size-cover bgi-attachment-fixed bgi-position-center">
		<script>var defaultThemeMode = "light"; var themeMode; if ( document.documentElement ) { if ( document.documentElement.hasAttribute("data-bs-theme-mode")) { themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); } else { if ( localStorage.getItem("data-bs-theme") !== null ) { themeMode = localStorage.getItem("data-bs-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-bs-theme", themeMode); }</script>
		<div class="d-flex flex-column flex-root" id="kt_app_root">
			<div class="d-flex flex-column flex-lg-row flex-column-fluid">
                <div class="d-flex flex-lg-row-fluid">
                    <div class="d-flex flex-column flex-center pb-0 pb-lg-10 p-10 w-100">
                        <img class="mx-auto w-550px elevate-img" src="{{ asset('assets/media/img/karakter-login4.webp') }}" alt="Karakter Login" />
                    </div>
                </div>
				<div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-15">
					<div class="bg-body d-flex flex-column flex-center w-md-550px" style="border-radius: 1.5rem;">
						<div class="d-flex flex-center flex-column align-items-stretch h-lg-100 w-md-400px">
							<div class="d-flex flex-center flex-column flex-column-fluid">
                                <form class="form w-100" method="POST" action="{{ route('login.authenticate') }}">
                                    @csrf
									<div class="text-center mb-20">
                                        <h1 class="text-dark fw-bolder mb-3">Selamat Datang</h1>
                                        <h1 class="text-dark fw-bolder mb-3">di Dunia Belajar Coding</h1>
                                        <div class="text-gray-500 fw-semibold fs-5">
                                            Login sekarang! dan jadilah 
                                            <span style="color: #F39C12;">1</span>
                                            di
                                            <span style="color: #F39C12;">Papan Peringkat</span>
                                        </div>
									</div>
                                    <div class="fv-row mb-8">
                                        <label for="email" class="form-label required">Email</label>
                                        <div class="input-group">
                                            <input type="text" placeholder="Masukkan Email" name="email" id="email" autocomplete="off" class="form-control bg-transparent" aria-describedby="email-addon" required value="" />
                                        </div>
                                    </div>
                                    <div class="fv-row mb-12">
                                        <label for="password" class="form-label required">Password</label>
                                        <div class="input-group">
                                            <input type="password" placeholder="Masukkan Password" name="password" id="password" autocomplete="off" class="form-control bg-transparent" aria-describedby="password-addon" required value="" />
                                            <span class="input-group-text" id="toggle-password" style="cursor:pointer;">
                                                <i class="ki-outline ki-eye fs-2" id="icon-eye"></i>
                                            </span>
                                        </div>
                                    </div>
									<div class="d-grid">
										<button type="submit" class="btn btn-primary btn-lg">
											<span class="indicator-label">Login</span>
											<span class="indicator-progress">Mohon tunggu...
											<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
										</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    <script>
        var hostUrl = "assets/";
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('login_error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Login Gagal',
            text: '{{ session('login_error') }}',
            confirmButtonText: 'Coba Lagi',
            customClass: {
                confirmButton: 'btn btn-danger'
            },
            buttonsStyling: false
        });
    </script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password');
            const togglePassword = document.getElementById('toggle-password');
            const iconEye = document.getElementById('icon-eye');

            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                // Toggle icon
                if (type === 'text') {
                    iconEye.classList.remove('ki-eye');
                    iconEye.classList.add('ki-eye-slash');
                } else {
                    iconEye.classList.remove('ki-eye-slash');
                    iconEye.classList.add('ki-eye');
                }
            });
        });
    </script>
    <script src="{!! asset('assets/plugins/global/plugins.bundle.js') !!}"></script>
    <script src="{!! asset('assets/js/scripts.bundle.js') !!}"></script>
    <script src="{!! asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.js') !!}"></script>
    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/radar.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/map.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/geodata/worldLow.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/geodata/continentsLow.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/geodata/usaLow.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/geodata/worldTimeZonesLow.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/geodata/worldTimeZoneAreasLow.js"></script>
    <script src="{!! asset('assets/plugins/custom/datatables/datatables.bundle.js') !!}"></script>
    <script src="{!! asset('assets/js/widgets.bundle.js') !!}"></script>
    <script src="{!! asset('assets/js/custom/widgets.js') !!}"></script>
    <script src="{!! asset('assets/js/custom/apps/chat/chat.js') !!}"></script>
    <script src="{!! asset('assets/js/custom/utilities/modals/upgrade-plan.js') !!}"></script>
    <script src="{!! asset('assets/js/custom/utilities/modals/users-search.js') !!}"></script>
    <script src="{!! asset('assets/plugins/custom/iconify/iconify-icon.min.js') !!}"></script>
    @stack('scripts')
</body>
</html>
