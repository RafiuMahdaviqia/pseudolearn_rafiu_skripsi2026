<?php

namespace Database\Seeders\Support;

class QueueSoalKunciDefaults
{
    public static function tipeData(): array
    {
        return [
            ['variabel' => 'antrian', 'tipe_data' => 'string[]', 'konversi' => 1],
            ['variabel' => 'front', 'tipe_data' => 'int', 'konversi' => 1],
            ['variabel' => 'rear', 'tipe_data' => 'int', 'konversi' => 1],
            ['variabel' => 'kapasitas', 'tipe_data' => 'int', 'konversi' => 1],
            ['variabel' => '', 'tipe_data' => 'int', 'konversi' => 0],
        ];
    }

    public static function algoritma(): array
    {
        return [
            ['langkah' => 'START', 'clue' => 0, 'konversi' => 0],
            ['langkah' => 'INISIALISASI antrian kosong', 'clue' => 0, 'konversi' => 1],
            ['langkah' => 'ENQUEUE(data)', 'clue' => 0, 'konversi' => 1],
            ['langkah' => 'IF antrian tidak kosong THEN', 'clue' => 1, 'konversi' => 1],
            ['langkah' => 'DEQUEUE()', 'clue' => 1, 'konversi' => 1],
            ['langkah' => 'PRINT data yang dilayani', 'clue' => 0, 'konversi' => 1],
            ['langkah' => 'ENDIF', 'clue' => 1, 'konversi' => 1],
            ['langkah' => 'END', 'clue' => 0, 'konversi' => 0],
        ];
    }

    public static function deskripsi(string $judul): string
    {
        return '<p>' . e($judul) . '</p>'
            . '<p>Susun tipe data dan algoritma antrian (queue) pada panel jawaban sesuai urutan yang benar.</p>';
    }

    public static function judulLevel1(): array
    {
        return [
            'Antrian Loket Karcis Bioskop',
            'Antrian Pasien Klinik',
            'Antrian Pengambilan Obat',
            'Loket Bank Belum Buka',
            'Antrian Wahana Taman Bermain',
            'Memanggil Pasien Pertama di Puskesmas',
            'Melayani Seluruh Antrian Kasir Supermarket',
            'Antrian Pendaftaran Lomba Bergantian',
            'Membalik Urutan Antrian Peserta Ujian',
            'Cek Palindrom Plat Nomor Kendaraan',
            'Pencarian Nomor Antrian di Rumah Sakit',
            'Mencari Stok Minimum di Gudang',
            'Menghitung Frekuensi Kehadiran Siswa',
            'Cari Nomor Paket lalu Balik Antrian Pengiriman',
            'Mencari Skor Tertinggi di Antrian Turnamen',
        ];
    }
}
