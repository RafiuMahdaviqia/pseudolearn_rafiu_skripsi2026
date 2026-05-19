<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Guide;
use Illuminate\Support\Str;

class PatchAfifahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Data Guide (Master Panduan dari Afifah)
        // Kita menggunakan updateOrCreate berdasarkan judul untuk mencegah duplikasi
        
        $guides = [
            [
                'judul' => 'Pengenalan Algoritma',
                'desc' => 'Panduan dasar mengenai apa itu algoritma dan bagaimana cara kerjanya.',
            ],
            [
                'judul' => 'Tipe Data dan Variabel',
                'desc' => 'Penjelasan mengenai berbagai tipe data dasar dan cara mendeklarasikan variabel.',
            ],
            [
                'judul' => 'Struktur Kontrol: Percabangan',
                'desc' => 'Mempelajari if, else, dan switch untuk mengatur alur program.',
            ],
            [
                'judul' => 'Struktur Kontrol: Perulangan',
                'desc' => 'Mempelajari for, while, dan do-while untuk mengulang blok kode.',
            ],
            [
                'judul' => 'Pengenalan Konversi Pseudocode',
                'desc' => 'Langkah-langkah menerjemahkan pseudocode ke dalam bahasa pemrograman Java.',
            ]
        ];

        foreach ($guides as $guide) {
            Guide::updateOrCreate(
                ['judul' => $guide['judul']], // Kunci pencarian
                [
                    'id' => Str::uuid(),
                    'desc' => $guide['desc'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Catatan: 
        // Tabel log_data, history_jawaban, ars_result, label_skor, dll
        // SENGAJA DIKOSONGKAN karena merupakan tabel transaksional.
        // Tabel-tabel tersebut akan terisi otomatis oleh sistem 
        // saat pengguna mulai berinteraksi dengan aplikasi.
    }
}