<?php 
    use App\Models\Mahasiswa;
    $user = auth()->user();
    $mahasiswa = Mahasiswa::where('id_user', $user->id)->first();
?>

<div class="modal fade" tabindex="-1" id="modal-guide" data-bs-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content rounded">
        @csrf
      <!-- Header -->
        <div class="modal-header py-2 position-relative" style="background-color: #F39C12; height: 50px;">
            <h3 class="modal-title text-white position-absolute top-50 start-50 translate-middle m-0">
                Panduan
            </h3>
            <button type="button" class="btn btn-sm position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close">
                <i class="ki-outline ki-cross fs-2x text-white"></i>
            </button>
        </div>

      <!-- Body -->
        <div class="modal-body text-center px-4 py-4">
            <div id="kt_carousel_2_carousel" class="carousel carousel-custom slide" data-bs-ride="false" data-bs-interval="false">
                <div class="carousel-inner">

                    <!-- Slide 1 -->
                    <div class="carousel-item active">
                        <h1><strong style="color: #F39C12;">Selamat Datang,</strong> <strong style="color: #03346E;">di <i>PseudoLearn!</i></strong></h1>
                        <p class="mt-2">Yuk, belajar <strong style="color: #03346E;">algoritma</strong> dan <strong style="color: #03346E;">tipe data</strong> dengan cara<br> yang seru dan penuh tantangan!</p>
                        <img src="/assets/media/img/karakter-login2.png"  alt="ha" class="mb-6" style="height: 150px;" />
                        <p>Siap mulai petualanganmu?</p>
                    </div>

                    <!-- Slide 2 -->
                    <div class="carousel-item">
                        <h1 class="mb-6"><strong style="color: #03346E;">Ini arena progres belajarmu!</strong></h1>
                        <img src="/assets/media/img/panduan2.png" alt="Karakter roket" class="mb-6" style="height: 50px;" />
                        <p class="mt-2">kamu bisa melihat total poin, badge yang berhasil diraih,<br>peringkatmu di leaderboard, dan jumlah nyawa yang<br>kamu miliki untuk melanjutkan petualangan belajar!!</p>
                    </div>

                    <!-- Slide 3 -->
                    <div class="carousel-item">
                        <div class="container px-4">
                            <h1 class="mb-5 text-center"><strong style="color: #03346E;">Hati-hati, Nyawamu Terbatas!</strong></h1>
                            <div class="row align-items-center justify-content-center mb-4">
                                <div class="col-auto text-center">
                                    <img src="/assets/media/img/panduan3_a.png" alt="Ilustrasi nyawa" class="img-fluid" style="max-height: 70px;" />
                                </div>
                                <div class="col text-start">
                                    <p class="mb-0">Kamu <strong style="color: #03346E">maksimal</strong> memiliki <strong style="color: #03346E;">25</strong> nyawa. Jika nyawamu hangus, satu nyawa akan otomatis kembali setiap 10 menit.</p>
                                </div>
                            </div>
                            <div class="row align-items-center justify-content-center mb-4">
                                <div class="col-auto text-center">
                                    <img src="/assets/media/img/panduan3_b.png" alt="Ilustrasi nyawa" class="img-fluid" style="max-height: 40px;" />
                                </div>
                                <div class="col text-start">
                                    <p class="mb-0">Mau lebih cepat isi ulang nyawa? Selesaikan misi dan klaim hadiahnya!</p>
                                </div>
                            </div>
                            <div class="row align-items-center justify-content-center mb-4">
                                <div class="col-auto text-center">
                                    <img src="/assets/media/img/panduan3_c.png" alt="Ilustrasi nyawa" class="img-fluid" style="max-height: 24px;" />
                                </div>
                                <div class="col text-start">
                                    <p class="mb-0">Pikirkan baik-baik sebelum klik <strong style="color: #03346E;">“Cek Jawaban”</strong> karena ketika jawaban salah = 1 nyawa melayang!</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 4 -->
                    <div class="carousel-item">
                        <h1><strong style="color: #03346E;">Buka Kunci Levelmu!</strong></h1>
                        <p class="mt-2">Semua level materi ada di halaman <strong style="color: #03346E;">“Latihan Soal”</strong></p>
                        <img src="/assets/media/img/panduan4.png" alt="Karakter roket" class="mb-4" style="height: 160px;" />
                        <p>Setiap level harus dikerjakan berurutan untuk membuka<br> jalan ke level selanjutnya.</p>
                    </div>

                    <!-- Slide 5 -->
                    <div class="carousel-item">
                        <h1><strong style="color: #03346E;">Rute Belajar:</strong></h1>
                        <p class="mt-2">Tiap <strong style="color: #03346E;">Level</strong> berisi beberapa <strong style="color: #03346E;">Soal</strong> yang harus dikerjakan<br> 
                        <strong style="color: #03346E;">secara urut</strong>. Mulai dari soal <strong style="color: #03346E">Pseudocode</strong> kemudian<br> dilanjut dengan soal 
                        <strong style="color: #03346E;">code program</strong>.</p>
                        <img src="/assets/media/img/panduan5.png" alt="Karakter roket" class="mb-4" style="height: 160px;" />
                    </div>

                     <!-- Slide 6 -->
                    <div class="carousel-item">
                        <h1><strong style="color: #03346E;">Arena Ujian Pseudocode</strong></h1>
                        <p class="mt-2">Susun algoritma dan tipe data yang telah diacak menjadi<br>urutan yang benar! 
                        lalu klik <strong style="color: #03346E;">"Cek Jawaban"</strong> untuk melihat hasilnya.</p>
                        <img src="/assets/media/img/panduan6.png" alt="Karakter roket" class="mb-4" style="height: 160px;" />
                    </div>

                       <!-- Slide 7 -->
                    <div class="carousel-item">
                        <h1><strong style="color: #03346E;">Arena Ujian Code Program</strong></h1>
                        <p class="mt-2">Ketikan code program sesuai dengan urutan langkah<strong style="color: #03346E;"> algoritma pseudocode!</strong>
                        lalu klik <strong style="color: #03346E;">“Cek Jawaban”</strong>untuk melihat hasilnya.</p>
                        <img src="/assets/media/img/panduan7.png" alt="Karakter roket" style="height: 160px;" />
                    </div>

                    <!-- Slide 8 -->
                    <div class="carousel-item">
                        <div class="container px-4">
                            <h1 class="mb-5 text-center"><strong style="color: #03346E;">Kumpulkan AlgoBadge,<br>AlgoPoin, dan Buka Level Baru!</strong></h1>
                            <div class="row align-items-center justify-content-center mb-4">
                                <div class="col-auto text-center">
                                    <img src="/assets/media/img/panduan8_a.png" alt="Ilustrasi nyawa" class="img-fluid" style="max-height: 96px;" />
                                </div>
                                <div class="col text-start">
                                    <p class="mb-0">Selesaikan setiap soal dan dapatkan <strong style="color: #03346E;">Algobadge</strong></p>
                                </div>
                            </div>
                            <div class="row align-items-center justify-content-center mb-4">
                                <div class="col-auto text-center">
                                    <img src="/assets/media/img/panduan8_b.png" alt="Ilustrasi nyawa" class="img-fluid" style="max-height: 70px;" />
                                </div>
                                <div class="col text-start">
                                    <p class="mb-0">Selesaikan semua soal kemudian dapat <strong style="color: #03346E">Algopoin</strong></p>
                                </div>
                            </div>
                            <div class="row align-items-center justify-content-center mb-4">
                                <div class="col-auto text-center">
                                    <img src="/assets/media/img/panduan8_c.png" alt="Ilustrasi nyawa" class="img-fluid" style="max-height: 80px;" />
                                </div>
                                <div class="col text-start">
                                    <p class="mb-0">Semua soal berhasil terselesaikan, <strong style="color: #03346E">Level</strong> berikutnya terbuka!</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Slide 9 -->
                    <div class="carousel-item">
                        <h1><strong style="color: #03346E;">Ayo Naik Peringkat!</strong></h1>
                        <p class="mt-2 mb-0">Leaderboard dihitung dari <strong style="color: #03346E;">total poin</strong> + <strong style="color: #03346E">total langkah</strong> drag & drop.</p>
                        <img src="/assets/media/img/panduan9.png" alt="Karakter roket" class="mb-4" style="height: 180px;" />
                        <p>Raih posisi teratas dengan poin terbanyak dan langkah paling efisien saat menyusun algoritma!.</p>
                    </div>

                </div>

                <!-- Carousel controls, indicators, and skip in one line --> 
                <div class="d-flex justify-content-center align-items-center mt-4 gap-2">
                    <button class="carousel-control-prev position-static" type="button" data-bs-target="#kt_carousel_2_carousel" data-bs-slide="prev" style="width: 40px;">
                            <i class="ki-outline ki-left fs-1 fw-bold "></i>
                            <span class="visually-hidden">Previous</span>
                    </button>
                        <ol class="carousel-indicators carousel-indicators-bullet p-0 m-0 gap-2">
                            <li data-bs-target="#kt_carousel_2_carousel" data-bs-slide-to="0" class="active ms-1 "></li>
                            <li data-bs-target="#kt_carousel_2_carousel" data-bs-slide-to="1" class="ms-1 "></li>
                            <li data-bs-target="#kt_carousel_2_carousel" data-bs-slide-to="2" class="ms-1 "></li>
                            <li data-bs-target="#kt_carousel_2_carousel" data-bs-slide-to="3" class="ms-1 "></li>
                            <li data-bs-target="#kt_carousel_2_carousel" data-bs-slide-to="4" class="ms-1 "></li>
                            <li data-bs-target="#kt_carousel_2_carousel" data-bs-slide-to="5" class="ms-1 "></li>
                            <li data-bs-target="#kt_carousel_2_carousel" data-bs-slide-to="6" class="ms-1 "></li>
                            <li data-bs-target="#kt_carousel_2_carousel" data-bs-slide-to="7" class="ms-1 "></li>
                            <li data-bs-target="#kt_carousel_2_carousel" data-bs-slide-to="8" class="ms-1 "></li>
                        </ol>
                    <button class="carousel-control-next position-static" type="button" data-bs-target="#kt_carousel_2_carousel" data-bs-slide="next" style="width: 40px;">
                        <i class="ki-outline ki-right fs-1 fw-bold "></i>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cek dari PHP (boolean yang benar)
        var openPanduan = @json($mahasiswa && $mahasiswa->open_panduan == 0);
        var id_mahasiswa = @json($mahasiswa ? (string) $mahasiswa->id : null);

        if (openPanduan) {
            var modal = document.getElementById('modal-guide');
            if (modal) {
                modal.addEventListener('hidden.bs.modal', function () {
                    $.ajax({
                        type: 'POST',
                        url: APP_URL + 'mahasiswa/update-open-panduan',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            open_panduan: 1,
                            id_mahasiswa: id_mahasiswa
                        },
                        success: function (response) {
                            
                        },
                        error: function (xhr) {
                            Swal.fire({
                                text: xhr.responseJSON?.message || "Terjadi kesalahan sistem.",
                                icon: "error",
                                confirmButtonText: "OK",
                                customClass: {
                                    confirmButton: "btn btn-primary",
                                },
                            });
                        }
                    });
                });
            }
        }
    });
</script>