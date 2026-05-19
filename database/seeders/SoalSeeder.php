<?php

namespace Database\Seeders;

use App\Models\Soal;
use Database\Seeders\Support\QueueSoalKunciDefaults;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SoalSeeder extends Seeder
{
    private const DEFAULT_LEVEL_ID = '019863c4-59f9-7319-9104-08267fc3c551';

    public function run(): void
    {
        $items = [
            // EASY
            ['judul' => 'Antrian Loket Karcis Bioskop', 'difficulty' => 'easy'],
            ['judul' => 'Antrian Pasien Klinik', 'difficulty' => 'easy'],
            ['judul' => 'Antrian Pengambilan Obat', 'difficulty' => 'easy'],
            ['judul' => 'Loket Bank Belum Buka', 'difficulty' => 'easy'],
            ['judul' => 'Antrian Wahana Taman Bermain', 'difficulty' => 'easy'],

            // MEDIUM
            ['judul' => 'Memanggil Pasien Pertama di Puskesmas', 'difficulty' => 'medium'],
            ['judul' => 'Melayani Seluruh Antrian Kasir Supermarket', 'difficulty' => 'medium'],
            ['judul' => 'Antrian Pendaftaran Lomba Bergantian', 'difficulty' => 'medium'],
            ['judul' => 'Membalik Urutan Antrian Peserta Ujian', 'difficulty' => 'medium'],
            ['judul' => 'Cek Palindrom Plat Nomor Kendaraan', 'difficulty' => 'medium'],

            // HARD
            ['judul' => 'Pencarian Nomor Antrian di Rumah Sakit', 'difficulty' => 'hard'],
            ['judul' => 'Mencari Stok Minimum di Gudang', 'difficulty' => 'hard'],
            ['judul' => 'Menghitung Frekuensi Kehadiran Siswa', 'difficulty' => 'hard'],
            ['judul' => 'Cari Nomor Paket lalu Balik Antrian Pengiriman', 'difficulty' => 'hard'],
            ['judul' => 'Mencari Skor Tertinggi di Antrian Turnamen', 'difficulty' => 'hard'],
        ];

        foreach ($items as $index => $row) {
            Soal::query()->updateOrCreate(
                ['judul' => $row['judul']],
                [
                    'id_level' => self::DEFAULT_LEVEL_ID,
                    'soal' => QueueSoalKunciDefaults::deskripsi($row['judul']),
                    'kunci_tipe_data' => QueueSoalKunciDefaults::tipeData(),
                    'kunci_algoritma' => QueueSoalKunciDefaults::algoritma(),
                    'order' => $index + 1,
                    'status' => 1,
                    'difficulty' => $row['difficulty'],
                ]
            );
        }
    }
}
