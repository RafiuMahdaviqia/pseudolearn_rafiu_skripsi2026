<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SoalQueueSeeder extends Seeder
{
    /**
     * Seeder untuk soal cerita materi Queue dan Linked List.
     *
     * Queue  (id_level: 019863c4-59f9-7319-9104-08267fc3c551)
     *   Materi: Queue dasar, Enqueue, Dequeue, Queue + Stack
     *   Difficulty: easy (5), medium (5), hard (5) — total 15 soal
     *
     * Linked List  (id_level: 019de356-abfa-717d-958c-e9311c2712f3)
     *   Materi: Single Linked List (SLL), Double Linked List (DLL)
     *   Difficulty: easy (5), medium (5), hard (5) — total 15 soal
     */
    public function run(): void
    {
        $idLevel = '019863c4-59f9-7319-9104-08267fc3c551';
        $now     = now();

        $soalList = [

            // ============================================================
            // EASY 1 - Antrian Loket Karcis Bioskop
            // ============================================================
            [
                'id'              => Str::uuid(),
                'id_level'        => $idLevel,
                'judul'           => 'Antrian Loket Karcis Bioskop',
                'soal'            => '<p>Sebuah bioskop membuka satu loket karcis. Tiga orang datang berurutan: Rina, Doni, lalu Yudi. Setelah ketiganya mengantri, petugas ingin mengetahui siapa yang berada paling depan antrian dan berapa total orang yang sedang mengantri.</p><p>Tentukan siapa yang berada di posisi FRONT antrian dan berapa SIZE antrian setelah ketiga orang tersebut masuk.</p>',
                'kunci_tipe_data' => json_encode([
                    ['variabel' => 'antrian', 'tipe_data' => 'Queue', 'konversi' => 0],
                ]),
                'kunci_algoritma' => json_encode([
                    ['langkah' => 'START',                                                        'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Deklarasikan antrian sebagai Queue kosong',                    'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'ENQUEUE(Rina) -> antrian=[Rina]',                              'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'ENQUEUE(Doni) -> antrian=[Rina,Doni]',                         'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'ENQUEUE(Yudi) -> antrian=[Rina,Doni,Yudi]',                    'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT FRONT(antrian) -> Rina',                                 'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT SIZE(antrian) -> 3',                                     'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'FIFO: Rina masuk pertama maka Rina berada di depan dan keluar pertama', 'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'END',                                                          'clue' => 0, 'konversi' => 0],
                ]),
                'order'      => 1,
                'status'     => 1,
                'difficulty' => 'easy',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],

            // ============================================================
            // EASY 2 - Antrian Pasien Klinik
            // ============================================================
            [
                'id'              => Str::uuid(),
                'id_level'        => $idLevel,
                'judul'           => 'Antrian Pasien Klinik',
                'soal'            => '<p>Sebuah klinik menerima tiga pasien secara berurutan dengan nomor antrian 101, 102, dan 103. Resepsionis ingin menampilkan nomor pasien yang berada paling depan (FRONT), paling belakang (REAR), dan total pasien yang sedang mengantri.</p><p>Tentukan nilai FRONT, REAR, dan SIZE dari antrian tersebut.</p>',
                'kunci_tipe_data' => json_encode([
                    ['variabel' => 'antrian', 'tipe_data' => 'Queue', 'konversi' => 0],
                ]),
                'kunci_algoritma' => json_encode([
                    ['langkah' => 'START',                                                         'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Deklarasikan antrian kosong',                                   'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'ENQUEUE(101) -> antrian=[101], front=101, rear=101',            'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'ENQUEUE(102) -> antrian=[101,102], front=101, rear=102',        'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'ENQUEUE(103) -> antrian=[101,102,103], front=101, rear=103',    'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT FRONT -> 101',                                            'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT REAR  -> 103',                                            'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT SIZE  -> 3',                                              'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'END',                                                           'clue' => 0, 'konversi' => 0],
                ]),
                'order'      => 2,
                'status'     => 1,
                'difficulty' => 'easy',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],

            // ============================================================
            // EASY 3 - Antrian Pengambilan Obat
            // ============================================================
            [
                'id'              => Str::uuid(),
                'id_level'        => $idLevel,
                'judul'           => 'Antrian Pengambilan Obat',
                'soal'            => '<p>Apotek rumah sakit mencatat antrian pengambilan obat satu per satu. Pasien pertama bernama Siti, kemudian Bagas, lalu Citra. Setiap kali seorang pasien masuk antrian, sistem langsung mencetak isi antrian beserta jumlahnya.</p><p>Tentukan output yang dicetak sistem setelah setiap operasi ENQUEUE.</p>',
                'kunci_tipe_data' => json_encode([
                    ['variabel' => 'antrian', 'tipe_data' => 'Queue', 'konversi' => 0],
                ]),
                'kunci_algoritma' => json_encode([
                    ['langkah' => 'START',                                                      'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Deklarasikan antrian kosong',                                'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'ENQUEUE(Siti) -> antrian=[Siti]',                           'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT Antrian: [Siti] Ukuran: 1',                           'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'ENQUEUE(Bagas) -> antrian=[Siti,Bagas]',                    'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT Antrian: [Siti,Bagas] Ukuran: 2',                     'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'ENQUEUE(Citra) -> antrian=[Siti,Bagas,Citra]',              'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT Antrian: [Siti,Bagas,Citra] Ukuran: 3',              'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Setiap ENQUEUE elemen masuk dari posisi REAR (belakang)',   'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'END',                                                        'clue' => 0, 'konversi' => 0],
                ]),
                'order'      => 3,
                'status'     => 1,
                'difficulty' => 'easy',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],

            // ============================================================
            // EASY 4 - Loket Bank Belum Buka
            // ============================================================
            [
                'id'              => Str::uuid(),
                'id_level'        => $idLevel,
                'judul'           => 'Loket Bank Belum Buka',
                'soal'            => '<p>Sebelum loket bank dibuka, sistem memeriksa apakah antrian kosong menggunakan fungsi ISEMPTY. Setelah loket dibuka, dua nasabah dengan nomor 201 dan 202 mendaftar. Sistem kembali memeriksa kondisi antrian, jumlah nasabah, dan nomor nasabah terdepan.</p><p>Tentukan seluruh output yang dihasilkan sistem secara berurutan.</p>',
                'kunci_tipe_data' => json_encode([
                    ['variabel' => 'antrian', 'tipe_data' => 'Queue', 'konversi' => 0],
                ]),
                'kunci_algoritma' => json_encode([
                    ['langkah' => 'START',                                                         'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Deklarasikan antrian kosong',                                   'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT ISEMPTY(antrian) -> TRUE',                                'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'ENQUEUE(201) -> antrian=[201]',                                 'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'ENQUEUE(202) -> antrian=[201,202]',                             'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT ISEMPTY(antrian) -> FALSE',                               'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT SIZE(antrian) -> 2',                                      'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT FRONT(antrian) -> 201',                                   'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'ISEMPTY=TRUE jika tidak ada elemen, FALSE jika ada elemen',     'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'END',                                                           'clue' => 0, 'konversi' => 0],
                ]),
                'order'      => 4,
                'status'     => 1,
                'difficulty' => 'easy',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],

            // ============================================================
            // EASY 5 - Antrian Wahana Taman Bermain
            // ============================================================
            [
                'id'              => Str::uuid(),
                'id_level'        => $idLevel,
                'judul'           => 'Antrian Wahana Taman Bermain',
                'soal'            => '<p>Sebuah wahana taman bermain menerima tiga pengunjung secara berurutan dengan nomor tiket 7, 8, dan 9. Petugas ingin mengetahui siapa yang paling depan, paling belakang, total pengunjung dalam antrian, dan apakah antrian sudah kosong.</p><p>Tentukan nilai FRONT, REAR, SIZE, dan ISEMPTY dari antrian tersebut.</p>',
                'kunci_tipe_data' => json_encode([
                    ['variabel' => 'antrian', 'tipe_data' => 'Queue', 'konversi' => 0],
                ]),
                'kunci_algoritma' => json_encode([
                    ['langkah' => 'START',                         'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Deklarasikan antrian kosong',   'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'ENQUEUE(7) -> antrian=[7]',     'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'ENQUEUE(8) -> antrian=[7,8]',   'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'ENQUEUE(9) -> antrian=[7,8,9]', 'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT FRONT  : 7',              'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT REAR   : 9',              'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT SIZE   : 3',              'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT ISEMPTY: FALSE',          'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'END',                           'clue' => 0, 'konversi' => 0],
                ]),
                'order'      => 5,
                'status'     => 1,
                'difficulty' => 'easy',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],

            // ============================================================
            // MEDIUM 1 - Memanggil Pasien Pertama di Puskesmas
            // ============================================================
            [
                'id'              => Str::uuid(),
                'id_level'        => $idLevel,
                'judul'           => 'Memanggil Pasien Pertama di Puskesmas',
                'soal'            => '<p>Puskesmas memiliki antrian tiga pasien: Hendra (depan), Lestari, dan Miko (belakang). Dokter memanggil satu pasien dari antrian menggunakan operasi DEQUEUE. Sistem mencatat pasien yang dipanggil, sisa antrian, pasien terdepan yang baru, dan jumlah antrian setelah pemanggilan.</p><p>Tentukan nilai variabel yang menyimpan hasil DEQUEUE, sisa isi antrian, FRONT yang baru, dan SIZE yang baru.</p>',
                'kunci_tipe_data' => json_encode([
                    ['variabel' => 'antrian',   'tipe_data' => 'Queue',  'konversi' => 0],
                    ['variabel' => 'dipanggil', 'tipe_data' => 'String', 'konversi' => 0],
                ]),
                'kunci_algoritma' => json_encode([
                    ['langkah' => 'START',                                                              'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'antrian=[Hendra,Lestari,Miko]',                                      'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'dipanggil=DEQUEUE -> dipanggil=Hendra, antrian=[Lestari,Miko]',      'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT Dipanggil  : Hendra',                                          'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT Sisa       : [Lestari,Miko]',                                  'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT FRONT baru : Lestari',                                         'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT SIZE baru  : 2',                                               'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'DEQUEUE mengambil elemen dari posisi FRONT (depan)',                  'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'END',                                                                'clue' => 0, 'konversi' => 0],
                ]),
                'order'      => 1,
                'status'     => 1,
                'difficulty' => 'medium',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],

            // ============================================================
            // MEDIUM 2 - Melayani Seluruh Antrian Kasir Supermarket
            // ============================================================
            [
                'id'              => Str::uuid(),
                'id_level'        => $idLevel,
                'judul'           => 'Melayani Seluruh Antrian Kasir Supermarket',
                'soal'            => '<p>Kasir supermarket memiliki antrian lima pembeli dengan nomor 1 hingga 5, di mana nomor 1 berada paling depan. Kasir melayani pembeli satu per satu dari depan antrian hingga antrian benar-benar kosong. Setiap kali pembeli dilayani, sistem mencetak nomor pembeli yang dilayani beserta sisa antrian.</p><p>Tentukan seluruh output yang dicetak sistem dari awal hingga akhir secara berurutan.</p>',
                'kunci_tipe_data' => json_encode([
                    ['variabel' => 'antrian', 'tipe_data' => 'Queue', 'konversi' => 0],
                    ['variabel' => 'pembeli',  'tipe_data' => 'int',   'konversi' => 0],
                ]),
                'kunci_algoritma' => json_encode([
                    ['langkah' => 'START',                                            'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'antrian=[1,2,3,4,5]',                              'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 1: pembeli=1, PRINT Dilayani:1 Sisa:4',   'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 2: pembeli=2, PRINT Dilayani:2 Sisa:3',   'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 3: pembeli=3, PRINT Dilayani:3 Sisa:2',   'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 4: pembeli=4, PRINT Dilayani:4 Sisa:1',   'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 5: pembeli=5, PRINT Dilayani:5 Sisa:0',   'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT Antrian telah kosong',                       'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'END',                                              'clue' => 0, 'konversi' => 0],
                ]),
                'order'      => 2,
                'status'     => 1,
                'difficulty' => 'medium',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],

            // ============================================================
            // MEDIUM 3 - Antrian Pendaftaran Lomba Bergantian
            // ============================================================
            [
                'id'              => Str::uuid(),
                'id_level'        => $idLevel,
                'judul'           => 'Antrian Pendaftaran Lomba Bergantian',
                'soal'            => '<p>Panitia lomba membuka pendaftaran secara bertahap. Peserta A dan B mendaftar, lalu peserta A dipanggil untuk verifikasi berkas. Kemudian peserta C mendaftar, lalu peserta B dipanggil. Terakhir peserta D mendaftar. Sistem mencatat nilai peserta yang dipanggil di setiap tahap dan menampilkan isi antrian akhir.</p><p>Telusuri setiap langkah. Tentukan siapa peserta pertama dan kedua yang dipanggil (variabel a dan b), serta siapa saja yang masih mengantri di akhir beserta jumlahnya.</p>',
                'kunci_tipe_data' => json_encode([
                    ['variabel' => 'antrian', 'tipe_data' => 'Queue',  'konversi' => 0],
                    ['variabel' => 'a',       'tipe_data' => 'String', 'konversi' => 0],
                    ['variabel' => 'b',       'tipe_data' => 'String', 'konversi' => 0],
                ]),
                'kunci_algoritma' => json_encode([
                    ['langkah' => 'START',                                     'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'ENQUEUE(A) -> antrian=[A]',                 'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'ENQUEUE(B) -> antrian=[A,B]',               'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'a=DEQUEUE -> a=A, antrian=[B]',             'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'ENQUEUE(C) -> antrian=[B,C]',               'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'b=DEQUEUE -> b=B, antrian=[C]',             'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'ENQUEUE(D) -> antrian=[C,D]',               'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT a         : A',                        'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT b         : B',                        'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT Isi akhir : [C,D]',                   'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT SIZE      : 2',                        'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'END',                                        'clue' => 0, 'konversi' => 0],
                ]),
                'order'      => 3,
                'status'     => 1,
                'difficulty' => 'medium',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],

            // ============================================================
            // MEDIUM 4 - Membalik Urutan Antrian Peserta Ujian
            // ============================================================
            [
                'id'              => Str::uuid(),
                'id_level'        => $idLevel,
                'judul'           => 'Membalik Urutan Antrian Peserta Ujian',
                'soal'            => '<p>Lima peserta ujian mengantri dengan urutan nomor 1 hingga 5, di mana nomor 1 berada paling depan. Panitia ingin membalik urutan antrian agar peserta yang terakhir datang dipanggil pertama. Tekniknya: semua peserta dipindahkan ke Stack satu per satu, lalu dikembalikan ke Queue dari Stack.</p><p>Telusuri setiap langkah pemindahan. Tentukan isi antrian sebelum dan sesudah proses pembalikan dilakukan.</p>',
                'kunci_tipe_data' => json_encode([
                    ['variabel' => 'antrian',  'tipe_data' => 'Queue', 'konversi' => 0],
                    ['variabel' => 'tumpukan', 'tipe_data' => 'Stack', 'konversi' => 0],
                ]),
                'kunci_algoritma' => json_encode([
                    ['langkah' => 'START',                                                      'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'antrian=[1,2,3,4,5], tumpukan=[]',                          'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT Sebelum: [1,2,3,4,5]',                                'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Pindah ke Stack: DEQUEUE 1->PUSH, DEQUEUE 2->PUSH, dst',    'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'tumpukan=[1,2,3,4,5] top=5',                                'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Kembalikan ke Queue: POP 5->ENQUEUE, POP 4->ENQUEUE, dst',  'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'antrian=[5,4,3,2,1]',                                       'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT Sesudah: [5,4,3,2,1]',                                'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'END',                                                        'clue' => 0, 'konversi' => 0],
                ]),
                'order'      => 4,
                'status'     => 1,
                'difficulty' => 'medium',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],

            // ============================================================
            // MEDIUM 5 - Cek Palindrom Plat Nomor Kendaraan
            // ============================================================
            [
                'id'              => Str::uuid(),
                'id_level'        => $idLevel,
                'judul'           => 'Cek Palindrom Plat Nomor Kendaraan',
                'soal'            => '<p>Petugas parkir ingin mengecek apakah plat nomor kendaraan <strong>"CIVIC"</strong> merupakan palindrom (terbaca sama dari depan maupun belakang). Setiap karakter plat dimasukkan ke Queue dan Stack sekaligus, kemudian karakter hasil DEQUEUE dari Queue dibandingkan dengan hasil POP dari Stack satu per satu.</p><p>Telusuri setiap langkah perbandingan dan tentukan apakah "CIVIC" merupakan palindrom.</p>',
                'kunci_tipe_data' => json_encode([
                    ['variabel' => 'antrian',     'tipe_data' => 'Queue',   'konversi' => 0],
                    ['variabel' => 'tumpukan',    'tipe_data' => 'Stack',   'konversi' => 0],
                    ['variabel' => 'isPalindrom', 'tipe_data' => 'boolean', 'konversi' => 0],
                ]),
                'kunci_algoritma' => json_encode([
                    ['langkah' => 'START',                                              'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'kata=CIVIC',                                         'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'ENQUEUE+PUSH C,I,V,I,C',                             'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'antrian=[C,I,V,I,C], tumpukan top=C',               'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Bandingkan: DEQUEUE=C vs POP=C -> sama',            'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Bandingkan: DEQUEUE=I vs POP=I -> sama',            'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Bandingkan: DEQUEUE=V vs POP=V -> sama',            'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Bandingkan: DEQUEUE=I vs POP=I -> sama',            'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Bandingkan: DEQUEUE=C vs POP=C -> sama',            'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'isPalindrom=TRUE',                                   'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT Palindrom: TRUE',                              'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'END',                                                'clue' => 0, 'konversi' => 0],
                ]),
                'order'      => 5,
                'status'     => 1,
                'difficulty' => 'medium',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],

            // ============================================================
            // HARD 1 - Pencarian Nomor Antrian di Rumah Sakit
            // ============================================================
            [
                'id'              => Str::uuid(),
                'id_level'        => $idLevel,
                'judul'           => 'Pencarian Nomor Antrian di Rumah Sakit',
                'soal'            => '<p>Sistem antrian rumah sakit menyimpan nomor pasien <strong>[11, 22, 33, 44, 55]</strong> dalam sebuah Queue. Petugas ingin mencari apakah pasien dengan nomor <strong>33</strong> ada di antrian menggunakan sequential search. Setiap elemen yang diperiksa dipindahkan ke Queue sementara agar antrian asli dapat dipulihkan setelah pencarian selesai.</p><p>Telusuri setiap langkah pencarian dan tentukan apakah nomor 33 ditemukan serta kondisi antrian setelah proses selesai.</p>',
                'kunci_tipe_data' => json_encode([
                    ['variabel' => 'antrian',   'tipe_data' => 'Queue',   'konversi' => 0],
                    ['variabel' => 'sementara', 'tipe_data' => 'Queue',   'konversi' => 0],
                    ['variabel' => 'ditemukan', 'tipe_data' => 'boolean', 'konversi' => 0],
                    ['variabel' => 'elemen',    'tipe_data' => 'int',     'konversi' => 0],
                ]),
                'kunci_algoritma' => json_encode([
                    ['langkah' => 'START',                                                        'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'antrian=[11,22,33,44,55], cari=33, ditemukan=FALSE',           'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 1: elemen=11, 11!=33, ENQUEUE(sementara,11)',          'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 2: elemen=22, 22!=33, ENQUEUE(sementara,22)',          'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 3: elemen=33, 33=33, ditemukan=TRUE, ENQUEUE(sementara,33)', 'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 4: elemen=44, 44!=33, ENQUEUE(sementara,44)',          'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 5: elemen=55, 55!=33, ENQUEUE(sementara,55)',          'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Kembalikan sementara ke antrian',                              'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT Ditemukan: TRUE',                                        'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT Antrian  : [11,22,33,44,55]',                           'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'END',                                                          'clue' => 0, 'konversi' => 0],
                ]),
                'order'      => 1,
                'status'     => 1,
                'difficulty' => 'hard',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],

            // ============================================================
            // HARD 2 - Mencari Stok Minimum di Gudang
            // ============================================================
            [
                'id'              => Str::uuid(),
                'id_level'        => $idLevel,
                'judul'           => 'Mencari Stok Minimum di Gudang',
                'soal'            => '<p>Sistem gudang menyimpan data stok barang dalam antrian: <strong>[50, 20, 80, 10, 60]</strong> di mana 50 adalah data terdepan. Manajer gudang ingin mencari nilai stok paling sedikit menggunakan sequential search. Setiap elemen yang diperiksa disimpan ke Queue sementara agar data antrian dapat dikembalikan setelah pencarian selesai.</p><p>Telusuri setiap langkah perbandingan. Tentukan nilai minimum stok dan kondisi antrian setelah proses selesai.</p>',
                'kunci_tipe_data' => json_encode([
                    ['variabel' => 'antrian',   'tipe_data' => 'Queue', 'konversi' => 0],
                    ['variabel' => 'sementara', 'tipe_data' => 'Queue', 'konversi' => 0],
                    ['variabel' => 'minimum',   'tipe_data' => 'int',   'konversi' => 0],
                    ['variabel' => 'elemen',    'tipe_data' => 'int',   'konversi' => 0],
                ]),
                'kunci_algoritma' => json_encode([
                    ['langkah' => 'START',                                                 'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'antrian=[50,20,80,10,60], minimum=50',                  'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 1: elemen=50, 50<50 FALSE, minimum=50',        'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 2: elemen=20, 20<50 TRUE, minimum=20',         'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 3: elemen=80, 80<20 FALSE, minimum=20',        'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 4: elemen=10, 10<20 TRUE, minimum=10',         'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 5: elemen=60, 60<10 FALSE, minimum=10',        'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Kembalikan sementara ke antrian',                       'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT Minimum: 10',                                     'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT Antrian: [50,20,80,10,60]',                      'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'END',                                                   'clue' => 0, 'konversi' => 0],
                ]),
                'order'      => 2,
                'status'     => 1,
                'difficulty' => 'hard',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],

            // ============================================================
            // HARD 3 - Menghitung Frekuensi Kehadiran Siswa
            // ============================================================
            [
                'id'              => Str::uuid(),
                'id_level'        => $idLevel,
                'judul'           => 'Menghitung Frekuensi Kehadiran Siswa',
                'soal'            => '<p>Sistem absensi mencatat ID kelas siswa yang hadir dalam satu sesi: <strong>[2, 5, 2, 3, 2, 5, 4]</strong>. Wali kelas ingin menghitung berapa kali siswa dengan ID kelas <strong>2</strong> hadir menggunakan sequential search. Setiap data yang diperiksa dipindahkan ke Queue sementara agar data absensi dapat dipulihkan setelah penghitungan selesai.</p><p>Telusuri setiap langkah dan tentukan berapa kali ID 2 muncul serta kondisi antrian setelah proses selesai.</p>',
                'kunci_tipe_data' => json_encode([
                    ['variabel' => 'antrian',   'tipe_data' => 'Queue', 'konversi' => 0],
                    ['variabel' => 'sementara', 'tipe_data' => 'Queue', 'konversi' => 0],
                    ['variabel' => 'frekuensi', 'tipe_data' => 'int',   'konversi' => 0],
                    ['variabel' => 'elemen',    'tipe_data' => 'int',   'konversi' => 0],
                ]),
                'kunci_algoritma' => json_encode([
                    ['langkah' => 'START',                                                     'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'antrian=[2,5,2,3,2,5,4], cari=2, frekuensi=0',             'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 1: elemen=2, 2=2 TRUE, frekuensi=1',               'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 2: elemen=5, 5=2 FALSE',                           'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 3: elemen=2, 2=2 TRUE, frekuensi=2',               'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 4: elemen=3, 3=2 FALSE',                           'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 5: elemen=2, 2=2 TRUE, frekuensi=3',               'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 6: elemen=5, 5=2 FALSE',                           'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 7: elemen=4, 4=2 FALSE',                           'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Kembalikan sementara ke antrian',                           'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT Frekuensi 2 : 3',                                    'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT Antrian     : [2,5,2,3,2,5,4]',                     'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'END',                                                       'clue' => 0, 'konversi' => 0],
                ]),
                'order'      => 3,
                'status'     => 1,
                'difficulty' => 'hard',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],

            // ============================================================
            // HARD 4 - Cari Nomor Paket lalu Balik Antrian Pengiriman
            // ============================================================
            [
                'id'              => Str::uuid(),
                'id_level'        => $idLevel,
                'judul'           => 'Cari Nomor Paket lalu Balik Antrian Pengiriman',
                'soal'            => '<p>Sistem logistik menyimpan antrian nomor paket: <strong>[301, 302, 303, 304, 305]</strong>. Petugas ingin mencari apakah paket <strong>303</strong> ada di antrian sambil memindahkan setiap paket ke Stack. Setelah pencarian selesai, semua paket di Stack dikembalikan ke Queue menggunakan POP dan ENQUEUE.</p><p>Telusuri setiap langkah. Tentukan apakah paket 303 ditemukan dan tunjukkan isi antrian akhir.</p>',
                'kunci_tipe_data' => json_encode([
                    ['variabel' => 'antrian',   'tipe_data' => 'Queue',   'konversi' => 0],
                    ['variabel' => 'tumpukan',  'tipe_data' => 'Stack',   'konversi' => 0],
                    ['variabel' => 'ditemukan', 'tipe_data' => 'boolean', 'konversi' => 0],
                    ['variabel' => 'elemen',    'tipe_data' => 'int',     'konversi' => 0],
                ]),
                'kunci_algoritma' => json_encode([
                    ['langkah' => 'START',                                                              'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'antrian=[301,302,303,304,305], cari=303, ditemukan=FALSE',           'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 1: DEQUEUE=301, 301!=303, PUSH(301)',                        'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 2: DEQUEUE=302, 302!=303, PUSH(302)',                        'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 3: DEQUEUE=303, 303=303, ditemukan=TRUE, PUSH(303)',         'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 4: DEQUEUE=304, 304!=303, PUSH(304)',                        'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 5: DEQUEUE=305, 305!=303, PUSH(305)',                        'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'tumpukan top=305',                                                   'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'POP=305,304,303,302,301 -> ENQUEUE ke antrian',                     'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'antrian=[305,304,303,302,301] urutan terbalik karena Stack LIFO',    'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT Ditemukan: TRUE',                                              'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT Antrian  : [305,304,303,302,301]',                            'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'END',                                                                'clue' => 0, 'konversi' => 0],
                ]),
                'order'      => 4,
                'status'     => 1,
                'difficulty' => 'hard',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],

            // ============================================================
            // HARD 5 - Mencari Skor Tertinggi di Antrian Turnamen
            // ============================================================
            [
                'id'              => Str::uuid(),
                'id_level'        => $idLevel,
                'judul'           => 'Mencari Skor Tertinggi di Antrian Turnamen',
                'soal'            => '<p>Sistem turnamen menyimpan skor peserta dalam antrian: <strong>[75, 90, 60, 95, 80]</strong> di mana 75 adalah skor terdepan. Panitia ingin mencari skor tertinggi beserta posisinya menggunakan sequential search, sambil memindahkan setiap skor ke Stack. Setelah selesai, semua skor dikembalikan dari Stack ke Queue.</p><p>Telusuri setiap langkah. Tentukan skor tertinggi, posisinya, dan isi antrian setelah proses selesai.</p>',
                'kunci_tipe_data' => json_encode([
                    ['variabel' => 'antrian',  'tipe_data' => 'Queue', 'konversi' => 0],
                    ['variabel' => 'tumpukan', 'tipe_data' => 'Stack', 'konversi' => 0],
                    ['variabel' => 'maksimum', 'tipe_data' => 'int',   'konversi' => 0],
                    ['variabel' => 'posisi',   'tipe_data' => 'int',   'konversi' => 0],
                    ['variabel' => 'index',    'tipe_data' => 'int',   'konversi' => 0],
                ]),
                'kunci_algoritma' => json_encode([
                    ['langkah' => 'START',                                                              'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'antrian=[75,90,60,95,80], maksimum=75, posisi=1, index=1',           'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 1: elemen=75, 75>75 FALSE, PUSH(75), index=2',              'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 2: elemen=90, 90>75 TRUE, maksimum=90, posisi=2, PUSH(90), index=3', 'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 3: elemen=60, 60>90 FALSE, PUSH(60), index=4',              'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 4: elemen=95, 95>90 TRUE, maksimum=95, posisi=4, PUSH(95), index=5', 'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Iterasi 5: elemen=80, 80>95 FALSE, PUSH(80), index=6',              'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'POP=80,95,60,90,75 -> ENQUEUE ke antrian',                          'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'antrian=[80,95,60,90,75]',                                          'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT Maksimum : 95',                                               'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT Posisi   : 4',                                                'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT Antrian  : [80,95,60,90,75]',                                'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'END',                                                               'clue' => 0, 'konversi' => 0],
                ]),
                'order'      => 5,
                'status'     => 1,
                'difficulty' => 'hard',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
        ];

        // ================================================================
        // LINKED LIST SOAL
        // ================================================================
        $idLevelLL = '019de356-abfa-717d-958c-e9311c2712f3';

        $soalLinkedList = [

            // ============================================================
            // EASY 1 - Menyambung Tiga Gerbong Kereta (SLL)
            // ============================================================
            [
                'id'              => Str::uuid(),
                'id_level'        => $idLevelLL,
                'judul'           => 'Menyambung Tiga Gerbong Kereta',
                'soal'            => '<p>Sebuah kereta barang dirangkai menggunakan konsep Single Linked List. Gerbong pertama (head) memiliki beban 10 ton. Kemudian disambungkan gerbong kedua dengan 20 ton, dan gerbong ketiga 30 ton.</p><p>Buat simpul untuk ketiga gerbong tersebut lalu cetak beban pada gerbong pertama dan gerbong terakhir secara berurutan.</p>',
                'kunci_tipe_data' => json_encode([
                    ['variabel' => 'head', 'tipe_data' => 'Node', 'konversi' => 0],
                ]),
                'kunci_algoritma' => json_encode([
                    ['langkah' => 'START',                                                           'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Buat node head dengan nilai 10',                                  'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Sambungkan head.next dengan node baru bernilai 20',               'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Sambungkan head.next.next dengan node baru bernilai 30',          'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT nilai dari head (Depan)',                                   'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT nilai dari head.next.next (Belakang)',                      'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'END',                                                             'clue' => 0, 'konversi' => 0],
                ]),
                'order'      => 1,
                'status'     => 1,
                'difficulty' => 'easy',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],

            // ============================================================
            // EASY 2 - Menghitung Total Gerbong Kereta (SLL)
            // ============================================================
            [
                'id'              => Str::uuid(),
                'id_level'        => $idLevelLL,
                'judul'           => 'Menghitung Total Gerbong Kereta',
                'soal'            => '<p>Rangkaian gerbong kereta (SLL) berisi daftar kode barang: "A", "B", dan "C". Petugas ingin menghitung berapa total gerbong yang ada dalam rangkaian tersebut menggunakan perulangan.</p><p>Tentukan total jumlah gerbong dengan menelusuri Linked List dari depan hingga akhir.</p>',
                'kunci_tipe_data' => json_encode([
                    ['variabel' => 'head',  'tipe_data' => 'Node', 'konversi' => 0],
                    ['variabel' => 'count', 'tipe_data' => 'int',  'konversi' => 0],
                ]),
                'kunci_algoritma' => json_encode([
                    ['langkah' => 'START',                                                                    'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Buat node A, B, C dan sambungkan menjadi A -> B -> C',                    'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Set count = 0, Node sementara = head',                                    'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'LAKUKAN SELAMA sementara tidak kosong:',                                  'clue' => 0, 'konversi' => 0],
                    ['langkah' => '  tambah count dengan 1',                                                 'clue' => 0, 'konversi' => 0],
                    ['langkah' => '  pindahkan sementara ke node selanjutnya (sementara = sementara.next)', 'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT jumlah gerbong',                                                    'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'END',                                                                     'clue' => 0, 'konversi' => 0],
                ]),
                'order'      => 2,
                'status'     => 1,
                'difficulty' => 'easy',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],

            // ============================================================
            // EASY 3 - Menambah Gerbong di Paling Depan (SLL)
            // ============================================================
            [
                'id'              => Str::uuid(),
                'id_level'        => $idLevelLL,
                'judul'           => 'Menambah Gerbong di Paling Depan',
                'soal'            => '<p>Sebuah rangkaian gerbong SLL saat ini memiliki isi "B" lalu "C". Kepala stasiun memerintahkan untuk menyisipkan gerbong baru berisi "A" tepat di posisi paling depan (menjadi head baru).</p><p>Sisipkan gerbong "A" di depan "B", kemudian cetak seluruh isi rangkaian dari depan ke belakang.</p>',
                'kunci_tipe_data' => json_encode([
                    ['variabel' => 'head', 'tipe_data' => 'Node', 'konversi' => 0],
                    ['variabel' => 'baru', 'tipe_data' => 'Node', 'konversi' => 0],
                ]),
                'kunci_algoritma' => json_encode([
                    ['langkah' => 'START',                                          'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Buat SLL awal: head = B, head.next = C',        'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Buat node baru = A',                             'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Sambungkan node baru ke head awal (baru.next = head)', 'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Ubah head menjadi node baru (head = baru)',      'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Telusuri dan PRINT semua isi SLL',              'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'END',                                            'clue' => 0, 'konversi' => 0],
                ]),
                'order'      => 3,
                'status'     => 1,
                'difficulty' => 'easy',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],

            // ============================================================
            // EASY 4 - Navigasi Playlist Lagu Maju dan Mundur (DLL)
            // ============================================================
            [
                'id'              => Str::uuid(),
                'id_level'        => $idLevelLL,
                'judul'           => 'Navigasi Playlist Lagu Maju dan Mundur',
                'soal'            => '<p>Sebuah aplikasi musik menggunakan Double Linked List (DLL) untuk menyimpan playlist. Terdapat dua lagu: "Lagu1" dan "Lagu2". Lagu dapat diputar ke lagu selanjutnya (next) atau kembali ke lagu sebelumnya (prev).</p><p>Buatlah DLL untuk kedua lagu tersebut, lalu cetak urutan maju dan urutan mundur dengan memanfaatkan pointer next dan prev.</p>',
                'kunci_tipe_data' => json_encode([
                    ['variabel' => 'lagu1', 'tipe_data' => 'DNode', 'konversi' => 0],
                    ['variabel' => 'lagu2', 'tipe_data' => 'DNode', 'konversi' => 0],
                ]),
                'kunci_algoritma' => json_encode([
                    ['langkah' => 'START',                             'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Buat DNode l1 (Lagu1) dan l2 (Lagu2)', 'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'l1.next = l2',                      'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'l2.prev = l1',                      'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT Maju: l1 lalu l1.next',       'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT Mundur: l2 lalu l2.prev',     'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'END',                                'clue' => 0, 'konversi' => 0],
                ]),
                'order'      => 4,
                'status'     => 1,
                'difficulty' => 'easy',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],

            // ============================================================
            // EASY 5 - Menampilkan Seluruh Daftar Pesanan Makanan (SLL)
            // ============================================================
            [
                'id'              => Str::uuid(),
                'id_level'        => $idLevelLL,
                'judul'           => 'Menampilkan Seluruh Daftar Pesanan Makanan',
                'soal'            => '<p>Sebuah restoran mencatat pesanan Sate, Soto, dan Bakso ke dalam Single Linked List secara berurutan. Pelayan ingin melihat semua daftar pesanan yang masuk dari pesanan pertama hingga terakhir.</p><p>Lakukan traversal (penelusuran) pada LinkedList dan cetak setiap pesanan ke layar.</p>',
                'kunci_tipe_data' => json_encode([
                    ['variabel' => 'head', 'tipe_data' => 'Node', 'konversi' => 0],
                    ['variabel' => 'tmp',  'tipe_data' => 'Node', 'konversi' => 0],
                ]),
                'kunci_algoritma' => json_encode([
                    ['langkah' => 'START',                                       'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Buat SLL: Sate -> Soto -> Bakso',            'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Set node sementara = head',                  'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'LAKUKAN SELAMA sementara tidak kosong:',     'clue' => 0, 'konversi' => 0],
                    ['langkah' => '  PRINT data pada node sementara',           'clue' => 0, 'konversi' => 0],
                    ['langkah' => '  sementara = sementara.next',               'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'END',                                         'clue' => 0, 'konversi' => 0],
                ]),
                'order'      => 5,
                'status'     => 1,
                'difficulty' => 'easy',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],

            // ============================================================
            // MEDIUM 1 - Mencari Buku di Rak Perpustakaan (SLL)
            // ============================================================
            [
                'id'              => Str::uuid(),
                'id_level'        => $idLevelLL,
                'judul'           => 'Mencari Buku di Rak Perpustakaan',
                'soal'            => '<p>Rak buku perpustakaan menyimpan ID buku dalam Single Linked List dengan urutan: 101, 102, dan 103. Pustakawan ingin mengecek apakah ID buku <strong>102</strong> tersedia di rak tersebut.</p><p>Telusuri SLL dari awal. Jika ditemukan, ubah status menjadi TRUE, lalu cetak hasil penemuannya.</p>',
                'kunci_tipe_data' => json_encode([
                    ['variabel' => 'head', 'tipe_data' => 'Node',    'konversi' => 0],
                    ['variabel' => 'ada',  'tipe_data' => 'boolean', 'konversi' => 0],
                ]),
                'kunci_algoritma' => json_encode([
                    ['langkah' => 'START',                                            'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Buat SLL dengan urutan 101 -> 102 -> 103',        'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Tentukan target yang dicari = 102',               'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Set boolean ada = false, sementara = head',       'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'LAKUKAN penelusuran sampai node habis:',          'clue' => 0, 'konversi' => 0],
                    ['langkah' => '  JIKA nilai node == target MAKA ada = true',     'clue' => 0, 'konversi' => 0],
                    ['langkah' => '  pindah ke node berikutnya',                     'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT status ditemukan (ada)',                    'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'END',                                              'clue' => 0, 'konversi' => 0],
                ]),
                'order'      => 1,
                'status'     => 1,
                'difficulty' => 'medium',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],

            // ============================================================
            // MEDIUM 2 - Menghapus Pasien Pertama dari Antrian (SLL)
            // ============================================================
            [
                'id'              => Str::uuid(),
                'id_level'        => $idLevelLL,
                'judul'           => 'Menghapus Pasien Pertama dari Antrian',
                'soal'            => '<p>Antrian klinik (SLL) berisi nomor pasien 1, 2, dan 3. Pasien nomor 1 telah selesai diperiksa, sehingga petugas harus menghapus pasien pertama dari depan (menghapus head).</p><p>Hapus elemen paling depan SLL tersebut, lalu cetak sisa antrian.</p>',
                'kunci_tipe_data' => json_encode([
                    ['variabel' => 'head', 'tipe_data' => 'Node', 'konversi' => 0],
                ]),
                'kunci_algoritma' => json_encode([
                    ['langkah' => 'START',                                                       'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Buat antrian 1 -> 2 -> 3',                                   'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'JIKA head tidak kosong MAKA:',                               'clue' => 0, 'konversi' => 0],
                    ['langkah' => '  Ubah head ke elemen berikutnya (head = head.next)',         'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Lakukan penelusuran untuk PRINT semua sisa antrian',         'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'END',                                                         'clue' => 0, 'konversi' => 0],
                ]),
                'order'      => 2,
                'status'     => 1,
                'difficulty' => 'medium',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],

            // ============================================================
            // MEDIUM 3 - Melepas Gerbong Terakhir Kereta (DLL)
            // ============================================================
            [
                'id'              => Str::uuid(),
                'id_level'        => $idLevelLL,
                'judul'           => 'Melepas Gerbong Terakhir Kereta',
                'soal'            => '<p>Rangkaian Double Linked List menyimpan nilai gerbong 10 <-> 20 <-> 30. Karena beban terlalu berat, petugas mekanik memotong dan melepas gerbong paling belakang (tail = 30).</p><p>Temukan gerbong terakhir, putus sambungannya, lalu cetak isi dari depan ke belakang.</p>',
                'kunci_tipe_data' => json_encode([
                    ['variabel' => 'head', 'tipe_data' => 'DNode', 'konversi' => 0],
                    ['variabel' => 'temp', 'tipe_data' => 'DNode', 'konversi' => 0],
                ]),
                'kunci_algoritma' => json_encode([
                    ['langkah' => 'START',                                                              'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Buat DLL: 10 <-> 20 <-> 30',                                        'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Telusuri sampai menemukan node terakhir (temp.next == null)',        'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Putus sambungan dari node sebelum terakhir (temp.prev.next = null)', 'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Telusuri dari head untuk PRINT sisa gerbong',                       'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'END',                                                                'clue' => 0, 'konversi' => 0],
                ]),
                'order'      => 3,
                'status'     => 1,
                'difficulty' => 'medium',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],

            // ============================================================
            // MEDIUM 4 - Menyisipkan Peserta di Tengah Barisan (SLL)
            // ============================================================
            [
                'id'              => Str::uuid(),
                'id_level'        => $idLevelLL,
                'judul'           => 'Menyisipkan Peserta di Tengah Barisan',
                'soal'            => '<p>Barisan Single Linked List awalnya berisi "Budi" yang menyambung ke "Doni". Tiba-tiba "Caca" datang dan petugas menyisipkan Caca tepat di antara Budi dan Doni.</p><p>Lakukan proses <em>Insert After</em> pada node Budi, lalu cetak urutan barisan yang baru.</p>',
                'kunci_tipe_data' => json_encode([
                    ['variabel' => 'head', 'tipe_data' => 'Node', 'konversi' => 0],
                    ['variabel' => 'caca', 'tipe_data' => 'Node', 'konversi' => 0],
                ]),
                'kunci_algoritma' => json_encode([
                    ['langkah' => 'START',                                                         'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Buat SLL: head(Budi) -> node(Doni)',                           'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Buat node baru Caca',                                           'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Sambungkan next dari Caca ke Doni (caca.next = head.next)',     'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Sambungkan next dari Budi ke Caca (head.next = caca)',          'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Telusuri dan PRINT seluruh barisan baru',                      'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'END',                                                           'clue' => 0, 'konversi' => 0],
                ]),
                'order'      => 4,
                'status'     => 1,
                'difficulty' => 'medium',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],

            // ============================================================
            // MEDIUM 5 - Menghitung Total Belanjaan di Kasir (SLL)
            // ============================================================
            [
                'id'              => Str::uuid(),
                'id_level'        => $idLevelLL,
                'judul'           => 'Menghitung Total Belanjaan di Kasir',
                'soal'            => '<p>Kasir menyimpan riwayat harga belanja pelanggan di Single Linked List dengan data: 5000, 10000, dan 15000. Kasir ingin menghitung jumlah keseluruhan belanjaan secara otomatis.</p><p>Telusuri elemen dari depan, jumlahkan nilainya ke dalam satu variabel, lalu cetak totalnya.</p>',
                'kunci_tipe_data' => json_encode([
                    ['variabel' => 'head',  'tipe_data' => 'Node', 'konversi' => 0],
                    ['variabel' => 'total', 'tipe_data' => 'int',  'konversi' => 0],
                ]),
                'kunci_algoritma' => json_encode([
                    ['langkah' => 'START',                                                          'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Buat SLL berisi: 5000 -> 10000 -> 15000',                       'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Set total = 0, node sementara = head',                          'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'LAKUKAN SELAMA sementara tidak kosong:',                        'clue' => 0, 'konversi' => 0],
                    ['langkah' => '  tambahkan nilai pada node sementara ke variabel total',       'clue' => 0, 'konversi' => 0],
                    ['langkah' => '  pindah ke node berikutnya',                                   'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT total akhir belanjaan',                                   'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'END',                                                            'clue' => 0, 'konversi' => 0],
                ]),
                'order'      => 5,
                'status'     => 1,
                'difficulty' => 'medium',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],

            // ============================================================
            // HARD 1 - Mencari Skor Peserta Tertinggi (SLL)
            // ============================================================
            [
                'id'              => Str::uuid(),
                'id_level'        => $idLevelLL,
                'judul'           => 'Mencari Skor Peserta Tertinggi',
                'soal'            => '<p>Panitia lomba menyimpan daftar skor (80, 95, 75) ke dalam Single Linked List. Untuk mencari pemenang, panitia perlu membandingkan semua skor secara iteratif.</p><p>Telusuri seluruh SLL, tentukan nilai skor paling tinggi, dan cetak skor maksimum tersebut.</p>',
                'kunci_tipe_data' => json_encode([
                    ['variabel' => 'head',     'tipe_data' => 'Node', 'konversi' => 0],
                    ['variabel' => 'maksimum', 'tipe_data' => 'int',  'konversi' => 0],
                ]),
                'kunci_algoritma' => json_encode([
                    ['langkah' => 'START',                                                                    'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Buat SLL berisi skor: 80 -> 95 -> 75',                                    'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Set maksimum = nilai head (80), lalu pindah cek ke node berikutnya',      'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'LAKUKAN SELAMA node belum habis:',                                        'clue' => 0, 'konversi' => 0],
                    ['langkah' => '  JIKA nilai node sekarang > maksimum MAKA maksimum = nilai node sekarang', 'clue' => 0, 'konversi' => 0],
                    ['langkah' => '  pindah ke node berikutnya',                                             'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT nilai maksimum',                                                    'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'END',                                                                     'clue' => 0, 'konversi' => 0],
                ]),
                'order'      => 1,
                'status'     => 1,
                'difficulty' => 'hard',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],

            // ============================================================
            // HARD 2 - Membalik Urutan Antrian Pemain (SLL)
            // ============================================================
            [
                'id'              => Str::uuid(),
                'id_level'        => $idLevelLL,
                'judul'           => 'Membalik Urutan Antrian Pemain',
                'soal'            => '<p>Tiga peserta masuk SLL dengan urutan 1 -> 2 -> 3. Petugas ingin membalik urutan (Reverse) sehingga yang terakhir menjadi yang pertama (3 -> 2 -> 1) hanya dengan mengubah manipulasi pointer "next" dari setiap Node.</p><p>Balik urutan SLL tersebut, ubah head menjadi node bernilai 3, lalu cetak urutan barunya.</p>',
                'kunci_tipe_data' => json_encode([
                    ['variabel' => 'head', 'tipe_data' => 'Node', 'konversi' => 0],
                    ['variabel' => 'prev', 'tipe_data' => 'Node', 'konversi' => 0],
                    ['variabel' => 'curr', 'tipe_data' => 'Node', 'konversi' => 0],
                ]),
                'kunci_algoritma' => json_encode([
                    ['langkah' => 'START',                                                         'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Buat SLL awal: 1 -> 2 -> 3',                                   'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Set Node prev = null, curr = head, next = null',               'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'LAKUKAN SELAMA curr tidak kosong:',                            'clue' => 0, 'konversi' => 0],
                    ['langkah' => '  simpan alamat next (next = curr.next)',                      'clue' => 0, 'konversi' => 0],
                    ['langkah' => '  balik arah sambungan (curr.next = prev)',                    'clue' => 0, 'konversi' => 0],
                    ['langkah' => '  geser prev dan curr maju (prev = curr, curr = next)',        'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Ubah head menjadi prev',                                       'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Telusuri dan PRINT hasil SLL yang dibalik',                   'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'END',                                                           'clue' => 0, 'konversi' => 0],
                ]),
                'order'      => 2,
                'status'     => 1,
                'difficulty' => 'hard',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],

            // ============================================================
            // HARD 3 - Menyisipkan Data di Tengah Double Linked List (DLL)
            // ============================================================
            [
                'id'              => Str::uuid(),
                'id_level'        => $idLevelLL,
                'judul'           => 'Menyisipkan Data di Tengah Double Linked List',
                'soal'            => '<p>Double Linked List menyimpan dua node: 10 (head) yang langsung menyambung ke 30 (tail). Buatlah program untuk menyisipkan node bernilai 20 agar berada pas di tengah-tengah antara 10 dan 30.</p><p>Sambungkan dengan benar pointer next dan prev, lalu cetak urutannya.</p>',
                'kunci_tipe_data' => json_encode([
                    ['variabel' => 'head', 'tipe_data' => 'DNode', 'konversi' => 0],
                    ['variabel' => 'baru', 'tipe_data' => 'DNode', 'konversi' => 0],
                ]),
                'kunci_algoritma' => json_encode([
                    ['langkah' => 'START',                                                     'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Buat DNode l1(10) dan l3(30). Sambungkan l1 <-> l3',       'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Buat node baru l2 dengan nilai 20',                         'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Sambungkan l2.next ke l1.next (menuju 30)',                 'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Sambungkan l2.prev ke l1 (menuju 10)',                      'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Ubah prev dari 30 menjadi l2 (l1.next.prev = l2)',          'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Ubah next dari 10 menjadi l2 (l1.next = l2)',               'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT seluruh DLL dari head',                               'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'END',                                                        'clue' => 0, 'konversi' => 0],
                ]),
                'order'      => 3,
                'status'     => 1,
                'difficulty' => 'hard',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],

            // ============================================================
            // HARD 4 - Mencabut Berkas Rusak di Tengah Urutan (SLL)
            // ============================================================
            [
                'id'              => Str::uuid(),
                'id_level'        => $idLevelLL,
                'judul'           => 'Mencabut Berkas Rusak di Tengah Urutan',
                'soal'            => '<p>SLL menyimpan ID berkas: 5 -> 10 -> 15. Berkas ber-ID <strong>10</strong> dianggap rusak dan harus dihapus. Kita harus menelusuri dari awal untuk mencari node 10, mencatat node sebelumnya, dan menyambungkan pointer melewati node yang rusak.</p><p>Lakukan logika penghapusan dengan traversal dan tracking node, lalu cetak isi SLL setelahnya.</p>',
                'kunci_tipe_data' => json_encode([
                    ['variabel' => 'head',  'tipe_data' => 'Node', 'konversi' => 0],
                    ['variabel' => 'hapus', 'tipe_data' => 'int',  'konversi' => 0],
                ]),
                'kunci_algoritma' => json_encode([
                    ['langkah' => 'START',                                                                 'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Buat SLL: 5 -> 10 -> 15',                                              'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Set node prev = null, node temp = head, ID hapus = 10',                'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'LAKUKAN SELAMA temp belum habis dan nilai temp != 10:',                'clue' => 0, 'konversi' => 0],
                    ['langkah' => '  prev = temp, temp = temp.next (maju mencari node 10)',               'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'JIKA node ditemukan, lompati node tersebut (prev.next = temp.next)',   'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT sisa berkas di SLL',                                             'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'END',                                                                   'clue' => 0, 'konversi' => 0],
                ]),
                'order'      => 4,
                'status'     => 1,
                'difficulty' => 'hard',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],

            // ============================================================
            // HARD 5 - Memeriksa Struktur Palindrom Sederhana (DLL)
            // ============================================================
            [
                'id'              => Str::uuid(),
                'id_level'        => $idLevelLL,
                'judul'           => 'Memeriksa Struktur Palindrom Sederhana',
                'soal'            => '<p>Sebuah kata disimpan dalam DLL huruf demi huruf: "A" <-> "B" <-> "A". Program harus mengecek apakah susunan kata ini merupakan palindrom dengan cara membandingkan node paling depan dan paling belakang yang bergerak ke arah tengah bersilangan.</p><p>Tentukan apakah struktur tersebut merupakan palindrom dan cetak hasil boolean-nya.</p>',
                'kunci_tipe_data' => json_encode([
                    ['variabel' => 'head',  'tipe_data' => 'DNode',   'konversi' => 0],
                    ['variabel' => 'tail',  'tipe_data' => 'DNode',   'konversi' => 0],
                    ['variabel' => 'isPal', 'tipe_data' => 'boolean', 'konversi' => 0],
                ]),
                'kunci_algoritma' => json_encode([
                    ['langkah' => 'START',                                                     'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Buat DNode n1(A) <-> n2(B) <-> n3(A)',                     'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'Set head = n1, tail = n3, boolean isPalindrom = true',     'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'LAKUKAN SELAMA head dan tail belum saling silang:',        'clue' => 0, 'konversi' => 0],
                    ['langkah' => '  JIKA head.data != tail.data MAKA isPalindrom = false',   'clue' => 0, 'konversi' => 0],
                    ['langkah' => '  head = head.next (maju dari kiri)',                      'clue' => 0, 'konversi' => 0],
                    ['langkah' => '  tail = tail.prev (mundur dari kanan)',                   'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'PRINT hasil isPalindrom',                                  'clue' => 0, 'konversi' => 0],
                    ['langkah' => 'END',                                                       'clue' => 0, 'konversi' => 0],
                ]),
                'order'      => 5,
                'status'     => 1,
                'difficulty' => 'hard',
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ],
        ];

        // ================================================================
        // INSERT: Queue
        // ================================================================
        foreach (array_chunk($soalList, 500) as $chunk) {
            DB::table('soal')->insert($chunk);
        }

        // ================================================================
        // INSERT: Linked List
        // ================================================================
        foreach (array_chunk($soalLinkedList, 500) as $chunk) {
            DB::table('soal')->insert($chunk);
        }

        $totalQueue = count($soalList);
        $totalLL    = count($soalLinkedList);

        $this->command->info("SoalQueueSeeder    : {$totalQueue} soal Queue berhasil di-seed.");
        $this->command->info("SoalLinkedListData : {$totalLL} soal Linked List berhasil di-seed.");
        $this->command->info('Total              : ' . ($totalQueue + $totalLL) . ' soal.');
    }
}
